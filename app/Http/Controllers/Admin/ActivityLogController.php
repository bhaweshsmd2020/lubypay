<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ActivityLogsDataTable;
use App\DataTables\Admin\KycDataTable;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Models\{ActivityLog,
    Kycdatastore, 
    User
};
use App\Models\Setting;

class ActivityLogController extends Controller
{
    
       protected $helper;
       protected $settings;

    public function __construct()
    {
        $this->helper   = new Common();
        $this->settings=  Setting::where('type', 'persona')->pluck('value', 'name')->toArray();
    }
    
    public function activities_list()
    {
        $data['menu']     = 'fraud_detection';
        $data['sub_menu'] = 'activity_logs';
        
        $data['activities'] = ActivityLog::with([
            'user'   => function ($query)
            {
                $query->select('id', 'first_name', 'last_name');
            },
            'admin' => function ($query)
            {
                $query->select('id', 'first_name', 'last_name');
            },
        ])
        ->select('activity_logs.*')->orderBy('id', 'desc')->get();
        return view('admin.activity_logs.list', $data);
    }
    
    
    public function UserKycView(Request $request,$id=null)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://withpersona.com/api/v1/inquiries/'.$id,
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
                     
                    $data['reference_id']=$decode['reference-id']??'';
                    if($decode['status']){
                        $status='completed';
                    }else{
                        $status=$decode['status'];
                    }
                    
                    $data['status']=$status??'';
                    $data['name_first']=$decode['name-first']??'';
                    $data['name_middle']=$decode['name-middle']??'';
                    $data['name_last']=$decode['name-last']??'';
                    $data['birthdate']=$decode['birthdate']??'';
                    $data['addressstreet1']=$decode['address-street-1']??'';
                    $data['addressstreet2']=$decode['address-street-2']??'';
                    $data['address_city']=$decode['address-city']??'';
                    $data['address_subdivision']=$decode['address-subdivision']??'';
                    $data['address_subdivision_abb']=$decode['address-subdivision-abb']??'';
                    $data['address_postal_code']=$decode['address-postal-code']??'';
                    $data['address_postal_code_abbr']=$decode['address-postal-code-abbr']??'';
                    $data['identification_number']=$decode['identification-number']??'';
                    $data['email_address']=$decode['email-address']??'';
                    $data['phone_number']=$decode['phone-number']??'';
                    $data['created_at']=$decode['created-at']??'';
                    $data['account_id']=$kyc_details->data->relationships->account->data->id??'';
                    $data['selected_id_class']=$decode['fields']['selected-id-class']['value'];
                    $data['selected_country_code']=$decode['fields']['selected-country-code']['value'];
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
                    $data['created_at']=$decode['created-at']??'';
                    $data['ip_address']=$decode['ip-address']??'';
                    $data['user_agent']=$decode['user-agent']??'';
                    $data['os_name']=$decode['os-name']??'';
                    $data['os_full_version']=$decode['os-full-version']??'';
                    $data['device_type']=$decode['device-type']??'';
                    $data['device_name']=$decode['device-name']??'';
                    $data['browser_name']=$decode['browser-name']??'';
                    $data['country_code']=$decode['country-code']??'';
                    $data['country_name']=$decode['country-name']??'';
                    $data['region_code']=$decode['region-code']??'';
                    $data['region_name']=$decode['region-name']??'';
                    $data['latitude']=$decode['latitude']??'';
                    $data['longitude']=$decode['longitude']??'';
                    $data['threat_level']=$decode['threat-level'];
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
            
            Kycdatastore::where('proof_id',$id)->update($data);
        }
        $data['menu'] = "kyc";
        $data['details'] = Kycdatastore::where('proof_id',$id)->first();
        return view('admin.kyc.kyc_details', $data);
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
    
    public function UserKyc(KycDataTable $dataTable)
    {
        $data['menu']     = 'kyc';
        $data['sub_menu']     = 'autokyc';
        return $dataTable->render('admin.kyc.list', $data);
    }
    
    
     public function UserKycDelete(Request  $request,$id=null){
         
         try{
            $id= Kycdatastore::where('id',$id)->delete();
            if($id)
            {
                $this->helper->one_time_message('success', 'Kyc Deleted Successfully!');
                 return redirect()->back();
            }
            else
            {
                $this->helper->one_time_message('error', 'Something Wrong try Again !');
                return redirect()->back();
            }
         }catch(\Exception $e){
                 $this->helper->one_time_message('error', $e->getMessage());
                return redirect()->back();
             
         }
         
    }
    
    
    
    
    
    
}



