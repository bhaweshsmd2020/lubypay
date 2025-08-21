<?php

namespace App\Http\Controllers\Api;
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
use App\Models\Noticeboard;
use App\Models\NotificationTemplate;
use App\Models\Notification;
use Illuminate\Support\Str;
use App\Models\UserDeviceLog;
use App\Http\Helpers\Common;
use Illuminate\Support\Facades\Hash;
use App\Models\ForgotOtp;
use App\Models\Device;

class RegistrationController extends Controller
{
    public $successStatus      = 200;
    public $emptyStatus        =201;
    public $unauthorisedStatus = 401;
    public $email;
    protected $user;
    protected $helper;

    public function __construct()
    {
        $this->email = new EmailController();
        $this->user  = new User();
        $this->helper = new Common();
        
        $setting = Setting::first();
        $this->admin_email = $setting->notification_email;
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
        $email = $request->email;
        $user_type = $request->user_type;
        
        $check_email = User::where('role_id', $user_type)->where('email', $email)->first();
        if (!empty($check_email))
        {
            $data['status'] = true;
            $data['fail']   = 'The email has already been taken!';
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "Email Available!";
        }
        return response()->json($data, $this->successStatus);
    }
    
    public function duplicatePhoneNumberCheckApi(Request $request)
    {
        $phone = $request->phone;
        $user_type = $request->user_type;
        
        $check_phone = User::where('role_id', $user_type)->where('formattedPhone', $phone)->first();

        if (!empty($check_phone))
        {
            $data['status'] = true;
            $data['fail']   = "The phone number has already been taken!";
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "The phone number is Available!";
        }
        return response()->json($data, $this->successStatus);
    }
    
    public function duplicateDeviceCheckApi(Request $request)
    {
        $device_id = $request->device_id;
        $user_type = $request->user_type;
        
        $check_device = User::where('device_id', $device_id)->where('role_id', $user_type)->whereIn('kyc_status', [1, 2, 3])->first();
        if (!empty($check_device))
        {
            $data['status'] = true;
            $data['email'] = $check_device->email;
            $data['fail']   = "Your device is associated with another account ".$check_device->email;
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "The device is Available!";
        }
        return response()->json($data, $this->successStatus);
    }
    
    public function duplicateDeviceRegister(Request $request)
    {
        $device_id = $request->device_id;
        $local_trans_time = $request->local_trans_time;
        $user_type = $request->user_type;
        
        $check_device = User::where('device_id', $device_id)->where('role_id', $user_type)->first();

        if (!empty($check_device))
        {
            $user_id = $check_device->id;
            
            $rs = UserDeviceLog::create([
                'user_id' => $user_id,
                'device_id' => $device_id,
                'ip_address' => request()->ip(),
                'local_trans_time' => $local_trans_time
            ]);
            
            User::where('device_id', $device_id)->where('role_id', $user_type)->update(['request_device' => '0']);
            
            $adminAllowed = Notification::has_permission([1]);
                    
            foreach($adminAllowed as $admin){
                $name = User::where('id', $user_id)->first();
                Notification::insert([
                    'user_id'               => $user_id,
                    'notification_to'       => $admin->agent_id,
                    'notification_type_id'  => 1,
                    'notification_type'     => 'App',
                    'description'           => 'User '.$name->first_name.' has requested to allow different registration from the same device.',
                    'url_to_go'             => 'admin/users/edit/'.$user_id,
                    'local_tran_time'       => null
                ]);
            }
    
            $userdevice = DB::table('devices')->where('user_id', $user_id)->first();
            if(!empty($userdevice)){
                $template = NotificationTemplate::where('temp_id', '34')->where('language_id', $userdevice->language)->first();
                $subject = $template->title;
                $subheader = $template->subheader;
                $message = $template->content;
                
                $type = 'cleardevice';
                $currency = '9';
                
                $date    = date("m-d-Y h:i");
                // $this->helper->sendFirabasePush($subject,$message,$user_id, $currency, $type);
                
                Noticeboard::create([
                    'tr_id' => null,
                    'title' => $subject,
                    'content' => $message,
                    'type' => 'push',
                    'content_type' => $type,
                    'user' => $user_id,
                    'sub_header' => $subheader,
                    'push_date' => $date,
                    'template' => '34',
                    'language' => $userdevice->language,
                ]);
            }
            
            $data['status']  = true;
            $data['success'] = "Your request has been submitted successfully ! You will be notified once this device is ready to continue for new signup.";
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "The device is not available!";
        }
        return response()->json($data, $this->successStatus);
    }
    
    public function send_email(Request $request)
    {
        $email = $request->email;
        $user_type = $request->user_type;
        
        $random_otp = random_int(100000, 999999);
        
        $check_email = User::where('role_id', $user_type)->where('email', $email)->first();
        if (empty($check_email))
        {
            $data['status']  = false;
            $data['fail'] = "Invalid Email!";
            return response()->json($data, $this->successStatus);
        }
        
        $user_update = User::where('role_id', $user_type)->where('email', $email)->update(['email_otp' => $random_otp]);
        
        $user = User::where('role_id', $user_type)->where('email', $email)->first();
        
        // Email verification
        $userdevice = DB::table('devices')->where('user_id', $user->id)->first();
        if(!empty($userdevice)){
            $device_lang = $userdevice->language;
        }else{
            $device_lang = getDefaultLanguage();
        }
        
        $twoStepVerification = EmailTemplate::where([
            'temp_id'     => '17',
            'language_id' => $device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{email}', $user->email, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{verification_otp}', $user->email_otp, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
        
        $data['status'] = true;
        $data['success']   = 'OTP send on email!';
        return response()->json($data, $this->successStatus);
    }
    
    public function verify_email(Request $request)
    {
        $email = $request->email;
        $otp = $request->email_otp;
        $user_type = $request->user_type;
        
        $check_email = User::where('role_id', $user_type)->where('email', $email)->first();
        if (empty($check_email))
        {
            $data['status']  = false;
            $data['success'] = "Invalid Email!";
            return response()->json($data, $this->successStatus);
        }
        
        $check_otp = User::where('role_id', $user_type)->where('email', $email)->where('email_otp', $otp)->first();
        if (empty($check_otp))
        {
            $data['status']  = false;
            $data['success'] = "Invalid OTP!";
            return response()->json($data, $this->successStatus);
        }
     
        if($check_otp->email_status == '1'){
            $data['status'] = false;
            $data['fail']   = 'The email has already been verified!';
            return response()->json($data, $this->successStatus);
        }else{
            User::where('role_id', $user_type)->where('email', $email)->where('email_otp', $otp)->update(['email_status' => '1']);
            $data['status'] = true;
            $data['fail']   = 'The email has been verified!';
            return response()->json($data, $this->successStatus);
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
            'email'      => 'required',
            'password'   => 'required',
            'phone'      => 'required',
        );

        $fieldNames = array(
            'email'      => 'Email',
            'password'   => 'Password',
            'phone'      => 'Phone',
        );
        
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);
        
        if ($validator->fails())
        {
            $response['message'] = "All fields are required.";
            $response['status']  = $this->unauthorisedStatus;
            return response()->json(['success' => $response], $this->successStatus);
        }
        else
        {
            $default_currency = Setting::where('name', 'default_currency')->first(['value']);

            try
            {
                $check_email = User::where('email', $request->email)->where('type', $request->type)->first();
                if(!empty($check_email)){
                    $success['status']  = $this->unauthorisedStatus;
                    $success['message'] = 'Email already registered.';
                    return response()->json(['success' => $success], $this->unauthorisedStatus);
                }
                
                $check_phone = User::where('phone', $request->phone)->where('type', $request->type)->first();
                if(!empty($check_phone)){
                    $success['status']  = $this->unauthorisedStatus;
                    $success['message'] = 'Phone Number already registered.';
                    return response()->json(['success' => $success], $this->unauthorisedStatus);
                }
                
                \DB::beginTransaction();

                //Create user
                $user = $this->user->createNewUser($request, $request->type);
                
                //Assign user type and role to new user
                RoleUser::insert(['user_id' => $user->id, 'role_id' => $user->role_id, 'user_type' => 'User']);

                // Create user detail
                $this->user->createUserDetail($request,$user->id);

                // Create user's default wallet
                $this->user->createUserDefaultWallet($user->id, $default_currency->value);

                //Entry for User's QrCode Generation
                $this->saveUserQrCodeApi($user);
                
                // Create user's crypto wallet/wallets address
                
                $userEmail          = $user->email;
                $userFormattedPhone = $user->formattedPhone;

                // Process Registered User Transfers
                $this->user->processUnregisteredUserTransfers($userEmail, $userFormattedPhone, $user, $default_currency->value);

                // Process Registered User Request Payments
                $this->user->processUnregisteredUserRequestPayments($userEmail, $userFormattedPhone, $user, $default_currency->value);
            	
            	$phone = $request->formattedPhone;
                $user_type = $request->type;
                
                $otp = rand(111111,999999);
                if($user_type == 'user'){
                    $text = "Hello, Your one-time validation code for LubyPay is ".$otp;
                    $role_id = '2';
                }else{
                    $text = "Hello, Your one-time validation code for LubyNet is ".$otp;
                    $role_id = '3';
                }
                
                if($request->otp_type == 'twilio'){
                    $smsPlatform = Country::where('phone_code', $request->carrierCode)->first();
                    $rs = ForgotOtp::create([
                        'phone' => $phone,
                        'otp' => $otp,
                        'role_id' => $role_id,
                    ]);
                    
                    sendSMS($phone,$text,$smsPlatform->region_name);
                } 
                
                \DB::commit();
                $success['status']  = $this->successStatus;
                $success['user_id']  = $user->id;
                $success['kyc_status']  = 0;
                $success['message'] = "Registration Successfull!";
                $success['otp_type'] = $request->otp_type;
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
        
        $checkuser = User::where('id', $userid)->first();

        $check_user = DB::table('devices')->where('user_id', $userid)->where('device_id', $device_id)->where('user_type', $checkuser->role_id)->first();
        if(!empty($check_user)){
            
            $check_device = DB::table('devices')->where('user_id', $userid)->where('device_id', $device_id)->where('user_type', $checkuser->role_id)->first();
           
            DB::table('devices')->where('user_id', $userid)->where('device_id', $device_id)->where('user_type', $checkuser->role_id)->update([
                'fcm_token'          => $fcm_token,
                'app_ver'            => $app_ver,
                'device_name'        => $device_name,
                'device_manufacture' => $device_manufacture,
                'device_model'       => $device_model,
                'os_ver'             => $os_ver,
                'device_os'          => $device_os,
                'user_type'          => $checkuser->role_id,
                'language'           => $check_device->language
            ]);
            
            DB::table('devices')->where('device_id', $device_id)->whereNull('user_id')->delete();
            DB::table('devices')->where('device_id', $device_id)->whereNull('fcm_token')->delete();

            $success['status']  = $this->successStatus;
            $success['message'] = "Device updated successfully 1.";
            return response()->json(['success' => $success], $this->successStatus);
        }else{
            
            $check_device = DB::table('devices')->where('device_id', $device_id)->where('user_type', $checkuser->role_id)->first();
            if(!empty($check_device)){
                DB::table('devices')->where('user_id', $userid)->where('user_type', $checkuser->role_id)->update([
                    'fcm_token'          => null,
                    'user_id'            => null,
                ]);
                
                DB::table('devices')->where('device_id', $device_id)->where('user_type', $checkuser->role_id)->update([
                    'fcm_token'          => null,
                    'user_id'            => null,
                ]);
                
                DB::table('devices')->insert([
                    'user_id'            => $userid,
                    'device_id'          => $device_id,
                    'fcm_token'          => $fcm_token,
                    'passcode'           => NULL,
                    'passcode_status'    => 0,
                    'touch_status'       => 0,
                    'app_ver'            => $app_ver,
                    'device_name'        => $device_name,
                    'device_manufacture' => $device_manufacture,
                    'device_model'       => $device_model,
                    'os_ver'             => $os_ver,
                    'device_os'          => $device_os,
                    'user_type'          => $checkuser->role_id,
                    'language'           => $check_device->language
                ]);
                
                DB::table('devices')->whereNull('user_id')->delete();
                DB::table('devices')->whereNull('fcm_token')->delete();
                
                $success['status']  = $this->successStatus;
                $success['message'] = "Device updated successfully 2.";
                return response()->json(['success' => $success], $this->successStatus);
            }else{
                $check_new_device = DB::table('devices')->where('device_id', $device_id)->first();
                DB::table('devices')->where('device_id', $device_id)->where('user_type', $checkuser->role_id)->delete();
                DB::table('devices')->where('user_id', $userid)->where('user_type', $checkuser->role_id)->delete();
                DB::table('devices')->where('device_id', $device_id)->whereNull('user_id')->delete();
                DB::table('devices')->where('device_id', $device_id)->whereNull('fcm_token')->delete();
                
                DB::table('devices')->insert([
                    'user_id'            => $userid,
                    'device_id'          => $device_id,
                    'fcm_token'          => $fcm_token,
                    'passcode'           => NULL,
                    'passcode_status'    => 0,
                    'touch_status'       => 0,
                    'app_ver'            => $app_ver,
                    'device_name'        => $device_name,
                    'device_manufacture' => $device_manufacture,
                    'device_model'       => $device_model,
                    'os_ver'             => $os_ver,
                    'device_os'          => $device_os,
                    'user_type'          => $checkuser->role_id,
                    'language'           => $check_new_device->language
                ]);
                
                $success['status']  = $this->successStatus;
                $success['message'] = "Device created successfully 3.";
                return response()->json(['success' => $success], $this->successStatus);
            }
        }
    }
    
    public function enableTouch(Request $request) 
    {
        $device_id = $request->device_id;
        $user_id = $request->user_id;
        $touch_status = $request->touch_status;
        
        $user = User::where('id', $user_id)->first();
        
        DB::table('devices')->where('device_id', $device_id)->where('user_id',$user_id)->where('user_type', $user->role_id)->update([
            'touch_status' => $touch_status,
            'user_id' => $user_id
        ]);
        
        $success['status']  = $this->successStatus;
        $success['user_id'] = $user_id;
        $success['device_id'] = $device_id;
        $success['touch_status'] = $touch_status;
        return response()->json(['success' => $success], $this->successStatus);
    }
    
    public function enablePasscode(Request $request) 
    {
        $device_id = $request->device_id;
        $user_id = $request->user_id;
        $passcode_status = $request->passcode_status;
        
        $user = User::where('id', $user_id)->first();
        
        DB::table('devices')->where('device_id', $device_id)->where('user_id',$user_id)->where('user_type', $user->role_id)->update(['passcode_status' => $passcode_status]);
        
        $success['status']  = $this->successStatus;
        $success['user_id'] = $user_id;
        $success['device_id'] = $device_id;
        $success['passcode_status'] = $passcode_status;
        return response()->json(['success' => $success], $this->successStatus);
    }
    
    public function setPasscode(Request $request) 
    {
        $device_id = $request->device_id;
        $user_id   = $request->user_id;
        $passcode  = $request->passcode;
        
        $user = User::where('id', $user_id)->first();

        DB::table('devices')->where('device_id',$device_id)->where('user_id',$user_id)->where('user_type', $user->role_id)->update([
            'passcode' => $passcode,
            'passcode_status' => 1
        ]);
        
        $success['status']  = $this->successStatus;
        $success['message'] = "Passcode Set Successfully!";
        return response()->json(['success' => $success], $this->successStatus);
    }
    
    public function changePasscode(Request $request) 
    {
        $device_id = $request->device_id;
        $user_id   = $request->user_id;
        $passcode  = $request->passcode;
        
        $user = User::where('id', $user_id)->first();
        
        $check = DB::table('devices')->where(['device_id' => $device_id, 'passcode_status' => 1, 'user_id' => $user_id, 'user_type' => $user->role_id])->first();
        if(empty($check))
        {
            $success['status']  = false;
            $success['message'] = "Please set passcode first!";
            return response()->json(['success' => $success], $this->successStatus);
        }else
        {
            DB::table('devices')->where('device_id', $device_id)->where('user_id', $user_id)->where('user_type', $user->role_id)->update(['passcode' => $passcode]);
            $success['status']  = $this->successStatus;
            $success['message'] = "Passcode changed Successfully!";
            return response()->json(['success' => $success], $this->successStatus);
        }
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
        $qrCode = QrCode::where(['object_id' => $user->id, 'object_type' => 'user', 'status' => 'Active'])->first(['id']);
        if (empty($qrCode))
        {
            $createInstanceOfQrCode              = new QrCode();
            $createInstanceOfQrCode->object_id   = $user->id;
            $createInstanceOfQrCode->object_type = 'user';
            if (!empty($user->phone))
            {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . $user->type . '-' . $user->phone . '-' . Str::random(6));
            }
            else
            {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . $user->type . '-' . Str::random(6));
            }
            $createInstanceOfQrCode->status = 'Active';
            $createInstanceOfQrCode->save();
        }
    }
    
    protected function noticeboard(Request $request)
    {
        $rules = array(
            'user_id'   => 'required',
        );
        $fieldNames = array(
            'user_id'   => 'User Id',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);
        if ($validator->fails())
        {
            $response['message'] = "All fields are required.";
            $response['status']  = $this->unauthorisedStatus;
            return response()->json(['success' => $response], $this->successStatus);
        }
        
        $user_id = request('user_id');
            
        $data = Noticeboard::where('user', $user_id)->whereNotNull('language')->orderBy('id', 'DESC')->get();
        $userdevice = DB::table('devices')->where('user_id', $user_id)->first();

        foreach ($data as $value) {
            $template = NotificationTemplate::where('temp_id', $value->template)->where('language_id', $userdevice->language)->first();
            if(!empty($template)){
                $subject = $template->title;
                $subheader = $template->subheader;
                $message = $template->content;
                
                $sub = str_replace('{status}', $value->status, $subject);
                $sub = str_replace('{ticket}', $value->ticket, $sub);
                $sub = str_replace('{last_four}', $value->last_four, $sub);
                $sub = str_replace('{currency}', $value->currency, $sub);
                $sub = str_replace('{amount}', $value->amount, $sub);
                
                $subhead = str_replace('{sender}', $value->sender, $subheader);
                $subhead = str_replace('{currency}', $value->currency, $subhead);
                $subhead = str_replace('{amount}', $value->amount, $subhead);
                $subhead = str_replace('{status}', $value->status, $subhead);
                $subhead = str_replace('{ticket}', $value->ticket, $subhead);
                
                $msg = str_replace('{receiver}', $value->receiver, $message);
                $msg = str_replace('{sender}', $value->sender, $msg);
                $msg = str_replace('{currency}', $value->currency, $msg);
                $msg = str_replace('{amount}', $value->amount, $msg);
                $msg = str_replace('{from_currency}', $value->from_currency, $msg);
                $msg = str_replace('{from_amount}', $value->from_amount, $msg);
                $msg = str_replace('{to_currency}', $value->to_currency, $msg);
                $msg = str_replace('{to_amount}', $value->to_amount, $msg);
                $msg = str_replace('{product}', $value->product, $msg);
                $msg = str_replace('{status}', $value->status, $msg);
                $msg = str_replace('{ticket}', $value->ticket, $msg);
                $msg = str_replace('{last_four}', $value->last_four, $msg);
                $msg = str_replace('{days}', $value->days, $msg);
                
                $value->new_title = $sub;
                $value->new_subheader = $subhead;
                $value->new_content = $msg;
            }
            
            if(!empty($value->tr_id))
            {
                $value->transac_data = $transaction_details = Transaction::where('transactions.user_id', $user_id)->where('transactions.id', $value->tr_id)->first();
                if(!empty($transaction_details)){
                    $picture = User::where('id',$transaction_details->end_user_id)->first()->picture??'';
                    $end_user_email = User::where('id',$transaction_details->end_user_id)->first()->email??'';
                }else{
                    $picture='';
                    $end_user_email='';
                }
                $value->end_user_picture = $picture ? url('/').'/'.'public/user_dashboard/profile/'.$picture : '' ;
                $value->end_user_email = $end_user_email;
            } else {
                $value->transac_data = '';
            }
        }
        
        $success['status']  = $this->successStatus;
        $success['data']  = $data;
        $success['message'] = "Noticboard data!";
        return response()->json(['success' => $success], $this->successStatus);
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
                    if(!empty($value->app_page)){
                        $new = AppPage::where('id', $value->app_page)->where('status', 'Active')->first();
                        $value->app_page_name = $new->app_page;
                    }else{
                        $value->app_page_name = '';
                    }
                    $value->banner_image = url('public/uploads/banner/'.$value->banner_image);
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
        $device_id = request('device_id');
        $user_type = request('user_type');
        
        $deviceInfo = DB::table("devices")->where('device_id', $device_id)->where('user_type', $user_type)->orderBy('id', 'desc')->first();
        if($deviceInfo){
            return response()->json([
                'status'   => $this->successStatus,
                'device_info' => $deviceInfo,
            ]);
        }else{
            return response()->json([
                'status'   => $this->emptyStatus,
                'message' => "No device info found",
            ]);
        }
    }
    
    public static function SendNotification (Request $request) 
    {
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
                'status'   =>201,
                'message' => $e->getMessage().$e->getLine(),
            ]);       
        }
    }
    
    public function resetPassword(Request $request) 
    {
        $phone = $request->email;
        $password = $request->password;
        
        $check_user = User::where('phone', $phone)->orWhere('formattedPhone',$phone)->first();
        if(!empty($check_user)){ 
            $updatePass = array(
                'password' => \Hash::make($request->password)
            );
            User::where('phone',$phone)->orWhere('formattedPhone',$phone)->update($updatePass);
            return response()->json([
                'status'     => $this->successStatus,
                'message'    => "Password has been reset successfully."
            ]);
        }else{
            return response()->json([
                'status'     => $this->unauthorisedStatus,
                'message'    => "Phone Number does not match!"
            ]);
        }
    }
    
    public function CheckPassword()
    {
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
            }else{
                return response()->json(['status'=>$this->successStatus,'message' =>'All fields are Required'], $this->successStatus);
            }
        }catch(\Exception $e){
            return response()->json(['status'=>400,'message' =>'Something went wrong'], $this->successStatus);
        }
    }
     
    public function changePassword(Request $request) 
    {
         $input = $request->all();
         $rules = array(
            'user_id'      => 'required',
            'current_password'   => 'required',
            'new_password'   => 'required',
            'password'   => 'required|same:new_password',
        );

        $fieldNames = array(
            'user_id'      => 'User Id',
            'current_password'      => 'Current Password',
            'new_password'      => 'New Password',
            'password'   => 'Password',
        );
        
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);
        
        if ($validator->fails())
        {    
            $response['message'] = $validator->messages()->first();
            $response['status']  = $this->unauthorisedStatus;
            return response()->json(['success' => $response], $this->successStatus);
        }
        
        $check_user = User::where('id',$input['user_id'])->first();
        
        if (!empty($check_user) &&  !Hash::check($input['current_password'], $check_user->password??'')){
             return response()->json([
                'status'     => $this->unauthorisedStatus,
                'message'    => "Current password is invalid."
            ]);
        }
        
        if(!empty($check_user)){ 
            $updatePass = array(
                'password' => \Hash::make($request->password)
            );
           $check_user->update($updatePass);
            return response()->json([
                'status'     => $this->successStatus,
                'message'    => "Password has been updated successfully."
            ]);
        }else{
            return response()->json([
                'status'     => $this->unauthorisedStatus,
                'message'    => "User does not exist!"
            ]);
        }
    }
    
    public function changeAuthentication(Request $request) 
    {
        $user_id = $request->user_id;
        $old_phone_email = $request->old_phone_email;
        $new_phone_email = $request->new_phone_email;
        $type = $request->type;
        $country_code = $request->country_code;
        
        if($type == 'email'){
            $user = User::where('id', $user_id)->where('email', $old_phone_email)->first();
            if(empty($user)){
                return response()->json([
                    'status'     => $this->unauthorisedStatus,
                    'message'    => "User not exists!"
                ]);
            }
            
            $random_otp = random_int(100000, 999999);
            
            User::where('id', $user_id)->where('email', $old_phone_email)->update(['email_otp' => $random_otp]);
            
            $checkUser = User::where('id', $user_id)->where('email', $old_phone_email)->first();
            
            $userdevice = Device::where('user_id', $user_id)->first();
            if(!empty($userdevice)){
                $device_lang = $userdevice->language;
            }else{
                $device_lang = getDefaultLanguage();
            }
            
            $twoStepVerification = EmailTemplate::where([
                'temp_id'     => '17',
                'language_id' => $device_lang,
                'type'        => 'email',
            ])->select('subject', 'body')->first();
           
            $twoStepVerification_sub = $twoStepVerification->subject;
            $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{email}', $new_phone_email, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{verification_otp}', $checkUser->email_otp, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            $this->email->sendEmail($new_phone_email, $twoStepVerification_sub, $twoStepVerification_msg);
            
            User::where('id', $user_id)->where('email', $old_phone_email)->update(['email' => $new_phone_email]);
            
            return response()->json([
                'status'     => $this->successStatus,
                'message'    => "Email OTP sent successfully."
            ]);
        }else{
            
            $user = User::where('id', $user_id)->where('phone', $old_phone_email)->first();
            if(empty($user)){
                return response()->json([
                    'status'     => $this->unauthorisedStatus,
                    'message'    => "User not exists!"
                ]);
            }
            
            $otp = rand(111111,999999);
            $text = "Hello, Your one-time validation code for LubyPay is ".$otp;
            
            $check_new_phone = '+'.$country_code.$new_phone_email;
            
            if($request->otp_type == 'twilio')
            {
                $smsPlatform = Country::where('phone_code', $country_code)->first();
                
                $rs = ForgotOtp::create([
                    'phone' => $check_new_phone,
                    'otp' => $otp,
                    'role_id' => 2,
                ]);
                
                sendSMS($check_new_phone,$text,$smsPlatform->region_name);
            }
            
            User::where('id', $user_id)->where('phone', $old_phone_email)->update(['phone' => $new_phone_email, 'carrierCode' => $country_code, 'formattedPhone' => $check_new_phone]);
            
            return response()->json([
                'status'     => $this->successStatus,
                'message'    => "Phone OTP sent successfully."
            ]);
        }
    }
    
    public function checkSmsGateway(Request $request) 
    {
        $gateway = 'twilio';
        
        return response()->json([
            'status'  => $this->successStatus,
            'gateway' => $gateway,
            'message' => "SMS gateway fetched successfully."
        ]);
    }
}