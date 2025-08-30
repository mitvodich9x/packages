<?php

return [
    'guard' => env('MIT_ADMIN_GUARD', 'admin'),

    'auth' => [
        'guards' => [
            'admin' => [
                'driver' => 'session',
                'provider' => 'admins',
            ],
        ],
        'providers' => [
            'admins' => [
                'driver' => 'eloquent',
                'model' => \Vgplay\Admins\Models\Admin::class,
            ],
        ],
    ],
];
