<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class CheckRevokedToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle($request, Closure $next)
{
    try {
        $token = $request->user()->token();

        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tokenId = $token->id;

        // Kiểm tra Redis có sẵn không
        try {
            if (Redis::get("blacklist:token:$tokenId")) {
                return response()->json(['message' => 'Token has been revoked'], 401);
            }
        } catch (\Exception $e) {
            // Nếu Redis không có sẵn, bỏ qua việc kiểm tra blacklist
            \Log::warning('Redis not available for token blacklist check: ' . $e->getMessage());
        }

        return $next($request);
    } catch (\Exception $e) {
        \Log::error('Error in CheckRevokedToken middleware: ' . $e->getMessage());
        return response()->json(['message' => 'Authentication error'], 401);
    }
}

}
