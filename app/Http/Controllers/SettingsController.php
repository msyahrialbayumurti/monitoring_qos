<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use App\Models\WhatsAppNotification;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    public function index(FonnteService $fonnte)
    {
        $userId = auth()->id();

        $interval = (int) UserSetting::getForUser(
            $userId,
            'monitoring_interval',
            '5'
        );

        $refreshInterval = (int) UserSetting::getForUser(
            $userId,
            'dashboard_refresh_interval',
            '30'
        );

        if (!in_array($interval, [1, 5, 10, 15], true)) {
            $interval = 5;
        }

        if (!in_array($refreshInterval, [15, 30, 60, 120], true)) {
            $refreshInterval = 30;
        }

        $whatsappNumber = $this->userSetting($userId, 'whatsapp_number', '');
        $whatsappNotifications = $this->userSetting($userId, 'whatsapp_notifications', '0') === '1';
        $fonnteConfigured = $fonnte->isConfigured();

        $canEdit = true;

        return view('settings.index', compact(
            'interval',
            'refreshInterval',
            'canEdit',
            'whatsappNumber',
            'whatsappNotifications',
            'fonnteConfigured'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'monitoring_interval' => [
                'required',
                'integer',
                'in:1,5,10,15',
            ],
            'dashboard_refresh_interval' => [
                'required',
                'integer',
                'in:15,30,60,120',
            ],
        ]);

        $userId = $request->user()->id;

        UserSetting::setForUser(
            $userId,
            'monitoring_interval',
            (string) $validated['monitoring_interval']
        );

        UserSetting::setForUser(
            $userId,
            'dashboard_refresh_interval',
            (string) $validated['dashboard_refresh_interval']
        );

        return redirect()
            ->route('settings.index')
            ->with('success', 'Pengaturan akun berhasil disimpan.');
    }

    public function updateWhatsApp(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'whatsapp_notifications' => ['nullable', 'boolean'],
        ]);

        $userId = $request->user()->id;
        $rawNumber = trim((string) ($validated['whatsapp_number'] ?? ''));
        $number = $rawNumber === ''
            ? null
            : FonnteService::normalizeNumber($rawNumber);

        if ($rawNumber !== '' && $number === null) {
            throw ValidationException::withMessages([
                'whatsapp_number' => 'Nomor WhatsApp tidak valid. Gunakan format 08xxxxxxxxxx.',
            ]);
        }

        $enabled = $request->boolean('whatsapp_notifications');

        if ($enabled && $number === null) {
            throw ValidationException::withMessages([
                'whatsapp_number' => 'Isi nomor WhatsApp terlebih dahulu sebelum mengaktifkan notifikasi.',
            ]);
        }

        UserSetting::setForUser(
            $userId,
            'whatsapp_number',
            $number ?? ''
        );

        UserSetting::setForUser(
            $userId,
            'whatsapp_notifications',
            $enabled ? '1' : '0'
        );

        return redirect()
            ->route('settings.index')
            ->with('success', 'Pengaturan notifikasi WhatsApp berhasil disimpan.');
    }

    public function testWhatsApp(Request $request, FonnteService $fonnte)
    {
        $user = $request->user();

        $number = $this->userSetting($user->id, 'whatsapp_number', '');

        if (blank($number)) {
            return redirect()
                ->route('settings.index')
                ->with('error', 'Isi dan simpan nomor WhatsApp terlebih dahulu.');
        }

        if (! $fonnte->isConfigured()) {
            return redirect()
                ->route('settings.index')
                ->with('error', 'Token Fonnte belum diatur pada file .env.');
        }

        $message = implode("\n", [
            '[NETMONITOR] Tes Notifikasi WhatsApp',
            'Jika Anda menerima pesan ini, notifikasi WhatsApp sudah siap digunakan.',
            'Waktu: ' . now()->format('d/m/Y H:i'),
        ]);

        $result = $fonnte->send($number, $message);

        if ($user->school_id) {
            WhatsAppNotification::create([
                'user_id' => $user->id,
                'school_id' => $user->school_id,
                'measurement_id' => null,
                'phone' => $number,
                'event' => 'test',
                'condition' => null,
                'message' => $message,
                'status' => $result['success'] ? 'sent' : 'failed',
                'response' => $result['reason']
                    ?? json_encode($result['response'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'sent_at' => $result['success'] ? now() : null,
            ]);
        }

        if (! $result['success']) {
            return redirect()
                ->route('settings.index')
                ->with('error', 'Tes WhatsApp gagal: ' . ($result['reason'] ?? 'Tidak diketahui.'));
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Pesan tes WhatsApp berhasil dikirim.');
    }

    private function userSetting(int $userId, string $key, ?string $default = null): ?string
    {
        $value = UserSetting::query()
            ->where('user_id', $userId)
            ->where('key', $key)
            ->value('value');

        return $value ?? $default;
    }
}
