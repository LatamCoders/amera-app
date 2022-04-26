<?php

namespace App\Services;

use App\Models\AmeraAdmin;
use App\Models\AmeraUser;
use App\Models\Booking;
use App\Models\CorporateAccount;
use App\Models\Driver;
use App\Models\Refund;
use App\utils\StatusCodes;
use App\utils\UniqueIdentifier;
use http\Exception\BadMessageException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AmeraAdminService
{
    /*
     * Registro de administradores
     */
    public function RegisterAdmin($request): string
    {
        DB::transaction(function () use ($request) {
            $user = new AmeraUser();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make('admins');
            $user->role = $request->role;
            $user->status = 1;

            $user->save();

            $admin = new AmeraAdmin();

            $admin->name = $request->name;
            $admin->user = UniqueIdentifier::GenerateUid();
            $admin->email = $request->email;
            $admin->amera_user_id = $user->id;

            $admin->save();
        });

        return 'Admin saved';
    }

    /*
     * Devolver datos del administrador
     */
    public function GetAdminData($AdminId)
    {
        try {
            return AmeraAdmin::with('AmeraUser.Role')
                ->where('id', $AdminId)->first();
        } catch (\Exception $exception) {
            throw new HttpException(500, $exception->getMessage());
        }
    }

    public function GetDriverList()
    {
        return Driver::with('Booking', 'Vehicle')->get();
    }

    public function GetDriverInfo($driverId)
    {
        return Driver::with('Booking', 'Vehicle.VehicleDocuments', 'DriverDocuments')->where('id', $driverId)->first();
    }

    public function GetCorporateAccountList()
    {
        return CorporateAccount::with('AmeraUser.Role', 'CorporateAccountPersonalInfo', 'CorporateAccountPaymentMethod')->get();
    }

    public function AssignDriverToBooking($driverId, $bookingId)
    {
        $driver = Driver::with('Booking')->where('driver_id', $driverId)->first();

        $booking = Booking::where('id', $bookingId)->first();

        if ($booking->driver_id != $driver->id) {
            $booking->driver_id = $driver->id;

            $booking->save();
        } else {
            throw new BadRequestException('The driver has already been assigned to this booking');
        }
    }

    public function ChangeUserStatus($userId)
    {
        $user = AmeraUser::where('id', $userId)->first();

        $user->status = !$user->satus;

        $user->save();
    }

    public function ApproveDriverDocuments($driverId, $document)
    {
        $user = Driver::where('id', $driverId)->first();

        switch ($document) {
            case 'driver_license_verify_at':
                $user->driver_license_verify_at = Carbon::now();
                $user->save();
                break;
            case 'proof_of_insurance_verify_at':
                $user->proof_of_insurance_verify_at = Carbon::now();
                $user->save();
                break;
            case 'vehicle_front_image_verify_at':
                $user->vehicle_front_image_verify_at = Carbon::now();
                $user->save();
                break;
            case 'vehicle_rear_image_verify_at':
                $user->vehicle_rear_image_verify_at = Carbon::now();
                $user->save();
                break;
            case 'vehicle_side_image_verify_at':
                $user->vehicle_side_image_verify_at = Carbon::now();
                $user->save();
                break;
            case 'vehicle_interior_image_verify_at':
                $user->vehicle_interior_image_verify_at = Carbon::now();
                $user->save();
                break;
        }
    }

    /*
     * Aprovar cancelacion del booking
     */
    public function ApproveCancellationTrip($bookingId): string
    {
        try {
            DB::beginTransaction();

            $booking = Booking::where('id', $bookingId)->first();

            if ($booking->status != StatusCodes::CANCELLATION_PENDING) {
                throw new BadMessageException('This booking is not pending for cancellation');
            }

            $stripe = new StripeClient(
                env('STRIPE_KEY')
            );
            $refund_id = $stripe->refunds->create([
                'charge' => $booking->charge_id,
            ]);

            $booking->status = StatusCodes::CANCELLED;
            $booking->refund = true;

            $refund = new Refund();

            $refund->stripe_refund_id = $refund_id;
            $refund->booking_id = $booking->id;

            $booking->save();
            $refund->save();

            DB::commit();

            return 'Refund successfully';
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BadRequestException($e->getMessage());
        }

    }

    /*
     * Marcar como pagado al driver
     */
    public function MarkAsDriverPaid($bookingId)
    {
        $booking = Booking::where('id', $bookingId)->first();

        if ($booking->status != StatusCodes::COMPLETED) {
            throw new BadRequestException("Can't mark as paid. This booking is not completed yet");
        }

        $booking->booking_paid_to_driver_at = Carbon::now();

        $booking->save();
    }

    public function DeleteCaUser($ameraUserId)
    {
        $user = AmeraUser::where('id', $ameraUserId)->first();

        $user->delete();
    }
}
