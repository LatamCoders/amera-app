<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SmsService
{
    public function LoginSmsProvider()
    {
        $response = Http::asForm()
            ->withoutVerifying()
            ->post('https://livecomm.vndsupport.com/api2/login.php/', [
                'action' => 'loginUser',
                'username' => 'ebe@vndx.com',
                'userpassword' => 'lc2020_debug$%1'
            ]);

        return $response['bearer_token'];
    }

    public function SendSmsCode($number)
    {
        $token = $this->LoginSmsProvider();

        $code = rand(1000, 9999);
        $smsCode = "Your Amera verification code is: $code. Don't share this code with anyone";

        $response = Http::asForm()
            ->withoutVerifying()
            ->withToken($token)
            ->post('https://livecomm.vndsupport.com/api2/', [
                'action' => 'submitOpenMessage',
                'to_number' => $number,
                'lc_number' => '2109609993',
                'message' => $smsCode,
                'personid' => '895'
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
