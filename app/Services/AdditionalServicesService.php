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

            $to = (object)['to' => $service->to, 'coordinate' => $service->to_coordinates];

            $services->service = $service->service;
            $services->to = json_encode($to);
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
