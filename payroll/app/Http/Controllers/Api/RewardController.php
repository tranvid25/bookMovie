<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardExchange;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RewardController extends Controller
{
    public function index()
    {
        $rewards = Reward::where('trang_thai', 'active')
                         ->where('so_luong_con', '>', 0)
                         ->get();
        
        return response()->json([
            'status' => 200,
            'message' => 'Danh sách quà tặng',
            'content' => $rewards
        ]);
    }

    public function show($id)
    {
        $reward = Reward::find($id);
        if (!$reward) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy quà tặng'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Chi tiết quà tặng',
            'content' => $reward
        ]);
    }

    public function exchange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reward_id' => 'required|exists:rewards,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $reward = Reward::find($request->reward_id);

            // Kiểm tra quà có còn không
            if ($reward->so_luong_con <= 0) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Quà tặng đã hết hàng'
                ], 400);
            }

            // Kiểm tra điểm của user
            if ($user->diem_tich_luy < $reward->so_diem_can) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Điểm không đủ để đổi quà. Cần ' . $reward->so_diem_can . ' điểm'
                ], 400);
            }

            // Trừ điểm của user
            $user->diem_tich_luy -= $reward->so_diem_can;
            $user->save();

            // Giảm số lượng quà
            $reward->so_luong_con -= 1;
            $reward->save();

            // Tạo lịch sử đổi quà
            RewardExchange::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'so_diem_da_dung' => $reward->so_diem_can,
                'trang_thai' => 'pending',
                'ngay_nhan' => now()->addDays(3) // Giao sau 3 ngày
            ]);

            // Lưu lịch sử điểm
            \App\Models\PointHistory::create([
                'id_user' => $user->id,
                'loai' => '-',
                'so_diem' => $reward->so_diem_can,
                'mo_ta' => 'Đổi quà: ' . $reward->ten_qua,
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Đổi quà thành công! Quà sẽ được giao sau 3 ngày.',
                'content' => [
                    'reward' => $reward,
                    'remaining_points' => $user->diem_tich_luy
                ]
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

    public function userExchanges()
    {
        $user = Auth::user();
        $exchanges = RewardExchange::with('reward')
                                  ->where('user_id', $user->id)
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return response()->json([
            'status' => 200,
            'message' => 'Lịch sử đổi quà',
            'content' => $exchanges
        ]);
    }
} 