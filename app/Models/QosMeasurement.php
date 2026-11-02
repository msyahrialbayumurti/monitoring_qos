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
        'success' => 'boolean',
        'measured_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
