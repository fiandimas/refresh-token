<?php

namespace App\Services\User;

use App\User;
use App\UserRefreshToken;
use Firebase\JWT\JWT;

class UserManager
{
    private $_user;

    public function _setUser($user)
    {
        $this->_user = $user;
    }

    public function getByUsername($username, $relations = [])
    {
        return User::select('id', 'name', 'username', 'password')->where('username', $username)->with($relations)->first();
    }

    public function isRegistered()
    {
        if (is_null($this->_user)) {
            throw new UserNotRegisteredException();
        }
    }

    public function passwordIsMatch($password)
    {
        if (app('hash')->check($password, $this->_user->password) === false) {
            throw new PasswordNotMatchException();
        }
    }

    public function refreshTokenNeedToUpdate()
    {
        $userRefreshToken = $this->_user->refreshToken;
        $userAgent = app('request')->header('user-agent', '');
        if (is_null($userRefreshToken) || time() > strtotime($userRefreshToken->expired_at) || strcmp($userRefreshToken->user_agent, $userAgent) !== 0) {
            return true;
        }
    }

    public function updateOrCreateRefreshToken()
    {
        $userId = $this->_user->id;
        $key = \Illuminate\Support\Str::random(32);
        $userAgent = app('request')->header('user-agent', '');
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

    public function createJWT()
    {
        $data = [
            'id' => $this->_user->id,
            'name' => $this->_user->name,
            'username' => $this->_user->username,
            'exp' => config('const.EXPIRED_JWT')
        ];

        return JWT::encode($data, env('APP_KEY'), 'HS256');
    }
}
