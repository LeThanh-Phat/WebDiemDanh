<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Kế thừa từ Authenticatable để hỗ trợ xác thực
use Laravel\Sanctum\HasApiTokens; // Thêm trait để hỗ trợ token

class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'admin'; // Ánh xạ tới bảng admin
    protected $primaryKey = 'id_admin'; // Khóa chính

    protected $fillable = [
        'name_admin',
        'email_admin',
        'password_admin',
    ];
    public $timestamps = false; // Tắt tự động thêm created_at và updated_at

    protected $hidden = [
        'password_admin', // Ẩn mật khẩu trong phản hồi JSON
    ];

    // Đổi tên các trường để phù hợp với logic xác thực
    public function getAuthPassword()
    {
        return $this->password_admin;
    }
    public function giangviens()
    {
        return $this->hasMany(GiangVien::class, 'id_admin');
    }

    public function lophocs()
    {
        return $this->hasMany(LopHoc::class, 'id_admin');
    }

    public function monhocs()
    {
        return $this->hasMany(MonHoc::class, 'id_admin');
    }

    public function sinhviens()
    {
        return $this->hasMany(SinhVien::class, 'id_admin');
    }

    public function buoihocs()
    {
        return $this->hasMany(BuoiHoc::class, 'id_admin');
    }
}
