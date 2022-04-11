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

            $cAPayment = new CorportateAccountPaymentMethod();

            $cAPayment->name_on_cc = $request->name_on_cc;
            $cAPayment->cc_number = $request->cc_number;
            $cAPayment->type_of_cc = $request->type_of_cc;
            $cAPayment->code_of_cc = $request->code_of_cc;

            $ca->save();

            $ca->CorporateAccountPersonalInfo()->save($CaInf);
            if ($request->name_on_cc != "") {
                $ca->CorporateAccountPaymentMethod()->save($cAPayment);
            }
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
