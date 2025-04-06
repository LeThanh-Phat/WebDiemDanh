<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class sinhvienController extends Controller
{
    public function index()
    {
        return view('dangnhap');
    }
    public function xuLyDangNhap(Request $request)
    {
        $request->validate([
            'mssv' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = DB::table('sinhvien')->where('mssv', $request->mssv)->first();

        if ($user && sha1($request->password) == $user->password_sinhvien) {
            Session::put('mssv', $user->mssv);
            Session::put('name', $user->name_sinhvien);

            return response()->json([
                'message' => 'Đăng nhập thành công',
                'canlogin' => true,
                'userName' => $user->name_sinhvien
            ]);
        } else return response()->json([
            'message' => 'Đăng nhập không thành công',
            'canlogin' => false,
        ]);
    }
    public function dangXuat()
    {
        Session::flush();
        return redirect()->route('dangnhap');
    }
}
