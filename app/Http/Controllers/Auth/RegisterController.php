<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Setting;
use App\Models\User;
use App\Models\QrCode;
use App\Models\VerifyUser;
use App\Models\Notification;
use Auth;
use DB;
use Illuminate\Http\Request;
use Validator;
use Session;
use App\Models\AppStoreCredentials;
use App\Models\EmailTemplate;
use App\Models\Country;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    protected $helper;
    protected $email;
    protected $user;
    protected $notification;

    public function __construct()
    {
        $this->helper   = new Common();
        $this->email    = new EmailController();
        $this->user     = new User();
        $this->notify   = new Notification();
        
        $setting = Setting::first();
        $this->admin_email = $setting->notification_email;
    }

    public function create()
    {
        $data['title'] = 'Register';

        if (Auth::check())
        {
            return redirect('/dashboard');
        }
        $data['countries'] = Country::where('status', '1')->get();
        return view('frontend.auth.register', $data);
    }

    public function store(Request $request)
    {
        if ($_POST)
        {
            $rules = array(
                'first_name'            => 'required',
                'last_name'             => 'required',
                'email'                 => 'required|email',
                'phone'                 => 'required',
                'usertype'              => 'required',
                'password'              => 'required|confirmed',
                'password_confirmation' => 'required',
            );

            $fieldNames = array(
                'first_name'            => 'First Name',
                'last_name'             => 'Last Name',
                'email'                 => 'Email',
                'phone'                 => 'Phone',
                'usertype'              => 'User Type',
                'password'              => 'Password',
                'password_confirmation' => 'Confirm Password',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);
            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                $check_email = User::where(['email' => $request->email, 'role_id' => $request->usertype])->exists();
                if ($check_email)
                {
                    $this->helper->one_time_message('error', 'The email has already been taken!');
                    return redirect('/login');
                }
                
                if (isset($request->carrierCode))
                {
                    $chec_phone = User::where(['phone' => $request->phone, 'carrierCode' => '+'.$request->carrierCode, 'role_id' => $request->usertype])->first();
                }
                else
                {
                    $chec_phone = User::where(['phone' => $request->phone, 'role_id' => $request->usertype])->first();
                }
        
                if ($chec_phone)
                {
                    $this->helper->one_time_message('error', 'The phone number has already been taken!');
                    return redirect('/login');
                }
                
                $default_currency = Setting::where('name', 'default_currency')->first(['value']);

                try
                {
                    \DB::beginTransaction();
                    
                    if($request->usertype == '2'){
                        $user_type = 'user';
                    }else{
                        $user_type = 'merchant';
                    }
                    
                    $user = $this->user->createNewUser($request, $user_type);
                    RoleUser::insert(['user_id' => $user->id, 'role_id' => $user->role_id, 'user_type' => 'User']);
                    
                    $adminAllowed = $this->notify->has_permission([7]);
                    
                    foreach($adminAllowed as $admin){
                        Notification::insert([
                            'user_id'               => $user->id,
                            'notification_to'       => $admin->agent_id,
                            'notification_type_id'  => 7,
                            'notification_type'     => 'Web',
                            'description'           => 'New '.$user->type.' registered. User ID: '.$user->id.'.
                            Name: '.$user->first_name.' '.$user->last_name,
                            'url_to_go'             => 'admin/users/edit/'.$user->id
                        ]);
                    }

                    $this->user->createUserDetail($request,$user->id);
                    $this->user->createUserDefaultWallet($user->id, $default_currency->value);
                    $this->saveUserQrCode($user);

                    $userEmail          = $user->email;
                    $userFormattedPhone = $user->formattedPhone;

                    $this->user->processUnregisteredUserTransfers($userEmail, $userFormattedPhone, $user, $default_currency->value);
                    $this->user->processUnregisteredUserRequestPayments($userEmail, $userFormattedPhone, $user, $default_currency->value);

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
                                    $this->helper->one_time_message('success', __('We sent you an activation code. Check your email and click on the link to verify.'));
                                    return redirect('/login');
                                }
                                catch (\Exception $e)
                                {
                                    \DB::rollBack();
                                    $this->helper->one_time_message('error', $e->getMessage());
                                    return redirect('/login');
                                }
                            }
                        }
                    }

                    \DB::commit();
                    $this->helper->one_time_message('success', __('Registration Successful!'));
                    return redirect('/login');
                }
                catch (\Exception $e)
                {
                    \DB::rollBack();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect('/register');
                }
            }
        }
    }

    protected function saveUserQrCode($user)
    {
        $qrCode = QrCode::where(['object_id' => $user->id, 'object_type' => 'user', 'status' => 'Active'])->first(['id']);
        if (empty($qrCode))
        {
            $createInstanceOfQrCode              = new QrCode();
            $createInstanceOfQrCode->object_id   = $user->id;
            $createInstanceOfQrCode->object_type = 'user';
            if (!empty($user->formattedPhone))
            {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . $user->formattedPhone . '-' . Str::random(6));
            }
            else
            {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . Str::random(6));
            }
            $createInstanceOfQrCode->status = 'Active';
            $createInstanceOfQrCode->save();
        }
    }

    public function verifyUser($token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if (isset($verifyUser))
        {
            if (!$verifyUser->user->user_detail->email_verification)
            {
                $verifyUser->user->user_detail->email_verification = 1;
                $verifyUser->user->user_detail->save();
                $status = __("Your account is verified. You can now login.");
            }
            else
            {
                $status = __("Your account is already verified. You can now login.");
            }
        }
        else
        {
            return redirect('/login')->with('warning', __("Sorry your email cannot be identified."));
        }
        return redirect('/login')->with('status', $status);
    }

    public function checkUserRegistrationEmail(Request $request)
    {
        $email = $request->email;
        $usertype = $request->usertype;
        
        $user = User::where(['email' => $email, 'role_id' => $usertype])->exists();
        if ($user)
        {
            $data['status'] = true;
            $data['fail']   = __('The email has already been taken!');
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "Email Available!";
        }
        return json_encode($data);
    }

    public function registerDuplicatePhoneNumberCheck(Request $request)
    {
        $carrierCode = $request->carrierCode;
        $phone = $request->phone;
        $usertype = $request->usertype;
        
        if (isset($carrierCode))
        {
            $user = User::where(['phone' => $phone, 'carrierCode' => '+'.$carrierCode, 'role_id' => $usertype])->first();
        }
        else
        {
            $user = User::where(['phone' => $phone, 'role_id' => $usertype])->first();
        }

        if ($user)
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
    
    public function checkDuplicateUser(Request $request)
    {
        $email = $request->email;
        $carrierCode = $request->carrierCode;
        $phone = $request->phone;
        $usertype = $request->usertype;
        
        $check_email = User::where(['email' => $email, 'role_id' => $usertype])->exists();
        if ($check_email)
        {
            $data['status'] = true;
            $data['fail']   = __('The email has already been taken!');
            return json_encode($data);
        }
        
        if (isset($carrierCode))
        {
            $check_phone = User::where(['phone' => $phone, 'carrierCode' => '+'.$carrierCode, 'role_id' => $usertype])->first();
        }
        else
        {
            $check_phone = User::where(['phone' => $phone, 'role_id' => $usertype])->first();
        }

        if ($check_phone)
        {
            $data['status'] = true;
            $data['fail']   = "The phone number has already been taken!";
            return json_encode($data);
        }
        
        $data['status']  = false;
        $data['success'] = "The User is Available!";
        return json_encode($data);
    }
    
    public function mpos_create()
    {
        $data['title'] = 'Virtual Mobile Terminal (mPOS)';

        if (Auth::check())
        {
            return redirect('/dashboard');
        }
        $data['checkMerchantRole'] = $checkMerchantRole = Role::where(['user_type' => 'User', 'customer_type' => 'merchant', 'is_default' => 'Yes'])->first(['id']);
        $data['checkUserRole']     = $checkUserRole     = Role::where(['user_type' => 'User', 'customer_type' => 'user', 'is_default' => 'Yes'])->first(['id']);
        return view('frontend.auth.mposregister', $data);
    }
    
    public function mpos_success()
    {
        $data['title'] = 'Virtual Mobile Terminal (mPOS)';
        $user = Session::get('user');
        $data['user_detail'] = User::where('id', $user->id)->first();
        $data['app_store'] = AppStoreCredentials::where('has_app_credentials', 'Yes')->get();
        return view('frontend.auth.mposregistersuccess', $data);
    }

    public function mpos_store(Request $request)
    {
        if ($_POST)
        {
             //dd($request->all());

            $rules = array(
                'first_name'            => 'required',
                'last_name'             => 'required',
                'email'                 => 'required|email|unique:users,email',
                'phone'                 => 'required|unique:users,phone',
                'password'              => 'required|confirmed',
                'password_confirmation' => 'required',
            );

            $fieldNames = array(
                'first_name'            => 'First Name',
                'last_name'             => 'Last Name',
                'email'                 => 'Email',
                'phone'                 => 'Phone',
                'password'              => 'Password',
                'password_confirmation' => 'Confirm Password',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);
            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                $default_currency = Setting::where('name', 'default_currency')->first(['value']);

                try
                {
                    \DB::beginTransaction();

                    // Create user
                    $user = $this->user->createNewUser($request, 'admin');

                    // Assign user type and role to new user
                    RoleUser::insert(['user_id' => $user->id, 'role_id' => $user->role_id, 'user_type' => 'User']);
                    
                    $adminAllowed = $this->notify->has_permission([7]);
                    
                    foreach($adminAllowed as $admin){
                        Notification::insert([
                            'user_id'               => $user->id,
                            'notification_to'       => $admin->agent_id,
                            'notification_type_id'  => 7,
                            'notification_type'     => 'Web',
                            'description'           => 'New '.$user->type.' registered. User ID: '.$user->id.'.
                            Name: '.$user->first_name.' '.$user->last_name,
                            'url_to_go'             => 'admin/users/edit/'.$user->id
                        ]);
                    }

                    // Create user detail
                    $this->user->createUserDetail($request,$user->id);

                    // Create user's default wallet
                    $this->user->createUserDefaultWallet($user->id, $default_currency->value);
                    
                    //Entry for User's QrCode Generation - starts
                    $this->saveUserQrCode($user);

                    $userEmail          = $user->email;
                    $userFormattedPhone = $user->formattedPhone;

                    // Process Registered User Transfers
                    $this->user->processUnregisteredUserTransfers($userEmail, $userFormattedPhone, $user, $default_currency->value);

                    // Process Registered User Request Payments
                    $this->user->processUnregisteredUserRequestPayments($userEmail, $userFormattedPhone, $user, $default_currency->value);

                    // Email verification
                    $emainVerificationArr = $this->user->processMerchantEmailVerification($user);
                    try
                    {
                        $this->email->sendEmail($emainVerificationArr['email'], $emainVerificationArr['subject'], $emainVerificationArr['message']);
                        
                        $admin->email = $this->admin_email;
        	
                    	if(!empty($admin->email)){
                        	$twoStepVerification = EmailTemplate::where([
                                'temp_id'     => 53,
                                'language_id' => getDefaultLanguage(),
                                'type'        => 'email',
                            ])->select('subject', 'body')->first();
                           
                            $twoStepVerification_sub = $twoStepVerification->subject;
                            $twoStepVerification_msg = str_replace('{name}', $user->first_name.' '.$user->last_name, $twoStepVerification->body);
                            $twoStepVerification_msg = str_replace('{email}', $user->email, $twoStepVerification_msg);
                            $twoStepVerification_msg = str_replace('{phone}', $user->formattedPhone, $twoStepVerification_msg);
                            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                            $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
                    	}

                        \DB::commit();
                        $this->helper->one_time_message('success', __('We sent you an email. Check your email for details.'));
                        Session::put('user', $user);
                        return redirect('/mpos-register-success');
                    }
                    catch (\Exception $e)
                    {
                        \DB::rollBack();
                        $this->helper->one_time_message('error', $e->getMessage());
                        Session::put('user', $user);
                        return redirect('/mpos-register-success');
                    }
                    
                    //email_verification - ends
                    \DB::commit();
                    $this->helper->one_time_message('success', __('Registration Successful!'));
                    Session::put('user', $user);
                    return redirect('/mpos-register-success');
                }
                catch (\Exception $e)
                {
                    \DB::rollBack();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect('/mpos-register');
                }
            }
        }
    }
}
