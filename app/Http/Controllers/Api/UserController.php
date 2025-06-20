<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\Advertise;
use App\Models\Deposit;
use App\Models\DeviceToken;
use App\Models\Episode;
use App\Models\Form;
use App\Models\History;
use App\Models\Item;
use App\Models\LiveTelevision;
use App\Models\NotificationLog;
use App\Models\Plan;
use App\Models\Membership;
use App\Models\Slider;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VideoReport;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller {
    public function dashboard() {
        $data['sliders']        = Slider::with('item', 'item.category', 'item.sub_category')->latest()->limit(4)->get();
        $data['televisions']    = LiveTelevision::where('status', 1)->latest()->limit(4)->get();
        $items                  = Item::active()->hasVideo();
        $data['featured']       = (clone $items)->where('featured', Status::ENABLE)->latest()->limit(10)->get();
        $data['recently_added'] = (clone $items)->orderBy('id', 'desc')->where('item_type', Status::SINGLE_ITEM)->latest()->limit(10)->get();
        $data['latest_series']  = (clone $items)->orderBy('id', 'desc')->where('item_type', Status::EPISODE_ITEM)->latest()->limit(10)->get();
        $data['single']         = (clone $items)->orderBy('id', 'desc')->where('single', 1)->with('category')->latest()->limit(10)->get();
        $data['trailer']        = (clone $items)->where('item_type', Status::SINGLE_ITEM)->where('is_trailer', Status::TRAILER)->latest()->limit(10)->get();
        $data['rent']           = (clone $items)->where('item_type', Status::SINGLE_ITEM)->where('version', Status::RENT_VERSION)->latest()->limit(10)->get();
        $data['free_zone']      = (clone $items)->free()->latest()->limit(10)->get();
        $data['advertise']      = Advertise::where('device', 2)->where('ads_show', 1)->where('ads_type', 'banner')->inRandomOrder()->first();

        $imagePath['portrait']   = getFilePath('item_portrait');
        $imagePath['landscape']  = getFilePath('item_landscape');
        $imagePath['television'] = getFilePath('television');
        $imagePath['ads']        = getFilePath('ads');

        return response()->json([
            'remark'  => 'dashboard',
            'status'  => 'success',
            'message' => ['success' => 'Dashboard Data'],
            'data'    => [
                'data' => $data,
                'path' => $imagePath,
            ],
        ]);
    }

    public function userDataSubmit(Request $request) {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            $notify[] = 'You\'ve already completed your profile';
            return response()->json([
                'remark'  => 'already_completed',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        $countryData  = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $validator = Validator::make($request->all(), [
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:users|min:6',
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = 'No special character, space or capital letters in username';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        $user->username     = $request->username;

        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->country_name = @$request->country;
        $user->dial_code    = $request->mobile_code;

        $user->profile_complete = Status::YES;
        $user->save();

        $notify[] = 'Profile completed successfully';
        return response()->json([
            'remark'  => 'profile_completed',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'user' => $user,
            ],
        ]);
    }

    public function kycForm() {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = 'Your KYC is under review';
            return response()->json([
                'remark'  => 'under_review',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = 'You are already KYC verified';
            return response()->json([
                'remark'  => 'already_verified',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $form     = Form::where('act', 'kyc')->first();
        $notify[] = 'KYC field is below';
        return response()->json([
            'remark'  => 'kyc_form',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'form' => $form->form_data,
            ],
        ]);
    }

    public function kycSubmit(Request $request) {
        $form = Form::where('act', 'kyc')->first();
        if (!$form) {
            $notify[] = 'Invalid KYC request';
            return response()->json([
                'remark'  => 'invalid_request',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);

        $validator = Validator::make($request->all(), $validationRule);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }
        $user = auth()->user();
        foreach (@$user->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $userData = $formProcessor->processFormData($request, $formData);

        $user->kyc_data             = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv                   = Status::KYC_PENDING;
        $user->save();

        $notify[] = 'KYC data submitted successfully';
        return response()->json([
            'remark'  => 'kyc_submitted',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);

    }

    public function depositHistory(Request $request) {
        $deposits = auth()->user()->deposits()->selectRaw("*, DATE_FORMAT(created_at, '%Y-%m-%d %h:%m') as date");
        if ($request->search) {
            $deposits = $deposits->where('trx', $request->search);
        }
        $deposits = $deposits->with(['gateway', 'subscription' => function ($query) {
            $query->with('plan', 'item');
        }])->orderBy('id', 'desc')->paginate(getPaginate());

        $notify[] = 'Deposit data';
        return response()->json([
            'remark'  => 'deposits',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                "wallet_amount" => auth()->user()->wallet_amount,
                'deposits' => $deposits,
            ],
        ]);
    }

    public function transactions(Request $request) {
        $remarks      = Transaction::distinct('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id());

        if ($request->search) {
            $transactions = $transactions->where('trx', $request->search);
        }

        if ($request->type) {
            $type         = $request->type == 'plus' ? '+' : '-';
            $transactions = $transactions->where('trx_type', $type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark', $request->remark);
        }

        $transactions = $transactions->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[]     = 'Transactions data';
        return response()->json([
            'remark'  => 'transactions',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'transactions' => $transactions,
                'remarks'      => $remarks,
            ],
        ]);
    }

    public function submitProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname'  => 'required',
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required'  => 'The last name field is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;

        if ($request->hasFile('image')) {
            try {
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), @$user->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->address = $request->address;
        $user->city    = $request->city;
        $user->state   = $request->state;
        $user->zip     = $request->zip;

        $user->save();

        $notify[] = 'Profile updated successfully';
        return response()->json([
            'remark'  => 'profile_updated',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function submitPassword(Request $request) {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', $passwordValidation],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password       = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = 'Password changed successfully';
            return response()->json([
                'remark'  => 'password_changed',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        } else {
            $notify[] = 'The password doesn\'t match!';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
    }

    public function plans($type = null) {
        $plans = Plan::active();
        if ($type == 'app') {
            $plans->whereNotNull('app_code');
        }
        $plans   = $plans->get();
        $appCode = $plans->pluck('app_code')->toArray();

        $notify[]  = 'Plan';
        $imagePath = getFilePath('plan');

        return response()->json([
            'remark'  => 'plan',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'plans'      => $plans,
                'image_path' => $imagePath,
                'appCode'    => $appCode,
            ],
        ]);
    }


   
    public function membership($type = null) {
        $plans = Membership::active();
        if ($type == 'app') {
            $plans->whereNotNull('app_code');
        }
        $plans   = $plans->get();
        $appCode = $plans->pluck('app_code')->toArray();

        $notify[]  = 'Plan';
        $imagePath = getFilePath('plan');

        return response()->json([
            'remark'  => 'membership',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'plans'      => $plans,
                'image_path' => $imagePath,
                'appCode'    => $appCode,
            ],
        ]);
    }






    public function subscribe() {
        $user = auth()->user();
        if ($user->exp > now()) {
            return response()->json([
                'remark'  => 'subscribed',
                'status'  => 'error',
                'message' => ['error' => 'You are already subscribed'],
            ]);
        }

        return response()->json([
            'remark'  => 'subscribe',
            'status'  => 'success',
            'message' => ['success' => 'You have to subscribe plan'],
        ]);

    }

    public function subscribePlan(Request $request) {
        $validator = Validator::make($request->all(), [
            'id'   => 'required|integer',
            'type' => 'required|string|in:plan,item',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();

        $planId   = 0;
        $itemId   = 0;
        $duration = null;

        if ($request->type == 'plan') {
            $plan = Plan::where('status', 1)->find($request->id);

            if (!$plan) {
                return response()->json([
                    'remark'  => 'not_found',
                    'status'  => 'error',
                    'message' => ['error' => 'Plan not found'],
                ]);
            }

            if ($user->exp > now()) {
                return response()->json([
                    'remark'  => 'already_subscribe',
                    'status'  => 'error',
                    'message' => ['error' => 'You already subscribed to a plan'],
                ]);
            }
            $planId   = $plan->id;
            $duration = $plan->duration;

        } else {
            $item = Item::active()->hasVideo()->where('id', $request->id)->first();
            if (!$item) {
                return response()->json([
                    'remark'  => 'not_found',
                    'status'  => 'error',
                    'message' => ['error' => 'Item not found'],
                ]);
            }
            $existItem = Subscription::active()->where('user_id', $user->id)->where('item_id', $item->id)->whereDate('expired_date', '>', now())->exists();
            if ($existItem) {
                return response()->json([
                    'remark'  => 'already_exists',
                    'status'  => 'error',
                    'message' => ['error' => 'Already subscribe this item'],
                ]);
            }
            $itemId   = $item->id;
            $duration = $item->rental_period;
        }

        $pendingPayment = $user->deposits()->where('status', Status::PAYMENT_PENDING)->count();
        if ($pendingPayment > 0) {
            return response()->json([
                'remark'  => 'pending_payment',
                'status'  => 'error',
                'message' => ['error' => 'Already 1 payment in pending. Please wait'],
            ]);
        }

        $subscription               = new Subscription();
        $subscription->user_id      = $user->id;
        $subscription->plan_id      = $planId;
        $subscription->item_id      = $itemId;
        $subscription->expired_date = now()->addDays($duration);
        $subscription->save();

        $notify[] = 'Plan Purchase';

        return response()->json([
            'remark'  => 'subscribe_payment',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'type'            => $request->type,
                'subscription_id' => $subscription->id,
                'redirect_url'    => route('user.deposit.index'),
            ],
        ]);
    }

    public function purchasePlan(Request $request) {
        $validator = Validator::make($request->all(), [
            'username'    => 'required',
            'token'       => 'required',
            'plan_id'     => 'required|integer',
            'amount'      => 'required',
            'method_code' => 'required|in:-1,-2',
            'type'        => 'required|string|in:plan,item',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $plan = Plan::active()->where('id', $request->plan_id)->first();
        if (!$plan) {
            $notify[] = ['error', 'Plan not found'];
            return back()->withNotify($notify);
        }

        $subscription               = new Subscription();
        $subscription->user_id      = auth()->id();
        $subscription->plan_id      = $plan->id;
        $subscription->expired_date = now()->addDays($plan->duration);
        $subscription->save();

        $general = gs();

        $detail = [
            'username' => $request->username,
            'plan'     => $plan->name,
            'amount'   => $request->amount,
        ];

        $data                  = new Deposit();
        $data->user_id         = auth()->id();
        $data->subscription_id = $subscription->id;
        $data->method_code     = $request->method_code;
        $data->method_currency = strtoupper($general->cur_text);
        $data->amount          = $request->amount;
        $data->charge          = 0;
        $data->rate            = gs()->cur_text;
        $data->final_amount    = $request->amount;
        $data->btc_amount      = 0;
        $data->btc_wallet      = "";
        $data->detail          = $detail;
        $data->trx             = getTrx();
        $data->status          = Status::PAYMENT_PENDING;
        $data->save();

        $user          = User::find($data->user_id);
        $user->plan_id = $subscription->plan_id;
        $user->exp     = $subscription->expired_date;
        $user->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = $user->username . ' subscribed to ' . $plan->name;
        $adminNotification->click_url = urlPath('admin.deposit.pending');
        $adminNotification->save();

        notify($user, 'SUBSCRIBE_PLAN', [
            'plan'     => $plan->name,
            'price'    => showAmount($plan->pricing),
            'duration' => $plan->duration,
        ]);

        $notify[] = 'You have deposit request has been taken';
        return response()->json([
            'remark'  => 'payment_pending',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);

    }

    public function purchaseFromApp(Request $request) {
        $validator = Validator::make($request->all(), [
            'plan_id'       => 'required|integer',
            'user_id'       => 'required|integer',
            'method_code'   => 'required|in:-1,-2',
            'amount'        => 'required|numeric|gt:0',
            'currency'      => 'required|string',
            'purchaseToken' => 'required',
            'packageName'   => 'required',
            'productId'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = User::active()->where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'remark'  => 'invalid_user',
                'status'  => 'error',
                'message' => ['error' => 'User not found'],
            ]);
        }

        $plan = Plan::active()->where('id', $request->plan_id)->first();
        if (!$plan) {
            return response()->json([
                'remark'  => 'invalid_plan',
                'status'  => 'error',
                'message' => ['error' => 'Plan not found'],
            ]);
        }

        if ($request->method_code == -1) {
            $general         = gs();
            $jsonKeyFilePath = getFilePath('appPurchase') . '/' . $general->app_purchase_credentials->google->file;
            $client          = new \Google_Client();
            $client->setAuthConfig($jsonKeyFilePath);
            $client->setScopes([\Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);
            $service = new \Google_Service_AndroidPublisher($client);

            $packageName   = $request->packageName;
            $productId     = $request->productId;
            $purchaseToken = $request->token;

            $response = $service->purchases_products->get($packageName, $productId, $purchaseToken);

            if ($response->getPurchaseState() != 0) {
                return response()->json([
                    'remark'  => 'invalid_purchase',
                    'status'  => 'error',
                    'message' => ['error' => 'Invalid purchase'],
                ]);
            }
        }

        $subscription               = new Subscription();
        $subscription->user_id      = $user->id;
        $subscription->plan_id      = $plan->id;
        $subscription->item_id      = 0;
        $subscription->expired_date = now()->addDays($plan->duration);
        $subscription->save();

        $data                  = new Deposit();
        $data->user_id         = $user->id;
        $data->subscription_id = $subscription->id;
        $data->method_code     = $request->method_code;
        $data->method_currency = strtoupper($request->currency);
        $data->amount          = $plan->pricing;
        $data->charge          = 0;
        $data->rate            = showAmount($request->amount / $plan->pricing);
        $data->final_amount    = $request->amount;
        $data->btc_amount      = 0;
        $data->btc_wallet      = "";
        $data->trx             = getTrx();
        $data->status          = Status::PAYMENT_SUCCESS;
        $data->save();

        return response()->json([
            'remark'  => 'plan_purchase',
            'status'  => 'success',
            'message' => ['success' => 'Plan purchase successfully'],
        ]);
    }

    public function wishlists() {
        $notify[]  = 'Wishlist';
        $wishlists = Wishlist::with('item.category', 'episode')->where('user_id', auth()->id())->paginate(getPaginate());

        return response()->json([
            'remark'  => 'wishlist',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'wishlists' => $wishlists,
            ],
        ]);
    }

    public function addWishList(Request $request) {
        $validator = Validator::make($request->all(), [
            'item_id'    => 'required_without:episode_id',
            'episode_id' => 'required_without:item_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $wishlist = new Wishlist();

        if ($request->item_id) {
            $item = Item::find($request->item_id);
            if (!$item) {
                return response()->json([
                    'remark'  => 'not_found',
                    'status'  => 'error',
                    'message' => ['error' => 'Item not found'],
                ]);
            }
            $exits             = Wishlist::where('item_id', $item->id)->where('user_id', auth()->id())->first();
            $wishlist->item_id = $item->id;

        } else if ($request->episode_id) {
            $episode = Episode::find($request->episode_id);
            if (!$episode) {
                return response()->json([
                    'remark'  => 'not_found',
                    'status'  => 'error',
                    'message' => ['error' => 'Episode not found'],
                ]);
            }
            $exits                = Wishlist::where('episode_id', $episode->id)->where('user_id', auth()->id())->first();
            $wishlist->episode_id = $episode->id;
        }

        if (!$exits) {
            $wishlist->user_id = auth()->id();
            $wishlist->save();

            $notify[] = 'Video added to wishlist successfully';
            return response()->json([
                'remark'  => 'added_successfully',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        }

        $notify[] = 'Already in wishlist';
        return response()->json([
            'remark'  => 'already_exits',
            'status'  => 'error',
            'message' => ['error' => 'Already in wishlist'],
        ]);
    }

    public function checkWishlist(Request $request) {

        $validator = Validator::make($request->all(), [
            'item_id'    => 'required_without:episode_id',
            'episode_id' => 'required_without:item_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }
        $wishlist = 0;
        if ($request->item_id) {
            $item = Item::find($request->item_id);
            if (!$item) {
                return response()->json([
                    'remark'  => 'not_found',
                    'status'  => 'error',
                    'message' => ['error' => 'Item not found'],
                ]);
            }
            $wishlist = Wishlist::where('item_id', $item->id)->where('user_id', auth()->id())->count();
        } else if ($request->episode_id) {
            $episode = Episode::find($request->episode_id);
            if (!$episode) {
                return response()->json([
                    'remark'  => 'not_found',
                    'status'  => 'error',
                    'message' => ['error' => 'Episode not found'],
                ]);
            }
            $wishlist = Wishlist::where('episode_id', $episode->id)->where('user_id', auth()->id())->count();
        }
        if ($wishlist) {
            $notify[] = 'Already in wishlist';
            return response()->json([
                'remark'  => 'true',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        } else {
            $notify[] = 'Data not found';
            return response()->json([
                'remark'  => 'false',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
    }

    public function removeWishlist(Request $request) {
        $validator = Validator::make($request->all(), [
            'item_id'    => 'required_without:episode_id',
            'episode_id' => 'required_without:item_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        if ($request->item_id) {
            $wishlist = Wishlist::where('item_id', $request->item_id)->where('user_id', auth()->id());
        }

        if ($request->episode_id) {
            $wishlist = Wishlist::where('episode_id', $request->episode_id)->where('user_id', auth()->id());
        }

        $wishlist = $wishlist->first();

        if ($wishlist) {
            $wishlist->delete();
            $notify[] = 'Video removed from wishlist successfully';
            return response()->json([
                'remark'  => 'remove_successfully',
                'status'  => 'success',
                'message' => ['success' => 'Video removed from wishlist successfully'],
            ]);
        }

        $notify[] = 'Something wrong';
        return response()->json([
            'remark'  => 'something_wrong',
            'status'  => 'error',
            'message' => ['error' => $notify],
        ]);
    }

    public function history() {
        $notify[]  = 'History';
        $histories = History::with('item', 'episode')->where('user_id', auth()->id())->paginate(getPaginate());

        return response()->json([
            'remark'  => 'history',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'histories' => $histories,
            ],
        ]);
    }

    public function watchVideo(Request $request) {
        $item = Item::hasVideo()->where('status', 1)->where('id', $request->item_id)->with('category', 'sub_category')->first();

        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

        $item->increment('view');

        $relatedItems = Item::hasVideo()->orderBy('id', 'desc')->where('category_id', $item->category_id)->where('id', '!=', $request->item_id)->limit(6)->get();

        $imagePath     = getFilePath('item_portrait');
        $landscapePath = getFilePath('item_landscape');
        $episodePath   = getFilePath('episode');

        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;

        if ($item->item_type == Status::EPISODE_ITEM) {
            $episodes = Episode::hasVideo()->where('item_id', $request->item_id)->get();

            if ($episodes->count()) {
                $this->storeHistory(0, $episodes[0]->id);
                $this->storeVideoReport(0, $episodes[0]->id);
            }

            $notify[] = 'Episode Video';
            return response()->json([
                'remark'  => 'episode_video',
                'status'  => 'success',
                'message' => ['success' => $notify],
                'data'    => [
                    'item'           => $item,
                    'episodes'       => $episodes,
                    'related_items'  => $relatedItems,
                    'portrait_path'  => $imagePath,
                    'landscape_path' => $landscapePath,
                    'episode_path'   => $episodePath,
                ],
            ]);
        }

        $watchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);

        if (!$watchEligable[0]) {
            return response()->json([
                'remark'  => 'unauthorized_' . $watchEligable[1],
                'status'  => 'error',
                'message' => ['error' => 'Unauthorized user'],
                'data'    => [
                    'item'           => $item,
                    'portrait_path'  => $imagePath,
                    'landscape_path' => $landscapePath,
                    'related_items'  => $relatedItems,
                ],
            ]);
        }

        $this->storeHistory($item->id, 0);
        $this->storeVideoReport($item->id, 0);

        $notify[] = 'Item Video';

        return response()->json([
            'remark'  => 'item_video',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'item'           => $item,
                'related_items'  => $relatedItems,
                'portrait_path'  => $imagePath,
                'landscape_path' => $landscapePath,
                'episode_path'   => $episodePath,
                'watchEligable'  => $watchEligable[0],
                'type'           => $watchEligable[1],
            ],
        ]);

    }

    protected function checkWatchEligableItem($item, $userHasSubscribed) {
        if ($item->version == Status::PAID_VERSION) {
            $watchEligable = $userHasSubscribed ? true : false;
            $type          = 'paid';
        } else if ($item->version == Status::RENT_VERSION) {
            $hasSubscribedItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $item->id)->whereDate('expired_date', '>', now())->exists();
            if ($item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = ($userHasSubscribed || $hasSubscribedItem) ? true : false;
            }
            $type = 'rent';
        } else {
            $watchEligable = true;
            $type          = 'free';
        }
        return [$watchEligable, $type];
    }

    protected function checkWatchEligableEpisode($episode, $userHasSubscribed) {
        if ($episode->version == Status::PAID_VERSION) {
            $watchEligable = $userHasSubscribed ? true : false;
            $type          = 'paid';
        } else if ($episode->version == Status::RENT_VERSION) {
            $hasSubscribedItem = Subscription::active()->where('user_id', auth()->id())->where('item_id', $episode->item_id)->whereDate('expired_date', '>', now())->exists();
            if (@$episode->item->exclude_plan) {
                $watchEligable = $hasSubscribedItem ? true : false;
            } else {
                $watchEligable = ($userHasSubscribed || $hasSubscribedItem) ? true : false;
            }
            $type = 'rent';
        } else {
            $watchEligable = true;
            $type          = 'free';
        }
        return [$watchEligable, $type];
    }

    public function playVideo(Request $request) {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $item = Item::hasVideo()->where('status', 1)->where('id', $request->item_id)->first();
        if (!$item) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Item not found'],
            ]);
        }

        if ($item->item_type == Status::EPISODE_ITEM && !$request->episode_id) {
            return response()->json([
                'remark'  => 'not_found',
                'status'  => 'error',
                'message' => ['error' => 'Episode id field is required'],
            ]);
        }

        $userHasSubscribed = (auth()->check() && auth()->user()->exp > now()) ? Status::ENABLE : Status::DISABLE;

        if ($item->item_type == Status::EPISODE_ITEM) {
            $episode = Episode::hasVideo()->where('item_id', $request->item_id)->find($request->episode_id);

            if (!$episode) {
                return response()->json([
                    'remark'  => 'no_episode',
                    'status'  => 'error',
                    'message' => ['error' => 'No episode found'],
                ]);
            }
            $watchEligable = $this->checkWatchEligableEpisode($episode, $userHasSubscribed);

            if (!$watchEligable[0]) {
                return response()->json([
                    'remark'  => 'unauthorized_' . $watchEligable[1],
                    'status'  => 'error',
                    'message' => ['error' => 'Unauthorized user'],
                    'data'    => [
                        'item' => $item,
                    ],
                ]);
            }

            $video    = $episode->video;
            $remark   = 'episode_video';
            $notify[] = 'Episode Video';

        } else {

            $watchEligable = $this->checkWatchEligableItem($item, $userHasSubscribed);
            if (!$watchEligable[0]) {
                return response()->json([
                    'remark'  => 'unauthorized_' . $watchEligable[1],
                    'status'  => 'error',
                    'message' => ['error' => 'Unauthorized user'],
                    'data'    => [
                        'item' => $item,
                    ],
                ]);
            }

            $video    = $item->video;
            $remark   = 'item_video';
            $notify[] = 'Item Video';
        }

        $videoFile    = $this->videoList($video);
        $subtitles    = $video->subtitles()->get();
        $adsTime      = $video->getAds();
        $subtitlePath = getFilePath('subtitle');

        return response()->json([
            'remark'  => $remark,
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'video'         => $videoFile,
                'subtitles'     => !blank($subtitles) ? $subtitles : null,
                'adsTime'       => !blank($adsTime) ? $adsTime : null,
                'subtitlePath'  => $subtitlePath,
                'watchEligable' => $watchEligable[0],
                'type'          => $watchEligable[1],
            ],
        ]);

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

    protected function storeHistory($itemId = null, $episodeId = null) {
        if (auth()->check()) {
            if ($itemId) {
                $history = History::where('user_id', auth()->id())->orderBy('id', 'desc')->limit(1)->first();
                if (!$history || ($history && $history->item_id != $itemId)) {
                    $history          = new History();
                    $history->user_id = auth()->id();
                    $history->item_id = $itemId;
                    $history->save();
                }
            }
            if ($episodeId) {
                $history = History::where('user_id', auth()->id())->orderBy('id', 'desc')->limit(1)->first();
                if (!$history || ($history && $history->episode_id != $episodeId)) {
                    $history             = new History();
                    $history->user_id    = auth()->id();
                    $history->episode_id = $episodeId;
                    $history->save();
                }
            }
        }
    }

    protected function storeVideoReport($itemId = null, $episodeId = null) {
        $deviceId = md5($_SERVER['HTTP_USER_AGENT']);

        if ($itemId) {
            $report = VideoReport::whereDate('created_at', now())->where('device_id', $deviceId)->where('item_id', $itemId)->exists();
        }

        if ($episodeId) {
            $report = VideoReport::whereDate('created_at', now())->where('device_id', $deviceId)->where('episode_id', $episodeId)->exists();
        }
        if (!$report) {
            $videoReport             = new VideoReport();
            $videoReport->device_id  = $deviceId;
            $videoReport->item_id    = $itemId ?? 0;
            $videoReport->episode_id = $episodeId ?? 0;
            $videoReport->save();
        }
    }

    public function addDeviceToken(Request $request) {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            $notify[] = 'Token already exists';
            return response()->json([
                'remark'  => 'token_exists',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        $notify[] = 'Token saved successfully';
        return response()->json([
            'remark'  => 'token_saved',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function show2faForm() {
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $notify[]  = '2FA Qr';
        return response()->json([
            'remark'  => '2fa_qr',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'secret'      => $secret,
                'qr_code_url' => $qrCodeUrl,
            ],
        ]);
    }

    public function create2fa(Request $request) {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'code'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code, $request->secret);
        if ($response) {
            $user->tsc = $request->secret;
            $user->ts  = Status::ENABLE;
            $user->save();

            $notify[] = 'Google authenticator activated successfully';
            return response()->json([
                'remark'  => '2fa_qr',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        } else {
            $notify[] = 'Wrong verification code';
            return response()->json([
                'remark'  => 'wrong_verification',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
    }

    public function disable2fa(Request $request) {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts  = Status::DISABLE;
            $user->save();
            $notify[] = 'Two factor authenticator deactivated successfully';
            return response()->json([
                'remark'  => '2fa_qr',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        } else {
            $notify[] = 'Wrong verification code';
            return response()->json([
                'remark'  => 'wrong_verification',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
    }

    public function pushNotifications() {
        $notifications = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[]      = 'Push notifications';
        return response()->json([
            'remark'  => 'notifications',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'notifications' => $notifications,
            ],
        ]);
    }

    public function pushNotificationsRead($id) {
        $notification = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->find($id);
        if (!$notification) {
            $notify[] = 'Notification not found';
            return response()->json([
                'remark'  => 'notification_not_found',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $notify[]                = 'Notification marked as read successfully';
        $notification->user_read = 1;
        $notification->save();

        return response()->json([
            'remark'  => 'notification_read',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function userInfo() {
        $notify[] = 'User information';
        return response()->json([
            'remark'  => 'user_info',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'user' => auth()->user(),
            ],
        ]);
    }

    public function deleteAccount() {
        $user           = auth()->user();
        $user->username = 'deleted_' . $user->username;
        $user->email    = 'deleted_' . $user->email;
        $user->save();

        $user->tokens()->delete();

        $notify[] = 'Account deleted successfully';
        return response()->json([
            'remark'  => 'account_deleted',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function rentedItem() {
        $rentedItems  = Subscription::active()->where('item_id', '!=', 0)->where('user_id', auth()->id())->with('item')->apiQuery();
        $notify[]     = 'Rented Items';
        $imagePath    = getFilePath('item_landscape');
        $portraitPath = getFilePath('item_portrait');
        return response()->json([
            'remark'  => 'rented_items',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'rentedItems'    => $rentedItems,
                'landscape_path' => $imagePath,
                'portrait_path'  => $portraitPath,
            ],
        ]);
    }

}
