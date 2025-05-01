<?php

namespace App\Models;

use App\Models\Giangvien;
use Illuminate\Database\Eloquent\Model;

class LopHoc extends Model
{
    protected $table = 'lophoc';
    protected $primaryKey = 'id_lophoc';
    public $timestamps = false;

    protected $fillable = [
        'name_lophoc',
        'id_giangvien',
        'id_monhoc',
        'hocky',
        'id_admin',
    ];

    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'id_monhoc', 'id_monhoc');
    }

    public function giangVien()
    {
        return $this->belongsTo(Giangvien::class, 'id_giangvien', 'id_giangvien');
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }
    public function buoiHocs()
    {
        return $this->hasMany(BuoiHoc::class, 'id_lophoc', 'id_lophoc');
    }
    public function sinhviens()
    {
        return $this->belongsToMany(SinhVien::class, 'sinhvien_lophoc', 'id_lophoc', 'id_sinhvien');
    }
}
