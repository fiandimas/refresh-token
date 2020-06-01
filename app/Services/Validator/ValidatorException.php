<?php

namespace App\Services\Validator;

use Exception;
use ReflectionClass;

class ValidatorException extends Exception
{
    private $errors;

    function __construct($errors)
    {
        $this->errors = self::mapError($errors);
    }

    public function render()
    {
        return response()->json([
            'error' => [
                'code' => 422,
                'message' => self::getFirstError($this->errors),
                'errors' => $this->errors
            ]
        ], 422);
    }

    private function mapError($errors)
    {
        $result = [];

        foreach ($errors as $key => $error) {
            array_push($result, [
                'reason' => (new ReflectionClass($this))->getShortName(),
                'message' => $error[0],
                'location' => $key,
                'location_type' => 'body'
            ]);
        }

        return $result;
    }

    private function getFirstError($erorrs)
    {
        if (count($erorrs) === 1) {
            return $erorrs[0]['message'];
        }

        return 'the given';
    }
}
