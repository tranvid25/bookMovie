<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardExchange extends Model
{
    use HasFactory;
    
    protected $table = 'reward_exchanges';
    
    protected $fillable = [
        'user_id',
        'reward_id',
        'so_diem_da_dung',
        'trang_thai',
        'ngay_nhan'
    ];

    protected $casts = [
        'ngay_nhan' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class, 'reward_id');
    }
} 