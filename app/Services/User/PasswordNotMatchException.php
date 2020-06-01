<?php

namespace App\Services\User;

use Exception;
use ReflectionClass;

class PasswordNotMatchException extends Exception
{
    public function render()
    {
        return response()->json([
            'error' => [
                'code' => 401,
                'message' => 'password is not match.',
                'errors' => [
                    [
                        'reason' => (new ReflectionClass($this))->getShortName(),
                        'message' => 'password is not match.',
                        'location' => 'password',
                        'location_type' => 'body'
                    ]
                ]
            ]
        ], 401);
    }
}
