<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Helpers\Common;
use App\Http\Controllers\Users\EmailController;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;
use App\Models\EmailConfig;
use App\Models\Device;
use App\Models\Notification;
use App\Models\Wallet;

class DeductSubscriptionFee extends Command
{
    protected $signature = 'subscription:deduct';
    protected $description = 'Deduct subscription fee when a plan expires and send notification email';
    
    public function __construct()
    {
        parent::__construct();
        
        $this->helper = new Common();
        $this->email  = new EmailController();
        
        $setting = EmailConfig::first();
        $this->admin_email = $setting->notification_email;
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

    public function handle()
    {
        $users = User::where('will_expire', '<=', now()->toDateString())->whereNotNull('plan_id')->get();

        foreach ($users as $user) {
            try {
                
                $user_id = $user->id;
                $plan_id = $user->plan_id;
                
                $url = env('BASE_URL').'subscription-details';
        
                $headers = [
                    'Host: cards.lubypay.com',
                    'Content-Type: application/json',
                ];
                
                $payloads = [
                    'plan_id' => $plan_id,
                    'user_email' => $user->email,
                    'platform' => 'Sandbox Ewallet',
                ];
                
                $plan = $this->postFunction($url, $headers, $payloads);
                
                $amount = $plan['data']['price'];
                
                if($plan_id != '10'){
                    $wallet = Wallet::where('user_id', $user_id)->where('currency_id', '9')->first();
                    if(!empty($wallet) && $wallet->balance <= $amount){
                        Log::info('Insufficient Balance');
                    }
                }
                
                $renewUrl = env('BASE_URL').'renew-subscription';
                
                $subscribe = $this->postFunction($renewUrl, $headers, $payloads);
                
                User::where('id', $user_id)->update([
                    'plan_data' => $subscribe['data']['user']['plan_data'],
                    'plan_id' => $subscribe['data']['user']['plan_id'],
                    'plan_name' => $subscribe['data']['plan']['title'],
                    'plan_amount' => $subscribe['data']['plan']['price'],
                    'will_expire' => $subscribe['data']['user']['will_expire'],
                ]);
                
                if($plan_id != '10'){
                    Wallet::where('user_id', $user_id)->where('currency_id', '9')->update([
                        'balance' => $wallet->balance - $amount
                    ]);
                }
                
                // Notification to User
                $userdevice = Device::where('user_id', $user_id)->first();
                if(!empty($userdevice)){
                    $device_lang = $userdevice->language;
                }else{
                    $device_lang = getDefaultLanguage();
                }
        
                $template = NotificationTemplate::where('temp_id', '31')->where('language_id', $device_lang)->first();
                $subject = $template->title;
                $subheader = $template->subheader;
                $message = $template->content;
                
                $this->helper->sendFirabasePush($subject, $message, $user_id, '9', 'push');
                
                Noticeboard::create([
                    'tr_id' => null,
                    'title' => $subject,
                    'content' => $message,
                    'type' => 'push',
                    'content_type' => 'cards',
                    'user' => $user_id,
                    'sub_header' => $subheader,
                    'push_date' => null,
                    'template' => '31',
                    'language' => $device_lang
                ]);
                
                $userPlan = User::where('id', $user_id)->first();
                $plan = json_decode($userPlan['plan_data'], true);
                $plan_card_limit = $plan['cards']['value'];
                $plan_deposit_fee = $plan['deposit_fee']['value'];
                $plan_transaction_fee = $plan['transaction_fee']['value'];
                $plan_service_fee = $plan['service_fee']['value'];
                
            	// Email to User
            	$twoStepVerification = EmailTemplate::where([
                    'temp_id'     => 66,
                    'language_id' => $device_lang,
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{subscription_plan}', $user->plan_name, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{subscription_amount}', 'USD '.number_format($user->plan_amount, 2, '.', ','), $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{card_limit}', $plan_card_limit, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{deposit_fee}', $plan_deposit_fee.'%', $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{transaction_fee}', $plan_transaction_fee.'%', $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{service_fee}', $plan_service_fee.'%', $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{subscription_expire}', $user['will_expire'], $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
                
                // Email / Notification to Admin
                $adminAllowed = Notification::has_permission([1]);
                                    
                foreach($adminAllowed as $admin){
                    Notification::insert([
                        'user_id'               => $user_id,
                        'notification_to'       => $admin->agent_id,
                        'notification_type_id'  => 1,
                        'notification_type'     => 'Web',
                        'description'           => 'A user '.$user->first_name . ' ' . $user->last_name.' has renewed subscription successfully.',
                        'url_to_go'             => null,
                        'local_tran_time'       => null
                    ]);
                }
            	
            	$admin->email = $this->admin_email;
            	
            	if(!empty($admin->email)){
                	$twoStepVerification = EmailTemplate::where([
                        'temp_id'     => 72,
                        'language_id' => getDefaultLanguage(),
                        'type'        => 'email',
                    ])->select('subject', 'body')->first();
                   
                    $twoStepVerification_sub = $twoStepVerification->subject;
                    $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                    $twoStepVerification_msg = str_replace('{subscription_plan}', $user->plan_name, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{subscription_amount}', 'USD '.number_format($user->plan_amount, 2, '.', ','), $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{card_limit}', $plan_card_limit, $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{deposit_fee}', $plan_deposit_fee.'%', $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{transaction_fee}', $plan_transaction_fee.'%', $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{service_fee}', $plan_service_fee.'%', $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{subscription_expire}', $user['will_expire'], $twoStepVerification_msg);
                    $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                    $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
            	}
                
                Log::info('Subscription fee deduction completed.');
                
            } catch (\Exception $e) {
                Log::error("Error processing user ID: {$user->id}, Error: " . $e->getMessage());
            }
        }
    }
}
