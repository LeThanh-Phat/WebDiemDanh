<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SinhVien;
use App\Models\LopHoc;
use App\Models\BuoiHoc;
use App\Models\GiangVien;
use App\Models\DiemDanh;
use App\Models\MonHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache; // Import facade Cache
use Illuminate\Support\Facades\Log;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'mssv' => 'required|string',
            'password_sinhvien' => 'required',
        ]);

        $sinhVien = SinhVien::where('mssv', $request->mssv)->first();

        if ($sinhVien && Hash::check($request->password_sinhvien, $sinhVien->password_sinhvien)) {
            $token = $sinhVien->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Đăng nhập thành công',
                'data' => [
                    'sinh_vien' => $sinhVien,
                    'token' => $token,
                ],
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'MSSV hoặc mật khẩu không đúng',
        ], 401);
    }

    
    public function capnhatSV(Request $request)
{
    try {
      
        $sinhVien = $request->user();
        if (!$sinhVien) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy sinh viên',
            ], 404);
        }

        $validated = $request->validate([
            'diachi_sinhvien' => 'required|string|max:255',
            'sdt_sinhvien' => 'required|string|max:15',
        ]);

        $sinhVien->diachi_sinhvien = $validated['diachi_sinhvien'];
        $sinhVien->sdt_sinhvien = $validated['sdt_sinhvien'];
        $sinhVien->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thông tin thành công',
            'data' => $sinhVien,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Có lỗi xảy ra khi cập nhật thông tin: ' . $e->getMessage(),
        ], 500);
    }
}

    public function thoikhoabieu(Request $request)
{
    try {
        // Lấy sinh viên đã đăng nhập
        $sinhVien = $request->user();
        if (!$sinhVien) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy sinh viên',
            ], 404);
        }

   
        $hocky = $request->query('hocky', 'HK1');

        
        $lopHocs = $sinhVien->lopHocs()
            ->where('hocky', $hocky) // Lọc theo học kỳ
            ->with([
                'monHoc',
                'giangVien',
                'buoiHocs'
            ])
            ->get();

        $thoiKhoaBieu = [];

       
        foreach ($lopHocs as $lopHoc) {
            foreach ($lopHoc->buoiHocs as $buoiHoc) {
                $thoiKhoaBieu[] = [
                    'ma_mon_hoc' => $lopHoc->monHoc->mamon,
                    'ten_mon_hoc' => $lopHoc->monHoc->name_monhoc,
                    'nhom_mon_hoc' => $lopHoc->name_lophoc,
                    'so_tiet' => $buoiHoc->tietkt - $buoiHoc->tietbd + 1,
                    'tiet_bat_dau' => $buoiHoc->tietbd,
                    'tiet_ket_thuc' => $buoiHoc->tietkt,
                    'phong' => $buoiHoc->phonghoc,
                    'giang_vien' => $lopHoc->giangVien->name_giangvien,
                    'thoi_gian' => $buoiHoc->ngayhoc,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy thời khóa biểu thành công',
            'data' => $thoiKhoaBieu,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Có lỗi xảy ra khi lấy thời khóa biểu: ' . $e->getMessage(),
        ], 500);
    }
    }

    public function diemdanh(Request $request)
    {
        try {
            $sinhVien = $request->user();
            if (!$sinhVien) {
                Log::error("Không tìm thấy sinh viên: " . json_encode($request->user()));
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy sinh viên',
                ], 404);
            }

            $validated = $request->validate([
                'qr_data' => 'required|string',
            ]);

            // Giải mã dữ liệu từ mã QR
            $qrData = json_decode($validated['qr_data'], true);
            if (!$qrData || !isset($qrData['id_buoihoc']) || !isset($qrData['id_lophoc'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dữ liệu mã QR không hợp lệ',
                ], 400);
            }

            $id_buoihoc = $qrData['id_buoihoc'];
            $id_lophoc = $qrData['id_lophoc'];

            // Kiểm tra buổi học
            $buoiHoc = BuoiHoc::where('id_buoihoc', $id_buoihoc)
                ->where('id_lophoc', $id_lophoc)
                ->first();
            if (!$buoiHoc) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Buổi học không tồn tại',
                ], 404);
            }

            // Kiểm tra sinh viên thuộc lớp học
            $sinhVienData = SinhVien::find($sinhVien->id_sinhvien);
            if (!$sinhVienData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sinh viên không tồn tại',
                ], 404);
            }

            $lophoc = \App\Models\LopHoc::find($id_lophoc);
            if (!$lophoc) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lớp học không tồn tại',
                ], 404);
            }

            if ($sinhVienData->lop_sinhvien !== $lophoc->name_lophoc) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không thuộc lớp học này',
                ], 403);
            }

            // Kiểm tra xem sinh viên đã điểm danh chưa
            $existingDiemDanh = DiemDanh::where('id_sinhvien', $sinhVien->id_sinhvien)
                ->where('id_buoihoc', $id_buoihoc)
                ->first();
            if ($existingDiemDanh) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn đã điểm danh cho buổi học này',
                ], 400);
            }

            // Lấy id_monhoc từ bảng lophoc
            $id_monhoc = $lophoc->id_monhoc;
            if (!$id_monhoc) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy môn học cho lớp học này',
                ], 404);
            }

            // Lưu điểm danh (bỏ trangthai_diemdanhh)
            $diemDanh = new DiemDanh();
            $diemDanh->id_sinhvien = $sinhVien->id_sinhvien;
            $diemDanh->id_buoihoc = $id_buoihoc;
            $diemDanh->trangthai_diemdanh = 1;
            $diemDanh->time_diemdanh = now();
            $diemDanh->id_monhoc = $id_monhoc;
            $diemDanh->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Điểm danh thành công',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Lỗi khi điểm danh: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi điểm danh: ' . $e->getMessage(),
            ], 500);
        }
    }
   
    public function ketquadiemdanh(Request $request)
    {
        try {
            $sinhVien = $request->user();
            if (!$sinhVien) {
                Log::error("Không tìm thấy sinh viên: " . json_encode($request->user()));
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy sinh viên',
                ], 404);
            }

            // Lấy dữ liệu từ bảng diemdanh
            $attendanceRecords = DiemDanh::where('id_sinhvien', $sinhVien->id_sinhvien)
                ->with([
                    'sinhVien', // Lấy thông tin sinh viên
                    'buoiHoc', // Lấy thông tin buổi học
                    'monHoc' // Lấy thông tin môn học
                ])
                ->get();

            Log::info("Dữ liệu kết quả điểm danh: " . $attendanceRecords->toJson());

            return response()->json([
                'status' => 'success',
                'message' => 'Lấy kết quả điểm danh thành công',
                'data' => $attendanceRecords,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Lỗi khi lấy kết quả điểm danh: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy kết quả điểm danh: ' . $e->getMessage(),
            ], 500);
        }
    }
//================================================================

}