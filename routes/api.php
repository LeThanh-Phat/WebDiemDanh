<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\sinhvienController;
use App\Http\Controllers\GiangvienController;
use App\Http\Controllers\DiemdanhController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Api\AuthController;


Route::post('/giangvien/dangnhap', [GiangvienController::class, 'dangNhap']);

Route::middleware(['auth:giangvien_api'])->group(function () {
    Route::get('/giangvien/ttgv', [GiangvienController::class, 'thongTin']);
    Route::post('/giangvien/dangxuat', [GiangvienController::class, 'dangXuat']);
    Route::put('/giangvien/capnhat', [GiangvienController::class, 'capnhat']);
    Route::post('/diemdanh/taomaqr', [DiemdanhController::class, 'taoMaQR']);
    Route::get('/diemdanh/buoihoc', [DiemdanhController::class, 'getBuoiHoc']);
    Route::get('/diemdanh/sinhvien/{id_lophoc}', [DiemdanhController::class, 'getSinhVienByLopHoc']);
    route::get('/diemdanh/submit', [DiemdanhController::class, 'submitDiemDanh'])->name('diemdanh.submit');
    Route::get('/giangvien/lichgiangday', [GiangvienController::class, 'lichgiangday']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/thoikhoabieu', [AuthController::class, 'thoikhoabieu']);
    Route::post('/capnhatSV', [AuthController::class, 'capnhatSV']);
    Route::post('/diemdanh', [AuthController::class, 'diemdanh']);
    Route::get('/ketquadiemdanh', [AuthController::class, 'ketquadiemdanh']);
});

Route::post('/admin/login', [AdminController::class, 'login']);
Route::middleware('auth:admin')->group(function () { // Sửa auth:sanctum thành auth:admin
    // Route cho Giảng viên
    Route::get('/admin/giangvien', [AdminController::class, 'getGiangViens']);
    Route::post('/admin/themgiangvien', [AdminController::class, 'themgiangvien']);
    Route::put('/admin/capnhatgiangvien/{id}', [AdminController::class, 'capnhatgiangvien']);

    // Route cho Sinh viên
    Route::get('/admin/sinhvien', [AdminController::class, 'getSinhViens']);
    Route::post('/admin/themsinhvien', [AdminController::class, 'themSinhVien']);
    Route::put('/admin/capnhatsinhvien/{id}', [AdminController::class, 'capnhatSinhVien']);
    // Route cho Môn học
    Route::get('/admin/monhoc', [AdminController::class, 'getMonHocs']);
    Route::post('/admin/themmonhoc', [AdminController::class, 'themMonHoc']);
    Route::put('/admin/capnhatmonhoc/{id}', [AdminController::class, 'capnhatMonHoc']);
    Route::delete('/admin/xoamonhoc/{id}', [AdminController::class, 'xoaMonHoc']);

    // Route cho Lớp học
    Route::get('/admin/lophoc', [AdminController::class, 'getLopHocs']);
    Route::post('/admin/themlophoc', [AdminController::class, 'themLopHoc']);
    Route::put('/admin/capnhatlophoc/{id}', [AdminController::class, 'capnhatLopHoc']);
    Route::delete('/admin/xoalophoc/{id}', [AdminController::class, 'xoaLopHoc']);

    Route::post('/admin/themsinhvienvaolophoc', [AdminController::class, 'themSinhVienVaoLopHoc']);
    Route::get('/admin/lophoc/{id_lophoc}/sinhvien', [AdminController::class, 'getSinhVienByLopHoc']);

    // Route cho Lịch học
    Route::get('/admin/lophoc/{id_lophoc}/lichhoc', [AdminController::class, 'getLichHocByLopHoc']);
    Route::post('/admin/themlichhoc', [AdminController::class, 'themLichHoc']);
    Route::put('/admin/capnhatlichhoc/{id_buoihoc}', [AdminController::class, 'capNhatLichHoc']);
    Route::delete('/admin/xoalichhoc/{id_buoihoc}', [AdminController::class, 'xoaLichHoc']);
});
