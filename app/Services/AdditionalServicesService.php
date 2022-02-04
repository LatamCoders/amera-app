<?php

namespace App\Services;

use App\Models\AdditionalService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

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

    public function DeleteService($serviceId, $bookingId)
    {
        $service = AdditionalService::where('id', $serviceId)->first();

        if ($service->booking_id != $bookingId) {
            throw new BadRequestException("This service doesn't belong to this booking");
        } else {
            $service->delete();
        }

    }

    public function ModifyServices($request, $serviceId, $bookingId)
    {
        $service = AdditionalService::where('id', $serviceId)->first();

        if ($service->booking_id != $bookingId) {
            throw new BadRequestException("This service doesn't belong to this booking");
        }

        $service->time = $request->time;
        $service->price = $request->price;

        $service->save();
    }
}
