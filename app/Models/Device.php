<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table    = 'devices';
    protected $fillable = [
        'user_id',
        'device_id',
        'fcm_token',
        'passcode',
        'passcode_status',
        'touch_status',
        'language',
        'device_name',
        'device_manufacture',
        'device_model',
        'os_ver',
        'device_os',
        'app_ver',
        'status',
        'user_type'
    ];
}
