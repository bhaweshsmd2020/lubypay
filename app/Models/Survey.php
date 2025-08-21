<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $table    = 'surveys';
    protected $fillable = ['url', 'message_en', 'message_es', 'message_fr', 'message_ht', 'message_pt', 'message_pm', 'status', 'user_type'];
    public $timestamps  = false;
}
