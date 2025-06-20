<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcceptJoinRequest implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $redirectUrl;
    public $userId;
    public $hostId;
    public $html;
    public $remark;
    public $partyCode;
    public $partyRoom;

    public function __construct($party, $partyMember) {
        $this->remark      = 'accept-join-request';
        $this->redirectUrl = route('user.watch.party.room', [$party->party_code, $partyMember->user_id]);
        $this->userId      = $partyMember->user_id;
        $this->hostId      = $party->user_id;
        $this->partyCode   = $party->party_code;
        $this->partyRoom   = $party;
        $this->html        = view(activeTemplate() . 'partials.join_list', ['partyMember' => $partyMember, 'partyRoom' => $party])->render();
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('accept-join-request');
    }

    public function broadcastAs() {
        return 'accept-join-request';
    }
}
