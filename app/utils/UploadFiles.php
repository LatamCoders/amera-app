<?php

namespace App\utils;

use App\Models\DriverDocument;
use App\Models\VehicleDocument;
use Illuminate\Support\Facades\Storage;

class UploadFiles
{
    public static function UploadVehicleFile($request, $number, $vehicleId): ?string
    {
        try {
            $vehicleFrontImageName = "$number-vehicle-front-image.{$request->file('vehicle_front_image')->getClientOriginalExtension()}";
            Storage::disk('public')->putFileAs('vehicle', $request->file('vehicle_front_image'), $vehicleFrontImageName);

            $vehicleRearImage = "$number-vehicle-rear-image.{$request->file('vehicle_rear_image')->getClientOriginalExtension()}";
            Storage::disk('public')->putFileAs('vehicle', $request->file('vehicle_rear_image'), $vehicleRearImage);

            $vehicleSideImage = "$number-vehicle-side-image.{$request->file('vehicle_side_image')->getClientOriginalExtension()}";
            Storage::disk('public')->putFileAs('vehicle', $request->file('vehicle_side_image'), $vehicleSideImage);

            $vehicleInteriorImage = "$number-vehicle-interior-image.{$request->file('vehicle_interior_image')->getClientOriginalExtension()}";
            Storage::disk('public')->putFileAs('vehicle', $request->file('vehicle_interior_image'), $vehicleInteriorImage);

            $DriverLicense = "$number-driver-license.{$request->file('driver_license')->getClientOriginalExtension()}";
            Storage::disk('public')->putFileAs('driver', $request->file('driver_license'), $DriverLicense);

            $ProofOfInsurance = "$number-proof-of-insurance.{$request->file('proof_of_insurance')->getClientOriginalExtension()}";
            Storage::disk('public')->putFileAs('driver', $request->file('proof_of_insurance'), $DriverLicense);

            $documents = new VehicleDocument();

            $documents->vehicle_id = $vehicleId;
            $documents->vehicle_front_image = Storage::disk('public')->url("vehicle/$vehicleFrontImageName");
            $documents->vehicle_rear_image = Storage::disk('public')->url("vehicle/$vehicleRearImage");
            $documents->vehicle_side_image = Storage::disk('public')->url("vehicle/$vehicleSideImage");
            $documents->vehicle_interior_image = Storage::disk('public')->url("vehicle/$vehicleInteriorImage");

            if ($documents->save()) {
                return true;
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function UploadDriverFile($request, $number, $driverId): ?string
    {
        try {
            $DriverLicense = "$number-driver-license.{$request->file('driver_license')->getClientOriginalExtension()}";
            Storage::disk('public')->putFileAs('driver', $request->file('driver_license'), $DriverLicense);

            $ProofOfInsurance = "$number-proof-of-insurance.{$request->file('proof_of_insurance')->getClientOriginalExtension()}";
            Storage::disk('public')->putFileAs('driver', $request->file('proof_of_insurance'), $DriverLicense);

            $driverDocument = new DriverDocument();

            $driverDocument->driver_id = $driverId;
            $driverDocument->driver_license = Storage::disk('public')->url("driver/$DriverLicense");
            $driverDocument->proof_of_insurance = Storage::disk('public')->url("driver/$ProofOfInsurance");

            if ($driverDocument->save()) {
                return true;
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
