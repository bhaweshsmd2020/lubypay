<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\EmailTemplate;
use App\Models\Preference;
use App\Models\Setting;
use App\Models\Role;
use App\Models\RoleUser;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Image;
use Session;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $helper, $emailController;

    public function __construct()
    {
        $this->helper          = new Common();
        $this->emailController = new EmailController();
    }

    public function login()
    {
        return redirect()->route('admin');
    }

    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $request['email'])->first();

        if (@$admin->status != 'Inactive')
        {
            if (Auth::guard('admin')->attempt(['email' => trim($request['email']), 'password' => trim($request['password'])]))
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

                $default_currency = Setting::where('name', 'default_currency')->first();
                if (!empty($default_currency))
                {
                    Session::put('default_currency', $default_currency->value);
                }

                $default_language = Setting::where('name', 'default_language')->first();
                if (!empty($default_language))
                {
                    Session::put('default_language', $default_language->value);
                }

                $company_name = Setting::where('name', 'name')->first();
                if (!empty($company_name))
                {
                    Session::put('name', $company_name->value);
                }

                $company_logo = Setting::where('name', 'logo')->first();
                if (!empty($company_logo))
                {
                    Session::put('company_logo', $company_logo->value);
                }
                
                $ip = $request->ip();    
                $location_details = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$ip);

                $log                  = [];
                $log['user_id']       = Auth::guard('admin')->check() ? Auth::guard('admin')->user()->id : null;
                $log['type']          = 'Admin';
                $log['ip_address']    = $request->ip();
                $log['browser_agent'] = $request->header('user-agent');
                $log['city'] = $location_details->geoplugin_city;
                $log['country'] = $location_details->geoplugin_countryName;
                ActivityLog::create($log);
                
                if(Auth::guard('admin')->user()->fa_status == '1'){
                    return redirect()->route('faverify');
                }else{
                    return redirect()->route('dashboard');
                }
            }
            else
            {
                $this->helper->one_time_message('danger', 'Please Check Your Email/Password');
                return redirect()->route('admin');
            }
        }
        else
        {
            $this->helper->one_time_message('danger', 'You are Blocked.');
            return redirect()->route('admin');
        }
    }

    public function faverify()
    {
        $data['menu'] = '2fa';
        return view('admin.auth.2fa', $data);
    }
    
    public function submitfa(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $g = new GoogleAuthenticator();
        if($g->checkcode($user->googlefa_secret, $request->code, 3)){
            $user->update(['fa_expiring' => Carbon::now()->addHours(2)]);
            return redirect()->route('dashboard');
        }else{
            $this->helper->one_time_message('error', 'Invalid code');
            return back();
        }
    } 

    public function verifyToken($token)
    {
        if (!$token)
        {
            $this->helper->one_time_message('error', 'Token not found!');
            return back();
        }
        $reset = DB::table('password_resets')->where('token', $token)->first();
        if ($reset)
        {
            $data['token'] = $token;
            return view('admin.auth.passwordForm', $data);
        }
        else
        {
            $this->helper->one_time_message('error', 'Token session has been destroyed. Please try to reset again.');
            return back();
        }
    }

    public function confirmNewPassword(Request $request)
    {
        $token    = $request->token;
        $password = $request->new_password;
        $confirm  = DB::table('password_resets')->where('token', $token)->first(['email']);

        $admin           = Admin::where('email', $confirm->email)->first();
        $admin->password = Hash::make($password);
        $admin->save();

        DB::table('password_resets')->where('token', $token)->delete();

        $this->helper->one_time_message('success', 'Password changed successfully.');
        return redirect()->to('/admin');
    }

    public function forgetPassword(Request $request)
    {
        $methodName = $request->getMethod();
        if ($methodName == "GET")
        {
            return view('admin.auth.forgetPassword');
        }
        else
        {
            $email = $request->email;
            $admin = Admin::where('email', $email)->first(['id', 'first_name', 'last_name']);
            if (!$admin)
            {
                $this->helper->one_time_message('error', 'Email Address doesn\'t match!');
                return back();
            }
            $data['email']      = $request->email;
            $data['token']      = $token      = base64_encode(encryptIt(rand(1000000, 9999999) . '_' . $request->email));
            $data['created_at'] = date('Y-m-d H:i:s');

            DB::table('password_resets')->insert($data);

            $adminFullName = $admin->first_name . ' ' . $admin->last_name;
            $this->sendPasswordResetEmail($request->email, $token, $adminFullName);

            $this->helper->one_time_message('success', 'Password reset link has been sent to your email address.');
            return back();
        }
    }

    public function sendPasswordResetEmail($toEmail, $token, $adminFullName)
    {
        $userPasswordResetTempInfo = EmailTemplate::where([
            'temp_id'     => 18,
            'language_id' => getDefaultLanguage(),
        ])->select('subject', 'body')->first();

        $englishUserPasswordResetTempInfo = EmailTemplate::where(['temp_id' => 18, 'lang' => 'en'])->select('subject', 'body')->first();

        if (!empty($userPasswordResetTempInfo->subject) && !empty($userPasswordResetTempInfo->body))
        {
            $userPasswordResetTempInfo_sub = $userPasswordResetTempInfo->subject;
            $userPasswordResetTempInfo_msg = str_replace('{user}', $adminFullName, $userPasswordResetTempInfo->body);
        }
        else
        {
            $userPasswordResetTempInfo_sub = $englishUserPasswordResetTempInfo->subject;
            $userPasswordResetTempInfo_msg = str_replace('{user}', $adminFullName, $englishUserPasswordResetTempInfo->body);
        }
        $userPasswordResetTempInfo_msg = str_replace('{email}', $toEmail, $userPasswordResetTempInfo_msg);
        $userPasswordResetTempInfo_msg = str_replace('{password_reset_url}', url('admin/password/resets', $token), $userPasswordResetTempInfo_msg);
        $userPasswordResetTempInfo_msg = str_replace('{soft_name}', getCompanyName(), $userPasswordResetTempInfo_msg);

        if (checkAppMailEnvironment())
        {
            $this->emailController->sendEmail($toEmail, $userPasswordResetTempInfo_sub, $userPasswordResetTempInfo_msg);
        }
    }
}