<?php

namespace App\Services;

use App\Models\AdditionalService;

class AdditionalServicesService
{
    public function Add($request, $bookingId)
    {
        $data = json_decode($request->services);

        foreach ($data as $service) {
            $services = new AdditionalService();

            $services->service = $service->service;
            $services->to = $service->to;
            $services->time = $service->time;
            $services->price = $service->price;
            $services->booking_id = $bookingId;

            $services->save();
        }

    }
}
