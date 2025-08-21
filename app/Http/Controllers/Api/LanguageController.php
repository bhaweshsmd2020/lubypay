<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LanguageContent;
use App\Models\Language;
use DB;

class LanguageController extends Controller
{
    //Language Change Starts here
    public function get_language(Request $request)
    {
    
        
       $language = Language::get();
        return response()->json([
            'message'=>'All Language List',
            'code'=>200,
            'data'=>$language,
            'url'=>ENV('FLAG_URL'),
            'status'=>'success'
        ]);
        
    }
    
    //Language Change Starts here
    public function change_language(Request $request)
    {
        $device = $request->device_id;
        $language_check = $request->language;
        $user_type = $request->user_type;
        
        if (is_numeric($language_check)){
            $language_get = Language::where('id', $language_check)->first();
            $language = $language_get->short_name;
        }else{
            $language_get = Language::where('name', $language_check)->first();
            $language = $language_get->short_name;
        }
      
        $get_device = DB::table('devices')->where('device_id', $device)->where('user_type', $user_type)->first();
     
        if(!empty($get_device->device_id)){
            $data = [
                'language' => $language_check,
            ];
           
            $rs = DB::table('devices')->where(['device_id'=> $device])->where('user_type', $user_type)->update($data);
        }else
        {
            $rs = DB::table('devices')->insert([
                'device_id'=>$device,
                'language' => $language_check,
                'user_type' => $user_type,
            ]);
        }
       
        if($language == $language){
            $query = LanguageContent::select($language, 'string')->where(['active' => '1'])->get();
            foreach($query as $queryes)
            {
                $sting[] = $queryes->string;
                $value[] = $queryes->$language;
            }
            $languagenew=  array_combine($sting,$value);
        }
        
        return response()->json([
            'message'=>'All Language List',
            'code'=>200,
            'data'=>$languagenew,
            'language'=>$language,
            'status'=>'success'
        ]);
        
    }
    //Language Change Ends here
    
    //Device Language Starts here
    public function device_language(Request $request)
    {
        //dd(request()->all());
        
        $device = request('device_id');
        
        $get_device = DB::table('devices')->where('device_id', $device)->first();
        
        if(empty($get_device->language)){
            $language = 'english';
        }else{
            $language = $get_device->language;
        }
        
        return response()->json([
            'message'=>'Selected Language',
            'code'=>200,
            'language'=>$language,
            'status'=>'success'
        ]);
        
    }
    //Device Language Ends here
    
    public function cmslanguage(Request $request)
    {
        $page_id = $request->page_id;
        $check_slug = DB::table('pages')->where('id', $page_id)->first();
        
        $language_check = $request->language;
        
        $language_get = Language::where('id', $language_check)->first();
        $language = $language_get->short_name;
        
        if($language == $language){
            $query = DB::table('pages')->select($language, 'string', 'string_content', 'en_content', 'fr_content', 'es_content', 'pt_content')->where(['id' => $page_id, 'status' => 'active'])->get();
            foreach($query as $queryes)
            {
                $sting[] = $queryes->string;
                $value[] = $queryes->$language;
                
                $lang = $language.'_content';
                
                $sting[] = $queryes->string_content;
                $value[] = $queryes->$lang;
            }
            $languagenew=  array_combine($sting,$value);
        }
        
        return response()->json([
            'message'=>'All Language List',
            'code'=>200,
            'data'=>$languagenew,
            'language'=>$request->language,
            'slug'=>$check_slug->url,
            'status'=>'success'
        ]);
    }
}
