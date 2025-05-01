<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\BuoiHoc;
use App\Models\Diemdanh;
use App\Models\LopHoc;
use App\Models\SinhVien;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Facades\Storage;

class DiemdanhController extends Controller
{
    public function taomaqr(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_buoihoc' => 'required|integer',
            ]);

            $id_buoihoc = $validated['id_buoihoc'];

            $buoiHoc = BuoiHoc::find($id_buoihoc);
            if (!$buoiHoc) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Buổi học không tồn tại',
                ], 404);
            }

            $id_lophoc = $buoiHoc->id_lophoc;


            $qrData = json_encode([
                'id_buoihoc' => $id_buoihoc,
                'id_lophoc' => $id_lophoc,
            ]);

            $fileName = 'qr_buoihoc_' . $id_buoihoc . '.png';
            $filePath = 'qr_codes/' . $fileName;
            $fullPath = storage_path('app/public/' . $filePath);

            $qrCode = QrCode::create($qrData)
                ->setSize(300);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $result->saveToFile($fullPath);

            $qrUrl = asset('storage/' . $filePath);

            return response()->json([
                'status' => 'success',
                'message' => 'Tạo mã QR thành công',
                'qr_url' => $qrUrl,
                'qr_data' => $qrData,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Lỗi khi tạo mã QR: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tạo mã QR: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function getBuoiHoc(Request $request)
    {
        try {
            $giangVien = $request->user();
            if (!$giangVien) {
                Log::error("Không tìm thấy giảng viên: " . json_encode($request->user()));
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy giảng viên',
                ], 404);
            }


            $buoiHocList = BuoiHoc::with(['lophoc', 'lophoc.monHoc'])
                ->whereHas('lophoc', function ($query) use ($giangVien) {
                    $query->where('id_giangvien', $giangVien->id_giangvien);
                })
                ->get();


            return response()->json([
                'status' => 'success',
                'message' => 'Lấy danh sách buổi học thành công',
                'data' => $buoiHocList->map(function ($buoiHoc) {
                    return [
                        'id_buoihoc' => $buoiHoc->id_buoihoc,
                        'display_name' => "{$buoiHoc->lophoc->name_lophoc} - {$buoiHoc->lophoc->monHoc->name_monhoc} (Tiết {$buoiHoc->tietbd}-{$buoiHoc->tietkt})",
                        'id_lophoc' => $buoiHoc->id_lophoc,
                        'ngayhoc' => $buoiHoc->ngayhoc,
                    ];
                }),
            ], 200);
        } catch (\Exception $e) {
            Log::error("Lỗi khi lấy danh sách buổi học: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách buổi học: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function getSinhVienByLopHoc(Request $request, $id_lophoc)
    {
        try {
            $giangVien = $request->user();
            if (!$giangVien) {
                Log::error("Không tìm thấy giảng viên: " . json_encode($request->user()));
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy giảng viên',
                ], 404);
            }


            $lopHoc = LopHoc::where('id_lophoc', $id_lophoc)
                ->where('id_giangvien', $giangVien->id_giangvien)
                ->first();
            if (!$lopHoc) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lớp học không tồn tại hoặc không thuộc giảng viên này',
                ], 404);
            }


            $sinhVienList = SinhVien::where('lop_sinhvien', $lopHoc->name_lophoc)
                ->get();


            return response()->json([
                'status' => 'success',
                'message' => 'Lấy danh sách sinh viên thành công',
                'data' => $sinhVienList->map(function ($sinhVien) {
                    return [
                        'id' => $sinhVien->id_sinhvien,
                        'mssv' => $sinhVien->mssv,
                        'name' => $sinhVien->name_sinhvien,
                        'class' => $sinhVien->lop_sinhvien,
                    ];
                }),
            ], 200);
        } catch (\Exception $e) {
            Log::error("Lỗi khi lấy danh sách sinh viên: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách sinh viên: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function getSinhVienDiemDanhByBuoiHoc(Request $request, $id_buoihoc)
    {
        try {
            
            if (!$id_buoihoc || !is_numeric($id_buoihoc)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ID buổi học không hợp lệ',
                ], 400);
            }


            $giangVien = $request->user();
            if (!$giangVien) {
                Log::error("Không tìm thấy giảng viên: " . json_encode($request->user()));
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy giảng viên',
                ], 404);
            }


        
            $buoiHoc = BuoiHoc::where('id_buoihoc', $id_buoihoc)
                ->whereHas('lophoc', function ($query) use ($giangVien) {
                    $query->where('id_giangvien', $giangVien->id_giangvien);
                })
                ->first();


            if (!$buoiHoc) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Buổi học không tồn tại hoặc không thuộc giảng viên này',
                ], 404);
            }


           
            $sinhVienList = SinhVien::whereIn('id_sinhvien', function ($query) use ($id_buoihoc) {
                $query->select('id_sinhvien')
                    ->from('diemdanh')
                    ->where('id_buoihoc', $id_buoihoc);
            })->get();


            return response()->json([
                'status' => 'success',
                'message' => 'Lấy danh sách sinh viên đã điểm danh thành công',
                'data' => $sinhVienList->map(function ($sinhVien) {
                    return [
                        'id' => $sinhVien->id_sinhvien,
                        'mssv' => $sinhVien->mssv,
                        'name' => $sinhVien->name_sinhvien,
                        'class' => $sinhVien->lop_sinhvien,
                    ];
                }),
            ], 200);
        } catch (\Exception $e) {
            Log::error("Lỗi khi lấy danh sách sinh viên đã điểm danh: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy danh sách sinh viên đã điểm danh: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function submitDiemDanh(Request $request)
    {
        $token = $request->query('token');


        if (!$token) {
            return response()->json(['message' => 'Thiếu token'], 400);
        }


        $buoiHocId = Cache::get("diemdanh_token_$token");


        if (!$buoiHocId) {
            return response()->json(['message' => 'Token không hợp lệ hoặc đã hết hạn'], 401);
        }


        $buoiHoc = Buoihoc::find($buoiHocId);
        if (!$buoiHoc) {
            return response()->json(['message' => 'Buổi học không tồn tại'], 404);
        }


        $sinhvien = auth()->user();


        if (!$sinhvien) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }


        $exists = Diemdanh::where('id_sinhvien', $sinhvien->id)
            ->where('id_buoihoc', $buoiHoc->id_buoihoc)
            ->exists();


        if ($exists) {
            return response()->json(['message' => 'Bạn đã điểm danh buổi học này rồi'], 409);
        }


        Diemdanh::create([
            'id_sinhvien' => $sinhvien->id,
            'id_buoihoc' => $buoiHoc->id_buoihoc,
            'time_diemdanh' => now(),
            'trangthai_diemdanh' => 'có mặt',
        ]);


        return response()->json([
            'message' => 'Điểm danh thành công',
            'buoi_hoc_id' => $buoiHoc->id_buoihoc,
            'sinhvien_id' => $sinhvien->id,
        ]);
    }
}
