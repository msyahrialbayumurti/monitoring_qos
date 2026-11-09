<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FonnteService
{
    /**
     * Apakah token Fonnte sudah diatur pada konfigurasi aplikasi.
     */
    public function isConfigured(): bool
    {
        return filled(config('services.fonnte.token'));
    }

    /**
     * Normalisasi nomor WhatsApp Indonesia menjadi format 62xxxxxxxxxx.
     */
    public static function normalizeNumber(?string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $number);

        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        if (! preg_match('/^628\d{7,13}$/', $digits)) {
            return null;
        }

        return $digits;
    }

    /**
     * Kirim pesan WhatsApp melalui Fonnte.
     *
     * @return array{success: bool, reason: ?string, response: mixed}
     */
    public function send(string $target, string $message): array
    {
        $token = (string) config('services.fonnte.token');

        if ($token === '') {
            return [
                'success' => false,
                'reason' => 'Token Fonnte belum diatur pada file .env.',
                'response' => null,
            ];
        }

        $number = self::normalizeNumber($target);

        if ($number === null) {
            return [
                'success' => false,
                'reason' => 'Nomor WhatsApp tidak valid.',
                'response' => null,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])
                ->asForm()
                ->timeout((int) config('services.fonnte.timeout', 10))
                ->post((string) config('services.fonnte.url'), [
                    'target' => $number,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            $payload = $response->json();
            $success = $response->successful()
                && is_array($payload)
                && (($payload['status'] ?? false) === true);

            if (! $success) {
                Log::warning('Fonnte gagal mengirim pesan WhatsApp.', [
                    'target' => $number,
                    'http_status' => $response->status(),
                    'reason' => is_array($payload) ? ($payload['reason'] ?? null) : null,
                ]);
            }

            return [
                'success' => $success,
                'reason' => is_array($payload)
                    ? ($payload['reason'] ?? ($success ? null : 'Respons Fonnte tidak valid.'))
                    : ($success ? null : 'Respons Fonnte tidak valid.'),
                'response' => $payload,
            ];
        } catch (Throwable $e) {
            Log::warning('Fonnte tidak dapat dihubungi.', [
                'target' => $number,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'reason' => $e->getMessage(),
                'response' => null,
            ];
        }
    }
}
