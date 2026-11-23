<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QosMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'throughput',
        'delay',
        'jitter',
        'packet_loss',
        'ping',
        'gateway_ping',
        'gateway_jitter',
        'gateway_packet_loss',
        'gateway_success',
        'target_ping',
        'target_packet_loss',
        'target_success',
        'success',
        'raw_output',
        'measured_at',
    ];

    protected $casts = [
        'throughput' => 'decimal:2',
        'delay' => 'decimal:2',
        'jitter' => 'decimal:2',
        'packet_loss' => 'decimal:2',
        'ping' => 'decimal:2',
        'gateway_ping' => 'decimal:2',
        'gateway_jitter' => 'decimal:2',
        'gateway_packet_loss' => 'decimal:2',
        'gateway_success' => 'boolean',
        'target_ping' => 'decimal:2',
        'target_packet_loss' => 'decimal:2',
        'target_success' => 'boolean',
        'success' => 'boolean',
        'measured_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
