<?php

namespace App\Services;

use App\Models\Experience;

class ExperienceService
{
    public function RateAmera($request, $bookingId, $driverId, $selfPayId)
    {
        $ameraRate = new Experience();

        $ameraRate->amera_rate = $request->amera_rate;
        $ameraRate->comments = $request->comments;
        $ameraRate->driver_id = $driverId;
        $ameraRate->selfpay_id = $selfPayId;
        $ameraRate->booking_id = $bookingId;

        $ameraRate->save();
    }
}
