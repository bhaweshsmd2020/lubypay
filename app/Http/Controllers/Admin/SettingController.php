<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\EmailConfig;
use App\Models\FeesLimit;
use App\Models\Language;
use App\Models\PaymentMethod;
use App\Models\Preference;
use App\Models\Setting;
use App\Models\SmsConfig;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
//use Image;
use Session;
use Validator;
use Intervention\Image\Facades\Image;
use Auth;
use App\Models\Revenue;
use App\Models\RevenueLog;
use App\Models\Transaction;
use App\DataTables\Admin\ServicesDataTable;
use App\Models\Services;
use App\Models\NfcCredential;

class SettingController extends Controller
{
    public $dimension = ['logo' => ['width' => 288, 'height' => 90], 'favicon' => ['width' => 40, 'height' => 40]];
    protected $helper;

    protected $cryptoCurrency;
    public function __construct()
    {
        $this->helper         = new Common();
    }
    public function all_utility()
    {
        $data['menu'] = 'settings';
        $data['sub_menu'] = 'general';
        $data['sub_sub_menu'] = 'all_utility';
        
        $data['service'] = DB::table('dhiraagu_services')->get();
        return view('admin.settings.allutility', $data);
    }
    
   public function error_code()
    {
        $data['menu'] = 'error_code';
        $data['service'] = DB::table('dhiraagu_services')->get();
        return view('admin.settings.error_code', $data);
    }
    
    public function update_utility(Request $request)
    {
          $validator = Validator::make($request->all(), [
          'service_id'   => 'required',
          'service_name' => 'required',
          'service_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
          'service_inactive_message' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
         }else
         {
            if ($request->hasFile('service_icon')) 
    		   {
    			 $image = $request->file('service_icon');
    			 
    			 $first_image = rand(00000,99999).'.'.$image->getClientOriginalExtension();
    			 $destinationPath = public_path('dhiraagu/');
    			 $resizedImg = Image::make($image)->resize(512,512);
    			 $image->move($destinationPath, $first_image);
    		   }else{
    			   $first_image = $request->old_logo;
    		   }
    		   if ($request->hasFile('service_inactive_icon')) 
    		   {
    			 $image = $request->file('service_inactive_icon');
    			 
    			 $second_image = rand(00000,99999).'.'.$image->getClientOriginalExtension();
    			 $destinationPath = public_path('dhiraagu/');
    			 $resizedImg = Image::make($image)->resize(512,512);
    			 $image->move($destinationPath, $second_image);
    		   }else{
    			   $second_image = $request->old_inactive_icon;
    		   }
    		   $update = DB::table('dhiraagu_services')->where('service_id',$request->service_id)->update([
    		       'service_name'       => $request->service_name, 
    		       'service_slug'       => $this->slugify($request->service_name), 
    		       'inactive_message'   => $request->service_inactive_message, 
    		       'inactive_icon'      => $second_image, 
    		       'logo'               => $first_image, 
    		       'is_active'          => $request->service_status, 
    		       ]);
    		   
    		  return redirect()->back()->with('success', 'Utility Settings Updated Successfully');
         }
         
        
    }
    
    public function slugify($text)
    {
      $text = preg_replace('~[^\pL\d]+~u', '_', $text);
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
      $text = preg_replace('~[^-\w]+~', '', $text);
      $text = trim($text, '-');
      $text = preg_replace('~-+~', '-', $text);
      $text = strtolower($text);
      if (empty($text)) {
        return 'n-a';
      }
      return $text;
    }
    public function general(Request $request)
    {

        if (!$_POST)
        {
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'general';
            $data['sub_sub_menu'] = 'general';

            //General
            $general        = Setting::where('type', 'general')->get()->toArray();
            $data['result'] = $result = $this->helper->key_value('name', 'value', $general);
            // dd($result['default_crypto_currencies']);

            //Nexmo
            $nexmo         = Setting::where('type', 'Nexmo')->get()->toArray();
            $data['nexmo'] = $nexmo = $this->helper->key_value('name', 'value', $nexmo);

            //Languages
            $data['language'] = $language = $this->helper->key_value('id', 'name', Language::where(['status' => 'Active'])->get(['id', 'name'])->toArray());

            //Currencies
            $data['currency'] = $currency = $this->helper->key_value('id', 'name', Currency::where(['type' => 'fiat', 'status' => 'Active'])->get(['id', 'name'])->toArray());

            return view('admin.settings.general', $data);
        }
        else if ($_POST)
        {
            // dd($request->all());

            $rules = array(
                'name' => 'required',
            );

            $fieldNames = array(
                'name' => 'Name',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {

                //Setting
                Setting::where(['name' => 'name'])->update(['value' => $request->name]);

                //Save Logo & Favicon
                foreach ($_FILES["photos"]["error"] as $key => $error)
                {
                    $tmp_name = $_FILES["photos"]["tmp_name"][$key];
                    $name     = str_replace(' ', '_', $_FILES["photos"]["name"][$key]);
                    $ext      = pathinfo($name, PATHINFO_EXTENSION);
                    $name     = time() . '_' . $key . '.' . $ext;
                    if ($error == 0)
                    {
                        $this->storeImageToFixedDimension($request->photos[$key], $name, $key);
                    }
                }

                Setting::where(['name' => 'head_code'])->update(['value' => is_null($request->head_code) ? '' : trim($request->head_code)]);
                Setting::where(['name' => 'default_currency'])->update(['value' => $request->default_currency]);
                Setting::where(['name' => 'default_language'])->update(['value' => $request->default_language]);
                Setting::where(['name' => 'virtual_card_fee'])->update(['value' => $request->virtual_card_fee]);

                //recaptcha
                Setting::where(['name' => 'has_captcha'])->update(['value' => $request->has_captcha]);
                
                //login_via
                Setting::where(['name' => 'login_via'])->update(['value' => $request->login_via]);

                //Currency
                Currency::where('default', '=', '1')->update(['default' => '0']);
                Currency::where('id', $request->default_currency)->update(['default' => '1']);
                //

                //updation or creation of fees limit entries on default currency change
                $paymentMethodArray = PaymentMethod::where(['status' => 'Active'])->pluck('id')->toArray();
                $transaction_types  = [Deposit, Withdrawal, Transferred, Exchange_From, Request_To];
                foreach ($transaction_types as $transaction_type)
                {
                    $feeslimit = FeesLimit::where(['has_transaction' => 'No', 'currency_id' => $request->default_currency])->get(['id', 'has_transaction']);
                    if ($feeslimit->count() > 0)
                    {
                        //update existing has transaciton - no to yes
                        foreach ($feeslimit as $fLimit)
                        {
                            $fLimit->has_transaction = 'Yes';
                            $fLimit->save();
                        }
                    }
                    else
                    {
                        if ($transaction_type == 1 || $transaction_type == 2)
                        {
                            foreach ($paymentMethodArray as $key => $value)
                            {
                                $checkFeeslimitMultiplePm = FeesLimit::where(['currency_id' => $request->default_currency, 'transaction_type_id' => $transaction_type, 'payment_method_id' => $value])
                                    ->first(['id', 'currency_id', 'transaction_type_id', 'payment_method_id', 'has_transaction']);
                                if (empty($checkFeeslimitMultiplePm))
                                {
                                    //insert new records of feeslimit on change of default currency with payment method
                                    $feesLimit                      = new FeesLimit();
                                    $feesLimit->currency_id         = $request->default_currency;
                                    $feesLimit->transaction_type_id = $transaction_type;
                                    $feesLimit->payment_method_id   = $value;
                                    $feesLimit->has_transaction     = 'Yes';
                                    $feesLimit->save();
                                }
                            }
                        }
                        else
                        {
                            $checkFeeslimitSinglePm = FeesLimit::where(['currency_id' => $request->default_currency, 'transaction_type_id' => $transaction_type])
                                ->first(['id', 'currency_id', 'transaction_type_id', 'has_transaction']);
                            if (empty($checkFeeslimitSinglePm))
                            {
                                //insert new records of feeslimit on change of default currency with no payment method
                                $feesLimit                      = new FeesLimit();
                                $feesLimit->currency_id         = $request->default_currency;
                                $feesLimit->transaction_type_id = $transaction_type;
                                $feesLimit->has_transaction     = 'Yes';
                                $feesLimit->save();
                            }
                        }
                    }
                }
                //

                //Language
                Language::where('default', '=', '1')->update(['default' => '0']);
                Language::where('id', $request->default_language)->update(['default' => '1']);

                $lang = Language::find($request->default_language, ['id', 'short_name']);
                Preference::where(['field' => 'dflt_lang', 'category' => 'company'])->update(['value' => $lang->short_name]);

                //Save Default CryptoCurrencies
                $getDefaultCryptoCurrenciesSetting = Setting::where(['name' => 'default_crypto_currencies']);
                if (isset($request->default_crypto_currencies))
                {
                    // .env - APP_DEMO - check
                    if (checkDemoEnvironment() == true)
                    {
                        $this->helper->one_time_message('error', 'Allowed Crypto Currencies cannot be updated on demo site.');
                        return redirect('admin/settings');
                    }
                    else
                    {
                        $getDefaultCryptoCurrenciesSetting->update(['value' => implode($request->default_crypto_currencies, ',')]);
                    }
                }
                else
                {
                    $getDefaultCryptoCurrenciesSetting->update(['value' => 'none']);
                }
                //
                $this->helper->one_time_message('success', 'General Settings Updated Successfully');
                return redirect('admin/settings');
            }
        }
    }
    
    public function appversions(Request $request)
    {
        if (!$_POST)
        {
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'app-store-credentials';
            $data['sub_sub_menu'] = 'app_versions';

            $general        = Setting::where('type', 'general')->get()->toArray();
            $data['result'] = $result = $this->helper->key_value('name', 'value', $general);

            return view('admin.settings.version', $data);
        }
        else if ($_POST)
        {
            $rules = array(
                'android_version' => 'required',
            );

            $fieldNames = array(
                'android_version' => 'Android Version',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            
            Setting::where(['name' => 'android_version'])->update(['value' => $request->android_version]);
            Setting::where(['name' => 'ios_version'])->update(['value' => $request->ios_version]);
            Setting::where(['name' => 'android_url'])->update(['value' => $request->android_url]);
            Setting::where(['name' => 'ios_url'])->update(['value' => $request->ios_url]);
            
            Setting::where(['name' => 'mpos_android_version'])->update(['value' => $request->mpos_android_version]);
            Setting::where(['name' => 'mpos_ios_version'])->update(['value' => $request->mpos_ios_version]);
            Setting::where(['name' => 'mpos_android_url'])->update(['value' => $request->mpos_android_url]);
            Setting::where(['name' => 'mpos_ios_url'])->update(['value' => $request->mpos_ios_url]);
           
            $this->helper->one_time_message('success', 'App Versions Updated Successfully');
            return redirect('admin/settings/appversions');
        }
    }
    
    protected function storeImageToFixedDimension($image, $fileName, $key)
    {
        $location = public_path("images/logos");
        $ext      = strtolower($image->getClientOriginalExtension());

        //check extension
        if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp')
        {
            try
            {
                $img = Image::make($image->getRealPath());
                $img->resize($this->dimension[$key]['width'], $this->dimension[$key]['height'])->save($location . '/' . $fileName);
                Setting::where(['name' => $key])->update(['value' => $fileName]);
            }
            catch (\Exception $e)
            {
                $this->helper->one_time_message('error', $e->getMessage());
            }
        }
        else
        {
            $this->helper->one_time_message('error', 'Invalid Image Format!');
        }
    }

    public function updateSideBarCompanyLogo(Request $request)
    {
        // dd($request->all());

        $filename = '';
        $picture  = $request->photos['logo'];
        if (isset($picture))
        {
            $location = public_path("images/logos");
            $ext      = strtolower($picture->getClientOriginalExtension());
            $filename = time() . '.' . $ext;
            $img      = Image::make($picture->getRealPath());
            $img->resize($this->dimension['logo']['width'], $this->dimension['logo']['height'])->save($location . '/' . $filename);
            return response()->json([
                'filename' => $filename,
            ]);
        }
    }

    public function checkSmsGatewaySettings(Request $request)
    {
        $smsConfigs = getSmsConfigDetails();
        if (empty($smsConfigs))
        {
            return response()->json([
                'status'  => false,
                'message' => 'Sms settings is inactive or configured incorrectly!',
            ]);
        }
    }

    //deleteSettingLogo
    public function deleteSettingLogo(Request $request)
    {
        $logo = $_POST['logo'];

        if (isset($logo))
        {
            $setting = Setting::where(['name' => 'logo', 'type' => 'general', 'value' => $request->logo])->first();

            if ($setting)
            {
                Setting::where(['name' => 'logo', 'type' => 'general', 'value' => $request->logo])->update(['value' => null]);

                if ($logo != null)
                {
                    $dir = public_path('images/logos/' . $logo);
                    if (file_exists($dir))
                    {
                        unlink($dir);
                    }
                }
                $data['success'] = 1;
                $data['message'] = 'Logo has been successfully deleted!';
            }
            else
            {
                $data['success'] = 0;
                $data['message'] = "No Record Found!";
            }
        }
        echo json_encode($data);
        exit();
    }

    //deleteSettingFavicon
    public function deleteSettingFavicon(Request $request)
    {
        $favicon = $_POST['favicon'];

        if (isset($favicon))
        {
            $setting = Setting::where(['name' => 'favicon', 'type' => 'general', 'value' => $request->favicon])->first();

            if ($setting)
            {
                Setting::where(['name' => 'favicon', 'type' => 'general', 'value' => $request->favicon])->update(['value' => null]);

                if ($favicon != null)
                {
                    $dir = public_path('images/logos/' . $favicon);
                    if (file_exists($dir))
                    {
                        unlink($dir);
                    }
                }
                $data['success'] = 1;
                $data['message'] = 'Favicon has been successfully deleted!';
            }
            else
            {
                $data['success'] = 0;
                $data['message'] = "No Record Found!";
            }
        }
        echo json_encode($data);
        exit();
    }

    //email settings
    public function email(Request $request)
    {
        if (!$_POST)
        {
            $data['menu']     = 'email';
            $data['sub_menu'] = 'email_config';
        
            $general        = EmailConfig::find("1")->toArray();
            $data['result'] = $general;
            //dd($general);

            return view('admin.settings.email', $data);
        }
        else if ($_POST)
        {
            $email_config = EmailConfig::find('1');
            if ($email_config)
            {
                $email_config->email_protocol   = $request->driver;
                $email_config->email_encryption = $request->encryption;
                $email_config->smtp_host        = $request->host;
                $email_config->smtp_port        = $request->port;
                $email_config->smtp_email       = $request->from_address;
                $email_config->smtp_username    = $request->username;
                $email_config->smtp_password    = $request->password;
                $email_config->from_address     = $request->from_address;
                $email_config->from_name        = $request->from_name;
                $email_config->notification_email = $request->notification_email;
                $email_config->save();
            }
            else
            {
                $configIns                   = new EmailConfig();
                $configIns->email_protocol   = $request->driver;
                $configIns->email_encryption = $request->encryption;
                $configIns->smtp_host        = $request->host;
                $configIns->smtp_port        = $request->port;
                $configIns->smtp_email       = $request->from_address;
                $configIns->smtp_username    = $request->username;
                $configIns->smtp_password    = $request->password;
                $configIns->from_address     = $request->from_address;
                $configIns->from_name        = $request->from_name;
                $configIns->notification_email = $request->notification_email;
                $configIns->save();
            }

            if ($request->driver == "smtp")
            {
                $rules = array(
                    'driver'       => 'required',
                    'host'         => 'required',
                    'port'         => 'required',
                    'from_address' => 'required',
                    'from_name'    => 'required',
                    'encryption'   => 'required',
                    'username'     => 'required',
                    'password'     => 'required',
                );

                $fieldNames = array(
                    'driver'       => 'Driver',
                    'host'         => 'Host',
                    'port'         => 'Port',
                    'from_address' => 'From Address',
                    'from_name'    => 'From Name',
                    'encryption'   => 'Encryption',
                    'username'     => 'Username',
                    'password'     => 'Password',
                );

                $validator = Validator::make($request->all(), $rules);
                $validator->setAttributeNames($fieldNames);

                if ($validator->fails())
                {
                    return back()->withErrors($validator)->withInput();
                }
                else
                {
                    Setting::where(['name' => 'driver'])->update(['value' => $request->driver]);
                    Setting::where(['name' => 'host'])->update(['value' => $request->host]);
                    Setting::where(['name' => 'port'])->update(['value' => $request->port]);
                    Setting::where(['name' => 'from_address'])->update(['value' => $request->from_address]);
                    Setting::where(['name' => 'from_name'])->update(['value' => $request->from_name]);
                    Setting::where(['name' => 'encryption'])->update(['value' => $request->encryption]);
                    Setting::where(['name' => 'username'])->update(['value' => $request->username]);
                    Setting::where(['name' => 'password'])->update(['value' => $request->password]);
                    Setting::where(['name' => 'notification_email'])->update(['value' => $request->notification_email]);

                    $data = $request->all();
                    Config::set([
                        'mail.driver'     => isset($data['driver']) ? $data['driver'] : '',

                        'mail.host'       => isset($data['host']) ? $data['host'] : '',

                        'mail.port'       => isset($data['port']) ? $data['port'] : '',

                        'mail.from'       => ['address' => isset($data['from_address']) ? $data['from_address'] : '',

                        'name'            => isset($data['from_name']) ? $data['from_name'] : ''],

                        'mail.encryption' => isset($data['encryption']) ? $data['encryption'] : '',

                        'mail.username'   => isset($data['username']) ? $data['username'] : '',

                        'mail.password'   => isset($data['password']) ? $data['password'] : '',
                    ]);

                    $fromInfo = \Config::get('mail.from');

                    $user = [];
                    $user['to']       = 'gautamsmdwebtech@gmail.com';
                    $user['from']     = $fromInfo['address'];
                    $user['fromName'] = $fromInfo['name'];
                    try
                    {
                        $ok = Mail::send('emails.verify', ['user' => $user], function ($m) use ($user)
                        {
                            $m->from($user['from'], $user['fromName']);
                            $m->to($user['to']);
                            $m->subject('verify smtp settings');
                        });
                        $emailConfig         = EmailConfig::find("1");
                        $emailConfig->status = 1;
                        $emailConfig->save();
                        $this->helper->one_time_message('success', 'SMTP settings are verified successfully!');
                        return redirect('admin/settings/email');
                    }
                    catch (\Exception $e)
                    {
                        $emailConfig         = EmailConfig::find("1");
                        $emailConfig->status = 0;
                        $emailConfig->save();
                        $this->helper->one_time_message('error', $e->getMessage());
                        return redirect('admin/settings/email');
                    }
                }
            }
            else
            {
                Setting::where(['name' => 'driver'])->update(['value' => $request->driver]);

                $this->helper->one_time_message('success', 'Email Settings Updated Successfully');
                return redirect('admin/settings/email');
            }
        }
    }

    //sms settings
    public function sms(Request $request, $type)
    {
        $data['menu']     = 'sms';
        $data['sub_menu'] = 'sms_config';
            
        if (!$request->isMethod('post'))
        {
            if ($type == 'twilio')
            {
                $data['twilio']      = $twilio      = SmsConfig::where(['type' => $type])->first();
                $data['credentials'] = $credentials = json_decode($twilio->credentials);
                return view('admin.settings.sms.twilio', $data);
            }
            else if ($type == 'nexmo')
            {
                $data['nexmo']       = $nexmo       = SmsConfig::where(['type' => $type])->first();
                $data['credentials'] = $credentials = json_decode($nexmo->credentials);
                return view('admin.settings.sms.nexmo', $data);
            }
            else if ($type == 'oneway')            
            {   
             
                $data['oneway']       = $oneway       = SmsConfig::where(['type' => $type])->first();   
                $data['credentials'] = $credentials = json_decode($oneway->credentials);  
                return view('admin.settings.sms.oneway', $data);            
                
            }
            else if ($type == 'smdsms')            
            { 
                $data['smdsms']       = $smdsms       = SmsConfig::where(['type' => $type])->first();               
                $data['credentials'] = $credentials = json_decode($smdsms->credentials);  
                
                return view('admin.settings.sms.smdsms', $data);           
            }
        }
        else
        {
            if ($type == 'twilio')
            {

                $rules = array(
                    'name'                               => 'required',
                    'twilio.account_sid'                 => 'required',
                    'twilio.auth_token'                  => 'required',
                    'twilio.default_twilio_phone_number' => 'required',
                    'status'                             => 'required',
                );

                $fieldNames = array(
                    'name'                               => 'Name',
                    'twilio.account_sid'                 => 'Twilio Key',
                    'twilio.auth_token'                  => 'Twilio Secret',
                    'twilio.default_twilio_phone_number' => 'Twilio Phone Number',
                    'status'                             => 'Status',
                );
                $validator = Validator::make($request->all(), $rules);
                $validator->setAttributeNames($fieldNames);
                if ($validator->fails())
                {
                    return back()->withErrors($validator)->withInput();
                }
                //
                $twilioSmsConfig = SmsConfig::where(['type' => base64_decode($request->type)])->first();
                if ($twilioSmsConfig && (($request->status == 'Active') || ($request->status == 'Inactive')))
                {
                    $twilioSmsConfig->credentials = json_encode($request->twilio);
                    $twilioSmsConfig->status      = $request->status == 'Active' ? 'Active' : 'Inactive';
                    $twilioSmsConfig->save();
                    if ($twilioSmsConfig->status == 'Active')
                    {
                        $nexmoSmsConfig  = SmsConfig::where(['type' => 'nexmo'])->first(['id', 'status']);
                        $nexmoSmsConfig->status = 'Inactive';
                        $nexmoSmsConfig->save();
                        
                        $onewaySmsConfig         = SmsConfig::where(['type' => 'oneway'])->first(['id', 'status']);                        
                        $onewaySmsConfig->status = 'Inactive';                       
                        $onewaySmsConfig->save();
                        
                        $smdSmsConfig  = SmsConfig::where(['type' => 'smdsms'])->first(['id', 'status']);
                        $smdSmsConfig->status = 'Inactive';
                        $smdSmsConfig->save();
                    }
                    $this->helper->one_time_message('success', 'Twilio SMS settings updated successfully!');
                    return redirect('admin/settings/sms/twilio');
                }
            }						
            else if ($type == 'nexmo')
            {
                // dd($request->all());

                $rules = array(
                    'name'                             => 'required',
                    'nexmo.Key'                        => 'required',
                    'nexmo.Secret'                     => 'required',
                    'nexmo.default_nexmo_phone_number' => 'required',
                    'status'                           => 'required',
                );
                $fieldNames = array(
                    'name'                             => 'Name',
                    'nexmo.Key'                        => 'Nexmo Key',
                    'nexmo.Secret'                     => 'Nexmo Secret',
                    'nexmo.default_nexmo_phone_number' => 'Nexmo Phone Number',
                    'status'                           => 'Status',
                );
                $validator = Validator::make($request->all(), $rules);
                $validator->setAttributeNames($fieldNames);
                if ($validator->fails())
                {
                    return back()->withErrors($validator)->withInput();
                }

                $nexmoSmsConfig = SmsConfig::where(['type' => base64_decode($request->type)])->first();
                // dd($nexmoSmsConfig);
                if (!empty($nexmoSmsConfig) && (($request->status == 'Active') || ($request->status == 'Inactive')))
                {
                    $nexmoSmsConfig->credentials = json_encode($request->nexmo);
                    $nexmoSmsConfig->status      = $request->status == 'Active' ? 'Active' : 'Inactive';
                    $nexmoSmsConfig->save();
                    if ($nexmoSmsConfig->status == 'Active')
                    {
                        $onewaySmsConfig         = SmsConfig::where(['type' => 'oneway'])->first(['id', 'status']); 
                        $onewaySmsConfig->status = 'Inactive';
                        $onewaySmsConfig->save();
                        
                        $smdSmsConfig  = SmsConfig::where(['type' => 'smdsms'])->first(['id', 'status']);
                        $smdSmsConfig->status = 'Inactive';
                        $smdSmsConfig->save();
                        
                        
                        $twilioSmsConfig         = SmsConfig::where(['type' => 'twilio'])->first(['id', 'status']);
                        $twilioSmsConfig->status = 'Inactive';
                        $twilioSmsConfig->save();
                        
                    }
                    $this->helper->one_time_message('success', 'Nexmo SMS settings updated successfully!');
                    return redirect('admin/settings/sms/nexmo');
                }
            }
       		elseif($type=='oneway')
       		{
       		    $rules = array(       
       		        'name'                             => 'required',    
       		       // 'oneway.Key'                        => 'required',   
       		       // 'oneway.Secret'                     => 'required',
       		       'oneway.account_sid'                     =>'required',
       		       'oneway.auth_token'                     =>'required',
       		        'oneway.default_oneway_phone_number' => 'required',           
       		        'status'                           => 'required',                
       		        );                
       		        $fieldNames = array(                 
       		            'name'                             => 'Name',    
       		           // 'oneway.Key'                        => 'Oneway Key',      
       		           // 'oneway.Secret'                     => 'Oneway Secret',
       		            'oneway.account_sid'                        => 'Oneway account_sid',
       		            'oneway.auth_token'                        => 'Oneway auth_token',
       		            'oneway.default_oneway_phone_number' => 'Oneway Phone Number',    
       		            'status'                           => 'Status',                );   
       		            $validator = Validator::make($request->all(), $rules);         
       		            $validator->setAttributeNames($fieldNames);              
       		            if ($validator->fails())                
       		            { 
       		                return back()->withErrors($validator)->withInput();  
       		           }              
       		       $onewaySmsConfig = SmsConfig::where(['type' => base64_decode($request->type)])->first();	
       		       if (!empty($onewaySmsConfig) && (($request->status == 'Active') || ($request->status == 'Inactive')))     
       		       {                    
       		           $onewaySmsConfig->credentials = json_encode($request->oneway);    
       		           $onewaySmsConfig->status      = $request->status == 'Active' ? 'Active' : 'Inactive';    
       		           $onewaySmsConfig->save();           
       		           if ($onewaySmsConfig->status == 'Active')   
       		           {                       
       		               $twilioSmsConfig         = SmsConfig::where(['type' => 'twilio'])->first(['id', 'status']);   
       		               $twilioSmsConfig->status = 'Inactive';     
       		               $twilioSmsConfig->save();        
       		               }
       		               
       		            $nexmoSmsConfig  = SmsConfig::where(['type' => 'nexmo'])->first(['id', 'status']);
                        $nexmoSmsConfig->status = 'Inactive';
                        $nexmoSmsConfig->save();
                        
                        $smdSmsConfig  = SmsConfig::where(['type' => 'smdsms'])->first(['id', 'status']);
                        $smdSmsConfig->status = 'Inactive';
                        $smdSmsConfig->save();
                        
                        
       		               $this->helper->one_time_message('success', 'Oneway SMS settings updated successfully!');
       		               return redirect('admin/settings/sms/oneway');              
       		               }			
       		    
       		}
       		elseif($type=='smdsms')
       		{
       		    $rules = array(    
       		        'smdsms.key'                        => 'required',   
       		        'smdsms.account_sid'                     => 'required',
       		        'smdsms.username'                        => 'required',
       		        'smdsms.password'                        => 'required',
       		        'smdsms.default_smdsms_phone_number' => 'required',       
       		        'status'                           => 'required',             
       		        );  
       		       
       		        $fieldNames = array(    
       		            'smdsms.key'                        => 'SMDSMS Key',    
       		            'smdsms.account_sid'                     => 'SMDSMS Secret', 
       		            'smdsms.username'                        => 'SMDSMS user name', 
       		            'smdsms.password'                        => 'SMDSMS password', 
       		            'smdsms.default_smdsms_phone_number' => 'SMDSMS Phone Number',    
       		            'status'                           => 'Status',    
       		            );   
       		            
       		            $validator = Validator::make($request->all(), $rules); 
       		            $validator->setAttributeNames($fieldNames); 
       		            if ($validator->fails()) 
       		            {       
       		                
       		                die('fail');
       		                return back()->withErrors($validator)->withInput();               
       		           }               
       		           $smdSmsConfig = SmsConfig::where(['type' => base64_decode($request->type)])->first();	
       		         
       		           if (!empty($smdSmsConfig) && (($request->status == 'Active') || ($request->status == 'Inactive')))                
       		           {                    
       		               $smdSmsConfig->credentials = json_encode($request->smdsms);
       		               $smdSmsConfig->status      = $request->status == 'Active' ? 'Active' : 'Inactive'; 
       		               $smdSmsConfig->save();                    
       		               if ($smdSmsConfig->status == 'Active')   
       		               {                        
       		                   $twilioSmsConfig         = SmsConfig::where(['type' => 'twilio'])->first(['id', 'status']);         
       		                   $twilioSmsConfig->status = 'Inactive';           
       		                   $twilioSmsConfig->save();									
       		                   $nexmoSmsConfig         = SmsConfig::where(['type' => 'nexmo'])->first(['id', 'status']);   
       		                   $nexmoSmsConfig->status = 'Inactive';
       		                   $nexmoSmsConfig->save();
       		                   
       		                   
       		                   $onewaySmsConfig         = SmsConfig::where(['type' => 'oneway'])->first(['id', 'status']); 
                                $onewaySmsConfig->status = 'Inactive';
                                $onewaySmsConfig->save();
       		               }           
       		               $this->helper->one_time_message('success', 'SMD SMS settings updated successfully!'); 
       		               return redirect('admin/settings/sms/smdsms');  
       		           }	
       		   }
       		    
            
        }
    }

    // social_links
    public function social_links(Request $request)
    {
        if (!$_POST)
        {
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'general';
            $data['sub_sub_menu'] = 'social_links';
            
            $general      = DB::table('socials')->get();

            $data['result'] = $general;
            return view('admin.settings.social', $data);
        }
        else if ($_POST)
        {
            // $rules = array(
            //     'facebook'    => 'required',
            //     'google_plus' => 'required',
            //     'twitter'     => 'required',
            //     'linkedin'    => 'required',
            //     'pinterest'   => 'required',
            //     'youtube'     => 'required',
            //     'instagram'   => 'required',
            // );

            // $fieldNames = array(
            //     'facebook'    => 'Facebook',
            //     'google_plus' => 'Google Plus',
            //     'twitter'     => 'Twitter',
            //     'linkedin'    => 'Linkedin',
            //     'pinterest'   => 'Pinterest',
            //     'youtube'     => 'Youtube',
            //     'instagram'   => 'Instagram',

            // );
            // $validator = Validator::make($request->all(), $rules);
            // $validator->setAttributeNames($fieldNames);

            // if ($validator->fails())
            // {
            //     return back()->withErrors($validator)->withInput();
            // }
            // else
            // {
            //     $links = $request->all();
            //     unset($links['_token']);

            //     foreach ($links as $key => $link)
            //     {
            //         $social = DB::table('socials')->where('name', $key)->first();
            //         if (!$social)
            //         {
            //             $key2 = str_replace('_', ' ', $key);

            //             $data['name'] = $key;
            //             $data['icon'] = "<i class=\"ti-$key2\" aria-hidden=\"true\"></i>";
            //             $data['url']  = $link;
            //             DB::table('socials')->insert($data);
            //         }
            //         else
            //         {
            //             DB::table('socials')->where('name', $key)->update(['url' => $link]);
            //         }
            //     }

            //     $this->helper->one_time_message('success', 'Social Links Settings Updated Successfully');
            //     return redirect('admin/settings/social_links');
            // }

            $links = $request->all();
            unset($links['_token']);

            foreach ($links as $key => $link)
            {
                $social = DB::table('socials')->where('name', $key)->first();
                if (!$social)
                {
                    $key2 = str_replace('_', ' ', $key);

                    $data['name'] = $key;
                    $data['icon'] = "<i class=\"ti-$key2\" aria-hidden=\"true\"></i>";
                    $data['url']  = $link;
                    DB::table('socials')->insert($data);
                }
                else
                {
                    DB::table('socials')->where('name', $key)->update(['url' => $link]);
                }
            }

            $this->helper->one_time_message('success', 'Social Links Settings Updated Successfully');
            return redirect('admin/settings/social_links');
        }
    }

    // api_informations
    public function api_informations(Request $request)
    {
        if (!$_POST)
        {
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'app-store-credentials';
            $data['sub_sub_menu'] = 'api_informations';

            $data['recaptcha'] = $recaptcha = Setting::where('type', 'recaptcha')->pluck('value', 'name')->toArray();
            // dd($recaptcha);
            return view('admin.settings.api_credentials', $data);
        }
        else if ($_POST)
        {
            $rules = array(
                'captcha_secret_key' => 'required',
                'captcha_site_key'   => 'required',
                'firebase_key'       => 'required',
            );

            $fieldNames = array(
                'captcha_secret_key' => 'Captcha Secret Key',
                'captcha_site_key'   => 'Captcha Site Key',
                'firebase_key'       => 'Firebase Key',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                Setting::where(['name' => 'secret_key', 'type' => 'recaptcha'])->update(['value' => $request->captcha_secret_key]);
                Setting::where(['name' => 'site_key', 'type' => 'recaptcha'])->update(['value' => $request->captcha_site_key]);
                Setting::where(['name' => 'firebase_key', 'type' => 'recaptcha'])->update(['value' => $request->firebase_key]);

                $data = $request->all();
                // dd($data);
                Config::set([
                    'captcha.secret'  => isset($data['captcha_secret_key']) ? $data['captcha_secret_key'] : '',
                    'captcha.sitekey' => isset($data['captcha_site_key']) ? $data['captcha_site_key'] : '',
                ]);

                // changeEnvironmentVariable('CAPTCHA_SECRET', isset($data['captcha_secret_key']) ? $data['captcha_secret_key'] : '');
                // changeEnvironmentVariable('CAPTCHA_SITEKEY', isset($data['captcha_site_key']) ? $data['captcha_site_key'] : '');

                $this->helper->one_time_message('success', 'Api informations Settings Updated Successfully');
                return redirect('admin/settings/api_informations');
            }
        }
        else
        {
            return redirect('admin/settings/api_informations');
        }
    }

    // payment_methods
    public function payment_methods(Request $request)
    {
        if (!$_POST)
        {
            $data['menu'] = 'payment_methods';

            $data['paypal']      = $paypal      = Setting::where('type', 'PayPal')->pluck('value', 'name', 'id')->toArray();
            $data['stripe']      = $stripe      = Setting::where('type', 'Stripe')->pluck('value', 'name', 'id')->toArray();
            $data['twoCheckout'] = $twoCheckout = Setting::where('type', '2Checkout')->pluck('value', 'name', 'id')->toArray();

            $data['payUmoney'] = $payUmoney = Setting::where('type', 'PayUmoney')->pluck('value', 'name', 'id')->toArray();

            $data['coinPayments'] = $coinPayments = Setting::where('type', 'Coinpayments')->pluck('value', 'name', 'id')->toArray();

            return view('admin.settings.payment', $data);
        }
        else if ($_POST['gateway'] == 'paypal')
        {

            $rules = array(
                'client_id'     => 'required',
                'client_secret' => 'required',
            );

            $fieldNames = array(
                'client_id'     => 'PayPal Client ID',
                'client_secret' => 'PayPal Client Secret',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                // $data['success'] = 0;
                // $data['errors']  = $validator->messages();
                return back()->withErrors($validator)->withInput();
                // echo json_encode($data);
            }
            else
            {
                // dd($request->all());
                Setting::where(['name' => 'client_id', 'type' => 'PayPal'])->update(['value' => $request->client_id]);

                Setting::where(['name' => 'client_secret', 'type' => 'PayPal'])->update(['value' => $request->client_secret]);

                Setting::where(['name' => 'mode', 'type' => 'PayPal'])->update(['value' => $request->mode]);

                $this->helper->one_time_message('success', 'Payment Method Settings Updated Successfully');
                return redirect('admin/settings/payment_methods');
            }
        }
        else if ($_POST['gateway'] == 'stripe')
        {
            // dd('ss');
            $rules = array(
                'secret_key'      => 'required',
                'publishable_key' => 'required',
            );

            $fieldNames = array(
                'secret_key'      => 'Secret Key',
                'publishable_key' => 'Publishable Key',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
                // $data['success'] = 0;
                // $data['errors']  = $validator->messages();
                // echo json_encode($data);
            }
            else
            {
                Setting::where(['name' => 'secret', 'type' => 'Stripe'])->update(['value' => $request->secret_key]);
                Setting::where(['name' => 'publishable', 'type' => 'Stripe'])->update(['value' => $request->publishable_key]);
                $this->helper->one_time_message('success', 'Payment Method Settings Updated Successfully');
                return redirect('admin/settings/payment_methods');
                // $data['message'] = 'Updated Successfully';
                // $data['success'] = 1;
                // echo json_encode($data);
            }
        }
        else if ($_POST['gateway'] == 'twoCheckout')
        {
            $rules = array(
                'seller_id' => 'required',
            );

            $fieldNames = array(
                'seller_id' => 'Seller Id',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                Setting::where(['name' => 'seller_id', 'type' => '2Checkout'])->update(['value' => $request->seller_id]);
                Setting::where(['name' => 'mode', 'type' => '2Checkout'])->update(['value' => $request->mode]);

                $this->helper->one_time_message('success', 'Payment Method Settings Updated Successfully');
                return redirect('admin/settings/payment_methods');
            }
        }
        else if ($_POST['gateway'] == 'payUMoney')
        {
            $rules = array(
                'key'  => 'required',
                'salt' => 'required',
            );

            $fieldNames = array(
                'key'  => 'Key',
                'salt' => 'Salt',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                Setting::where(['name' => 'key', 'type' => 'PayUmoney'])->update(['value' => $request->key]);
                Setting::where(['name' => 'salt', 'type' => 'PayUmoney'])->update(['value' => $request->salt]);
                Setting::where(['name' => 'mode', 'type' => 'PayUmoney'])->update(['value' => $request->mode]);
                $this->helper->one_time_message('success', 'Payment Method Settings Updated Successfully');
                return redirect('admin/settings/payment_methods');
            }
        }
        else if ($_POST['gateway'] == 'coinPayments')
        {
            $rules = array(
                'merchant_id' => 'required',
                'private_key' => 'required',
                'public_key'  => 'required',
            );

            $fieldNames = array(
                'merchant_id' => 'Merchant Key',
                'private_key' => 'Private Key',
                'public_key'  => 'Public Key',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                // changeEnvironmentVariable('COIN_PAYMENT_MARCHANT_ID', $request->merchant_id);
                // changeEnvironmentVariable('COIN_PAYMENT_PRIVATE_KEY', $request->private_key);
                // changeEnvironmentVariable('COIN_PAYMENT_PUBLIC_KEY', $request->public_key);

                Setting::where(['name' => 'merchant_id', 'type' => 'Coinpayments'])->update(['value' => $request->merchant_id]);
                Setting::where(['name' => 'private_key', 'type' => 'Coinpayments'])->update(['value' => $request->private_key]);
                Setting::where(['name' => 'public_key', 'type' => 'Coinpayments'])->update(['value' => $request->public_key]);

                $this->helper->one_time_message('success', 'Payment Method Settings Updated Successfully');
                return redirect('admin/settings/payment_methods');
            }
        }
    }

    // preference - form
    public function preference()
    {
        $data['menu'] = 'settings';
        $data['sub_menu'] = 'general';
        $data['sub_sub_menu'] = 'preference';

        // $data['timezones'] = TimeZone::all();
        $data['timezones'] = $timezones = phpDefaultTimeZones();

        $pref     = Preference::where('category', 'preference')->get();
        $data_arr = [];
        foreach ($pref as $row)
        {
            $data_arr[$row->category][$row->field] = $row->value;
        }
        $data['prefData'] = $data_arr;

        return view('admin.settings.preference', $data);
    }

    // preference - save
    public function savePreference(Request $request)
    {
        $post = $request->all();
        // dd($post);

        unset($post['_token']);

        if ($post['date_format'] == 0)
        {
            $post['date_format_type'] = 'yyyy' . $post['date_sepa'] . 'mm' . $post['date_sepa'] . 'dd';
        }
        elseif ($post['date_format'] == 1)
        {
            $post['date_format_type'] = 'dd' . $post['date_sepa'] . 'mm' . $post['date_sepa'] . 'yyyy';
        }
        elseif ($post['date_format'] == 2)
        {
            $post['date_format_type'] = 'mm' . $post['date_sepa'] . 'dd' . $post['date_sepa'] . 'yyyy';
        }
        elseif ($post['date_format'] == 3)
        {
            $post['date_format_type'] = 'dd' . $post['date_sepa'] . 'M' . $post['date_sepa'] . 'yyyy';
        }
        elseif ($post['date_format'] == 4)
        {
            $post['date_format_type'] = 'yyyy' . $post['date_sepa'] . 'M' . $post['date_sepa'] . 'dd';
        }

        $i = 0;
        foreach ($post as $key => $value)
        {
            $data[$i]['category'] = "preference";
            $data[$i]['field']    = $key;
            $data[$i]['value']    = $value;
            $i++;
        }
        foreach ($data as $key => $value)
        {
            $category = $value['category'];
            $field    = $value['field'];
            $val      = $value['value'];
            $res      = Preference::where(['field' => $field])->first();
            // dd($res);
            // if (count($res) == 0)k
            if (empty($res))
            {
                DB::insert(DB::raw("INSERT INTO preferences(category,field,value) VALUES ('$category','$field','$val')"));
            }
            else
            {
                Preference::where(['category' => 'preference', 'field' => $field])->update(array('field' => $field, 'value' => $val));
            }
        }

        $pref = Preference::where('category', 'preference')->get();
        if (!empty($pref))
        {
            foreach ($pref as $value)
            {
                $prefer[$value->field] = $value->value;
            }
            Session::put($prefer);
        }
        // dd($prefer);
        $this->helper->one_time_message('success', 'Preferences Updated Successfully');
        return redirect('admin/settings/preference');
    }

    // Enable woocommerce - form
    public function enableWoocommerce(Request $request)
    {
        $wooCommerce = Setting::where(['type' => 'envato'])->get(['value', 'name'])->toArray();
        $wooCommerce = $this->helper->key_value('name', 'value', $wooCommerce);
        // dd($wooCommerce);

        if ($request->method() != 'POST')
        {
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'general';
            $data['sub_sub_menu'] = 'enablewoocommerce';
            $data['code_status']       = isset($wooCommerce['code_status']) ? $wooCommerce['code_status'] : '';
            $data['publicationStatus'] = isset($wooCommerce['publication_status']) ? $wooCommerce['publication_status'] : '';
            $data['plugin_name']       = isset($wooCommerce['plugin_name']) ? $wooCommerce['plugin_name'] : '';
            return view('admin.settings.enablewoocommerce', $data);
        }
        else
        {
            if ($request->key == 'purchasecodeverification')
            {
                $this->validate($request, [
                    'envatopurchasecode' => 'nullable|required',
                ], [
                    'envatopurchasecode.required' => 'The Purchase code field is required.',
                ]);

                $domainName     = request()->getHost();
                $domainIp       = request()->ip();
                $purchaseStatus = $this->getPurchaseStatus($domainName, $domainIp, $request->envatopurchasecode);
                $match          = ['type' => 'envato', 'name' => 'purchasecodeverificationstatus'];
                if ($purchaseStatus == 1)
                {
                    try
                    {
                        \DB::beginTransaction();

                        //Insert data for purchase code verification status to settings table
                        $Settings        = Setting::firstOrNew($match);
                        $Settings->name  = 'purchasecodeverificationstatus';
                        $Settings->value = 1;
                        $Settings->type  = 'envato';
                        $Settings->save();

                        //Insert data for code status to settings table
                        $matchs         = ['type' => 'envato', 'name' => 'code_status'];
                        $Setting        = Setting::firstOrNew($matchs);
                        $Setting->name  = 'code_status';
                        $Setting->value = 1;
                        $Setting->type  = 'envato';
                        $Setting->save();
                        \DB::commit();

                        $this->helper->one_time_message('success', 'Your purchase code is verified.You can upload plugin zip file now.');
                        return redirect('admin/settings/enable-woocommerce');
                    }
                    catch (\Exception $e)
                    {
                        \DB::rollBack();
                        $this->helper->one_time_message('error', $e->getMessage());
                        return redirect('admin/settings/enable-woocommerce');
                    }
                }
                else
                {
                    //Insert data for purchase code verification status to settings table
                    $Settings        = Setting::firstOrNew($match);
                    $Settings->name  = 'purchasecodeverificationstatus';
                    $Settings->value = 0;
                    $Settings->type  = 'envato';
                    $Settings->save();
                    return back()->withErrors(['envatopurchasecode' => 'Invalid purchase code'])->withInput();
                }
            }
            else
            {
                // dd($request->all());
                // dd($request->publication_status);

                if ((empty($request->plugin) || !empty($request->plugin)) && $request->publication_status != "Active")
                {
                    $this->validate($request, [
                        'publication_status' => 'required',
                    ]);
                    $statusUpdateSetting        = Setting::firstOrNew(['name' => 'publication_status', 'type' => 'envato']);
                    $statusUpdateSetting->name  = 'publication_status';
                    $statusUpdateSetting->value = $request->publication_status;
                    $statusUpdateSetting->type  = 'envato';
                    $statusUpdateSetting->save();
                    $this->helper->one_time_message('success', 'Plugin Uploaded Successfully');
                    return redirect('admin/settings/enable-woocommerce');
                }
                else
                {
                    $this->validate($request, [
                        'plugin'             => 'mimes:zip|max:2048',
                        'publication_status' => 'required',
                    ], [
                        'publication_status.required' => 'The Publication Status field is required.',
                        'plugin.required'             => 'The plugin field is required.',
                        'plugin.mimes'                => 'The plugin must be a zip file.',
                        'plugin.max'                  => 'The plugin file size must be less than 2 MB.',
                    ]);

                    try
                    {
                        \DB::beginTransaction();

                        $statusUpdateSetting        = Setting::firstOrNew(['name' => 'publication_status', 'type' => 'envato']);
                        $statusUpdateSetting->name  = 'publication_status';
                        $statusUpdateSetting->value = $request->publication_status;
                        $statusUpdateSetting->type  = 'envato';
                        $statusUpdateSetting->save();

                        if ($_FILES["plugin"]["error"] == 0)
                        {
                            $tmp_name = $_FILES["plugin"]["tmp_name"];
                            $name     = str_replace(' ', '_', $_FILES["plugin"]["name"]);
                            //
                            $location = public_path('uploads/woocommerce/' . $name);
                            if (file_exists($location))
                            {
                                unlink($location);
                            }
                            //
                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                            if ($ext == 'zip')
                            {
                                if (move_uploaded_file($tmp_name, $location))
                                {
                                    $fileSetting        = Setting::firstOrNew(['name' => 'plugin_name', 'type' => 'envato']);
                                    $fileSetting->name  = 'plugin_name';
                                    $fileSetting->value = $name;
                                    $fileSetting->type  = 'envato';
                                    $fileSetting->save();
                                    \DB::commit();
                                    $this->helper->one_time_message('success', 'Plugin Uploaded Successfully');
                                    return redirect('admin/settings/enable-woocommerce');
                                }
                                else
                                {
                                    \DB::rollBack();
                                    return back()->withErrors(['plugin' => 'Error in plugin upload'])->withInput();
                                }
                            }
                        }
                        else
                        {
                            $fileSetting        = Setting::firstOrNew(['name' => 'plugin_name', 'type' => 'envato']);
                            $fileSetting->name  = 'plugin_name';
                            $fileSetting->value = $request->pluginUploaded;
                            $fileSetting->type  = 'envato';
                            $fileSetting->save();
                            \DB::commit();
                            $this->helper->one_time_message('success', 'Plugin Uploaded Successfully');
                            return redirect('admin/settings/enable-woocommerce');
                        }
                    }
                    catch (\Exception $e)
                    {
                        \DB::rollBack();
                        $this->helper->one_time_message('error', $e->getMessage());
                        return redirect('admin/settings/enable-woocommerce');
                    }
                }
            }
        }
    }

    public function getPurchaseStatus($domainName, $domainIp, $envatopurchasecode)
    {
        $data = array(
            'domain_name'        => $domainName,
            'domain_ip'          => $domainIp,
            'envatopurchasecode' => $envatopurchasecode,
        );
        $url = "https://envatoapi.techvill.org/";
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POSTREDIR, 3);
        $output = curl_exec($ch);
        if ($output == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
     // virtual_card
    
    public function virtual_card()
    {
        $data['menu'] = 'virtual_card';
        $general      = DB::table('virtual_card_limit')->get();
        $data['result'] = $general;
        return view('admin.settings.virtual_card', $data);
    }
    
    public function updateVirtualCard(Request $request)
    {
        $data = [
           'min_limit' => $request->input('min_limit'),
           'max_limit' => $request->input('max_limit')
        ];
       
        $rs = DB::table('virtual_card_limit')->where(['id'=> '1'])->update($data);
       
        if($rs){
           return back();
        }
    }
    
    public function revenues(Request $request)
    {
        if (!$_POST)
        {
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'general';
            $data['sub_sub_menu'] = 'revenues';
            
            $general        = Setting::where('type', 'revenue')->get()->toArray();
            $data['result'] = $result = $this->helper->key_value('name', 'value', $general);
            $data['logs'] = RevenueLog::orderBy('id', 'desc')->get();
            
            return view('admin.settings.revenues', $data);
        }
        elseif ($_POST)
        {
            //dd($request->all());
            if($request->all()){
                
                $transactions = Transaction::get();
        
                $transactional = Setting::where('id', '37')->first();
                $operational   = Setting::where('id', '38')->first();
                $operational_a = Setting::where('id', '39')->first();
                $operational_b = Setting::where('id', '40')->first();
                
                foreach($transactions as $transaction){
                    $new_transaction = Revenue::where('transaction_id', $transaction->id)->exists();
                    if(!$new_transaction){
                        $revenue = ($transaction->charge_percentage + $transaction->charge_fixed); 
                        $transactional_revenue = ($transactional->value*$revenue)/100;
                        $operational_revenue = ($operational->value*$revenue)/100;
                        $operationala_revenue = ($operational_a->value*$revenue)/100;
                        
                        $revnue = Revenue::create([
                            'transactional' => $transactional_revenue,
                            'operational' => $operational_revenue, 
                            'operational_a' => $operationala_revenue, 
                            'transaction_id' => $transaction->id, 
                            'currency_id' => $transaction->currency_id
                        ]);
                    }
                }
                
                Setting::where(['name' => 'transactional'])->update(['value' => $request->transactional]);
                Setting::where(['name' => 'operational'])->update(['value' => $request->operational]);
                Setting::where(['name' => 'operational_a'])->update(['value' => $request->operational_a]);
                Setting::where(['name' => 'operational_b'])->update(['value' => $request->operational_b]);
                
                $rs = RevenueLog::create([
                    'transactional' => $request->input('transactional'),
                    'operational' => $request->input('operational'), 
                    'operational_a' => $request->input('operational_a'), 
                    'operational_b' => $request->input('operational_b'), 
                    'changed_by' => Auth::guard('admin')->id(), 
                ]);
            }
        
            $this->helper->one_time_message('success', 'Revenue Settings Updated Successfully');
            return redirect('admin/settings/revenues');
        }
    }
    
    public function key_informations(Request $request)
    {
        $data['menu'] = 'settings';
        $data['sub_menu'] = 'app-store-credentials';
        $data['sub_sub_menu'] = 'key_informations';

        $data['giftcard'] = Setting::where('type', 'giftcard')->pluck('value', 'name')->toArray();
        $data['persona']  = Setting::where('type', 'persona')->pluck('value', 'name')->toArray();
        $data['ding']     = Setting::where('type', 'ding')->pluck('value', 'name')->toArray();
        $data['plaid']    = Setting::where('type', 'plaid')->pluck('value', 'name')->toArray();

        return view('admin.settings.keys', $data);
    }
    
    public function giftcard_informations(Request $request)
    {
        $rules = array(
            'main_url' => 'required',
            'client_id' => 'required',
            'client_secret' => 'required',
        );
        
        $fieldNames = array(
            'main_url' => 'Main url',
            'client_id'   => 'client Id',
            'client_secret'   => 'Client Secret',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        Setting::where(['name' => 'main_url', 'type' => 'giftcard'])->update(['value' => $request->main_url]);
        Setting::where(['name' => 'client_id', 'type' => 'giftcard'])->update(['value' => $request->client_id]);
        Setting::where(['name' => 'client_secret', 'type' => 'giftcard'])->update(['value' => $request->client_secret]);

        $this->helper->one_time_message('success', 'Gift Card Informations Settings Updated Successfully');
        return redirect('admin/settings/key_informations');
    }
    
    public function persona_informations(Request $request)
    {
        $rules = array(
            'mode' => 'required',
            'persona_api_key' => 'required',
            'persona_templete' =>'required'
        );
        
        $fieldNames = array(
            'mode' => 'Mode',
            'persona_api_key' => 'Persona Api Key',
            'persona_templete' => 'Persona Templete',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        Setting::where(['name' => 'persona_api_key', 'type' => 'persona'])->update(['value' => $request->persona_api_key]);
        Setting::where(['name' => 'persona_templete', 'type' => 'persona'])->update(['value' => $request->persona_templete]);
        Setting::where(['name' => 'mode', 'type' => 'persona'])->update(['value' => $request->mode]);

        $this->helper->one_time_message('success', 'Persona Informations Settings Updated Successfully');
        return redirect('admin/settings/key_informations');
    }

    public function ding_informations(Request $request)
    {
        $rules = array(
            'ding_main_url' => 'required',
            'ding_api_key'   => 'required',
        );
        
        $fieldNames = array(
            'ding_main_url' => 'Main url',
            'ding_api_key'   => 'client Id',

        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        Setting::where(['name' => 'ding_main_url', 'type' => 'ding'])->update(['value' => $request->ding_main_url]);
        Setting::where(['name' => 'ding_api_key', 'type' => 'ding'])->update(['value' => $request->ding_api_key]);

        $this->helper->one_time_message('success', 'Ding Informations Settings Updated Successfully');
        return redirect('admin/settings/key_informations');
    }
    
    public function plaid_informations(Request $request)
    {
        $rules = array(
            'plaid_base_url' => 'required',
            'plaid_client_id' => 'required',
            'plaid_client_secret' =>'required',
            'stripe_webhook_key' =>'required'
        );
        
        $fieldNames = array(
            'plaid_base_url' => 'Plaid Base Url',
            'plaid_client_id' => 'Plaid Client Id',
            'plaid_client_secret' => 'Plaid Client Secret',
            'stripe_webhook_key' => 'Stripe Webhook Key',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        Setting::where(['name' => 'plaid_base_url', 'type' => 'plaid'])->update(['value' => $request->plaid_base_url]);
        Setting::where(['name' => 'plaid_client_id', 'type' => 'plaid'])->update(['value' => $request->plaid_client_id]);
        Setting::where(['name' => 'plaid_client_secret', 'type' => 'plaid'])->update(['value' => $request->plaid_client_secret]);
        Setting::where(['name' => 'stripe_webhook_key', 'type' => 'plaid'])->update(['value' => $request->stripe_webhook_key]);

        $this->helper->one_time_message('success', 'Plaid Informations Settings Updated Successfully');
        return redirect('admin/settings/key_informations');
    }
    
    public function Services(Request $request, $type=null)
    {
        if (!$_POST && $type=='view')
        {
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'app-store-credentials';
            $data['sub_sub_menu'] = 'services';
            $data['services'] = Services::orderBy('id', 'desc')->get();
            
            return view('admin.services.services', $data);
        }
        
        if(!$_POST && $type=='add')
        {
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'app-store-credentials';
            $data['sub_sub_menu'] = 'services';
            
            $data['app_pages']=DB::table('app_pages')->get();
            return view('admin.services.details', $data);
        }
        
        if($_POST && $type=='add')
        {
               
                $rules = array(
                'name' => 'required',
                'page'   => 'required',

            );
            $fieldNames = array(
                'name' => 'Main url',
                'page'   => 'Api key',

            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
               
             $details=$request->except(['_token']);
               
            $pic = $request->file('image');
            if (isset($pic))
            {
                $upload = 'public/uploads/userPic';
               
                $filename  = $pic->getClientOriginalName();
                $request->image->move(public_path(), $filename);
                $details['image']=$filename;
                
            }
              Services::create($details);
              return redirect('admin/settings/services/view');
            }
            
        }
        else
        {
            return redirect('admin/settings/services');
        }
    }
    
    public function ServicesEdit(Request $request,$id=null)
    {
        $data['menu'] = 'settings';
        $data['sub_menu'] = 'app-store-credentials';
        $data['sub_sub_menu'] = 'services';

        if(!$_POST){
            
            $data['details']=Services::where('id',$id)->first();
            $data['app_pages']=DB::table('app_pages')->get();
            return view('admin.services.edit', $data);
            
        }
        else{
               
                $rules = array(
                'name' => 'required',
                'page'   => 'required',

            );
            $fieldNames = array(
                'name' => 'Main url',
                'page'   => 'Api key',

            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
               
             $details=$request->except(['_token']);
               
            $pic = $request->file('image');
            if (isset($pic))
            {
                $upload = 'public/uploads/userPic';
                $removeimage=Services::where('id',$id)->first()->image??'';
                if($removeimage){
                    unlink(public_path().'/'.$removeimage);
                }
                $filename  = $pic->getClientOriginalName();
                $request->image->move(public_path(), $filename);
                $details['image']=$filename;
                
            }
              Services::where('id',$id)->update($details);
              return redirect('admin/settings/services/view');
            }
            
        }
       
    }
    
    public function ServicesDelete(Request $request,$id=null)
    {
        $data['menu'] = 'services';

        if(!$_POST){
            $data['details']=Services::where('id',$id)->first();
            $data['app_pages']=DB::table('app_pages')->get();
            Services::where('id',$id)->delete();
            return redirect('admin/settings/services/view');
        }
    }
    
    public function mpos_fees()
    {
        $data['menu'] = 'settings';
        $data['settings_menu'] = 'mposfees';
        
        $general        = Setting::where('type', 'mpos')->get()->toArray();
        $data['result'] = $result = $this->helper->key_value('name', 'value', $general);
        return view('admin.settings.mpos', $data);
    }
    
    public function mpos_fees_update(Request $request)
    {
        Setting::where(['name' => 'mpos_fee'])->update(['value' => $request->mpos_fee]);
       
        $this->helper->one_time_message('success', 'Fee Settings Updated Successfully');
            return redirect('admin/settings/fee');
    }
    
    public function nfc_credntials()
    {
        $data['menu'] = 'settings';
        $data['sub_menu'] = 'app-store-credentials';
        $data['sub_sub_menu'] = 'nfc_credentials';
        
        $data['credential'] = NfcCredential::where('id', '1')->first();
        return view('admin.settings.nfc', $data);
    }
    
    public function nfc_credntials_update(Request $request)
    {
        NfcCredential::where(['id' => '1'])->update([
            'pub_key' => $request->pub_key,
            'sec_key' => $request->sec_key,
            'mode' => $request->mode,
            'status' => $request->status,
        ]);
       
        $this->helper->one_time_message('success', 'NFC Credentials Updated Successfully');
        return redirect('admin/settings/nfc');
    }
}
