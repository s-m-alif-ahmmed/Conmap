<?php

return [
    'plans' => [
        'free' => [
            'name' => 'Free Trial',
            'price_id' => null, // No Stripe price ID for free trial
            'trial_days' => 14,
        ],
        'monthly' => [
            'name' => 'Monthly Plan',
            'price_id' => 'price_123abc', // Replace with actual Stripe price ID
            'amount' => 300,
            'interval' => 'month',
        ],
        'yearly' => [
            'name' => 'Yearly Plan',
            'price_id' => 'price_456def', // Replace with actual Stripe price ID
            'amount' => 5000,
            'interval' => 'year',
        ],
    ],
];
