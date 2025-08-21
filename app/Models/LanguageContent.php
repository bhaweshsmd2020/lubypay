<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageContent extends Model
{
	protected $table    = 'language_contents';
    protected $fillable = ['string', 'en', 'es', 'pt', 'vn', 'active'];
    public $timestamps  = false;
}
