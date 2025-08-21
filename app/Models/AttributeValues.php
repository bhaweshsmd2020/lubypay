<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValues extends Model
{
	protected $table    = 'attribute_values';
    protected $fillable = ['user_id','attribute_id','value','short_order', 'active','deleted_at'];
    public $timestamps  = false;
    
    
    public function attribute()
    {
        return $this->belongsTo(Attributes::class, 'attribute_id');
    }
}
