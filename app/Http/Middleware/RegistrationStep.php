<?php

namespace App\Http\Middleware;
use App\Models\UserDevice;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrationStep {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        $user = auth()->user();
        if (!$user->profile_complete) {
            if ($request->is('api/*')) {
                $notify[] = 'Please complete your profile to go next';
                return response()->json([
                    'remark'  => 'profile_incomplete',
                    'status'  => 'error',
                    'message' => ['error' => $notify],
                ]);
            } else {
                return to_route('user.data');
            }
        }

        if (!session('app_payment')) {
            $general = gs();
            if ($general->device_limit && $user->plan) {
                $userDevices     = UserDevice::where('user_id', $user->id)->distinct()->pluck('device_id')->toArray();
                $currentDeviceId = md5($_SERVER['HTTP_USER_AGENT']);
                if (count($userDevices) == @$user->plan->device_limit && !in_array($currentDeviceId, $userDevices)) {
                    if (!$request->is('api/deposit/history')) {
                        if ($request->is('api/*')) {
                            $notify[] = 'Device limit is over';
                            return response()->json([
                                'remark'  => 'device_limit_error',
                                'status'  => 'error',
                                'message' => ['error' => $notify],
                            ]);
                        } else {
                            session()->flush();
                            Auth::logout();
                            $notify[] = ['error', 'Device limit is over'];
                            return to_route('user.login')->withNotify($notify);
                        }
                    }
                }
                $existDevice = UserDevice::where('user_id', $user->id)->where('device_id', $currentDeviceId)->exists();
                if (!$existDevice) {
                    $device            = new UserDevice();
                    $device->user_id   = $user->id;
                    $device->device_id = $currentDeviceId;
                    $device->save();
                }
            }
        } else {
            session()->forget('app_payment');
        }
        return $next($request);
    }
}
