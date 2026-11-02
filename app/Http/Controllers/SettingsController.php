<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $interval = (int) (
            Setting::where('key', 'monitoring_interval')->value('value') ?? 5
        );

        $refreshInterval = (int) (
            Setting::where('key', 'dashboard_refresh_interval')->value('value') ?? 30
        );

        if (!in_array($interval, [1, 5, 10, 15], true)) {
            $interval = 5;
        }

        if (!in_array($refreshInterval, [15, 30, 60, 120], true)) {
            $refreshInterval = 30;
        }

        $canEdit = auth()->user()?->isAdmin() ?? false;

        return view('settings.index', compact('interval', 'refreshInterval', 'canEdit'));
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

        Setting::updateOrCreate(
            ['key' => 'monitoring_interval'],
            ['value' => (string) $validated['monitoring_interval']]
        );

        Setting::updateOrCreate(
            ['key' => 'dashboard_refresh_interval'],
            ['value' => (string) $validated['dashboard_refresh_interval']]
        );

        return redirect()
            ->route('settings.index')
            ->with('success', 'Pengaturan monitoring berhasil disimpan.');
    }
}
