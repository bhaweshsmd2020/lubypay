<?php

namespace App\Models;

use App\Models\User;
use App\Models\NotificationType;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // faizah's changes -- 11:43, 9th September 2020
    
    protected $table = 'notifications';
    
    protected $fillable = [
        'user_id',
        'notification_to',
        'notification_type_id',
        'notification_type',
        'description',
        'url_to_go'
    ];
    
    /**
     * Create new notification of user registration
     * param  [object] $param
     */
     
    public function addNewNotification($param, $notificationTo)
    {
        $notification = new self();
        
        $notification->user_id              = $param['user_id'];
        $notification->notification_to      = $notificationTo;
        $notification->notification_type_id = $param['notification_type_id'];
        $notification->notification_type    = $param['notification_type'];
        $notification->description          = $param['description'];
        $notification->url_to_go            = $param['url_to_go'];
        
        $notification->save();
    }
    
    /*
     * @param $user_id
     * @param $permissions
     * @static has_permission
     */
    public static function has_permission($notification_type_id)
    {
        $allowedAdmins = \DB::table('notification_config')->whereIn('receivable_notification_id', $notification_type_id)->get(['agent_id']);
        if (count($allowedAdmins))
        {
            return $allowedAdmins;
        }
        else
        {
            return 0;
        }
    }
    
}
