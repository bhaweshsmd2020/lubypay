<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Currency;
use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\MaintenanceSetting;
use App\Models\Language;
use App\Models\LanguageContent;
use Carbon\Carbon;
use App\Models\User;

class MaintainanceController extends Controller
{
    protected $helper, $email, $currency, $user;
    protected $cryptoCurrency;

    public function __construct()
    {
        $this->helper         = new Common();
        $this->email          = new EmailController();
        $this->currency       = new Currency();
    }
    
    protected $data = [];
    
    public function index()
    {
        $data['menu']     = 'maintainance';
        $data['settings'] = MaintenanceSetting::orderBy('id', 'desc')->get();
        return view('admin.maintainance.index', $data);
    }
    
    public function add()
    {
        $data['menu']     = 'maintainance';
        $data['languages'] = Language::where('status', 'Active')->orderBy('id', 'asc')->get();
        return view('admin.maintainance.create', $data);
    }
    
    public function store(Request $request)
    {
        $setting = MaintenanceSetting::create([
            'subject' => $request->subject,
            'date' => $request->date,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time,
            'message_en' => $request->message_en,
            'message_es' => $request->message_es,
            'message_fr' => $request->message_fr,
            'message_ht' => $request->message_ht,
            'message_pt' => $request->message_pt,
            'message_pm' => $request->message_pm,
            'user_type' => $request->user_type,
        ]);
        
        $currency = "9";
        $type = "maintainance";
        $date = date("Y-m-d h:i:s");
        
        if($setting->user_type == '1'){
            $devices = DB::table('devices')->where('user_type', '2')->groupBy('device_id')->get();
        }elseif($setting->user_type == '2'){
            $devices = DB::table('devices')->where('user_type', '3')->groupBy('device_id')->get();
        }if($setting->user_type == '3'){
            $devices = DB::table('devices')->groupBy('device_id')->get();
        }
        
        $languages = Language::where('status', 'Active')->orderBy('id', 'asc')->get();
        
        foreach($devices as $device){
            foreach($languages as $language){
                if($device->language == $language->id){
                    
                    $short_code = $language['short_name'];
                    $template = LanguageContent::where('string', 'Scheduled_app_maintenance')->first();
                 
                    $subject = $template->$short_code;
                    $message = 'message_'.$short_code;
                    if(!empty($setting->$message)){
                        $newmessage = $setting->$message;
                    }else{
                        $newmessage = $setting->message_en;
                    }
                    
                    $ondate = Carbon::parse($setting->date)->format('d M');
                    $fromtime = Carbon::parse($setting->from_time)->format('h:i A');
                    $totime = Carbon::parse($setting->to_time)->format('h:i A');
                    $zone = Carbon::now();
                    
                    $check_message = 'On '.$ondate.' from '.$fromtime.' to '.$totime.' ('.$zone->tzName.'+0), '.$newmessage;
                    
                    $this->helper->sendFirabasePush($subject, $check_message, $device->user_id, $currency, $type);
                }
            }
        }
        
        $mail_message = $setting->message_en;
        $mail_ondate = Carbon::parse($setting->date)->format('d M');
        $mail_fromtime = Carbon::parse($setting->from_time)->format('h:i A');
        $mail_totime = Carbon::parse($setting->to_time)->format('h:i A');
        $mail_zone = Carbon::now();
        $new_message = 'On '.$mail_ondate.' from '.$mail_fromtime.' to '.$mail_totime.' ('.$mail_zone->tzName.'+0), '.$mail_message;
        
        if($setting->user_type == '1'){
            $users = User::where('role_id', '2')->get();
        }elseif($setting->user_type == '2'){
            $users = User::where('role_id', '3')->get();
        }if($setting->user_type == '3'){
            $users = User::get();
        }
        
        foreach($users as $user){
            
            $userdevice = DB::table('devices')->where('user_id', $user->id)->first();
            if(!empty($userdevice)){
                $device_lang = $userdevice->language;
            }else{
                $device_lang = getDefaultLanguage();
            }

            $email_template = EmailTemplate::where(['temp_id' => 67, 'language_id' => $device_lang, 'type' => 'email'])->select('subject', 'body')->first();
            $mail_subject = $email_template->subject;
        
            $mail_message = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $email_template->body);
            $mail_message = str_replace('{message}', $new_message, $mail_message);
            $mail_message = str_replace('{soft_name}', getCompanyName(), $mail_message);
            $this->email->sendEmail($user->email, $mail_subject, $mail_message);
        }
        
        $this->helper->one_time_message('success', 'Maintenance Setting Created Successfully');
        return redirect('admin/maintainance-settings');
    }
    
    public function edit($id)
    {
        $data['menu']     = 'maintainance';
        $data['setting'] = MaintenanceSetting::where('id', $id)->first();
        $data['languages'] = Language::where('status', 'Active')->orderBy('id', 'asc')->get();
        return view('admin.maintainance.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        $rs = MaintenanceSetting::where('id', $id)->update([
            'subject' => $request->subject,
            'date' => $request->date,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time,
            'message_en' => $request->message_en,
            'message_es' => $request->message_es,
            'message_fr' => $request->message_fr,
            'message_ht' => $request->message_ht,
            'message_pt' => $request->message_pt,
            'message_pm' => $request->message_pm,
            'user_type' => $request->user_type,
        ]);
        
        $this->helper->one_time_message('success', 'Maintenance Setting Updated Successfully');
        return redirect('admin/maintainance-settings');
    }
    
    public function delete($id)
    {
        $setting = MaintenanceSetting::where('id', $id)->delete();
        
        $this->helper->one_time_message('success', 'Maintenance Setting Deleted Successfully');
        return redirect('admin/maintainance-settings');
    }
    
    public function remind($id)
    {
        $setting = MaintenanceSetting::where('id', $id)->first();
        
	    $currency = "9";
        $type = "maintainance";
        $date = date("Y-m-d h:i:s");
        
        if($setting->user_type == '1'){
            $devices = DB::table('devices')->where('user_type', '2')->groupBy('device_id')->get();
        }elseif($setting->user_type == '2'){
            $devices = DB::table('devices')->where('user_type', '3')->groupBy('device_id')->get();
        }if($setting->user_type == '3'){
            $devices = DB::table('devices')->groupBy('device_id')->get();
        }
        
        $languages = Language::where('status', 'Active')->orderBy('id', 'asc')->get();
        $template = LanguageContent::where('string', 'Scheduled_app_maintenance')->first();
        
        foreach($devices as $device){
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
                    
                    $ondate = Carbon::parse($setting->date)->format('d M');
                    $fromtime = Carbon::parse($setting->from_time)->format('h:i A');
                    $totime = Carbon::parse($setting->to_time)->format('h:i A');
                    $zone = Carbon::now();
                    
                    $check_message = 'On '.$ondate.' from '.$fromtime.' to '.$totime.' ('.$zone->tzName.'+0), '.$newmessage;
                    
                    $this->helper->sendFirabasePush($subject, $check_message, $device->user_id, $currency, $type);
                }
            }
        }
        
        $mail_message = $setting->message_en;
        $mail_ondate = Carbon::parse($setting->date)->format('d M');
        $mail_fromtime = Carbon::parse($setting->from_time)->format('h:i A');
        $mail_totime = Carbon::parse($setting->to_time)->format('h:i A');
        $mail_zone = Carbon::now();
        $new_message = 'On '.$mail_ondate.' from '.$mail_fromtime.' to '.$mail_totime.' ('.$mail_zone->tzName.'+0), '.$mail_message;
        
        if($setting->user_type == '1'){
            $users = User::where('role_id', '2')->get();
        }elseif($setting->user_type == '2'){
            $users = User::where('role_id', '3')->get();
        }if($setting->user_type == '3'){
            $users = User::get();
        }
        
        foreach($users as $user){
            
            $userdevice = DB::table('devices')->where('user_id', $user->id)->first();
            if(!empty($userdevice)){
                $device_lang = $userdevice->language;
            }else{
                $device_lang = getDefaultLanguage();
            }

            $email_template = EmailTemplate::where(['temp_id' => 67, 'language_id' => $device_lang, 'type' => 'email'])->select('subject', 'body')->first();
            $mail_subject = $email_template->subject;
            
            $mail_message = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $email_template->body);
            $mail_message = str_replace('{message}', $new_message, $mail_message);
            $mail_message = str_replace('{soft_name}', getCompanyName(), $mail_message);
            $this->email->sendEmail($user->email, $mail_subject, $mail_message);
        }
        
        $this->helper->one_time_message('success', 'Maintenance Setting Reminder Sent Successfully');
        return redirect('admin/maintainance-settings');
    }
    
    public function remind_sms($id)
    {
        $data['menu']     = 'maintainance';
        $setting = MaintenanceSetting::where('id', $id)->first();
        
        if($setting->user_type == '1'){
            $devices = DB::table('devices')->where('user_type', '2')->groupBy('device_id')->get();
        }elseif($setting->user_type == '2'){
            $devices = DB::table('devices')->where('user_type', '3')->groupBy('device_id')->get();
        }if($setting->user_type == '3'){
            $devices = DB::table('devices')->groupBy('device_id')->get();
        }
        
        $languages = Language::where('status', 'Active')->orderBy('id', 'asc')->get();
        $template = LanguageContent::where('string', 'Scheduled_app_maintenance')->first();
        
        foreach($devices as $device){
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
                    
                    $ondate = Carbon::parse($setting->date)->format('d M');
                    $fromtime = Carbon::parse($setting->from_time)->format('h:i A');
                    $totime = Carbon::parse($setting->to_time)->format('h:i A');
                    $zone = Carbon::now();
                    
                    $check_message = 'On '.$ondate.' from '.$fromtime.' to '.$totime.' ('.$zone->tzName.'+0), '.$newmessage;
                }
            }
        }
        
        $data['ms_id'] = $id;
        $data['subject'] = $subject;
        $data['message'] = $check_message;
        $data['users'] = User::get();
        
        return view('admin.maintainance.sms', $data);
    }
    
    public function remind_sms_send(Request $request, $id)
    {
        $message = $request->message;
        $user_id = $request->user;
        $user = User::where('id', $user_id)->first();

        $accountSID   = 'AC8e6ad270231cac6b1f01e970addb5e8c';
        $authToken    = 'e9155db11f863668bed955dc33f7b04f';
        $twilioNumber = '+12408394973';
        $trimmedMsg   = trim(preg_replace('/\s\s+/', ' ', $message));
        $to = $user->formattedPhone;
    
        $client = new \Twilio\Rest\Client($accountSID, $authToken);
        $client->messages->create(
            $to,
            array(
                'from' => $twilioNumber,
                'body' => strip_tags($trimmedMsg)
            )
        );
        
        $this->helper->one_time_message('success', 'Maintenance Setting Reminder Sent Successfully');
        return redirect('admin/maintainance-settings');
    }
}

