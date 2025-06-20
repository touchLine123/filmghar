<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationMessage implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $html;
    public $membersId;
    public $remark;
    public $conversation;
    public $partyCode;

    public function __construct($conversation, $allMemberId, $partyCode) {
        $this->remark       = 'conversation-message';
        $this->conversation = $conversation;
        $this->partyCode    = $partyCode;
        $this->html         = view(activeTemplate() . 'partials.single_message', ['conversation' => $conversation])->render();
        $this->membersId    = $allMemberId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('conversation-message');
    }
    public function broadcastAs() {
        return 'conversation-message';
    }
}
