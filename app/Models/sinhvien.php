<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Sinhvien extends Authenticatable implements JWTSubject
{
    protected $table = 'sinhvien';
    protected $primaryKey = 'id_sinhvien';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'mssv',
        'password_sinhvien',
        'name_sinhvien',
        'email_sinhvien',
        'sdt_sinhvien'
    ];

    protected $hidden = ['password_sinhvien'];

    // Mapping password field
    public function getAuthPassword()
    {
        return $this->password_sinhvien;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
