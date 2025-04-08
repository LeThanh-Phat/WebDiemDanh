<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Giangvien extends Authenticatable implements JWTSubject
{
    protected $table = 'giangvien';
    protected $primaryKey = 'id_giangvien';
    public $timestamps = true;

    protected $fillable = [
        'Magiangvien',
        'name_giangvien',
        'email_giangvien',
        'sdt_giangvien',
        'password_giangvien',
    ];

    protected $hidden = ['password_giangvien'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
