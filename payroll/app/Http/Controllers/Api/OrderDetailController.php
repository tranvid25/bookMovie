<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAfterBookingJob;
use App\Models\Movie;
use App\Models\OrderDetail;
use App\Models\PointHistory;
use App\Models\Seat;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderDetailController extends Controller
{
    public function index(){
     $Order=OrderDetail::all();
     if($Order){
        return response()->json([
            'status'=>200,
            'content'=>$Order
        ]);
     }
     else{
        return response()->json([
            'status'=>404,
            'message'=>'not found Order'
        ]);
     }
    }
    public function show($id){
        $Order=OrderDetail::findOrFail($id);
        if($Order){
            return response()->json([
                'status'=>200,
                'message'=>$Order
            ]);
        }
        else{
            return response()->json([
                'status'=>404,
                'message'=>'not found Order'
            ]);
        }
    }
        public function store(Request $request)
{
    // 1. Validate dữ liệu đầu vào
    $validator = Validator::make($request->all(), [
        'maLichChieu' => 'required|exists:showtime,maLichChieu',
        'danhSachGhe' => 'required|string', // chuỗi dạng: "101,102,103"
        'name' => 'required|string|max:255',
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $validator->errors()
        ], 422);
    }
    DB::beginTransaction();
    // 2. Lấy thông tin lịch chiếu kèm phim và rạp
    try {
        $showtime = Showtime::with(['rapChieu', 'phim'])->where('maLichChieu', $request->maLichChieu)->first();
        if (!$showtime || !$showtime->rapChieu || !$showtime->phim) {
            DB::rollBack();
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy lịch chiếu'], 404);
        }

        // Kiểm tra lịch chiếu có phải trong tương lai không
        $ngayGioChieu = $showtime->ngayChieu . ' ' . $showtime->gioChieu;
        $ngayGioHienTai = now();

        if (strtotime($ngayGioChieu) <= strtotime($ngayGioHienTai)) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => 'Không thể đặt vé cho lịch chiếu đã qua hoặc đang diễn ra'
            ], 400);
        }

        $arrMaGhe = array_map('intval', explode(',', $request->danhSachGhe));

        // ⚠️ LOCK GHẾ để tránh bị cập nhật trùng
        $seats = Seat::whereIn('maGhe', $arrMaGhe)
                     ->where('maLichChieu', $request->maLichChieu)
                     ->lockForUpdate()
                     ->get();

        $gheDaDat = $seats->filter(fn($ghe) => $ghe->daDat);

        if ($gheDaDat->count() > 0) {
            DB::rollBack();
            return response()->json([
                'status' => 409,
                'message' => 'Một số ghế đã được đặt: ' . $gheDaDat->pluck('tenGhe')->implode(', ')
            ], 409);
        }

        $tongTien = $seats->sum('giaVe');
        $danhSachTenGhe = $seats->pluck('tenGhe')->implode(', ');

        $order = OrderDetail::create([
            'maLichChieu' => $request->maLichChieu,
            'maPhim' => $showtime->maPhim,
            'phim' => $showtime->phim->tenPhim,
            'rapChieu' => $showtime->rapChieu->tenRap,
            'gioChieu' => $showtime->gioChieu,
            'ngayChieu' => $showtime->ngayChieu,
            'danhSachGhe' => $danhSachTenGhe,
            'tongTien' => $tongTien,
            'userId' => Auth::id(),
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Đánh dấu ghế đã đặt
        Seat::whereIn('maGhe', $arrMaGhe)->update(['daDat' => 1]);
        ProcessAfterBookingJob::dispatch($order, Auth::user());
        DB::commit(); // ✅ Quan trọng: commit sau khi xong mọi thứ
        return response()->json([
            'status' => 200,
            'message' => 'Đặt vé thành công!',
            'content' => $order
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 500,
            'message' => 'Lỗi hệ thống',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function showByUser($id)
    {
        $order = OrderDetail::where('userId', $id)->get();
        if ($order) {
            return response()->json([
                'status' => 200,
                'content' => $order
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no such order found'
            ], 404);
        }
    }


    public function doanhthu($year)
    {
        $order = OrderDetail::whereYear('created_at', $year)->get();
        if ($order->count() >= 0) {
            return response()->json([
                'status' => 200,
                'content' => $order
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'no oder found'
            ], 404);
        }
    }


}
