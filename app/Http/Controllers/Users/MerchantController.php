<?php
namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\MerchantGroup;
use App\Models\MerchantPayment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\QrCode;
use Auth;use Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;
use Validator;
use FileHelper;
use App\Models\Store;
use Illuminate\Support\Str;
use App\Models\ShippingCost;
use App\Models\Country;

class MerchantController extends Controller
	{
		protected $helper;
		public function __construct()
		{
			$this->helper = new Common();
		}
		public function index()
		{
			$data['menu']          = 'merchant';
			$data['sub_menu']      = 'merchant';
			$data['content_title'] = 'Merchant';
			$data['icon']          = 'user';
			$data['list']          = $merchants          = Merchant::with(['appInfo', 'currency:id,code'])->where(['user_id' => Auth::user()->id])->orderBy('id', 'desc')->paginate(10);
			// dd($merchants);
			$data['defaultWallet'] = $defaultWallet = Wallet::where(['user_id' => auth()->user()->id, 'is_default' => 'Yes'])->first(['currency_id']); //new
			//check Decimal Thousand Money Format Preference
			$data['preference']   = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);
			return view('user_dashboard.Merchant.list', $data);
		}

		//Standard Merchant QrCode - starts
		public function generateStandardMerchantPaymentQrCode(Request $request)
		{
			// dd($request->all());
			$qrCode           = QrCode::where(['object_id' => $request->merchantId, 'object_type' => 'standard_merchant', 'status' => 'Active'])->first(['id', 'secret']);
			$merchantCurrency = Currency::where('id', $request->merchantDefaultCurrency)->first(['code']); // NEW - THIS IS ADDED AS in PAYMONEY 2.1 THERE WAS DEFAULT CURRENCY WHEN QRCODE WAS DONE
			if (empty($qrCode))
			{
				$createMerchantQrCode              = new QrCode();
				$createMerchantQrCode->object_id   = $request->merchantId;
				$createMerchantQrCode->object_type = 'standard_merchant';
				$createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->paymentAmount . '-' . str_random(6));
				$createMerchantQrCode->status      = 'Active';
				$createMerchantQrCode->save();
				return response()->json([
					'status' => true,
					'secret' => urlencode($createMerchantQrCode->secret),
				]);
			}
			else
			{
				//Make existing qr-code inactive
				$qrCode->status = 'Inactive';
				$qrCode->save();

				//create a new qr-code entry on each update, after making status 'Inactive'
				$createMerchantQrCode              = new QrCode();
				$createMerchantQrCode->object_id   = $request->merchantId;
				$createMerchantQrCode->object_type = 'standard_merchant';
				$createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->paymentAmount . '-' . str_random(6));
				$createMerchantQrCode->status      = 'Active';
				$createMerchantQrCode->save();
				return response()->json([
					'status' => true,
					'secret' => urlencode($createMerchantQrCode->secret),
				]);
			}
		}
		//Standard Merchant QrCode - ends

		//Express Merchant QrCode - starts
		public function generateExpressMerchantQrCode(Request $request)
		{
			// dd($request->all());

			//merchantDefaultCurrencyId
			// $qrCode = QrCode::where(['object_id' => $request->merchantId, 'status' => 'Active'])->first(['id','secret']);
			$qrCode           = QrCode::where(['object_id' => $request->merchantId, 'object_type' => 'express_merchant', 'status' => 'Active'])->first(['id', 'secret']);
			$merchantCurrency = Currency::where('id', $request->merchantDefaultCurrencyId)->first(['code']); // NEW - THIS IS ADDED AS in PAYMONEY 2.1 THERE WAS DEFAULT CURRENCY WHEN QRCODE WAS DONE
			if (empty($qrCode))
			{
				$createMerchantQrCode              = new QrCode();
				$createMerchantQrCode->object_id   = $request->merchantId;
				$createMerchantQrCode->object_type = 'express_merchant';
				$createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->clientId . str_random(6));
				$createMerchantQrCode->status      = 'Active';
				$createMerchantQrCode->save();
				return response()->json([
					'status' => true,
					'secret' => urlencode($createMerchantQrCode->secret),
				]);
			}
			else
			{
				return response()->json([
					'status' => true,
					'secret' => urlencode($qrCode->secret),
				]);
			}
		}

		public function updateExpressMerchantQrCode(Request $request)
		{
			// dd($request->all());

			$qrCode = QrCode::where(['object_id' => $request->merchantId, 'object_type' => 'express_merchant', 'status' => 'Active'])->first(['id', 'secret']);

			$merchantCurrency = Currency::where('id', $request->merchantDefaultCurrencyId)->first(['code']); // NEW - THIS IS ADDED AS in PAYMONEY 2.1 THERE WAS DEFAULT CURRENCY WHEN QRCODE WAS DONE
			if (empty($qrCode))
			{
				$createMerchantQrCode              = new QrCode();
				$createMerchantQrCode->object_id   = $request->merchantId;
				$createMerchantQrCode->object_type = 'express_merchant';
				$createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->clientId . str_random(6));
				$createMerchantQrCode->status      = 'Active';
				$createMerchantQrCode->save();
				return response()->json([
					'status' => true,
					'secret' => urlencode($createMerchantQrCode->secret),
				]);
			}
			else
			{
				// //Make existing qr-code inactive
				$qrCode->status = 'Inactive';
				$qrCode->save();

				//create a new qr-code entry on each update, after making status 'Inactive'
				$createMerchantQrCode              = new QrCode();
				$createMerchantQrCode->object_id   = $request->merchantId;
				$createMerchantQrCode->object_type = 'express_merchant';
				$createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->clientId . str_random(6));
				$createMerchantQrCode->status      = 'Active';
				$createMerchantQrCode->save();
				return response()->json([
					'status' => true,
					'secret' => urlencode($createMerchantQrCode->secret),
				]);
			}
		}
		//Express Merchant QrCode - ends

		//Print Merchant QrCode - starts
		public function printMerchantQrCode($merchantId, $objectType)
		{
			$this->helper->printQrCode($merchantId, $objectType);
		}
		//Print Merchant QrCode - ends
		
		//Store Merchant QrCode - starts
		public function generateStoreMerchantQrCode(Request $request)
		{
			$qrCode = QrCode::where(['object_id' => $request->userId, 'object_type' => 'store', 'status' => 'Active'])->first(['id', 'secret']);
			if (empty($qrCode))
			{
				$createMerchantQrCode              = new QrCode();
				$createMerchantQrCode->object_id   = $request->userId;
				$createMerchantQrCode->object_type = 'store';
				$createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->userId . '-' . str_random(6));
				$createMerchantQrCode->status      = 'Active';
				$createMerchantQrCode->save();
				return response()->json([
					'status' => true,
					'secret' => urlencode($createMerchantQrCode->secret),
				]);
			}
			else
			{
				return response()->json([
					'status' => true,
					'secret' => urlencode($qrCode->secret),
				]);
			}
		}
		
		public function updateStoreMerchantQrCode(Request $request)
		{
			$qrCode = QrCode::where(['object_id' => $request->userId, 'object_type' => 'store', 'status' => 'Active'])->first(['id', 'secret']);

			if (empty($qrCode))
			{
				$createMerchantQrCode              = new QrCode();
				$createMerchantQrCode->object_id   = $request->userId;
				$createMerchantQrCode->object_type = 'store';
				$createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->userId . '-' . str_random(6));
				$createMerchantQrCode->status      = 'Active';
				$createMerchantQrCode->save();
				return response()->json([
					'status' => true,
					'secret' => urlencode($createMerchantQrCode->secret),
				]);
			}
			else
			{
				// //Make existing qr-code inactive
				$qrCode->status = 'Inactive';
				$qrCode->save();

				//create a new qr-code entry on each update, after making status 'Inactive'
				$createMerchantQrCode              = new QrCode();
				$createMerchantQrCode->object_id   = $request->userId;
				$createMerchantQrCode->object_type = 'store';
				$createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->userId . '-' . str_random(6));
				$createMerchantQrCode->status      = 'Active';
				$createMerchantQrCode->save();
				return response()->json([
					'status' => true,
					'secret' => urlencode($createMerchantQrCode->secret),
				]);
			}
		}
		//Store Merchant QrCode - ends

		public function add()
		{
			$data['menu']     = 'merchant';
			$data['sub_menu'] = 'merchant';

			//pm_v2.3
			$data['activeCurrencies'] = $activeCurrencies = Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'code']);
			$data['defaultWallet']    = $defaultWallet    = Wallet::where(['user_id' => auth()->user()->id, 'is_default' => 'Yes'])->first(['currency_id']);

			return view('user_dashboard.Merchant.add', $data);
		}

		public function store(Request $request)
		{
			$rules = array(
				'business_name' => 'required|unique_merchant_business_name',
				'site_url'      => 'required|url',
				'type'          => 'required',
				'note'          => 'required',
				'logo'          => 'mimes:png,jpg,jpeg,gif,bmp',
			);

			$fieldNames = array(
				'business_name' => 'Business Name',
				'site_url'      => 'Site url',
				'type'          => 'Type',
				'note'          => 'Note',
				'logo'          => 'The file must be an image (png, jpg, jpeg,gif,bmp)',
			);

			$validator = Validator::make($request->all(), $rules);
			$validator->setAttributeNames($fieldNames);

			if ($validator->fails())
			{
				return back()->withErrors($validator)->withInput();
			}
			else
			{
				try
				{
					\DB::beginTransaction();

					$filename = null;
					$picture  = $request->logo;
					if (isset($picture))
					{
						$dir = public_path("/user_dashboard/merchant/");
						$ext = $picture->getClientOriginalExtension();
						// dd($ext);
						$filename = time() . '.' . $ext;

						if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp')
						{
							$img = Image::make($picture->getRealPath());
							$img->resize(100, 80)->save($dir . '/' . $filename);
							$img->resize(70, 70)->save($dir . '/thumb/' . $filename);
						}
						else
						{
							$this->helper->one_time_message('error', 'Invalid Image Format!');
						}
					}

					$merchantGroup               = MerchantGroup::where(['is_default' => 'Yes'])->select('id', 'fee')->first();
					$Merchant                    = new Merchant();
					$Merchant->user_id           = Auth::user()->id;
					$Merchant->currency_id       = $request->currency_id;
					$Merchant->merchant_group_id = isset($merchantGroup) ? $merchantGroup->id : null;
					$Merchant->business_name     = $request->business_name;
					$Merchant->site_url          = $request->site_url;
					$uuid                        = unique_code();
					$Merchant->merchant_uuid     = $uuid;
					$Merchant->type              = $request->type;
					$Merchant->note              = $request->note;
					$Merchant->logo              = $filename;
					$Merchant->fee               = isset($merchantGroup) ? $merchantGroup->fee : 0.00;
					// dd($Merchant);
					$Merchant->save();

					if (strtolower($request->type) == 'express')
					{
						try {
							$Merchant->appInfo()->create([
								'client_id'     => str_random(30),
								'client_secret' => str_random(100),
							]);
						}
						catch (\Exception $ex)
						{
							DB::rollBack();
							$this->helper->one_time_message('error', __('Client id must be unique. Please try again!'));
							return back();
						}
					}

					\DB::commit();
					$this->helper->one_time_message('success', __('Merchant Created Successfully!'));
					return redirect('merchants');
				}
				catch (\Exception $e)
				{
					\DB::rollBack();
					$this->helper->one_time_message('error', $e->getMessage());
					return redirect('merchants');
				}
			}
		}

		public function edit($id)
		{
			$data['menu']             = 'merchant';
			$data['sub_menu']         = 'merchant';
			$data['content_title']    = 'Merchant';
			$data['icon']             = 'user';
			$data['activeCurrencies'] = $activeCurrencies = Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'code']);
			$data['merchant']         = $merchant         = Merchant::with('currency:id,code')->find($id);
			$data['defaultWallet']    = $defaultWallet    = Wallet::with(['currency:id,code'])->where(['user_id' => $merchant->user->id, 'is_default' => 'Yes'])->first(['currency_id']); //new
			if (!isset($merchant) || $merchant->user_id != Auth::user()->id)
			{
				abort(404);
			}
			return view('user_dashboard.Merchant.edit', $data);
		}

		public function update(Request $request)
		{
			// dd($request->all());
			$rules = array(
				'business_name' => 'required|unique:merchants,business_name,' . $request->id,
				'site_url'      => 'required|url',
				'note'          => 'required',
				'logo'          => 'mimes:png,jpg,jpeg,gif,bmp',
			);

			$fieldNames = array(
				'business_name' => 'Business Name',
				'site_url'      => 'Site url',
				'note'          => 'Note',
				'logo'          => 'The file must be an image (png, jpg, jpeg, gif,bmp)',
			);

			$validator = Validator::make($request->all(), $rules);
			$validator->setAttributeNames($fieldNames);

			if ($validator->fails())
			{
				return back()->withErrors($validator)->withInput();
			}
			else
			{
				$picture  = $request->logo;
				$filename = null;

				try
				{
					\DB::beginTransaction();

					if (isset($picture))
					{
						$dir      = public_path("/user_dashboard/merchant/");
						$ext      = $picture->getClientOriginalExtension();
						$filename = time() . '.' . $ext;

						if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp')
						{
							$img = Image::make($picture->getRealPath());
							$img->resize(100, 80)->save($dir . '/' . $filename);
							$img->resize(70, 70)->save($dir . '/thumb/' . $filename);
						}
						else
						{
							$this->helper->one_time_message('error', 'Invalid Image Format!');
						}
					}
					$Merchant                = Merchant::find($request->id, ['id', 'currency_id', 'business_name', 'site_url', 'note', 'logo']);
					$Merchant->currency_id   = $request->currency_id; //2.3
					$Merchant->business_name = $request->business_name;
					$Merchant->site_url      = $request->site_url;
					$Merchant->note          = $request->note;
					$Merchant->tax          = $request->tax;
					if ($filename != null)
					{
						$Merchant->logo = $filename;
					}
					$Merchant->save();

					\DB::commit();
					$this->helper->one_time_message('success', __('Merchant Updated Successfully!'));
					return redirect('merchants');
				}
				catch (\Exception $e)
				{
					\DB::rollBack();
					$this->helper->one_time_message('error', $e->getMessage());
					return redirect('merchants');
				}
			}
		}

		public function detail($id)
		{
			$data['menu']          = 'merchant';
			$data['sub_menu']      = 'merchant';
			$data['content_title'] = 'Merchant';
			$data['icon']          = 'user';
			$data['merchant']      = $merchant      = Merchant::find($id);
			$data['defaultWallet'] = $defaultWallet = Wallet::with(['currency:id,code'])->where(['user_id' => $merchant->user->id, 'is_default' => 'Yes'])->first(['currency_id']); //new
			if (!isset($merchant) || $merchant->user_id != Auth::user()->id)
			{
				abort(404);
			}
			return view('user_dashboard.Merchant.detail', $data);
		}

		public function payments()
		{
			$data['menu']              = 'merchant_payment';
			$data['sub_menu']          = 'merchant_payment';
			$data['content_title']     = 'Merchant payments';
			$data['icon']              = 'user';
			$merchant                  = Merchant::where('user_id', Auth::user()->id)->pluck('id')->toArray();
			$data['merchant_payments'] = MerchantPayment::with(['merchant:id,business_name', 'payment_method:id,name', 'currency:id,code'])->whereIn('merchant_id', $merchant)
				->select('id', 'created_at', 'merchant_id', 'payment_method_id', 'order_no', 'amount', 'charge_percentage', 'charge_fixed', 'total', 'currency_id', 'status')
				->orderBy('id', 'desc')->paginate(15);

			return view('user_dashboard.Merchant.payments', $data);
	}		
	
	public function mystore()
	{	
		$user_id                   = auth()->user()->id;	
        $currencyArray[0]          = 1;
		$walletData                = Wallet::where(['user_id' => $user_id])->get(['currency_id'])->toArray();
		$storeCurrencyData         = array();
		if(!empty($walletData)){
			
			foreach($walletData as $key=>$val){
				 if(!in_array($val['currency_id'],$currencyArray)){
					 array_push($currencyArray,$val['currency_id']);
				 }
			}
		}
		foreach($currencyArray as $key=>$val){
			$query            = Currency::where('id',$val)->where('status','Active');
			$currencyData     = $query->get()->toArray();
            if(!empty($currencyData)){
			$index            = count($storeCurrencyData);	
            $storeCurrencyData[$index]['id']     = $currencyData[0]['id'];
            $storeCurrencyData[$index]['name']   = $currencyData[0]['name'];
            $storeCurrencyData[$index]['symbol'] = $currencyData[0]['symbol'];
            $storeCurrencyData[$index]['code']   = $currencyData[0]['code'];
			}
		}
		
		$obj                       = new Store();		
		$data['storeCurrencyData'] = $storeCurrencyData;
		$data['countries']         = Country::where('status', '1')->get();
		$data['menu']              = 'my_store';        
		$data['sub_menu']          = 'my_store';      		
		$data['title']             = 'My Store';      		
		$data['content_title']     = 'My Store';       
		$userData                  = $obj->where('user_id',$user_id)->get()->toArray();		
		$data['data']              = array();
        $store_url                 = '';		
		if(!empty($userData)){		
    		$data['data']              = $userData[0];	
            $store_url                 = url('/store/'.$userData[0]['slug']);
		}	
		$data['store_url']         = $store_url;
		
		if(empty($userData))
		{
		    return view('user_dashboard.Merchant.mystorenew', $data);	
		}
		return view('user_dashboard.Merchant.mystore', $data);	
	}
	
	public function submitdata(Request $request){		     
		$user_id                        = auth()->user()->id;		        
		$obj                            = new Store();				
		$storeData                      = $obj->where('user_id',$user_id)->get()->toArray();	
		
		$rules['name']                  = 'required';				
		$rules['description']           = 'required';				
		$rules['image']                 = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';			$validator                      = Validator::make($request->all(), $rules)->setAttributeNames([ 'name' => LANG::get('message.store.name'),'description'=>LANG::get('message.store.description'),'image'=> LANG::get('message.store.logo')]);		
		if ($validator->fails()) {					
		$url = route('mystore');					
		return redirect($url)								
		->withErrors($validator)								
		->withInput();				
		}                
		$postData                       =  $request->all();	
		if(!empty($storeData)){				
		$postData['id']                 = $storeData[0]['id'];
		$postData['updated_at']         = date('Y-m-d H:i:s');	
		$postData['slug']               = $this->createSlug(request('name'),$storeData[0]['id']);
		}else{
		 $postData['slug']               = $this->createSlug(request('name'));
		$postData['created_at']         = date('Y-m-d H:i:s');	
		$postData['updated_at']         = date('Y-m-d H:i:s');	
		}		
		if($request->file('image')){					
			$imageData              = $request->file('image');				    
			$dir                    = public_path("/uploads/store/");                   
			$ext                    = $imageData->getClientOriginalExtension();                    $filename               = time().'.'. $ext;                    
			$img                    = Image::make($imageData->getRealPath());                    
			$img->resize(100, 80)->save($dir . '/' . $filename);          
			$postData['image']      = $filename;          
		}			    
		$postData['name']       = request('name');				
		$postData['user_id']    = $user_id;				
		$postData['description']= request('description');				
		$id                     = $obj->addeditdata($postData);				
		$showmessage            = 'Store updated suceessfully';				
		return redirect('mystore')->with('success',$showmessage);          		   
			
	}


	public function createSlug($title, $id = 0)
		{
				$slug = Str::slug($title,'-');
				$allSlugs = $this->getRelatedSlugs($slug, $id);
				if (! $allSlugs->contains('slug', $slug)){
					return $slug;
				}
				for ($i = 1; $i <= 1000; $i++) {
					$newSlug = $slug.'-'.$i;
					if (! $allSlugs->contains('slug', $newSlug)) {
						return $newSlug;
					}
				}
				throw new \Exception('Can not create a unique slug');
	}

	protected function getRelatedSlugs($slug, $id = 0){
				return Store::select('slug')->where('slug', 'like', $slug.'%')
				->where('id', '<>', $id)
				->get();
	}



        public function shipping_cost()
		{
			$data['menu']          = 'shipping_cost';
			$data['sub_menu']      = 'shipping_cost';
			$data['content_title'] = 'Merchant';
			$data['icon']          = 'user';
			$data['list']          =  ShippingCost::with(['country_detail','state_detail', 'user'])->where('merchant_id',Auth::user()->id)->orderBy('id', 'desc')->paginate(10);
			// dd($merchants);
			$data['defaultWallet'] = $defaultWallet = Wallet::where(['user_id' => auth()->user()->id, 'is_default' => 'Yes'])->first(['currency_id']); //new
			//check Decimal Thousand Money Format Preference
			$data['preference']   = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);
			return view('user_dashboard.ShippingCost.list', $data);
		}
        
        public function shippingcostadd()
		{
			$data['menu']     = 'shipping_cost';
			$data['sub_menu'] = 'shipping_cost';

			//pm_v2.3
			$data['activeCurrencies'] = $activeCurrencies = Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'code']);
			$data['defaultWallet']    = $defaultWallet    = Wallet::where(['user_id' => auth()->user()->id, 'is_default' => 'Yes'])->first(['currency_id']);

			return view('user_dashboard.ShippingCost.add', $data);
		}

		public function shippingcoststore(Request $request)
		{
			$rules = array(
			    'name' =>'required',
				'country' => 'required',
				'state'      => 'required',
				'city'          => 'required',
				'currency_id'          => 'required',
				'price'          => 'required',
			);

			$fieldNames = array(
			    'name' =>'Name',
				'country' => 'country Name',
				'state'      => 'State name',
				'city'          => 'City name',
				'currency_id'          => 'Currency',
				'price'          => 'Price',
			);

			$validator = Validator::make($request->all(), $rules);
			$validator->setAttributeNames($fieldNames);

			if ($validator->fails())
			{
				return back()->withErrors($validator)->withInput();
			}
			else
			{
			    
			 //   $chek_duplicate = DB::table('shipping_cost')->where('country',$request->country)->where('state',$request->state)->where('city',$request->city)->first();
			 //   if($chek_duplicate)
			 //   {
			 //       $this->helper->one_time_message('error', 'Already Exist');
				// 	return redirect()->back()->withInput();
			 //   }
			    
				try
				{
					\DB::beginTransaction();

					$filename = null;
					$picture  = $request->logo;
					if (isset($picture))
					{
						$dir = public_path("/user_dashboard/merchant/");
						$ext = $picture->getClientOriginalExtension();
						// dd($ext);
						$filename = time() . '.' . $ext;

						if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp')
						{
							$img = Image::make($picture->getRealPath());
							$img->resize(100, 80)->save($dir . '/' . $filename);
							$img->resize(70, 70)->save($dir . '/thumb/' . $filename);
						}
						else
						{
							$this->helper->one_time_message('error', 'Invalid Image Format!');
						}
					}

					$merchantGroup               = MerchantGroup::where(['is_default' => 'Yes'])->select('id', 'fee')->first();
					$shipping                    = new ShippingCost();
					$shipping->merchant_id           = Auth::user()->id;
					$shipping->currency_id       = $request->currency_id;
					$shipping->country     = $request->country;
					$shipping->state          = $request->state;
					$shipping->city              = $request->city;
					$shipping->price              = $request->price;
						$shipping->name              = $request->name;
					$shipping->carrier_id              = $request->carriers;
					$shipping->duration              = $request->duration;
					$shipping->min_amount              = $request->min_amount;
					$shipping->max_amount              = $request->max_amount;
					// dd($Merchant);
					$shipping->save();

					

					\DB::commit();
					$this->helper->one_time_message('success', __('Shipping Cost Created Successfully!'));
					return redirect('shipping_cost');
				}
				catch (\Exception $e)
				{
					\DB::rollBack();
					$this->helper->one_time_message('error', $e->getMessage());
					return redirect()->back();
				}
			}
		}

		public function shippingcostedit($id)
		{
			$data['menu']             = 'shipping_cost';
			$data['sub_menu']         = 'shipping_cost';
			$data['content_title']    = 'Merchant';
			$data['icon']             = 'user';
			$data['activeCurrencies'] = $activeCurrencies = Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'code']);
			$data['merchant']         = $merchant         = Merchant::with('currency:id,code')->find($id);
			
			if(isset($merchant))
			{
			    $defaultWallet    = Wallet::with(['currency:id,code'])->where(['user_id' => $merchant->user->id, 'is_default' => 'Yes'])->first(['currency_id']);
			}
			else
			{
			    $defaultWallet ="";
			}
			$data['defaultWallet']    = $defaultWallet  ; //new
			$data['activeCurrencies'] = $activeCurrencies = Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'code']);
			$data['details'] = ShippingCost::where('id',$id)->first();
           
			return view('user_dashboard.ShippingCost.edit', $data);
		}
        
        	public function shippingcostupdate(Request $request)
		{
			$rules = array(
				'country' => 'required',
				'state'      => 'required',
				'city'          => 'required',
				'currency_id'          => 'required',
				'price'          => 'required',
			);

			$fieldNames = array(
				'country' => 'country Name',
				'state'      => 'State name',
				'city'          => 'City name',
				'currency_id'          => 'Currency',
				'price'          => 'Price',
			);

			$validator = Validator::make($request->all(), $rules);
			$validator->setAttributeNames($fieldNames);

			if ($validator->fails())
			{
				return back()->withErrors($validator)->withInput();
			}
			else
			{
			    
			 //   $chek_duplicate = DB::table('shipping_cost')->where('country',$request->country)->where('state',$request->state)->where('city',$request->city)->where('id','!=',$request->id)->first();
			 //   if($chek_duplicate)
			 //   {
			 //       $this->helper->one_time_message('error', 'Already Exist');
				// 	return redirect()->back()->withInput();
			 //   }
			    
				try
				{
					\DB::beginTransaction();

					$filename = null;
					$picture  = $request->logo;
					if (isset($picture))
					{
						$dir = public_path("/user_dashboard/merchant/");
						$ext = $picture->getClientOriginalExtension();
						// dd($ext);
						$filename = time() . '.' . $ext;

						if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp')
						{
							$img = Image::make($picture->getRealPath());
							$img->resize(100, 80)->save($dir . '/' . $filename);
							$img->resize(70, 70)->save($dir . '/thumb/' . $filename);
						}
						else
						{
							$this->helper->one_time_message('error', 'Invalid Image Format!');
						}
					}

					$merchantGroup               = MerchantGroup::where(['is_default' => 'Yes'])->select('id', 'fee')->first();
					$shipping                    = ShippingCost::find($request->id);
					$shipping->merchant_id           = Auth::user()->id;
					$shipping->currency_id       = $request->currency_id;
					$shipping->country     = $request->country;
					$shipping->state          = $request->state;
					$shipping->city              = $request->city;
					$shipping->price              = $request->price;
					
					$shipping->name              = $request->name;
					$shipping->carrier_id              = $request->carriers;
					$shipping->duration              = $request->duration;
					$shipping->min_amount              = $request->min_amount;
					$shipping->max_amount              = $request->max_amount;
					// dd($Merchant);
					$shipping->save();

					

					\DB::commit();
					$this->helper->one_time_message('success', __('Shipping Cost Updated Successfully!'));
					return redirect('shipping_cost');
				}
				catch (\Exception $e)
				{
					\DB::rollBack();
					$this->helper->one_time_message('error', $e->getMessage());
					return redirect()->back();
				}
			}
		}

        
// 		public function shippingcostupdate(Request $request)
// 		{
// 			// dd($request->all());
// 			$rules = array(
// 				'business_name' => 'required|unique:merchants,business_name,' . $request->id,
// 				'site_url'      => 'required|url',
// 				'note'          => 'required',
// 				'logo'          => 'mimes:png,jpg,jpeg,gif,bmp',
// 			);

// 			$fieldNames = array(
// 				'business_name' => 'Business Name',
// 				'site_url'      => 'Site url',
// 				'note'          => 'Note',
// 				'logo'          => 'The file must be an image (png, jpg, jpeg, gif,bmp)',
// 			);

// 			$validator = Validator::make($request->all(), $rules);
// 			$validator->setAttributeNames($fieldNames);

// 			if ($validator->fails())
// 			{
// 				return back()->withErrors($validator)->withInput();
// 			}
// 			else
// 			{
// 				$picture  = $request->logo;
// 				$filename = null;

// 				try
// 				{
// 					\DB::beginTransaction();

// 					if (isset($picture))
// 					{
// 						$dir      = public_path("/user_dashboard/merchant/");
// 						$ext      = $picture->getClientOriginalExtension();
// 						$filename = time() . '.' . $ext;

// 						if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp')
// 						{
// 							$img = Image::make($picture->getRealPath());
// 							$img->resize(100, 80)->save($dir . '/' . $filename);
// 							$img->resize(70, 70)->save($dir . '/thumb/' . $filename);
// 						}
// 						else
// 						{
// 							$this->helper->one_time_message('error', 'Invalid Image Format!');
// 						}
// 					}
// 					$Merchant                = Merchant::find($request->id, ['id', 'currency_id', 'business_name', 'site_url', 'note', 'logo']);
// 					$Merchant->currency_id   = $request->currency_id; //2.3
// 					$Merchant->business_name = $request->business_name;
// 					$Merchant->site_url      = $request->site_url;
// 					$Merchant->note          = $request->note;
// 					if ($filename != null)
// 					{
// 						$Merchant->logo = $filename;
// 					}
// 					$Merchant->save();

// 					\DB::commit();
// 					$this->helper->one_time_message('success', __('Merchant Updated Successfully!'));
// 					return redirect('merchants');
// 				}
// 				catch (\Exception $e)
// 				{
// 					\DB::rollBack();
// 					$this->helper->one_time_message('error', $e->getMessage());
// 					return redirect('merchants');
// 				}
// 			}
// 		}
        
        
        
    public function states(Request $request)
    {
        $country = $request->country;
        $states = DB::table('states')->where('country_id',$country)->get();
        $data = '<option value="">Select State</option>';
        foreach($states as $state)
        {
           $data .= '<option value="'.$state->id.'">'.$state->name.'</option>';   
        }
        return $data;
    }
    
    public function citys(Request $request)
    {
        $state = $request->state;
        $citys = DB::table('city')->where('state_id',$state)->where('active',1)->get();
        $data = '<option value="">Select City</option>';
        foreach($citys as $city)
        {
           $data .= '<option value="'.$city->id.'">'.$city->city_name.'</option>';   
        }
        return $data;
    }


}
