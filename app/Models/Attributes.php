<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attributes extends Model
{
	protected $table    = 'attributes';
    protected $fillable = ['user_id','name','short_order', 'active','deleted_at'];
    public $timestamps  = false;
}
