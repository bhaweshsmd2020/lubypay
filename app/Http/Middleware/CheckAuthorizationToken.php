<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use Exception;
use App\Models\User;
use App\Models\Device;
use App\Models\Setting;
use App\Models\Language;
use App\Models\LanguageContent;
use App\Models\MaintenanceSetting;
use Carbon\Carbon;

class CheckAuthorizationToken
{
    public function __construct(TokenRepository $tokens, JwtParser $jwt)
    {
        $this->jwt    = $jwt;
        $this->tokens = $tokens;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION_TOKEN']))
        {
            $authToken = $_SERVER['HTTP_AUTHORIZATION_TOKEN'];
        }
        elseif (isset($_SERVER['HTTP_AUTHORIZATION']))
        {
            $authToken = $_SERVER['HTTP_AUTHORIZATION'];
        }
        else
        {
            $authToken = null;
        }

        if (empty($authToken)) {
            return response()->json(['error' => 'Unauthorized', 'status' => '503']);
        } else {
            $accessToken = $this->findUserAccessToken($authToken);
            if (!$accessToken) {
                return response()->json(['error' => 'Unauthorized', 'status' => '503']);
            }
            
            $user = User::where('id', $accessToken->user_id)->first();
            $device = Device::where('user_id', $user->id)->first();
            if(!empty($device)){
                $user_type = $device->user_type;
                $device_id = $device->device_id;
                $platform = $device->device_os;
                $app_ver = $device->app_ver;
                
                $today_date = Carbon::now()->format('Y-m-d');
                $today_time = Carbon::now()->format('h:i');
                
                if($user_type == '2'){
                    $devices = Device::where('user_type', '3')->groupBy('device_id')->get();
                }if($user_type == '3'){
                    $devices = Device::groupBy('device_id')->get();
                }
                
                $languages = Language::where('status', 'Active')->orderBy('id', 'asc')->get();
                $template = LanguageContent::where('string', 'Scheduled_app_maintenance')->first();
                $device = Device::where('user_type', $user_type)->where('device_id', $device_id)->first();
                $setting = MaintenanceSetting::where('date', $today_date)->where('from_time', '<=', $today_time)->where('to_time', '>=', $today_time)->first();
                
                if(!empty($setting)){
                    $ondate = Carbon::parse($setting->date)->format('d M');
                    $fromtime = Carbon::parse($setting->from_time)->format('d M Y, h:i:s A');
                    $totime = Carbon::parse($setting->to_time)->format('d M Y, h:i:s A');
                    $zone = Carbon::now();
                
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
                            'status'  => '501',
                            'message' => 'Maintainance break available.',
                            'data'    => $data
                        ]);
                    }elseif($setting->user_type == '2' && $user_type == '3'){
                        return response()->json([
                            'status'  => '501',
                            'message' => 'Maintainance break available.',
                            'data'    => $data
                        ]);
                    }elseif($setting->user_type == '3'){
                        return response()->json([
                            'status'  => '501',
                            'message' => 'Maintainance break available.',
                            'data'    => $data
                        ]);
                    }
                }
            
                // if($platform == 'android'){
                //     $name = 'android_version';
                //     $url = 'android_url';
                // }elseif($platform == 'ios'){
                //     $name = 'ios_version';
                //     $url = 'ios_url';
                // }elseif($platform == 'mpos_android'){
                //     $name = 'mpos_android_version';
                //     $url = 'mpos_android_url';
                // }elseif($platform == 'mpos_ios'){
                //     $name = 'mpos_ios_version';
                //     $url = 'mpos_ios_url';
                // }
                
                // $app_version = Setting::where('name', $name)->first();
                // $app_url = Setting::where('name', $url)->first();
                
                // if($app_version->value > $app_ver){
                //     $data['api_ver_playstore'] = $app_version->value;
                //     $data['update_available']  =  true;
                //     return response()->json([
                //         'status' => '502',
                //         'message' => "Update App",
                //         'data' => $data, 
                //         'forceUpdate' => true,
                //         'url' => $app_url->value
                //     ]);
                // }
            }
            
            if($user->phone_status != '1'){
                return response()->json([
                    'status' => '504',
                    'error' => 'Phone Not Verified'
                ]);
            }
            if($user->email_status != '1'){
                return response()->json([
                    'status' => '505',
                    'error' => 'Email Not Verified'
                ]);
            }
            // if($user->kyc_status != '1'){
            //     return response()->json([
            //         'status' => '506',
            //         'error' => 'KYC Not Verified'
            //     ]);
            // }
            
            return $next($request);
        }
    }

    protected function findUserAccessToken($generatedToken)
    {
        try {
            return $this->tokens->find(
                $this->jwt->parse($generatedToken)->claims()->get('jti')
            );
        } catch (Exception $e) {
            return false;
        }
    }
}