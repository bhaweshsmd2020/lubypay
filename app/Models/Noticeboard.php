<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticeboard extends Model
{
    protected $table = 'noticeboard';

    protected $fillable = [
        'tr_id',
        'title',
        'sub_header',
        'content', 
        'type', 
        'read_staus',
        'user', 
        'content_type',
        'push_date',
        'template',
        'language',
        'currency',
        'amount',
        'sender',
        'receiver',
        'from_currency',
        'from_amount',
        'to_currency',
        'to_amount',
        'product',
        'status',
        'ticket',
        'last_four',
        'days'
    ];
}