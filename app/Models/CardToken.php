<?php

namespace App\Models;
Use DB;
use Illuminate\Database\Eloquent\Model;

class CardToken extends Model
{
    protected $table    = 'card_tokens';
    protected $fillable = ['email', 'token', 'expire_time', 'status'];
    public $timestamps  = false;
}
