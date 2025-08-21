<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSetting extends Model
{
    protected $table    = 'maintenance_settings';
    protected $fillable = ['subject', 'date', 'from_time', 'to_time', 'message_en', 'message_es', 'message_fr', 'message_ht', 'message_pt', 'message_pm', 'status', 'user_type'];
    public $timestamps  = false;
}
