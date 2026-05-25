<?php

namespace App\Controller;

class InputController
{
    /**
     * Validate and Sanitize User Input Data.
     * 
     * @param array $inputs User input data
     * @return array
     */
    public static function inputValidate(array $inputs): array
    {
        $errors      = [];
        
        $name        = htmlspecialchars(trim($inputs['name'] ?? ''));
        $email       = filter_var(trim($inputs['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $address     = htmlspecialchars(trim($inputs['address'] ?? ''));
        $city        = htmlspecialchars(trim($inputs['city'] ?? ''));
        $postalCode  = htmlspecialchars(trim($inputs['postal_code'] ?? ''));

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

        if ($postalCode === '') {
            $errors['postal_code'] = "Postal Code is required";
        }

        if (!empty($errors)) {
            return [
                "errors" => $errors
            ];
        }

        return [
            "data" => [
                "name"        => $name,
                "email"       => $email,
                "address"     => $address,
                "city"        => $city,
                "postal_code" => $postalCode
            ]
        ];
    }
}
