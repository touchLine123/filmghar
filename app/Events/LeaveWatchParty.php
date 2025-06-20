<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveWatchParty implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $userId;
    public $partyMembers;
    public $hostId;
    public $html;
    public $partyRoom;
    public $partyCode;

    public function __construct($userId, $partyMembers, $hostId, $partyRoom) {
        $this->userId       = $userId;
        $this->partyMembers = $partyMembers;
        $this->hostId       = $hostId;
        $this->partyRoom    = $partyRoom;
        $this->partyCode    = $partyRoom->party_code;
        $this->html         = view(activeTemplate() . 'partials.member', compact('partyMembers', 'partyRoom', 'hostId'))->render();
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('leave-watch-party');
    }
    public function broadcastAs() {
        return 'leave-watch-party';
    }
}
