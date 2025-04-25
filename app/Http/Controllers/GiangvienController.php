<?php

namespace App\Http\Controllers;

use App\Models\Giangvien;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class GiangvienController extends Controller
{
    public function dangNhap(Request $request)
    {
        $credentials = $request->only('Magiangvien', 'password');

        $gv = Giangvien::where('Magiangvien', $credentials['Magiangvien'])->first();

        if (!$gv || sha1($credentials['password']) !== $gv->password_giangvien) {
            return response()->json(['message' => 'Sai mã GV hoặc mật khẩu'], 401);
        } else {
            $token = JWTAuth::fromUser($gv);

            return response()->json([
                'message' => 'Đăng nhập thành công',
                'token' => $token,
                'name' => $gv->name_giangvien,
                'canlogin' => true,
            ]);
        }
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
    public function capnhat(Request $request)
    {
        $gv = JWTAuth::parseToken()->authenticate();

        $validated = $request->validate([
            'diachi' => 'nullable|string',
            'sdt' => 'nullable|string|max:15',
        ]);
        $gv->sdt_giangvien = $validated['sdt'];
        $gv->diachi_giangvien = $validated['diachi'];
        $gv->save();
    }
    public function dangXuat()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Đăng xuất thành công']);
    }
    public function lichgiangday()
    {
        $gv = JWTAuth::parseToken()->authenticate();

        $data = DB::table('lophoc')
            ->join('monhoc', 'lophoc.id_monhoc', '=', 'monhoc.id_monhoc')
            ->join('giangvien', 'giangvien.id_giangvien', '=', 'lophoc.id_giangvien')
            ->join('buoihoc', 'lophoc.id_lophoc', '=', 'buoihoc.id_lophoc')
            ->where('giangvien.id_giangvien', $gv->id_giangvien)
            ->select(
                'monhoc.mamon as maMon',
                'monhoc.name_monhoc as tenMon',
                'buoihoc.tietbd as tietBD',
                'buoihoc.tietkt as tietKT',
            )
            ->get();

        return response()->json(['data' => $data, 'message' => 'Lịch giảng dạy']);
    }
}
