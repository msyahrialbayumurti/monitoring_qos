<?php

namespace App\Observers;

use App\Models\QosMeasurement;
use App\Services\QosNotificationService;

class QosMeasurementObserver
{
    public function created(QosMeasurement $measurement): void
    {
        app(QosNotificationService::class)->handle($measurement);
    }
}
