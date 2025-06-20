<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerSetting implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $allMemberId;
    public $status;
    public $remark;
    public $partyCode;

    public function __construct($allMemberId, $status, $partyCode) {
        $this->allMemberId = $allMemberId;
        $this->status      = $status;
        $this->partyCode   = $partyCode;
        $this->remark      = 'player-setting';

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastOn() {
        return new PrivateChannel('player-setting');
    }

    public function broadcastAs() {
        return 'player-setting';
    }
}
