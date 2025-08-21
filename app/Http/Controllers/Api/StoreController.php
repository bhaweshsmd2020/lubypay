<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Country;
use App\Models\RequestPayment;
use App\Models\Transfer;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\DocumentVerification;
use App\Models\UsersKyc;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\Admin;
use App\Models\EmailTemplate;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\TicketStatus;
use DB;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Categories;
use App\Models\Attributes;
use App\Models\AttributeValues;
use App\Models\Product;
use App\Models\Order;
use App\Models\Cart;
use App\Models\QrCode;
use App\Models\NfcCredential;
use App\Models\Currency;
use App\Models\FeesLimit;
use App\Models\Transaction;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Http\Controllers\Users\EmailController;
use Carbon\Carbon;
use App\Models\CountryPayout;
use App\Models\CountryBank;
use App\Models\Withdrawal;
use App\Models\WithdrawalDetail;
use App\Models\Label;
use App\Models\TransDeviceInfo;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;
use Illuminate\Support\Str;
use App\Models\PendingTransaction;
use Illuminate\Support\Facades\View;
use App\Models\CollectPayment;


class StoreController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    public $unavailable = 405;
    protected $helper;
    protected $email;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email  = new EmailController();
        
        $setting = Setting::first();
        $this->admin_email = $setting->notification_email;
    }
    
    public function store_list(Request $request)
    {
        $rules = array(
            'user_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	
    	$check = Store::where('user_id', $user_id)->first();
    	
    	$data['base_url'] = url('public/uploads/store/');
    	$store_detail = Store::where('user_id', $user_id)->first();
    	if(!empty($store_detail)){
        	$currency = Currency::where('id', $store_detail->currency_id)->first();
        	$country = Country::where('id', $store_detail->country)->first();
        	
        	$data['store_detail'] = [
        	    'id' => $store_detail->id,
                'user_id' => $store_detail->user_id,
                'currency_id' => $store_detail->currency_id,
                'currency_code' => $currency->code,
                'currency_symbol' => $currency->symbol,
                'name' => $store_detail->name,
                'slug' => $store_detail->slug,
                'image' => $store_detail->image,
                'description' => $store_detail->description,
                'address' => $store_detail->address,
                'country' => $store_detail->country,
                'country_name' => $country->name,
                'country_flag' => 'https://s3.amazonaws.com/rld-flags/'.strtolower($country->short_name).'.svg',
                'state' => $store_detail->state,
                'city' => $store_detail->city,
                'postalcode' => $store_detail->postalcode,
                'tax' => $store_detail->tax,
                'created_at' => $store_detail->created_at,
                'updated_at' => $store_detail->updated_at,
            ];
    	
        	return response()->json([
        	    'status' => $this->successStatus, 
        	    'message' => 'Stores fetched successfully.',
        	    'data' => $data
        	], $this->successStatus);
    	}else{
    	    return response()->json([
        	    'status' => $this->unavailable, 
        	    'message' => 'Store not available!',
        	    'data' => null
        	], $this->successStatus);
    	}
    }
    
    public function store_update(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'name'  => 'required',
            'currency_id'   => 'required',
            'address'   => 'required',
            'country'   => 'required',
            'postalcode'   => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$name = $request->name;
    	$description = $request->description;
    	$image = $request->image;
    	$currency_id = $request->currency_id;
    	$address = $request->address;
    	$city = $request->city;
    	$state = $request->state;
    	$country = $request->country;
    	$postalcode = $request->postalcode;
    	$tax = $request->tax;
    	
    	$check = Store::where('user_id', $user_id)->first();
    	
    	if($image){
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = time().'.'.'png';
            File::put(public_path(). '/uploads/store/' . $imageName, base64_decode($image));  
    	}else{
    	    if(!empty($check)){
    	        $imageName = $check->image;
    	    }else{
    	        $imageName = null;
    	    }
    	}
    	
    	$data = [
    	    'user_id' => $user_id,
    	    'name' => $name,
    	    'description' => $description,
    	    'image' => $imageName,
    	    'currency_id' => $currency_id,
    	    'address' => $address,
    	    'city' => $city,
    	    'state' => $state,
    	    'country' => $country,
    	    'postalcode' => $postalcode,
    	    'tax' => $tax,
    	];
    	
    	if(!empty($address)){
    	    $address1 = $address.',';
    	}
    	if(!empty($city)){
    	    $address2 = $city.',';
    	}
    	if(!empty($state)){
    	    $address3 = $state.',';
    	}
    	if(!empty($country)){
    	    $check_country = Country::where('id', $country)->first();
    	    $address4 = $check_country->name;
    	}
    	
    	$location_add = $address1.' '.$address2.' '.$address3.' '.$address4;
    	
    	if(!empty($check)){
    	    $update = Store::where('user_id', $user_id)->update($data);
    	    
    	    return response()->json([
        	    'status' => $this->successStatus, 
        	    'message' => 'Store updated successfully.',
        	], $this->successStatus);
    	}else{
    	    $create = Store::create($data);
    	    
    	    $checkmerchantWallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $currency_id])->first();
            if (empty($checkmerchantWallet))
            {
                $wallet              = new Wallet();
                $wallet->user_id     = $user_id;
                $wallet->currency_id = $currency_id;
                $wallet->balance     = 0;
                $wallet->is_default  = 'No';
                $wallet->save();
            }
    	    
    	    $user = User::where('id', $user_id)->first();
    	    $currency = $currency_id;
            $type = "mpos";
            $date = date("m-d-Y h:i");
            
            //Notification to Merchant
            $userdevice = DB::table('devices')->where('user_id', $user_id)->first();
            if(!empty($userdevice)){
                $device_lang = $userdevice->language;
            }else{
                $device_lang = getDefaultLanguage();
            }

            $template = NotificationTemplate::where('temp_id', '15')->where('language_id', $device_lang)->first();
            $subject = $template->title;
            $subheader = $template->subheader;
            $message = $template->content;
            
            $this->helper->sendFirabasePush($subject, $message, $user_id, $currency, $type);
            
            Noticeboard::create([
                'tr_id' => null,
                'title' => $subject,
                'content' => $message,
                'type' => 'push',
                'content_type' => 'mpos',
                'user' => $user_id,
                'sub_header' => $subheader,
                'push_date' => $request->local_tran_time,
                'template' => '15',
                'language' => $device_lang
            ]);
            
        	// Email to Merchant
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 41,
                'language_id' => $device_lang,
                'type'        => 'email',
            ])->select('subject', 'body')->first();
           
            $twoStepVerification_sub = $twoStepVerification->subject;
            $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{store_name}', $create->name, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
            
            // Email / Notification to Admin
            $adminAllowed = Notification::has_permission([1]);
                                
            foreach($adminAllowed as $admin){
                Notification::insert([
                    'user_id'               => $user_id,
                    'notification_to'       => $admin->agent_id,
                    'notification_type_id'  => 1,
                    'notification_type'     => 'Web',
                    'description'           => 'A new store '.$name.' has been created successfully.',
                    'url_to_go'             => 'admin/users/edit/'.$user_id,
                    'local_tran_time'       => $request->local_tran_time
                ]);
            }
        	
        	$admin->email = $this->admin_email;
        	
        	if(!empty($admin->email)){
            	$twoStepVerification = EmailTemplate::where([
                    'temp_id'     => 38,
                    'language_id' => getDefaultLanguage(),
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{merchant}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{store_name}', $create->name, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{location}', $location_add, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
        	}
    	    
    	    return response()->json([
        	    'status' => $this->successStatus, 
        	    'message' => 'Store created successfully.',
        	    'data' => $create
        	], $this->successStatus);
    	}
    }
    
    public function store_category_list(Request $request)
    {
        $rules = array(
            'user_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	
    	$data['base_url'] = url('public/user_dashboard/categories/thumb/');
    	$data['categories'] = Categories::where('user_id', $user_id)->get();
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Categories fetched successfully.',
    	    'data' => $data
    	], $this->successStatus);
    }
    
    public function store_category_add(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'name'  => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$name = $request->name;
    	$description = $request->description;
    	$image = $request->image;
    	$order = $request->order;
    	
    	if($image){
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = time().'.'.'png';
            File::put(public_path(). '/user_dashboard/categories/thumb/' . $imageName, base64_decode($image));  
    	}else{
    	    $imageName = null;
    	}
    	
    	$data = Categories::create([
    	    'user_id' => $user_id,
    	    'name' => $name,
    	    'image' => $imageName,
            'order' => $request->order,
            'description' => $description,
    	]);
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Category created successfully.',
    	    'data' => $data
    	], $this->successStatus);
    }
    
    public function store_category_update(Request $request)
    {	
        $rules = array(
            'cat_id' => 'required',
            'name'  => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$cat_id = $request->cat_id;
    	$name = $request->name;
    	$description = $request->description;
    	$image = $request->image;
    	$order = $request->order;
    	
    	if($image){
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = time().'.'.'png';
            File::put(public_path(). '/user_dashboard/categories/thumb/' . $imageName, base64_decode($image));  
    	}else{
    	    $cat = Categories::where('id', $cat_id)->first();
    	    if($cat){
    	        $imageName = $cat->image;
    	    }else{
    	        $imageName = null;
    	    }
    	}
    	
    	$data = Categories::where('id', $cat_id)->update([
    	    'name' => $name,
    	    'image' => $imageName,
            'order' => $request->order,
            'description' => $description,
    	]);
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Category updated successfully.',
    	], $this->successStatus);
    }
    
    public function store_attributes_list(Request $request)
    {
        $rules = array(
            'user_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	
    	$categories = Attributes::where('user_id', $user_id)->get();
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Attributes fetched successfully.',
    	    'data' => $categories
    	], $this->successStatus);
    }
    
    public function store_attributes_add(Request $request)
    {	
        $rules = array(
            'user_id' => 'required',
            'name'  => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$name = $request->name;
    	$order = $request->order;
    	
    	$data = Attributes::create([
    	    'user_id' => $user_id,
    	    'name' => $name,
            'short_order' => $order,
    	]);
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Attributes created successfully.',
    	    'data' => $data
    	], $this->successStatus);
    }
    
    public function store_attributes_update(Request $request)
    {	
        $rules = array(
            'attr_id' => 'required',
            'name'  => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$attr_id = $request->attr_id;
    	$name = $request->name;
    	$order = $request->order;
    	
    	$data = Attributes::where('id', $attr_id)->update([
    	    'name' => $name,
            'short_order' => $order,
    	]);
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Attributes updated successfully.',
    	], $this->successStatus);
    }
    
    public function store_attributes_value(Request $request)
    {
        $rules = array(
            'attr_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$attr_id = $request->attr_id;
    	
    	$check = AttributeValues::where('attribute_id', $attr_id)->get();
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Attribute values fetched successfully.',
    	    'data' => $check
    	], $this->successStatus);
    }
    
    public function store_attributes_value_list(Request $request)
    {		  
        $rules = array(
            'user_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	
    	$categories = AttributeValues::where('user_id', $user_id)->get();
    	foreach($categories as $category){
    	    $attribute = Attributes::where('id', $category->attribute_id)->first();
    	    $category['attr_name'] = $attribute->name;
    	}
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Attributes fetched successfully.',
    	    'data' => $categories
    	], $this->successStatus);
    }
    
    public function store_attributes_value_add(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'value'  => 'required',
            'attribute_id'   => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$value = $request->value;
    	$attribute_id = $request->attribute_id;
    	$order = $request->order;
    	
    	$data = AttributeValues::create([
    	    'user_id' => $user_id,
    	    'value' => $value,
            'attribute_id' => $attribute_id,
            'short_order' => $order,
    	]);
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Attributes value added successfully.',
    	    'data' => $data
    	], $this->successStatus);
    }
    
    public function store_attributes_value_update(Request $request)
    {		    
        $rules = array(
            'attr_val_id' => 'required',
            'value'  => 'required',
            'attribute_id'   => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$attr_val_id = $request->attr_val_id;
    	$value = $request->value;
    	$attribute_id = $request->attribute_id;
    	$order = $request->order;
    	
    	$data = AttributeValues::where('id', $attr_val_id)->update([
    	    'value' => $value,
            'attribute_id' => $attribute_id,
            'short_order' => $order,
    	]);
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Attributes value updated successfully.',
    	], $this->successStatus);
    }
    
    public function store_products_list(Request $request)
    {		 
        $rules = array(
            'user_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	
    	$data['base_url'] = url('public/user_dashboard/product/thumb/');
    	$data['products'] = $products = Product::where('userid', $user_id)->get();
    	
    	foreach($products as $product){
    	    $category = Categories::where('id', $product->category_id)->first();
    	    $store = Store::where('user_id', $product->userid)->first();
    	    $currency = Currency::where('id', $store->currency_id)->first();
    	    
    	    if($product->discount_type == 'percent'){
    	        $discount = ($product->discount * $product->price)/100;
    	    }elseif($product->discount_type == 'fixed'){
    	        $discount = $product->discount;
    	    }else{
    	        $discount = 0;
    	    }
    	    
    	    $product['cat_name'] = $category['name'];
    	    $product['curr_symbol'] = $currency['symbol'];
    	    $product['discounted_price'] = number_format($product->price - $discount, 2, '.', ',');
    	    $product['discount_amount'] = number_format($discount, 2, '.', ',');
    	}
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Products fetched successfully.',
    	    'data' => $data
    	], $this->successStatus);
    }
    
    public function store_products_stock(Request $request)
    {		 
        $rules = array(
            'product_id' => 'required',
            'quantity' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$product_id = $request->product_id;
    	$quantity = $request->quantity;
    	
    	$product = $products = Product::where('id', $product_id)->where('quantity', $quantity)->first();
    	if(!empty($product)){
    	    return response()->json([
        	    'status' => $this->successStatus, 
        	    'message' => 'Product available in stock.',
        	    'data' => $product
        	], $this->successStatus);
    	}else{
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Product not in stock!',
        	    'data' => null
        	], $this->unauthorisedStatus);
    	}
    }
    
    public function store_products_details(Request $request)
    {		 
        $rules = array(
            'user_id' => 'required',
            'product_id' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$product_id = $request->product_id;
    	
    	$data['base_url'] = url('public/user_dashboard/product/thumb/');
    	$data['products'] = Product::where('userid', $user_id)->where('id', $product_id)->get();
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Product details fetched successfully.',
    	    'data' => $data
    	], $this->successStatus);
    }
    
    public function store_products_add(Request $request)
    {	
        $rules = array(
            'user_id' => 'required',
            'category_id'   => 'required',
            'name'   => 'required',
            'price'   => 'required',
            'quantity'   => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$product_sku = $request->product_sku;
    	$category_id = $request->category_id;
    	$name = $request->name;
    	$price = $request->price;
    	$description = $request->description;
    	$quantity = $request->quantity;
    	$discount_type = $request->discount_type;
    	$discount = $request->discount;
    	$image = $request->image;
    	
    	if($image){
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = time().'.'.'png';
            File::put(public_path(). '/user_dashboard/product/thumb/' . $imageName, base64_decode($image));  
    	}else{
    	    $imageName = null;
    	}
                
    	$data = Product::create([
    	    'userid' => $user_id,
    	    'user_product_id' => $product_sku,
            'category_id' => $category_id,
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'quantity' => $quantity,
            'discount_type' => $discount_type,
            'discount' => $discount,
            'image' => $imageName,
    	]);
    	
    	$product_id = $data->id;
    	$urlData = 'checkout?id='.$product_id;
        $productToUpdate = Product::find($product_id);
        $productToUpdate->url               = $urlData;
        $productToUpdate->url_data          = $urlData;
        $productToUpdate->save();
        
        $attributes    = Attributes::where('active',1)->get();
        
        foreach($attributes as $attr)
        {
            if($request->input('attributes_'.$attr->id))
            {
                $check = DB::table('product_attributes')->where('product_id', $product_id)->where('attributes', $attr->id)->first();
                $data = array(
                    'product_id' => $product_id,
                    'attributes' => $attr->id,
                    'attributes_values'=>json_encode($request->input('attributes_'.$attr->id))
                );
                
                if($check)
                {
                    DB::table('product_attributes')->where('product_id',$Product->id)->where('attributes',$attr->id)->update($data);
                }
                else
                {
                    DB::table('product_attributes')->insert($data);
                }
            }
        }
        
        
        $user = User::where('id', $user_id)->first();
        $store = Store::where('user_id', $user_id)->first();
	    $currency = $store->currency_id;
        $type = "mpos";
        $date    = date("m-d-Y h:i");
        
        //Notification to Merchant
        $userdevice = DB::table('devices')->where('user_id', $user_id)->first();
        if(!empty($userdevice)){
            $device_lang = $userdevice->language;
        }else{
            $device_lang = getDefaultLanguage();
        }
        
        $template = NotificationTemplate::where('temp_id', '16')->where('language_id', $device_lang)->first();
        $subject = $template->title;
        $subheader = $template->subheader;
        $message = $template->content;
        
        $this->helper->sendFirabasePush($subject, $message, $user_id, $currency, $type);
        
        $check_currency = Currency::where('id', $currency)->first();
        
        Noticeboard::create([
            'tr_id' => null,
            'title' => $subject,
            'content' => $message,
            'type' => 'push',
            'content_type' => 'mpos',
            'user' => $user_id,
            'sub_header' => $subheader,
            'push_date' => $request->local_tran_time,
            'template' => '16',
            'language' => $device_lang,
            'currency' => $check_currency->code,
            'amount' => number_format($price, 2, '.', ',')
        ]);
        
        // Email to Merchant
    	$twoStepVerification = EmailTemplate::where([
            'temp_id'     => 42,
            'language_id' => $device_lang,
            'type'        => 'email',
        ])->select('subject', 'body')->first();
       
        $twoStepVerification_sub = $twoStepVerification->subject;
        $twoStepVerification_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $twoStepVerification->body);
        $twoStepVerification_msg = str_replace('{product_name}', $data->name, $twoStepVerification_msg);
        $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
        $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
        
        // Email / Notification to Admin
        $adminAllowed = Notification::has_permission([1]);
                            
        foreach($adminAllowed as $admin){
            Notification::insert([
                'user_id'               => $user_id,
                'notification_to'       => $admin->agent_id,
                'notification_type_id'  => 1,
                'notification_type'     => 'Web',
                'description'           => 'A new product '.$name.' has been added successfully.',
                'url_to_go'             => '#',
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
    	
    	$admin->email = $this->admin_email;
    	
    	if(!empty($admin->email)){
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 39,
                'language_id' => getDefaultLanguage(),
                'type'        => 'email',
            ])->select('subject', 'body')->first();
           
            $twoStepVerification_sub = $twoStepVerification->subject;
            $twoStepVerification_msg = str_replace('{product_name}', $name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{store}', $store->name, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{merchant}', $user->first_name . ' ' . $user->last_name, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            $this->email->sendEmail($admin->email, $twoStepVerification_sub, $twoStepVerification_msg);
    	}
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Product added successfully.',
    	    'data' => $data
    	], $this->successStatus);
    }
    
    public function store_products_update(Request $request)
    {	
        $rules = array(
            'product_id' => 'required',
            'category_id'   => 'required',
            'name'   => 'required',
            'price'   => 'required',
            'quantity'   => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$product_id = $request->product_id;
    	$product_sku = $request->product_sku;
    	$category_id = $request->category_id;
    	$name = $request->name;
    	$price = $request->price;
    	$description = $request->description;
    	$quantity = $request->quantity;
    	$discount_type = $request->discount_type;
    	$discount = $request->discount;
    	$image = $request->image;
    	
    	if($image){
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = time().'.'.'png';
            File::put(public_path(). '/user_dashboard/product/thumb/' . $imageName, base64_decode($image));  
    	}else{
    	    $product_detail = Product::where('id', $product_id)->first();
    	    $imageName = $product_detail->image;
    	}
                
    	$data = Product::where('id', $product_id)->update([
    	    'user_product_id' => $product_sku,
            'category_id' => $category_id,
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'quantity' => $quantity,
            'discount_type' => $discount_type,
            'discount' => $discount,
            'image' => $imageName,
    	]);
    	
        $attributes    = Attributes::where('active',1)->get();
        
        foreach($attributes as $attr)
        {
            if($request->input('attributes_'.$attr->id))
            {
                $check = DB::table('product_attributes')->where('product_id', $product_id)->where('attributes', $attr->id)->first();
                $data = array(
                    'product_id' => $product_id,
                    'attributes' => $attr->id,
                    'attributes_values'=>json_encode($request->input('attributes_'.$attr->id))
                );
                
                if($check)
                {
                    DB::table('product_attributes')->where('product_id',$Product->id)->where('attributes',$attr->id)->update($data);
                }
                else
                {
                    DB::table('product_attributes')->insert($data);
                }
            }
        }
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Product updated successfully.',
    	], $this->successStatus);
    }
    
    public function store_orders_list(Request $request)
    {
        $rules = array(
            'user_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	
    	$orders = Order::where('store_user_id', $user_id)->where('status', 'success')->where('payment_response', 'success')->get();
    	foreach($orders as $order){
    	    $trans_detail = Transaction::where('transaction_reference_id', $order->unique_id)->first();
    	    if(!empty($trans_detail)){
    	        $order['local_tran_time'] = $trans_detail->local_tran_time;
    	    }else{
    	        $order['local_tran_time'] = null;
    	    }
    	    
    	    $currency = Currency::where('id', $order->currency_id)->first();
    	    if(!empty($currency)){
    	        $order['currency_symbol'] = $currency->symbol;
    	    }else{
    	        $order['currency_symbol'] = null;
    	    }
    	}
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Orders fetched successfully.',
    	    'data' => $orders
    	], $this->successStatus);
    }
    
    public function store_order_details(Request $request)
    {	
        $rules = array(
            'user_id' => 'required',
            'order_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$order_id = $request->order_id;
    	
    	$orders = Order::where('store_user_id', $user_id)->where('id', $order_id)->first();
    	$allproducts = json_decode($orders->products);
        foreach($allproducts as $allproduct){
            $product = Product::where('id', $allproduct->product_id)->first();
            $category = Categories::where('id', $product->category_id)->first();
            $currency = Currency::where('id', $orders->currency_id)->first();
            
            if($product->discount_type == 'percent'){
                $discount_price = ($product->discount/100)*$product->price;
            }else{
                $discount_price = $product->price - $product->discount;
            }
            
            if(!empty($product->image)){
	            $product_image = url('public/user_dashboard/product/thumb/'.$product->image);
	        }else{
	            $product_image = '';
	        }
	        
            $products[] = [
                'name' => $product->name,
                'description' => $product->description,
                'image' => $product_image,
                'price' => $product->price,
                'discount' => $product->discount,
                'discount_type' => $product->discount_type,
                'final_price' => $discount_price,
                'category' => $category->name,
                'currency' => $currency->symbol,
                'quantity' => $allproduct->qty
            ];
        }
        
        $orders->products = $products;
        
	    $trans_detail = Transaction::where('transaction_reference_id', $orders->unique_id)->first();
	    if(!empty($trans_detail)){
	        $orders->local_tran_time = $trans_detail->local_tran_time;
	    }else{
	        $orders->local_tran_time = null;
	    }
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Order details fetched successfully.',
    	    'data' => $orders
    	], $this->successStatus);
    }
    
    public function store_order_status_update(Request $request)
    {	
        $rules = array(
            'user_id' => 'required',
            'order_id' => 'required',
            'status' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$order_id = $request->order_id;
    	$status = $request->status;
    	
    	$orders = Order::where('store_user_id', $user_id)->where('id', $order_id)->update(['status' => $status]);
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Order status changed successfully.'
    	], $this->successStatus);
    }
    
    public function add_cart(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required',
            'packeging' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$product_id = $request->product_id;
    	$quantity = $request->quantity;
    	$packeging = $request->packeging;
    	
        $storeData    = Store::where('user_id', $user_id)->first();
	    if(empty($storeData)){
		    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Store not found.'
        	], $this->unauthorisedStatus);
	    }
	    
	    $productData = Product::where('userid', $user_id)->where('id', $product_id)->first();
	    if(empty($productData)){
		    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Product not found.'
        	], $this->unauthorisedStatus);
	    }
	    
	    if($productData->quantity < $quantity){
	        return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Out of Stock.'
        	], $this->unauthorisedStatus);
	    }
	    
	    $store_id = $storeData->id;
	    $currency_id = $storeData->currency_id;
	    $amount = $productData->price * $quantity;
	    
	    $cart = Cart ::create([
	        'user_id' => $user_id,
	        'product_id' => $product_id,
	        'quantity' => $quantity,
	        'packeging' => $packeging,
	        'store_id' => $store_id,
	        'amount' => $amount,
	        'currency' => $currency_id,
	    ]);
	    
	    return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Product successfully added to cart.',
    	    'data' => $cart
    	], $this->successStatus);
	}
	
	public function cart(Request $request)
	{
	    $rules = array(
            'user_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	
    	$carts = Cart::where('user_id', $user_id)->get();
    	if(count($carts) == '0'){
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Cart is empty',
        	], $this->unauthorisedStatus);
    	}
    	
    	$products = Product::where('userid', $user_id)->get();
    	
    	$cart_total = 0;
    	foreach($carts as $cart){
    	    foreach($products as $product){
    	        
    	        if(!empty($product->image)){
    	            $product_image = url('public/user_dashboard/product/thumb/'.$product->image);
    	        }else{
    	            $product_image = url('public/uploads/userPic/default-image.png');
    	        }
    	        
    	        if($cart->product_id == $product->id){
    	            $cart_data[] = [
    	                'name' => $product->name,
    	                'image' => $product_image,
    	                'price' => $product->price,
    	                'quantity' => $cart->quantity,
    	                'total' => $cart->amount,
    	            ];
    	            
    	            $cart_total += $cart->amount;
    	            $cart_currency = $cart->currency;
    	        }
    	    }
    	}
    	
    	$data['products'] = $cart_data;
    	$data['total'] = $cart_total;
    	$data['currency'] = $cart_currency;
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Cart fetched successfully.',
    	    'data' => $data
    	], $this->successStatus);
	}
	
	public function update_cart(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$product_id = $request->product_id;
    	$quantity = $request->quantity;
    	
        $storeData    = Store::where('user_id', $user_id)->first();
	    if(empty($storeData)){
		    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Store not found.'
        	], $this->unauthorisedStatus);
	    }
	    
	    $productData = Product::where('userid', $user_id)->where('id', $product_id)->first();
	    if(empty($productData)){
		    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Product not found.'
        	], $this->unauthorisedStatus);
	    }
	    
	    if($productData->quantity < $quantity){
	        return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Out of Stock.'
        	], $this->unauthorisedStatus);
	    }
	    
	    $store_id = $storeData->id;
	    $currency_id = $storeData->currency_id;
	    $amount = $productData->price * $quantity;
	    
	    $update_cart = Cart ::where('user_id', $user_id)->where('product_id', $product_id)->where('store_id', $store_id)->update([
	        'quantity' => $quantity,
	        'amount' => $amount,
	    ]);
	    
	    return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Product successfully updated to cart.',
    	], $this->successStatus);
	}
	
	public function checkout(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'subtotal' => 'required',
            'total' => 'required',
            'discount' => 'required',
            'currency' => 'required',
            'payment_method' => 'required',
            'products' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$subtotal = $request->subtotal;
    	$total = $request->total;
    	$discount = $request->discount;
    	$currency = $request->currency;
    	$payment_method = $request->payment_method;
    	$products = $request->products;
    	$tax = $request->tax;
    	
    	$storeData = Store::where('user_id', $user_id)->first();
    	$store_id = $storeData->id;
    	$order_id = $this->random_strings(6);
    	
    	$check_cart = Cart::where('store_id', $store_id)->delete();
    	
    	foreach($products as $product){
    	    $cart = Cart ::create([
    	        'user_id' => $user_id,
    	        'store_id' => $store_id,
    	        'currency' => $currency,
    	        'product_id' => $product['product_id'],
    	        'quantity' => $product['quantity'],
    	        'amount' => $product['amount'],
    	    ]);
    	}
    	
    	$cartData = Cart::where('user_id', $user_id)->get();
    	if(count($cartData) == 0){
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'No product in cart.',
        	], $this->unauthorisedStatus);
    	}
    	
        foreach($cartData as $key=>$val){
            $productArray[] = [
                'product_id' => $val->product_id,
                'qty' => $val->quantity,
            ];
	    }
	    
    	$order = Order::create([
           'store_id'               => $store_id,
    	   'store_user_id'          => $user_id,
           'unique_id'	            => strtoupper($order_id),				   
           'products'	            => json_encode($productArray),
           'payment_method_id'      => $payment_method,
           'subtotal'               => $subtotal,
           'total_amount'           => $total,
           'discount'               => $discount,
           'currency_id'            => $currency,
           'tax'                    => $tax,
           'status'                 => 'pending',
        ]);
        
        if($payment_method == '1'){
            $qrCode = QrCode::where(['object_id' => $order->id, 'object_type' => 'order', 'status' => 'Active'])->first(['id', 'secret']);
    		if (empty($qrCode))
    		{
    			$createMerchantQrCode              = new QrCode();
    			$createMerchantQrCode->object_id   = $user_id;
    			$createMerchantQrCode->object_type = 'order';
    			$createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $order->id . '-' . Str::random(6));
    			$createMerchantQrCode->status      = 'Active';
    			$createMerchantQrCode->save();
    			
    			$secret = $createMerchantQrCode->secret;
    		}
    		else
    		{
    			$secret = urlencode($qrCode->secret);
    		}
    		
    		$data['qr_code'] = "https://api.qrserver.com/v1/create-qr-code/?data=".$secret."&amp;size=200x200";
        }else{
            
            $feeInfo       = FeesLimit::where(['transaction_type_id' => '36', 'currency_id' => $currency])->first();
            $feePercent    = $total * ($feeInfo->charge_percentage / 100);
            $feePercentage = number_format((float)$feePercent, 2, '.', '');
            $feeFixed = number_format((float)$feeInfo->charge_fixed, 2, '.', '');
            $total_fee = number_format((float)$feePercentage + $feeFixed, 2, '.', '');
            
            $data['order_total'] = $total;
            $data['order_currency'] = $currency;
            $data['nfc_fee'] = $total_fee;
        }
        
        $check_currency = Currency::where('id', $currency)->first();
        
        $data['order_id'] = $order->unique_id;
        $data['currency_symbol'] = $check_currency->symbol;
        $data['currency_code'] = $check_currency->code;
        
	    return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Payment initiated successfully.',
    	    'data' => $data
    	], $this->successStatus);
	}
	
// 	public function update_order(Request $request)
//     {
//         $rules = array(
//             'user_id' => 'required',
//             'order_id' => 'required',
//             'payment_status' => 'required',
//             'amount' => 'required',
//             'currency' => 'required',
//         );
        
//         $validator = Validator::make($request->all(), $rules);
//         if ($validator->fails())
//         {
//             return response()->json([
//         	    'status' => $this->unauthorisedStatus, 
//         	    'message' => 'All fields are required!',
//         	    'error' => $validator->errors()
//         	], $this->unauthorisedStatus);
//         }
        
//     	$user_id = $request->user_id;
//     	$order_id = $request->order_id;
//     	$payment_status = $request->payment_status;
//     	$amount = $request->amount;
//     	$currency = $request->currency;
//     	$name = $request->name;
//     	$email = $request->email;
//     	$phone = $request->phone;
//     	$phone_code = $request->phone_code;
//     	$nfc_fee = $request->nfc_fee;
//     	$local_tran_time = $request->local_tran_time;
//     	$last_four = $request->last_four;
    	
//     	$check_currency = Currency::where('code', strtoupper($currency))->first();
//     	$order_detail = Order::where('store_user_id', $user_id)->where('unique_id', $order_id)->first();
    	
//     	if($order_detail->payment_response == 'success'){
//     	    return response()->json([
//         	    'status' => $this->unauthorisedStatus, 
//         	    'message' => 'Payment already done.'
//         	], $this->unauthorisedStatus);
//     	}elseif($order_detail->payment_response == 'failed'){
//     	    return response()->json([
//         	    'status' => $this->unauthorisedStatus, 
//         	    'message' => 'Payment already failed.'
//         	], $this->unauthorisedStatus);
//     	}
    	
//     	$feeInfo       = FeesLimit::where(['transaction_type_id' => '33', 'currency_id' => $check_currency->id])->first();
//         $feePercent    = $amount * ($feeInfo->charge_percentage / 100);
//         $feePercentage = number_format((float)$feePercent, 2, '.', '');
	    
// 	    if($payment_status == 'succeeded'){
	        
// 	        $unique_code           = unique_code();
	        
// 	        //Payment_Received
//             $transaction_B                           = new Transaction();
//             $transaction_B->user_id                  = $user_id;
//             $transaction_B->end_user_id              = null;
//             $transaction_B->currency_id              = $check_currency->id;
//             $transaction_B->payment_method_id        = 2;
//             $transaction_B->merchant_id              = null;
//             $transaction_B->uuid                     = $unique_code;
//             $transaction_B->transaction_reference_id = $order_detail->unique_id;
//             $transaction_B->transaction_type_id      = "35";
//             $transaction_B->subtotal                 = $amount - $nfc_fee;
//             $transaction_B->percentage               = 0;
//             $transaction_B->charge_percentage        = $nfc_fee;
//             $transaction_B->charge_fixed             = 0;
//             $transaction_B->store_fee                = $feePercentage;
//             $transaction_B->total                    = $amount;
//             $transaction_B->status                   = 'Success';
//             $transaction_B->local_tran_time          = $local_tran_time;
//             $transaction_B->ip_address               = request()->ip();
//             $transaction_B->last_four                = $last_four;
//             $transaction_B->save();
            
//             $rs = TransDeviceInfo::create([
//                 'user_id' => $user_id, 
//                 'trans_id' => $transaction_B->id, 
//                 'device_id' => $request->device_id, 
//                 'app_ver' => $request->app_ver, 
//                 'device_name' => $request->device_name, 
//                 'device_manufacture' => $request->device_manufacture, 
//                 'device_model' => $request->device_model, 
//                 'os_ver' => $request->os_ver, 
//                 'device_os' => $request->device_os, 
//                 'ip_address' => request()->ip(),
//             ]);
            
//             //updating/Creating merchant wallet
//             $merchantWallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $check_currency->id])->first(['id', 'balance']);
//             if (empty($merchantWallet))
//             {
//                 $wallet              = new Wallet();
//                 $wallet->user_id     = $user_id;
//                 $wallet->currency_id = $check_currency->id;
//                 $wallet->balance     = $amount - $feePercentage;
//                 $wallet->is_default  = 'No';
//                 $wallet->save();
//             }
//             else
//             {
//                 $merchantWallet->balance = $merchantWallet->balance + ($amount - $feePercentage);
//                 $merchantWallet->save();
//             }
	        
// 	        $update_order = Order::where('store_user_id', $user_id)->where('unique_id', $order_id)->update([
//     	        'status' => 'success',
//     	        'paid_amount' => $amount,
//     	        'paid_currency_id' => $check_currency->id,
//     	        'payment_response' => 'success',
//     	        'nfc_fee' => $nfc_fee,
//     	        'customer_name' => $name,
//     	        'customer_email' => $email,
//     	        'customer_phone' => $phone,
//     	        'customer_phone_prefix' => $phone_code,
//     	        'local_tran_time' => $local_tran_time,
//                 'ip_address' => request()->ip(),
//     	    ]);
    	    
// 	        $cartData = Cart::where('user_id', $user_id)->delete();
	        
// 	        $allproducts = json_decode($order_detail->products);
//             foreach($allproducts as $allproduct){
//                 $product = Product::where('id', $allproduct->product_id)->first();
                
//                 if(!empty($product)){
//                     $update_stock = Product::where('id', $allproduct->product_id)->update([
//                         'quantity' => $product->quantity - $allproduct->qty,
//                     ]);
//                 }
//             }
	        
// 	        $user = User::where('id', $user_id)->first();
// 	        $store = Store::where('user_id', $user_id)->first();
// 	        $type = "mpos";
// 	        $date    = date("m-d-Y h:i");
	        
// 	        // Email / Notification to user
//             $subject   = "Payment Done Successfully!";
//             $subheader = "Congratulations! Your payment has been done successfully.";
//             $message = "You’ve successfully done payment of ".strtoupper($currency) .' '.$transaction_B->total;
            
//         //     $this->helper->sendFirabasePush($subject,$subheader,$user_id, $check_currency->id, $type);
//         //     $datanotice1= array('title'=>$subject,'content'=>$message,'type'=>'push','content_type'=>'mpos','user'=>$user_id,'sub_header'=>$subheader,'push_date'=>request('local_tran_time'));
//         // 	   DB::table('noticeboard')->insert($datanotice1);
        	
//         	if(!empty($email)){
//             	$twoStepVerification = EmailTemplate::where([
//                     'temp_id'     => 36,
//                     'language_id' => getDefaultLanguage(),
//                     'type'        => 'email',
//                 ])->select('subject', 'body')->first();
               
//                 $twoStepVerification_sub = $twoStepVerification->subject;
//                 $twoStepVerification_msg = str_replace('{user}', $name, $twoStepVerification->body);
//                 $twoStepVerification_msg = str_replace('{store_name}', $store->name, $twoStepVerification_msg);
//                 $twoStepVerification_msg = str_replace('{order_id}', '#'.$order_id, $twoStepVerification_msg);
//                 $twoStepVerification_msg = str_replace('{currency}', strtoupper($currency), $twoStepVerification_msg);
//                 $twoStepVerification_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $twoStepVerification_msg);
//                 $twoStepVerification_msg = str_replace('{pay_method}', 'Card Payment', $twoStepVerification_msg);
//                 $twoStepVerification_msg = str_replace('{date}', $date, $twoStepVerification_msg);
//                 $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
//                 $this->email->sendEmail($email, $twoStepVerification_sub, $twoStepVerification_msg);
//         	}
        	
//             //Notification to Merchant
//             $userdevice = DB::table('devices')->where('user_id', $user_id)->first();
//             if(!empty($userdevice)){
//                 $device_lang = $userdevice->language;
//             }else{
//                 $device_lang = getDefaultLanguage();
//             }
//             $template = NotificationTemplate::where('temp_id', '17')->where('language_id', $device_lang)->first();
//             $st_subject = $template->title;
//             $st_subheader = $template->subheader;
//             $st_message = $template->content;
            
//             $msg = str_replace('{currency}', strtoupper($currency), $st_message);
//             $msg = str_replace('{amount}', number_format($transaction_B->total, 2, '.', ','), $msg);
//             $msg = str_replace('{sender}', 'XXXX '.$last_four, $msg);
            
//             $this->helper->sendFirabasePush($st_subject, $msg, $user_id, $check_currency->id, $type);
            
//             Noticeboard::create([
//                 'tr_id' => $transaction_B->id,
//                 'title' => $st_subject,
//                 'content' => $msg,
//                 'type' => 'push',
//                 'content_type' => 'mpos',
//                 'user' => $user_id,
//                 'sub_header' => $st_subheader,
//                 'push_date' => $request->local_tran_time,
//                 'template' => '17',
//                 'language' => $device_lang,
//                 'currency' => strtoupper($currency),
//                 'amount' => number_format($transaction_B->total, 2, '.', ','),
//                 'sender' => 'XXXX '.$last_four
//             ]);
            
//         	// Email to Merchant
//         	if(!empty($user->email)){
//             	$twoStepVerificationmerc = EmailTemplate::where([
//                     'temp_id'     => 43,
//                     'language_id' => $device_lang,
//                     'type'        => 'email',
//                 ])->select('subject', 'body')->first();
               
//                 $twoStepVerificationmerc_sub = $twoStepVerificationmerc->subject;
//                 $twoStepVerificationmerc_msg = str_replace('{store_name}', $store->name, $twoStepVerificationmerc->body);
//                 $twoStepVerificationmerc_msg = str_replace('{order_id}', '#'.$order_id, $twoStepVerificationmerc_msg);
//                 $twoStepVerificationmerc_msg = str_replace('{currency}', strtoupper($currency), $twoStepVerificationmerc_msg);
//                 $twoStepVerificationmerc_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $twoStepVerificationmerc_msg);
//                 $twoStepVerificationmerc_msg = str_replace('{pay_method}', 'Card Payment', $twoStepVerificationmerc_msg);
//                 $twoStepVerificationmerc_msg = str_replace('{date}', $date, $twoStepVerificationmerc_msg);
//                 $twoStepVerificationmerc_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerificationmerc_msg);
//                 $this->email->sendEmail($user->email, $twoStepVerificationmerc_sub, $twoStepVerificationmerc_msg);
//         	}
        	
//         	// Email / Notification to admin
//             $adminAllowed = Notification::has_permission([1]);
                                
//             foreach($adminAllowed as $admin){
//                 Notification::insert([
//                     'user_id'               => $user_id,
//                     'notification_to'       => $admin->agent_id,
//                     'notification_type_id'  => 1,
//                     'notification_type'     => 'Web',
//                     'description'           => 'Payment of '.strtoupper($currency) .' '.$transaction_B->total.' received on '.$store->name,
//                     'url_to_go'             => 'admin/mpos/edit/'.$transaction_B->id,
//                     'local_tran_time'       => $request->local_tran_time
//                 ]);
//             }
        	
//         	$admin->email = $this->admin_email;
        	
//         	if(!empty($admin->email)){
//             	$twoStepVerificationadm = EmailTemplate::where([
//                     'temp_id'     => 40,
//                     'language_id' => getDefaultLanguage(),
//                     'type'        => 'email',
//                 ])->select('subject', 'body')->first();
               
//                 $twoStepVerificationadm_sub = $twoStepVerificationadm->subject;
//                 $twoStepVerificationadm_msg = str_replace('{store_name}', $store->name, $twoStepVerificationadm->body);
//                 $twoStepVerificationadm_msg = str_replace('{order_id}', '#'.$order_id, $twoStepVerificationadm_msg);
//                 $twoStepVerificationadm_msg = str_replace('{currency}', strtoupper($currency), $twoStepVerificationadm_msg);
//                 $twoStepVerificationadm_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $twoStepVerificationadm_msg);
//                 $twoStepVerificationadm_msg = str_replace('{pay_method}', 'Card Payment', $twoStepVerificationadm_msg);
//                 $twoStepVerificationadm_msg = str_replace('{date}', $date, $twoStepVerificationadm_msg);
//                 $twoStepVerificationadm_msg = str_replace('{merchant_name}', $user->first_name.' '.$user->last_name, $twoStepVerificationadm_msg);
//                 $twoStepVerificationadm_msg = str_replace('{customer_name}', $name, $twoStepVerificationadm_msg);
//                 $twoStepVerificationadm_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerificationadm_msg);
//                 $this->email->sendEmail($admin->email, $twoStepVerificationadm_sub, $twoStepVerificationadm_msg);
//         	}
// 	    }else{
// 	        $update_order = Order::where('store_user_id', $user_id)->where('unique_id', $order_id)->update([
//     	        'status' => 'failed',
//     	        'payment_response' => 'failed',
//     	        'customer_name' => $name,
//     	        'customer_email' => $email,
//     	        'customer_phone' => $phone,
//     	        'customer_phone_prefix' => $phone_code,
//     	    ]);
// 	    }
	    
// 	    $order_details = Order::where('unique_id', $order_id)->first();
//     	$currency = Currency::where('id', $order_details->currency_id)->first();
//     	$paid_currency = Currency::where('id', $order_details->paid_currency_id)->first();
    	
//         $order_details->store_name = $store->name;
//         $order_details->currency_code = $currency->code;
//         $order_details->currency_symbole = $currency->symbol;
//         $order_details->user_currency_code = $paid_currency->code;
//         $order_details->user_currency_symbole = $paid_currency->symbol;
        
//         $allproducts = json_decode($order_details->products);
//         foreach($allproducts as $allproduct){
//             $product = Product::where('id', $allproduct->product_id)->first();
//             $products[] = [
//                 'name' => $product->name,
//                 'description' => $product->description,
//                 'image' => url('public/user_dashboard/product/thumb/'.$product->image),
//                 'price' => $product->price
//             ];
//         }
        
//         $order_details->products = $products;
//         $order_details->last_four = $last_four;
	    
// 	    return response()->json([
//     	    'status' => $this->successStatus, 
//     	    'message' => 'Order updated successfully.',
//     	    'data' => $order_details
//     	], $this->successStatus);
//     }
    
    public function update_order(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'order_id' => 'required',
            'payment_status' => 'required',
            'amount' => 'required',
            'currency' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$order_id = $request->order_id;
    	$payment_status = $request->payment_status;
    	$amount = $request->amount;
    	$currency = $request->currency;
    	$name = $request->name;
    	$email = $request->email;
    	$phone = $request->phone;
    	$phone_code = $request->phone_code;
    	$nfc_fee = $request->nfc_fee;
    	$local_tran_time = $request->local_tran_time;
    	$last_four = $request->last_four;
    	
    	$check_currency = Currency::where('code', strtoupper($currency))->first();
    	$order_detail = Order::where('store_user_id', $user_id)->where('unique_id', $order_id)->first();
    	
    	if($order_detail->payment_response == 'success'){
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Payment already done.'
        	], $this->unauthorisedStatus);
    	}elseif($order_detail->payment_response == 'failed'){
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Payment already failed.'
        	], $this->unauthorisedStatus);
    	}
    	
    	$feeInfo       = FeesLimit::where(['transaction_type_id' => '33', 'currency_id' => $check_currency->id])->first();
        $feePercent    = $amount * ($feeInfo->charge_percentage / 100);
        $feePercentage = number_format((float)$feePercent, 2, '.', '');
	    
	    if($payment_status == 'succeeded'){
	        
	        $unique_code           = unique_code();
	        
	        //Payment_Received
            $transaction_B                           = new Transaction();
            $transaction_B->user_id                  = $user_id;
            $transaction_B->end_user_id              = null;
            $transaction_B->currency_id              = $check_currency->id;
            $transaction_B->payment_method_id        = 2;
            $transaction_B->merchant_id              = null;
            $transaction_B->uuid                     = $unique_code;
            $transaction_B->transaction_reference_id = $order_detail->unique_id;
            $transaction_B->transaction_type_id      = "35";
            $transaction_B->subtotal                 = $amount;
            $transaction_B->percentage               = 0;
            $transaction_B->charge_percentage        = $nfc_fee;
            $transaction_B->charge_fixed             = 0;
            $transaction_B->store_fee                = $feePercentage;
            $transaction_B->total                    = $amount;
            $transaction_B->status                   = 'Success';
            $transaction_B->local_tran_time          = $local_tran_time;
            $transaction_B->ip_address               = request()->ip();
            $transaction_B->last_four                = $last_four;
            $transaction_B->save();
            
            $rs = TransDeviceInfo::create([
                'user_id' => $user_id, 
                'trans_id' => $transaction_B->id, 
                'device_id' => $request->device_id, 
                'app_ver' => $request->app_ver, 
                'device_name' => $request->device_name, 
                'device_manufacture' => $request->device_manufacture, 
                'device_model' => $request->device_model, 
                'os_ver' => $request->os_ver, 
                'device_os' => $request->device_os, 
                'ip_address' => request()->ip(),
            ]);
            
            //updating/Creating merchant wallet
            $merchantWallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $check_currency->id])->first(['id', 'balance']);
            if (empty($merchantWallet))
            {
                $wallet              = new Wallet();
                $wallet->user_id     = $user_id;
                $wallet->currency_id = $check_currency->id;
                $wallet->balance     = $amount - ($nfc_fee + $feePercentage);
                $wallet->is_default  = 'No';
                $wallet->save();
            }
            else
            {
                $merchantWallet->balance = $merchantWallet->balance + ($amount - ($nfc_fee + $feePercentage));
                $merchantWallet->save();
            }
	        
	        $update_order = Order::where('store_user_id', $user_id)->where('unique_id', $order_id)->update([
    	        'status' => 'success',
    	        'paid_amount' => $amount,
    	        'paid_currency_id' => $check_currency->id,
    	        'payment_response' => 'success',
    	        'nfc_fee' => $nfc_fee,
    	        'customer_name' => $name,
    	        'customer_email' => $email,
    	        'customer_phone' => $phone,
    	        'customer_phone_prefix' => $phone_code,
    	        'local_tran_time' => $local_tran_time,
                'ip_address' => request()->ip(),
    	    ]);
    	    
	        $cartData = Cart::where('user_id', $user_id)->delete();
	        
	        $allproducts = json_decode($order_detail->products);
            foreach($allproducts as $allproduct){
                $product = Product::where('id', $allproduct->product_id)->first();
                
                if(!empty($product)){
                    $update_stock = Product::where('id', $allproduct->product_id)->update([
                        'quantity' => $product->quantity - $allproduct->qty,
                    ]);
                }
            }
	        
	        $user = User::where('id', $user_id)->first();
	        $store = Store::where('user_id', $user_id)->first();
	        $type = "mpos";
	        $date    = date("m-d-Y h:i");
	        
	        // Email / Notification to user
            $subject   = "Payment Done Successfully!";
            $subheader = "Congratulations! Your payment has been done successfully.";
            $message = "You’ve successfully done payment of ".strtoupper($currency) .' '.$transaction_B->total;
            
        //     $this->helper->sendFirabasePush($subject,$subheader,$user_id, $check_currency->id, $type);
        //     $datanotice1= array('title'=>$subject,'content'=>$message,'type'=>'push','content_type'=>'mpos','user'=>$user_id,'sub_header'=>$subheader,'push_date'=>request('local_tran_time'));
        // 	   DB::table('noticeboard')->insert($datanotice1);
        	
        	if(!empty($email)){
            	$twoStepVerification = EmailTemplate::where([
                    'temp_id'     => 36,
                    'language_id' => getDefaultLanguage(),
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{user}', $name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{store_name}', $store->name, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{order_id}', '#'.$order_id, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{currency}', strtoupper($currency), $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{pay_method}', 'Card Payment', $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{date}', $date, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                $this->email->sendEmail($email, $twoStepVerification_sub, $twoStepVerification_msg);
        	}
        	
            //Notification to Merchant
            $userdevice = DB::table('devices')->where('user_id', $user_id)->first();
            if(!empty($userdevice)){
                $device_lang = $userdevice->language;
            }else{
                $device_lang = getDefaultLanguage();
            }
            $template = NotificationTemplate::where('temp_id', '17')->where('language_id', $device_lang)->first();
            $st_subject = $template->title;
            $st_subheader = $template->subheader;
            $st_message = $template->content;
            
            $msg = str_replace('{currency}', strtoupper($currency), $st_message);
            $msg = str_replace('{amount}', number_format($transaction_B->total, 2, '.', ','), $msg);
            $msg = str_replace('{sender}', 'XXXX '.$last_four, $msg);
            
            $this->helper->sendFirabasePush($st_subject, $msg, $user_id, $check_currency->id, $type);
            
            Noticeboard::create([
                'tr_id' => $transaction_B->id,
                'title' => $st_subject,
                'content' => $msg,
                'type' => 'push',
                'content_type' => 'mpos',
                'user' => $user_id,
                'sub_header' => $st_subheader,
                'push_date' => $request->local_tran_time,
                'template' => '17',
                'language' => $device_lang,
                'currency' => strtoupper($currency),
                'amount' => number_format($transaction_B->total, 2, '.', ','),
                'sender' => 'XXXX '.$last_four
            ]);
            
        	// Email to Merchant
        	if(!empty($user->email)){
            	$twoStepVerificationmerc = EmailTemplate::where([
                    'temp_id'     => 43,
                    'language_id' => $device_lang,
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerificationmerc_sub = $twoStepVerificationmerc->subject;
                $twoStepVerificationmerc_msg = str_replace('{store_name}', $store->name, $twoStepVerificationmerc->body);
                $twoStepVerificationmerc_msg = str_replace('{order_id}', '#'.$order_id, $twoStepVerificationmerc_msg);
                $twoStepVerificationmerc_msg = str_replace('{currency}', strtoupper($currency), $twoStepVerificationmerc_msg);
                $twoStepVerificationmerc_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $twoStepVerificationmerc_msg);
                $twoStepVerificationmerc_msg = str_replace('{pay_method}', 'Card Payment', $twoStepVerificationmerc_msg);
                $twoStepVerificationmerc_msg = str_replace('{date}', $date, $twoStepVerificationmerc_msg);
                $twoStepVerificationmerc_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerificationmerc_msg);
                $this->email->sendEmail($user->email, $twoStepVerificationmerc_sub, $twoStepVerificationmerc_msg);
        	}
        	
        	// Email / Notification to admin
            $adminAllowed = Notification::has_permission([1]);
                                
            foreach($adminAllowed as $admin){
                Notification::insert([
                    'user_id'               => $user_id,
                    'notification_to'       => $admin->agent_id,
                    'notification_type_id'  => 1,
                    'notification_type'     => 'Web',
                    'description'           => 'Payment of '.strtoupper($currency) .' '.$transaction_B->total.' received on '.$store->name,
                    'url_to_go'             => 'admin/mpos/edit/'.$transaction_B->id,
                    'local_tran_time'       => $request->local_tran_time
                ]);
            }
        	
        	$admin->email = $this->admin_email;
        	
        	if(!empty($admin->email)){
            	$twoStepVerificationadm = EmailTemplate::where([
                    'temp_id'     => 40,
                    'language_id' => getDefaultLanguage(),
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerificationadm_sub = $twoStepVerificationadm->subject;
                $twoStepVerificationadm_msg = str_replace('{store_name}', $store->name, $twoStepVerificationadm->body);
                $twoStepVerificationadm_msg = str_replace('{order_id}', '#'.$order_id, $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{currency}', strtoupper($currency), $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{pay_method}', 'Card Payment', $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{date}', $date, $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{merchant_name}', $user->first_name.' '.$user->last_name, $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{customer_name}', $name, $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerificationadm_msg);
                $this->email->sendEmail($admin->email, $twoStepVerificationadm_sub, $twoStepVerificationadm_msg);
        	}
	    }else{
	        $update_order = Order::where('store_user_id', $user_id)->where('unique_id', $order_id)->update([
    	        'status' => 'failed',
    	        'payment_response' => 'failed',
    	        'customer_name' => $name,
    	        'customer_email' => $email,
    	        'customer_phone' => $phone,
    	        'customer_phone_prefix' => $phone_code,
    	    ]);
	    }
	    
	    $order_details = Order::where('unique_id', $order_id)->first();
    	$currency = Currency::where('id', $order_details->currency_id)->first();
    	$paid_currency = Currency::where('id', $order_details->paid_currency_id)->first();
    	
        $order_details->store_name = $store->name;
        $order_details->currency_code = $currency->code;
        $order_details->currency_symbole = $currency->symbol;
        $order_details->user_currency_code = $paid_currency->code;
        $order_details->user_currency_symbole = $paid_currency->symbol;
        
        $allproducts = json_decode($order_details->products);
        foreach($allproducts as $allproduct){
            $product = Product::where('id', $allproduct->product_id)->first();
            $products[] = [
                'name' => $product->name,
                'description' => $product->description,
                'image' => url('public/user_dashboard/product/thumb/'.$product->image),
                'price' => $product->price
            ];
        }
        
        $order_details->products = $products;
        $order_details->last_four = $last_four;
	    
	    return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Order updated successfully.',
    	    'data' => $order_details
    	], $this->successStatus);
    }
    
    public function store_qrcode(Request $request)
    {
        $rules = array(
            'user_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	
	    $qrcode = QrCode::where('object_id', $user_id)->where('status', 'Active')->first();
	    
	    return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Store QR code fetched successfully.',
    	    'qr_code' => "https://api.qrserver.com/v1/create-qr-code/?data=".$qrcode->secret."&amp;size=200x200",
    	], $this->successStatus);
    }
    
    public function fetch_credentials(Request $request)
    {
        $rules = array(
            'user_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	
	    $credentials = NfcCredential::where('id', '1')->where('status', '1')->first();
	    
	    return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Credentials fetched successfully.',
    	    'data' => $credentials,
    	], $this->successStatus);
    }
    
    public function check_order(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'order_id' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$order_id = $request->order_id;
    	
	    $order = Order::where('unique_id', $order_id)->where('store_user_id', $user_id)->where('status', 'success')->where('payment_response', 'success')->first();
	    if(!empty($order)){
	        $currency_symbol = Currency::where('id', $order->currency_id)->first();
	        $paid_currency_symbol = Currency::where('id', $order->paid_currency_id)->first();
	    
	        $order->currency_symbol = $currency_symbol->symbol;
	        $order->paid_currency_symbol = $paid_currency_symbol->symbol;
	        return response()->json([
        	    'status' => $this->successStatus, 
        	    'message' => 'Order was placed successfully.',
        	    'data' => $order,
        	], $this->successStatus);
	    }else{
	        return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Order was not placed.',
        	    'data' => null
        	], $this->unauthorisedStatus);
	    }
    }
    
    public function today_payment(Request $request)
    {
        $rules = array(
            'user_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	
    	$todays = Order::where('store_user_id', $user_id)->where('status', 'success')->where('payment_response', 'success')->whereDate('created_at', Carbon::today())->groupBy('currency_id')->selectRaw('currency_id,sum(total_amount) as total_amt')->get();
    	if(count($todays) > 0){
        	foreach($todays as $today){
        	    $check_currecy = Currency::where('id', $today->currency_id)->first();
        	    $today['currency_code'] = $check_currecy->code;
        	}
        	return response()->json([
        	    'status' => $this->successStatus, 
        	    'message' => 'Today payments fetched successfully.',
        	    'data' => $todays
        	], $this->successStatus);
    	}else{
    	    $store = Store::where('user_id', $user_id)->first();
    	    if(!empty($store)){
    	        $check_currecy = Currency::where('id', $store->currency_id)->first();
    	    
        	    $today['currency_id']   = $store->currency_id;
        	    $today['total_amt']     = "0.00";
        	    $today['currency_code'] = $check_currecy->code;
        	    
        	    return response()->json([
            	    'status' => $this->successStatus, 
            	    'message' => 'Today payments fetched successfully.',
            	    'data' => array($today)
            	], $this->successStatus);
    	    }else{
    	        $wallet = Wallet::where('user_id', $user_id)->where('is_default', 'Yes')->first();
    	        $check_currecy = Currency::where('id', $wallet->currency_id)->first();
    	    
        	    $today['currency_id']   = $wallet->currency_id;
        	    $today['total_amt']     = "0.00";
        	    $today['currency_code'] = $check_currecy->code;
        	    
        	    return response()->json([
            	    'status' => $this->successStatus, 
            	    'message' => 'Today payments fetched successfully.',
            	    'data' => array($today)
            	], $this->successStatus);
    	    }
    	    
    	}
    }
    
    public function invoice(Request $request)
    {
        $rules = array(
            'order_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$order_id = $request->order_id;
    	
    	$order = Order::where('id', $order_id)->where('status', 'success')->where('payment_response', 'success')->first();
    	if(!empty($order)){
    	    
    	    $pdf_url = url('invoice/'.$order_id);
    	    
    	    
    	    $user = User::where('id', $order->store_user_id)->first();
            $store = Store::where('id', $order->store_id)->first();
            $country = Country::where('id', $store->country)->first();
            $currency = Currency::where('id', $store->currency_id)->first();
            $paidcurrency = Currency::where('id', $order->paid_currency_id)->first();
            $transaction = Transaction::where('transaction_reference_id', $order->unique_id)->first();
            $pdf_view = View::make('store.invoice', compact('order', 'user', 'store', 'country', 'currency', 'paidcurrency', 'transaction'))->render();
    	    
    	    
    	    
    	    
    	    
        	return response()->json([
        	    'status' => $this->successStatus, 
        	    'message' => 'Order fetched successfully.',
        	    'data' => $pdf_url,
        	     'pdf_data'=> $pdf_view
        	], $this->successStatus);
    	}else{
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Order not found.',
        	    'data' => null
        	], $this->unauthorisedStatus);
    	}
    }
    
    public function country_payouts(Request $request)
    {
        $rules = array(
            'country_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$country_id = $request->country_id;
    	
    	$payouts = CountryPayout::where('country', $country_id)->orderBy('sort_by', 'asc')->get();
    	foreach($payouts as $payout){
    	    $pay_met = Label::where('id', $payout->payout_method)->first();
    	    $payout['payout_method'] = $pay_met->name;
    	    $payout['string'] = $pay_met->string;
    	}
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Bank fields fetched successfully.',
    	    'data' => $payouts
    	], $this->successStatus);
    }
    
    public function add_country_bank(Request $request)
    {
        $rules = array(
            'country_id' => 'required',
            'user_id' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$country_id = $request->country_id;
    	$user_id = $request->user_id;
    	$banks = $request->banks;
    	
    	$rs = CountryBank::create([
    	    'user_id' => $user_id,
    	    'country_id' => $country_id,
    	    'bank' => json_encode($banks),
    	]);
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Bank added successfully.',
    	    'data' => $rs
    	], $this->successStatus);
    }
    
    public function check_country_bank(Request $request)
    {
        $rules = array(
            'bank_id' => 'required',
            'user_id' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$bank_id = $request->bank_id;
    	$user_id = $request->user_id;
    	
    	$checkBank = CountryBank::where(['id' => $bank_id, 'user_id' => $user_id])->first();
    	if(empty($checkBank)){
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Bank not exists!',
        	    'error' => null
        	], $this->unauthorisedStatus);
    	}
    	
    	$payouts = CountryPayout::where('country', $checkBank->country_id)->orderBy('sort_by', 'asc')->get();
    	$avail_fields = json_decode($checkBank->bank, true);
    	
    	foreach($payouts as $p=>$payout){
    	    $label = Label::where('id', $payout->payout_method)->first();
    	    $payout['payout_method'] = $label->name;
    	    foreach($avail_fields as $a=>$avail_field){
    	        $payout['string'] = $label->string;
    	        if($a == $label->name){
    	            $payout['value'] = $avail_field;
    	        }
    	    }
    	}
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Bank details successfully.',
    	    'data' => $payouts
    	], $this->successStatus);
    }
    
    public function edit_country_bank(Request $request)
    {
        $rules = array(
            'bank_id' => 'required',
            'user_id' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$bank_id = $request->bank_id;
    	$user_id = $request->user_id;
    	$banks = $request->banks;
    	
    	$checkBank = CountryBank::where(['id' => $bank_id, 'user_id' => $user_id])->first();
    	if(empty($checkBank)){
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Bank not exists!',
        	    'error' => null
        	], $this->unauthorisedStatus);
    	}
    	
    	$rs = CountryBank::where('user_id', $user_id)->where('id', $bank_id)->update(['bank' => json_encode($banks)]);
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Bank details updated successfully.',
    	    'data' => null
    	], $this->successStatus);
    }
    
    public function country_bank_list(Request $request)
    {
        $rules = array(
            'user_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	
    	$banks = CountryBank::where('user_id', $user_id)->get();
    	foreach($banks as $bank){
    	    $bank['country'] = Country::where('id', $bank->country_id)->first();
    	}
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Bank fetched successfully.',
    	    'data' => $banks
    	], $this->successStatus);
    }
    
    public function delete_country_bank(Request $request)
    {
        $rules = array(
            'bank_id' => 'required',
            'user_id' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$bank_id = $request->bank_id;
    	$user_id = $request->user_id;
    	
    	$checkBank = CountryBank::where(['id' => $bank_id, 'user_id' => $user_id])->first();
    	if(empty($checkBank)){
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Bank not exists!',
        	    'error' => null
        	], $this->unauthorisedStatus);
    	}
    	
    	$rs = CountryBank::where('user_id', $user_id)->where('id', $bank_id)->delete();
    	
    	return response()->json([
    	    'status' => $this->successStatus, 
    	    'message' => 'Bank deleted successfully.',
    	    'data' => null
    	], $this->successStatus);
    }
    
    public function bank_payout(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'currency_id' => 'required',
            'amount' => 'required',
            'bank_id' => 'required',
            'local_tran_time' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$currency_id = $request->currency_id;
    	$amount = $request->amount;
    	$bank_id = $request->bank_id;
    	$local_tran_time = $request->local_tran_time;
    	$uuid = unique_code();
    	
    	$checkBank = CountryBank::where(['id' => $bank_id, 'user_id' => $user_id])->first();
    	if(empty($checkBank)){
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Bank not exists!',
        	    'error' => null
        	], $this->unauthorisedStatus);
    	}
    	
    	$user_detail = User::where('id', $user_id)->first();
        $user_wallet = Wallet::where('user_id', $user_id)->where('currency_id', $currency_id)->first();
        $feeInfo = FeesLimit::where('currency_id', $currency_id)->where('transaction_type_id', '2')->where('payment_method_id', '6')->first();
        $charge_percent = $feeInfo->charge_percentage;
        $percentage_charge = $amount * ($charge_percent / 100);
        $fixed_charge = $feeInfo->charge_fixed;
    	$totalFees = $percentage_charge + $fixed_charge;
    	$totalWithFee = $totalFees + $amount;
    	
    	// Check Fraud
        $pending_transaction                           = new PendingTransaction();
        $pending_transaction->user_id                  = $user_id;
        $pending_transaction->currency_id              = $currency_id;
        $pending_transaction->payment_method_id        = '6';
        $pending_transaction->transaction_reference_id = $uuid;
        $pending_transaction->transaction_type_id      = Withdrawal;
        $pending_transaction->uuid                     = $uuid;
        $pending_transaction->subtotal                 = $amount - $totalFees;
        $pending_transaction->percentage               = $charge_percent;
        $pending_transaction->charge_percentage        = $percentage_charge;
        $pending_transaction->charge_fixed             = $fixed_charge;
        $pending_transaction->total                    = $amount;
        $pending_transaction->ip_address               = request()->ip();
        $pending_transaction->status                   = 'Pending';
        $pending_transaction->save();
        
        $response_fraud = $this->helper->check_fraud($pending_transaction->id);
        if(!empty($response_fraud->id)){
                    
            if(!empty($response_fraud->transactions_hour)){
                $message = 'You have exceed allowed number of transactions per hour.';
                $fraud_type = 'transactions_hour';
            }elseif(!empty($response_fraud->transactions_day)){
                $message = 'You have exceed allowed number of transactions per day.';
                $fraud_type = 'transactions_day';
            }elseif(!empty($response_fraud->amount_hour)){
                $message = 'You have exceed allowed amount limit per Hour.';
                $fraud_type = 'amount_hour';
            }elseif(!empty($response_fraud->amount_day)){
                $message = 'You have exceed allowed amount limit per Day.';
                $fraud_type = 'amount_day';
            }elseif(!empty($response_fraud->amount_week)){
                $message = 'You have exceed allowed amount limit per Week.';
                $fraud_type = 'amount_week';
            }elseif(!empty($response_fraud->amount_month)){
                $message = 'You have exceed allowed amount limit per Month.';
                $fraud_type = 'amount_month';
            }elseif(!empty($response_fraud->same_amount)){
                $message = 'You transaction is rejected due to repeating same amount multiple times.';
                $fraud_type = 'same_amount';
            }elseif(!empty($response_fraud->email_day)){
                $message = 'You transaction is rejected due to repeat transactions on same account.';
                $fraud_type = 'email_day';
            }elseif(!empty($response_fraud->ipadd_day)){
                $message = 'You transaction is rejected due to repeat transactions on same IP.';
                $fraud_type = 'ipadd_day';
            }elseif(!empty($response_fraud->user_created_at)){
                $message = 'You transaction is rejected as per new account limitations. Please try after some days.';
                $fraud_type = 'user_created_at';
            }
            
            $delete_trans = PendingTransaction::where('id', $pending_transaction->id)->delete();
            
            return response()->json(['status'=>'420','message' => $message,'fraud_type' => $fraud_type], $this->unauthorisedStatus);
        }
    	
    	$checkWalletBalance = Wallet::where(['currency_id' => $currency_id, 'user_id' => $user_id])->first();
    	if (!empty($checkWalletBalance))
        {
            if (($totalWithFee > $checkWalletBalance->balance) || ($checkWalletBalance->balance < 0))
            {
                return response()->json([
            	    'status' => $this->unauthorisedStatus, 
            	    'message' => 'Not have enough balance !',
            	    'error' => null
            	], $this->unauthorisedStatus);
            }else{
                
                //Create Withdrawal
                $withdrawal                      = new Withdrawal();
                $withdrawal->user_id             = $user_id;
                $withdrawal->currency_id         = $currency_id;
                $withdrawal->payment_method_id   = '6';
                $withdrawal->uuid                = $uuid;
                $withdrawal->charge_percentage   = $percentage_charge;
                $withdrawal->charge_fixed        = $fixed_charge;
                $withdrawal->subtotal            = $amount - $totalFees;
                $withdrawal->amount              = $amount;
                $withdrawal->payment_method_info = 'Bank';
                $withdrawal->status              = 'Pending';
                $withdrawal->local_tran_time     = $request->local_tran_time;
                $withdrawal->ip_address          = request()->ip();
                $withdrawal->pay_type            = $request->pay_type;
                $withdrawal->save();
                
                //Create Withdrawal Detail
                $withdrawalDetail                = new WithdrawalDetail();
                $withdrawalDetail->withdrawal_id = $withdrawal->id;
                $withdrawalDetail->type          = '6';
                $withdrawalDetail->email         = $user_detail->email;
                $withdrawalDetail->bank_id       = $bank_id;
                $withdrawalDetail->save();
                
                //Create Withdrawal Transaction
                $transaction                           = new Transaction();
                $transaction->user_id                  = $user_id;
                $transaction->bank_id                  = $bank_id;
                $transaction->currency_id              = $currency_id;
                $transaction->payment_method_id        = '6';
                $transaction->uuid                     = $withdrawal->uuid;
                $transaction->transaction_reference_id = $withdrawal->id;
                $transaction->transaction_type_id      = '2';
                $transaction->subtotal                 = $amount - $totalFees;
                $transaction->percentage               = $charge_percent;
                $transaction->charge_percentage        = $percentage_charge;
                $transaction->charge_fixed             = $fixed_charge;
                $transaction->total                    = '-' . ($amount);
                $transaction->local_tran_time          = $local_tran_time;
                $transaction->ip_address               = request()->ip();
                $transaction->pay_type                 = $request->pay_type;
                $transaction->status                   = 'Pending';
                $transaction->save();
                
                $rs = TransDeviceInfo::create([
                    'user_id' => $user_id, 
                    'trans_id' => $transaction->id, 
                    'device_id' => $request->device_id, 
                    'app_ver' => $request->app_ver, 
                    'device_name' => $request->device_name, 
                    'device_manufacture' => $request->device_manufacture, 
                    'device_model' => $request->device_model, 
                    'os_ver' => $request->os_ver, 
                    'device_os' => $request->device_os, 
                    'ip_address' => request()->ip(),
                ]);
                
                //Update Wallet
                $update_wallet = Wallet::where('user_id', $user_id)->where('currency_id', $currency_id)->update(['balance' => $user_wallet->balance - $amount]);
                
                // //Admin Notification
                $notificationToAdmin = $this->helper->sendTransactionNotificationToAdmin('payout', ['data' => $withdrawal]);
                
                $currency = $currency_id;
        	    $check_currency = Currency::where('id', $currency_id)->first();
                $type = "mpos";
                $date    = date("m-d-Y h:i");
                
                $userdevice = DB::table('devices')->where('user_id', $user_id)->first();
                if(!empty($userdevice)){
                    $device_lang = $userdevice->language;
                }else{
                    $device_lang = getDefaultLanguage();
                }
                $template = NotificationTemplate::where('temp_id', '2')->where('language_id', $device_lang)->first();
                $subject = $template->title;
                $subheader = $template->subheader;
                $message = $template->content;
                
                $msg = str_replace('{currency}', $check_currency->code, $message);
                $msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $msg);
                
                $this->helper->sendFirabasePush($subject,$msg,$user_id, $currency, $type);
                
                Noticeboard::create([
                    'tr_id' => $transaction->id,
                    'title' => $subject,
                    'content' => $msg,
                    'type' => 'push',
                    'content_type' => 'payout',
                    'user' => $user_id,
                    'sub_header' => $subheader,
                    'push_date' => $request->local_tran_time,
                    'template' => '2',
                    'language' => $device_lang,
                    'currency' => $check_currency->code,
                    'amount' => number_format($amount, 2, '.', ',')
                ]);
                
            	$twoStepVerification = EmailTemplate::where([
                    'temp_id'     => 44,
                    'language_id' => $device_lang,
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerification_sub = $twoStepVerification->subject;
                $twoStepVerification_msg = str_replace('{user_id}', $user_detail->first_name . ' ' . $user_detail->last_name, $twoStepVerification->body);
                $twoStepVerification_msg = str_replace('{amount}', $amount, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{amountinbank}', $transaction->subtotal, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{created_at}', request('local_tran_time'), $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{uuid}', $transaction->uuid, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{code}', $check_currency->code, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{fee}', $transaction->charge_percentage + $transaction->charge_fixed, $twoStepVerification_msg);
                $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
                $this->email->sendEmail($user_detail->email, $twoStepVerification_sub, $twoStepVerification_msg);
                
                $response['withdrawalTransactionId'] = $transaction->id;
                $response['withdrawal_id'] = $withdrawal->id;
                $response['status'] = 200;
                
                return response()->json([
            	    'status' => $this->successStatus, 
            	    'message' => 'Withdraw request sent successfully.',
            	    'data' => $response
            	], $this->successStatus);
            }
        }else{
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Wallet not exists!',
        	    'error' => null
        	], $this->unauthorisedStatus);
        }
    }
    
    public function bank_check_limit(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'currency_id' => 'required',
            'amount' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$user_id = $request->user_id;
    	$currency_id = $request->currency_id;
    	$amount = $request->amount;
        
        $user_detail = User::where('id', $user_id)->first();
        $user_wallet = Wallet::where('user_id', $user_id)->where('currency_id', $currency_id)->first();
        $feeInfo = FeesLimit::where('currency_id', $currency_id)->where('transaction_type_id', '2')->where('payment_method_id', '6')->first();
        $charge_percent = $feeInfo->charge_percentage;
        $percentage_charge = $amount * ($charge_percent / 100);
        $fixed_charge = $feeInfo->charge_fixed;
    	$totalFees = $percentage_charge + $fixed_charge;
    	$totalWithFee = $totalFees + $amount;
    	
    	$checkWalletBalance = Wallet::where(['currency_id' => $currency_id, 'user_id' => $user_id])->first();
    	if(!empty($checkWalletBalance)){
            if(($totalWithFee > $checkWalletBalance->balance) || ($checkWalletBalance->balance < 0))
            {
                return response()->json([
            	    'status' => $this->unauthorisedStatus, 
            	    'message' => 'Not have enough balance !',
            	    'error' => null
            	], $this->unauthorisedStatus);
            }else{
                if($totalWithFee < $feeInfo->min_limit){
                    $failed['reason']   = 'minLimit';
                    $failed['minLimit'] = $feeInfo->min_limit;
                    
                    return response()->json([
                	    'status' => $this->unauthorisedStatus, 
                	    'message' => 'Minimum amount ' . formatNumber($feeInfo->min_limit),
                	    'error' => $failed
                	], $this->unauthorisedStatus);
                }
                
                if($totalWithFee > $feeInfo->max_limit){
                    $failed['reason']   = 'maxLimit';
                    $failed['minLimit'] = $feeInfo->max_limit;
                    
                    return response()->json([
                	    'status' => $this->unauthorisedStatus, 
                	    'message' => 'Maximum amount ' . formatNumber($feeInfo->max_limit),
                	    'error' => $failed
                	], $this->unauthorisedStatus);
                }
                
                $success['amount']            = $amount;
                $success['totalFees']         = $totalFees;
                $success['totalHtml']         = formatNumber($totalFees);
                $success['currency_id']       = $feeInfo->currency_id;
                $success['currSymbol']        = $feeInfo->currency->symbol;
                $success['currCode']          = $feeInfo->currency->code;
                $success['totalAmount']       = $totalWithFee;
                
                return response()->json([
            	    'status' => $this->successStatus, 
            	    'message' => 'Balance available.',
            	    'data' => $success
            	], $this->successStatus);
            }
    	}else{
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Wallet not available !',
        	    'error' => null
        	], $this->unauthorisedStatus);
    	}
    }
    
    public function transaction_limit(Request $request)
    {
        $rules = array(
            'currency_id' => 'required',
            'trans_type' => 'required',
            'pay_method' => 'required',
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$currency_id = $request->currency_id;
    	$trans_type = $request->trans_type;
    	$pay_method = $request->pay_method;
        
        $feeInfo = FeesLimit::where('currency_id', $currency_id)->where('transaction_type_id', $trans_type)->where('payment_method_id', $pay_method)->first();
    	if(!empty($feeInfo)){
    	    $success['charge_percent'] = $feeInfo->charge_percentage;
            $success['fixed_charge'] = $feeInfo->charge_fixed;
        	$success['min_limit'] = $feeInfo->min_limit;
        	$success['max_limit'] = $feeInfo->max_limit;
                
            return response()->json([
        	    'status' => $this->successStatus, 
        	    'message' => 'Details fetched successfully.',
        	    'data' => $success
        	], $this->successStatus);
    	}else{
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'No details found.',
        	    'data' => null
        	], $this->unauthorisedStatus);
    	}
    }
	
	function random_strings($length_of_string) 
    { 
        $str_result = '0123456789abcdefghijklmnopqrstuvwxyz'; 
        return substr(str_shuffle($str_result), 0, $length_of_string); 
    } 
    
    public function CollectPaymentStore(Request $request)
    { 
        $validation = Validator::make(request()->all(), [
            'stripeToken' => '',
        ]);
        
        if ($validation->fails())
        {
            $data['status']  = 401;
            $data['message'] = $validation->errors();
            return response()->json(['success' => $data]);
        }
        
        $payment_method_id = request('collect_payment_id');

        $user_id = request('user_id');
        $wallet  = Wallet::where(['currency_id' => request('currency_id'), 'user_id' => $user_id])->first(['id', 'currency_id']);
        try{
            \DB::beginTransaction();

            if (empty($wallet))
            {
                $walletInstance              = new Wallet();
                $walletInstance->user_id     = $user_id;
                $walletInstance->currency_id = request('currency_id');
                $walletInstance->balance     = 0;
                $walletInstance->is_default  = 'No';
                $walletInstance->save();
            }
            
            $currencyId = isset($wallet->currency_id) ? $wallet->currency_id : $walletInstance->currency_id;
            $currency   = Currency::find($currencyId, ['id', 'code']);

            $totalAmount           = (float) request('totalAmount');
            $amount                = (float) request('amount');
            if (request()->all())
            {   
                $token         = "ABC";
                $feeInfo       = FeesLimit::where(['transaction_type_id' => '42', 'currency_id' => $currencyId, 'payment_method_id' => $payment_method_id])->first(['charge_percentage', 'charge_fixed']);
                $feePercentage = $amount * ($feeInfo->charge_percentage / 100);
                $feeInfomPos       = FeesLimit::where(['transaction_type_id' => '33', 'currency_id' =>$currencyId])->first();
                $feePercentMpos    = $amount * ($feeInfomPos->charge_percentage / 100);
                $feePercentageMpos = number_format((float)$feePercentMpos, 2, '.', '');
                
                $uuid                       = unique_code();

                //Save to Deposit
                $deposit                    = new CollectPayment();
                $deposit->user_id           = $user_id;
                $deposit->store_id           = request('store_id');
                $deposit->store_user_id     = $user_id;
                $deposit->currency_id       = $currencyId;
                $deposit->payment_method_id = $payment_method_id;
                $deposit->uuid              = $uuid;
                $deposit->charge_percentage = $feePercentage;
                $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
                $deposit->amount            = $amount;
                $deposit->status            = 'Success';
                $deposit->local_tran_time   = $request->local_tran_time;
                $deposit->ip_address        = request()->ip();
                $deposit->save();

               // Save to Transaction
                $transaction                           = new Transaction();
                $transaction->user_id                  = $user_id;
                $transaction->currency_id              = $currencyId;
                $transaction->payment_method_id        = $payment_method_id;
                $transaction->uuid                     = $uuid;
                $transaction->transaction_reference_id = $uuid;
                $transaction->transaction_type_id      = '42';
                $transaction->subtotal                 = $amount;
                $transaction->percentage               = $feeInfo->charge_percentage;
                $transaction->charge_percentage        = $feePercentage;
                $transaction->charge_fixed             = $feeInfo->charge_fixed;
                $transaction->total                    = $amount;
                $transaction->status                   = 'Success';
                $transaction->store_fee                = $feePercentageMpos+$feeInfomPos->charge_fixed??0;
                $transaction->local_tran_time          = $request->local_tran_time;
                $transaction->last_four                = $request->last_four;
                $transaction->ip_address               = request()->ip();
                $transaction->save();
                
                if($transaction->id){
                    $rs = TransDeviceInfo::create([
                        'user_id' => $user_id, 
                        'trans_id' => $transaction->id, 
                        'device_id' => $request->device_id, 
                        'app_ver' => $request->app_ver, 
                        'device_name' => $request->device_name, 
                        'device_manufacture' => $request->device_manufacture, 
                        'device_model' => $request->device_model, 
                        'os_ver' => $request->os_ver, 
                        'device_os' => $request->device_os, 
                        'ip_address' => request()->ip(),
                    ]);
                }
                //Update to Wallet
                $fee_total= $feeInfo->charge_fixed+$feePercentage+$feePercentageMpos+$feeInfomPos->charge_fixed;
                $addtowallet=$amount-$fee_total ;//($feeInfo->charge_fixed??0+$feePercentage+$feePercentageMpos+$feeInfomPos->charge_fixed??0);
                $wallet          = Wallet::where(['user_id' => $user_id, 'currency_id' => $currencyId])->first(['id', 'balance']);
                $wallet->balance = ($wallet->balance + $addtowallet);
                $wallet->save();

                \DB::commit();

                $userdevice = DB::table('devices')->where('user_id', $user_id)->first();
                $date    = date("m-d-Y h:i");
            	$user = User::where('id', $user_id)->first();
            	$currency_sym = Currency::where('id', $currencyId)->first();
            	$transaction->collect_payment_id=$deposit->id;
                $data['transaction'] = $transaction;
                $data['status']      = 200;
                return response()->json(['status'=>200,'success' => $data]);
            }
            else
            {
                $data['status']  = 401;
                $data['message'] = $validation->errors();
                return response()->json(['status'=>401,'success' => $data]);
            }
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage();
            return response()->json(['status'=>$this->unauthorisedStatus,'success' => $success], $this->unauthorisedStatus);
        }
    }
    
      public function collectPaymentInvoice(Request $request)
    {
        $rules = array(
            'collect_payment_id' => 'required'
        );
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        
    	$order_id = $request->collect_payment_id;
    	
    	$order = CollectPayment::where('id', $order_id)->first();
    	if(!empty($order)){
    	    
    	    $pdf_url = url('collect-payment-invoice/'.$order_id);
    	    
  
            $user = User::where('id', $order->store_user_id)->first();
            $store = Store::where('id', $order->store_id)->first();
            $country = Country::where('id', $store->country??'')->first();
            $currency = Currency::where('id', $order->currency_id)->first();
            $paidcurrency = Currency::where('id', $order->currency_id)->first();
            $transaction = Transaction::where('uuid', $order->uuid)->first();
            
            $pdf_view = View::make('store.collectpaymentInvoice', compact('order', 'user', 'store', 'country', 'currency', 'paidcurrency', 'transaction'))->render();

        	return response()->json([
        	    'status' => $this->successStatus, 
        	    'message' => 'Collect payment fetched successfully.',
        	    'data' => $pdf_url,
        	    'pdf_data'=> $pdf_view
        	], $this->successStatus);
    	}else{
    	    return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'Payment not found.',
        	    'data' => null
        	], $this->unauthorisedStatus);
    	}
    }
        public function createCollectPaymentWallet(Request $request){
            
        $rules = array(
            'user_id' => 'required',
            'currency_id'   => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return response()->json([
        	    'status' => $this->unauthorisedStatus, 
        	    'message' => 'All fields are required!',
        	    'error' => $validator->errors()
        	], $this->unauthorisedStatus);
        }
        try{
    	$user_id = $request->user_id;
    	$currency_id = $request->currency_id;
    	$checkmerchantWallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $currency_id])->first();
    	  if (empty($checkmerchantWallet))
            {
                $wallet              = new Wallet();
                $wallet->user_id     = $user_id;
                $wallet->currency_id = $currency_id;
                $wallet->balance     = 0;
                $wallet->is_default  = 'No';
                $wallet->is_collect_payment=1;
                $wallet->save();
            }else{

             	$checkmerchantWallet->update(['is_collect_payment'=>1]);
        
         	}
    	  
    	    return response()->json([
        	    'status' => $this->successStatus, 
        	    'message' => 'Collect payment wallet created successfully.',
        	], $this->successStatus);
        }catch(\Exception $e){
            
        }
    }
}
