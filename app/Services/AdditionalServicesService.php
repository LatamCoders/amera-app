<?php

namespace App\Services;

use App\Models\AdditionalService;

class AdditionalServicesService
{
    public function Add($request, $bookingId)
    {
        $services = new AdditionalService();

        $services->service = $request->service;
        $services->from = $request->from;
        $services->to = $request->to;
        $services->booking_id = $bookingId;

        $services->save();
    }
}
