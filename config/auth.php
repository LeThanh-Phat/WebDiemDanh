<?php

return [

    'defaults' => [
        'guard' => 'sanctum',
        'passwords' => 'sinhviens',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'sinhviens',
        ],
        'admin' => [
            'driver' => 'sanctum',
            'provider' => 'admins',
        ],
        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => 'sinhviens',
        ],

        'giangvien_api' => [
            'driver' => 'jwt',
            'provider' => 'giangvien',
        ],
    ],

    'providers' => [
        'sinhviens' => [
            'driver' => 'eloquent',
            'model' => App\Models\SinhVien::class,
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class, // Sử dụng model Admin
        ],
        'giangvien' => [
            'driver' => 'eloquent',
            'model' => App\Models\Giangvien::class,
        ],
    ],


    'password_timeout' => 10800,

];
