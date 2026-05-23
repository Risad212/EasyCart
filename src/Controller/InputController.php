<?php

namespace App\Controller;

class InputController
{
    public static function inputValidate($inputs)
    {
        $errors = [];

        // ✅ SANITIZE FIRST
        $name        = htmlspecialchars(trim($inputs['name'] ?? ''));
        $email       = filter_var(trim($inputs['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $address     = htmlspecialchars(trim($inputs['address'] ?? ''));
        $city        = htmlspecialchars(trim($inputs['city'] ?? ''));
        $postal_code = htmlspecialchars(trim($inputs['postal_code'] ?? ''));

        // ✅ VALIDATE
        if ($name === '') {
            $errors['name'] = "Name is required";
        }

        if ($email === '') {
            $errors['email'] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        }

        if ($address === '') {
            $errors['address'] = "Address is required";
        }

        if ($city === '') {
            $errors['city'] = "City is required";
        }

        if ($postal_code === '') {
            $errors['postal_code'] = "Postal Code is required";
        }

        // ❌ Return errors if any
        if (!empty($errors)) {
            return [
                "errors" => $errors
            ];
        }

        // ✅ Return clean sanitized data
        return [
            "data" => [
                "name"        => $name,
                "email"       => $email,
                "address"     => $address,
                "city"        => $city,
                "postal_code" => $postal_code
            ]
        ];
    }
}
