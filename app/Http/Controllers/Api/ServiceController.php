<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use DB;
class ServiceController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    public $unverifiedUser     = 201;
    public $inactiveUser       = 501;
    protected $helper;
    protected $email;
    protected $currency;
    protected $user;
    protected $cryptoCurrency;

    public function __construct()
    {
        $this->main_url       = "https://topups.reloadly.com";
        $this->helper         = new Common();
        $this->user           = new User();
    }
   
    public function getServices()
    {
        $data=Services::select('id','name','page','image','position','sorting','status')->orderBy('sorting', 'ASC')->get();
        
        // return response()->json($data);
        
        return response()->json([
            'data'      => $data,
            'url' => ENV('SERVICE_ICON'),
        ]);
           
    }
    
}
