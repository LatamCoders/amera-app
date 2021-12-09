<?php

namespace App\utils;

use App\Models\VehicleDocument;
use Illuminate\Support\Facades\Storage;

class UploadFiles
{
    public static function UploadDriverFile($request, $number): ?string
    {
        $vehicleFrontImageName = "$number-vehicle-front-image.{$request->file('vehicle_front_image')->getClientOriginalExtension()}";
        Storage::disk('spaces')->putFileAs('profiles', $request->file('vehicle_front_image'), $vehicleFrontImageName);

        $vehicleRearImage = "$number-vehicle-rear-image.{$request->file('vehicle_rear_image')->getClientOriginalExtension()}";
        Storage::disk('spaces')->putFileAs('profiles', $request->file('vehicle_rear_image'), $vehicleRearImage);

        $vehicleSideImage = "$number-vehicle-side-image.{$request->file('vehicle_side_image')->getClientOriginalExtension()}";
        Storage::disk('spaces')->putFileAs('profiles', $request->file('vehicle_side_image'), $vehicleSideImage);

        $vehicleInteriorImage = "$number-vehicle-interior-image.{$request->file('vehicle_interior_image')->getClientOriginalExtension()}";
        Storage::disk('spaces')->putFileAs('profiles', $request->file('vehicle_interior_image'), $vehicleInteriorImage);

        $DriverLicense = "$number-driver-license.{$request->file('driver_license')->getClientOriginalExtension()}";
        Storage::disk('spaces')->putFileAs('profiles', $request->file('driver_license'), $DriverLicense);

        $ProofOfInsurance = "$number-proof-of-insurance.{$request->file('proof_of_insurance')->getClientOriginalExtension()}";
        Storage::disk('spaces')->putFileAs('profiles', $request->file('proof_of_insurance'), $DriverLicense);

        $documents = new VehicleDocument();

        $documents->vehicle_front_image = Storage::disk('spaces')->url("profiles/$vehicleFrontImageName");
        $documents->vehicle_rear_image = Storage::disk('spaces')->url("profiles/$vehicleRearImage");
        $documents->vehicle_side_image = Storage::disk('spaces')->url("profiles/$vehicleSideImage");
        $documents->vehicle_interior_image = Storage::disk('spaces')->url("profiles/$vehicleInteriorImage");
        $documents->driver_license = Storage::disk('spaces')->url("profiles/$DriverLicense");
        $documents->proof_of_insurance = Storage::disk('spaces')->url("profiles/$ProofOfInsurance");

        $documents->save();


        return Storage::disk('spaces')->url("profiles/$filename");
    }
}
