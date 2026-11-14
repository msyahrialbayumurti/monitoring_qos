<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_sekolah',
        'ip_target',
        'bandwidth',
        'paket_isp',
        'status',
        'agent_token',
        'agent_enrollment_token',
        'agent_enrollment_expires_at',
        'agent_last_seen_at',
        'agent_gateway',
        'agent_public_ip',
        'agent_isp',
        'agent_version',
        'measure_requested_at',
        'agent_offline_notified_at',
        'agent_speedtest_cooldown_until',
        'agent_speedtest_notice',
    ];

    protected $casts = [
        'bandwidth' => 'decimal:2',
        'status' => 'boolean',
        'agent_last_seen_at' => 'datetime',
        'agent_enrollment_expires_at' => 'datetime',
        'measure_requested_at' => 'datetime',
        'agent_offline_notified_at' => 'datetime',
        'agent_speedtest_cooldown_until' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (School $school) {
            if (blank($school->agent_token)) {
                $school->agent_token = Str::random(48);
            }
        });
    }

    public function agentIsOnline(int $minutes = 10): bool
    {
        return $this->agent_last_seen_at !== null
            && $this->agent_last_seen_at->gte(now()->subMinutes($minutes));
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function qosMeasurements()
    {
        return $this->hasMany(QosMeasurement::class);
    }

    public function latestQosMeasurement()
    {
        return $this->hasOne(QosMeasurement::class)
            ->latestOfMany('measured_at');
    }
}
