<?php

namespace App\Controller;

class InputController
{
    public static function inputValidate($inputs)
    {
        $errors = [];

        $name    = trim($inputs['name'] ?? '');
        $email   = trim($inputs['email'] ?? '');
        $address = trim($inputs['address'] ?? '');

        // validation
        if ($name === '') {
            $errors['name'] = "Name is required";
        }

        if ($email === '') {
            $errors['email'] = "Email is required";
        }

        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        }

        if ($address === '') {
            $errors['address'] = "Address is required";
        }

        // ❌ if errors return them
        if (!empty($errors)) {
            return [
                "errors" => $errors
            ];
        }

        // ✅ return CLEAN data
        return [
            "data" => [
                "name" => $name,
                "email" => $email,
                "address" => $address
            ]
        ];
    }
}
