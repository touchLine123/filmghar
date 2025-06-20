<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendJoinWatchParty implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $partyCode;
    public $user;
    public $html;
    public $hostId;
    public $remark;
    public $memberId;

    public function __construct($party, $memberId, $member) {
        $this->remark    = 'send-join-request';
        $this->partyCode = $party->party_code;
        $this->user      = $member;
        $this->hostId    = $party->user_id;
        $this->memberId  = $memberId;
        $this->html      = view(activeTemplate() . 'partials.watch_join_request', ['username' => $member, 'memberId' => encrypt($memberId)])->render();
    }

    public function broadcastOn() {
        return new PrivateChannel('send-notification');
    }

    public function broadcastAs() {
        return 'send-notification';
    }
}
