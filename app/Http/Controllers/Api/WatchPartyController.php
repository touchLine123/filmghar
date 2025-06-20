<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Events\AcceptJoinRequest;
use App\Events\CancelWatchParty;
use App\Events\ConversationMessage;
use App\Events\LeaveWatchParty;
use App\Events\PlayerSetting;
use App\Events\RejectJoinRequest;
use App\Events\ReloadWatchParty;
use App\Events\SendJoinWatchParty;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Item;
use App\Models\PartyMember;
use App\Models\Subscription;
use App\Models\WatchParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class WatchPartyController extends Controller {
    public function create(Request $request) {

        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }

        $data = $this->initializePusher();
        if (!$data) {
            return response()->json([
                'remark'  => 'pusher_connection',
                'status'  => 'error',
                'message' => ['error' => 'Pusher connection is required'],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'item_id'    => 'required|integer|exists:items,id',
            'episode_id' => 'nullable|integer|exists:episodes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();

        $running = WatchParty::active()->where('user_id', $user->id)->exists();
        if ($running) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => 'You have created already a watch party room'],
            ]);
        }

        $item = Item::active()->hasVideo()->where('id', $request->item_id)->first();
        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

        $watchEligable = true;

        if ($item->version == Status::PAID_VERSION) {
            $watchEligable = $user->exp > now() ? true : false;
        }

        if ($item->version == Status::RENT_VERSION) {
            $hasSubscribedItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $item->id)->whereDate('expired_date', '>', now())->exists();
            if ($item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = (now() > $user->exp || $hasSubscribedItem) ? true : false;
            }
        }

        if (!$watchEligable) {
            return response()->json([
                'remark'  => 'not_eligable',
                'status'  => 'error',
                'message' => ['error' => 'You are not eligable for this item'],
            ]);
        }

        $watchParty             = new WatchParty();
        $watchParty->user_id    = $user->id;
        $watchParty->item_id    = $request->item_id;
        $watchParty->episode_id = @$request->episode_id ?? 0;
        $watchParty->party_code = getTrx(6);
        $watchParty->save();

        $notify[] = 'Room created successfully';
        return response()->json([
            'remark'  => 'room_create',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'watchParty' => $watchParty,
                'item'       => $item,
            ],
        ]);
    }

    public function room($code, $guestId = 0) {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }

        $user      = auth()->user();
        $partyRoom = WatchParty::active();

        if (!$guestId) {
            $partyRoom->where('user_id', $user->id);
        }

        $partyRoom = $partyRoom->where('party_code', $code)->with(['item', 'episode', 'user'])->first();

        if (!$partyRoom) {
            return response()->json([
                'remark'  => 'invalid_room',
                'status'  => 'error',
                'message' => ['error' => 'Invalid watch party room request'],
            ]);
        }

        if ($partyRoom->user_id != $user->id) {
            $partyMember = PartyMember::accepted()->where('user_id', $user->id)->where('watch_party_id', $partyRoom->id)->first();
            if (!$partyMember) {
                return response()->json([
                    'remark'  => 'permission_denied',
                    'status'  => 'error',
                    'message' => ['error' => 'Access denied permission to watch party'],
                ]);
            }
        }

        if ($partyRoom->episode) {
            $video = $partyRoom->episode->video;
            $item  = $partyRoom->episode->item;
        } else {
            $video = $partyRoom->item->video;
            $item  = $partyRoom->item;
        }

        $eligable = $this->checkEligable($user, $item);

        if (!$eligable[0]) {
            return response()->json([
                'remark'  => 'not_eligable',
                'status'  => 'error',
                'message' => ['error' => $eligable[1]],
            ]);
        }

        $videos    = $this->videoList($video);
        $subtitles = $video->subtitles;

        $conversations = Conversation::where('watch_party_id', $partyRoom->id)->with('user')->latest()->limit(10)->get();
        $partyMembers  = PartyMember::accepted()->where('watch_party_id', $partyRoom->id)->with('user')->get();

        $notify[] = 'Room data';
        return response()->json([
            'remark'  => 'party_room',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'videos'        => $videos,
                'subtitles'     => $subtitles,
                'conversations' => $conversations,
                'partyMembers'  => $partyMembers,
                'item'          => $item,
                'partyRoom'     => $partyRoom,
            ],
        ]);
    }

    private function checkEligable($user, $item) {

        $status  = true;
        $message = null;

        if ($item->version == Status::PAID_VERSION && gs('watch_party_users')) {
            $status  = $user->exp > now() ? true : false;
            $message = 'This member are not eligable for this paid item';
        }

        if ($item->version == Status::RENT_VERSION && gs('watch_party_users')) {
            $hasSubscribedItem = Subscription::active()->where('user_id', $user->id)->where('item_id', $item->id)->whereDate('expired_date', '>', now())->exists();
            if ($item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = (now() > $user->exp || $hasSubscribedItem) ? true : false;
            }
            if (!$watchEligable) {
                $status  = false;
                $message = 'This member are not eligable for this rent item';
            }
        }
        return [$status, $message];
    }

    private function videoList($video) {
        $videoFile = [];
        if ($video->three_sixty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'three_sixty'),
                'size'    => 360,
            ];
        }
        if ($video->four_eighty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'four_eighty'),
                'size'    => 480,
            ];
        }
        if ($video->seven_twenty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'seven_twenty'),
                'size'    => 720,
            ];
        }
        if ($video->thousand_eighty_video) {
            $videoFile[] = [
                'content' => getVideoFile($video, 'thousand_eighty'),
                'size'    => 1080,
            ];
        }
        return json_decode(json_encode($videoFile, true));
    }

    public function joinRequest(Request $request) {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }

        $data = $this->initializePusher();
        if (!$data) {
            return response()->json([
                'remark'  => 'pusher_connection',
                'status'  => 'error',
                'message' => ['error' => 'Pusher connection is required'],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'party_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $party = WatchParty::active()->where('party_code', $request->party_code)->with(['item', 'episode'])->first();
        if (!$party) {
            return response()->json([
                'remark'  => 'invalid_party',
                'status'  => 'error',
                'message' => ['error' => 'Invalid join request'],
            ]);
        }

        if ($party->episode) {
            $item = $party->episode;
        } else {
            $item = $party->item;
        }

        $user = auth()->user();

        $eligable = $this->checkEligable($user, $item);

        if (!$eligable[0]) {
            return response()->json([
                'remark'  => 'not_eligable',
                'status'  => 'error',
                'message' => ['error' => $eligable[1]],
            ]);
        }

        $alreadyJoined = PartyMember::accepted()->where('user_id', $user->id)->where('watch_party_id', $party->id)->first();
        if ($alreadyJoined) {
            return response()->json([
                'remark'  => 'already_joined',
                'status'  => 'error',
                'message' => ['error' => 'Already joined this party'],
                'data'    => [
                    'party' => $party,
                    'item'  => $item,
                    'user'  => $user->id,
                ],
            ]);
        }

        $member                 = new PartyMember();
        $member->watch_party_id = $party->id;
        $member->user_id        = $user->id;
        $member->save();

        event(new SendJoinWatchParty($party, $member->id, @$member->user->username));

        $notify[] = 'Sent join watch party';
        return response()->json([
            'remark'  => 'join_request',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'party'  => $party,
                'member' => $member,
                'item'   => $item,
            ],
        ]);
    }

    private function initializePusher() {
        $general = gs();
        Config::set('broadcasting.connections.pusher.driver', 'pusher');
        Config::set('broadcasting.connections.pusher.key', $general->pusher_config->app_key);
        Config::set('broadcasting.connections.pusher.secret', $general->pusher_config->app_secret_key);
        Config::set('broadcasting.connections.pusher.app_id', $general->pusher_config->app_id);
        Config::set('broadcasting.connections.pusher.option.cluster', $general->pusher_config->cluster);

        $pusherConfig = config('broadcasting.connections.pusher');
        if ($pusherConfig['driver'] === 'pusher' &&
            $pusherConfig['key'] === $general->pusher_config->app_key &&
            $pusherConfig['secret'] === $general->pusher_config->app_secret_key &&
            $pusherConfig['app_id'] === $general->pusher_config->app_id &&
            $pusherConfig['option']['cluster'] === $general->pusher_config->cluster) {
            return true;
        } else {
            return false;
        }
    }

    public function requestAccept($id) {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }

        $data = $this->initializePusher();
        if (!$data) {
            return response()->json([
                'remark'  => 'pusher_connection',
                'status'  => 'error',
                'message' => ['error' => 'Pusher connection is required'],
            ]);
        }

        $partyMember = PartyMember::where('id', $id)->first();
        if (!$partyMember) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Member not found'],
            ]);
        }
        $party = WatchParty::active()->where('user_id', auth()->id())->where('id', $partyMember->watch_party_id)->first();
        if (!$party) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Party room not found'],
            ]);
        }

        $member = $partyMember->user;
        if ($party->episode) {
            $item = $party->episode;
        } else {
            $item = $party->item;
        }

        $eligable = $this->checkEligable($member, $item);
        if (!$eligable[0]) {
            return response()->json([
                'remark'  => 'not_eligable',
                'status'  => 'error',
                'message' => ['error' => $eligable[1]],
            ]);
        }

        $partyMember->status = Status::WATCH_PARTY_REQUEST_ACCEPTED;
        $partyMember->save();

        event(new AcceptJoinRequest($party, $partyMember));

        $notify[] = 'Joining request has been accepted';
        return response()->json([
            'remark'  => 'request_accepted',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'partyMember' => $partyMember,
                'party'       => $party,
            ],
        ]);
    }

    public function requestReject($id) {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json([
                'remark'  => 'pusher_connection',
                'status'  => 'error',
                'message' => ['error' => 'Pusher connection is required'],
            ]);
        }
        $partyMember = PartyMember::where('id', $id)->first();
        if (!$partyMember) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Member not found'],
            ]);
        }
        $party = WatchParty::active()->where('user_id', auth()->id())->where('id', $partyMember->watch_party_id)->first();
        if (!$party) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Party room not found'],
            ]);
        }

        $partyMember->status = Status::WATCH_PARTY_REQUEST_REJECTED;
        $partyMember->save();

        event(new RejectJoinRequest(route('user.home'), $partyMember->user_id, $party->party_code));

        $notify[] = 'Joining request has been rejected';
        return response()->json([
            'remark'  => 'request_rejected',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'partyMember' => $partyMember,
            ],
        ]);
    }

    public function sendMessage(Request $request) {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json([
                'remark'  => 'pusher_connection',
                'status'  => 'error',
                'message' => ['error' => 'Pusher connection is required'],
            ]);
        }
        $validator = Validator::make($request->all(), [
            'message'  => 'required|string',
            'party_id' => 'required|integer|exists:watch_parties,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $party = WatchParty::active()->with('partyMember')->where('id', $request->party_id)->first();
        if (!$party) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Party room not found'],
            ]);
        }

        $joinedUsersId = $party->partyMember->pluck('user_id')->unique()->toArray();
        $hostId        = $party->user_id;
        $allMemberId   = array_merge([$hostId], $joinedUsersId);

        $user = auth()->user();
        if (!in_array($user->id, $allMemberId)) {
            return response()->json([
                'remark'  => 'conversation',
                'status'  => 'error',
                'message' => ['error' => 'Access denied for conversation'],
            ]);
        }

        $conversation                 = new Conversation();
        $conversation->user_id        = $user->id;
        $conversation->watch_party_id = $party->id;
        $conversation->message        = $request->message;
        $conversation->save();

        event(new ConversationMessage($conversation, $allMemberId, $party->party_code));

        $notify[] = 'Message sent successfully';
        return response()->json([
            'remark'  => 'sent_message',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'conversation' => $conversation,
                'allMemberId'  => $allMemberId,
            ],
        ]);
    }

    public function status($id) {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json([
                'remark'  => 'pusher_connection',
                'status'  => 'error',
                'message' => ['error' => 'Pusher connection is required'],
            ]);
        }
        $partyMember = PartyMember::accepted()->where('id', $id)->withWhereHas('watchParty', function ($query) {
            $query->where('user_id', auth()->id());
        })->first();

        if (!$partyMember) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Member not found'],
            ]);
        }

        $partyMember->status = Status::WATCH_PARTY_REQUEST_REJECTED;
        $partyMember->save();

        $party = $partyMember->watchParty;

        event(new RejectJoinRequest(route('user.home'), $partyMember->user_id, $party->party_code));
        $notify[] = 'Member removed successfully';
        return response()->json([
            'remark'  => 'sent_message',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'partyMember' => $partyMember,
            ],
        ]);
    }

    public function cancel($id) {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json([
                'remark'  => 'pusher_connection',
                'status'  => 'error',
                'message' => ['error' => 'Pusher connection is required'],
            ]);
        }
        $party = WatchParty::where('user_id', auth()->id())->where('id', $id)->first();
        if (!$party) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Party room not found'],
            ]);
        }
        $party->status = Status::DISABLE;
        $party->save();

        $members = $party->partyMember;
        foreach ($members as $member) {
            $member->status = Status::WATCH_PARTY_REQUEST_REJECTED;
            $member->save();
        }

        $joinedUsersId = $party->partyMember->pluck('user_id')->unique()->toArray();
        $hostId        = $party->user_id;
        $allMemberId   = array_merge([$hostId], $joinedUsersId);

        event(new CancelWatchParty(route('user.home'), $allMemberId, $party->party_code));

        $notify[] = 'Party canceled successfully';
        return response()->json([
            'remark'  => 'sent_message',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'party'       => $party,
                'allMemberId' => $allMemberId,
            ],
        ]);
    }

    public function leave($id, $userId) {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json([
                'remark'  => 'pusher_connection',
                'status'  => 'error',
                'message' => ['error' => 'Pusher connection is required'],
            ]);
        }

        $partyRoom = WatchParty::active()->where('id', $id)->first();
        if (!$partyRoom) {
            return response()->json([
                'remark'  => 'party_not_found',
                'status'  => 'error',
                'message' => ['error' => 'Party room not found'],
            ]);
        }

        $member = PartyMember::accepted()->where('watch_party_id', $partyRoom->id)->where('user_id', $userId)->first();
        if (!$member) {
            return response()->json([
                'remark'  => 'member_not_found',
                'status'  => 'error',
                'message' => ['error' => 'Member not found'],
            ]);
        }

        $member->status = Status::WATCH_PARTY_REQUEST_REJECTED;
        $member->save();

        $hostId       = $partyRoom->user_id;
        $partyMembers = $partyRoom->partyMember()->accepted()->with('user')->get()->groupBy('user_id')->map(function ($group) {
            return $group->first();
        });

        event(new LeaveWatchParty($member->user_id, $partyMembers, $hostId, $partyRoom));

        $notify[] = 'Party leave successfully';
        return response()->json([
            'remark'  => 'sent_message',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'hostId'       => $hostId,
                'partyMembers' => $partyMembers,
                'user_id'      => $member->user_id,
                'partyRoom'    => $partyRoom,
            ],
        ]);
    }

    public function history() {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }
        $parties = WatchParty::where('user_id', auth()->id())
            ->with('item:id,title', 'episode:id,title')
            ->with(['partyMember' => function ($query) {
                $query->select('user_id', 'watch_party_id')->distinct('user_id');
            }])->apiQuery();

        $notify[] = 'Party canceled successfully';
        return response()->json([
            'remark'  => 'party_history',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'parties' => $parties,
            ],
        ]);
    }

    public function disabled($id) {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json([
                'remark'  => 'pusher_connection',
                'status'  => 'error',
                'message' => ['error' => 'Pusher connection is required'],
            ]);
        }
        $party = WatchParty::where('user_id', auth()->id())->where('id', $id)->first();
        if (!$party) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Party room not found'],
            ]);
        }
        $party->status = Status::DISABLE;
        $party->save();

        $members = $party->partyMember;
        foreach ($members as $member) {
            $member->status = Status::WATCH_PARTY_REQUEST_REJECTED;
            $member->save();
        }

        $joinedUsersId = $party->partyMember->pluck('user_id')->unique()->toArray();
        $hostId        = $party->user_id;
        $allMemberId   = array_merge([$hostId], $joinedUsersId);

        event(new CancelWatchParty(route('user.home'), $allMemberId, $party->party_code));

        $notify[] = 'Party canceled successfully';
        return response()->json([
            'remark'  => 'sent_message',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'party'       => $party,
                'allMemberId' => $allMemberId,
            ],
        ]);
    }

    public function reload(Request $request) {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json([
                'remark'  => 'pusher_connection',
                'status'  => 'error',
                'message' => ['error' => 'Pusher connection is required'],
            ]);
        }
        $validator = Validator::make($request->all(), [
            'party_id' => 'required|integer|exists:watch_parties,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }
        $party = WatchParty::where('user_id', auth()->id())->where('id', $request->party_id)->first();
        if (!$party) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Party room not found'],
            ]);
        }
        $joinedUsersId = $party->partyMember->pluck('user_id')->unique()->toArray();
        event(new ReloadWatchParty($joinedUsersId));

        $notify[] = 'Party Rreloaded successfully';
        return response()->json([
            'remark'  => 'sent_message',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'party'         => $party,
                'joinedUsersId' => $joinedUsersId,
            ],
        ]);
    }

    public function playerSetting(Request $request) {
        $general = gs();
        if (!$general->watch_party) {
            return response()->json([
                'remark'  => 'disable_watch_party',
                'status'  => 'error',
                'message' => ['error' => 'The watch party has been disabled'],
            ]);
        }
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json([
                'remark'  => 'pusher_connection',
                'status'  => 'error',
                'message' => ['error' => 'Pusher connection is required'],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'party_id' => 'required|integer|exists:watch_parties,id',
            'status'   => 'required|string|in:play,pause',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $party = WatchParty::active()->with('partyMember')->where('id', $request->party_id)->first();
        if (!$party) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Party room not found'],
            ]);
        }

        $joinedUsersId = $party->partyMember->pluck('user_id')->unique()->toArray();
        $hostId        = $party->user_id;
        $allMemberId   = array_merge([$hostId], $joinedUsersId);

        event(new PlayerSetting($allMemberId, $request->status, $party->party_code));

        $notify[] = 'Player setting';
        return response()->json([
            'remark'  => 'player_setting',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'party'       => $party,
                'hostId'      => $hostId,
                'allMemberId' => $allMemberId,
            ],
        ]);
    }
}
