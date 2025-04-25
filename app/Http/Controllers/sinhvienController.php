<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sinhvien;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class sinhvienController extends Controller
{
    public function xuLyDangNhap(Request $request)
    {
        $yeucau = $request->only('mssv', 'password');

        $sinhvien = Sinhvien::where('mssv', $yeucau['mssv'])->first();

        if (!$sinhvien || sha1($yeucau['password']) !== $sinhvien->password_sinhvien) {
            return response()->json(['message' => 'Đăng nhập không thành công'], 401);
        } else {
            $token = JWTAuth::fromUser($sinhvien);
            return response()->json([
                'message' => 'Đăng nhập thành công',
                'canlogin' => true,
                'token' => $token,
                'userName' => $sinhvien->name_sinhvien,
            ]);
        }
    }

    public function dangXuat()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    public function loadTTSV()
    {
        $sinhvien = JWTAuth::parseToken()->authenticate();

        return response()->json([
            'studentId' => $sinhvien->mssv,
            'fullName' => $sinhvien->name_sinhvien,
            'className' => $sinhvien->lop_sinhvien,
            'birthDate' => $sinhvien->ngaysinh_sinhvien,
            'gender' => $sinhvien->gioitinh_sinhvien,
            'address' => $sinhvien->diachi_sinhvien,
            'email' => $sinhvien->email_sinhvien,
            'phone' => $sinhvien->sdt_sinhvien,
        ]);
    }
}
