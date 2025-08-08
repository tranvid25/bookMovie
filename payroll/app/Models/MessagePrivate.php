<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessagePrivate extends Model
{
    protected $fillable = ['content'];
    protected $table = 'message_private';
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chatroom(): BelongsTo
    {
        return $this->belongsTo(Chatroom::class);
    }
}
