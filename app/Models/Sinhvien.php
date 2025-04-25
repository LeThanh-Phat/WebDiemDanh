<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class SinhVien extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'sinhvien';
    protected $primaryKey = 'id_sinhvien';
    public $timestamps = false;

    protected $fillable = [
        'name_sinhvien',
        'email_sinhvien',
        'password_sinhvien',
        'sdt_sinhvien',
        'diachi_sinhvien',
        'mssv',
        'lop_sinhvien',
        'ngaysinh_sinhvien',
        'gioitinh_sinhvien',
        'id_admin',
    ];

    public function lopHocs()
    {
        return $this->belongsToMany(LopHoc::class, 'sinhvien_lophoc', 'id_sinhvien', 'id_lophoc');
    }
}
