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
        $userRefreshToken = null;

        $refreshToken = $request->header('refresh-token', null);

        try {
            if (is_null($refreshToken)) {
                throw new Exception('err');
            }

            $userRefreshToken = UserRefreshToken::where('token', $refreshToken)->first();

            if (is_null($userRefreshToken)) {
                throw new Exception('refresh token not found');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        $userAgent = $request->header('user-agent', '');

        $userManager = app('UserManager');
        $user = $userManager->getById($userRefreshToken->user_id);

        if ($userManager->refreshTokenNeedToUpdate($userRefreshToken, $userAgent)) {
            $refreshToken = $userManager->updateOrCreateRefreshToken($user->id, $userAgent)->token;
        }

        return response()->json([
            'token' => $userManager->createJWT($user),
            'refresh_token' => $refreshToken
        ]);
    }
}
