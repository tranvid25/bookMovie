<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\SignupRequest;
use App\Http\Resources\UserResource;
use App\Mail\SendNewPassword;
use App\Models\User;
use App\Services\AuthService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function signup(SignupRequest $request)
    {
        $user = $this->auth->signup($request->validated());//lấy toàn bộ dữ liệu
        return response()->json(['status' => 200, 'user' => new UserResource($user)]);
    }

    public function login(LoginRequest $request)
    {
        $data = $this->auth->login(
            $request->only('email', 'password'),
            $request->boolean('remember', false)
        );

        if (!$data) {
            return response()->json(['status' => 401, 'message' => 'Invalid credentials'], 401);
        }

        return response()->json(['status' => 200] + $data);
    }

    public function logout(Request $request)
    {
        $this->auth->logout($request->user());
        return response()->json(['status' => 200, 'message' => 'Logged out']);
    }

    public function passwordRetrieval(PasswordResetRequest $request)
    {
        $this->auth->resetPassword($request->email);
        return response()->json(['status' => 200, 'message' => 'New password sent to email']);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => 200,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'diem_tich_luy' => $user->diem_tich_luy ?? 0,
                'rank' => $user->rank ?? 'thuong',
                'rank_info' => $this->getRankInfo($user->rank ?? 'thuong')
            ]
        ]);
    }

    private function getRankInfo($rank)
    {
        $rankInfo = [
            'thuong' => [
                'name' => 'Thành viên thường',
                'min_points' => 0,
                'max_points' => 499,
                'benefits' => ['Giảm giá 5% cho vé phim']
            ],
            'bac' => [
                'name' => 'Thành viên bạc',
                'min_points' => 500,
                'max_points' => 1499,
                'benefits' => ['Giảm giá 10% cho vé phim', 'Ưu tiên đặt vé']
            ],
            'vang' => [
                'name' => 'Thành viên vàng',
                'min_points' => 1500,
                'max_points' => 2999,
                'benefits' => ['Giảm giá 15% cho vé phim', 'Ưu tiên đặt vé', 'Quà tặng sinh nhật']
            ],
            'kimcuong' => [
                'name' => 'Thành viên kim cương',
                'min_points' => 3000,
                'max_points' => null,
                'benefits' => ['Giảm giá 20% cho vé phim', 'Ưu tiên đặt vé', 'Quà tặng sinh nhật', 'Vé xem phim miễn phí']
            ]
        ];

        return $rankInfo[$rank] ?? $rankInfo['thuong'];
    }

    // Method để test thêm điểm (chỉ dùng cho development)
    public function addTestPoints(Request $request)
    {
        $user = $request->user();
        $points = $request->input('points', 100);

        $user->diem_tich_luy = ($user->diem_tich_luy ?? 0) + $points;

        // Cập nhật rank
        if($user->diem_tich_luy >= 3000) {
            $user->rank = 'kimcuong';
        } elseif($user->diem_tich_luy >= 1500) {
            $user->rank = 'vang';
        } elseif($user->diem_tich_luy >= 500) {
            $user->rank = 'bac';
        } else {
            $user->rank = 'thuong';
        }

        $user->save();

        // Lưu lịch sử điểm
        \App\Models\PointHistory::create([
            'id_user' => $user->id,
            'loai' => '+',
            'so_diem' => $points,
            'mo_ta' => 'Test thêm điểm',
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Đã thêm {$points} điểm",
            'new_points' => $user->diem_tich_luy,
            'new_rank' => $user->rank
        ]);
    }
    public function loginGoogle(Request $request)
{
    try {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $client = new Client();

        $response = $client->get('https://oauth2.googleapis.com/tokeninfo', [
            'query' => ['id_token' => $request->id_token],
        ]);

        $googleUser = json_decode($response->getBody(), true);

        // Debug log
        

    if (!isset($googleUser['email_verified']) || $googleUser['email_verified'] !== 'true') {
        return response()->json([
            'status' => false,
            'message' => 'Email chưa xác thực'
        ], 403);
    }

    $email = $googleUser['email'];
    $name = $googleUser['name'] ?? explode('@', $email)[0];
    $avatar = $googleUser['picture'] ?? null;

    $user = User::firstOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'avatar' => $avatar,
            'password' => bcrypt(Str::random(16)),
            'role' => 'user',
            'diem_tich_luy' => 0,
            'rank' => 'thuong',
        ]
    );

    $token = $user->createToken('UserToken')->accessToken;

        return response()->json([
            'status' => true,
            'token' => $token,
            'user' => $user,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Lỗi đăng nhập Google: ' . $e->getMessage()
        ], 500);
    }
}
}
