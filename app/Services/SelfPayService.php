<?php

namespace App\Services;

use App\Mail\RecoveryPassword;
use App\Mail\VerifyEmail;
use App\Models\Booking;
use App\Models\ReservationCode;
use App\Models\SelfPay;
use App\utils\CustomHttpResponse;
use App\utils\Stripe;
use App\utils\UploadImage;
use App\utils\VerifyEmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Stripe\Customer;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SelfPayService
{
    public function SelfPaySignIn(Request $request, $phoneVerify, $activatedUser = true)
    {
        $clienteExistente = SelfPay::where('phone_number', $request->phone_number)->exists();

        if ($clienteExistente) {
            return CustomHttpResponse::HttpResponse('Client already exist', '', 400);
        }

        DB::transaction(function () use ($request, $phoneVerify, $activatedUser) {
            $customer = Stripe::RegisterStripeCustomer("$request->name $request->lastname", $request->email, $request->phone_number);

            $selfpay = new SelfPay();

            $selfPayId = 'SP' . rand(100, 9999);

            $selfpay->client_id = $selfPayId;
            $selfpay->name = $request->name;
            $selfpay->phone_number = $request->phone_number;
            $selfpay->lastname = $request->lastname;
            $selfpay->email = $request->email;
            $selfpay->stripe_customer_id = $customer->id;
            $selfpay->gender = $request->gender;
            $selfpay->birthday = $request->birthday;
            $selfpay->address = $request->address;
            $selfpay->city = $request->city;
            $selfpay->note = $request->note;
            $selfpay->profile_picture = UploadImage::UploadProfileImage($request->file('profile_picture'), $selfPayId);
            $selfpay->user_device_id = $request->user_device_id;
            $selfpay->ca_id = $request->ca_id;
            $selfpay->phone_number_verified_at = $phoneVerify;
            $selfpay->active = $activatedUser;

            $selfpay->save();

            if ($request->number != null) {
                $this->AddStripePaymentMethod($request, $selfPayId);
            }
        });

        return 'Client register';
    }

    public function SelfPayUpdate(Request $request)
    {
        $selfpay = SelfPay::where('id', $request->id)->first();

        if ($selfpay === null) {
            return CustomHttpResponse::HttpResponse('Client does not exist', '', 400);
        }

        DB::transaction(function () use ($request, $selfpay) {
            $selfpay->client_id = $request->client_id;
            $selfpay->name = $request->name;
            $selfpay->phone_number = $request->phone_number;
            $selfpay->lastname = $request->lastname;
            $selfpay->email = $request->email;
            $selfpay->stripe_customer_id = $request->stripe_customer_id;
            $selfpay->gender = $request->gender;
            $selfpay->birthday = $request->birthday;
            $selfpay->address = $request->address;
            $selfpay->city = $request->city;
            $selfpay->note = $request->note;
            //$selfpay->profile_picture = UploadImage::UploadProfileImage($request->file('profile_picture'), $selfpay->id);
            $selfpay->user_device_id = $request->user_device_id;
            $selfpay->phone_number_verified_at = $request->phone_number_verified_at;
            $selfpay->active = $request->active;

            $selfpay->save();
        });

        return 'Client Updated';
    }

    public function AddStripePaymentMethod($request, $clientId)
    {
        DB::transaction(function () use ($request, $clientId) {
            $client = SelfPay::where('client_id', $clientId)->first();

            $stripe = new StripeClient(
                env('STRIPE_KEY')
            );

            $card_id = $stripe->tokens->create([
                'card' => [
                    'number' => $request->number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                    'name' => $request->name,
                ],
            ]);

            $paymentId = $stripe->customers->createSource(
                "$client->stripe_customer_id",
                ['source' => $card_id->id]
            );

            $client->stripe_payment_method_id = $paymentId->id;
            $client->save();
        });
    }

    public function GetCreditCard($clientId)
    {
        $client = SelfPay::where('client_id', $clientId)->first();

        return Stripe::GetStripeCreditCard($client->stripe_customer_id, $client->stripe_payment_method_id);
    }

    public function ChargeCreditCard($request, $clientId)
    {
        try {
            DB::beginTransaction();

            $client = SelfPay::where('client_id', $clientId)->first();

            $stripe = new StripeClient(
                env('STRIPE_KEY')
            );

            $status = $stripe->charges->create([
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'customer' => $client->stripe_customer_id,
                'description' => $request->description,
            ]);

            Booking::where('selfpay_id', $client->id)->update(['charge_id' => $status->id]);

            DB::commit();

            return $status->status;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BadRequestException('Charge error');
        }
    }

    public function DeleteCreditCard($clientId)
    {
        DB::transaction(function () use ($clientId) {
            $client = SelfPay::where('client_id', $clientId)->first();

            $stripe = new StripeClient(
                env('STRIPE_KEY')
            );

            $stripe->customers->deleteSource(
                $client->stripe_customer_id,
                $client->stripe_payment_method_id,
                []
            );

            $client->stripe_payment_method_id = null;
            $client->save();
        });
    }

    public function ModifyCreditCard($request, $clientId)
    {
        $client = SelfPay::where('client_id', $clientId)->first();

        $stripe = new StripeClient(
            env('STRIPE_KEY')
        );

        $stripe->customers->updateSource(
            $client->stripe_customer_id,
            $client->stripe_payment_method_id,
            [
                'name' => $request->credit_card_name,
                'exp_month' => $request->exp_month,
                'exp_year' => $request->exp_year,
            ]
        );
    }

    public function VerifyClientNumberOrEmail($selfpayId, $verificationType, $request)
    {
        if ($verificationType == 'phone_number') {

            $data = SelfPay::where('client_id', $selfpayId)->first();

            $data->phone_number_verified_at = Carbon::now();

            $data->save();
        } else if ($verificationType == 'email') {
            $data = SelfPay::where('client_id', $selfpayId)->first();

            $code = Cache::get("VerifyEmail.$data->email");

            if ($code != (int)$request->code) {
                throw new BadRequestException("Invalid code");
            }

            $data->email_verified_at = Carbon::now();

            $data->save();

            Cache::forget("VerifyEmail.$data->email");
        } else {
            throw new BadRequestException('Invalid verification type');
        }
    }

    public function ActivateReservationCodeSP($clientId)
    {
        $sp = SelfPay::where('client_id', $clientId)->first();

        $sp->active = true;

        $sp->save();
    }

    public function ReservationCode($request)
    {
        $code = ReservationCode::with('SelfPay')->where('code', $request->code)->first();

        if (!$code) {
            throw new BadRequestException('Invalid reservation code');
        }

        return $code;
    }

    public function SendVerificationEmailCode($clientId)
    {
        $client = SelfPay::where('client_id', $clientId)->first();

        VerifyEmailService::SendCode($client->email, VerifyEmail::class, "VerifyEmail.$client->email");
    }
}
