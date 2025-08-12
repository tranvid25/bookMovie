<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class VoiceCallEvent implements ShouldBroadcastNow
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('voice.'.$this->data['room_id']);
    }

    public function broadcastAs()
    {
        return 'VoiceCall';
    }
}
