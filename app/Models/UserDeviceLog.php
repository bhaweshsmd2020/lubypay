<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDeviceLog extends Model
{
    protected $table    = 'user_device_logs';
    protected $fillable = ['user_id', 'device_id', 'ip_address', 'local_trans_time', 'status'];
    public $timestamps  = false;
}