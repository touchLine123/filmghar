<?php

namespace App\Http\Controllers\User;

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

        $data = $this->initializePusher();
        if (!$data) {
            return response()->json(['error' => 'Pusher connection is required']);
        }

        $validator = Validator::make($request->all(), [
            'item_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Party code not found']);
        }

        $item = Item::active()->hasVideo()->where('id', $request->item_id)->first();
        if (!$item) {
            return response()->json(['error' => 'Item not found']);
        }

        $user = auth()->user();

        $running = WatchParty::active()->where('user_id', $user->id)->exists();
        if ($running) {
            return response()->json(['error' => 'You have created already a watch party room']);
        }

        if ($item->version == Status::PAID_VERSION) {
            $watchEligable = $user->exp > now() ? true : false;
            if (!$watchEligable) {
                return response()->json(['error' => 'You are not eligable for this item']);
            }
        }

        if ($item->version == Status::RENT_VERSION) {
            $hasSubscribedItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $item->id)->whereDate('expired_date', '>', now())->exists();
            if ($item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = (now() > $user->exp || $hasSubscribedItem) ? true : false;
            }
            if (!$watchEligable) {
                return response()->json(['error' => 'You are not eligable for this item']);
            }
        }

        $watchParty             = new WatchParty();
        $watchParty->user_id    = $user->id;
        $watchParty->item_id    = $request->item_id;
        $watchParty->episode_id = @$request->episode_id ?? 0;
        $watchParty->party_code = getTrx(6);
        $watchParty->save();

        return response()->json([
            'redirect_url' => route('user.watch.party.room', $watchParty->party_code),
        ]);
    }

    public function room($code, $guestId = 0) {

        $user      = auth()->user();
        $partyRoom = WatchParty::active();
        if (!$guestId) {
            $partyRoom->where('user_id', $user->id);
        }
        $partyRoom = $partyRoom->where('party_code', $code)->with(['item', 'episode', 'user'])->first();

        if (!$partyRoom) {
            $notify[] = ['error', 'Invalid watch party room request'];
            return to_route('user.watch.party.history')->withNotify($notify);
        }

        if ($partyRoom->user_id != $user->id) {
            $partyMember = PartyMember::accepted()->where('user_id', $user->id)->where('watch_party_id', $partyRoom->id)->first();
            if (!$partyMember) {
                $notify[] = ['error', 'Access denied permission to watch party'];
                return to_route('user.home')->withNotify($notify);
            }
        }

        if ($partyRoom->episode) {
            $video = $partyRoom->episode->video;
            $item  = $partyRoom->episode->item;
        } else {
            $video = $partyRoom->item->video;
            $item  = $partyRoom->item;
        }

        if ($item->version == Status::PAID_VERSION && gs('watch_party_users')) {
            $watchEligable = $user->exp > now() ? true : false;
            if (!$watchEligable) {
                return response()->json(['error' => 'You are not eligable for this item']);
            }
        }

        if ($item->version == Status::RENT_VERSION && gs('watch_party_users')) {
            $hasSubscribedItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $item->id)->whereDate('expired_date', '>', now())->exists();
            if ($item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = (now() > $user->exp || $hasSubscribedItem) ? true : false;
            }
            if (!$watchEligable) {
                return response()->json(['error' => 'You are not eligable for this item']);
            }
        }

        if (!$video) {
            $notify = ['error', 'There are no video in this item'];
            return back()->withNotify($notify);
        }

        $subtitles = $video->subtitles ?? [];
        $videos    = $this->videoList($video);

        $pageTitle     = 'Watch Party Room - ' . $partyRoom->party_code;
        $conversations = Conversation::where('watch_party_id', $partyRoom->id)->with('user')->latest()->limit(10)->get();
        $partyMembers  = PartyMember::accepted()->where('watch_party_id', $partyRoom->id)->with('user')->get();
        return view('Template::user.watch_party.room', compact('partyRoom', 'pageTitle', 'videos', 'subtitles', 'item', 'conversations', 'partyMembers'));
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

        $data = $this->initializePusher();
        if (!$data) {
            return response()->json(['error' => 'Pusher connection is required']);
        }

        $validator = Validator::make($request->all(), [
            'party_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }

        $party = WatchParty::active()->where('party_code', $request->party_code)->with(['item', 'episode'])->first();
        if (!$party) {
            return response()->json([
                'redirect' => false,
                'error', 'Invalid join request',
            ]);
        }

        if ($party->episode) {
            $item = $party->episode;
        } else {
            $item = $party->item;
        }

        $user = auth()->user();
        if ($item->version == Status::PAID_VERSION && gs('watch_party_users')) {
            $watchEligable = $user->exp > now() ? true : false;
            if (!$watchEligable) {
                return response()->json(['error' => 'You are not eligable for this item']);
            }
        }

        if ($item->version == Status::RENT_VERSION && gs('watch_party_users')) {
            $hasSubscribedItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $item->id)->whereDate('expired_date', '>', now())->exists();
            if ($item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = (now() > $user->exp || $hasSubscribedItem) ? true : false;
            }
            if (!$watchEligable) {
                return response()->json(['error' => 'You are not eligable for this item']);
            }
        }

        $alreadyJoined = PartyMember::accepted()->where('user_id', $user->id)->where('watch_party_id', $party->id)->first();

        if ($alreadyJoined) {
            return response()->json([
                'redirect_url' => route('user.watch.party.room', [$party->party_code, $alreadyJoined->user_id]),
            ]);
        }

        $member                 = new PartyMember();
        $member->watch_party_id = $party->id;
        $member->user_id        = $user->id;
        $member->save();

        event(new SendJoinWatchParty($party, $member->id, @$member->user->username));

        return response()->json([
            'redirect' => false,
            'host_id'  => $party->user_id,
            'success'  => 'Joining request send successfully',
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

        $data = $this->initializePusher();
        if (!$data) {
            return response()->json(['error' => 'Pusher connection is required']);
        }

        $partyMember = PartyMember::where('id', decrypt($id))->first();
        if (!$partyMember) {
            return response()->json(['error' => 'Member not found']);
        }
        $party = WatchParty::active()->where('user_id', auth()->id())->where('id', $partyMember->watch_party_id)->first();
        if (!$party) {
            return response()->json(['error' => 'Invalid Request']);
        }

        $member = $partyMember->user;
        if ($party->episode) {
            $item = $party->episode;
        } else {
            $item = $party->item;
        }

        if ($item->version == Status::PAID_VERSION && gs('watch_party_users')) {
            $watchEligable = $member->exp > now() ? true : false;
            if (!$watchEligable) {
                return response()->json(['error' => 'This member are not eligable for this item']);
            }
        }

        if ($item->version == Status::RENT_VERSION && gs('watch_party_users')) {
            $hasSubscribedItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $item->id)->whereDate('expired_date', '>', now())->exists();
            if ($item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = (now() > $member->exp || $hasSubscribedItem) ? true : false;
            }
            if (!$watchEligable) {
                return response()->json(['error' => 'This member are not eligable for this item']);
            }
        }

        $partyMember->status = Status::WATCH_PARTY_REQUEST_ACCEPTED;
        $partyMember->save();

        event(new AcceptJoinRequest($party, $partyMember));

        return response()->json([
            'success' => 'Joining request has been accepted',
        ]);
    }

    public function requestReject($id) {
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json(['error' => 'Pusher connection is required']);
        }
        $partyMember = PartyMember::where('id', decrypt($id))->first();
        if (!$partyMember) {
            return response()->json(['error' => 'Member not found']);
        }
        $party = WatchParty::active()->where('user_id', auth()->id())->where('id', $partyMember->watch_party_id)->first();
        if (!$party) {
            return response()->json(['error' => 'Invalid Request']);
        }

        $partyMember->status = Status::WATCH_PARTY_REQUEST_REJECTED;
        $partyMember->save();

        event(new RejectJoinRequest(route('user.home'), $partyMember->user_id, $party->partyCode));

        return response()->json([
            'success' => 'Joining request has been rejected',
        ]);
    }

    public function sendMessage(Request $request) {
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json(['error' => 'Pusher connection is required']);
        }
        $validator = Validator::make($request->all(), [
            'message'  => 'required|string',
            'party_id' => 'required|integer|exists:watch_parties,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }

        $party = WatchParty::active()->with('partyMember')->where('id', $request->party_id)->first();
        if (!$party) {
            return response()->json(['error', 'Party room not found']);
        }

        $joinedUsersId = $party->partyMember->pluck('user_id')->unique()->toArray();
        $hostId        = $party->user_id;
        $allMemberId   = array_merge([$hostId], $joinedUsersId);

        $user = auth()->user();
        if (!in_array($user->id, $allMemberId)) {
            return response()->json(['error', 'Access denied for conversation']);
        }

        $conversation                 = new Conversation();
        $conversation->user_id        = $user->id;
        $conversation->watch_party_id = $party->id;
        $conversation->message        = $request->message;
        $conversation->save();

        event(new ConversationMessage($conversation, $allMemberId, $party->party_code));
        return response()->json([
            'success' => true,
        ]);
    }

    public function playerSetting(Request $request) {
        $validator = Validator::make($request->all(), [
            'party_id' => 'required|integer|exists:watch_parties,id',
            'status'   => 'required|string|in:play,pause',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }

        $party = WatchParty::active()->with('partyMember')->where('id', $request->party_id)->first();
        if (!$party) {
            return response()->json(['error', 'Party room not found']);
        }

        $joinedUsersId = $party->partyMember->pluck('user_id')->unique()->toArray();
        $hostId        = $party->user_id;
        $allMemberId   = array_merge([$hostId], $joinedUsersId);

        $this->initializePusher();
        event(new PlayerSetting($allMemberId, $request->status, $party->party_code));

        return response()->json(['success' => true]);
    }

    public function status($id) {
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json(['error' => 'Pusher connection is required']);
        }
        $partyMember = PartyMember::accepted()->where('id', $id)->withWhereHas('watchParty', function ($query) {
            $query->where('user_id', auth()->id());
        })->first();

        if (!$partyMember) {
            return response()->json(['error' => 'Member not found']);
        }

        $partyMember->status = Status::WATCH_PARTY_REQUEST_REJECTED;
        $partyMember->save();

        $party = $partyMember->watchParty;

        event(new RejectJoinRequest(route('user.home'), $partyMember->user_id, $party->party_code));

        return response()->json([
            'success' => 'Member removed successfully',
            'id'      => $partyMember->id,
        ]);
    }

    public function cancel($id) {
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json(['error' => 'Pusher connection is required']);
        }
        $party = WatchParty::where('user_id', auth()->id())->where('id', $id)->first();
        if (!$party) {
            return response()->json(['error' => 'Party not found']);
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

        return response()->json(['success' => true]);
    }

    public function leave($id, $userId) {
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json(['error' => 'Pusher connection is required']);
        }
        $partyRoom = WatchParty::where('id', $id)->first();
        if (!$partyRoom) {
            return response()->json(['error' => 'Party not found']);
        }

        $member = PartyMember::accepted()->where('watch_party_id', $partyRoom->id)->where('user_id', $userId)->first();
        if (!$member) {
            return response()->json(['error' => 'Member not found']);
        }

        $member->status = Status::WATCH_PARTY_REQUEST_REJECTED;
        $member->save();

        $hostId       = $partyRoom->user_id;
        $partyMembers = $partyRoom->partyMember()->accepted()->with('user')->get()->groupBy('user_id')->map(function ($group) {
            return $group->first();
        });
        event(new LeaveWatchParty($member->user_id, $partyMembers, $hostId, $partyRoom));
        $html = view('Template::partials.member', compact('partyMembers', 'partyRoom', 'hostId'))->render();
        return response()->json([
            'success' => true,
            'hostId'  => $hostId,
            'html'    => $html,
            'user_id' => $member->user_id,
            'route'   => route('user.watch.party.history'),
        ]);
    }

    public function history() {
        $pageTitle = 'Watch Party History';
        $parties   = WatchParty::where('user_id', auth()->id());

        $total = (clone $parties)->count();
        if (request()->lastId) {
            $parties = $parties->where('id', '<', request()->lastId);
        }
        $parties = $parties->with('item', 'episode')
            ->with(['partyMember' => function ($query) {
                $query->select('user_id', 'watch_party_id')->distinct('user_id');
            }])->orderBy('id', 'desc')->take(20)->get();

        $lastId = @$parties->last()->id;
        if (request()->lastId) {
            if ($parties->count()) {
                $data = view('Template::user.watch_party.fetch_party', compact('parties'))->render();
                return response()->json([
                    'data'   => $data,
                    'lastId' => $lastId,
                ]);
            }
            return response()->json([
                'error' => 'Item not more yet',
            ]);
        }
        return view('Template::user.watch_party.history', compact('pageTitle', 'parties', 'lastId', 'total'));
    }

    public function disabled($id) {
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json(['error' => 'Pusher connection is required']);
        }
        $party = WatchParty::where('user_id', auth()->id())->where('id', $id)->first();
        if (!$party) {
            $notify[] = ['error', 'Party not found'];
            return back()->withNotify($notify);
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

        $notify[] = ['success', 'Party canceled successfully'];
        return back()->withNotify($notify);
    }

    public function reload(Request $request) {
        $data = $this->initializePusher();
        if (!$data) {
            return response()->json(['error' => 'Pusher connection is required']);
        }
        $validator = Validator::make($request->all(), [
            'party_id' => 'required|integer|exists:watch_parties,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
            ]);
        }
        $party = WatchParty::where('user_id', auth()->id())->where('id', $request->party_id)->first();
        if (!$party) {
            $notify[] = ['error', 'Party not found'];
            return back()->withNotify($notify);
        }
        $joinedUsersId = $party->partyMember->pluck('user_id')->unique()->toArray();
        event(new ReloadWatchParty($joinedUsersId));
        return response()->json(['success' => true]);
    }
}
