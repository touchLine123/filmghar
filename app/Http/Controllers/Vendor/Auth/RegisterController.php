<?php
namespace App\Http\Controllers\Vendor\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\DeviceToken;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\VendorUnderReviewMail;

class RegisterController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        parent::__construct();
    }

    public function showRegistrationForm()
    {
        $pageTitle = "Register";
        Intended::identifyRoute();
        return view('vendor.auth.register', compact('pageTitle'));
    }

    protected function validator(array $data)
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $agree = 'nullable';
        if (gs('agree')) {
            $agree = 'required';
        }

        $validate = Validator::make($data, [
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|string|email|unique:admins',
            'password'  => ['required', 'confirmed', $passwordValidation],
            'captcha'   => 'sometimes|required',
            'agree'     => $agree,
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required'  => 'The last name field is required',
        ]);

        return $validate;
    }

    public function register(Request $request)
    {
        if (!gs('registration')) {
            $notify[] = ['error', 'Registration is currently disabled'];
            return back()->withNotify($notify);
        }

        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        event(new Registered($admin = $this->create($request->all())));

       // $this->guard()->login($admin);
       $notify[] = ['success', 'Registration successful. Your profile is under review. You will be able to log in once approved.'];
        Mail::to($admin->email)->send(new VendorUnderReviewMail($admin));
        return redirect()->route('vendor.register')->withNotify($notify);
    }

    protected function create(array $data)
    {
        // Handle reference logic
        $referBy = session()->get('reference');
        $referAdmin = null;

        if ($referBy) {
            $referAdmin = Admin::where('username', $referBy)->first();
        }

        // Create Admin User
        $admin = new Admin();
        $admin->email = strtolower($data['email']);
        $admin->username = strtolower($data['firstname']) ;
        $admin->name = $data['firstname'] . ' ' . $data['lastname'];  // Combine first and last name
        $admin->password = Hash::make($data['password']);
        $admin->user_role = 2; // Assuming user_role is set for this context
        $admin->save();
        

        // Create Admin Notification for Admin Panel
        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $admin->id;
        $adminNotification->title = 'New vendor registered';
        $adminNotification->click_url = urlPath('admin.vendor.detail', $admin->id);
        $adminNotification->save();

        // Handle User Login info
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        if ($exist) {
            $userLogin->longitude = $exist->longitude;
            $userLogin->latitude = $exist->latitude;
            $userLogin->city = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country = $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude = @implode(',', $info['long']);
            $userLogin->latitude = @implode(',', $info['lat']);
            $userLogin->city = @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country = @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $admin->id;
        $userLogin->user_ip = $ip;
        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();

        return $admin;
    }

    public function checkUser(Request $request)
    {
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = Admin::where('email', $request->email)->exists();
            $exist['type'] = 'email';
            $exist['field'] = 'Email';
        }
        if ($request->mobile) {
            $exist['data'] = Admin::where('mobile', $request->mobile)->where('dial_code', $request->mobile_code)->exists();
            $exist['type'] = 'mobile';
            $exist['field'] = 'Mobile';
        }
        if ($request->username) {
            $exist['data'] = Admin::where('username', $request->username)->exists();
            $exist['type'] = 'username';
            $exist['field'] = 'Username';
        }
        return response($exist);
    }

    public function registered()
    {
        if (session()->has('device_token')) {
            $deviceToken = session()->get('device_token');
            $token = DeviceToken::where('token', $deviceToken)->first();
            if ($token) {
                $token->user_id = auth()->id();
                $token->save();
            }
            session()->forget('device_token');
        }
        return to_route('user.home');
    }
}
