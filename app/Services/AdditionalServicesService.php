<?php

namespace App\Services;

use App\Models\AdditionalService;

class AdditionalServicesService
{
    public function Add($request, $bookingId)
    {
        $services = new AdditionalService();

        $services->service = $request->service;
        $services->to = $request->to;
        $services->time = $request->time;
        $services->price = $request->price;
        $services->booking_id = $bookingId;

        $services->save();
    }
}
