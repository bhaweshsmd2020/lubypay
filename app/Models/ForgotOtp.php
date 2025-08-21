<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForgotOtp extends Model
{
    protected $fillable=['user_id','phone','otp','role_id','status'];
}
