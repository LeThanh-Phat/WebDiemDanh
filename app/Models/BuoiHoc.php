<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuoiHoc extends Model
{
    protected $table = 'buoihoc';

    protected $primaryKey = 'id_buoihoc';

    public $timestamps = false;

    protected $fillable = [
        'ngayhoc',
        'tietbd',
        'tietkt',
        'phonghoc',
        'id_lophoc',
        'qr_codedata',
        'thoigianhethan',
    ];

    // Quan hệ với model DiemDanh
    public function diemDanhs()
    {
        return $this->hasMany(DiemDanh::class, 'id_buoihoc', 'id_buoihoc');
    }

    // Quan hệ với model LopHoc (nếu có)
    public function lopHoc()
    {
        return $this->belongsTo(LopHoc::class, 'id_lophoc', 'id_lophoc');
    }
}