<?php

use App\Http\Controllers\sinhvienController;
use App\Http\Controllers\GiangvienController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/dangnhap', [sinhvienController::class, 'xuLyDangNhap'])->name('xulydangnhap');
Route::get('/dangxuat', [sinhvienController::class, 'dangXuat'])->name('dangxuat');

Route::post('/dangnhapGV', [GiangvienController::class, 'xuLyDangNhapGV'])->name('xulydangnhapGV');
Route::get('/dangxuatGV', [GiangvienController::class, 'dangXuat'])->name('dangxuat');

Route::get('/welcomeDD', function () {
    return view('welcome', ['name' => Session::get('name')]);
})->name('welcomeDD');

Route::get('/home', function () {
    return view('hello');
});

Route::get('/userFat', [UserController::class, 'index1']);
