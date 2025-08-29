<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use Aws\Credentials\Credentials;
use Aws\Sns\SnsClient;
use Illuminate\Support\Str;
use App\Models\Subscription;
use App\Models\User;

class SubsciptionController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function subscriptions(Request $request)
    {
        $subscriptions = Subscription::where('status', '1')->get();
        return response()->json([
            'status'  => 'success',
            'message' => 'Membership packages fetched successfully.',
            'data'    => $subscriptions
        ]);
    }
}