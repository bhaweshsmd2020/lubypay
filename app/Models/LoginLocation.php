<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginLocation extends Model
{
    protected $table = 'users_login_location';
    protected $fillable = ['user_id', 'ip_address', 'city', 'country'];
    
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public static function getLoginDetails($user_id){
        $login_details = DB::table('users_login_location')->where('user_id', $user_id)->first();
        return (object)$login_details;
    }
}
