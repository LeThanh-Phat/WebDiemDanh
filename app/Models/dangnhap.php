<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dangnhap extends Model
{
       
    protected $table = 'mssv';
    protected $primaryKey = 'password';
    protected $fillable = [
        'mssv',
        'password',
    ];
    public $timestamps = false;
}
