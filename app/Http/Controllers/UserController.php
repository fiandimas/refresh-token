<?php

namespace App\Http\Controllers;

use App\UserRefreshToken;
use Exception;
use Firebase\JWT\JWT;
use ReflectionClass;

class UserController extends Controller
{
    public function me()
    {
        $token = app('request')->header('token', null);

        try {
            if (is_null($token)) {
                throw new Exception('token required');
            }

            $data = JWT::decode($token, env('APP_KEY'), ['HS256']);
            unset($data->exp);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'code' => 401,
                    'message' => $e->getMessage(),
                    'errors' => [
                        [
                            'reason' => (new ReflectionClass($e))->getShortName(),
                            'message' => $e->getMessage()
                        ]
                    ]
                ]
            ], 401);
        }
    }

    public function refreshToken()
    {
        $request = app('request');

        $refreshToken = $request->header('refresh-token', null);

        try {
            if (is_null($refreshToken)) {
                throw new Exception('err');
            }

            $userRefreshToken = UserRefreshToken::where('token', $refreshToken)->with('user')->first();

            if (is_null($userRefreshToken)) {
                throw new Exception('refresh token not found');
            }

            $refreshToken = $userRefreshToken->token;

            if (time() > strtotime($userRefreshToken->expired_at)) {
                $refreshToken = app('UserManager')->updateOrCreateRefreshToken($data->id)->token;
            }

            $userManager = app('UserManager');
            $userManager->_setUser($userRefreshToken->user);

            return response()->json([
                'token' => $userManager->createJWT(),
                'refresh_token' => $refreshToken
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
