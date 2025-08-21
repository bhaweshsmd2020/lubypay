<?php

namespace App\Http\Controllers\Api;
require_once(base_path('/firebase/vendor/autoload.php'));
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Models\Country;
use App\Models\EmailTemplate;
use App\Models\QrCode;
use App\Models\RequestPayment;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\Language;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\VerifyUser;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\AppPage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class WebhookController extends Controller
{
    public $successStatus      = 200;
    public $emptyStatus        =201;
    public $unauthorisedStatus = 401;
    public $email;
    protected $user;

    public function __construct()
    {
        $this->email = new EmailController();
        $this->user  = new User();
    }

    public function getMerchantUserRoleExistence()
    {
        $data['checkMerchantRole'] = $checkMerchantRole = Role::where(['user_type' => 'User', 'customer_type' => 'merchant', 'is_default' => 'Yes'])->first(['id']);
        $data['checkUserRole']     = $checkUserRole     = Role::where(['user_type' => 'User', 'customer_type' => 'user', 'is_default' => 'Yes'])->first(['id']);

        return response()->json([
            'status'            => $this->successStatus,
            'checkMerchantRole' => $checkMerchantRole,
            'checkUserRole'     => $checkUserRole,
        ]);
    }

    public function duplicateEmailCheckApi(Request $request)
    {
        $email = User::where(['email' => $request->email])->exists();
        if ($email)
        {
            $data['status'] = true;
            $data['fail']   = 'The email has already been taken!';
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "Email Available!";
        }
        return json_encode($data);
    }

    public function duplicatePhoneNumberCheckApi(Request $request)
    {
        $req_id = $request->id;
        if (isset($req_id))
        {
            // dd('with id');
            $phone = User::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone)])->where(function ($query) use ($req_id)
            {
                $query->where('id', '!=', $req_id);
            })->exists();
        }
        else
        {
            // dd('no id');
            $phone = User::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone)])->orWhere('formattedPhone',$request->phone)->exists();
        }
        // dd($phone);

        if ($phone)
        {
            $data['status'] = true;
            $data['fail']   = "The phone number has already been taken!";
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "The phone number is Available!";
        }
        return json_encode($data);
    }
    
    public function changePasscode(Request $request) {
       // dd($request->all());
        $device_id = $request->device_id;
        $user_id   = $request->user_id;
        $passcode  = $request->passcode;
        
        $check = DB::table('devices')->where(['device_id'=>$device_id,'passcode_status'=>1])->where('user_id',$user_id)->first();
        if(empty($check))
        {
            $success['status']  = false;
            $success['message'] = "Please set passcode first!";
            return response()->json(['success' => $success], $this->successStatus);
        }else
        {
            $dataArray =  array('passcode' => $passcode);
            DB::table('devices')->where('device_id',$device_id)->where('user_id',$user_id)->update($dataArray);
            $success['status']  = $this->successStatus;
            $success['message'] = "Passcode changed Successfully!";
            return response()->json(['success' => $success], $this->successStatus);
        }
        
    }
    public function forgetPasscode(Request $request) {
        // dd($request->all());
        $phone = $request->phone;
        $device_id = $request->device_id;
        $passcode  = $request->passcode;
        
        $check = DB::table('users')->where('phone', $phone)->first();
        
        if($check){
        
            $check_user = DB::table('devices')->where('user_id', $check->id)->where('device_id', $device_id)->first();
            
            if($check_user){
                
                if(($device_id == '')||($passcode == ''))
                {
                    $success['status']  = false;
                    $success['message'] = "All fields are required!";
                    return response()->json(['success' => $success], $this->successStatus);
                }else
                {
                $check = DB::table('devices')->where(['device_id'=>$device_id,'passcode_status'=>1])->first();
                if(empty($check))
                {
                    $success['status']  = false;
                    $success['message'] = "Please set passcode first!";
                    return response()->json(['success' => $success], $this->successStatus);
                }else
                {
                    $dataArray =  array('passcode' => $passcode);
                    DB::table('devices')->where('device_id',$device_id)->update($dataArray);
                    $success['status']  = $this->successStatus;
                    $success['message'] = "Passcode Forget Successfully!";
                    return response()->json(['success' => $success], $this->successStatus);
                 }
                }
            }else{
                
                $success['status']  = $this->emptyStatus;
                $success['message'] = "Device not exists!!";
                return response()->json(['success' => $success], $this->emptyStatus);
               
            }
        }else{
            return response()->json(['success' => 'Phone Number not exists!']);
        }
        
    }
    
     public function registration(Request $request)
    {
        $rules = array(
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required',
            // 'firebase_token'   => 'required',
        );

        $fieldNames = array(
            'first_name' => 'First Name',
            'last_name'  => 'Last Name',
            'middle_name'  => 'Middle Name',
            'second_last_name'  => 'Second Last Name',
            'phone'      => 'Phone',
            'email'      => 'Email',
            'password'   => 'Password',
            
        );
        
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);
        
        if ($validator->fails())
        {     
            //dd($validator->messages());
            
            $response['message'] = "All fields are required.";
            $response['status']  = $this->unauthorisedStatus;
            return response()->json(['success' => $response], $this->successStatus);
        }
        else
        {
            //default_currency
            $default_currency = Setting::where('name', 'default_currency')->first(['value']);

            try
            {
                
                \DB::beginTransaction();

                //Create user
                $user = $this->user->createNewUser($request, 'user');
                
                //Assign user type and role to new user
                RoleUser::insert(['user_id' => $user->id, 'role_id' => $user->role_id, 'user_type' => 'User']);

                // Create user detail
                $this->user->createUserDetail($request,$user->id);

                // Create user's default wallet
                $this->user->createUserDefaultWallet($user->id, $default_currency->value);

                //Entry for User's QrCode Generation - starts
                $this->saveUserQrCodeApi($user);
                //Entry for User's QrCode Generation - ends
                // Create user's crypto wallet/wallets address
                $this->user->generateUserCryptoWalletAddress($user);
// echo("hello"); die;
                // Create user's crypto wallet/wallets address
                // $generateUserCryptoWalletAddress = $this->user->generateUserCryptoWalletAddress($user);
                // dd($generateUserCryptoWalletAddress);
                // if ($generateUserCryptoWalletAddress['status'] == 401)
                // {
                    
                //     \DB::rollBack();
                //     $success['status']  = $this->successStatus;
                //     $success['reason']  = 'create-wallet-address-failed';
                //     $success['message'] = $generateUserCryptoWalletAddress['message'];
                //     return response()->json(['success' => $success], $this->successStatus);
                // }
// echo("hello"); die;
                $userEmail          = $user->email;
                $userFormattedPhone = $user->formattedPhone;

                // Process Registered User Transfers
                $this->user->processUnregisteredUserTransfers($userEmail, $userFormattedPhone, $user, $default_currency->value);

                // Process Registered User Request Payments
                $this->user->processUnregisteredUserRequestPayments($userEmail, $userFormattedPhone, $user, $default_currency->value);

                // Email verification
                if (!$user->user_detail->email_verification)
                {
                    if (checkVerificationMailStatus() == "Enabled")
                    {
                        if (checkAppMailEnvironment())
                        {
                            $emainVerificationArr = $this->user->processUserEmailVerification($user);

                            try
                            {
                                $this->email->sendEmail($emainVerificationArr['email'], $emainVerificationArr['subject'], $emainVerificationArr['message']);

                               \DB::commit();
                                $success['status']  = $this->successStatus;
                                $success['kyc_status']  = 0;
                                $success['reason']  = 'email_verification';
                                $success['message'] = 'We sent you an activation code. Check your email and click on the link to verify.';
                                return response()->json(['success' => $success], $this->successStatus);
                            }
                            catch (\Exception $e)
                            {
                                \DB::rollBack();
                                $success['status']  = $this->unauthorisedStatus;
                                $success['message'] = $e->getMessage();
                                return response()->json(['success' => $success], $this->unauthorisedStatus);
                            }
                        }
                    }
                }
                //
                
                $firebase_token = $request->firebase_token;
        
                $factory = (new Factory)->withServiceAccount('/home/ticktappay/public_html/test/firebase/firebase-adminsdk.json');
                $database = $factory->createDatabase();
                $auth = $factory->createAuth();
                 
                // $idTokenString = $firebase_token;
                // $verifiedIdToken = $auth->verifyIdToken($idTokenString);
                // $uid = $verifiedIdToken->claims()->get('sub');
                // $user_uid = $auth->getUser($uid);
                // //dd($user_uid->uid);
                
                // $update_uid = User::where('id', $user->id)->update(['uid' => $user_uid->uid]);
                
                \DB::commit();
                $success['status']  = $this->successStatus;
                $success['kyc_status']  = 0;
                $success['message'] = "Registration Successfull!";
                return response()->json(['success' => $success], $this->successStatus);
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $success['status']  = $this->unauthorisedStatus;
                $success['message'] = $e->getMessage().$e->getLine();
                return response()->json(['success' => $success], $this->unauthorisedStatus);
            }
        }
    }
   public function test_device(Request $request) {
        
        
        
        $userid = $request->user_id;
        $device_id = $request->device_id;
        $fcm_token = $request->fcm_token;
        
        $dataArray =  array(
          'user_id' => $userid,
          'device_id' => $device_id,
          'fcm_token' => $fcm_token,
          'passcode' => NULL,
          'passcode_status'=>0,
          'touch_status'=>0
        );

            $exists = DB::table('devices')->where('device_id', $device_id)->first();
            if($exists){
                $devicecheck=DB::table('devices')->where('user_id', $userid)->first();
                if($devicecheck){
                    DB::table('devices')->where('user_id', $userid)->where('device_id', $device_id)->update(['fcm_token' => $fcm_token]);
                }
                else{
                    DB::table('devices')->where('user_id', $userid)->where('device_id', '!=', $device_id)->update(['fcm_token' => $fcm_token,'device_id'=> $device_id,'passcode' => NULL,'passcode_status'=>0,'touch_status'=>0]);
                }
            }

           
            else
            {
             DB::table('devices')->insert($dataArray);   
                
            }


        $success['status']  = $this->successStatus;
        $success['message'] = "Device Registered Successfully!";
        return response()->json(['success' => $success], $this->successStatus);
    }
    // public function registerDevices1(Request $request)
    // {
    //     $userid     = $request->user_id;
    //     $device_id  = $request->device_id;
    //     $fcm_token  = $request->fcm_token;
        
    //     $dataArray =  array(
    //       'user_id'         => $userid,
    //       'device_id'       => $device_id,
    //       'fcm_token'       => $fcm_token,
    //       'passcode'        => NULL,
    //       'passcode_status' => 0,
    //       'touch_status'    => 0
    //     );

    //         $exists = DB::table('devices')->where('user_id', $userid)->first();
           
    //         if($exists){
    //              //die('sfsdffd');
    //                 $devicecheck=DB::table('devices')->where('user_id', $userid)->where('device_id', $device_id)->first();
                      
    //                 if($devicecheck){
    //                     DB::table('devices')->where('user_id', $userid)->where('device_id', $device_id)->update(['fcm_token' => $fcm_token]);
    //                 }
    //                 else{
    //                     DB::table('devices')->where('user_id', $userid)->update(['fcm_token' => $fcm_token,'device_id'=> $device_id,'passcode' => NULL,'passcode_status'=>0,'touch_status'=>0]);
    //                 }
    //             }
    //         else 
    //         {
    //             //die('24234');
    //             $devicecheck = DB::table('devices')->where('device_id', $device_id)->first();
    //             if($devicecheck){
    //                 DB::table('devices')->where('device_id', $device_id)->update(['user_id' => $userid, 'fcm_token' => $fcm_token]);
    //             }
    //         }


    //     $success['status']  = $this->successStatus;
    //     $success['message'] = "Device Registered Successfully!";
    //     return response()->json(['success' => $success], $this->successStatus);
    // }
    
    
    public function registerDevices(Request $request) 
    {
            $userid     = $request->user_id;
            $device_id  = $request->device_id;
            $fcm_token  = $request->fcm_token;
            $app_ver  = $request->app_ver;
            $device_name  = $request->device_name;
            $device_manufacture  = $request->device_manufacture;
            $device_model  = $request->device_model;
            $os_ver  = $request->os_ver;
            $device_os  = $request->device_os;

        $dataArray =  array(
          'user_id'         => $userid,
          'device_id'       => $device_id,
          'fcm_token'       => $fcm_token,
          'passcode'        => NULL,
          'passcode_status' => 0,
          'touch_status'    => 0,
          'app_ver'    => $app_ver,
          'device_name'    => $device_name,
          'device_manufacture'    =>$device_manufacture,
          'device_model'    =>$device_model,
          'os_ver'    => $os_ver,
          'device_os'    =>$device_os,
          
        );

            $exists = DB::table('devices')->where('user_id', $userid)->first();
           
            if($exists){
                    $devicecheck=DB::table('devices')->where('device_id', $device_id)->first();
                    if($devicecheck){
                        $check_user_device = DB::table('devices')->where('user_id', $userid)->where('device_id', $device_id)->first();
                        if($check_user_device){
                            
                            $data = [
                                'fcm_token' => $fcm_token,
                                'app_ver'    => $app_ver,
                                'device_name'    => $device_name,
                                'device_manufacture'    =>$device_manufacture,
                                'device_model'    =>$device_model,
                                'os_ver'    => $os_ver,
                                'device_os'    =>$device_os,
                            ];
                           
                            $rs = DB::table('devices')->where('user_id', $userid)->where('device_id', $device_id)->update($data);
          
                            $success['status']  = $this->successStatus;
                            $success['message'] = "if device  exist!";
                            return response()->json(['success' => $success], $this->successStatus);
                        }else{
                            
                            $rs_del = DB::table('devices')->where('device_id', $device_id)->delete();
                            
                            $data = [
                               'fcm_token' => $fcm_token,
                               'device_id' => $device_id,
                               'passcode' => '',
                               'passcode_status' => '',
                               'touch_status' => '',
                            ];
                           
                            $rs = DB::table('devices')->where('user_id', $userid)->update($data);
                            
                            
                            
                            $success['status']  = $this->successStatus;
                            $success['message'] = "if device user not exist!";
                            return response()->json(['success' => $success], $this->successStatus);
                        }
                    }
                    else{
                        DB::table('devices')->where('user_id', $userid)->update(['fcm_token' => $fcm_token,'device_id'=> $device_id,'passcode' => NULL,'passcode_status'=>0,'touch_status'=>0]);
                        $success['status']  = $this->successStatus;
                        $success['message'] = "if device not exist!";
                        return response()->json(['success' => $success], $this->successStatus);
                    }
                }
            else 
            {
                $exists = DB::table('devices')->where('device_id', $device_id)->first();
                if($exists){
                    $devicecheck=DB::table('devices')->where('user_id', $userid)->first();
                    if($devicecheck){
                        DB::table('devices')->where('user_id', $userid)->where('device_id', $device_id)->update(['fcm_token' => $fcm_token]);
                        $success['status']  = $this->successStatus;
                        $success['message'] = "else user  exist!";
                        return response()->json(['success' => $success], $this->successStatus);
                    }
                    else{
                        DB::table('devices')->where('device_id', $device_id)->update(['fcm_token' => $fcm_token,'user_id'=> $userid,'passcode' => NULL,'passcode_status'=>0,'touch_status'=>0]);
                        $success['status']  = $this->successStatus;
                        $success['message'] = "else user not exist!";
                        return response()->json(['success' => $success], $this->successStatus);
                    }
                }else
                {
                     DB::table('devices')->insert($dataArray);   
                     $success['status']  = $this->successStatus;
                     $success['message'] = "insert array!";
                     return response()->json(['success' => $success], $this->successStatus);
                }
           }
    
    }
    
    public function setPasscode(Request $request) {
        // print_r($request->all());
        // die;
        $device_id = $request->device_id;
        $user_id   = $request->user_id;
        $passcode  = $request->passcode;

        $dataArray =  array(
          'passcode' => $passcode,
          'passcode_status' => 1
        );
        
        
        DB::table('devices')->where('device_id',$device_id)->where('user_id',$user_id)->update($dataArray);
        
        $success['status']  = $this->successStatus;
        $success['message'] = "Passcode Set Successfully!";
        return response()->json(['success' => $success], $this->successStatus);
    }
    
    
    public function enableTouch(Request $request) {
        
        $device_id = $request->device_id;
        $user_id = $request->user_id;
        $touch_status = $request->touch_status;
        
        $dataArray =  array(
          'touch_status' => $touch_status
        );
        
        
        DB::table('devices')->where('device_id',$device_id)->where('user_id',$user_id)->update($dataArray);
        
        $success['status']  = $this->successStatus;
        $success['user_id'] = $user_id;
        $success['device_id'] = $device_id;
        $success['touch_status'] = $touch_status;
        return response()->json(['success' => $success], $this->successStatus);
    }
    
    
    public function enablePasscode(Request $request) {
        
        $device_id = $request->device_id;
        $user_id = $request->user_id;
        $passcode_status = $request->passcode_status;
        
        $dataArray =  array(
          'passcode_status' => $passcode_status
        );
        
        
        DB::table('devices')->where('device_id',$device_id)->where('user_id',$user_id)->update($dataArray);
        
        $success['status']  = $this->successStatus;
        $success['user_id'] = $user_id;
        $success['device_id'] = $device_id;
        $success['passcode_status'] = $passcode_status;
        return response()->json(['success' => $success], $this->successStatus);
    }
   
    
     public function registrationWithOtp(Request $request)
    {
        $rules = array(
            //'username'   => 'required|username|unique:users,username',
            'email'      => 'required|email|unique:users,email',
            //'phone'      => 'required|phone|unique:users,phone',
            'pin'   => 'required',
        );
        $fieldNames = array(
            'username'   => 'User Name',
            'phone'      => 'Phone',
            'email'      => 'Email',
           
        );
        $validator = Validator::make($request->all(), $rules);
         //echo "hello"; die;
        $validator->setAttributeNames($fieldNames);
        
        if ($validator->fails())
        {
            $response['message'] = "Email/Phone/Username already exist.";
            $response['status']  = $this->unauthorisedStatus;
            return response()->json(['success' => $response], $this->successStatus);
        }
        else
        {
            //default_currency
            $default_currency = Setting::where('name', 'default_currency')->first(['value']);

            try
            {
                \DB::beginTransaction();

                //Create user
                $user = $this->user->createNewUser($request, 'user');

                //Assign user type and role to new user
                RoleUser::insert(['user_id' => $user->id, 'role_id' => $user->role_id, 'user_type' => 'User']);

                // Create user detail
                $this->user->createUserDetail($user->id);

                // Create user's default wallet
                $this->user->createUserDefaultWallet($user->id, $default_currency->value);

                // Create user's crypto wallet/wallets address
                $this->user->generateUserCryptoWalletAddress($user);

                // Create user's crypto wallet/wallets address
                $generateUserCryptoWalletAddress = $this->user->generateUserCryptoWalletAddress($user);
                // dd($generateUserCryptoWalletAddress);
                if ($generateUserCryptoWalletAddress['status'] == 401)
                {
                    \DB::rollBack();
                    $success['status']  = $this->successStatus;
                    $success['reason']  = 'create-wallet-address-failed';
                    $success['message'] = $generateUserCryptoWalletAddress['message'];
                    return response()->json(['success' => $success], $this->successStatus);
                }

                $userEmail          = $user->email;
                $userFormattedPhone = $user->formattedPhone;

                // Process Registered User Transfers
                $this->user->processUnregisteredUserTransfers($userEmail, $userFormattedPhone, $user, $default_currency->value);

                // Process Registered User Request Payments
                $this->user->processUnregisteredUserRequestPayments($userEmail, $userFormattedPhone, $user, $default_currency->value);
               
                //
                \DB::commit();
                $success['status']  = $this->successStatus;
                $success['data']  = $user;
                $success['message'] = "Registration Successfull!";
                return response()->json(['success' => $success], $this->successStatus);
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $success['status']  = $this->unauthorisedStatus;
                $success['message'] = $e->getMessage();
                return response()->json(['success' => $success], $this->unauthorisedStatus);
            }
        }
    }

    protected function saveUserQrCodeApi($user)
    {
        // print_r($user);
        // die;
        $qrCode = QrCode::where(['object_id' => $user->id, 'object_type' => 'user', 'status' => 'Active'])->first(['id']);
        if (empty($qrCode))
        {
            $createInstanceOfQrCode              = new QrCode();
            $createInstanceOfQrCode->object_id   = $user->id;
            $createInstanceOfQrCode->object_type = 'user';
            if (!empty($user->formattedPhone))
            {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . $user->formattedPhone . '-' . str_random(6));
            }
            else
            {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . str_random(6));
            }
            $createInstanceOfQrCode->status = 'Active';
            $createInstanceOfQrCode->save();
        }
    }
    
    
    
    
    
   
    //  protected function noticeboard()
    // {
    //   if (request('user_id'))
    //     {
    //         $user_id   = request('user_id');
    //         // $data      = DB::table('noticeboard')->where('user', $user_id)->groupBy('push_date')->get();
    //         $data      = DB::table('noticeboard')->where('user', $user_id)->get();
    //         $count     = DB::table('noticeboard')->where('user', $user_id)->get()->count();
    //         foreach($data as $value)
    //         {
    //             $push_date[] = $value->push_date;
    //             $push[$value->push_date][]      = DB::table('noticeboard')->where('user', $user_id)->where('push_date',$value->push_date)->get();
    //         }
    //       // dd($push);
           
    //         $success['status']  = $this->successStatus;
    //         $success['count']   = $count;
    //         $success['data']    = $push;
    //         $success['message'] = "Noticboard data!";
    //         return response()->json(['success' => $success], $this->successStatus);
          
    //     }
    //     else
    //     {
    //             $success['status']  = $this->unauthorisedStatus;
    //             return response()->json(['success' => $success], $this->unauthorisedStatus);
    //     }
    // }
    
    protected function noticeboard()
    {
       
       
        if (request('user_id'))
        {
            $user_id       = request('user_id');
            $data      = DB::table('noticeboard')->where('user', $user_id)->orderBy('id', 'DESC')->get();
            //dd($data);
            
            foreach ($data as $value) {
               if(!empty($value->tr_id))
                {
                    $t_arr = $value->tr_id;
                   $value->transac_data = $transaction_details= DB::table('transactions')->select('transactions.*','currencies.name','currencies.code','currencies.symbol')->join('currencies','currencies.id','=','transactions.currency_id')
                   ->where(['transactions.user_id'=>request('user_id'),'transactions.transaction_reference_id'=> $value->tr_id,'transactions.transaction_type_id'=>10])->first();
                      if(!empty($transaction_details)){
                                 $picture = User::where('id',$transaction_details->end_user_id)->first()->picture??'';
                                 $end_user_email = User::where('id',$transaction_details->end_user_id)->first()->email??'';
                      }else{
                          $picture='';
                          $end_user_email='';
                      }
                     $value->end_user_picture=   $picture ? url('/').'/'.'public/user_dashboard/profile/'.$picture : '' ;
                     $value->end_user_email=$end_user_email;
                } else {
                 $value->transac_data = '';
                }
            }
            
            $success['status']  = $this->successStatus;
            $success['data']  = $data;
            $success['message'] = "Noticboard data!";
            return response()->json(['success' => $success], $this->successStatus);
          
        }
        else
        {
                $success['status']  = $this->unauthorisedStatus;
                return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }
    
    public function readNotification(Request $request)
    {
        $update = DB::table('noticeboard')->where(['user'=>$request->user_id,'id'=>$request->notice_id])->update(['read_status'=>1]);
        $success['status'] = $this->successStatus;
        return response()->json(['success' => $success, 'message' => 'Transaction Read Successfully'], $this->successStatus);
    }
    
    
    protected function message_detail()
    {
        if (request('user_id') && request('message_id'))
        {
            $user_id       = request('user_id');
            $message_id       = request('message_id');
            $data      = DB::table('noticeboard')->where('user', $user_id)->where('id',$message_id)->first();
            DB::table('noticeboard')->where('user', $user_id)->where('id',$message_id)->update(array('read_status'=>1));
            $success['status']  = $this->successStatus;
            $success['data']  = $data;
            $success['message'] = "Noticboard data!";
            return response()->json(['success' => $success], $this->successStatus);
          
        }
        else
        {
            $success['status']  = $this->unauthorisedStatus;
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }
    
    protected function banner()
    {
       
        if (request('position') && request('lang_id'))
        {
            $position = request('position');
            $lang_id = request('lang_id');
            $data      = DB::table('banner')->where('status', 'Active')->where(['position'=>$position,'language'=>$lang_id])->get();
          try{
            foreach($data as $value)
            {
                $new = AppPage::where('id', $value->app_page)->where('status', 'Active')->first(['app_page']);
                 $value->app_page = $new->app_page;
            }
            $success['status']  = $this->successStatus;
            $success['data']  = $data;
            $success['message'] = "Banner data!";
            return response()->json(['success' => $success], $this->successStatus);
          }
          catch(\Exception $e){
              return response()->json(['status' =>400,'erorr'=>$e->getMessage()]);
          }
        }
          
        
    }
    
    
    public function getDeviceInfo()
    {
        $device_id= request('device_id');
        $deviceInfo = DB::table("devices")->where('device_id',$device_id)->first();
        if($deviceInfo){
            return response()->json([
            'status'   => $this->successStatus,
            'device_info' => $deviceInfo,
        ]);
        }
        else{
            return response()->json([
            'status'   => $this->emptyStatus,
            'message' => "No device info found",
        ]);
        }
    }
    
    public static function SendNotification (Request $request) {
        
         try{
        $subject = $request->subject;
        $subheader = $request->sub_header;
        $user_id = $request->user_id;
        $currency = $request->currency_id;
        $type = $request->type;

        $userdevices     = DB::table('devices')->where(['user_id' => $user_id])->first()->fcm_token??'';
        
        $language_id        = DB::table('devices')->where(['user_id' => $user_id])->first()->language;
        // dd($language_id);
        $language_get = Language::where('id', $language_id)->first();
        $language = $language_get->short_name;
        $string1         = DB::table('language_contents')->where(['string'=>$subject ])->first();
        $string2         = DB::table('language_contents')->where(['string'=>$subheader ])->first();
        // dd($language);
        $subject         = $string1->$language;
        $subheader       = $string2->$language;
        // dd($abc);
        $fcmUrl          = 'https://fcm.googleapis.com/fcm/send';
        $headers         = [
            'Authorization: key=AAAA0KDSl6g:APA91bEdYjfk7VcckAbq6DLgxrCV349FXlbHJtQgitYF8uP6l_sYmhb8bpo43iUfxCOVhrxt9N9ligizh97YdaWA188Y-gD9DknoEtFg5PtldlRAe41Hzj7Hi4S43ylVS4B1eCiTUb_r',
            'Content-Type: application/json'
        ];  
        $fcmNotification = [
            'to'        => $userdevices,
            'notification'     => [
                'title' => $subject,
                'body'  => $subheader,
                'sound' => TRUE,
                'priority' => "high",
                
            ],
            'data'             => [
                'title' => $subject,
                'body'  => $subheader,
                 "type"    => $type,
                 "currency"=> $currency
            ]
        ];
        
        // dd($fcmNotification);
        $ch              = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        // dd($result);
        return $result;
        }catch(\Exception $e){
            
             return response()->json([
            // 'status'   => $this->emptyStatus,
            'status'   =>201,
            'message' => $e->getMessage().$e->getLine(),
        ]);       
        }
    }
    
    
    public function resetPassword(Request $request) 
    {
        $email = $request->email;
        
        $firebase_token = $request->firebase_token;
        
        $factory = (new Factory)->withServiceAccount('/home/ticktappay/public_html/ewallet/firebase/firebase-adminsdk.json');
        $database = $factory->createDatabase();
        $auth = $factory->createAuth();
         
        $idTokenString = $firebase_token;
        $verifiedIdToken = $auth->verifyIdToken($idTokenString);
        $uid = $verifiedIdToken->claims()->get('sub');
        $user_uid = $auth->getUser($uid);
        //dd($user_uid->uid);
        
        $check_user = User::where('uid', $user_uid->uid)->where('email', $email)->orWhere('formattedPhone',$email)->orWhere('phone',$email)->first();
        if(!empty($check_user)){       
            $user  = User::where('email', $email)->orWhere('formattedPhone',$email)->orWhere('phone',$email)->first();
            if ($user)
            {
                $updatePass = array(
                    'password' => \Hash::make($request->password)
                );
                User::where('email', $email)->orWhere('phone',$email)->orWhere('formattedPhone',$email)->update($updatePass);
                return response()->json([
                    'status'     => $this->successStatus,
                    'message'    => "Password has been reset successfully."
                ]);
            } else {
                return response()->json([
                    'status'     => $this->unauthorisedStatus,
                    'message'    => "Email Address / Phone Number does not match!"
                ]);
            }
        }else{
            return response()->json([
                'status'     => $this->unauthorisedStatus,
                'message'    => "Unauthorized User!"
            ]);
        }
    }
      public function CheckPassword(){
         
        try{
            
            
            
         if(request('user_id') && request('password')){
             
            $result=User::where('id',request('user_id'))->first();
            $hasher = app('hash');
            if ($hasher->check(request('password'), $result->password)) {
                        return response()->json(['status'=>$this->successStatus,'message' =>'Password is correct']);
                }else{
                
                 return response()->json(['status'=>400,'message' =>'Invalid password'], $this->successStatus);
            }
         }elseif(request('device_id') && request('passcode')){

         $users = DB::table('devices')->where(['device_id'=>request('device_id'),'passcode_status'=>1])->first();
         if($users->passcode == request('passcode'))
         {
             return response()->json(['status'=>$this->successStatus,'message' =>'Passcode is correct']);
      
         }else{
                 return response()->json(['status'=>400,'message' =>'Invalid passcode'], $this->successStatus);
 
            }
         }
         else{
            return response()->json(['status'=>$this->successStatus,'message' =>'All fields are Required'], $this->successStatus);
         }
        }catch(\Exception $e){
            
            return response()->json(['status'=>400,'message' =>'Something went wrong'], $this->successStatus);

            
        }
         
     }
     
     
     
     
     
    
    
}
