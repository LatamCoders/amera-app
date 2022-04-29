<?php

namespace App\Services;

use App\Models\AmeraUser;
use App\Models\Booking;
use App\Models\CorporateAccount;
use App\Models\CorporateAccountPersonalInfo;
use App\Models\CorportateAccountPaymentMethod;
use App\Models\SelfPay;
use App\utils\Stripe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CorporateAccountService
{
    /*
     * Registrarse
     */
    public function RegisterCA($request)
    {
        try {
            DB::beginTransaction();

            $customer_id = Stripe::RegisterStripeCustomer($request->company_legal_name, $request->email, $request->telephone_number);

            $user = new AmeraUser();

            $user->name = $request->company_legal_name;
            $user->email = $request->email;
            $user->password = Hash::make('amera');
            $user->role = 3;
            $user->status = 0;

            $user->save();

            $ca = new CorporateAccount();

            $ca->company_legal_name = $request->company_legal_name;
            $ca->dba = $request->dba;
            $ca->tin = $request->tin;
            $ca->office_location_address = $request->office_location_address;
            $ca->billing_address = $request->billing_address;
            $ca->amera_user_id = $user->id;

            $CaInf = new CorporateAccountPersonalInfo();

            $CaInf->telephone_number = $request->telephone_number;
            $CaInf->fax_number = $request->fax_number;
            $CaInf->email = $request->email;
            $CaInf->website = $request->website;
            $CaInf->additional_contact_name = $request->additional_contact_name;
            $CaInf->additional_contact_number = $request->additional_contact_number;
            $CaInf->additional_contact_email = $request->additional_contact_email;
            $CaInf->additional_contact_title = $request->additional_contact_title;
            $CaInf->stripe_customer_id = $customer_id->id;

            $ca->save();

            $ca->CorporateAccountPersonalInfo()->save($CaInf);

            if ($request->name_on_cc != "") {
                $this->AddStripePaymentMethod($request, $customer_id->id, $ca->id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $stripe = new StripeClient(
                env('STRIPE_KEY')
            );

            $stripe->customers->delete(
                $customer_id->id,
                []
            );

            throw new BadRequestException($e->getMessage());
        }
    }

    private function AddStripePaymentMethod($request, $clientId, $caId)
    {
        DB::transaction(function () use ($request, $clientId, $caId) {
            $client = new CorportateAccountPaymentMethod();

            $stripe = new StripeClient(
                env('STRIPE_KEY')
            );

            $card_id = $stripe->tokens->create([
                'card' => [
                    'number' => $request->cc_number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->code_of_cc,
                    'name' => $request->name_on_cc,
                ],
            ]);

            $paymentId = $stripe->customers->createSource(
                "$clientId",
                ['source' => $card_id->id]
            );

            $client->stripe_payment_method_id = $paymentId->id;
            $client->corporate_account_id = $caId;
            $client->save();
        });
    }

    /*
     * Devolver datos del CA
     */
    public function GetCorporateAccountData($caId)
    {
        try {
            return CorporateAccount::with('CorporateAccountPersonalInfo', 'CorporateAccountPaymentMethod', 'AmeraUser.Role')
                ->where('id', $caId)->first();
        } catch (\Exception $exception) {
            throw new HttpException(500, $exception->getMessage());
        }
    }

    /*
     * Registrar un booking
     */
    public function BookingRegister($request)
    {

        $booking = new Booking();


    }

    /*
     * Obtener la lista de clientes de un CA
     */
    public function GetCaClientList($caId)
    {
        return SelfPay::where('ca_id', $caId)->get();
    }
}
