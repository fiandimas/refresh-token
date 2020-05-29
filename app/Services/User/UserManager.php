<?php

namespace App\Services\User;

use App\User;
use App\UserRefreshToken;
use Firebase\JWT\JWT;

class UserManager
{
    public function getByUsername($username, $relations = [])
    {
        $relations = is_string($relations) ? [$relations] : $relations;
        return User::where('username', $username)->with($relations)->first();
    }

    public function isRegistered($user)
    {
        if (is_null($user)) {
            throw new \Exception('user is not registered.');
        }
    }

    public function passwordIsMatch($password, $userPassword)
    {
        if (app('hash')->check($password, $userPassword) === false) {
            throw new \Exception('password is incorrect.');
        }
    }

    public function updateOrCreateRefreshToken($userId)
    {
        $exp = date('Y-m-d H:i:s', config('const.EXPIRED_REFRESH_TOKEN'));
        $token = hash('SHA256', time() . $userId . \Illuminate\Support\Str::random(32));

        return UserRefreshToken::updateOrCreate([
            'user_id' => $userId
        ], [
            'user_id' => $userId,
            'token' => $token,
            'expired_at' => $exp
        ]);
    }

    public function generateJWT($user)
    {
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'exp' => config('const.EXPIRED_JWT')
        ];

        return JWT::encode($data, env('APP_KEY'), 'HS256');
    }
}
