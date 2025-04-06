<?php

use App\Http\Controllers\sinhvienController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Session;

Route::get('/dn', [sinhvienController::class, 'index'])->name('dangnhap');
Route::post('/dangnhap', [sinhvienController::class, 'xuLyDangNhap']);
Route::get('/dangxuat', [sinhvienController::class, 'dangXuat'])->name('dangxuat');

Route::get('/welcomeDD', function () {
    return view('welcome', ['name' => Session::get('name')]);
})->name('welcomeDD');

Route::get('/home', function () {
    return view('hello');
});
Route::get('/user-home', [UserController::class, 'index'])->name('user-home');
