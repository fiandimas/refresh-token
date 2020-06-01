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
            app('ValidatorManager')->validate(self::rule());

            $userManager = app('UserManager');
            $user = $userManager->getByUsername($request->username);

            $userManager->isRegistered($user);
            $userManager->passwordIsMatch($request->password, $user->password);
        } catch (\ValidatorException $e) {
        } catch (\UserNotRegisteredException $e) {
        } catch (\PasswordNotMatchException $e) {
        }

        $userAgent = $request->header('user-agent');

        $userRefreshToken = $user->refresh_token;
        $refreshToken = optional($userRefreshToken)->token;

        if ($userManager->refreshTokenNeedToUpdate($userRefreshToken, $userAgent)) {
            $refreshToken = $userManager->updateOrCreateRefreshToken($user->id, $userAgent)->token;
        }

        return response()->json([
            'token' => $userManager->createJWT($user),
            'refresh_token' => $refreshToken
        ]);
    }

    private static function rule()
    {
        return [
            'username' => 'required|min:4|max:50',
            'password' => 'required|min:8'
        ];
    }
}
