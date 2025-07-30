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
}
