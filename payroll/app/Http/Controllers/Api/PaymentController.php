<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    private function getAccessToken()
    {
        $auth=Http::asForm()->withBasicAuth(
            env('PAYPAL_CLIENT_ID'),
            env('PAYPAL_SECRET')
        )->post("https://api-m.sandbox.paypal.com/v1/oauth2/token", [
            'grant_type' => 'client_credentials'
        ])->json();
        return $auth['access_token'];
    }
     public function createOrder(Request $request)
    {
        $booking = OrderDetail::findOrFail($request->maOrder);

        // Convert VND -> USD
        $amountUSD = round($booking->amount / 24000, 2);

        $accessToken = $this->getAccessToken();

        $order = Http::withToken($accessToken)
            ->post("https://api-m.sandbox.paypal.com/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'description' => "Đặt vé phim {$booking->movie_name}",
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $amountUSD,
                    ],
                ]],
            ])->json();

        return response()->json([
            'orderId' => $order['id'],
            'amountUSD' => $amountUSD,
        ]);
    }

    // 2. BE capture order sau khi user approve
    public function captureOrder(Request $request)
    {

        $booking = OrderDetail::findOrFail($request->maOrder);
        $accessToken = $this->getAccessToken();

        $capture = Http::withToken($accessToken)
            ->post("https://api-m.sandbox.paypal.com/v2/checkout/orders/{$request->orderId}/capture")
            ->json();

        if (isset($capture['status']) && $capture['status'] === 'COMPLETED') {
            $booking->update([
                'status' => 'paid',
                'paypal_order_id' => $request->orderId,
                'payer_email' => $capture['payer']['email_address'] ?? null,
            ]);

            return response()->json(['success' => true, 'capture' => $capture]);
        }

        return response()->json(['success' => false, 'capture' => $capture]);
    }
}
