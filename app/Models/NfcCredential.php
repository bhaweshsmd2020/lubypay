<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfcCredential extends Model
{
    protected $table = 'nfc_credentials';

    protected $fillable = [
        'pub_key',
        'sec_key', 
        'status'
        ];
}