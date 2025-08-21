<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Country;
use App\Models\RequestPayment;
use App\Models\Transfer;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\DocumentVerification;
use App\Models\UsersKyc;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\File;
use Illuminate\Support\Facades\URL;
use App\Models\Kycdatastore;
use DB;
use App\Models\Notification;
use Carbon\Carbon;
use App\Models\Preference;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;

class ProfileController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }
    
    public function postFunction($url, $headers, $payloads)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloads));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        
        $result = curl_exec($ch);
        if ($result === false) {
            dd([
                'error' => curl_error($ch),
                'info' => curl_getinfo($ch)
            ]);
        }
        curl_close($ch);
        $response = json_decode($result, true);
        return $response;
    }

    public function checkUserStatusApi(Request $request)
    {
        $user       = User::where(['id' => $request->user_id])->first(['status']);
       
        $userStatus = $this->helper->getUserStatus($user->status);
        return response()->json([
            'status'      => $this->successStatus,
            'user-status' => $userStatus,
        ]);
    }

    public function checkProcessedByApi()
    {
        return response()->json([
            'status'      => $this->successStatus,
            'processedBy' => $this->helper->getPrefProcessedBy(),
        ]);
    }

    //Get User Updated Balance
    public function getDefaultWalletBalance()
    {
        // dd(request()->all());

        $wallet                          = Wallet::with(['currency:id,code,symbol'])->where(['user_id' => request('user_id'), 'is_default' => 'Yes'])->first(['currency_id', 'balance']);
        $success['defaultWalletBalance'] = $wallet->balance;
        $success['code'] = $wallet->currency->code;
        $success['symbol'] = $wallet->currency->symbol;
        $success['status']               = $this->successStatus;
        return response()->json(['success' => $success], $this->successStatus);
    }

    public function getUserAvailableWalletsBalances()
    {
        // dd(request()->all());
        $user_id = request('user_id');
        if ($user_id)
        {
            

            $wallet            = new Wallet();
            $wallets           = $wallet->getAvailableBalance($user_id);
            $success['status'] = $this->successStatus;
            return response()->json(['status' => $this->successStatus,'success' => $success, 'wallets' => $wallets], $this->successStatus);
        }
        else
        {
            echo "In else block";
            exit();
            return false;
        }
    }

    public function details()
    {
        // dd(request()->all());
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }

    //Grab specific user profile details based on email address.
    public function getUserSpecificProfile()
    {
        // dd(request()->all());
        try
        {
            if (request('type') == 'sendMoney')
            {
                $transfer = Transfer::where(['id' => request('tr_ref_id')])->first(['receiver_id', 'email', 'phone']);
                if (!empty($transfer->receiver))
                {
                    $success['receiver']['first_name'] = $transfer->receiver->first_name;
                    $success['receiver']['last_name']  = $transfer->receiver->last_name;
                    $success['receiver']['email']      = $transfer->receiver->email;
                    $success['receiver']['picture']    = $transfer->receiver->picture;
                    $success['status']                 = $this->successStatus;
                    return response()->json(['success' => $success], $this->successStatus);
                }
                else
                {
                    $success['receiver']['first_name'] = null;
                    $success['receiver']['last_name']  = null;
                    $success['receiver']['email']      = $transfer->email;
                    $success['receiver']['phone']      = $transfer->phone;
                    $success['receiver']['picture']    = null;
                    $success['status']                 = $this->successStatus;
                    return response()->json(['success' => $success], $this->successStatus);
                }
            }
            elseif (request('type') == 'requestMoneyCreate')
            {
                $requestPaymentCreate = RequestPayment::where(['id' => request('tr_ref_id')])->first(['receiver_id', 'email', 'phone']);
                if (!empty($requestPaymentCreate->receiver))
                {
                    $success['receiver']['first_name'] = $requestPaymentCreate->receiver->first_name;
                    $success['receiver']['last_name']  = $requestPaymentCreate->receiver->last_name;
                    $success['receiver']['email']      = $requestPaymentCreate->receiver->email;
                    $success['receiver']['picture']    = $requestPaymentCreate->receiver->picture;
                    $success['status']                 = $this->successStatus;
                    return response()->json(['success' => $success], $this->successStatus);
                }
                else
                {
                    $success['receiver']['first_name'] = null;
                    $success['receiver']['last_name']  = null;
                    $success['receiver']['email']      = $requestPaymentCreate->email;
                    $success['receiver']['phone']      = $requestPaymentCreate->phone;
                    $success['receiver']['picture']    = null;
                    $success['status']                 = $this->successStatus;
                    return response()->json(['success' => $success], $this->successStatus);
                }
            }
            elseif (request('type') == 'requestMoneyAccept')
            {
                $requestPaymentAccept = RequestPayment::where(['id' => request('tr_ref_id')])->first(['user_id', 'email', 'phone']);
                if (!empty($requestPaymentAccept->user))
                {
                    $success['user']['first_name'] = $requestPaymentAccept->user->first_name;
                    $success['user']['last_name']  = $requestPaymentAccept->user->last_name;
                    $success['user']['email']      = $requestPaymentAccept->user->email;
                    $success['user']['picture']    = $requestPaymentAccept->user->picture;
                }
                else
                {
                    $success['user']['first_name'] = null;
                    $success['user']['last_name']  = null;
                    $success['user']['email']      = $requestPaymentAccept->email;
                    $success['user']['phone']      = $requestPaymentAccept->phone;
                    $success['user']['picture']    = null;
                    $success['status']             = $this->successStatus;
                }
                $success['status'] = $this->successStatus;
                return response()->json(['success' => $success], $this->successStatus);
            }
            else
            {
                $user              = User::where(['email' => request('email')])->first(['email']);
                $success['user']   = $user->email;
                $success['status'] = $this->successStatus;
                return response()->json(['success' => $success], $this->successStatus);
            }
        }
        catch (\Exception $e)
        {
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage(); // echo print_r($e->getMessage());
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }

    //Fetch Specific User Profile Details
    public function getUserProfile()
    {
        // dd(request()->all());
       
       try{
        //id is needed for user_detail relation
        $user              = User::with('user_detail', 'user_detail.country:id')->where(['id' => request('user_id')])->first(['id', 'first_name', 'second_last_name','middle_name', 'last_name', 'email', 'phone', 'formattedPhone', 'carrierCode', 'defaultCountry','picture','created_at','vcard','status']);
        $success['status'] = $this->successStatus;

        //users data
        $success['user']['first_name']     = $user->first_name??'';
        $success['user']['middle_name']    = $user->middle_name??'';
        $success['user']['second_last_name']    = $user->second_last_name??'';
        $success['user']['last_name']      = $user->last_name??'';
        $success['user']['email']          = $user->email??'';
        $success['user']['phone']          = $user->phone??'';
        $success['user']['formattedPhone'] = $user->formattedPhone??'';
        $success['user']['carrierCode']    = $user->carrierCode??'';
        $success['user']['defaultCountry'] = $user->defaultCountry??'';
        $success['user']['picture']        = $user->picture??'';
        $success['user']['created_at']     = $user->created_at??'';
        $success['user']['vcard_status']   = $user->vcard;
        if($user->picture??'') {
         $success['user']['picture'] = url('/').'/'.'public/user_dashboard/profile/'.$user->picture??'';
        }

        //user details deta
        $success['user']['address_1'] = !empty($user->user_detail->address_1) ? $user->user_detail->address_1 : '';
        $success['user']['address_2'] = !empty($user->user_detail->address_2) ? $user->user_detail->address_2 : '';

        $success['user']['city']      = !empty($user->user_detail->city) ? $user->user_detail->city : '';
        $success['user']['state']     = !empty($user->user_detail->state) ? $user->user_detail->state : '';
        $success['user']['country']     = !empty($user->user_detail->country) ? $user->user_detail->country : '';
        $success['user']['zip_code']     = !empty($user->user_detail->zip_code) ? $user->user_detail->zip_code : '';
        //countries and country_id
        $success['countries']          = Country::get(['id', 'name']);
        $success['user']['country_id'] = !empty($user->user_detail->country_id) ? $user->user_detail->country_id : '';

        //timezones and timezone
        $success['timezones']        = phpDefaultTimeZones();
        $success['user']['timezone'] = !empty($user->user_detail->timezone) ? $user->user_detail->timezone : '';

        $wallets            = Wallet::whereHas('currency', function ($q) {
            $q->where(['type' => 'fiat']);
        })->with(['currency:id,code'])->where(['user_id' => request('user_id')])->get(['id', 'currency_id', 'is_default']);
        $success['wallets'] = $wallets->map(function ($wallet)
        {
            $arr['id']           = $wallet->id;
            $arr['currencyCode'] = $wallet->currency->code;
            $arr['is_default']   = $wallet->is_default;
            return $arr;
        });
        
        if($user->status == 'Inactive'){
            $success['user']['user_status']     = 'Inactive';
        }else{
            $success['user']['user_status']     = 'Active';
        }

        return response()->json(['success' => $success], $this->successStatus);
       }catch(\Exception $e){
            return response()->json(['success' => $success], $this->successStatus);
       }
    }

    public function userProfileDuplicateEmailCheckApi(Request $request)
    {
        // dd(request()->all());

        $req_id = $request->user_id;
        $email  = User::where(['email' => $request->email])->where(function ($query) use ($req_id)
        {
            $query->where('id', '!=', $req_id);
        })->exists();

        if ($email)
        {
            $data['status'] = true;
            $data['fail']   = "The email has already been taken!";
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "Email Available!";
        }
        return json_encode($data);
    }

    public function updateUserProfile()
    {
        try
        {
            \DB::beginTransaction();

            $user             = User::find(request('user_id'), ['id', 'first_name', 'middle_name','second_last_name', 'last_name', 'email', 'phone', 'defaultCountry', 'carrierCode', 'formattedPhone', 'picture']);
            $user->first_name = request('first_name');
            $user->middle_name = request('middle_name');
            $user->last_name  = request('last_name');
            $user->second_last_name  = request('second_last_name');
            $user->email      = request('email');
            $user->phone      = request('phone');
            $formattedPhone   = ltrim(request('phone'), '0');
            
            $countries = Country::where('name', request('user_defaultCountry'))->first();
           
            $path       = public_path('user_dashboard/profile/');
            if(request('picture') && (request('picture') != NULL) && (request('picture') != "null")) {
            $user->picture = $this->createProfileImageFromBase64(request('picture'), $path);
            }
            
            if (!empty(request('phone')))
            {
                $user->phone          = preg_replace("/[\s-]+/", "", $formattedPhone);
                $user->defaultCountry = request('user_defaultCountry');
                $user->carrierCode    = request('user_carrierCode');
                $user->formattedPhone = request('formattedPhone');
            }
            else
            {
                $user->phone          = null;
                $user->defaultCountry = null;
                $user->carrierCode    = null;
                $user->formattedPhone = null;
            }
            $user->save();
            
            $userDetail             = UserDetail::where(['user_id' => request('user_id')])->first(['id','country' ,'country_id', 'address_1', 'city', 'state', 'timezone']);
            $userDetail->country_id = $countries->id;
            $userDetail->country    = $countries->id;
            $userDetail->zip_code   = request('zip_code');
            $userDetail->address_1  = request('address');
            $userDetail->address_2  = request('address_2');
            $userDetail->city       = request('city');
            $userDetail->state      = request('state');
            $userDetail->timezone   = Preference::where(['category' => 'preference', 'field' => 'dflt_timezone'])->first(['value'])->value;
            $userDetail->save();

            $defaultWallet = Wallet::where('user_id', request('user_id'))->where('is_default', 'Yes')->first(['id', 'is_default']);
            if (@$defaultWallet->id != request('defaultWallet'))
            {
                $defaultWallet->is_default = 'No';
                $defaultWallet->save();

                $walletToDefault             = Wallet::find(request('defaultWallet'), ['id', 'is_default']);
                $walletToDefault->is_default = 'Yes';
                $walletToDefault->save();
            }

            \DB::commit();

            if (!empty($user->formattedPhone))
            {
                $success['formattedPhone'] = $user->formattedPhone;
            }
            $success['username'] = $user->first_name . ' ' . $user->last_name;
            $success['email']    = $user->email;

            return response()->json(['status'=>$this->successStatus,'success' => $success], $this->successStatus);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['message'] = $e->getMessage();
            return response()->json(['status'=>$this->unauthorisedStatus,'success' => $success], $this->unauthorisedStatus);
        }
    }
    
    public function pages(Request $request) {
        $url = $request->url;
        $pages = DB::table('pages')->where('url',$url)->first();
        
        //$success['status']  = $this->successStatus;
        $success['data'] = $pages;
        return response()->json(['status'=>$this->successStatus, 'success' => $success], $this->successStatus);
    }
    
    //get kyc status
    public function getKycStatus()
    {
        // dd(request()->all());
        try
        {
           \DB::beginTransaction();
           
          $userData = DocumentVerification::where('user_id',request('user_id'))->groupBy('verification_type')->orderBy('id','DESC')->get();
          if(count($userData) < 3) {
             $success['status'] = 'Not Uploaded';
          } else {
          if($userData->isEmpty()) {
             $success['status'] = 'Not Uploaded';
          } else {
              $i = 0; foreach($userData as $userData1) {
                  
                  $file = File::where('id',$userData1->file_id)->first();
                  if($file) {
                  $userData[$i]->file_url = URL::to('/').'/public/uploads/user-documents/identity-proof-files/'.$file->filename;
                  $userData[$i]->file_name = $file->originalname;
                  }
                  $userDataStatus[] = $userData1->status;
              $i++; }
              
          if(in_array('rejected',$userDataStatus)) {
              $success['status'] = 'rejected';
          } else if(in_array('pending',$userDataStatus)) {
              $success['status'] = 'pending';
          } else if(in_array('approved',$userDataStatus)) {
              $success['status'] = 'approved';
          }
          }
          
          }
          
          $success['data'] = $userData;
          return response()->json(['status'=>$this->successStatus, 'success' => $success], $this->successStatus);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage(); // echo print_r($e->getMessage());
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
        
    }
    
    //uploadKycDocument
    public function uploadKycDocument(Request $request)
    {
        try
        {
            $user_id = request('user_id');
            $identitytype = request('identity_type');
            $identitynumber = request('identity_number');
            $documenttype = request('document_type');
        
            $app_ver  = request('app_ver');
            $device_name  = request('device_name');
            $device_manufacture  = request('device_manufacture');
            $device_model  = request('device_model');
            $os_ver  = request('os_ver');
            $device_os  = request('device_os');
            $ip=$request->ip();    
            $location_details = json_decode(file_get_contents("http://ipinfo.io/$ip/json"));
            $ip_address = $request->ip();
            $city = $location_details->city;
            $country = $location_details->country;
            $updated_at = Carbon::now()->toDateTimeString();
    
            if($documenttype == 'photo') {
                $user  = User::find($user_id);
                $user->photo_verified = false;
                $user->kyc_submitted_on = $updated_at;
                $user->save();
    
                $param = "photo";
                $path       = 'uploads/user-documents/photo-proof-files';
                $fileId = $this->insertUserIdentityInfoToFilesTable(request('photo'),$param, $path);
                $documentVerification = DocumentVerification::where(['user_id' => $user_id, 'verification_type' => 'photo'])->first();
                
                if (empty($documentVerification))
                {
                    $createDocumentVerification          = new DocumentVerification();
                    $createDocumentVerification->user_id = request('user_id');
                    if (!empty(request('photo')))
                    {
                        $createDocumentVerification->file_id = $fileId;
                    }
                    $createDocumentVerification->app_ver = $app_ver;
                    $createDocumentVerification->device_name = $device_name;
                    $createDocumentVerification->device_manufacture = $device_manufacture;
                    $createDocumentVerification->device_model = $device_model;
                    $createDocumentVerification->os_ver = $os_ver;
                    $createDocumentVerification->device_os = $device_os;
                    $createDocumentVerification->ip_address = $ip_address;
                    $createDocumentVerification->city = $city;
                    $createDocumentVerification->country = $country;
                    $createDocumentVerification->updated_at = $updated_at;
                    $createDocumentVerification->verification_type = 'photo';
                    $createDocumentVerification->identity_type     = 'photo';
                    $createDocumentVerification->identity_number   =  '';
                    $createDocumentVerification->status            = 'pending';
                    $createDocumentVerification->local_tran_time   = $request->local_tran_time;
                    $createDocumentVerification->save();
                }
                else
                {
                    $documentVerification->user_id = request('user_id');
                    if (!empty(request('photo')))
                    {
                        $documentVerification->file_id = $fileId;
                    }
                    $documentVerification->app_ver = $app_ver;
                    $documentVerification->device_name = $device_name;
                    $documentVerification->device_manufacture =$device_manufacture;
                    $documentVerification->device_model = $device_model;
                    $documentVerification->os_ver = $os_ver;
                    $documentVerification->device_os =$device_os;
                    $documentVerification->ip_address =$ip_address;
                    $documentVerification->city =$city;
                    $documentVerification->country = $country;
                    $documentVerification->updated_at = $updated_at;
                    $documentVerification->verification_type = 'photo';
                    $documentVerification->identity_type     = 'photo';
                    $documentVerification->identity_number   = '';
                    $documentVerification->status            = 'pending';
                    $documentVerification->local_tran_time   = $request->local_tran_time;
                    $documentVerification->save();
                }
                $adminAllowed = Notification::has_permission([1]);
                foreach($adminAllowed as $admins){
                    $name = User::where('id', $user_id)->first();
                    Notification::insert([
                        'user_id'               => $user_id,
                        'notification_to'       => 1,
                        'notification_type_id'  => 12,
                        'notification_type'     => 'App',
                        'description'           => "User ".$name->first_name." has uploaded Photo Verification Image ",
                        'url_to_go'             => 'admin/users/kyc-verications/'.request('user_id'),
                        'local_tran_time'       => $request->local_tran_time
                    ]);
                }
                $mailresponse = $this->helper->sendTransactionNotificationToAdmin('user_verification', ['data' => $documentVerification]);
                $success['message'] = "Kyc document is uploaded successfully.";
                return response()->json(['status'=>$this->successStatus, 'success' => $success], $this->successStatus);
            }
            
            if($documenttype == 'id_proof') {
                $user  = User::find($user_id);
                $user->identity_verified = false;
                $user->kyc_submitted_on = $updated_at;
                $user->save();
    
                $param  = "id_proof";
                $path   = 'uploads/user-documents/identity-proof-files';
                $fileId = $this->insertUserIdentityInfoToFilesTable(request('id_proof'),$param, $path);
                
                $param_back  = "id_proof_back";
                $path_back   = 'uploads/user-documents/identity-proof-files';
                $fileId_back = $this->insertUserIdentityInfoToFilesTable(request('id_proof_back'),$param_back, $path_back);
    
                $documentVerification = DocumentVerification::where(['user_id' => $user_id, 'verification_type' => 'identity'])->first();
    
                if (empty($documentVerification))
                {
                    $createDocumentVerification = new DocumentVerification();
                    $createDocumentVerification->user_id = request('user_id');
                    if (!empty(request('id_proof')))
                    {
                        $createDocumentVerification->file_id = $fileId;
                    }
                    if (!empty(request('id_proof_back')))
                    {
                        $createDocumentVerification->file_back_id = $fileId_back;
                    }
                    $createDocumentVerification->app_ver = $app_ver;
                    $createDocumentVerification->device_name = $device_name;
                    $createDocumentVerification->device_manufacture =$device_manufacture;
                    $createDocumentVerification->device_model = $device_model;
                    $createDocumentVerification->os_ver = $os_ver;
                    $createDocumentVerification->device_os =$device_os;
                    $createDocumentVerification->ip_address =$ip_address;
                    $createDocumentVerification->city =$city;
                    $createDocumentVerification->country = $country;
                    $createDocumentVerification->updated_at = $updated_at;
                    $createDocumentVerification->verification_type = 'identity';
                    $createDocumentVerification->identity_type     = request('identity_type');
                    $createDocumentVerification->identity_number   = request('identity_number');
                    $createDocumentVerification->status            = 'pending';
                    $createDocumentVerification->local_tran_time   = $request->local_tran_time;
                    $createDocumentVerification->save();
                }
                else
                {
                    $documentVerification->user_id = request('user_id');
                    if (!empty(request('id_proof')))
                    {
                        $documentVerification->file_id = $fileId;
                    }
                    if (!empty(request('id_proof_back')))
                    {
                        $documentVerification->file_back_id = $fileId_back;
                    }
                    $documentVerification->app_ver = $app_ver;
                    $documentVerification->device_name = $device_name;
                    $documentVerification->device_manufacture =$device_manufacture;
                    $documentVerification->device_model = $device_model;
                    $documentVerification->os_ver = $os_ver;
                    $documentVerification->device_os =$device_os;
                    $documentVerification->ip_address =$ip_address;
                    $documentVerification->city =$city;
                    $documentVerification->country = $country;
                    $documentVerification->updated_at = $updated_at;
                    $documentVerification->verification_type = 'identity';
                    $documentVerification->identity_type     = request('identity_type');
                    $documentVerification->identity_number   = request('identity_number');
                    $documentVerification->status            = 'pending';
                    $documentVerification->local_tran_time   = $request->local_tran_time;
                    $documentVerification->save();
                }
                $adminAllowed = Notification::has_permission([1]);
                foreach($adminAllowed as $admins){
                    $name = User::where('id', $user_id)->first();
                    Notification::insert([
                        'user_id'               => $user_id,
                        'notification_to'       => 1,
                        'notification_type_id'  => 12,
                        'notification_type'     => 'App',
                        'description'           => "User ".$name->first_name." has uploaded ID Verification Document",
                        'url_to_go'             => 'admin/users/kyc-verications/'.request('user_id'),
                        'local_tran_time'       => $request->local_tran_time
                    ]);
                }
                $mailresponse = $this->helper->sendTransactionNotificationToAdmin('user_verification', ['data' => $documentVerification]);
                $success['message'] = "Kyc document is uploaded successfully.";
                return response()->json(['status'=>$this->successStatus, 'success' => $success], $this->successStatus);
            }
          
            if($documenttype == 'address_proof') {
                $user  = User::find($user_id);
                $user->address_verified = false;
                $user->kyc_submitted_on = $updated_at;
                $user->save();
    
                $param = "address_proof";
                $path       = 'uploads/user-documents/address-proof-files';
                $fileId = $this->insertUserIdentityInfoToFilesTable(request('address_proof'),$param, $path);
    
                $documentVerification = DocumentVerification::where(['user_id' => $user_id, 'verification_type' => 'address'])->first();
                if (empty($documentVerification))
                {
                    $createDocumentVerification          = new DocumentVerification();
                    $createDocumentVerification->user_id = request('user_id');
                    if (!empty(request('address_proof')))
                    {
                        $createDocumentVerification->file_id = $fileId;
                    }
                    $createDocumentVerification->app_ver = $app_ver;
                    $createDocumentVerification->device_name = $device_name;
                    $createDocumentVerification->device_manufacture =$device_manufacture;
                    $createDocumentVerification->device_model = $device_model;
                    $createDocumentVerification->os_ver = $os_ver;
                    $createDocumentVerification->device_os =$device_os;
                    $createDocumentVerification->ip_address =$ip_address;
                    $createDocumentVerification->city =$city;
                    $createDocumentVerification->country = $country;
                    $createDocumentVerification->updated_at = $updated_at;
                    $createDocumentVerification->verification_type = 'address';
                    $createDocumentVerification->identity_type     = request('identity_type');
                    $createDocumentVerification->identity_number   = request('identity_number');
                    $createDocumentVerification->status            = 'pending';
                    $createDocumentVerification->local_tran_time   = $request->local_tran_time;
                    $createDocumentVerification->save();
                }
                else
                {
                    $documentVerification->user_id = request('user_id');
                    if (!empty(request('address_proof')))
                    {
                        $documentVerification->file_id = $fileId;
                    }
                    $documentVerification->app_ver = $app_ver;
                    $documentVerification->device_name = $device_name;
                    $documentVerification->device_manufacture =$device_manufacture;
                    $documentVerification->device_model = $device_model;
                    $documentVerification->os_ver = $os_ver;
                    $documentVerification->device_os =$device_os;
                    $documentVerification->ip_address =$ip_address;
                    $documentVerification->city =$city;
                    $documentVerification->country = $country;
                    $documentVerification->updated_at = $updated_at;
                    $documentVerification->verification_type = 'address';
                    $documentVerification->identity_type     = request('identity_type');
                    $documentVerification->identity_number   = request('identity_number');
                    $documentVerification->status            = 'pending';
                    $documentVerification->local_tran_time   = $request->local_tran_time;
                    $documentVerification->save();
                }
                $adminAllowed = Notification::has_permission([1]);
                foreach($adminAllowed as $admins){
                    $name = User::where('id', $user_id)->first();
                    Notification::insert([
                        'user_id'               => $user_id,
                        'notification_to'       => 1,
                        'notification_type_id'  => 12,
                        'notification_type'     => 'App',
                        'description'           => "User ".$name->first_name." has uploaded Address Verification Document",
                        'url_to_go'             => 'admin/users/kyc-verications/'.request('user_id'),
                        'local_tran_time'       => $request->local_tran_time
                    ]);
                }
                $mailresponse = $this->helper->sendTransactionNotificationToAdmin('user_verification', ['data' => $documentVerification]);
                $success['message'] = "Kyc document is uploaded successfully.";
                return response()->json(['status'=>$this->successStatus, 'success' => $success], $this->successStatus);
            }
            
            if($documenttype == 'video') {
                $user  = User::find($user_id);
                $user->video_verified = false;
                $user->kyc_submitted_on = $updated_at;
                $user->kyc_status = '2';
                $user->save();
    
                $param = "video";
                $path       = 'uploads/user-documents/video-proof-files';
                $fileId = $this->insertUserIdentityInfoToFilesTable(request('video'),$param, $path);
                $documentVerification = DocumentVerification::where(['user_id' => $user_id, 'verification_type' => 'video'])->first();
                
                if (empty($documentVerification))
                {
                    $createDocumentVerification          = new DocumentVerification();
                    $createDocumentVerification->user_id = request('user_id');
                    if (!empty(request('video')))
                    {
                        $createDocumentVerification->file_id = $fileId;
                    }
                    $createDocumentVerification->app_ver = $app_ver;
                    $createDocumentVerification->device_name = $device_name;
                    $createDocumentVerification->device_manufacture = $device_manufacture;
                    $createDocumentVerification->device_model = $device_model;
                    $createDocumentVerification->os_ver = $os_ver;
                    $createDocumentVerification->device_os = $device_os;
                    $createDocumentVerification->ip_address = $ip_address;
                    $createDocumentVerification->city = $city;
                    $createDocumentVerification->country = $country;
                    $createDocumentVerification->updated_at = $updated_at;
                    $createDocumentVerification->verification_type = 'video';
                    $createDocumentVerification->identity_type     = 'video';
                    $createDocumentVerification->identity_number   =  '';
                    $createDocumentVerification->status            = 'pending';
                    $createDocumentVerification->local_tran_time   = $request->local_tran_time;
                    $createDocumentVerification->save();
                }
                else
                {
                    $documentVerification->user_id = request('user_id');
                    if (!empty(request('video')))
                    {
                        $documentVerification->file_id = $fileId;
                    }
                    $documentVerification->app_ver = $app_ver;
                    $documentVerification->device_name = $device_name;
                    $documentVerification->device_manufacture =$device_manufacture;
                    $documentVerification->device_model = $device_model;
                    $documentVerification->os_ver = $os_ver;
                    $documentVerification->device_os =$device_os;
                    $documentVerification->ip_address =$ip_address;
                    $documentVerification->city =$city;
                    $documentVerification->country = $country;
                    $documentVerification->updated_at = $updated_at;
                    $documentVerification->verification_type = 'video';
                    $documentVerification->identity_type     = 'video';
                    $documentVerification->identity_number   = '';
                    $documentVerification->status            = 'pending';
                    $documentVerification->local_tran_time   = $request->local_tran_time;
                    $documentVerification->save();
                }
                
                $userdevice = DB::table('devices')->where('user_id', request('user_id'))->first();
                if(!empty($userdevice)){
                    $template = NotificationTemplate::where('temp_id', '23')->where('language_id', $userdevice->language)->first();
                    $subject = $template->title;
                    $subheader = $template->subheader;
                    $message = $template->content;
                    
                    $currency = '9';
                    $type = 'kyc';
                    $date    = date("Y-m-d h:i:s");
                    $this->helper->sendFirabasePush($subject, $message, request('user_id'), $currency, $type);
                    
                    Noticeboard::create([
                        'tr_id' => null,
                        'title' => $subject,
                        'content' => $message,
                        'type' => 'push',
                        'content_type' => 'kyc',
                        'user' => request('user_id'),
                        'sub_header' => $subheader,
                        'push_date' => $request->local_tran_time,
                        'template' => '23',
                        'language' => $userdevice->language,
                    ]);
                }
                
                // Cards Code Start
                
                $cardUser = User::find($user_id);
                $cardUserDetail = UserDetail::where('user_id', $user_id)->first();
                $cardUserCountry = Country::where('id', $cardUserDetail->country)->first();
        
                $url = env('BASE_URL').'create-user';
                
                $headers = [
                    'Host: cards.lubypay.com',
                    'Content-Type: application/json',
                ];
                
                $payloads = [
                    'firstname' => $cardUser->first_name,
                    'lastname' => $cardUser->last_name,
                    'dial_code' => str_replace('+', '', $cardUser->carrierCode),
                    'mobile' => $cardUser->phone,
                    'email' => $cardUser->email,
                    'platform' => 'Sandbox Ewallet',
                    'country_name' => $cardUserCountry->name,
                    'state' => $cardUserDetail->state,
                    'city' => $cardUserDetail->city,
                    'zip' => $cardUserDetail->zip_code,
                    'address' => $cardUserDetail->address_1,
                ];
                
                $userDetails = $this->postFunction($url, $headers, $payloads);
                
                // Cards Code End
                
                $adminAllowed = Notification::has_permission([1]);
                foreach($adminAllowed as $admins){
                    $name = User::where('id', request('user_id'))->first();
                    Notification::insert([
                        'user_id'               => request('user_id'),
                        'notification_to'       => 1,
                        'notification_type_id'  => 12,
                        'notification_type'     => 'App',
                        'description'           => "User ".$name->first_name." has uploaded Verification Video ",
                        'url_to_go'             => 'admin/users/kyc-verications/'.request('user_id'),
                        'local_tran_time'       => $request->local_tran_time
                    ]);
                }
                $mailresponse = $this->helper->sendTransactionNotificationToAdmin('user_verification', ['data' => $documentVerification]);
                $success['message'] = "Kyc document is uploaded successfully.";
                return response()->json(['status'=>$this->successStatus, 'success' => $success], $this->successStatus);
            }
        }
        catch (\Exception $e)
        {
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage().$e->getLine(); // echo print_r($e->getMessage());
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }
    
    protected function insertUserIdentityInfoToFilesTable($identity_file, $param, $path)
    {
        if (!empty($identity_file))
        {
            $request = app(\Illuminate\Http\Request::class);
            if ($request->hasFile($param))
            {
                $fileName     = $request->file($param);
                $originalName = $fileName->getClientOriginalName();
                $uniqueName   = strtolower(time(). rand(10,100) . '.' . $fileName->getClientOriginalExtension());
                $file_extn    = strtolower($fileName->getClientOriginalExtension());

                if ($file_extn == 'docx' || $file_extn == 'rtf' || $file_extn == 'doc' || $file_extn == 'pdf' || $file_extn == 'png' || $file_extn == 'jpg' || $file_extn == 'jpeg' || $file_extn == 'csv' || $file_extn == 'txt' || $file_extn == 'gif' || $file_extn == 'bmp' || $file_extn == 'mp4')
                {
                    $uploadPath = public_path($path); //problem
                    $fileName->move($uploadPath, $uniqueName);

                    if (isset($request->existingIdentityFileID))
                    {
                        $checkExistingFile               = File::where(['id' => $request->existingIdentityFileID])->first();
                        $checkExistingFile->filename     = $uniqueName;
                        $checkExistingFile->originalname = $originalName;
                        $checkExistingFile->save();
                        return $checkExistingFile->id;
                    }
                    else
                    {
                        $file               = new File();
                        $file->user_id      = $request->user_id;
                        $file->filename     = $uniqueName;
                        $file->originalname = $originalName;
                        $file->type         = $file_extn;
                        $file->save();
                        return $file->id;
                    }
                }
                else
                {
                    $this->helper->one_time_message('error', __('Invalid File Format!'));
                }
            }
        }
    }
    //Personal Identity Verification - end
    //uploadKycDocumentReact
    public function uploadKycDocumentReact(Request $request)
    {
        // dd(request()->all());
        try
        {
          // \DB::beginTransaction();
        $user_id = request('user_id');
        $identitytype = "";
        $identitynumber = "";
        $documenttype = request('document_type');
        
        if(request('photo')) {
            //echo request('photo'); die;
            $user  = User::find($user_id);
            //dd($user);
            $user->identity_verified = false;
            $user->save();
            $param = "photo";
            $path       = public_path('uploads/user-documents/photo-proof-files');
            $fileId = $this->createImageFromBase64(request('photo'),$param, $path);
            //echo $fileId; die;
            $documentVerification = DocumentVerification::where(['user_id' => $user_id, 'verification_type' => 'photo'])->first();
            if (empty($documentVerification))
            {
                $createDocumentVerification          = new DocumentVerification();
                $createDocumentVerification->user_id = request('user_id');
                if (!empty(request('photo')))
                {
                    $createDocumentVerification->file_id = $fileId;
                }
                $createDocumentVerification->verification_type = 'photo';
                $createDocumentVerification->identity_type     = 'photo';
                $createDocumentVerification->identity_number   =  '';
                $createDocumentVerification->status            = 'pending';
                $createDocumentVerification->save();
            }
            else
            {
                $documentVerification->user_id = request('user_id');
                if (!empty(request('photo')))
                {
                    $documentVerification->file_id = $fileId;
                }
                $documentVerification->verification_type = 'photo';
                $documentVerification->identity_type     = 'photo';
                $documentVerification->identity_number   = '';
                $documentVerification->status            = 'pending';
                $documentVerification->save();
            }
          }
          if(request('id_proof')) {
            $user  = User::find($user_id);
            $user->identity_verified = false;
            $user->save();
            // $this->validate($request, [
            //     'identity_type'   => 'required',
            //     'identity_number' => 'required|numeric',
            //     'attachment'      => 'mimes:docx,rtf,doc,pdf,png,jpg,jpeg,csv,txt,gif,bmp|max:10000',
            // ]);
            $param = "id_proof";
            $path       = public_path('uploads/user-documents/identity-proof-files');
            $fileId = $this->createImageFromBase64(request('id_proof'),$param, $path);
            $fileId_Back = $this->createImageFromBase64(request('id_proof_back'),$param, $path);
            //echo $fileId; die;
            $documentVerification = DocumentVerification::where(['user_id' => $user_id, 'verification_type' => 'identity'])->first();
            if (empty($documentVerification))
            {
                //for front side of image
                $createDocumentVerification          = new DocumentVerification();
                $createDocumentVerification->user_id = request('user_id');
                if (!empty(request('id_proof')))
                {
                    $createDocumentVerification->file_id = $fileId;
                }
                $createDocumentVerification->verification_type = 'identity';
                $createDocumentVerification->identity_type     = request('identity_type');
                $createDocumentVerification->identity_number   = request('identity_number');
                $createDocumentVerification->status            = 'pending';
                $createDocumentVerification->save();
                //for back side of image
                $ins = DocumentVerification::create([
                    'user_id' => request('user_id'),
                    'file_id' => $fileId_Back,
                    'verification_type' => 'identity',
                    'identity_type' => request('identity_type'),
                    'identity_number' => request('identity_number'),
                    'status' => 'pending'
                    ]);
                // $createDocumentVerification_back          = new DocumentVerification();
                // $createDocumentVerification_back->user_id = request('user_id');
                // if (!empty(request('id_proof_back')))
                // {
                //     $createDocumentVerification_back->file_id = $fileId_Back;
                // }
                // $createDocumentVerification_back->verification_type = 'identity';
                // $createDocumentVerification_back->identity_type     = request('identity_type');
                // $createDocumentVerification_back->identity_number   = request('identity_number');
                // $createDocumentVerification_back->status            = 'pending';
                // $createDocumentVerification_back->save();
            }
            else
            {
                $documentVerification->user_id = request('user_id');
                if (!empty(request('id_proof')))
                {
                    $documentVerification->file_id = $fileId;
                }
                $documentVerification->verification_type = 'identity';
                $documentVerification->identity_type     = request('identity_type');
                $documentVerification->identity_number   = request('identity_number');
                $documentVerification->status            = 'pending';
                $documentVerification->save();
            }
          }
          if(request('address_proof')) {
            $user  = User::find($user_id);
            $user->identity_verified = false;
            $user->save();

            // $this->validate($request, [
            //     'identity_type'   => 'required',
            //     'identity_number' => 'required|numeric',
            //     'attachment'      => 'mimes:docx,rtf,doc,pdf,png,jpg,jpeg,csv,txt,gif,bmp|max:10000',
            // ]);
            $param = "address_proof";
            $path       = public_path('uploads/user-documents/address-proof-files');
            $fileId = $this->createImageFromBase64(request('address_proof'),$param, $path);
            $fileId_back = $this->createImageFromBase64(request('address_proof_back'),$param, $path);
            //echo $fileId; die;
            $documentVerification = DocumentVerification::where(['user_id' => $user_id, 'verification_type' => 'address'])->first();
            if (empty($documentVerification))
            {
                $createDocumentVerification          = new DocumentVerification();
                $createDocumentVerification->user_id = request('user_id');
                if (!empty(request('address_proof')))
                {
                    $createDocumentVerification->file_id = $fileId;
                }
                $createDocumentVerification->verification_type = 'address';
                $createDocumentVerification->identity_type     = request('identity_type');
                $createDocumentVerification->identity_number   = request('identity_number');
                $createDocumentVerification->status            = 'pending';
                $createDocumentVerification->save();
                // For kyc uploading(Back)
                $ins = DocumentVerification::create([
                    'user_id'           => request('user_id'),
                    'file_id'           => $fileId_Back,
                    'verification_type' => 'address',
                    'identity_type'     => request('identity_type'),
                    'identity_number'   => request('identity_number'),
                    'status' => 'pending'
                    ]);
                // $createDocumentVerification_back          = new DocumentVerification();
                // $createDocumentVerification_back->user_id = request('user_id');
                // if (!empty(request('address_proof_back')))
                // {
                //     $createDocumentVerification_back->file_id = $fileId_back;
                // }
                // $createDocumentVerification_back->verification_type = 'address';
                // $createDocumentVerification_back->identity_type     = request('identity_type');
                // $createDocumentVerification_back->identity_number   = request('identity_number');
                // $createDocumentVerification_back->status            = 'pending';
                // $createDocumentVerification_back->save();
            }
            else
            {
                $documentVerification->user_id = request('user_id');
                if (!empty(request('address_proof')))
                {
                    $documentVerification->file_id = $fileId;
                }
                $documentVerification->verification_type = 'address';
                $documentVerification->identity_type     = request('identity_type');
                $documentVerification->identity_number   = request('identity_number');
                $documentVerification->status            = 'pending';
                $documentVerification->save();
            }
          }
          $success['message'] = "Kyc document is uploaded successfully.";
          return response()->json(['status'=>$this->successStatus, 'success' => $success], $this->successStatus);
        }
        catch (\Exception $e)
        {
            //echo $e->getMessage(); die;
            //\DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage(); // echo print_r($e->getMessage());
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
        
    }
    
     // Create Image From Base64
    public function createImageFromBase64($image,$param,$imagedir) {
        if(isset($image) && $image && isset($imagedir) && $imagedir) {
            $upload_dir = $imagedir;
            $img =$image;
            // $extype = explode(';base64,',$img);
            // $extypea = explode('/',$extype[0]);
            // $type = '.'.$extypea[1];
            // if($type=='.png') {
            //     $img = str_replace('data:image/png;base64,', '', $img);
            // } elseif($type=='.jpeg' || $type=='.jpg') {
            //     $img = str_replace('data:image/jpeg;base64,', '', $img);
            // } else {
            //     $img = str_replace('data:image/*;charset=utf-8;base64,', '', $img);
            //     $type = '.pdf';
            // }
            $type= ".jpg";
            //$img = str_replace(' ', '+', $img);
            $datas = base64_decode($img);
            // echo $datas;
            // die;
            $fileName = strtolower(time() . $type);
            $file = $upload_dir .'/'. $fileName;
            $success = file_put_contents($file, $datas);
            $file               = new File();
            $file->user_id      = request('user_id');
            $file->filename     = $fileName;
            $file->originalname = $fileName;
            $file->type         = $type;
            $file->save();
            return $file->id;
        } else {
            return "";
        }
    }
    
    // Create Profile Image From Base64
    public function createProfileImageFromBase64($image,$imagedir) {
        if(isset($image) && $image && isset($imagedir) && $imagedir) {
            $upload_dir = $imagedir;
            $img =$image;
            $type= ".jpg";
            //$img = str_replace(' ', '+', $img);
            $datas = base64_decode($img);
            $fileName = strtolower(time() . $type);
            $file = $upload_dir .'/'. $fileName;
            $success = file_put_contents($file, $datas);
            return $fileName;
        } else {
            return "";
        }
    }

     // Create Image From Base64
    public function testcreateImageFromBase64(Request $request) {
       $imagedir       = public_path('public/uploads/test-image');
       $image = $request->image;
        if(isset($image)) {
            $upload_dir = $imagedir;
            $img =$image;
           
            $type= ".jpg";
            $img = str_replace('data:image/png;base64,', '', $img);
           // $img = str_replace('data:image/*;charset=utf-8;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $datas = base64_decode($img);
            $fileName = strtolower(time() . $type);
            $file = $upload_dir .'/'. $fileName;
            $success = file_put_contents($file, $datas);
            // pagosdelbienestartech/public_html/test
            $img = url("uploads/test-image/").$fileName;
            echo "check this URL : ".$img;
        } else {
            return "";
        }
        
        // die;
        //  $img = str_replace('data:image/png;base64,', '', $img);
        //     $img = str_replace(' ', '+', $img);
        //     $data1 = base64_decode($img);
        //     $Iname = uniqid();
        //     $file = public_path('/images/upload/') . $Iname . ".png";
        //     $success = file_put_contents($file, $data1);
    }
    
    public function vcard_status(Request $request)
    { 
            $user_id = $request->input('user_id');
            
            $data = array(
                'vcard'=> 1,
            );
            
            $rs = User::where('id', $user_id)->update($data);
            if($rs){
                return response()->json([
                    'message'=>'Card updated successfully.',
                    'code'=>200,
                    'status'=>'success'
                ]);
            }else{
                return response()->json([
                    'message'=>"something went wrong contact with administrator.",
                    'status'=>'error'
                ]);
            }
       
    }
    
    public function kycstatusstore(Request $request)
    {  
        $user_id = $request->user_id;
        $proof_id = $request->proof_id;
        $status = $request->status;

        $check_user = Kycdatastore::where('user_id',$user_id)->first();

        if(!empty($check_user)){
            $data = [
                'proof_id' => $request->input('proof_id'),
                'status' => $request->input('status') 
            ];
           
            $rs = Kycdatastore::where(['user_id'=> $user_id])->update($data);
        }else{
            $rs = Kycdatastore::create([
                'user_id' => $request->input('user_id'),
                'proof_id' => $request->input('proof_id'),
                'status' => $request->input('status')
            ]); 
        }
        
        User::where('id', $request->input('user_id'))->update(['kyc_submitted_on' => Carbon::now()->toDateTimeString(), 'kyc_status' => '1']);
        
        if($rs){
            $userdevice = DB::table('devices')->where('user_id', $user_id)->first();
            $template = NotificationTemplate::where('temp_id', '24')->where('language_id', $userdevice->language)->first();
            $subject = $template->title;
            $subheader = $template->subheader;
            $message = $template->content;
            
            $currency = '9';
            $type = 'kyc';
            $date    = date("Y-m-d h:i:s");
            $this->helper->sendFirabasePush($subject, $message, $user_id, $currency, $type);
            
            Noticeboard::create([
                'tr_id' => null,
                'title' => $subject,
                'content' => $message,
                'type' => 'push',
                'content_type' => 'kyc',
                'user' => $user_id,
                'sub_header' => $subheader,
                'push_date' => $request->local_tran_time,
                'template' => '24',
                'language' => $userdevice->language,
            ]);
            
            return response()->json([
                'message'=>'KYC Data stored successfully.',
                'code'=>200,
                'status'=>'success'
            ]);
        }else{
            return response()->json([
                'message'=>"Data already exists.",
                'code'=>200,
                'status'=>'success'
            ]);
        }
    }
    
    // public function getKycStatusManualPersona()
    // { 
    //     try
    //     {
    //         \DB::beginTransaction();
            
    //         $userData_persona = Kycdatastore::where('user_id',request('user_id'))->first();
            
    //         $userData = DocumentVerification::where('user_id',request('user_id'))->groupBy('verification_type')->orderBy('id','DESC')->get();
    //         if(!empty($userData_persona)) {
    //             $success['status'] = 'Not Uploaded';
    //             if(!empty($userData_persona)){
    //                 if($userData_persona->status == 'completed'){
    //                     $success['persona_status'] = 'completed';
    //                 }else{
    //                     $success['persona_status'] = 'Not Uploaded';
    //                 }
    //             }else{
    //                 $success['persona_status'] = 'N/A';
    //             }
    //         } else {
    //             if($userData->isEmpty()) {
    //                 $success['status'] = 'Not Uploaded';
    //                 if(!empty($userData_persona)){
    //                     if($userData_persona->status == 'completed'){
    //                         $success['persona_status'] = 'completed';
    //                     }else{
    //                         $success['persona_status'] = 'Not Uploaded';
    //                     }
    //                 }else{
    //                     $success['persona_status'] = 'N/A';
    //                 }
    //             } else {
    //                 $i = 0; foreach($userData as $userData1) {
    //                     $file = File::where('id',$userData1->file_id)->first();
    //                     if($file) {
    //                         $userData[$i]->file_url = URL::to('/').'/public/uploads/user-documents/identity-proof-files/'.$file->filename;
    //                         $userData[$i]->file_name = $file->originalname;
    //                     }
    //                     $userDataStatus[] = $userData1->status;
    //                     $i++; 
    //                 }
              
    //                 if(in_array('rejected',$userDataStatus)) {
    //                     $success['status'] = 'rejected';
    //                     if(!empty($userData_persona)){
    //                         if($userData_persona->status == 'completed'){
    //                             $success['persona_status'] = 'completed';
    //                         }else{
    //                             $success['persona_status'] = 'rejected';
    //                         }
    //                     }else{
    //                         $success['persona_status'] = 'N/A';
    //                     }
    //                 } else if(in_array('pending',$userDataStatus)) {
    //                     $success['status'] = 'pending';
    //                     if(!empty($userData_persona)){
    //                         if($userData_persona->status == 'completed'){
    //                             $success['persona_status'] = 'completed';
    //                         }else{
    //                             $success['persona_status'] = 'pending';
    //                         }
    //                     }else{
    //                         $success['persona_status'] = 'N/A';
    //                     }
    //                 } else if(in_array('approved',$userDataStatus)) {
    //                     $success['status'] = 'completed';
    //                     if(!empty($userData_persona)){
    //                         if($userData_persona->status == 'completed'){
    //                             $success['persona_status'] = 'completed';
    //                         }else{
    //                             $success['persona_status'] = 'completed';
    //                         }
    //                     }else{
    //                         $success['persona_status'] = 'N/A';
    //                     }
    //                 }
    //             }
    //         }
    //         $success['data'] = $userData;
    //         return response()->json(['status'=>$this->successStatus, 'success' => $success], $this->successStatus);
    //     }
    //     catch (\Exception $e)
    //     {
    //         \DB::rollBack();
    //         $success['status']  = $this->unauthorisedStatus;
    //         $success['message'] = $e->getMessage(); // echo print_r($e->getMessage());
    //         return response()->json(['success' => $success], $this->unauthorisedStatus);
    //     }
    // }
    
    public function getKycStatusManualPersona()
    { 
        try
        {
            \DB::beginTransaction();
            
            $check_kyc = User::where('id', request('user_id'))->first();
            $userData_persona = Kycdatastore::where('user_id',request('user_id'))->first();
            if(!empty($userData_persona)){
                $kyc_type = 'Auto KYC';
                $kycData ='N/A';
            }else{
                $kyc_type = 'Manual KYC';
                $kycData = DocumentVerification::where('user_id',request('user_id'))->groupBy('verification_type')->orderBy('id','DESC')->get();
            }
            
            $success['status'] = $check_kyc->kyc_status;
            $success['type'] = $kyc_type;
            $success['data'] = $kycData;
            
            return response()->json(['status'=>$this->successStatus, 'success' => $success], $this->successStatus);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage(); // echo print_r($e->getMessage());
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }
}
