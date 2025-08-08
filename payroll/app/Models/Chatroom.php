<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chatroom extends Model
{
    protected $fillable = ['name', 'description'];

    public function messages(): HasMany
    {
        return $this->hasMany(MessagePrivate::class, 'room_id');
    }
}
