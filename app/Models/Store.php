<?php

namespace App\Models;
Use DB;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table    = 'stores';
    protected $fillable = ['name', 'description', 'image', 'currency_id', 'address', 'city', 'state', 'country', 'postalcode', 'tax', 'user_id'];
    public $timestamps  = false;
	
    public function  addeditdata($postData){
		        $Obj                           = new Store;
                if(isset($postData['id'])){
                $Obj                           = Store::find($postData['id']);   
				}
				
                foreach($postData as $key=>$val){
                     if($key!='_token'){
                     	$Obj->$key            = $val;
                     }
                }
                $Obj->save();
				return $Obj->id;
   }
   
   
   
   public function country_details()
    {
        return $this->belongsTo(Country::class, 'country');
    }
    
     public function state_details()
    {
        return $this->belongsTo(State::class, 'state');
    }
    
     public function city_details()
    {
        return $this->belongsTo(City::class, 'city');
    }
    
    
    

}
