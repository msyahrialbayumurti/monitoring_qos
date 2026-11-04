<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    /**
     * Ambil setelan milik akun. Jika belum ada, gunakan setelan global
     * sebagai nilai bawaan.
     */
    public static function getForUser(
        ?int $userId,
        string $key,
        ?string $default = null
    ): ?string {
        if ($userId) {
            $value = static::query()
                ->where('user_id', $userId)
                ->where('key', $key)
                ->value('value');

            if ($value !== null) {
                return $value;
            }
        }

        $global = Setting::query()
            ->where('key', $key)
            ->value('value');

        return $global ?? $default;
    }

    /**
     * Simpan setelan milik akun.
     */
    public static function setForUser(
        int $userId,
        string $key,
        string $value
    ): void {
        static::updateOrCreate(
            [
                'user_id' => $userId,
                'key' => $key,
            ],
            [
                'value' => $value,
            ]
        );
    }
}
