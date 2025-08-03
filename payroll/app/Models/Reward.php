<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;
    
    protected $table = 'rewards';
    
    protected $fillable = [
        'ten_qua',
        'mo_ta',
        'hinh_anh',
        'so_diem_can',
        'so_luong_con',
        'trang_thai'
    ];

    public function rewardExchanges()
    {
        return $this->hasMany(RewardExchange::class, 'reward_id');
    }
} 