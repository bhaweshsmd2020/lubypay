<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantPackages extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'merchant_group_id', 'status'];

    public function MerchantGroup()
    {
        return $this->belongsTo(MerchantGroup::class, 'merchant_group_id');
    }
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
