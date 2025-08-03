<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory;
    protected $table = 'point_histories';

    protected $fillable = [
        'id_user',
        'loai',
        'so_diem',
        'mo_ta',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
