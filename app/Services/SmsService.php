<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SmsService
{
    public function LoginSmsProvider()
    {
        $response = Http::asForm()
            ->withoutVerifying()
            ->post(env('LIVECOMM_LOGIN_URL'), [
                'action' => 'loginUser',
                'username' => env('LIVECOMM_USERNAME'),
                'userpassword' => env('LIVECOMM_PASSWORD')
            ]);

        return $response['bearer_token'];
    }

    public function SendSmsCode($number): array
    {
        $token = $this->LoginSmsProvider();

        $code = rand(1000, 9999);
        $smsCode = "Your Amera verification code is: $code. Don't share this code with anyone";

        $response = Http::asForm()
            ->withoutVerifying()
            ->withToken($token)
            ->post(env('LIVECOMM_SEND_URL'), [
                'action' => 'submitOpenMessage',
                'to_number' => $number,
                'lc_number' => env('LIVECOMM_LC_NUMBER'),
                'message' => $smsCode,
                'personid' => env('LIVECOMM_PERSON_ID')
            ]);

        if ($response['success'] == '1') {
            return [
                "message" => $response['message'],
                "data" => (object) ["code" => $code]
            ];
        } else {
            throw new BadRequestException($response['message']);
        }
    }
}
