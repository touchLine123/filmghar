<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RejectJoinRequest implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $redirectUrl;
    public $userId;
    public $remark;
    public $partyCode;
    public function __construct($redirectUrl, $userId, $partyCode) {
        $this->remark      = 'reject-join-request';
        $this->redirectUrl = $redirectUrl;
        $this->userId      = $userId;
        $this->partyCode   = $partyCode;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('reject-join-request');
    }
    public function broadcastAs() {
        return 'reject-join-request';
    }
}
