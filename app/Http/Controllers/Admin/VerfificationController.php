<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\DocumentVerification;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use DB;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;
use App\Models\Kycdatastore;
use App\Models\Setting;
use App\Models\UserDetail;
use App\Models\Country;
use App\Models\ApplyCard;
use App\Models\Card;

class VerfificationController extends Controller
{
    protected $helper;
    protected $documentVerification;
    protected $email;

    public function __construct()
    {
        $this->helper               = new Common();
        $this->documentVerification = new DocumentVerification();
        $this->email                = new EmailController();
        $this->settings             =  Setting::where('type', 'persona')->pluck('value', 'name')->toArray();
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

    public function index($id)
    {
        $data['menu']     = 'proofs';
        $data['sub_menu'] = 'proofs';
        $data['documentVerificationStatus'] = $this->documentVerification->where(['user_id' => $id])->get();
        $data['users'] = User::find($id);
        
        $autokyc = Kycdatastore::where('user_id', $id)->first();
        if(!empty($autokyc)){
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://withpersona.com/api/v1/inquiries/'.$autokyc->proof_id,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->settings['persona_api_key'],
                'accept: application/json'
              ),
            ));
    
            $response = curl_exec($curl);
            $kyc_details = json_decode($response);
            
            if(!isset($kyc_details->errors)){
                if(!empty($kyc_details)){
                    
                    if($kyc_details->data->type=='inquiry'){
                        $decode = json_decode(json_encode($kyc_details->data->attributes),true);
                         
                        $kycdata['reference_id']=$decode['reference-id']??'';
                        if($decode['status']){
                            $status='completed';
                        }else{
                            $status=$decode['status'];
                        }
                        
                        $kycdata['status']=$status??'';
                        $kycdata['name_first']=$decode['name-first']??'';
                        $kycdata['name_middle']=$decode['name-middle']??'';
                        $kycdata['name_last']=$decode['name-last']??'';
                        $kycdata['birthdate']=$decode['birthdate']??'';
                        $kycdata['addressstreet1']=$decode['address-street-1']??'';
                        $kycdata['addressstreet2']=$decode['address-street-2']??'';
                        $kycdata['address_city']=$decode['address-city']??'';
                        $kycdata['address_subdivision']=$decode['address-subdivision']??'';
                        $kycdata['address_subdivision_abb']=$decode['address-subdivision-abb']??'';
                        $kycdata['address_postal_code']=$decode['address-postal-code']??'';
                        $kycdata['address_postal_code_abbr']=$decode['address-postal-code-abbr']??'';
                        $kycdata['identification_number']=$decode['identification-number']??'';
                        $kycdata['email_address']=$decode['email-address']??'';
                        $kycdata['phone_number']=$decode['phone-number']??'';
                        $kycdata['created_at']=$decode['created-at']??'';
                        $kycdata['account_id']=$kyc_details->data->relationships->account->data->id??'';
                        $kycdata['selected_id_class']=$decode['fields']['selected-id-class']['value'];
                        $kycdata['selected_country_code']=$decode['fields']['selected-country-code']['value'];
                    }
                }
    
                foreach($kyc_details->included as $key=>$value){
                    if($value->type==="verification/government-id"){
                        $decode = json_decode(json_encode($value->attributes),true);
                        $decode['front-photo-url'];
                        $this->DonloadFiles($id,$decode['front-photo-url'],'front_photo_url');
                        $this->DonloadFiles($id,$decode['back-photo-url'],'back_photo_url');
                        $this->DonloadFiles($id,$decode['selfie-photo-url'],'selfie_photo_url');
                    }
                    
                    if($value->type==='verification/selfie'){
                        $decode = json_decode(json_encode($value->attributes),true);
                        $this->DonloadFiles($id,$decode['left-photo-url'],'left_photo_url');
                        $this->DonloadFiles($id,$decode['center-photo-url'],'center_photo_url');
                        $this->DonloadFiles($id,$decode['right-photo-url'],'right_photo_url');
                    }
                    
                    if($value->type==='inquiry-session'){
                        $decode = json_decode(json_encode($value->attributes),true);
                        $kycdata['created_at']=$decode['created-at']??'';
                        $kycdata['ip_address']=$decode['ip-address']??'';
                        $kycdata['user_agent']=$decode['user-agent']??'';
                        $kycdata['os_name']=$decode['os-name']??'';
                        $kycdata['os_full_version']=$decode['os-full-version']??'';
                        $kycdata['device_type']=$decode['device-type']??'';
                        $kycdata['device_name']=$decode['device-name']??'';
                        $kycdata['browser_name']=$decode['browser-name']??'';
                        $kycdata['country_code']=$decode['country-code']??'';
                        $kycdata['country_name']=$decode['country-name']??'';
                        $kycdata['region_code']=$decode['region-code']??'';
                        $kycdata['region_name']=$decode['region-name']??'';
                        $kycdata['latitude']=$decode['latitude']??'';
                        $kycdata['longitude']=$decode['longitude']??'';
                        $kycdata['threat_level']=$decode['threat-level'];
                    }
                    
                    if($value->type==='document/government-id'){
                        $decode = json_decode(json_encode($value->attributes),true);
                        $frontphoto=$decode['front-photo'];
                        $backphoto=$decode['back-photo'];
                        $selfiephoto=$decode['selfie-photo'];
                        if(!empty($frontphoto)){
                            $frontphotourl=$frontphoto['url'];
                            $this->DonloadFiles($id,$frontphotourl,'front_photo');
                        }
                        if(!empty($backphoto)){
                            $backphotourl=$backphoto['url'];
                            $this->DonloadFiles($id,$backphotourl,'back_photo');
                        }
                        if(!empty($selfiephoto)){
                            $selfiephotourl=$selfiephoto['url'];
                            $this->DonloadFiles($id,$selfiephotourl,'selfie_photo');
                        }
                    }
                }
                
                Kycdatastore::where('proof_id', $autokyc->proof_id)->update($kycdata);
                $data['autokyc_details'] = Kycdatastore::where('proof_id', $autokyc->proof_id)->first();
            }
        }else{
            $data['autokyc_details'] = 'N/A';
        }
        
        return view('admin.verifications.list', $data);
    }
    
    public function DonloadFiles($id,$file_url,$colum_name)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL =>$file_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->settings['persona_api_key'],
            'accept: application/json',
            'Persona-Version: 2021-07-05',
          ),
        ));
        
        $response = curl_exec($curl);
        
        $persona_url = public_path('/kyc_documents/');
        
        $image=rand();
        $filename = $persona_url.$image.'.jpg';
        file_put_contents($filename, $response);
        $oldfile=Kycdatastore::where('proof_id',$id)->first()->$colum_name??'';
        if($oldfile){
            $image_path = $persona_url.$oldfile; 
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        Kycdatastore::where('proof_id',$id)->update([$colum_name=>$image.'.jpg']);
        return;
    }


    public function update(Request $request)
    {
        $documentVerification         = DocumentVerification::find($request->id);
        $updated_by = Auth::guard('admin')->user()->id;
        $documentVerification->updated_by = $updated_by??'';
        $documentVerification->status = $request->status;
        $documentVerification->save();

        $user = User::find($request->user_id);
        if ($request->verification_type == 'photo')
        {
            if ($request->status == 'approved')
            {
                $user->photo_verified = true;
            }
            else
            {
                $user->photo_verified = false;
            }
            
            $notification_temp = '18';
        }
        if ($request->verification_type == 'address')
        {
            if ($request->status == 'approved')
            {
                $user->address_verified = true;
            }
            else
            {
                $user->address_verified = false;
            }
            
            $notification_temp = '19';
        }
        if ($request->verification_type == 'identity')
        {
            if ($request->status == 'approved')
            {
                $user->identity_verified = true;
            }
            else
            {
                $user->identity_verified = false;
            }
            
            $notification_temp = '20';
        }
        if ($request->verification_type == 'video')
        {
            if ($request->status == 'approved')
            {
                $user->video_verified = true;
            }
            else
            {
                $user->video_verified = false;
            }
            
            $notification_temp = '35';
        }
        $user->save();
        
        if($request->status == 'pending'){
            User::where('id', $request->user_id)->update(['kyc_status' => '2']);
        }elseif($request->status == 'rejected'){
            User::where('id', $request->user_id)->update(['kyc_status' => '3']);
        }
        
        if($user->photo_verified == '1' && $user->address_verified == '1' && $user->identity_verified == '1' && $user->video_verified == '1'){
            
            $url = env('BASE_URL').'user-status';
        
            $headers = [
                'Host: cards.lubypay.com',
                'Content-Type: application/json',
            ];
            
            $payloads = [
                'user_email' => $user->email,
                'platform' => 'Sandbox Ewallet'
            ];
            
            $kyc = $this->postFunction($url, $headers, $payloads);
            if($kyc['data']['card_user_status'] == 'INVITE_PENDING'){
                $this->helper->one_time_message('success', 'Invite is in pending!');
                return back();
            }elseif($kyc['data']['card_user_status'] == 'INVITE_EXPIRED'){
                $this->helper->one_time_message('success', 'Invite has expired!');
                return back();
            }elseif($kyc['data']['card_user_status'] == 'USER_ACTIVE'){
                User::where('id', $request->user_id)->update([
                    'card_user_id' => $kyc['data']['card_user_id'], 
                    'card_user_status' => $kyc['data']['card_user_status'],
                    'kyc_status' => '1',
                    'kyc_verified_on' => now()->toDateTimeString()
                ]);
                
                if(!empty($kyc['data']['plan_data'])){
                    User::where('id', $request->user_id)->update([
                        'plan_data' => $kyc['data']['plan_data'],
                        'plan_id' => $kyc['data']['plan_id'],
                        'plan_name' => $kyc['data']['plan_name'],
                        'plan_amount' => $kyc['data']['plan_amount'],
                        'will_expire' => $kyc['data']['will_expire']
                    ]);
                    
                    $apply_card = ApplyCard::where('user_id', $request->user_id)->first();
                    if(empty($apply_card)){
                        $cardUser = User::find($request->user_id);
                        $cardUserDetail = UserDetail::where('user_id', $request->user_id)->first();
                        $cardUserCountry = Country::where('id', $cardUserDetail->country)->first();
                        
                        $card = ApplyCard::create([
                            'user_id' => $request->user_id,
                            'address_line' => $cardUserDetail->address_1,
                            'city' => $cardUserDetail->city,
                            'state' => $cardUserDetail->state,
                            'country' => $cardUserCountry->name,
                            'postal_code' => $cardUserDetail->zip_code
                        ]);
                        
                        $cardurl = env('BASE_URL').'platform-cards';
                        
                        $cards = $this->postFunction($cardurl, $headers, $payloads);
                        if(count($cards['data']) > 0){
                            foreach($cards['data'] as $card){
                                Card::create([
                                    'user_id' => $request->user_id,
                                    'invite_id' => $card['invite_id'],
                                    'card_holder' => $card['name_on_card'],
                                    'card_user_id' => $card['user_id'],
                                    'card_id' => $card['card_id'],
                                    'card' => $card['card'],
                                    'last_four' => $card['last4'],
                                    'type' => $card['type'],
                                    'amount' => $card['available_limits'],
                                    'card_number' => $card['full_card_number'],
                                    'card_cvv' => $card['card_cvv'],
                                    'expiry_month' => $card['exp_month'],
                                    'expiry_year' => $card['exp_year'],
                                    'currency' => $card['currency'],
                                    'status' => 'success',
                                    'card_status' => $card['status'],
                                    'applied_from' => 'web'
                                ]);
                            }
                        }
                    }
                }
            }
        }

        $userdevice = DB::table('devices')->where('user_id', $user->id)->where('user_type', $user->role_id)->first();
        if(!empty($userdevice)){
            $device_lang = $userdevice->language;
        }else{
            $device_lang = Session::get('default_language');
        }

        $identityVerificationEmailTemp        = EmailTemplate::where(['temp_id' => 21, 'language_id' => $device_lang, 'type' => 'email'])->select('subject', 'body')->first();

        $identityVerificationEmailSub  = str_replace('{identity/address/photo}', ucfirst($request->verification_type), $identityVerificationEmailTemp->subject);
        $identityVerificationEmailBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $identityVerificationEmailTemp->body);
        $identityVerificationEmailBody = str_replace('{identity/address/photo}', ucfirst($request->verification_type), $identityVerificationEmailBody);
        $identityVerificationEmailBody = str_replace('{approved/pending/rejected}', ucfirst($request->status), $identityVerificationEmailBody);
        $identityVerificationEmailBody = str_replace('{soft_name}', Session::get('name'), $identityVerificationEmailBody);

        if (checkAppMailEnvironment())
        {
            $this->email->sendEmail($user->email, $identityVerificationEmailSub, $identityVerificationEmailBody);
        }
        
    	//notification
	    $currency = "9";
        $type = "verification";
        $date = date("Y-m-d h:i:s");
        
    	$userdevice = DB::table('devices')->where('user_id', $request->user_id)->where('user_type', $user->role_id)->first();
    	if(!empty($userdevice)){
            $template = NotificationTemplate::where('temp_id', $notification_temp)->where('language_id', $userdevice->language)->first();
            $subject = $template->title;
            $subheader = $template->subheader;
            $message = $template->content;
            
            $sub = str_replace('{status}', $request->status, $subject);
            $subhead = str_replace('{status}', $request->status, $subheader);
            $msg = str_replace('{status}', $request->status, $message);
            $this->helper->sendFirabasePush($sub, $msg, $request->user_id, $currency, $type);
            
            Noticeboard::create([
                'tr_id' => null,
                'title' => $sub,
                'content' => $msg,
                'type' => 'push',
                'content_type' => 'identity',
                'user' => $request->user_id,
                'sub_header' => $subhead,
                'push_date' => $date,
                'template' => $notification_temp,
                'language' => $userdevice->language,
                'status' => $request->status
            ]);
    	}
        
        $this->helper->one_time_message('success', ucfirst($request->verification_type).' '.ucfirst($request->status).' Successfully!');
        return back();
    }
}
