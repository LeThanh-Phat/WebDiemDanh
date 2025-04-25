<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SinhVienLopHoc extends Model
{
    protected $table = 'sinhvien_lophoc';
    protected $primaryKey = ['id_sinhvien', 'id_lophoc'];
    public $incrementing = false;
    public $timestamps = false; // Tắt timestamp tự động
    protected $keyType = 'integer';

    protected $fillable = ['id_sinhvien', 'id_lophoc'];

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('id_sinhvien', $this->getAttribute('id_sinhvien'))
            ->where('id_lophoc', $this->getAttribute('id_lophoc'));
    }

    public function sinhvien()
    {
        return $this->belongsTo(SinhVien::class, 'id_sinhvien');
    }

    public function lophoc()
    {
        return $this->belongsTo(LopHoc::class, 'id_lophoc');
    }
}
