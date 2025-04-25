<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log; // Thêm dòng này vào đầu file
use Illuminate\Support\Facades\Validator; // Thêm import này
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Giangvien;
use App\Models\Sinhvien;
use App\Models\MonHoc;
use App\Models\SinhVienLopHoc;
use App\Models\BuoiHoc;
use App\Models\LopHoc;

use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        // Validate dữ liệu đầu vào
        $credentials = $request->validate([
            'name_admin' => 'required|string',
            'password_admin' => 'required|string',
        ]);

        // Tìm admin theo tên
        $admin = Admin::where('name_admin', $credentials['name_admin'])->first();

        // Kiểm tra username và mật khẩu (so sánh bằng SHA1)
        if (!$admin || sha1($credentials['password_admin']) !== $admin->password_admin) {
            return response()->json(['message' => 'Sai tên đăng nhập hoặc mật khẩu'], 401);
        } else {
            // Tạo token cho admin
            $token = $admin->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Đăng nhập thành công',
                'token' => $token,
                'name' => $admin->name_admin,
                'id_admin' => $admin->id_admin, // Trả về id_admin
                'canlogin' => true,
            ], 200);
        }
    }
    //     public function logout(Request $request)
    // {
    //     // Xóa token hiện tại của admin
    //     $request->user()->currentAccessToken()->delete();

    //     return response()->json(['message' => 'Đăng xuất thành công'], 200);
    // }

    public function getGiangViens(Request $request)
    {
        $admin = $request->user();

        if (!$admin instanceof Admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $giangViens = Giangvien::all();
        return response()->json($giangViens, 200);
    }
    // Thêm giảng viên mới
    public function themgiangvien(Request $request)
    {
        try {
            $validated = $request->validate([
                'Magiangvien' => 'required|string|unique:giangvien,Magiangvien',
                'name_giangvien' => 'required|string|max:50',
                'sdt_giangvien' => 'required|string|max:20',
                'email_giangvien' => 'required|email|unique:giangvien,email_giangvien',
                'password_giangvien' => 'required|string|min:3',
                'diachi_giangvien' => 'required|string|max:100',
                'gioitinh_giangvien' => 'nullable|string|max:10',
            ]);

            $teacher = Giangvien::create([
                'Magiangvien' => $validated['Magiangvien'],
                'name_giangvien' => $validated['name_giangvien'],
                'sdt_giangvien' => $validated['sdt_giangvien'],
                'email_giangvien' => $validated['email_giangvien'],
                'password_giangvien' => sha1($validated['password_giangvien']), // Sử dụng SHA1 để mã hóa mật khẩu
                'diachi_giangvien' => $validated['diachi_giangvien'],
                'gioitinh_giangvien' => $validated['gioitinh_giangvien'],
                'id_admin' => $request->user()->id_admin, // Lấy id_admin từ admin đang đăng nhập
            ]);

            return response()->json(['message' => 'Teacher created successfully', 'teacher' => $teacher], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    // Cập nhật thông tin giảng viên<?php
    public function capnhatgiangvien(Request $request, $id)
    {
        // Tìm giảng viên theo id_giangvien
        $teacher = Giangvien::where('id_giangvien', $id)->first();

        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        $validated = $request->validate([
            'Magiangvien' => 'required|string|unique:giangvien,Magiangvien,' . $teacher->id_giangvien . ',id_giangvien',
            'name_giangvien' => 'required|string|max:50',
            'sdt_giangvien' => 'required|string|max:20',
            'email_giangvien' => 'required|email|unique:giangvien,email_giangvien,' . $teacher->id_giangvien . ',id_giangvien',
            'password_giangvien' => 'nullable|string|min:6',
            'diachi_giangvien' => 'nullable|string|max:50',
            'gioitinh_giangvien' => 'nullable|string|max:10',
        ]);

        $teacher->update([
            'Magiangvien' => $validated['Magiangvien'],
            'name_giangvien' => $validated['name_giangvien'],
            'sdt_giangvien' => $validated['sdt_giangvien'],
            'email_giangvien' => $validated['email_giangvien'],
            'password_giangvien' => isset($validated['password_giangvien']) ? sha1($validated['password_giangvien']) : $teacher->password_giangvien,
            'diachi_giangvien' => $validated['diachi_giangvien'] ?? $teacher->diachi_giangvien,
            'gioitinh_giangvien' => $validated['gioitinh_giangvien'] ?? $teacher->gioitinh_giangvien,
        ]);

        return response()->json(['message' => 'Teacher updated successfully', 'teacher' => $teacher], 200);
    }

    // Lấy danh sách sinh viên
    public function getSinhViens(Request $request)
    {
        $admin = $request->user();

        if (!$admin instanceof Admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $sinhViens = Sinhvien::all();
        return response()->json($sinhViens, 200);
    }

    // Thêm sinh viên mới
    public function themSinhVien(Request $request)
    {
        try {
            $validated = $request->validate([
                'mssv' => 'required|string|unique:sinhvien,mssv',
                'name_sinhvien' => 'required|string|max:50',
                'sdt_sinhvien' => 'required|string|max:20',
                'email_sinhvien' => 'required|email|unique:sinhvien,email_sinhvien',
                'password_sinhvien' => 'required|string|min:3',
                'diachi_sinhvien' => 'required|string|max:100',
                'gioitinh_sinhvien' => 'nullable|string|max:10',
                'ngaysinh_sinhvien' => 'required|date',
                'lop_sinhvien' => 'required|string|max:20',
            ]);

            $student = Sinhvien::create([
                'mssv' => $validated['mssv'],
                'name_sinhvien' => $validated['name_sinhvien'],
                'sdt_sinhvien' => $validated['sdt_sinhvien'],
                'email_sinhvien' => $validated['email_sinhvien'],
                'password_sinhvien' => Hash::make($validated['password_sinhvien']), // Sử dụng bcrypt
                'diachi_sinhvien' => $validated['diachi_sinhvien'],
                'gioitinh_sinhvien' => $validated['gioitinh_sinhvien'],
                'ngaysinh_sinhvien' => $validated['ngaysinh_sinhvien'],
                'lop_sinhvien' => $validated['lop_sinhvien'],
                'id_admin' => $request->user()->id_admin,
            ]);

            return response()->json(['message' => 'Student created successfully', 'student' => $student], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    // Cập nhật thông tin sinh viên
    public function capnhatSinhVien(Request $request, $id)
    {
        $student = Sinhvien::where('id_sinhvien', $id)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $validated = $request->validate([
            'mssv' => 'required|string|unique:sinhvien,mssv,' . $student->id_sinhvien . ',id_sinhvien',
            'name_sinhvien' => 'required|string|max:50',
            'sdt_sinhvien' => 'required|string|max:20',
            'email_sinhvien' => 'required|email|unique:sinhvien,email_sinhvien,' . $student->id_sinhvien . ',id_sinhvien',
            'password_sinhvien' => 'nullable|string|min:6',
            'diachi_sinhvien' => 'nullable|string|max:50',
            'gioitinh_sinhvien' => 'nullable|string|max:10',
            'ngaysinh_sinhvien' => 'required|date',
            'lop_sinhvien' => 'required|string|max:20',
        ]);

        $student->update([
            'mssv' => $validated['mssv'],
            'name_sinhvien' => $validated['name_sinhvien'],
            'sdt_sinhvien' => $validated['sdt_sinhvien'],
            'email_sinhvien' => $validated['email_sinhvien'],
            'password_sinhvien' => isset($validated['password_sinhvien']) ? Hash::make($validated['password_sinhvien']) : $student->password_sinhvien,
            'diachi_sinhvien' => $validated['diachi_sinhvien'] ?? $student->diachi_sinhvien,
            'gioitinh_sinhvien' => $validated['gioitinh_sinhvien'] ?? $student->gioitinh_sinhvien,
            'ngaysinh_sinhvien' => $validated['ngaysinh_sinhvien'],
            'lop_sinhvien' => $validated['lop_sinhvien'],
        ]);

        return response()->json(['message' => 'Student updated successfully', 'student' => $student], 200);
    }
    // Lấy danh sách môn học
    public function getMonHocs(Request $request)
    {
        $admin = $request->user();

        if (!$admin instanceof Admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $monHocs = MonHoc::all();
        return response()->json($monHocs, 200);
    }

    // Thêm môn học mới
    public function themMonHoc(Request $request)
    {
        try {
            $validated = $request->validate([
                'mamon' => 'required|string|unique:monhoc,mamon',
                'name_monhoc' => 'required|string|max:50',
            ]);

            $monHoc = MonHoc::create([
                'mamon' => $validated['mamon'],
                'name_monhoc' => $validated['name_monhoc'],
                'id_admin' => $request->user()->id_admin,
            ]);

            return response()->json(['message' => 'Subject created successfully', 'monhoc' => $monHoc], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    // Cập nhật thông tin môn học
    public function capnhatMonHoc(Request $request, $id)
    {
        $monHoc = MonHoc::where('id_monhoc', $id)->first();

        if (!$monHoc) {
            return response()->json(['message' => 'Subject not found'], 404);
        }

        $validated = $request->validate([
            'mamon' => 'required|string|unique:monhoc,mamon,' . $monHoc->id_monhoc . ',id_monhoc',
            'name_monhoc' => 'required|string|max:50',
        ]);

        $monHoc->update([
            'mamon' => $validated['mamon'],
            'name_monhoc' => $validated['name_monhoc'],
        ]);

        return response()->json(['message' => 'Subject updated successfully', 'monhoc' => $monHoc], 200);
    }
    public function xoaMonHoc(Request $request, $id)
    {
        $monHoc = MonHoc::where('id_monhoc', $id)->first();

        if (!$monHoc) {
            return response()->json(['message' => 'Subject not found'], 404);
        }

        try {
            $monHoc->delete();
            return response()->json(['message' => 'Subject deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
    public function getLopHocs(Request $request)
    {
        $admin = $request->user();

        if (!$admin instanceof Admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $lopHocs = LopHoc::with(['monHoc', 'giangVien', 'sinhviens'])
            ->get()
            ->map(function ($lopHoc) {
                return [
                    'id_lophoc' => $lopHoc->id_lophoc,
                    'name_lophoc' => $lopHoc->name_lophoc,
                    'monhoc' => $lopHoc->monHoc ? $lopHoc->monHoc->name_monhoc : null,
                    'giangvien' => $lopHoc->giangVien ? $lopHoc->giangVien->name_giangvien : null,
                    'student_count' => $lopHoc->sinhviens->count(),
                    'hocky' => $lopHoc->hocky,
                ];
            });

        return response()->json($lopHocs, 200);
    }

    public function themLopHoc(Request $request)
    {
        try {
            $validated = $request->validate([
                'name_lophoc' => 'required|string|max:50',
                'id_monhoc' => 'required|exists:monhoc,id_monhoc',
                'id_giangvien' => 'required|exists:giangvien,id_giangvien',
                'hocky' => 'required|string|max:20',
            ]);

            $lopHoc = LopHoc::create([
                'name_lophoc' => $validated['name_lophoc'],
                'id_monhoc' => $validated['id_monhoc'],
                'id_giangvien' => $validated['id_giangvien'],
                'hocky' => $validated['hocky'],
                'id_admin' => $request->user()->id_admin,
            ]);

            return response()->json(['message' => 'Class created successfully', 'lophoc' => $lopHoc], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function capNhatLopHoc(Request $request, $id)
    {
        try {
            $admin = $request->user();
            if (!$admin instanceof Admin) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $lopHoc = LopHoc::find($id);
            if (!$lopHoc) {
                return response()->json(['message' => 'Class not found'], 404);
            }

            $validated = $request->validate([
                'name_lophoc' => 'required|string|max:50',
                'id_monhoc' => 'required|exists:monhoc,id_monhoc',
                'id_giangvien' => 'required|exists:giangvien,id_giangvien',
                'hocky' => 'required|string|max:20',
            ]);

            $lopHoc->name_lophoc = $validated['name_lophoc'];
            $lopHoc->id_monhoc = $validated['id_monhoc'];
            $lopHoc->id_giangvien = $validated['id_giangvien'];
            $lopHoc->hocky = $validated['hocky'];
            $lopHoc->save();

            return response()->json(['message' => 'Class updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function xoaLopHoc(Request $request, $id)
    {
        $lopHoc = LopHoc::where('id_lophoc', $id)->first();

        if (!$lopHoc) {
            return response()->json(['message' => 'Class not found'], 404);
        }

        try {
            $lopHoc->delete();
            return response()->json(['message' => 'Class deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    // Thêm sinh viên vào lớp học
    public function themSinhVienVaoLopHoc(Request $request)
    {
        try {
            Log::info('Request data for themSinhVienVaoLopHoc:', $request->all());

            $validator = Validator::make($request->all(), [
                'id_lophoc' => 'required|exists:lophoc,id_lophoc',
                'sinhviens' => 'required|array',
                'sinhviens.*' => 'exists:sinhvien,id_sinhvien',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed in themSinhVienVaoLopHoc:', $validator->errors()->toArray());
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $id_lophoc = $request->input('id_lophoc');
            $sinhViens = $request->input('sinhviens');

            Log::info('Validated data:', ['id_lophoc' => $id_lophoc, 'sinhviens' => $sinhViens]);

            foreach ($sinhViens as $id_sinhvien) {
                $exists = SinhVienLopHoc::where('id_lophoc', $id_lophoc)
                    ->where('id_sinhvien', $id_sinhvien)
                    ->exists();

                if (!$exists) {
                    SinhVienLopHoc::create([
                        'id_lophoc' => $id_lophoc,
                        'id_sinhvien' => $id_sinhvien,
                    ]);
                    Log::info('Added student to class:', ['id_lophoc' => $id_lophoc, 'id_sinhvien' => $id_sinhvien]);
                }
            }

            $studentCount = SinhVienLopHoc::where('id_lophoc', $id_lophoc)->count();

            return response()->json([
                'message' => 'Students added to class successfully',
                'student_count' => $studentCount,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error in themSinhVienVaoLopHoc: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
    public function getSinhVienByLopHoc(Request $request, $id_lophoc)
    {
        try {
            $admin = $request->user();
            if (!$admin instanceof Admin) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $lopHoc = LopHoc::with('sinhviens')->find($id_lophoc);
            if (!$lopHoc) {
                return response()->json(['message' => 'Class not found'], 404);
            }

            $sinhViens = $lopHoc->sinhviens->map(function ($sinhVien) {
                return [
                    'id_sinhvien' => $sinhVien->id_sinhvien,
                    'mssv' => $sinhVien->mssv,
                    'name_sinhvien' => $sinhVien->name_sinhvien,
                    'lop_sinhvien' => $sinhVien->lop_sinhvien,
                ];
            });

            return response()->json($sinhViens, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    // Lấy danh sách lịch học theo lớp học
    public function getLichHocByLopHoc(Request $request, $id_lophoc)
    {
        try {
            $admin = $request->user();
            if (!$admin instanceof Admin) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $lopHoc = LopHoc::with(['buoiHocs', 'monHoc', 'giangVien'])->find($id_lophoc);
            if (!$lopHoc) {
                return response()->json(['message' => 'Class not found'], 404);
            }

            $lichHocs = $lopHoc->buoiHocs->map(function ($buoiHoc) use ($lopHoc) {
                return [
                    'id_buoihoc' => $buoiHoc->id_buoihoc,
                    'subject' => $lopHoc->monHoc ? $lopHoc->monHoc->name_monhoc : null,
                    'teacher' => $lopHoc->giangVien ? $lopHoc->giangVien->name_giangvien : null,
                    'group' => "Tiết {$buoiHoc->tietbd} → {$buoiHoc->tietkt}",
                    'room' => $buoiHoc->phonghoc,
                    'date' => $buoiHoc->ngayhoc, // Chỉ sử dụng ngày học
                ];
            });

            return response()->json($lichHocs, 200);
        } catch (\Exception $e) {
            Log::error('Error in getLichHocByLopHoc:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    // Thêm lịch học mới cho lớp học
    public function themLichHoc(Request $request)
    {
        try {
            $admin = $request->user();
            if (!$admin instanceof Admin) {
                Log::error('User is not an Admin instance', ['user' => $admin]);
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Kiểm tra id_admin
            if (!isset($admin->id_admin) || is_null($admin->id_admin)) {
                Log::error('Admin ID is missing or null', ['admin' => $admin]);
                return response()->json(['message' => 'Admin ID is missing or invalid'], 400);
            }

            Log::info('Request data for themLichHoc:', $request->all());

            $validated = $request->validate([
                'id_lophoc' => 'required|exists:lophoc,id_lophoc',
                'tietbd' => 'required|integer|min:1|max:12',
                'tietkt' => 'required|integer|min:1|max:12|gte:tietbd',
                'phonghoc' => 'required|string|max:20',
                'ngayhoc' => 'required|date',
            ]);

            Log::info('Validated data:', $validated);
            Log::info('Admin ID:', ['id_admin' => $admin->id_admin]);

            $buoiHoc = BuoiHoc::create([
                'id_lophoc' => $validated['id_lophoc'],
                'tietbd' => $validated['tietbd'],
                'tietkt' => $validated['tietkt'],
                'phonghoc' => $validated['phonghoc'],
                'ngayhoc' => $validated['ngayhoc'],
                'id_admin' => $admin->id_admin,


            ]);

            return response()->json(['message' => 'Schedule created successfully', 'buoihoc' => $buoiHoc], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in themLichHoc:', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in themLichHoc:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'admin_data' => $admin,
            ]);
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    // Cập nhật lịch học
    public function capNhatLichHoc(Request $request, $id_buoihoc)
    {
        try {
            $admin = $request->user();
            if (!$admin instanceof Admin) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $buoiHoc = BuoiHoc::find($id_buoihoc);
            if (!$buoiHoc) {
                return response()->json(['message' => 'Schedule not found'], 404);
            }

            $validated = $request->validate([
                'tietbd' => 'required|integer|min:1|max:12',
                'tietkt' => 'required|integer|min:1|max:12|gte:tietbd',
                'phonghoc' => 'required|string|max:20',
                'ngayhoc' => 'required|date',
            ]);

            $buoiHoc->update([
                'tietbd' => $validated['tietbd'],
                'tietkt' => $validated['tietkt'],
                'phonghoc' => $validated['phonghoc'],
                'ngayhoc' => $validated['ngayhoc'],
            ]);

            return response()->json(['message' => 'Schedule updated successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in capNhatLichHoc:', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in capNhatLichHoc:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    // Xóa lịch học
    public function xoaLichHoc(Request $request, $id_buoihoc)
    {
        try {
            $admin = $request->user();
            if (!$admin instanceof Admin) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $buoiHoc = BuoiHoc::find($id_buoihoc);
            if (!$buoiHoc) {
                return response()->json(['message' => 'Schedule not found'], 404);
            }

            $buoiHoc->delete();
            return response()->json(['message' => 'Schedule deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error in xoaLichHoc:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
