<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packeging extends Model
{
	protected $table    = 'packeging';
    protected $fillable = ['name', 'shipping', 'length','width','height','dimension_unit','weight','weight_unit','user_id','active'];
    public $timestamps  = false;
    
    
    
      public function shipping()
    {
        return $this->belongsTo(ShippingCost::class, 'shipping');
    }
    
    
}
