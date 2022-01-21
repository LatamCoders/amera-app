<?php

namespace App\Services;

use App\Models\AmeraAdmin;
use App\Models\AmeraUser;
use App\Models\CorporateAccount;
use App\utils\UniqueIdentifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
     * Iniciar la sesión
     */
    public function AdminLogin($request): array
    {
        $existUser = AmeraUser::with('AmeraAdmin', 'Role')
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
    public function AdminLogOut(): string
    {
        auth()->logout(true);

        return 'Admin logout successfully';
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

    public function GetCorporateAccountList()
    {
        return CorporateAccount::with('AmeraUser.Role')->get();
    }

    public function ChangeUserStatus($userId)
    {
        $user = AmeraUser::where('id', $userId)->first();

        $user->status = !$user->satus;

        $user->save();
    }
}
