<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;

class WishlistController extends Controller {
    public function wishlist() {
        $pageTitle = 'My Wishlists';
        $wishlists = Wishlist::where('user_id', auth()->id());
        $total     = (clone $wishlists)->count();
        if (request()->lastId) {
            $wishlists = $wishlists->where('id', '<', request()->lastId);
        }
        $wishlists = $wishlists->with('item', 'episode.item')->orderBy('id', 'desc')->take(20)->get();
        $lastId    = @$wishlists->last()->id;

        if (request()->lastId) {
            if ($wishlists->count()) {
                $data = view('Template::user.wishlist.fetch_wishlist', compact('wishlists'))->render();
                return response()->json([
                    'data'   => $data,
                    'lastId' => $lastId,
                ]);
            }
            return response()->json([
                'error' => 'Item not more yet',
            ]);
        }
        return view('Template::user.wishlist.index', compact('pageTitle', 'wishlists', 'total', 'lastId'));
    }

    public function wishlistRemove($id) {
        Wishlist::where('user_id', auth()->id())->where('id', $id)->delete();
        $notify[] = ['success', 'Item removed from your wishlists'];
        return back()->withNotify($notify);
    }
}
