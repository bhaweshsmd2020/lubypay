<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\ActivityLog;
use App\Models\DeviceLog;
use App\Models\EmailTemplate;
use App\Models\Preference;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\VerifyUser;
use App\Models\Wallet;
use App\Models\Notification;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Session;

class LoginController extends Controller
{
    protected $helper;
    protected $email;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email  = new EmailController();
    }

    public function index()
    {
        $data['title'] = 'Login';

        if (Auth::check())
        {
            return redirect('/dashboard');
        }
        $general         = Setting::where(['type' => 'general'])->get(['value', 'name'])->toArray();
        $data['setting'] = $setting = $this->helper->key_value('name', 'value', $general);

        return view('frontend.auth.login', $data);
    }

    public function authenticate(Request $request)
    {
        if ($request->has_captcha == 'Enabled' && $request->login_via == 'email_only')
        {
            $this->validate($request, [
                'email_only'           => 'required',
                'password'             => 'required',
                'g-recaptcha-response' => 'required|captcha',
            ], [
                'g-recaptcha-response.required' => 'Captcha is required.',
                'g-recaptcha-response.captcha'  => 'Please enter correct captcha.',
            ]);
        }
        elseif ($request->has_captcha == 'Enabled' && $request->login_via == 'phone_only')
        {
            $this->validate($request, [
                'phone_only'           => 'required',
                'password'             => 'required',
                'g-recaptcha-response' => 'required|captcha',
            ], [
                'g-recaptcha-response.required' => 'Captcha is required.',
                'g-recaptcha-response.captcha'  => 'Please enter correct captcha.',
            ]);
        }
        elseif ($request->has_captcha == 'Enabled' && $request->login_via == 'email_or_phone')
        {
            $this->validate($request, [
                'email_or_phone'       => 'required',
                'password'             => 'required',
                'g-recaptcha-response' => 'required|captcha',
            ], [
                'g-recaptcha-response.required' => 'Captcha is required.',
                'g-recaptcha-response.captcha'  => 'Please enter correct captcha.',
            ]);
        }
        else
        {
            $this->validate($request, [
                'password' => 'required',
            ]);
        }
        
        $user_type = $request->user_type;

        if ($request->login_via == 'email_only')
        {
            $loginValue = $request->input('email_only');
        }
        elseif ($request->login_via == 'phone_only')
        {
            $loginValue = $request->input('phone_only');
        }
        else
        {
            $loginValue = $request->input('email_or_phone');
        }
        
        $loginData = $this->getLoginData($loginValue, $request->login_via, $user_type);
        
        if (!empty($loginData['value']))
        {
            $checkLoggedInUser = User::where(['email' => $loginData['value'], 'role_id' => $loginData['user_type']])->first(['status']);
            if ($checkLoggedInUser->status == 'Inactive')
            {
                auth()->logout();
                $this->helper->one_time_message('danger', __('Your account is inactivated. Please try again later!'));
                return redirect('/login');
            }
            
            $checkUserType = User::where(['email' => $loginData['value'], 'role_id' => $user_type])->first();
            if (empty($checkUserType))
            {
                auth()->logout();
                $this->helper->one_time_message('danger', __('Account not exists.'));
                return redirect('/login');
            }

            $checkUserVerificationStatus = $this->checkUserVerificationStatus($loginData['value'], $loginData['user_type']);
            if ($checkUserVerificationStatus == true)
            {
                auth()->logout();
                return redirect('login')->with('status', __('We sent you an activation code.<br>Check your email and click on the link to verify.'));
            }
            else
            {
                $request->merge([
                    $loginData['type'] => $loginData['value'],
                ]);
                $type = $loginData['type'];
                $data = $request->only($type, 'password');

                if (Auth::attempt(['email' => $data['email'], 'password' => $data['password'], 'role_id' => $user_type]))
                {
                    $preferences = Preference::where('field', '!=', 'dflt_lang')->get();
                    if (!empty($preferences))
                    {
                        foreach ($preferences as $pref)
                        {
                            $pref_arr[$pref->field] = $pref->value;
                        }
                    }
                    if (!empty($preferences))
                    {
                        Session::put($pref_arr);
                    }

                    // default_currency
                    $default_currency = Setting::where('name', 'default_currency')->first();
                    if (!empty($default_currency))
                    {
                        Session::put('default_currency', $default_currency->value);
                    }

                    //default_timezone
                    $default_timezone = User::with(['user_detail:id,user_id,timezone'])->where(['id' => auth()->user()->id])->first(['id'])->user_detail->timezone;
                    if (!$default_timezone)
                    {
                        Session::put('dflt_timezone_user', session('dflt_timezone'));
                    }
                    else
                    {
                        Session::put('dflt_timezone_user', $default_timezone);
                    }

                    // default_language
                    $default_language = Setting::where('name', 'default_language')->first();
                    if (!empty($default_language))
                    {
                        Session::put('default_language', $default_language->value);
                    }

                    // company_name
                    $company_name = Setting::where('name', 'name')->first();
                    if (!empty($company_name))
                    {
                        Session::put('name', $company_name->value);
                    }

                    // company_logo
                    $company_logo = Setting::where(['name' => 'logo', 'type' => 'general'])->first();
                    if (!empty($company_logo))
                    {
                        Session::put('company_logo', $company_logo->value);
                    }


                    try
                    {
                        \DB::beginTransaction();

                        //check default wallet
                        $chkWallet = Wallet::where(['user_id' => Auth::user()->id, 'currency_id' => $default_currency->value])->first();
                        if (empty($chkWallet))
                        {
                            $wallet              = new Wallet();
                            $wallet->user_id     = Auth::user()->id;
                            $wallet->currency_id = $default_currency->value;
                            $wallet->balance     = 0.00;
                            $wallet->is_default  = 'No'; //fixed
                            $wallet->save();
                        }
                        
                        $ip = $request->ip();    
                        $location_details = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$ip);
                    
                        $log                  = [];
                        $log['user_id']       = Auth::check() ? Auth::user()->id : null;
                        $log['type']          = 'User';
                        $log['ip_address']    = $request->ip();
                        $log['browser_agent'] = $request->header('user-agent');
                        $log['city'] = $location_details->geoplugin_city;
                        $log['country'] = $location_details->geoplugin_countryName;
                        ActivityLog::create($log);
                        
                        DB::table('users_login_location')->insert([
                            [
                                'user_id' => Auth::user()->id,
                                'ip_address' => $request->ip(),
                                'city' => $location_details->geoplugin_city,
                                'country' => $location_details->geoplugin_countryCode
                            ]
                        ]);

                        //user_detail - adding last_login_at and last_login_ip
                        auth()->user()->user_detail()->update([
                            'last_login_at' => Carbon::now()->toDateTimeString(),
                            'last_login_ip' => $request->getClientIp(),
                        ]);

                        \DB::commit();

                        //2fa
                        $two_step_verification = Preference::where(['category' => 'preference', 'field' => 'two_step_verification'])->first(['value'])->value;
                        $checkDeviceLog        = DeviceLog::where(['user_id' => auth()->user()->id, 'browser_fingerprint' => $request->browser_fingerprint])->first(['browser_fingerprint']);

                        \Session::put('browser_fingerprint', $request->browser_fingerprint); //putting browser_fingerprint on session to restrict users accessing dashboard

                        if (auth()->user()->user_detail->two_step_verification_type != "disabled" && $two_step_verification != "disabled")
                        {
                            if (auth()->user()->user_detail->two_step_verification_type == "google_authenticator")
                            {
                                if (!auth()->user()->user_detail->two_step_verification || empty($checkDeviceLog))
                                {
                                    $google2fa                             = app('pragmarx.google2fa');
                                    $registration_data                     = $request->all();
                                    $registration_data["google2fa_secret"] = $google2fa->generateSecretKey();

                                    $request->session()->flash('registration_data', $registration_data);

                                    $QR_Image = $google2fa->getQRCodeInline(
                                        config('app.name'),
                                        auth()->user()->email,
                                        $registration_data['google2fa_secret']
                                    );
                                    $data = [
                                        'QR_Image' => $QR_Image,
                                        'secret'   => $registration_data['google2fa_secret'],
                                    ];
                                    return \Redirect::route('google2fa')->with(['data' => $data]);
                                }
                                else
                                {
                                    return redirect('dashboard');
                                }
                            }
                            else
                            {
                                if (!auth()->user()->user_detail->two_step_verification || empty($checkDeviceLog))
                                {
                                    $this->execute2fa();
                                    return redirect('2fa');
                                }
                                else
                                {
                                    return redirect('dashboard');
                                }
                            }
                        }
                        else
                        {
                            return redirect('dashboard');
                        }
                    }
                    catch (\Exception $e)
                    {
                        \DB::rollBack();
                        $this->helper->one_time_message('danger', $e->getMessage());
                        return redirect('/login');
                    }
                }
                else
                {
                    $this->helper->one_time_message('danger', __('Unable to login with provided credentials!'));
                    return redirect('/login');
                }
            }
        }
        else
        {
            $this->helper->one_time_message('danger', __('Unable to login with provided credentials!'));
            return redirect('/login');
        }
    }

    protected function getLoginData($loginValue, $loginVia, $user_type)
    {
        $loginArray = [];
        if ($loginVia == 'phone_only')
        {
            $loginArray['type'] = 'email';
            $phnUser            = User::where(['phone' => ltrim($loginValue, '0')])->orWhere(['formattedPhone' => ltrim($loginValue, '0')])->first(['email']);
            if (!$phnUser)
            {
                $loginArray['value'] = null;
            }
            else
            {
                $loginArray['value'] = $phnUser->email;
            }
        }
        else if ($loginVia == 'email_or_phone')
        {
            $loginArray['type'] = 'email';
            if (strpos($loginValue, '@') !== false)
            {
                $user = User::where(['email' => $loginValue])->first(['email']);
                if (!$user)
                {
                    $loginArray['value'] = null;
                }
                else
                {
                    $loginArray['value'] = $user->email;
                }
            }
            else
            {
                $phoneOrEmailUser = User::where(['phone' => ltrim($loginValue, '0')])->orWhere(['formattedPhone' => ltrim($loginValue, '0')])->first(['email']);
                if (!$phoneOrEmailUser)
                {
                    $loginArray['value'] = null;
                }
                else
                {
                    $loginArray['value'] = $phoneOrEmailUser->email;
                }
            }
        }
        else if ($loginVia == 'email_only')
        {
            $loginArray['type'] = 'email';
            $user               = User::where(['email' => $loginValue])->first(['email']);
            if (!$user)
            {
                $loginArray['value'] = null;
            }
            else
            {
                $loginArray['value'] = $user->email;
            }
        }
        $loginArray['user_type'] = $user_type;
        return $loginArray;
    }

    protected function checkUserVerificationStatus($loginValue, $user_type)
    {
        $checkLoginDataOfUser = User::where(['email' => $loginValue, 'role_id' => $user_type])->first(['id', 'first_name', 'last_name', 'email', 'status']);
        if (checkVerificationMailStatus() == 'Enabled' && $checkLoginDataOfUser->user_detail->email_verification == 0)
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
                $this->email->sendEmail($checkLoginDataOfUser->email, $userVerificationEmailTempInfo_sub, $userVerificationEmailTempInfo_msg);
                return true;
            }
        }
    }

    public function execute2fa()
    {
        $six_digit_random_number                = six_digit_random_number();
        $userDetail                             = UserDetail::where(['user_id' => auth()->user()->id])->first();
        $userDetail->two_step_verification_code = $six_digit_random_number;
        $userDetail->save();

        if (auth()->user()->user_detail->two_step_verification_type == 'phone')
        {
            //sms
            $message = $six_digit_random_number . ' is your ' . getCompanyName() . ' 2-factor authentication code. ';

            if (!empty(auth()->user()->carrierCode) && !empty(auth()->user()->phone))
            {
                if (checkAppSmsEnvironment() == true)
                {
                    // dd(auth()->user()->carrierCode . auth()->user()->phone, $message);
                    sendSMS(auth()->user()->carrierCode . auth()->user()->phone, $message);
                }
            }
        }
        elseif (auth()->user()->user_detail->two_step_verification_type == 'email')
        {
            //email
            if (checkAppMailEnvironment())
            {
                $twoStepVerification = EmailTemplate::where([
                    'temp_id'     => 19,
                    'language_id' => getDefaultLanguage(),
                    'type'        => 'email',
                ])->select('subject', 'body')->first();

                $englishtwoStepVerification = EmailTemplate::where(['temp_id' => 19, 'lang' => 'en', 'type' => 'email'])->select('subject', 'body')->first();

                if (!empty($twoStepVerification->subject) && !empty($twoStepVerification->body))
                {
                    $twoStepVerification_sub = $twoStepVerification->subject;
                    $twoStepVerification_msg = str_replace('{user}', auth()->user()->first_name . ' ' . auth()->user()->last_name, $twoStepVerification->body);
                }
                else
                {
                    $twoStepVerification_sub = $englishtwoStepVerification->subject;
                    $twoStepVerification_msg = str_replace('{user}', auth()->user()->first_name . ' ' . auth()->user()->last_name, $englishtwoStepVerification->body);
                }
                $twoStepVerification_msg = str_replace('{code}', $six_digit_random_number, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                $this->email->sendEmail(auth()->user()->email, $twoStepVerification_sub, $twoStepVerification_msg);
            }
        }
    }

}
