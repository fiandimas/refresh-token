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
            $user = $userManager->getByUsername($request->username, 'refreshToken');
            $userManager->_setUser($user);

            $userManager->isRegistered();
            $userManager->passwordIsMatch($request->password);
        } catch (\ValidatorException $e) {
        } catch (\UserNotRegisteredException $e) {
        } catch (\PasswordNotMatchException $e) {
        }

        $refreshToken = optional($user->refreshToken)->token;

        if ($userManager->refreshTokenNeedToUpdate()) {
            $refreshToken = $userManager->updateOrCreateRefreshToken()->token;
        }

        return response()->json([
            'token' => $userManager->createJWT(),
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
