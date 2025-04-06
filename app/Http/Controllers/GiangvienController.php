<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GiangvienController extends Controller
{
    public function xuLyDangNhapGV(Request $request)
    {
        $request->validate([
            'maGV' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = DB::table('giangvien')->where('Magiangvien', $request->maGV)->first();

        if ($user && sha1($request->password) == $user->password_giangvien) {
            Session::put('maGV', $user->Magiangvien);
            Session::put('nameGV', $user->name_giangvien);

            return response()->json([
                'message' => 'Đăng nhập thành công',
                'canlogin' => true,
                'userName' => $user->name_giangvien
            ]);
        } else return response()->json([
            'message' => 'Đăng nhập không thành công',
            'canlogin' => false,
        ]);
    }
}
