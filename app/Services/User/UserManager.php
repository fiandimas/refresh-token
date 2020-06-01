<?php

namespace App\Services\User;

use App\User;
use App\UserRefreshToken;
use Firebase\JWT\JWT;

class UserManager
{
    public function getByUsername($username)
    {
        return User::select('id', 'name', 'username', 'password')->where('username', $username)->first();
    }

    public function getById($id)
    {
        return User::select('id', 'name', 'username', 'password')->where('id', $id)->first();
    }

    public function appendRelation($user)
    {
        return $user->with();
    }

    public function isRegistered($user)
    {
        if (is_null($user)) {
            throw new UserNotRegisteredException();
        }
    }

    public function passwordIsMatch($password, $userPassword)
    {
        if (app('hash')->check($password, $userPassword) === false) {
            throw new PasswordNotMatchException();
        }
    }

    public function refreshTokenNeedToUpdate($userRefreshToken, $userAgent)
    {
        if (is_null($userRefreshToken) || time() > strtotime($userRefreshToken->expired_at) || strcmp($userRefreshToken->user_agent, $userAgent) !== 0) {
            return true;
        }
    }

    public function updateOrCreateRefreshToken($userId, $userAgent)
    {
        $key = \Illuminate\Support\Str::random(32);
        $token = hash('SHA256', $userId . $key . $userAgent);
        $exp = date('Y-m-d H:i:s', config('const.EXPIRED_REFRESH_TOKEN'));

        return UserRefreshToken::updateOrCreate([
            'user_id' => $userId
        ], [
            'user_id' => $userId,
            'token' => $token,
            'key' => $key,
            'user_agent' => $userAgent,
            'expired_at' => $exp
        ]);
    }

    public function createJWT($user)
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'exp' => config('const.EXPIRED_JWT')
        ];

        return JWT::encode($data, env('APP_KEY'), 'HS256');
    }
}
