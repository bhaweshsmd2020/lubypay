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
use App\Models\Survey;

class SurveyController extends Controller
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
        $data['menu']     = 'survey';
        $data['settings'] = Survey::orderBy('id', 'desc')->get();
        return view('admin.survey.index', $data);
    }
    
    public function add()
    {
        $data['menu']     = 'survey';
        $data['languages'] = Language::where('status', 'Active')->orderBy('id', 'asc')->get();
        return view('admin.survey.create', $data);
    }
    
    public function store(Request $request)
    {
        $setting = Survey::create([
            'url' => $request->url,
            'message_en' => $request->message_en,
            'message_es' => $request->message_es,
            'message_fr' => $request->message_fr,
            'message_ht' => $request->message_ht,
            'message_pt' => $request->message_pt,
            'message_pm' => $request->message_pm,
            'user_type' => $request->user_type,
        ]);
        
        $currency = "9";
        $type = "survey";
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
                    $template = LanguageContent::where('string', 'Send_survey')->first();
                 
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
                    
                    $this->helper->push_survey($subject, $check_message, $device->user_id, $currency, $type, $setting->url);
                }
            }
        }
        
        $this->helper->one_time_message('success', 'Survey Created Successfully');
        return redirect('admin/survey');
    }
    
    public function edit($id)
    {
        $data['menu']     = 'survey';
        $data['setting'] = Survey::where('id', $id)->first();
        $data['languages'] = Language::where('status', 'Active')->orderBy('id', 'asc')->get();
        return view('admin.survey.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        $rs = Survey::where('id', $id)->update([
            'url' => $request->url,
            'message_en' => $request->message_en,
            'message_es' => $request->message_es,
            'message_fr' => $request->message_fr,
            'message_ht' => $request->message_ht,
            'message_pt' => $request->message_pt,
            'message_pm' => $request->message_pm,
            'user_type' => $request->user_type,
        ]);
        
        $this->helper->one_time_message('success', 'Survey Updated Successfully');
        return redirect('admin/survey');
    }
    
    public function delete($id)
    {
        $setting = Survey::where('id', $id)->delete();
        
        $this->helper->one_time_message('success', 'Survey Deleted Successfully');
        return redirect('admin/survey');
    }
    
    public function remind($id)
    {
        $setting = Survey::where('id', $id)->first();
        
	    $currency = "9";
        $type = "survey";
        $date = date("Y-m-d h:i:s");
        
        if($setting->user_type == '1'){
            $devices = DB::table('devices')->where('user_type', '2')->groupBy('device_id')->get();
        }elseif($setting->user_type == '2'){
            $devices = DB::table('devices')->where('user_type', '3')->groupBy('device_id')->get();
        }if($setting->user_type == '3'){
            $devices = DB::table('devices')->groupBy('device_id')->get();
        }
        
        $languages = Language::where('status', 'Active')->orderBy('id', 'asc')->get();
        $template = LanguageContent::where('string', 'Send_survey')->first();
        
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
                    
                    $this->helper->push_survey($subject, $check_message, $device->user_id, $currency, $type, $setting->url);
                }
            }
        }
        
        $this->helper->one_time_message('success', 'Survey Reminder Sent Successfully');
        return redirect('admin/survey');
    }
}

