<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\EmailTemplate;
use App\Models\ActivityLog;
use App\Models\Preference;
use App\Models\Setting;
use App\Models\User;
use App\Models\VerifyUser;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\AppPage;
use App\Models\Country;
use App\Models\Noticeboard;
use App\Models\NotificationTemplate;
use App\Models\Notification;
use App\Models\MaintenanceSetting;
use App\Models\Language;
use App\Models\LanguageContent;
use App\Models\ForgotOtp;

class LoginController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    public $unverifiedUser     = 201;
    public $inactiveUser       = 501;
    protected $helper;
    public $email;
    public $jwt;
    public $tokens;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email  = new EmailController();
        $this->jwt    = new TokenRepository();
        $this->base_url   = Setting::where('name', 'card_url')->first(['value'])->value;
        $this->api_key    = Setting::where('name', 'card_key')->first(['value'])->value;
        $this->api_secret = Setting::where('name', 'card_secret')->first(['value'])->value;
        
        $setting = Setting::first();
        $this->admin_email = $setting->notification_email;
    }

    public function checkLoginVia()
    {
        $loginVia = Setting::where('name', 'login_via')->first(['value'])->value;
        return response()->json([
            'status'   => $this->successStatus,
            'loginVia' => $loginVia,
        ]);
    }
    public function charity_list()
    {
        $charity = User::where(['is_charity'=>1,'status'=>'Active'])->select('id','first_name','phone','email','status','picture')->get();
        if(count($charity)>0)
        {
             return response()->json([
                'status'     => 200,
                'message'    => "Charity List get successfully...",
                'data'       => $charity
                 ]);
        }else
        {
            return response()->json([
                'status'     => 400,
                'message'    => "No Data Found!",
                'data'       => array()
                 ]);
        }
       
       
    }
    
    
    public function logoutfromotherdevices(Request $request) {
            $user_id = $request->user_id;
            $user  = User::where('id', $user_id)->first();
            if (!$user)
            {
                $this->helper->one_time_message('error', __('No data Found by this user id!'));
                return response()->json([
                'status'     => $this->unverifiedUser,
                'message'    => "No Data Found!"
                 ]);
            }
            else 
            {
                $devices= \DB::table('devices')->where('user_id', $user->id);
                $devices->delete();
                // $this->helper->one_time_message('success', __('Password reset link has been sent to your email address'));
                return response()->json([
                'status'     => $this->successStatus,
                'message'    => "Logout from other devices successfully !"
                 ]);
             
            }
    }
    
    public function logout(Request $request) 
    {
        $user_id = $request->user_id;
        $user  = User::where('id', $user_id)->first();
        if (!$user)
        {
            $this->helper->one_time_message('error', __('No data Found by this user id!'));
            return response()->json([
                'status'     => $this->unverifiedUser,
                'message'    => "No Data Found!"
            ]);
        }
        else 
        {
            $rs= DB::table('devices')->where('user_id', $user->id)->update([
                'passcode' => null,
                'passcode_status' => 0,
                'touch_status' => 0,
            ]);
           
            return response()->json([
                'status'     => $this->successStatus,
                'message'    => "Logout from other devices successfully !"
            ]);
        }
    }
    
    public function firebaselog(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'phone'      => 'required',
                'fcm_token'  => 'required',
                'device_id'  => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => $this->unauthorisedStatus,'message'=>$validator->errors()], $this->unauthorisedStatus);
            } else {
            $insert = DB::table('firebase_log')->insert([
                 'phone'     => $request->phone,
                 'device_id' => $request->device_id,
                 'fcm_token' => $request->fcm_token,
                 'user_id'   => $request->user_id,
                ]);
            if($insert)
            {
                return response()->json([
                    'status'     => 200,
                    'message'    => "Firebase detail store successfully!"
                     ]);
            }else
            {
                 return response()->json([
                    'status'     => 401,
                    'message'    => "Not Stored!"
                     ]);
            }
        }  
    }
    
    public function forgotPassword(Request $request) {
        //dd($request->all());
         $validator = Validator::make($request->all(), [
                'email'      => 'required',
                'password'  => 'required',
                'conf_password'  => 'required',
             ]);
            if ($validator->fails()) {
                return response()->json(['status' => $this->unauthorisedStatus,'message'=>$validator->errors()], $this->unauthorisedStatus);
            } else {
            $email = $request->email;
            $user  = User::where('email', $email)->orWhere('phone',$email)->first();
            if (!$user)
            {
                $this->helper->one_time_message('error', __('Email Address does not match!'));
                return response()->json([
                'status'     => $this->unverifiedUser,
                'message'    => "Email Address / Phone Number does not match!"
                 ]);
            }
            
            if(is_numeric($email)) {
               
               if($request->password == $request->conf_password)
               {
                    $phone = $email;
                    $updatePass = array(
                      'password' => \Hash::make($request->password)
                    );
                    
                    User::where('email', $email)->orWhere('phone',$email)->update($updatePass);
                    return response()->json([
                    'status'     => $this->successStatus,
                    'message'    => "Password has been forget successfully."
                     ]);
               }else
               {
                   return response()->json([
                    'status'     => $this->unauthorisedStatus,
                    'message'    => "password and confirm password should be same!."
                     ]);
               }
               
               
            } 
            else {
            $userData['email']      = $request->email;
            $userData['token']      = $token      = base64_encode(encryptIt(rand(1000000, 9999999) . '_' . $request->email));
            $userData['created_at'] = date('Y-m-d H:i:s');

            DB::table('password_resets')->insert($userData);

            $userFullName = $user->first_name . ' ' . $user->last_name;
            $this->sendPasswordResetEmail($request->email, $token, $userFullName); //email

            $this->helper->one_time_message('success', __('Password reset link has been sent to your email address'));
            return response()->json([
            'status'     => $this->successStatus,
            'message'    => "Password reset link has been sent to your email address"
             ]);
             
            }
    }
    }
    
    public function resetPassword(Request $request) 
    {
            $email = $request->email;
            $user  = User::where('email', $email)->orWhere('formattedPhone',$email)->orWhere('phone',$email)->first();
            if ($user)
            {
                //echo "hello"; die;
                $updatePass = array(
                  'password' => \Hash::make($request->password)
                );
                //print_r($updatePass); die;
                User::where('email', $email)->orWhere('phone',$email)->orWhere('formattedPhone',$email)->update($updatePass);
                return response()->json([
                'status'     => $this->successStatus,
                'message'    => "Password has been reset successfully."
                 ]);
            } else {
                $this->helper->one_time_message('error', __('Email Address does not match!'));
                return response()->json([
                'status'     => $this->unverifiedUser,
                'message'    => "Email Address / Phone Number does not match!"
                 ]);
            }
    }
    
    public function sendPasswordResetEmail($toEmail, $token, $userFullName)
    {
        $user = User::where('email', $toEmail)->first();
        $userdevice = DB::table('devices')->where('user_id', $user->id)->first();
        if(!empty($userdevice)){
            $device_lang = $userdevice->language;
        }else{
            $device_lang = getDefaultLanguage();
        }

        $userPasswordResetTempInfo = EmailTemplate::where([
            'temp_id'     => 18,
            'language_id' => $device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();

        $userPasswordResetTempInfo_sub = $userPasswordResetTempInfo->subject;
        $userPasswordResetTempInfo_msg = str_replace('{user}', $userFullName, $userPasswordResetTempInfo->body);
        $userPasswordResetTempInfo_msg = str_replace('{email}', $toEmail, $userPasswordResetTempInfo_msg);
        $userPasswordResetTempInfo_msg = str_replace('{password_reset_url}', url('password/resets', $token), $userPasswordResetTempInfo_msg);
        $userPasswordResetTempInfo_msg = str_replace('{soft_name}', getCompanyName(), $userPasswordResetTempInfo_msg);

        $this->email->setupEmailConfig();

        if (checkAppMailEnvironment())
        {
            $this->email->sendEmail($toEmail, $userPasswordResetTempInfo_sub, $userPasswordResetTempInfo_msg);
        }
    }

    public function getPreferenceSettings()
    {
        $preference            = Preference::where(['category' => 'preference'])->whereIn('field', ['thousand_separator', 'decimal_format_amount', 'money_format'])->get(['field', 'value'])->toArray();
        $preference            = Common::key_value('field', 'value', $preference);
        $thousand_separator    = $preference['thousand_separator'];
        $decimal_format_amount = $preference['decimal_format_amount'];
        $money_format          = $preference['money_format'];
        return response()->json([
            'status'                => $this->successStatus,
            'thousand_separator'    => $thousand_separator,
            'decimal_format_amount' => $decimal_format_amount,
            'money_format'          => $money_format,
        ]);
    }
    
    // public function sendOTP(Request $request) 
    // {
    //     $phone = $request->phone;
    //     $user_type = $request->user_type;
        
    //     $otp = rand(111111,999999);
    //     if($user_type == '2'){
    //         $text = "Hello, Your one-time validation code for LubyPay is ".$otp;
    //     }else{
    //         $text = "Hello, Your one-time validation code for LubyNet is ".$otp;
    //     }
        
    //     $rs = ForgotOtp::create([
    //         'phone' => $phone,
    //         'otp' => $otp,
    //         'role_id' => $user_type,
    //     ]);
        
    //     sendSMS($phone,$text);
        
    //     $success['status']      = $this->successStatus;
    //     $success['message']     = 'OTP sent successfully.';
        
    //     return response()->json(['response' => $success], $this->successStatus);
    // }
    
    public function resendOTP(Request $request) 
    {
        $phone = $request->phone;
        $user_type = $request->user_type;
        $otp_type = $request->otp_type;
        
        $check_user = User::where('formattedPhone', $phone)->where('role_id', $user_type)->first();
        if(empty($check_user)){
            $success['status']      = $this->unauthorisedStatus;
            $success['message']     = 'Invalid User!';
            return response()->json(['response' => $success], $this->successStatus);
        }
        
        $otp = rand(111111,999999);
        if($user_type == '2'){
            $text = "Hello, Your one-time validation code for LubyPay is ".$otp;
        }else{
            $text = "Hello, Your one-time validation code for LubyNet is ".$otp;
        }
        
        if($otp_type == 'twilio')
        {
            $country_code = $check_user->carrierCode;
            
            $smsPlatform = Country::where('phone_code', $country_code)->first();
            
            $rs = ForgotOtp::create([
                'user_id' => $check_user->id,
                'phone' => $phone,
                'otp' => $otp,
                'role_id' => $user_type,
            ]);
            
            sendSMS($phone,$text,$smsPlatform->region_name);
        }
        
        $success['status']      = $this->successStatus;
        $success['message']     = 'OTP sent successfully.';
        
        return response()->json(['response' => $success], $this->successStatus);
    }
    
    public function verifyOTP(Request $request) 
    {
        $app = env('APP_NAME');
        $phone = $request->phone;
        $user_type = $request->user_type;
        $otp   = $request->otp;
        
        if($request->otp_type == 'firebase')
        {
            User::where('formattedPhone', $phone)->where('role_id', $user_type)->update(['phone_status' => '1']);
            
            $success['status']      = $this->successStatus;
            $success['message']     = 'Phone verified successfully.';
            
            return response()->json(['response' => $success], $this->successStatus);
        }elseif($request->otp_type == 'twilio')
        {
            $check_phone = ForgotOtp::where('phone', $phone)->where('role_id', $user_type)->orderBy('id', 'desc')->first();
            if(!empty($check_phone)){
                $check_otp = ForgotOtp::where('phone', $phone)->where('role_id', $user_type)->where('otp', $otp)->orderBy('id', 'desc')->first();
                if(!empty($check_otp) && $check_otp->status == '0'){
                    ForgotOtp::where('phone', $phone)->where('role_id', $user_type)->where('otp', $otp)->orderBy('id', 'desc')->update(['status' => '1']);
                    User::where('formattedPhone', $phone)->where('role_id', $user_type)->update(['phone_status' => '1']);
                    
                    $success['status']      = $this->successStatus;
                    $success['message']     = 'Phone verified successfully.';
                }elseif(!empty($check_otp) && $check_otp->status == '1'){
                    $success['status']      = $this->unauthorisedStatus;
                    $success['message']     = 'Phone already verified.';
                }else{
                    $success['status']      = $this->unauthorisedStatus;
                    $success['message']     = 'Invalid OTP!';
                }
            }else{
                $success['status']      = $this->unauthorisedStatus;
                $success['message']     = 'Invalid phone number!';
            }
            
            return response()->json(['response' => $success], $this->successStatus);
        }
    }
    
    public function sendforgotOTP(Request $request) 
    {
        $phone = $request->phone;
        $user_type = $request->user_type;
        $otp_type = $request->otp_type;
        
        $check_user = User::where('formattedPhone', $phone)->where('role_id', $user_type)->first();
        if(empty($check_user)){
            $success['status']      = $this->unauthorisedStatus;
            $success['message']     = 'Invalid User!';
            return response()->json(['response' => $success], $this->successStatus);
        }
        
        $otp = rand(111111,999999);
        if($user_type == '2'){
            $text = "Hello, Your one-time validation code for LubyPay is ".$otp;
        }else{
            $text = "Hello, Your one-time validation code for LubyNet is ".$otp;
        }
        
        if($otp_type == 'twilio')
        {
            $country_code = $check_user->carrierCode;
            
            $smsPlatform = Country::where('phone_code', $country_code)->first();
            
            $rs = ForgotOtp::create([
                'user_id' => $check_user->id,
                'phone' => $phone,
                'otp' => $otp,
                'role_id' => $user_type,
            ]);
            
            sendSMS($phone,$text,$smsPlatform->region_name);
        }
        
        $success['status']      = $this->successStatus;
        $success['message']     = 'OTP sent successfully.';
        
        return response()->json(['response' => $success], $this->successStatus);
    }
    
    public function verifyforgotOTP(Request $request) 
    {
        $app = env('APP_NAME');
        $phone = $request->phone;
        $user_type = $request->user_type;
        $otp   = $request->otp;
        
        if($request->otp_type == 'firebase')
        {
            User::where('formattedPhone', $phone)->where('role_id', $user_type)->update(['phone_status' => '1']);
            
            $success['status']      = $this->successStatus;
            $success['message']     = 'Phone verified successfully.';
            
            return response()->json(['response' => $success], $this->successStatus);
        }elseif($request->otp_type == 'twilio')
        {
            $check_user = User::where('formattedPhone', $phone)->where('role_id', $user_type)->first();
            if(empty($check_user)){
                $success['status']      = $this->unauthorisedStatus;
                $success['message']     = 'Invalid User!';
                return response()->json(['response' => $success], $this->successStatus);
            }
            
            $check_phone = ForgotOtp::where('phone', $phone)->where('role_id', $user_type)->orderBy('id', 'desc')->first();
            if(!empty($check_phone)){
                $check_otp = ForgotOtp::where('phone', $phone)->where('role_id', $user_type)->where('otp', $otp)->orderBy('id', 'desc')->first();
                if(!empty($check_otp) && $check_otp->status == '0'){
                    ForgotOtp::where('phone', $phone)->where('role_id', $user_type)->where('otp', $otp)->orderBy('id', 'desc')->update(['status' => '1']);
                    
                    $success['status']      = $this->successStatus;
                    $success['message']     = 'Phone verified successfully.';
                }elseif(!empty($check_otp) && $check_otp->status == '1'){
                    $success['status']      = $this->unauthorisedStatus;
                    $success['message']     = 'Phone already verified.';
                }else{
                    $success['status']      = $this->unauthorisedStatus;
                    $success['message']     = 'Invalid OTP!';
                }
            }else{
                $success['status']      = $this->unauthorisedStatus;
                $success['message']     = 'Invalid phone number!';
            }
        
            return response()->json(['response' => $success], $this->successStatus);
        }
    }
    
    public function loginWithPasscode (Request $request) 
    {
        $device_id = $request->device_id;
        $passcode = $request->passcode;
        $user_type = $request->user_type;
        
        $users = DB::table('devices')->where(['device_id'=>$device_id, 'passcode_status'=>1, 'user_type' => $user_type])->first();
      
        if($users->passcode == $passcode)
        {
            if ($users)
            {
                Auth::loginUsingId($users->user_id);
                $user             = Auth::user();
                $default_currency = Setting::where('name', 'default_currency')->first(['value']);
                $chkWallet        = Wallet::where(['user_id' => $user->id, 'currency_id' => $default_currency->value])->first();
                try
                {
                    \DB::beginTransaction();
                    if (empty($chkWallet))
                    {
                        $wallet              = new Wallet();
                        $wallet->user_id     = $user->id;
                        $wallet->currency_id = $default_currency->value;
                        $wallet->balance     = 0.00;
                        $wallet->is_default  = 'No';
                        $wallet->save();
                    }
                    $ip=$request->ip();    
                    $location_details = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$ip);
                    $log                  = [];
                    $log['user_id']       = Auth::check() ? $user->id : null;
                    $log['type']          = 'User';
                    $log['ip_address']    = $request->ip();
                    $log['browser_agent'] = $request->device_log;
                    $log['city'] = $location_details->geoplugin_city;
                    $log['country'] = $location_details->geoplugin_countryName;
                    ActivityLog::create($log);

                    $user->user_detail()->update([
                        'last_login_at' => $request->last_login_at,
                        'last_login_ip' => $request->getClientIp(),
                    ]);
                    
                    
                    
                    DB::table('users_login_location')->updateOrInsert(
                        [
                            'user_id' => Auth::check() ? $user->id : null
                        ],
                        [
                            'ip_address' => $request->ip(),
                            'city' => $location_details->geoplugin_city,
                            'country' => $location_details->geoplugin_countryCode,
                            'updated_at'=>Carbon::now()->toDateTimeString()
                        ]
                    );
                    
                    \DB::commit();
                    $dal = DB::table('activity_logs')->where('user_id',$user->id)->orderBy('id', 'desc')->skip(1)->take(1)->first();
                    if(isset($dal))
                    {
                        $success['last_login']      = $request->last_login_at;
                    }
                    $success['user_id']        = $user->id;
                    $success['caribpay_id']     =$user->carib_id;
                    $success['first_name']     = $user->first_name;
                    $success['middle_name']     = $user->middle_name;
                    $success['last_name']      = $user->last_name;
                    $success['email']          = $user->email;
                    $success['formattedPhone'] = $user->formattedPhone;
                    if($user->picture) {
                        $success['picture'] = url('/').'/'.'public/user_dashboard/profile/'.$user->picture;
                    }
                    $fullName                  = $user->first_name . ' ' . $user->last_name;
                    
                    $accessToken               = \DB::table('oauth_access_tokens')->where('user_id', $user->id);
                    $getAccessToken            = $accessToken->first(['id']);
                    
                    if (empty($getAccessToken))
                    {
                        $success['token'] = $user->createToken($fullName)->accessToken;
                    }else{
                        $accessToken->delete();
                        $success['token'] = $user->createToken($fullName)->accessToken;
                    }
                    
                    if ($user->status == 'Inactive')
                    {
                        $error['status']      = $this->successStatus;
                        $error['user_status'] = $user->status;
                        $error['message']     = 'Your account is inactivated. Please contact support!';
                        return response()->json(['response' => $error], $this->successStatus);
                    }else{
                        \DB::table('users')->where('id', '=', $users->user_id)->update(['passcode_failed'=>0]);
                        $success['status']      = $this->successStatus;
                        return response()->json(['response' => $success], $this->successStatus);
                    }
                }
                catch (\Exception $e)
                {
                    \DB::rollBack();
                    $success['status']  = $this->unauthorisedStatus;
                    $success['message'] = $e->getMessage();
                    return response()->json(['response' => $success], $this->unauthorisedStatus);
                }
            }else{
                $success['status']  = $this->unauthorisedStatus;
                $success['message'] = "User is not exist with provided details in the database!";
                return response()->json(['response' => $success], $this->unauthorisedStatus);
            }
        }else{
            DB::table('users')->where('id', '=', $users->user_id)->increment('passcode_failed',1);
            $userData = User::where('id', '=', $users->user_id)->first();
            if($userData->passcode_failed >= 3) {
                $success['passcode_failed']     = $userData->passcode_failed;
                $success['passcode_status']     = 0;
                $success['status']  = $this->unauthorisedStatus;
                $success['message'] = "you exceed your wrong passcode limit please use other way for login!";
                return response()->json(['response' => $success], $this->unauthorisedStatus);
            }else{
                $success['passcode_failed']     = $userData->passcode_failed;
                $success['status']  = $this->unauthorisedStatus;
                $success['message'] = "Invalid passcode!";
                return response()->json(['response' => $success], $this->unauthorisedStatus);
            }
        }
    }
    
    public function loginWithTouch (Request $request) 
    {
        $device_id = $request->device_id;
        $user_type = $request->user_type;
        
        $users = DB::table('devices')->where(['device_id'=>$device_id, 'touch_status'=>1, 'user_type' => $user_type])->first();
        if ($users)
        {
            Auth::loginUsingId($users->user_id);
            
            $user             = Auth::user();
            $default_currency = Setting::where('name', 'default_currency')->first(['value']);
            $chkWallet        = Wallet::where(['user_id' => $user->id, 'currency_id' => $default_currency->value])->first();
            try
            {
                \DB::beginTransaction();
                if (empty($chkWallet))
                {
                    $wallet              = new Wallet();
                    $wallet->user_id     = $user->id;
                    $wallet->currency_id = $default_currency->value;
                    $wallet->balance     = 0.00;
                    $wallet->is_default  = 'No';
                    $wallet->save();
                }
                $ip=$request->ip();    
                $location_details = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$ip);
                $log                  = [];
                $log['user_id']       = Auth::check() ? $user->id : null;
                $log['type']          = 'User';
                $log['ip_address']    = $request->ip();
                $log['browser_agent'] = $request->device_log;
                $log['city'] = $location_details->geoplugin_city;
                    $log['country'] = $location_details->geoplugin_countryName;
                ActivityLog::create($log);

                $user->user_detail()->update([
                    'last_login_at' => $request->last_login_at,
                    'last_login_ip' => $request->getClientIp(),
                ]);
                
                
                
                DB::table('users_login_location')->updateOrInsert(['user_id' => Auth::check() ? $user->id : null],
                    [
                        'ip_address' => $request->ip(),
                        'city' => $location_details->geoplugin_city,
                        'country' => $location_details->geoplugin_countryCode,
                        'updated_at'=>Carbon::now()->toDateTimeString()
                    ]
                );
                
                \DB::commit();
                $dal = DB::table('activity_logs')->where('user_id',$user->id)->orderBy('id', 'desc')->skip(1)->take(1)->first();
                if(isset($dal))
                {
                    $success['last_login']      = $request->last_login_at;
                }
                $success['user_id']        = $user->id;
                $success['caribpay_id']     =$user->carib_id;
                $success['first_name']     = $user->first_name;
                $success['middle_name']     = $user->middle_name;
                $success['last_name']      = $user->last_name;
                $success['email']          = $user->email;
                $success['formattedPhone'] = $user->formattedPhone;
                if($user->picture) {
                    $success['picture'] = url('/').'/'.'public/user_dashboard/profile/'.$user->picture;
                }
                $fullName                  = $user->first_name . ' ' . $user->last_name;
                
                $accessToken               = \DB::table('oauth_access_tokens')->where('user_id', $user->id);
                $getAccessToken            = $accessToken->first(['id']);
                
                if (empty($getAccessToken))
                {
                    $success['token'] = $user->createToken($fullName)->accessToken;
                }
                else
                {
                    $accessToken->delete();
                    $success['token'] = $user->createToken($fullName)->accessToken;
                }
                if ($user->status == 'Inactive')
                {
                    $error['status']      = $this->successStatus;
                    $error['user_status'] = $user->status;
                    $error['message']     = 'Your account is inactivated. Please contact support!';
                    return response()->json(['response' => $error], $this->successStatus);
                }else
                {
                    $success['status']      = $this->successStatus;
                    return response()->json(['response' => $success], $this->successStatus);
                }
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $success['status']  = $this->unauthorisedStatus;
                $success['message'] = $e->getMessage();
                return response()->json(['response' => $success], $this->unauthorisedStatus);
            }
        }
        else
        {
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = "User is not exist with provided details in the database!";
            return response()->json(['response' => $success], $this->unauthorisedStatus);
        }
    }
    
    
    public function loginWithOTP(Request $request) {
        $phone = $request->phone;
        $checkphoneexist = User::where(['formattedPhone' => $phone])->first(['id','status']);
        
        if ((isset($checkphoneexist)))
        {
         //Auth attempt - starts
         //Get the user
            $user = User::where('formattedPhone', '=', $request->phone)->first();
            //Now log in the user if exists
            if ($user)
            {
                Auth::loginUsingId($user->id);
                //echo "hello"; die;
                $user             = Auth::user();
                $default_currency = Setting::where('name', 'default_currency')->first(['value']);
                $chkWallet        = Wallet::where(['user_id' => $user->id, 'currency_id' => $default_currency->value])->first();
                try
                {
                    \DB::beginTransaction();

                    if (empty($chkWallet))
                    {
                        $wallet              = new Wallet();
                        $wallet->user_id     = $user->id;
                        $wallet->currency_id = $default_currency->value;
                        $wallet->balance     = 0.00;
                        $wallet->is_default  = 'No';
                        $wallet->save();
                    }
                    $ip=$request->ip();    
                    $location_details = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$ip);
                    $log                  = [];
                    $log['user_id']       = Auth::check() ? $user->id : null;
                    $log['type']          = 'User';
                    $log['ip_address']    = $request->ip();
                    $log['browser_agent'] = $request->header('user-agent');
                    $log['city'] = $location_details->geoplugin_city;
                    $log['country'] = $location_details->geoplugin_countryName;
                    
                    ActivityLog::create($log);

                    $user->user_detail()->update([
                        'last_login_at' => $request->last_login_at,
                        'last_login_ip' => $request->getClientIp(),
                    ]);
                    
                   $ip=$request->ip();    
                    $location_details = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$ip);
                    
                    DB::table('users_login_location')->updateOrInsert(['user_id' => Auth::check() ? $user->id : null],
                        [
                            'ip_address' => $request->ip(),
                            'city' => $location_details->geoplugin_city,
                            'country' => $location_details->geoplugin_countryCode,
                            'updated_at'=>Carbon::now()->toDateTimeString()
                        ]
                      );
                    
                    \DB::commit();
                    
                     $dal = DB::table('activity_logs')
                        ->where('user_id',$user->id)
                        ->orderBy('id', 'desc')
                        ->skip(1)
                        ->take(1)
                        ->first();
                        if(isset($dal))
                        {
                            $success['last_login']      = $request->last_login_at;
                        }
                        
                    $success['user_id']        = $user->id;
                    $success['first_name']     = $user->first_name;
                    $success['middle_name']     = $user->middle_name;
                    $success['last_name']      = $user->last_name;
                    $success['email']          = $user->email;
                    $success['formattedPhone'] = $user->formattedPhone;
                    if($user->picture) {
                    $success['picture'] = url('/').'/'.'public/user_dashboard/profile/'.$user->picture;
                    }
                    $fullName                  = $user->first_name . ' ' . $user->last_name;
                    $accessToken               = \DB::table('oauth_access_tokens')->where('user_id', $user->id);
                    $getAccessToken            = $accessToken->first(['id']);
                    if (empty($getAccessToken))
                    {
                        $success['token'] = $user->createToken($fullName)->accessToken;
                    }
                    else
                    {
                        $accessToken->delete();
                        $success['token'] = $user->createToken($fullName)->accessToken;
                    }
                    $success['status']      = $this->successStatus;
                    $success['user-status'] = $checkphoneexist->status;
                    $success['last_login'] = $request->last_login_at;
                    return response()->json(['response' => $success], $this->successStatus);
                }
                catch (\Exception $e)
                {
                    \DB::rollBack();
                    $success['status']  = $this->unauthorisedStatus;
                    $success['message'] = $e->getMessage();
                    return response()->json(['response' => $success], $this->unauthorisedStatus);
                }
            }
            else
            {
                //d($request->all(),1);
                $success['status']  = $this->unauthorisedStatus;
                $success['message'] = "Something went wrong!";
                return response()->json(['response' => $success], $this->unauthorisedStatus);
            }
        } else {
        $success['status']  = $this->unauthorisedStatus;
        $success['message'] = "Something went wrong!";
        return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }
    
    public function login(Request $request)
    {
        $loginVia = Setting::where('name', 'login_via')->first(['value'])->value;
        
        if ((isset($loginVia) && $loginVia == 'phone_only'))
        {
            $formattedRequest = $request->email;
            $checkPhone = User::where('phone', $formattedRequest)->where('role_id', request('user_type'))->first();
            if(empty($checkPhone)){
                $checkFormattedPhone = User::where('formattedPhone', '+'.$formattedRequest)->where('role_id', request('user_type'))->first();
                if(empty($checkFormattedPhone)){
                    $success['status']  = $this->unauthorisedStatus;
                    $success['message'] = "Invalid email & credentials";
                    return response()->json(['success' => $success], $this->unauthorisedStatus);
                }else{
                    $requestemail = $checkFormattedPhone->email;
                }
            }else{
                $requestemail = $checkPhone->email;
            }
        }
        else if (isset($loginVia) && $loginVia == 'email_or_phone')
        {
            if (strpos($request->email, '@') !== false)
            {
                $user = User::where('email', $request->email)->where('role_id', request('user_type'))->first();
                if (!$user)
                {
                    $success['status']  = $this->unauthorisedStatus;
                    $success['message'] = "Invalid email & credentials";
                    return response()->json(['success' => $success], $this->unauthorisedStatus);
                }
                $requestemail = $user->email;
            }
            else
            {
                $formattedRequest = $request->email;
                
                $checkPhone = User::where('phone', $formattedRequest)->where('role_id', request('user_type'))->first();
                if(empty($checkPhone)){
                    $checkFormattedPhone = User::where('formattedPhone', '+'.$formattedRequest)->where('role_id', request('user_type'))->first();
                    if(empty($checkFormattedPhone)){
                        $success['status']  = $this->unauthorisedStatus;
                        $success['message'] = "Invalid email & credentials";
                        return response()->json(['success' => $success], $this->unauthorisedStatus);
                    }else{
                        $requestemail = $checkFormattedPhone->email;
                    }
                }else{
                    $requestemail = $checkPhone->email;
                }
            }
        }
        else
        {
            $user = User::where('email', $request->email)->where('role_id', request('user_type'))->first();
            if (!$user)
            {
                $success['status']  = $this->unauthorisedStatus;
                $success['message'] = "Invalid email & credentials";
                return response()->json(['success' => $success], $this->unauthorisedStatus);
            }
            $requestemail = $user->email;
        }
     
        $checkLoggedInUser = User::where('email', $requestemail)->where('role_id', request('user_type'))->first();
        if ($checkLoggedInUser->status == 'Inactive')
        {
            $success['status']      = $this->unauthorisedStatus;
            $success['user_status'] = $checkLoggedInUser->status;
            $success['message']     = 'Your account is inactivated. Please contact support!';
            return response()->json(['response' => $success], $this->unauthorisedStatus);
        }
       
        $checkUserVerificationStatus = $this->checkUserVerificationStatusApi($requestemail);
        if ($checkUserVerificationStatus == true)
        {
            $success['status']  = $this->unverifiedUser;
            $success['message'] = 'We sent you an activation code. Check your email and click on the link to verify.';
            return response()->json(['response' => $success], $this->unverifiedUser);
        }
        else
        {
            $check_user = User::where('email', $requestemail)->where('role_id', request('user_type'))->first();
            if(empty($check_user)){
                $success['status']  = $this->unauthorisedStatus;
                if(request('user_type') == '2'){
                    $success['message'] = "This user is registered as Merchant.";
                }else{
                    $success['message'] = "This user is registered as Customer.";
                }
                
                return response()->json(['response' => $success], $this->unauthorisedStatus);
            }
            
            if (Auth::attempt(['email' => $requestemail, 'password' => request('password'), 'role_id' => request('user_type')]))
            {
                $user             = Auth::user();
                $default_currency = Setting::where('name', 'default_currency')->first(['value']);
                $chkWallet        = Wallet::where(['user_id' => $user->id, 'currency_id' => $default_currency->value])->first();
                try
                {
                    \DB::beginTransaction();

                    if (empty($chkWallet))
                    {
                        $wallet              = new Wallet();
                        $wallet->user_id     = $user->id;
                        $wallet->currency_id = $default_currency->value;
                        $wallet->balance     = 0.00;
                        $wallet->is_default  = 'No';
                        $wallet->save();
                    }
                    $ip = $request->ip();    
                    $location_details = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$ip);
                    $log                  = [];
                    $log['user_id']       = Auth::check() ? $user->id : null;
                    $log['type']          = 'User';
                    $log['ip_address']    = $request->ip();
                    $log['browser_agent'] = $request->device_log;
                    $log['city'] = $location_details->geoplugin_city;
                    $log['country'] = $location_details->geoplugin_countryName;
                    ActivityLog::create($log);
                            
                    
                    DB::table('users_login_location')->updateOrInsert(['user_id' => Auth::check() ? $user->id : null],
                        [
                            'ip_address' => $request->ip(),
                            'city' => $location_details->geoplugin_city,
                            'country' => $location_details->geoplugin_countryCode,
                            'updated_at'=>Carbon::now()->toDateTimeString()
                        ]
                    );

                    $user->user_detail()->update([
                        'last_login_at' => $request->last_login_at,
                        'last_login_ip' => $request->getClientIp(),
                    ]);
                    
                    $check_country = Country::where('name', $user->defaultCountry)->first();
                    
                    \DB::commit();
                    $success['user_id']        = $user->id;
                    $success['caribpay_id']     =$user->carib_id;
                    $success['first_name']     = $user->first_name;
                    $success['middle_name']     = $user->middle_name;
                    $success['last_name']      = $user->last_name;
                    $success['failed_attempt'] = $user->failed_attempt;
                    $success['email']          = $user->email;
                    $success['type']           = $user->type;
                    $success['formattedPhone'] = $user->formattedPhone;
                    $success['picture']        = $user->picture;
                    if($user->picture) {
                    $success['picture'] = url('/').'/'.'public/user_dashboard/profile/'.$user->picture;
                    }
                    $fullName                  = $user->first_name . ' ' . $user->last_name;
                    $accessToken               = \DB::table('oauth_access_tokens')->where('user_id', $user->id);
                    $getAccessToken            = $accessToken->first(['id']);
                    if (empty($getAccessToken))
                    {
                        $success['token'] = $user->createToken($fullName)->accessToken;
                    }
                    else
                    {
                        $accessToken->delete();
                        $success['token'] = $user->createToken($fullName)->accessToken;
                    }
                    User::where(['email' => $requestemail])->where('role_id', request('user_type'))->update(['failed_attempt'=>0]);
                    $success['status']      = $this->successStatus;
                    $success['user-status'] = $checkLoggedInUser->status;
                    $success['last_login'] = $request->last_login_at;
                    $success['carrierCode'] = $user->carrierCode;
                    $success['phone'] = $user->phone;
                    $success['defaultCountry'] = !empty($check_country->id) ? $check_country->id : '';
                    $success['email_status'] = $user->email_status;
                    $success['phone_status'] = $user->phone_status;
                    $success['kyc_status'] = $user->kyc_status;
                    
                    if($request->login_type == '1'){
                        if($user->role_id == '2'){
                            $notify_temp_id = '29';
                            $temp_id = '61';
                            $admin_temp_id = '63';
                        }else{
                            $notify_temp_id = '30';
                            $temp_id = '62';
                            $admin_temp_id = '64';
                        }
        
                        $userdevice = DB::table('devices')->where('user_id', $user->id)->first();
                        if(!empty($userdevice)){
                            $device_language = $userdevice->language;
                        }else{
                            $device_language = getDefaultLanguage();
                        }
                        
                        $twoStepVerification = EmailTemplate::where([
                            'temp_id'     => $temp_id,
                            'language_id' => $device_language,
                            'type'        => 'email',
                        ])->select('subject', 'body')->first();
                        
                        $twoStepVerification_sub = $twoStepVerification->subject;
                        $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                        $twoStepVerification_msg = str_replace('{email}', $user->email, $twoStepVerification_msg);
                        $twoStepVerification_msg = str_replace('{phone}', $user->formattedPhone, $twoStepVerification_msg);
                        $twoStepVerification_msg = str_replace('{country}', $user->defaultCountry, $twoStepVerification_msg);
                        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                        $this->email->sendRegistrationEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg, $request->device_id);
                        
                        $template = NotificationTemplate::where('temp_id', $notify_temp_id)->where('language_id', $device_language)->first();
                        $subject = $template->title;
                        $subheader = $template->subheader;
                        $message = $template->content;
                        
                        $currency = '9';
                        $type = 'new user';
                        
                        $this->helper->sendFirabasePush($subject, $message, $user->id, $currency, $type);
                        
                        Noticeboard::create([
                            'tr_id' => null,
                            'title' => $subject,
                            'content' => $message,
                            'type' => 'push',
                            'content_type' => $type,
                            'user' => $user->id,
                            'sub_header' => $subheader,
                            'push_date' => $request->local_tran_time,
                            'template' => $notify_temp_id,
                            'language' => $device_language,
                        ]);
                        
                        $adminAllowed = Notification::has_permission([1]);
                        foreach($adminAllowed as $admin){
                            $name = User::where('id', $user->id)->first();
                            Notification::insert([
                                'user_id'               => $user->id,
                                'notification_to'       => $admin->agent_id,
                                'notification_type_id'  => 16,
                                'notification_type'     => 'App',
                                'description'           => 'User '.$user->first_name.' has registered',
                                'url_to_go'             => 'admin/users/edit/'.$user->id,
                                'local_tran_time'       => $request->local_tran_time
                            ]);
                        }
                        
                        $admin->email = $this->admin_email;

                        if(!empty($admin->email)){
                        	$twoStepVerification = EmailTemplate::where([
                                'temp_id'     => $admin_temp_id,
                                'language_id' => getDefaultLanguage(),
                                'type'        => 'email',
                            ])->select('subject', 'body')->first();
                           
                            $twoStepVerification_sub = $twoStepVerification->subject;
                            $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                            $twoStepVerification_msg = str_replace('{email}', $user->email, $twoStepVerification_msg);
                            $twoStepVerification_msg = str_replace('{phone}', $user->formattedPhone, $twoStepVerification_msg);
                            $twoStepVerification_msg = str_replace('{country}', $user->defaultCountry, $twoStepVerification_msg);
                            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                            $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
                        }
                    }

                    return response()->json([
                        'response' => $success
                    ], $this->successStatus);
                }
                catch (\Exception $e)
                {
                    \DB::rollBack();
                    $error['status']  = $this->unauthorisedStatus;
                    $error['message'] = $e->getMessage();
                    return response()->json(['response' => $error], $this->unauthorisedStatus);
                }
            }
            else
            {
                if($checkLoggedInUser && $checkLoggedInUser->failed_attempt >= 2) {
                    $success['status']      = $this->unauthorisedStatus;
                    $success['user_status'] = $checkLoggedInUser->status;
                    $success['message']     = 'Your account is inactivated. Please contact support!';
                    return response()->json(['response' => $success], $this->unauthorisedStatus);
                }else
                {
                    User::where('email', $requestemail)->where('role_id', request('user_type'))->increment('failed_attempt',1);
                    $userData = User::where('email',$request->email)->where('role_id', request('user_type'))->first();
                    if(!empty($userData) && $userData->failed_attempt >= 2) {
                        User::where(['email' => $request->email, 'role_id' => request('user_type')])->update(['status'=>"Inactive"]);
                    }
                    $success['failed_attempt']     = $userData->failed_attempt??'';
                    $success['status']  = $this->unauthorisedStatus;
                    $success['message'] = "Invalid email & credentials";
                    return response()->json(['response' => $success], $this->unauthorisedStatus);
                }
            }
        }
    }

    //Check User Verification Status
    protected function checkUserVerificationStatusApi($userEmail)
    {
        $checkLoginDataOfUser = User::where(['email' => $userEmail])->first(['id', 'first_name', 'last_name', 'email', 'status']);
        if (checkVerificationMailStatus() == 'Enabled' && $checkLoginDataOfUser->user_detail->email_verification == 0)
        {
            try
            {
                $verifyUser = VerifyUser::where(['user_id' => $checkLoginDataOfUser->id])->first(['id']);
                if (empty($verifyUser))
                {
                    $verifyUserNewRecord          = new VerifyUser();
                    $verifyUserNewRecord->user_id = $checkLoginDataOfUser->id;
                    $verifyUserNewRecord->token   = str_random(40);
                    $verifyUserNewRecord->save();
                }
                $englishUserVerificationEmailTempInfo = EmailTemplate::where(['temp_id' => 17, 'lang' => 'en', 'type' => 'email'])->select('subject', 'body')->first();
                $userVerificationEmailTempInfo        = EmailTemplate::where([
                    'temp_id'     => 17,
                    'language_id' => getDefaultLanguage(),
                    'type'        => 'email',
                ])->select('subject', 'body')->first();

                if (!empty($userVerificationEmailTempInfo->subject) && !empty($userVerificationEmailTempInfo->body))
                {
                    $userVerificationEmailTempInfo_sub = $userVerificationEmailTempInfo->subject;
                    $userVerificationEmailTempInfo_msg = str_replace('{user}', $checkLoginDataOfUser->first_name . ' ' . $checkLoginDataOfUser->last_name, $userVerificationEmailTempInfo->body);
                }
                else
                {
                    $userVerificationEmailTempInfo_sub = $englishUserVerificationEmailTempInfo->subject;
                    $userVerificationEmailTempInfo_msg = str_replace('{user}', $checkLoginDataOfUser->first_name . ' ' . $checkLoginDataOfUser->last_name, $englishUserVerificationEmailTempInfo->body);
                }
                $userVerificationEmailTempInfo_msg = str_replace('{email}', $checkLoginDataOfUser->email, $userVerificationEmailTempInfo_msg);
                $userVerificationEmailTempInfo_msg = str_replace('{verification_url}', url('user/verify', $checkLoginDataOfUser->verifyUser->token), $userVerificationEmailTempInfo_msg);
                $userVerificationEmailTempInfo_msg = str_replace('{soft_name}', getCompanyName(), $userVerificationEmailTempInfo_msg);

                if (checkAppMailEnvironment())
                {
                    try
                    {
                        $this->email->sendEmail($checkLoginDataOfUser->email, $userVerificationEmailTempInfo_sub, $userVerificationEmailTempInfo_msg);
                        return true;
                    }
                    catch (\Exception $e)
                    {
                        $success['status']  = $this->unauthorisedStatus;
                        $success['message'] = $e->getMessage();
                        return response()->json(['success' => $success], $this->unauthorisedStatus);
                    }
                }
            }
            catch (\Exception $e)
            {
                $success['status']  = $this->unauthorisedStatus;
                $success['message'] = $e->getMessage();
                return response()->json(['response' => $success], $this->unauthorisedStatus);
            }
        }
    }

    public function checkAppUpdate(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'platform'      => 'max:32',
            'app_ver'       => 'max:32',
            'app_build_ver' => 'max:32',
            'api_ver'       => 'max:32',
            'device'        => '',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => $this->unauthorisedStatus,
                'message'=>$validator->errors()
            ], $this->unauthorisedStatus);
        } else {
            
            $platform = $request->platform;
            $app_ver = $request->app_ver;
        
            if($platform == 'android'){
                $name = 'android_version';
                $url = 'android_url';
            }elseif($platform == 'ios'){
                $name = 'ios_version';
                $url = 'ios_url';
            }elseif($platform == 'mpos_android'){
                $name = 'mpos_android_version';
                $url = 'mpos_android_url';
            }elseif($platform == 'mpos_ios'){
                $name = 'mpos_ios_version';
                $url = 'mpos_ios_url';
            }
            
            $app_version = Setting::where('name', $name)->first();
            $app_url = Setting::where('name', $url)->first();
       
            if (!empty($app_ver)) {
                $data['api_ver_playstore'] = $app_version->value;
                if($data['api_ver_playstore'] > $app_ver){
                    $data['update_available']  =  true;
                }else{
                    $data['update_available']  =  false;
                }
                return response()->json([
                    'status' => $this->successStatus,
                    'message' => "Data Fetched Successfully.",
                    'data' => $data, 
                    'forceUpdate' => true,
                    'url' => $app_url->value
                ], $this->successStatus);
            } else {
                return response()->json([
                    'status' => 404,
                    'message'=>"Internal Server Error', 'Error', 'Data Load Failed, Please try again.",
                    'data'=>array()
                ], $this->unauthorisedStatus);
            }
        }
    } 

    public function get_string_between($string, $start, $end) {
        $string = ' '.$string;
        $ini    = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }
     public function getdhiraagutoken()
    {
    //  echo $request->grant_type;
    //  die;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/apitoken");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            "grant_type" => "password",
            "username"   => "conus",
            "password"   => "C0nu5Inv3st&DPay_DHI*21!"

               )));
       // curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/x-www-form-urlencoded",
          
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
       
        $d = json_decode($response);
        $arr = json_decode(json_encode($d), true);
        return $arr['access_token'];
    }
    
    public function allduewaterpayments(Request $request)
    {
        if($request->user_id == '')
        {
            $data['status'] = 'false';
            $data['message'] = 'User Id required!';
            echo json_encode($data);
        }else
        {
            $watercheck           = DB::table('store_user_bills')->where(['service_id'=>'2','user_id'=>$request->user_id])->get();
          
            $postData = [];
              //For Water
            if(count($watercheck)>0)
            {
                 foreach($watercheck as $water)
                 {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/balance");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
                $requestBody = json_encode([
                  "account"    => $water->account_num,
                  "meter"      => $water->meter_num,
                  "bill_type"  => "MWSC"
                ]);
                //dd($requestBody);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                  "Content-Type: application/json",
                   "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
                ]);
                
                 $response = curl_exec($ch);
                 curl_close($ch);
                 $d   = json_decode($response);
                 $arr = json_decode(json_encode($d), true);
                //dd($arr);
                 if(count($arr)>=1)
                 {
                   if (array_key_exists('error', $arr))
                     {
                        $data['water_status'][] = 'false';
                        $data['water_biller_detail'][] = '';
                        $data['water_message'][] = $arr['error'];
                        $data['water_data'][] = array();
                     }
                    else
                      {
                        $watericon  = DB::table('dhiraagu_services')->where(['service_id'=>'2'])->first();
                        $data['water_status'][]  = 'true';
                        $data['water_message'][] = 'Data get successfully!';
                        $data['water_biller_detail'][] = array('bill_id'=>$water->id,'icon'=>$water->logo??'','bill_name'=>$water->bill_name,'service_id'=>$water->service_id,'account'=>$water->account_num??'','meter'=>$water->meter_num??'');
                        $data['water_data'][] = json_decode($response);
                      }
                   }
                 }
             }else
             {
                $data['water_status'] = 'false';
                $data['water_biller_detail'] = 'nothing';
                $data['water_message'] = 'Not getting any data';
                $data['water_data'] = array(0);
             }
            for ($i=0; $i < count($data['water_data']); $i++) { 
                    $postData['all_water_data'][] = [
                        'type'              => 1,
                        'water_status'   => $data['water_status'][$i],
                        'water_biller_detail' => $data['water_biller_detail'][$i],
                        'water_data' => $data['water_data'][$i],
                   	];
                }
        echo json_encode($postData);
        }
    }
    //For Testiong
     public function allduebillpayments_test(Request $request)
    {
        if($request->user_id == '')
        {
            $data['status'] = 'false';
            $data['message'] = 'User Id required!';
            echo json_encode($data);
        }else
        {
            $watercheck           = DB::table('store_user_bills')->where(['service_id'=>'2','user_id'=>$request->user_id])->get();
            $electriccheck        = DB::table('store_user_bills')->where(['service_id'=>'1','user_id'=>$request->user_id])->get();
            $ooredoo_postpaid     = DB::table('store_user_bills')->where(['service_id'=>'9','user_id'=>$request->user_id])->get();
            $dhiraagu_postpaid    = DB::table('store_user_bills')->where(['service_id'=>'7','user_id'=>$request->user_id])->get();
            $postData = [];
              //For Water
            if(count($watercheck)>0)
            {
                
                // array of curl handles
                $multiCurl = array();
                // data to be returned
                $result = array();
                // multi handle
                $mh = curl_multi_init();
                foreach ($watercheck as $i => $water) {
                  // URL from which data will be fetched
                 
                  $multiCurl[$i] = curl_init();
                    $ch = curl_init();
                    curl_setopt($multiCurl[$i], CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/balance");
                    curl_setopt($multiCurl[$i], CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($multiCurl[$i], CURLOPT_HEADER, FALSE);
                    curl_setopt($multiCurl[$i], CURLOPT_POST, TRUE);
                    curl_setopt($multiCurl[$i], CURLOPT_SSL_VERIFYPEER, FALSE);
                   
                    $requestBody = json_encode([
                      "account"    => $water->account_num,
                      "meter"      => $water->meter_num,
                      "bill_type"  => "MWSC"
                    ]);
                    //dd($requestBody);
                    curl_setopt($multiCurl[$i], CURLOPT_POSTFIELDS, $requestBody);
                    curl_setopt($multiCurl[$i], CURLOPT_HTTPHEADER, [
                      "Content-Type: application/json",
                       "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
                    ]);
                }
                    $index=null;
                    do {
                      curl_multi_exec($mh,$index);
                    } while($index > 0);
                    // get content and remove handles
                    foreach($multiCurl as $k => $ch) {
                      $result[$k] = curl_multi_getcontent($ch);
                      curl_multi_remove_handle($mh, $ch);
                    }
                // close
                curl_multi_close($mh);
                dd($result);
            }else
             {
                $data['dhiraagu_status'] = 'false';
                $data['dhiraagu_biller_detail'] = 'nothing';
                $data['dhiraagu_message'] = 'Not getting any data';
                $data['dhiraagu_data'] = array(0);
             }   
                
             //For Dhiraagu postpaid
            if(count($dhiraagu_postpaid)>0)
            {
                foreach($dhiraagu_postpaid as $dhiraagu)
                 {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/mfs/bills/pending/".$dhiraagu->number);
            
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       "Content-Type: application/x-www-form-urlencoded",
                      "Authorization: Bearer ".$this->getdhiraagutoken() ));
                    
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $d = json_decode($response);
                    $arr = json_decode(json_encode($d), true);
                   
                    if (array_key_exists("message",$arr))
                      {
                        $data['dhiraagu_status'][] = 'false';
                        $data['dhiraagu_biller_detail'][] = '';
                        $data['dhiraagu_message'][] = $arr['message'];
                        $data['dhiraagu_data'][] = array();
                        //echo json_encode($data);
                      }else
                      {
                        $data['dhiraagu_status'][] = 'true';
                        $data['dhiraagu_biller_detail'][] = array('bill_id'=>$dhiraagu->id,'bill_name'=>$dhiraagu->bill_name,'service_id'=>$dhiraagu->service_id,'number'=>$dhiraagu->number??'');
                        $data['dhiraagu_message'][] = 'Successfully dhiraagu Pending Bill list!';
                        $data['dhiraagu_data'][] = $arr;
                        //echo json_encode($data);
                       
                      }
                      $dhir['dhiraagu_all_data'][] = $data;
                 }
                 
             }else
             {
                $data['dhiraagu_status'] = 'false';
                $data['dhiraagu_biller_detail'] = 'nothing';
                $data['dhiraagu_message'] = 'Not getting any data';
                $data['dhiraagu_data'] = array(0);
             }
             
              for ($i=0; $i < count($data['dhiraagu_data']); $i++) { 
                    $postData['all_dhiraagu_data'][] = [
                        'type'              => 2,
                        'dhiraagu_status'   => $data['dhiraagu_status'][$i],
                        'dhiraagu_biller_detail' => $data['dhiraagu_biller_detail'][$i],
                        'dhiraagu_data' => $data['dhiraagu_data'][$i],
                   	];
                }
                 
             //For Ooredoo postpaid
            if(count($ooredoo_postpaid)>0)
            {
                 foreach($ooredoo_postpaid as $ooredoo)
                 {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/balance/ooredoo");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
                $requestBody = json_encode([
                  "account"    => $ooredoo->account_num,
                  "bill_type"  => "POSTPAID"
                ]);
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                  "Content-Type: application/json",
                   "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
                ]);
                
                $response = curl_exec($ch);
                //dd($response);
                curl_close($ch);
                $d = json_decode($response);
                $arr = json_decode(json_encode($d), true);
                if (array_key_exists("error",$arr))
                  {
                    $data['ooredoo_status'][] = 'false';
                    $data['ooredoo_biller_detail'][] = '';
                    $data['ooredoo_message'][] = $arr['error'];
                    $data['ooredoo_data'][] = array();
                   // echo json_encode($data);
                  }else
                  {
                    
                    $data['ooredoo_status'][]   = 'true';
                    $data['ooredoo_message'][]  = 'Successfully get ooredoo Bill list!';
                    $data['ooredoo_biller_detail'][] = array('bill_id'=>$ooredoo->id,'bill_name'=>$ooredoo->bill_name,'service_id'=>$ooredoo->service_id,'account'=>$ooredoo->account_num??'');
                    $data['ooredoo_data'][] = $arr;
                  }
                 }
             }else
             {
                $data['ooredoo_status'] = 'false';
                $data['ooredoo_biller_detail'] = 'nothing';
                $data['ooredoo_message'] = 'Not getting any data';
                $data['ooredoo_data'] = array(0);
             }
            
              for ($i=0; $i < count($data['ooredoo_data']); $i++) { 
                    $postData['all_ooredoo_data'][] = [
                        'type'              => 3,
                        'ooredoo_status'   => $data['ooredoo_status'][$i],
                        'ooredoo_biller_detail' => $data['ooredoo_biller_detail'][$i],
                        'ooredoo_data' => $data['ooredoo_data'][$i],
                   	];
                }
         
           
             //For Electricity
            if(count($electriccheck)>0)
            {
                foreach($electriccheck as $electric)
                 {
                 $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/balance");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
                $requestBody = json_encode([
                  "account"    => $electric->account_num,
                  "mobile"      => $electric->number,
                  "bill_type"  => "STELCO"
                ]);
                //dd($requestBody);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                  "Content-Type: application/json",
                   "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
                ]);
                
                $response = curl_exec($ch);
                curl_close($ch);
                 $d   = json_decode($response);
                 $arr = json_decode(json_encode($d), true);
                //  print_r($arr);
                //  die;
                 if(is_array($arr))
                 {
                   if (array_key_exists('error', $arr))
                     {
                        $data['electric_status'][] = 'false';
                        $data['electric_biller_detail'][] = '';
                        $data['electric_message'][] = $arr['error'];
                        $data['electric_data'][] = array();
                     }
                    else
                      {
                        $electricicon    = DB::table('dhiraagu_services')->where(['service_id'=>'1'])->first();
                        $data['electric_status'][] = 'true';
                        $data['electric_message'][] = 'Data get successfully!';
                        $data['electric_biller_detail'][] = array('bill_id'=>$electric->id,'icon'=>$electric->logo??'','bill_name'=>$electric->bill_name,'service_id'=>$electric->service_id,'account'=> $electric->account_num??'','number'=> $electric->number??'');
                        $data['electric_data'][] = json_decode($response);
                      }
                 }
                 }
            }else
             {
                $data['electric_status'] = 'false';
                $data['electric_biller_detail'] = 'nothing';
                $data['electric_message'] = 'Not getting any data';
                $data['electric_data'] = array(0);
             }
          for ($i=0; $i < count($data['electric_data']); $i++) { 
                    $postData['all_electric_data'][] = [
                        'type'              => 4,
                        'electric_status'   => $data['electric_status'][$i],
                        'electric_biller_detail' => $data['electric_biller_detail'][$i],
                        'electric_data' => $data['electric_data'][$i],
                   	];
                }
           
            echo json_encode($postData);
        
        }    
        
       
    }
    
    
    //For Testiong
     public function allduebillpayments(Request $request)
    {
        if($request->user_id == '')
        {
            $data['status'] = 'false';
            $data['message'] = 'User Id required!';
            echo json_encode($data);
        }else
        {
           // $watercheck           = DB::table('store_user_bills')->where(['service_id'=>'2','user_id'=>$request->user_id])->get();
            $electriccheck        = DB::table('store_user_bills')->where(['service_id'=>'1','user_id'=>$request->user_id])->get();
            $ooredoo_postpaid     = DB::table('store_user_bills')->where(['service_id'=>'9','user_id'=>$request->user_id])->get();
            $dhiraagu_postpaid    = DB::table('store_user_bills')->where(['service_id'=>'7','user_id'=>$request->user_id])->get();
            $postData = [];
             
             //For Dhiraagu postpaid
            if(count($dhiraagu_postpaid)>0)
            {
                foreach($dhiraagu_postpaid as $dhiraagu)
                 {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://testapi.dhiraagu.com.mv/v1/mfs/bills/pending/".$dhiraagu->number);
            
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       "Content-Type: application/x-www-form-urlencoded",
                      "Authorization: Bearer ".$this->getdhiraagutoken() ));
                    
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $d = json_decode($response);
                    $arr = json_decode(json_encode($d), true);
                   
                    if (array_key_exists("message",$arr))
                      {
                        $data['dhiraagu_status'][] = 'false';
                        $data['dhiraagu_biller_detail'][] = '';
                        $data['dhiraagu_message'][] = $arr['message'];
                        $data['dhiraagu_data'][] = array();
                        //echo json_encode($data);
                      }else
                      {
                        $data['dhiraagu_status'][] = 'true';
                        $data['dhiraagu_biller_detail'][] = array('bill_id'=>$dhiraagu->id,'bill_name'=>$dhiraagu->bill_name,'service_id'=>$dhiraagu->service_id,'number'=>$dhiraagu->number??'');
                        $data['dhiraagu_message'][] = 'Successfully dhiraagu Pending Bill list!';
                        $data['dhiraagu_data'][] = $arr;
                        //echo json_encode($data);
                       
                      }
                      $dhir['dhiraagu_all_data'][] = $data;
                 }
                 
             }else
             {
                $data['dhiraagu_status'] = 'false';
                $data['dhiraagu_biller_detail'] = 'nothing';
                $data['dhiraagu_message'] = 'Not getting any data';
                $data['dhiraagu_data'] = array(0);
             }
             
              for ($i=0; $i < count($data['dhiraagu_data']); $i++) { 
                    $postData['all_dhiraagu_data'][] = [
                        'type'              => 2,
                        'dhiraagu_status'   => $data['dhiraagu_status'][$i],
                        'dhiraagu_biller_detail' => $data['dhiraagu_biller_detail'][$i],
                        'dhiraagu_data' => $data['dhiraagu_data'][$i],
                   	];
                }
                 
             //For Ooredoo postpaid
            if(count($ooredoo_postpaid)>0)
            {
                 foreach($ooredoo_postpaid as $ooredoo)
                 {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/balance/ooredoo");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
                $requestBody = json_encode([
                  "account"    => $ooredoo->account_num,
                  "bill_type"  => "POSTPAID"
                ]);
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                  "Content-Type: application/json",
                   "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
                ]);
                
                $response = curl_exec($ch);
                //dd($response);
                curl_close($ch);
                $d = json_decode($response);
                $arr = json_decode(json_encode($d), true);
                if (array_key_exists("error",$arr))
                  {
                    $data['ooredoo_status'][] = 'false';
                    $data['ooredoo_biller_detail'][] = '';
                    $data['ooredoo_message'][] = $arr['error'];
                    $data['ooredoo_data'][] = array();
                   // echo json_encode($data);
                  }else
                  {
                    
                    $data['ooredoo_status'][]   = 'true';
                    $data['ooredoo_message'][]  = 'Successfully get ooredoo Bill list!';
                    $data['ooredoo_biller_detail'][] = array('bill_id'=>$ooredoo->id,'bill_name'=>$ooredoo->bill_name,'service_id'=>$ooredoo->service_id,'account'=>$ooredoo->account_num??'');
                    $data['ooredoo_data'][] = $arr;
                  }
                 }
             }else
             {
                $data['ooredoo_status'] = 'false';
                $data['ooredoo_biller_detail'] = 'nothing';
                $data['ooredoo_message'] = 'Not getting any data';
                $data['ooredoo_data'] = array(0);
             }
            
              for ($i=0; $i < count($data['ooredoo_data']); $i++) { 
                    $postData['all_ooredoo_data'][] = [
                        'type'              => 3,
                        'ooredoo_status'   => $data['ooredoo_status'][$i],
                        'ooredoo_biller_detail' => $data['ooredoo_biller_detail'][$i],
                        'ooredoo_data' => $data['ooredoo_data'][$i],
                   	];
                }
         
           
             //For Electricity
            if(count($electriccheck)>0)
            {
                foreach($electriccheck as $electric)
                 {
                 $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://ssh.onthewifi.com/uat/v1/bills/balance");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
                $requestBody = json_encode([
                  "account"    => $electric->account_num,
                  "mobile"      => $electric->number,
                  "bill_type"  => "STELCO"
                ]);
                //dd($requestBody);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                  "Content-Type: application/json",
                   "X-API-Key: q2nZGDQDoymZn9uN2BzDRUNKwBcn4aDWlPrzNLgsL6rJ*95pXS"
                ]);
                
                $response = curl_exec($ch);
                curl_close($ch);
                 $d   = json_decode($response);
                 $arr = json_decode(json_encode($d), true);
                //  print_r($arr);
                //  die;
                 if(is_array($arr))
                 {
                   if (array_key_exists('error', $arr))
                     {
                        $data['electric_status'][] = 'false';
                        $data['electric_biller_detail'][] = '';
                        $data['electric_message'][] = $arr['error'];
                        $data['electric_data'][] = array();
                     }
                    else
                      {
                        $electricicon    = DB::table('dhiraagu_services')->where(['service_id'=>'1'])->first();
                        $data['electric_status'][] = 'true';
                        $data['electric_message'][] = 'Data get successfully!';
                        $data['electric_biller_detail'][] = array('bill_id'=>$electric->id,'icon'=>$electric->logo??'','bill_name'=>$electric->bill_name,'service_id'=>$electric->service_id,'account'=> $electric->account_num??'','number'=> $electric->number??'');
                        $data['electric_data'][] = json_decode($response);
                      }
                 }
                 }
            }else
             {
                $data['electric_status'] = 'false';
                $data['electric_biller_detail'] = 'nothing';
                $data['electric_message'] = 'Not getting any data';
                $data['electric_data'] = array(0);
             }
          for ($i=0; $i < count($data['electric_data']); $i++) { 
                    $postData['all_electric_data'][] = [
                        'type'              => 4,
                        'electric_status'   => $data['electric_status'][$i],
                        'electric_biller_detail' => $data['electric_biller_detail'][$i],
                        'electric_data' => $data['electric_data'][$i],
                   	];
                }
           
            echo json_encode($postData);
        
        }    
        
       
    }
  
   public function allOffers(Request $request)
   {
        $user_id = $request->user_id;
        $lang_id = $request->lang_id;
        $users = DB::table('users')->where('id', $user_id)->first();
        $offer = DB::table('customer_offers')->where('language',$lang_id)->get();
        $num = 0;
      //  dd($offer);
      if(count($offer)>0)
      {
         foreach($offer as $value)
        {
            if((!empty($value->expire_date_time))&&($value->expire_date_time >= date('Y-m-d h-i-s')))
            {   
                 $new = AppPage::where('id', $value->app_page)->where('status', 'Active')->first(['app_page']);
                  
                  if($value->app_redirect==1){
                      $value->app_page_name = $new->app_page; 
                  }elseif($value->app_redirect==2){
                      $value->offer_url = $value->offer_url;
                  }
                  $arr[] = $value;
            }else
            {
                $arr[] = array();
            }
        }
       
       foreach($arr as $value)
       {
           if(!empty($value))
           {
           
          if(is_array(json_decode($value->read_users)))
           {
               $d = json_decode($value->read_users);
               if(in_array($user_id,$d))
               {
                   $value->read_status = 1;
               }else
               {
                 $value->read_status = 1;
               }
           }else
           {
             $value->read_status = 1;
           }}
       }
       
       
       $newarr = [];
       foreach($arr as $count)
       {
           if(!empty($count))
           {
              $newarr[] = $count;
              $de =  json_decode(json_encode($count->read_status),true);
              if($de === 0)
              {
                  $num = $num+1;
              }else
              {
                  $num = $num;
              }
           }
       }
       
       if(count($newarr)>0)
       {
            $data['status']  = true;
            $data['count'] = $num;
            $data['message'] = 'Getting all offers!';
            $data['data']    = $newarr;
            
       }else
       {
            $data['status']  = false;
            $data['count'] = $count;
            $data['message'] = 'cant find any offer!';
            $data['data']    = array();
       }
       
      }else
      {
            $data['status']  = false;
          
            $data['message'] = 'cant find any offer!';
            $data['data']    = array();
      }
       echo json_encode($data);
       
   }
   
   public function readOffers(Request $request)
   {
      
       $check = DB::table('customer_offers')->where('id',$request->offer_id)->first();
       if(!empty($check))
       {
           if(is_array(json_decode($check->read_users)))
           {
          $users = $check->read_users;
         // dd(json_decode($users));
          if (in_array($request->user_id, json_decode($users)))
          {
              // die($users);
           $data['status']  = true;
           $data['message'] = 'Allready read offer!';
           echo json_encode($data);
          }
        else
          {
              $m = json_decode($users);
              array_push($m,$request->user_id);
             //  dd($m);
             $offer = DB::table('customer_offers')->where('id',$request->offer_id)->update(['read_users'=>json_encode($m)]);
             $data['status']  = true;
             $data['message'] = 'Offer read successfully!';
             echo json_encode($data);
          }
           }else
           {
              $m = [];
              array_push($m,$request->user_id);
             //  dd($m);
             $offer = DB::table('customer_offers')->where('id',$request->offer_id)->update(['read_users'=>json_encode($m)]);
             $data['status']  = true;
             $data['message'] = 'Offer read successfully!';
             echo json_encode($data);
           }
           //dd(json_decode($d));
       }
   }
   
    public function partner_details()
    {
        $general = Setting::where('type', 'cards')->get()->toArray();
        $card    = $this->helper->key_value('name', 'value', $general);
        
        return response()->json([
            'status'  => $this->successStatus,
            'message' => 'Partner details fetched successfully.',
            'data'    => $card
        ]);
    }
    
    public function check_maintainance(Request $request)
    {
        $user_type = $request->user_type;
        $device_id = $request->device_id;
        
        $today_date = Carbon::now()->format('Y-m-d');
        $today_time = Carbon::now()->format('h:i'); // 05:57
        
        if($user_type == '2'){
            $devices = DB::table('devices')->where('user_type', '3')->groupBy('device_id')->get();
        }if($user_type == '3'){
            $devices = DB::table('devices')->groupBy('device_id')->get();
        }
        
        $languages = Language::where('status', 'Active')->orderBy('id', 'asc')->get();
        $template = LanguageContent::where('string', 'Scheduled_app_maintenance')->first();
        $device = DB::table('devices')->where('user_type', $user_type)->where('device_id', $device_id)->first();
        $setting = MaintenanceSetting::where('date', $today_date)->where('from_time', '<=', $today_time)->where('to_time', '>=', $today_time)->first();
        
        if(!empty($setting)){
            
            $ondate = Carbon::parse($setting->date)->format('d M');
            $fromtime = Carbon::parse($setting->from_time)->format('d M Y, h:i:s A');
            $totime = Carbon::parse($setting->to_time)->format('d M Y, h:i:s A');
            $zone = Carbon::now();
            
            if(empty($device)){
                
                $data['subject'] = $template->en;
                $data['message'] = $setting->message_en;
                $data['date'] = $ondate;
                $data['fromtime'] = $fromtime;
                $data['totime'] = $totime;
                $data['timezone'] = $zone->tzName.'+0';
                
                return response()->json([
                    'status'  => $this->successStatus,
                    'message' => 'Maintainance break available.',
                    'data'    => $data
                ]);
            }
        
            foreach($languages as $language){
                if($device->language == $language->id){
                    $short_code = $language['short_name'];
                    $subject = $template->$short_code;
                    $message = 'message_'.$short_code;
                    if(!empty($setting->$message)){
                        $newmessage = $setting->$message;
                    }else{
                        $newmessage = $setting->message_en;
                    }
                    
                    $check_message = 'On '.$ondate.' from '.$fromtime.' to '.$totime.' ('.$zone->tzName.'+0), '.$newmessage;
                }
            }
            
            $data['subject'] = $subject;
            $data['message'] = $newmessage;
            $data['date'] = $ondate;
            $data['fromtime'] = $fromtime;
            $data['totime'] = $totime;
            $data['timezone'] = $zone->tzName.'+0';
            
            if($setting->user_type == '1' && $user_type == '2'){
                return response()->json([
                    'status'  => $this->successStatus,
                    'message' => 'Maintainance break available.',
                    'data'    => $data
                ]);
            }elseif($setting->user_type == '2' && $user_type == '3'){
                return response()->json([
                    'status'  => $this->successStatus,
                    'message' => 'Maintainance break available.',
                    'data'    => $data
                ]);
            }elseif($setting->user_type == '3'){
                return response()->json([
                    'status'  => $this->successStatus,
                    'message' => 'Maintainance break available.',
                    'data'    => $data
                ]);
            }
        }else{
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => 'Maintainance break not available.',
                'data'    => null
            ]);
        }
    }
   
}
