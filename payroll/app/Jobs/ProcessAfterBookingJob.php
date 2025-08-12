<?php

namespace App\Jobs;

use App\Models\OrderDetail;
use App\Models\PointHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessAfterBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    public $user;

    public function __construct(OrderDetail $order, $user)
    {
        $this->order = $order;
        $this->user = $user;
    }

    public function handle()
    {
        // Gửi email
        Mail::send('mail.sendEmail', [
            'rapChieu' => $this->order->rapChieu,
            'phim' => $this->order->phim,
            'gioChieu' => $this->order->gioChieu,
            'ngayChieu' => $this->order->ngayChieu,
            'danhSachGhe' => $this->order->danhSachGhe,
            'tongTien' => $this->order->tongTien,
            'name' => $this->order->name,
            'email' => $this->order->email,
        ], function ($message) {
            $message->to($this->order->email, $this->order->name)
                    ->subject('TV - Thông tin đặt vé');
        });

        // Tích điểm
        if ($this->user) {
            $diemCong = floor($this->order->tongTien / 10000);
            $this->user->diem_tich_luy = $this->user->diem_tich_luy ?? 0;
            $this->user->diem_tich_luy += $diemCong;

            // Cập nhật rank
            if ($this->user->diem_tich_luy >= 3000) {
                $this->user->rank = 'kimcuong';
            } elseif ($this->user->diem_tich_luy >= 1500) {
                $this->user->rank = 'vang';
            } elseif ($this->user->diem_tich_luy >= 500) {
                $this->user->rank = 'bac';
            } else {
                $this->user->rank = 'thuong';
            }

            $this->user->save();

            PointHistory::create([
                'id_user' => $this->user->id,
                'loai' => '+',
                'so_diem' => $diemCong,
                'mo_ta' => 'Đặt vé phim ' . $this->order->phim,
            ]);
        }
    }
}

