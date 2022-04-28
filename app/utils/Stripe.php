<?php

namespace App\utils;

use Stripe\Customer;
use Stripe\StripeClient;

class Stripe
{
    public static function RegisterStripeCustomer($name, $email, $phoneNumber): Customer
    {
        $stripe = new StripeClient(
            env('STRIPE_KEY')
        );

        return $stripe->customers->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phoneNumber,
        ]);
    }
}
