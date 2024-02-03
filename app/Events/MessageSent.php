<?php

namespace App\Events;

use App\Models\User;
use App\Models\Message;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Pusher\Pusher;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $video_id;

    /**
     * Create a new event instance.
     */
    public function __construct($message,)
    {
        
        $this->message = $message;
        $this->video_id = $message['video_id'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {

        return [$this->video_id];
    }


    public function broadcastAs()
    {
        if ($this->message['type'] == 'creator') {
            return 'CreMessageSent';
        } if ($this->message['message_type'] == 'stickers') {
            return 'StickerSent';
        }else {
            return 'MessageSent';
        }
    }
}
