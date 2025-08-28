<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbassadorCode extends Model
{
    protected $table    = 'ambassador_codes';
    protected $fillable = ['code', 'fixed_discount', 'percentage_discount', 'created_by', 'created_for', 'total_uses', 'individual_uses', 'expires_on', 'status', 'description'];
    public $timestamps  = false;

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_for');
    }
}
