<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\utils\CustomHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Exception;

class ExperienceController extends Controller
{
    /*
     * Calificar a amera
     */
    public function Rate(Request $request, $booking): JsonResponse
    {
        try {
            $ameraRate = new Experience();

            $ameraRate->amera_rate = $request->amera_rate;
            $ameraRate->driver_id = $request->driver_id;
            $ameraRate->selfpay_id = $request->selfpay_id;
            $ameraRate->booking_id = $booking;

            $ameraRate->save();

            return CustomHttpResponse::HttpResponse('OK', '', 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }
}
