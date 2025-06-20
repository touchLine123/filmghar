<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PartyMember;
use App\Models\User;
use App\Models\WatchParty;

class WatchPartyController extends Controller {
    public function all() {
        $pageTitle    = 'Watch Party List';
        $watchParties = $this->getPartyList('');
        return view('vendor.watch_party.index', compact('pageTitle', 'watchParties'));
    }
    public function running() {
        $pageTitle    = 'Running Party List';
        $watchParties = $this->getPartyList('active');
        return view('vendor.watch_party.index', compact('pageTitle', 'watchParties'));
    }
    public function canceled() {
        $pageTitle    = 'Canceled Party List';
        $watchParties = $this->getPartyList('inactive');
        return view('vendor.watch_party.index', compact('pageTitle', 'watchParties'));
    }

    private function getPartyList($scope = null) {

        if ($scope) {
            $watchParties = WatchParty::$scope();
        } else {
            $watchParties = WatchParty::query();
        }
        return $watchParties->searchable(['user:username,firstname,lastname', 'party_code'])->with('user', 'item:id,title', 'episode:id,title')->with(['partyMember' => function ($query) {
            $query->select('user_id', 'watch_party_id')->distinct()->pluck('user_id');
        }])->latest()->paginate(getPaginate());

    }

    public function joined($id) {
        $joinedMember = PartyMember::where('watch_party_id', $id)->select('user_id')->distinct()->pluck('user_id')->toArray();
        $users        = User::whereIn('id', $joinedMember)->paginate(getPaginate());
        $pageTitle    = 'Joined Member Watch Party';
        return view('vendor.users.list', compact('users', 'pageTitle'));
    }

}
