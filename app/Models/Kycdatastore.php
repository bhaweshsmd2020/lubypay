<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kycdatastore extends Model
{
	protected $table    = 'kycdatastores';
    protected $fillable = ['user_id','proof_id','status'];
    public $timestamps  = false;
    
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
    
}
