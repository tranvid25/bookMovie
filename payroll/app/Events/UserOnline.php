<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserOnline implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $message;
    public $hinhAnhUrl;

    public function __construct(User $user, $message, $hinhAnhUrl = null)
    {
        $this->user = $user;
        $this->message = $message;
        $this->hinhAnhUrl = $hinhAnhUrl;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('chat');
    }
    public function broadcastAs(){
        return 'UserOnline';
    }
}
