<?php

namespace App\Http\Controllers;

class LoginController extends Controller
{
    public function login()
    {
        $user = null;
        $userManager = null;
        $request = app('request');

        try {
            $rule = self::rule();
            $validator = app('validator')->make($request->only(array_keys($rule)), $rule);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }

            $userManager = app('UserManager');

            $user = $userManager->getByUsername($request->username, 'refreshToken');

            $userManager->isRegistered($user);
            $userManager->passwordIsMatch($request->password, $user->password);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }

        $refreshToken = optional($user->refreshToken)->token;

        if (is_null($user->refreshToken) || time() > strtotime($user->refreshToken->expired_at)) {
            $refreshToken = $userManager->updateOrCreateRefreshToken($user->id)->token;
        }

        return response()->json([
            'token' => $userManager->generateJWT($user),
            'refresh_token' => $refreshToken
        ]);
    }

    private static function rule()
    {
        return [
            'username' => 'required|min:4',
            'password' => 'required|min:8'
        ];
    }
}
