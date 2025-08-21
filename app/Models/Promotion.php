<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table    = 'promotions';
    protected $fillable = ['user_type', 'title', 'subject', 'type', 'app_redirect', 'app_page', 'redirect_url', 'description', 'language', 'image'];
    public $timestamps  = false;
}