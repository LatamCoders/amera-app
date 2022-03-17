<?php

namespace App\Services;

use App\Models\AmeraUser;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AmeraUserService
{
    /*
     * Iniciar la sesión
     */
    public function AmeraUserLogin($request): array
    {
        $existUser = AmeraUser::with('AmeraAdmin', 'CorporateAccount.CorporateAccountPersonalInfo', 'CorporateAccount.CorporateAccountPaymentMethod', 'Role')
            ->where('email', $request->email)->first();

        if ($existUser != null && $existUser->status == 0) throw new HttpException(403, 'This user is not active');

        if (!$existUser) throw new HttpException(404, 'User not found');

        $credentials = $request->only('email', 'password');

        $token = auth('users')->attempt($credentials);

        if (!$token) throw new HttpException(500, 'password incorrect');

        return $this->RespondWithToken($token, $existUser);
    }

    public function UserList()
    {
        return AmeraUser::with('Role', 'AmeraAdmin')->get();
    }

    public function GetAndModifyUser($action, $ameraUserId, $request)
    {
        $data = AmeraUser::with('Role', 'AmeraAdmin')->where('id', $ameraUserId)->first();
        if ($action == 'getData') {
            return $data;
        } else if ($action == 'modify') {
            $data->name = $request->name;
            $data->email = $request->email;
            $data->rol = $request->rol;

            $data->save();

            return 'User has been modify';
        } else {
            throw new BadRequestException('Invalid option');
        }
    }

    /*
     * Cerrar la sesión
     */
    public function AmeraUserLogOut(): string
    {
        auth()->logout(true);

        return 'logout successfully';
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
}
