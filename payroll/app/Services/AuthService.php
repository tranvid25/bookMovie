<?php
namespace App\Services;

use App\Exceptions\UserException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use App\Mail\SendNewPassword;
use App\Repositories\AuthRepository;
use Carbon\Carbon;

class AuthService
{
    protected $users;

    public function __construct(AuthRepository $users)
    {
        $this->users = $users;
    }

    public function signup(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->users->create($data);
    }
     public function login(array $credentials, bool $remember = false)
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($remember) {
            $token->expires_at = Carbon::now()->addWeek();
        }
        $token->save();

        return [
            'user' => $user,
            'accessToken' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
        ];
    }
    public function logout($user)
    {
        $token = $user->token();
        $ttl = Carbon::parse($token->expires_at)->diffInSeconds(now());
        Redis::setex("blacklist:token:{$token->id}", $ttl, true);
        $token->revoke();
    }

    public function resetPassword(string $email)
    {
        $user = $this->users->findByEmail($email);
        if (!$user) {
            throw new UserException();
        }

        $newPassword = Str::random(6);
        $this->users->updatePassword($user, Hash::make($newPassword));

        Mail::to($email)->queue(new SendNewPassword($newPassword));

        return $newPassword;
    }
}

