<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PusherController extends Controller {

    public function authentication($socketId, $channelName) {

        $pusherSecret = config('app.PUSHER_APP_SECRET');
        $str          = $socketId . ":" . $channelName;
        $hash         = hash_hmac('sha256', $str, $pusherSecret);

        return response()->json([
            'success' => true,
            'message' => "Pusher authentication successfully",
            'auth'    => config('app.PUSHER_APP_KEY') . ":" . $hash,
        ]);
    }

    public function authenticationApp(Request $request) {

        $general      = gs();
        $pusherSecret = $general->pusher_config->app_secret_key;
        $str          = $request->socket_id . ":" . $request->channel_name;
        $hash         = hash_hmac('sha256', $str, $pusherSecret);

        return response()->json([
            'success' => true,
            'message' => "Pusher authentication successfully",
            'auth'    => $general->pusher_config->app_key . ":" . $hash,
        ]);
    }
}
