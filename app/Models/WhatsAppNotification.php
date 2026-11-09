<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppNotification extends Model
{
    protected $table = 'whatsapp_notifications';

    protected $fillable = [
        'user_id',
        'school_id',
        'measurement_id',
        'phone',
        'event',
        'condition',
        'message',
        'status',
        'response',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function measurement()
    {
        return $this->belongsTo(QosMeasurement::class, 'measurement_id');
    }
}
