<?php
namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laramin\Utility\Onumoti;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = 'vendor/dashboard';

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $pageTitle = "Vendor Login";
        return view('vendor.auth.login', compact('pageTitle'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard('vendor');
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);
        $request->session()->regenerateToken();
    
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
    
        // Get credentials
        $credentials = $this->credentials($request);
    
        // Attempt to find user with those credentials
        $vendor = $this->guard()->getProvider()->retrieveByCredentials($credentials);
    
        // If user exists and password is correct
        if ($vendor && $this->guard()->getProvider()->validateCredentials($vendor, $credentials)) {
            if ($vendor->status == 0) {
                return back()->withErrors([
                    $this->username() => 'Your profile is under review. You will be able to log in once approved.',
                ]);
            }
    
            $this->guard()->login($vendor, $request->filled('remember'));
            return $this->sendLoginResponse($request);
        }
    
        $this->incrementLoginAttempts($request);
    
        return $this->sendFailedLoginResponse($request);
    }
    


    public function logout(Request $request)
    {
        $this->guard('vendor')->logout();
        $request->session()->invalidate();
        return $this->loggedOut($request) ?: redirect($this->redirectTo);
    }
}
