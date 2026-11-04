<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
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

        $canEdit = true;

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
}
