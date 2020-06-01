<?php

namespace App\Services\User;

use Exception;
use ReflectionClass;

class UserNotRegisteredException extends Exception
{
    public function render()
    {
        return response()->json([
            'error' => [
                'code' => 404,
                'message' => 'user is not registered.',
                'errors' => [
                    [
                        'reason' => (new ReflectionClass($this))->getShortName(),
                        'message' => 'user is not registered.',
                        'location' => 'username',
                        'location_type' => 'body'
                    ]
                ]
            ]
        ], 404);
    }
}
