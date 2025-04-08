<?php

use App\Http\Controllers\sinhvienController;
use App\Http\Controllers\GiangvienController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;


Route::post('/sinhvien/dangnhap', [sinhvienController::class, 'xuLyDangNhap']);

Route::middleware(['auth:sinhvien_api'])->group(function () {
    Route::get('/sinhvien/ttsv', [sinhvienController::class, 'loadTTSV']);
    Route::post('/sinhvien/dangxuat', [sinhvienController::class, 'dangXuat']);
});


Route::post('/giangvien/dangnhap', [GiangvienController::class, 'dangNhap']);
Route::middleware(['auth:giangvien_api'])->group(function () {
    Route::get('/giangvien/ttgv', [GiangvienController::class, 'thongTin']);
    Route::post('/giangvien/dangxuat', [GiangvienController::class, 'dangXuat']);
});

