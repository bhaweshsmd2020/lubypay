<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $table = 'notification_templates';

    public $timestamps = false;

    protected $fillable = [
        'language_id',
        'temp_id',
        'title',
        'subheader',
        'content',
        'lang'
    ];
}
