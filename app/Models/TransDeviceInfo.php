<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransDeviceInfo extends Model
{
	protected $table = 'trans_device_info';
	protected $fillable = ['user_id', 'trans_id', 'device_id', 'app_ver', 'device_name', 'device_manufacture', 'device_model', 'os_ver', 'device_os', 'ip_address', 'status'];
}
