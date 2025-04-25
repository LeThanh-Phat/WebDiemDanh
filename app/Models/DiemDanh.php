<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiemDanh extends Model
{
    protected $table = 'diemdanh';
    protected $primaryKey = 'id_diemdanh';
    public $timestamps = false;

    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'id_sinhvien', 'id_sinhvien');
    }

    public function buoiHoc()
    {
        return $this->belongsTo(BuoiHoc::class, 'id_buoihoc', 'id_buoihoc');
    }
    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'id_monhoc', 'id_monhoc');
    }
}
