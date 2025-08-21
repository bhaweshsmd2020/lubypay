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

class SendSubscriptionExpiryReminder extends Command
{
    protected $signature = 'subscription:reminder';
    protected $description = 'Send subscription expiry reminders to users';

    public function __construct()
    {
        parent::__construct();
        
        $this->helper = new Common();
        $this->email  = new EmailController();
        
        $setting = EmailConfig::first();
        $this->admin_email = $setting->notification_email;
    }

    public function handle()
    {
        $reminderDays = [7, 3, 1];
        
        foreach ($reminderDays as $days) {
            // Fetch users whose subscriptions are expiring on the exact date
            $users = User::whereDate('will_expire', now()->addDays($days)->toDateString())->get();

            foreach ($users as $user) {
                // Notification to User
                $userdevice = Device::where('user_id', $user->id)->first();
                if(!empty($userdevice)){
                    $device_lang = $userdevice->language;
                }else{
                    $device_lang = getDefaultLanguage();
                }
        
                $template = NotificationTemplate::where('temp_id', '37')->where('language_id', $device_lang)->first();
                $subject = $template->title;
                $subheader = str_replace('{days}', $days, $template->subheader);
                $message = str_replace('{days}', $days, $template->content);
                
                $this->helper->sendFirabasePush($subject, $message, $user->id, '9', 'push');
                
                Noticeboard::create([
                    'tr_id' => null,
                    'title' => $subject,
                    'content' => $message,
                    'type' => 'push',
                    'content_type' => 'cards',
                    'user' => $user->id,
                    'sub_header' => $subheader,
                    'push_date' => null,
                    'template' => '37',
                    'language' => $device_lang,
                    'days' => $days
                ]);
                
                // Email to User
            	$twoStepVerification = EmailTemplate::where([
                    'temp_id'     => 68,
                    'language_id' => $device_lang,
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{days}', $days, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{subscription_expiry_date}', $user->will_expire, $twoStepVerification_msg);
                $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
            }
        }

        $this->info('Subscription expiry reminders sent successfully.');
    }
}
