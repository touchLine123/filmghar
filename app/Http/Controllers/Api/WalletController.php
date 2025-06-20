<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\SupportTicketManager;
use AWS\CRT\HTTP\Request;

class WalletController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        parent::__construct();
        $this->userType     = 'user';
        $this->column       = 'user_id';
        $this->user = auth()->user();
        $this->apiRequest = true;
    }

    public function wallet(Request $request){
        echo "Test"; die;
    }
}
 