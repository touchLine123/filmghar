<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CancelWatchParty implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $allMemberId;
    public $redirectUrl;
    public $remark;
    public $partyCode;

    public function __construct($redirectUrl, $allMemberId, $partyCode) {
        $this->remark      = 'cancel-party';
        $this->redirectUrl = $redirectUrl;
        $this->allMemberId = $allMemberId;
        $this->partyCode   = $partyCode;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('cancel-party');
    }

    public function broadcastAs() {
        return 'cancel-party';
    }
}
