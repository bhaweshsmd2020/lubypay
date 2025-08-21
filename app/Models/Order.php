<?php

namespace App\Models;
Use DB;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table    = 'orders';
    protected $fillable = ['store_id', 'store_user_id', 'user_id', 'unique_id', 'customer_name', 'customer_phone_prefix', 'customer_phone', 'customer_email', 'customer_address1', 'customer_address2', 'customer_zipcode', 'customer_city', 'customer_state', 'customer_country', 'products', 'payment_method_id', 'subtotal', 'total_amount', 'currency_id', 'paid_currency_id', 'status', 'discount', 'tax'];
    public $timestamps  = false;
	
    public function  addeditdata($postData){
		        $Obj                           = new Order;
                if(isset($postData['id'])){
                $Obj                           = Order::find($postData['id']);   
				}
				
                foreach($postData as $key=>$val){
                     if($key!='_token'){
                     	$Obj->$key            = $val;
                     }
                }
                $Obj->save();
				return $Obj->id;
   }

}
