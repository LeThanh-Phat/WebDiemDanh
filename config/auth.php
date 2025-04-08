<?php

return [

    'defaults' => [
        'guard' => 'sinhvien_api',
        'passwords' => 'users',
    ],

    'guards' => [
        'sinhvien_api' => [
            'driver' => 'jwt',
            'provider' => 'sinhvien',
        ],

        'giangvien_api' => [
            'driver' => 'jwt',
            'provider' => 'giangvien',
        ],
    ],

    'providers' => [
        'sinhvien' => [
            'driver' => 'eloquent',
            'model' => App\Models\Sinhvien::class,
        ],

        'giangvien' => [
            'driver' => 'eloquent',
            'model' => App\Models\Giangvien::class,
        ],
    ],


    'password_timeout' => 10800,

];
