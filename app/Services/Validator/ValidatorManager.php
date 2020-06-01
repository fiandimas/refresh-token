<?php

namespace App\Services\Validator;

class ValidatorManager
{
    public function validate($rule)
    {
        $request = app('request');
        $validator = app('validator')->make($request->only(array_keys($rule)), $rule);

        if ($validator->fails()) {
            throw new ValidatorException($validator->errors()->toArray());
        }
    }
}
