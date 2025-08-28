<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table    = 'subscriptions';
    protected $fillable = ['title', 'description', 'icon', 'duration', 'price', 'featured', 'status'];
    public $timestamps  = false;
}
