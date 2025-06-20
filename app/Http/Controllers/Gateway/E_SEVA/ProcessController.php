<?php

namespace App\Http\Controllers\Gateway\E_SEVA;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $general = gs(); // general settings
        $eSevaAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter); // E-Seva gateway parameters

        // E-Seva required params (replace with actual E-Seva params if different)
        $val = [
            'merchant_id'     => trim($eSevaAcc->merchant_id),
            'order_id'        => $deposit->trx,
            'amount'          => round($deposit->final_amount, 2),
            'currency'        => $deposit->method_currency,
            'redirect_url'    => route('ipn.' . $deposit->gateway->alias),
            'cancel_url'      => route('home') . $deposit->failed_url,
            'language'        => 'EN',
            'billing_name'    => $deposit->user->fullname ?? 'Customer',
            'billing_email'   => $deposit->user->email ?? 'noemail@example.com',
            'billing_tel'     => $deposit->user->mobile ?? '0000000000',
        ];

        // Optional: Include any secure hash generation here if E-Seva requires it

        $send['val'] = $val;
        $send['view'] = 'user.payment.redirect'; // Laravel view that auto-posts
        $send['method'] = 'post';
        $send['url'] = 'https://pay.eseva.com/payment'; // Replace with real E-Seva URL

        return json_encode($send);
    }

    public function ipn()
    {
        // Assuming POST IPN from E-Seva
        $response = $_POST;

        if (isset($response['order_id']) && isset($response['status'])) {
            $deposit = Deposit::where('trx', $response['order_id'])->orderBy('id', 'DESC')->first();

            if (!$deposit) {
                return response('Invalid Transaction', 400);
            }

            $deposit->detail = $response;
            $deposit->save();

            // Confirm amount and status match
            if (
                $response['amount'] == round($deposit->final_amount, 2) &&
                strtolower($response['status']) == 'success' &&
                $deposit->status == Status::PAYMENT_INITIATE
            ) {
                PaymentController::userDataUpdate($deposit);
            }
        }

        return response('OK', 200); // Or redirect as needed
    }
}
