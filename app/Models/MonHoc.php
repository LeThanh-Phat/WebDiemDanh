<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonHoc extends Model
{
    protected $table = 'monhoc';
    protected $primaryKey = 'id_monhoc';
    public $timestamps = false;

    protected $fillable = [
        'name_monhoc',
        'mamon',
        'hocky',
        'id_admin',
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }

    public function lophocs()
    {
        return $this->hasMany(LopHoc::class, 'id_monhoc');
    }
}
