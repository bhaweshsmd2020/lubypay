<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppPage extends Model
{
    protected $table    = 'app_pages';
    protected $fillable = ['app_page', 'page_name', 'status'];
    public $timestamps  = false;

}
