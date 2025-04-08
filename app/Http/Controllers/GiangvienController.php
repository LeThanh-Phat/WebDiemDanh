<?php

namespace App\Http\Controllers;

use App\Models\Giangvien;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class GiangvienController extends Controller
{
    public function dangNhap(Request $request)
    {
        $credentials = $request->only('Magiangvien', 'password');

        $gv = Giangvien::where('Magiangvien', $credentials['Magiangvien'])->first();

        if (!$gv || sha1($credentials['password']) !== $gv->password_giangvien) {
            return response()->json(['message' => 'Sai mã GV hoặc mật khẩu'], 401);
        }

        $token = JWTAuth::fromUser($gv);

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'name' => $gv->name_giangvien,
            'canlogin' => true,
        ]);
    }

    public function thongTin()
    {
        $gv = JWTAuth::parseToken()->authenticate();

        return response()->json([
            'magv' => $gv->Magiangvien,
            'name' => $gv->name_giangvien,
            'email' => $gv->email_giangvien,
            'diachi' => $gv->diachi_giangvien,
            'sdt' => $gv->sdt_giangvien,
            'gioitinh' => $gv->gioitinh_giangvien,
        ]);
    }

    public function dangXuat()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Đăng xuất thành công']);
    }
}
