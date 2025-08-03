<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reward;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $rewards = [
            [
                'ten_qua' => 'Vé xem phim miễn phí',
                'mo_ta' => 'Vé xem phim bất kỳ tại rạp PHTV Cinema. Có thể sử dụng cho tất cả các phim đang chiếu.',
                'hinh_anh' => null,
                'so_diem_can' => 500,
                'so_luong_con' => 50,
                'trang_thai' => 'active'
            ],
            [
                'ten_qua' => 'Combo bắp nước',
                'mo_ta' => 'Combo bắp rang bơ và nước ngọt size lớn. Thưởng thức cùng khi xem phim.',
                'hinh_anh' => null,
                'so_diem_can' => 200,
                'so_luong_con' => 100,
                'trang_thai' => 'active'
            ],
            [
                'ten_qua' => 'Giảm giá 20% vé phim',
                'mo_ta' => 'Coupon giảm giá 20% cho lần đặt vé tiếp theo. Có hiệu lực trong 30 ngày.',
                'hinh_anh' => null,
                'so_diem_can' => 300,
                'so_luong_con' => 75,
                'trang_thai' => 'active'
            ],
            [
                'ten_qua' => 'Áo thun PHTV Cinema',
                'mo_ta' => 'Áo thun cotton cao cấp với logo PHTV Cinema. Size M, L, XL có sẵn.',
                'hinh_anh' => null,
                'so_diem_can' => 800,
                'so_luong_con' => 25,
                'trang_thai' => 'active'
            ],
            [
                'ten_qua' => 'Mũ bóng chày PHTV',
                'mo_ta' => 'Mũ bóng chày thời trang với logo PHTV Cinema. Phù hợp cho mọi lứa tuổi.',
                'hinh_anh' => null,
                'so_diem_can' => 400,
                'so_luong_con' => 40,
                'trang_thai' => 'active'
            ],
            [
                'ten_qua' => 'Túi đeo chéo PHTV',
                'mo_ta' => 'Túi đeo chéo thời trang với logo PHTV Cinema. Chất liệu vải bền đẹp.',
                'hinh_anh' => null,
                'so_diem_can' => 600,
                'so_luong_con' => 30,
                'trang_thai' => 'active'
            ],
            [
                'ten_qua' => 'Bình nước PHTV',
                'mo_ta' => 'Bình nước inox cao cấp với logo PHTV Cinema. Dung tích 500ml.',
                'hinh_anh' => null,
                'so_diem_can' => 350,
                'so_luong_con' => 60,
                'trang_thai' => 'active'
            ],
            [
                'ten_qua' => 'Sổ tay PHTV Cinema',
                'mo_ta' => 'Sổ tay cao cấp với bìa cứng in logo PHTV Cinema. Giấy chất lượng cao.',
                'hinh_anh' => null,
                'so_diem_can' => 250,
                'so_luong_con' => 80,
                'trang_thai' => 'active'
            ],
            [
                'ten_qua' => 'Bộ poster phim hot',
                'mo_ta' => 'Bộ 5 poster phim đang hot nhất tại PHTV Cinema. Kích thước A3.',
                'hinh_anh' => null,
                'so_diem_can' => 450,
                'so_luong_con' => 35,
                'trang_thai' => 'active'
            ],
            [
                'ten_qua' => 'Thẻ VIP PHTV',
                'mo_ta' => 'Thẻ VIP với nhiều ưu đãi đặc biệt. Giảm giá 15% cho tất cả dịch vụ.',
                'hinh_anh' => null,
                'so_diem_can' => 1000,
                'so_luong_con' => 20,
                'trang_thai' => 'active'
            ]
        ];

        foreach ($rewards as $reward) {
            Reward::create($reward);
        }
    }
} 