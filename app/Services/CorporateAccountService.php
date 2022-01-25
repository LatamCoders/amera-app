<?php

namespace App\Services;

use App\Models\AmeraUser;
use App\Models\Booking;
use App\Models\CorporateAccount;
use App\Models\CorporateAccountPersonalInfo;
use App\Models\CorportateAccountPaymentMethod;
use App\Models\SelfPay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CorporateAccountService
{
    /*
     * Registrarse
     */
    public function RegisterCA($request)
    {
        DB::transaction(function () use ($request) {
            $user = new AmeraUser();

            $user->name = $request->contact_name;
            $user->email = $request->email;
            $user->password = Hash::make('amera');
            $user->role = 3;
            $user->status = 0;

            $user->save();

            $ca = new CorporateAccount();

            $ca->company_legal_name = $request->company_legal_name;
            $ca->dba = $request->dba;
            $ca->company_type = $request->company_type;
            $ca->tin = $request->tin;
            $ca->nature_of_business = $request->nature_of_business;
            $ca->contract_start_date = $request->contract_start_date;
            $ca->office_location_address = $request->office_location_address;
            $ca->billing_address = $request->billing_address;
            $ca->amera_user_id = $user->id;

            $CaInf = new CorporateAccountPersonalInfo();

            $CaInf->telephone_number = $request->telephone_number;
            $CaInf->fax_number = $request->fax_number;
            $CaInf->email = $request->email;
            $CaInf->website = $request->website;
            $CaInf->contact_name = $request->contact_name;
            $CaInf->contact_number = $request->contact_number;
            $CaInf->additional_contact_name = $request->additional_contact_name;
            $CaInf->additional_contact_title = $request->additional_contact_title;
            $CaInf->additional_contact_number = $request->additional_contact_number;
            $CaInf->additional_contact_email = $request->additional_contact_email;

            $cAPayment = new CorportateAccountPaymentMethod();

            $cAPayment->name_on_cc = $request->name_on_cc;
            $cAPayment->cc_number = $request->cc_number;
            $cAPayment->type_of_cc = $request->type_of_cc;
            $cAPayment->zip = $request->zip;
            $cAPayment->code_of_cc = $request->code_of_cc;

            $ca->save();

            $ca->CorporateAccountPersonalInfo()->save($CaInf);
            $ca->CorporateAccountPaymentMethod()->save($cAPayment);
        });
    }

    /*
     * Iniciar la sesión
     */
    public function CorporateAccountLogin($request): array
    {
        $existUser = AmeraUser::with('CorporateAccount.CorporateAccountPersonalInfo', 'CorporateAccount.CorporateAccountPaymentMethod', 'Role')
            ->where('email', $request->email)->first();

        if ($existUser != null && $existUser->status == 0) throw new HttpException(403, 'This user is not active');

        if (!$existUser) throw new HttpException(404, 'User not found');

        $credentials = $request->only('email', 'password');

        $token = auth('users')->attempt($credentials);

        if (!$token) throw new HttpException(500, 'password incorrect');

        return $this->RespondWithToken($token, $existUser);
    }

    /*
     * Cerrar la sesión
     */
    public function CorporateAccountLogOut(): string
    {
        auth()->logout(true);

        return 'Corporate account logout successfully';
    }

    /*
     * Retornar token con datos del usuario
     */
    protected function RespondWithToken($token, $client): array
    {
        return [
            'user' => $client,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
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
     * Registrar un paciente/reservation code
     */
    public function ReservationCodeRegister($request)
    {
        DB::transaction(function () use ($request) {
            $client = new SelfPay();

            $selfPayId = 'SP' . rand(100, 9999);

            $client->client_id = $selfPayId;
            $client->name = $request->name;
            $client->lastname = $request->lastname;
            $client->phone_number = $request->phone_number;
            $client->email = $request->email;

            $client->save();


        });
    }
}
