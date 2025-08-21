<?php
namespace App\Http\Controllers;
use App\Models\Store;
use App\Models\Order;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use App\Http\Helpers\Common;
use App\Models\Country;
use App\Models\PaymentMethod;
use App\Models\CurrencyPaymentMethod;
use App\Models\FeesLimit;
use App\Models\Bank;
use App\Models\CryptoapiLog;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\PayoutSetting;
use CoinPayment;
use Hexters\CoinPayment\Entities\CointpaymentLogTrx;
use Omnipay\Omnipay;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use Validator;
use Session;
use DB;
use Mail;
use PHPMailer\PHPMailer\PHPMailer;
use App\Models\Admin;
use App\Models\CollectPayment;

class StoreController extends Controller
{
    public function __construct()
    {

        $this->helper  = new Common();
        
		$this->transaction_type_id = 28;

    }


    public function index(Request $request,$store=null)
    {
		    $request->session()->forget('customerData');
		    $request->session()->forget('orderData');
			$request->session()->forget('last_order_id');

		    $obj        = new Store();
			$storeData  = $obj::where('slug',$store)->get()->toArray();
			$store_exist= false;
			if(!empty($storeData)){
				$userData     = User::where('id',$storeData[0]['user_id'])->get()->toArray();
				$currencyData = Currency::where('id',$storeData[0]['currency_id'])->get()->toArray();
				
				if(!empty($currencyData)){
				$storeData[0]['currency_id']     = $currencyData[0]['id'];
				$storeData[0]['currency_name']   = $currencyData[0]['name'];
				$storeData[0]['currency_code']   = $currencyData[0]['code'];
				$storeData[0]['currency_symbol'] = $currencyData[0]['symbol'];
                				
				}else{
				$storeData[0]['currency_id']     = 1;
				$storeData[0]['currency_name']   = 'US Dollar';
				$storeData[0]['currency_code']   = 'USD';
				$storeData[0]['currency_symbol'] = '$';	
				}
				if(!empty($userData)){
				$store_exist= true;	
				}
               				
				
				
			}
            $data                = array();
            
           $data['title']    = $storeData[0]['name'];
           $data['description']    = $storeData[0]['description'];
           
           $data['images']    = ($storeData[0]['image']) ? url('public/uploads/store/').'/'.$storeData[0]['image'] :'';
          
			if($store_exist && !empty($storeData)){
			$user_id             = $storeData[0]['user_id'];
			$productData         = Product::where('userid',$user_id)->get()->toArray();
			$data['productData'] = $productData;
			Session::put('storeData', $storeData[0]);
            $data['share_url']  = url()->current();
            
            // 3-11-2020
            $ip = $request->ip();
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            $data['ip_datails'] = $ipdat;
            
            $data['packeging'] = DB::table('packeging')->where('active',1)->get(); 
            // 3-11-2020
            
            
            return view('store.index', $data);
			}else{
            return view('store.empty', $data);
            }


    }
	function vieworder(Request $request,$unique_id=false){
		    $request->session()->forget('cartData');
			$errormsg     = "Order not exits any more";
            if($unique_id){		    
			 $obj          = new Order();
			 $merchantObj  = new Merchant();
			 $storeObj     = new Store();
			 $orderData    = $obj::where('unique_id',$unique_id)->get()->toArray();
			 
			 $shipping = DB::table('shipping_cost')->where('country',$orderData[0]['customer_country'])->where('state',$orderData[0]['customer_state'])->where('city',$orderData[0]['customer_city'])->first();
    		   if($shipping)
    		   {
    		       $shipping_cost =$shipping->price;
    		   }
    		   else
    		   {
    		       $shipping_cost =0;
    		       
    		   }
    					   
    
    
    
		     if(!Session::has('storeData')){
				$currency_id          = $orderData[0]['currency_id'];		
				$newstoreData         = $storeObj::where('id',$orderData[0]['store_id'])->get()->toArray();
				$storeData            = $newstoreData[0];
				$currencyData         = Currency::where('id',$currency_id)->get()->toArray();
				if(!empty($currencyData)){
							$storeData['currency_id']     = $currencyData[0]['id'];
							$storeData['currency_name']   = $currencyData[0]['name'];
							$storeData['currency_code']   = $currencyData[0]['code'];
							$storeData['currency_symbol'] = $currencyData[0]['symbol'];
											
							}else{
							$storeData['currency_id']     = 1;
							$storeData['currency_name']   = 'US Dollar';
							$storeData['currency_code']   = 'USD';
							$storeData['currency_symbol'] = '$';	
				}
				Session::put('storeData',$storeData);
				return redirect(url('/pay/'.strtolower($unique_id)));
			}else{
				$storeData        = Session::get('storeData');
			}
					
			 
			 
			 
			 
			
			 
			 $user_id         = $storeData['user_id'];
			 $subtotal        = 0;
			 $tax             = 0;
			 $total           = 0;
			 $merchantData    = $merchantObj::where('user_id',$user_id)->get()->toArray();
			 $currency_id     = $storeData['currency_id'];
			 
			 $order_id        = $orderData[0]['id'];
			 $productIdArray  = json_decode($orderData[0]['products'],true);
			 $productData     = array();
					  
			 if(!empty($productIdArray)){
						  foreach($productIdArray as $key=>$val){
							  $index  = count($productData);
							  $pData  = Product::where('id',$val['product_id'])->get()->toArray();
							  if(!empty($pData)){
								$productData[$index]['product_id']    = $val['product_id'];  
								$productData[$index]['product_name']  = $pData[0]['name'];  
								$productData[$index]['product_price'] = $pData[0]['price'];  
								$productData[$index]['product_qty']   = $val['qty']; 
                                $subtotal                             = $subtotal+($pData[0]['price']*$val['qty']);                              								
							  }
							  
						  }
			}
			    if(!empty($productData)){
						  $updateData['id']                   = $order_id;
						  $updateData['subtotal']             = $subtotal;
						  $updateData['tax']                  = $tax;
						  $updateData['total_amount']         = $subtotal+$tax+$shipping_cost;
						  $updateData['currency_id']          = $currency_id;
						  $updateData['shipping_cost']          = $shipping_cost;
						 
						  $obj->addeditdata($updateData);
						  $orderData[0]['productData']        = $productData;
						  $data['orderData']                  = $orderData[0];
						  $data['store_name']                 = $storeData['name'];
						  $data['subtotal']                   = $subtotal;
						  $data['tax']                        = $tax;
						  $data['total_amount']               = $subtotal+$tax;
						  $data['unique_id']                  = $unique_id;
						  $data['merchant_details']           =$merchantData;
						  $data['user_details']           =User::where('id',$user_id)->first();
						  
						  $data['store_details']           =Store::with('country_details','state_details','city_details')->where('user_id',$user_id)->first();
						  
						  //echo "<pre>";
						  //print_r($merchantData);
						  //die;
						  return view('store.vieworder', $data);
						  
		        }else{
						  redirect('/store/'.$storedata['name'])->with('errormsg',$errormsg);
					  }
					  
					 
				 
			 }else{
				 
				 redirect('/store/'.$storedata['name'])->with('errormsg',$errormsg);
          
			 }
	}
	
	public function pay(Request $request,$order_unique_id=false){
	    
	   
		     $request->session()->forget('cartData');
			 $obj                      = new Order();
			 $storeObj                 = new Store();
			 $errormsg                 = "Order not exits any more";
			 if($order_unique_id){
				 $orderData            = $obj::where('unique_id',$order_unique_id)->get()->toArray();
				 if(!empty($orderData)){
					 $order_id             = $orderData[0]['id'];
					 $unique_id            = $orderData[0]['unique_id'];
					 $currency_id          = $orderData[0]['currency_id'];
					if(!Session::has('storeData')){
						
					 $newstoreData         = $storeObj::where('id',$orderData[0]['store_id'])->get()->toArray();
					 $storeData            = $newstoreData[0];
					 $currencyData         = Currency::where('id',$currency_id)->get()->toArray();
				     if(!empty($currencyData)){
						$storeData['currency_id']     = $currencyData[0]['id'];
						$storeData['currency_name']   = $currencyData[0]['name'];
						$storeData['currency_code']   = $currencyData[0]['code'];
						$storeData['currency_symbol'] = $currencyData[0]['symbol'];
										
						}else{
						$storeData['currency_id']     = 1;
						$storeData['currency_name']   = 'US Dollar';
						$storeData['currency_code']   = 'USD';
						$storeData['currency_symbol'] = '$';	
					}
					 Session::put('storeData',$storeData);
					 return redirect(url('/pay/'.strtolower($order_unique_id)));
					}else{
						$storeData        = Session::get('storeData');
					}
					
					$storeBankDataQuery   = PayoutSetting::where('user_id',$orderData[0]['store_user_id'])
				                        ->where('type',6)
										->whereNotNull('bank_name')
										->whereNotNull('account_number');
                    $storeBankDataFirst     = $storeBankDataQuery->first();
				    $storeBankData          = array();
				    if(!empty($storeBankDataFirst)){
				     $storeBankData       = $storeBankDataFirst->toArray();
				    }
					$data['storeBankData']=$storeBankData;
					$user_id              = $storeData['user_id'];
					$subtotal             = 0;
					$tax                  = 0;
					$total                = 0;
					$paymentMethod        = $this->getpaymentmethods($currency_id,$this->transaction_type_id);
			        $data['paymentMethod']= $paymentMethod;
				    $productIdArray  = json_decode($orderData[0]['products'],true);
					$productData     = array();
					if(!empty($productIdArray)){
						  foreach($productIdArray as $key=>$val){
							  $index  = count($productData);
							  $pData  = Product::where('id',$val['product_id'])->get()->toArray();
							  if(!empty($pData)){
								$productData[$index]['product_id']    = $val['product_id'];  
								$productData[$index]['product_name']  = $pData[0]['name'];  
								$productData[$index]['product_price'] = $pData[0]['price'];  
								$productData[$index]['product_qty']   = $val['qty']; 
                                $subtotal                             = $subtotal+($pData[0]['price']*$val['qty']);                              								
							  }
							  
						  }
					  }
					  if(!empty($productData)){
						  
						  $orderData[0]['productData']        = $productData;
						  $data['orderData']                  = $orderData[0];
						  $data['store_name']                 = $storeData['name'];
						  $data['subtotal']                   = $subtotal;
						  $data['tax']                        = $tax;
						  $data['total_amount']               = $subtotal+$tax;
						  $data['unique_id']                  = $unique_id;
						  $data['currency_id']                = $currency_id;
						 
						  
						  return view('store.pay', $data);
						  
					  }else{
						  redirect('/store/'.$storedata['name'])->with('errormsg',$errormsg);
					  }
					  
					 
				 }else{
				 redirect('/store/'.$storedata['name'])->with('errormsg',$errormsg);
           
				 }
			 }else{
				 
				 redirect('/store/'.$storedata['name'])->with('errormsg',$errormsg);
          
			 }
	}
	
	public function selectpaymentmethod(Request $request){
		            $fees                 = 0;
					$charge_percentage    = 0;
					$charge_fixed         = 0;
					
					$is_make_payment      = true;
					
					$feeObj               = new FeesLimit();
					$pMethodObj           = new PaymentMethod();
					$orderObj             = new Order();
					
					$orderData            = $orderObj::where('id',$request['order_id'])->get()->toArray();
					
					$userData             = User::where('id',$orderData[0]['store_user_id'])->get()->toArray();
					
					
					$storeBankDataQuery   = PayoutSetting::where('user_id',$orderData[0]['store_user_id'])
				                        ->where('type',6)
										->whereNotNull('bank_name')
										->whereNotNull('account_number');
                    $storeBankDataFirst     = $storeBankDataQuery->get();
				    $storeBankData          = array();
				    if(!empty($storeBankDataFirst)){
				     $storeBankData       = $storeBankDataFirst->toArray();
				    }
                    $amount               = $orderData[0]['total_amount'];
					$query                = $feeObj::where('currency_id',$orderData[0]['currency_id']);
					$query->where('transaction_type_id',$this->transaction_type_id);
					$query->where('payment_method_id',$request['payment_method_id']);				
		            $feeData              = $query->get()->toArray();
					
					$feeLine      = '';
					$feeCharge    = 0;
					
					if(!empty($feeData)){
						$charge_percentage = $feeData[0]['charge_percentage'];
					    $charge_fixed      = $feeData[0]['charge_fixed'];
						if($feeData[0]['charge_percentage']>0){
							$feeCharge = $feeCharge+($amount*($feeData[0]['charge_percentage']/100));
							$feeLine  .= number_format((float)$feeData[0]['charge_percentage'], 2, '.', '').'%';
						}
						if($feeData[0]['charge_fixed']>0){
							$feeCharge = $feeCharge+$feeData[0]['charge_fixed'];
							$feeLine  .= '+ RM '.number_format((float)$feeData[0]['charge_fixed'], 2, '.', '');
						}
						
					}
					if($feeLine!=''){
						$feeLine      = $feeLine;
					}
					$paymentMethodData = $pMethodObj::where('id',$request['payment_method_id'])->get()->toArray();
					if(!empty($paymentMethodData)){
					$payment_method_name = $paymentMethodData[0]['name'];
    					
					}else{
					$payment_method_name = '' ;
                    $is_make_payment		= false;			
					}
					
					$totalPaidAmount        = $amount+$feeCharge;
					if($totalPaidAmount<=0){
					$is_make_payment		= false;	
					}
					if($is_make_payment){
					$updateData['id']                = $request['order_id'];	
					$updateData['tax']               = $feeCharge;
					$updateData['paid_amount']       = $totalPaidAmount;
					$updateData['charge_percentage'] = $charge_percentage;
					$updateData['charge_fixed']      = $charge_fixed;
					$updateData['payment_method_id'] = $request['payment_method_id'];
					$orderObj->addeditdata($updateData);
					}
					
					$data['payment_method']          = $payment_method_name;
					$data['feeCharge']               = $feeCharge;
					$data['subtotal']                = $amount;
					$data['paid_amount']             = $totalPaidAmount;
					$data['feeLine']                 = $feeLine;
					$data['is_make_payment']         = $is_make_payment;
					$data['order_id']                = $request['order_id'];
					$data['payment_method_id']       = $request['payment_method_id'];
					$data['storeBankData']           = $storeBankData;
					$data['share_phone']             = $userData[0]['formattedPhone'];
					$data['share_msg']               = 'Hi i alredy made the payment,Please check bank account Order Form\n '.url('/vieworder/'.strtolower($orderData[0]['unique_id']));
					$data['currency_id']             = $orderData[0]['currency_id'];
					$data['shipping_cost']               = $orderData[0]['shipping_cost'];
					echo view('store.paymentresponse', $data);
					
					
					
	}
	public function paymentmethodprocess(Request $request){
		    $payment_name                       = '';
			$currency_code                      = 'USD';
		    if($request['order_id']){
				    $orderData                  = $request->all();
					$orderTableData             = Order::find($request['order_id']);
					//echo $orderTableData->unique_id;die;
					$paymentMethodData          = PaymentMethod::where('id',$request['payment_method'])->get()->toArray();
					$currencyData               = Currency::find($request['currency_id'], ['id', 'code']);
					if(isset($paymentMethodData[0]['name']) && $paymentMethodData[0]['name']!=''){
					$payment_name               = strtolower($paymentMethodData[0]['name']);
					}
					if(isset($currencyData->code) && $currencyData->code!=''){
					$currency_code              = $currencyData->code;	
					}
					
					$orderData['payment_name']  = $payment_name;
					$orderData['currency_code'] = $currency_code;
					/* Put all order Data in Session */
					
					Session::put('orderData',$orderData);
					
                    $currencyPaymentMethodQuery = CurrencyPaymentMethod::where('currency_id',$request['currency_id']);
					$currencyPaymentMethodQuery->where('method_id',$request['payment_method']);
					$currencyPaymentMethodQuery->where('activated_for', 'like', "%deposit%");
					$cpmData                    = $currencyPaymentMethodQuery->get()->toArray();
					if(isset($cpmData[0]['method_data'])){
                        $methodData             = json_decode($cpmData[0]['method_data']);
						$orderData['methodData']= $methodData;
						Session::put('orderData',$orderData);
						if (empty($methodData))
					   {
						   $this->helper->one_time_message('error', __('Payment gateway credentials not found!'));
						   return back();
                       }
					   if($payment_name=='paypal'){
						   
							$apiContext = $this->paypalSetup($methodData->client_id, $methodData->client_secret, $methodData->mode);
                            $payer        = new Payer();
                            $payer->setPaymentMethod('paypal');
                            $amount       = new Amount();
                            $amount->setTotal(round($request['paid_amount'], 3));
                            $amount->setCurrency($currency_code);
                            $transaction = new \PayPal\Api\Transaction();
                            $transaction->setAmount($amount);
                            $redirectUrls = new RedirectUrls();
							$successUrl   = route('paypalsuccess');
							$cancelUrl    = route('paypalcancel');
                            $redirectUrls->setReturnUrl($successUrl)
                            ->setCancelUrl($cancelUrl);
							$errorUrl     = url('/pay/'.strtolower($orderTableData->unique_id));
                            $payment = new Payment();
                            $payment->setIntent('sale')
                             ->setPayer($payer)
                             ->setTransactions(array($transaction))
                             ->setRedirectUrls($redirectUrls);
                            try {

                             $payment->create($apiContext);
                             return redirect()->to($payment->getApprovalLink());

                            }catch (PayPalConnectionException $ex){
							  $errorData = 	json_decode($ex->getData(),true);
							 
                              $this->helper->one_time_message('error', $errorData['message']);
                              return redirect($errorUrl);
                            }

						   
					   }else if ($payment_name == 'stripe'){
                           $publishable = $methodData->publishable_key;
                           Session::put('publishable', $publishable);
                           return redirect(route('stripecheckout'));
                       }else if ($payment_name=='systempay'){
						  return redirect(route('systempaycheckout'));
					   }
					   
					   
					   
					  
					}
					
			}
	}
	
	
	
	/************* Paypal**********************/
	public function paypalSetup()

    {

        $numarr = func_num_args();
        if ($numarr > 0)
        {
            $clientID   = func_get_arg(0);
            $secret     = func_get_arg(1);
            $mode       = func_get_arg(2);
            $apicontext = new ApiContext(new OAuthTokenCredential($clientID, $secret));
            $apicontext->setConfig([
                'mode' => $mode,
            ]);
        }else
        {
            $credentials = Setting::where(['type' => 'PayPal'])->get();
            $clientID    = $credentials[0]->value;
            $secret      = $credentials[1]->value;
            $apicontext  = new ApiContext(new OAuthTokenCredential($clientID, $secret));
            $apicontext->setConfig([
                'mode' => $credentials[3]->value,
            ]);
        }

        return $apicontext;

    }
	public function paypalsuccess(Request $request){
		            $orderSessionData   = Session::get('orderData');
		            $response_message   = "Total amount ".$orderSessionData['currency_code']." ".number_format($orderSessionData['paid_amount'],2).' has been paid successfully';
				    $redirect_message   = $response_message;
					$is_payment_success = true;
					$this->updateorderafterpayment($is_payment_success,$response_message);
					$this->updatetransactiononsuccess();
					$redirect_url       = route('paymentmethodresponsesuccess');
					return redirect($redirect_url)->with('response_message',$redirect_message);
                    
	}
	public function paypalcancel(Request $request){
		            $orderSessionData   = Session::get('orderData');
		            $is_payment_success = false;
		            $response_message   = "Payment cancelled or failed";
					$redirect_message   = "Sorry ! Your last payment of ".$orderSessionData['currency_code']." ".number_format($orderSessionData['paid_amount'],2)." has been failed";
                    $this->updateorderafterpayment($is_payment_success,$response_message);
					$redirect_url       = route('paymentmethodresponsecancel');
					return redirect($redirect_url)->with('response_message',$redirect_message);
		            
	}
	/******************** Stripe *************************/
	
	
	
	public function stripecheckout(Request $request){
		    $orderData     = Session::get('orderData');
			if(!empty($orderData)){
		    $data = array();
			$data['publishable']       = $orderData['methodData']->publishable_key;
			$data['orderdata'] = $orderData;
		    return view('store.stripe', $data);
			}else{
			echo "Something went wrong!Please try again later";	
			}
	}
	public function storestripe(Request $request){
		            
		            $validation = Validator::make($request->all(), [
                   'stripeToken' => 'required',
					]);
					if ($validation->fails())
					{
						return redirect()->back()->withErrors($validation->errors());
					}
					$orderSessionData   = Session::get('orderData');
					
					$storeData          = Session::get('storeData');
					
					$is_payment_success = false;
					$redirect_url       = url('/store/'.$storeData['slug']);
					$response_message    = "Some error occur while payment using stripe";
					if(!empty($orderSessionData) && isset($request['stripeToken'])){
						
						
						$secret_key      = $orderSessionData['methodData']->secret_key;
						$publishable_key = $orderSessionData['methodData']->publishable_key;
						$gateway         = Omnipay::create('Stripe');
						$gateway->setApiKey($secret_key);
						$response = $gateway->purchase([
							'amount'   => number_format((float) $orderSessionData['paid_amount'], 2, '.', ''),
							'currency' => $orderSessionData['currency_code'],
							'token'    => $request->stripeToken,
						])->send();
						
						if ($response->isSuccessful())
                        {
				// 		 $response_message = "Payment of Amount ".$orderSessionData['currency_code']." ".number_format($orderSessionData['paid_amount'],2).' Completed Successfully';
						  $response_message   = "Total amount ".$orderSessionData['currency_code']." ".number_format($orderSessionData['paid_amount'],2).' has been paid successfully';
						 $redirect_message   = $response_message;
						 $is_payment_success = true;
						 
						 $this->updatetransactiononsuccess();
						 
						 
						 $redirect_url       = route('paymentmethodresponsesuccess');
						}else{
                         $response_message   = $response->getMessage();
						 $redirect_message   = "Sorry ! Your last payment of ".$orderSessionData['currency_code']." ".number_format($orderSessionData['paid_amount'],2)." has been failed";
                         $redirect_url       = route('paymentmethodresponsecancel');
						}
						
						
				    }
					
					$this->updateorderafterpayment($is_payment_success,$response_message);
					return redirect($redirect_url)->with('response_message',$redirect_message);
    }
	/******************** Stripe *************************/
	
	
	
	public function systempaycheckout(Request $request){
		    $orderSessionData          = Session::get('orderData');
			$merchantData              = $orderSessionData['methodData'];
			$orderObj                  = new Order();
			$orderData                 = $orderObj::where('id',$orderSessionData['order_id'])->get()->toArray();
			
		    if(!empty($orderSessionData) && !empty($orderData)){
				
				$amount                = $orderSessionData['paid_amount'];
				$currency_code         = $orderSessionData['currency_code'];
				$systempay_merchant_id = $merchantData->merchant_id; 
				$systempay_password    = $merchantData->password;
				$systempay_api_url     = $merchantData->api_url;
				$systempay_public_key  = $merchantData->public_key;
				$systempay_hash        = $merchantData->hash;
				$params['username']    = $systempay_merchant_id;
				$params['password']    = $systempay_password;
				$params['publicKey']   = $systempay_public_key;
				$params['hashKey']     = $systempay_hash;
				$params['endpoint']    = $systempay_api_url;
			
				// $clSystemPay           = new  Lyra \ Client ($params);
				$clSystemPay->setDefaultUsername($systempay_merchant_id);
				$clSystemPay->setDefaultPassword($systempay_password);
				$clSystemPay->setDefaultEndpoint($systempay_api_url);
				$clSystemPay->setDefaultPublicKey($systempay_public_key);
				$clSystemPay->setDefaultSHA256Key($systempay_hash);
				$clSystemPay->setSHA256Key($systempay_hash);
			
		        $url                   = '/V4/Charge/CreatePayment';
		        $amount                = $amount*100;
		        $store = array("amount" => $amount, 
		         "currency" => $currency_code, 
		         "orderId" => $orderData[0]['unique_id']
		        );
		
                $response              = $clSystemPay->post($url, $store);
		
				if(strtolower($response['status'])=='success'){
				 $data['endpoints'] = 'https://api.payzen.eu';
				 $data['publickey'] = $systempay_public_key;
				 $data['formToken'] = $response['answer']['formToken'];
				 return view('store.systempay', $data);
				}else{
				 $this->helper->one_time_message('error', $response['answer']['errorMessage']);
				 return back();
				}
			 
		    
		   }else{
			 $this->helper->one_time_message('error', 'Something went wrong ! Please try again later');
             return back();
		   }	
			
	}
	public function storesystempay(Request $request){
		          //  $client                    = new  Lyra \ Client ();
					$orderSessionData          = Session::get('orderData');
			        $merchantData              = $orderSessionData['methodData'];
					$orderObj                  = new Order();
					$storeData                 = Session::get('storeData');
			        $orderData                 = $orderObj::where('id',$orderSessionData['order_id'])->get()->toArray();
					$hashKey                   = $merchantData->hash;
					$is_payment_success        = false;
					$redirect_url              = url('/store/'.$storeData['slug']);
					$response_message          = "Some error occur while payment using stripe";
				    $formAnswer                = $client->getParsedFormAnswer();
				    $orderUniqueID             = $formAnswer['kr-answer']['orderDetails']['orderId'];
					if (empty($_POST)) {
			        $this->helper->one_time_message('error', __('Something went wrong ! Please try again later.'));
                    return back();
            	    }
					if(!$client->checkHash($hashKey)){
					$this->helper->one_time_message('error', __('Something went wrong ! Please try again later.'));
					throw new Exception("invalid signature");
					return back();
				    }
					if(strtolower($formAnswer['kr-answer']['orderStatus'])=='paid'){
					  $is_payment_success  = true;
				// 	  $redirect_message    = "Payment of Amount ".$orderSessionData['currency_code']." ".$orderSessionData['paid_amount'].' Completed Successfully';
					   $redirect_message   = "Total amount ".$orderSessionData['currency_code']." ".number_format($orderSessionData['paid_amount'],2).' has been paid successfully';
					  $response_message    = $redirect_message;
					  $redirect_url        = route('paymentmethodresponsesuccess');
					  $this->updatetransactiononsuccess();
					  $this->updateorderafterpayment($is_payment_success,$response_message);
					  return redirect($redirect_url)->with('response_message',$redirect_message);
					}else{
					  $redirect_message    =  "Something went wrong ! Please try again later. ";
					  $response_message    =  $redirect_message;
					  $this->updateorderafterpayment($is_payment_success,$response_message);
					  $this->helper->one_time_message('error', __($redirect_message));
                      return back();	
					}
					
					
    }
	/************ Common function for update transaction **********/
	public function updatetransactiononsuccess(){
		           $orderSessionData   = Session::get('orderData');
				   $storeData          = Session::get('storeData');
				   $merObj             = new Merchant();
				   $ordObj             = new Order();
				   $transObj           = new Transaction();
				   $merchantData       = $merObj::where('user_id',$storeData['user_id'])->get()->toArray();
				   $orderData          = $ordObj::where('id',$orderSessionData['order_id'])->get()->toArray();
				   if(!empty($orderData)){
					   if(isset($merchantData[0]['id'])){
					   $insertData['merchant_id'] = $merchantData[0]['id'];   
					   }
					   //currency_id 
					   $insertData['end_user_id']      = $storeData['user_id'];
					   $insertData['currency_id']      = $orderData[0]['currency_id'];
					   $insertData['payment_method_id']= $orderData[0]['payment_method_id'];
					   $insertData['uuid']             = $orderData[0]['unique_id'];
					   $insertData['transaction_reference_id']= $orderData[0]['id'];
					   $insertData['transaction_type_id']= 28;
					   $insertData['user_type']        = 'unregistered';
					   $insertData['phone']            = $orderData[0]['customer_phone'];
					   $insertData['email']            = $orderData[0]['customer_email'];
					   $insertData['subtotal']         = $orderData[0]['total_amount'];
					   $insertData['percentage']       = 0;
					   if($orderData[0]['charge_percentage']>0){
						$insertData['percentage']       = $orderData[0]['charge_percentage'];   
					   }
					   $insertData['charge_percentage']= $orderData[0]['charge_percentage'];
					   $insertData['charge_fixed']     = $orderData[0]['charge_fixed'];
					   $insertData['total']            = $orderData[0]['paid_amount'];
					   $insertData['note']             = 'Store Order';
					   $insertData['status']           = 'Success';
					   $insertData['created_at']       = date('Y-m-d H:i:s');
					   $insertData['updated_at']       = date('Y-m-d H:i:s');
					   $trans_id                       = $transObj->addeditdata($insertData);
					   return $trans_id;
					   
				   }
				   return true;
				   
	}
	/************ Common function for update order **********/
	public function updateorderafterpayment($status,$payment_response_msg){
		            $orderSessionData          = Session::get('orderData');
					$orderObj                  = new Order();
					if($status){
					$updateOrderData['status'] = 'success';
					}else{
					$updateOrderData['status'] = 'failed';	
					}
					$updateOrderData['payment_response'] = $payment_response_msg;
					$updateOrderData['updated_at']       = date('Y-m-d H:i:s');
					$updateOrderData['id']               = $orderSessionData['order_id'];
					Session::put('last_order_id',$orderSessionData['order_id']);
					$orderObj->addeditdata($updateOrderData);
					return true;
	}
	/************ Common function for sending response of payment **********/
	
	public function paymentmethodresponsesuccess(Request $request){
		            $request->session()->forget('orderData');
					$orderObj         = new Order();
					$currencyObj      = new Currency();
		            $customerData     = Session::get('customerData');
					$storeData        = Session::get('storeData');
					$response_message = Session::get('response_message');
					$last_order_id    = Session::get('last_order_id');
					if(isset($last_order_id)){
					     // 3-11-2020
					   $getdatabyorder_id = DB::table('orders')->where('id',$last_order_id)->first();
					   if(isset($getdatabyorder_id))
					   {
					       $msg = 'Your order # '. $last_order_id. 'has been placed successfully';
					       $subject = 'Order Notifications';
					       $email = $getdatabyorder_id->customer_email;
					       
					       $mobile = $getdatabyorder_id->customer_phone_prefix.$getdatabyorder_id->customer_phone;
					       $smsmsg= 'Your%20order%20'.$last_order_id.'%20has%20been%20placed%20successfully';
					     $this->helper->sendemailnotification($email,$subject,$msg);
		                 $this->helper->sendsms($mobile,$smsmsg);
					   }
                        // 3-11-2020

					  



						 $orderDataQuery        = $orderObj::where('id',$last_order_id)->get();
						 $orderData             = $orderDataQuery->toArray();
						 $finalOrderData        = $orderData[0]; 
						 
						 
						 $feeLine      = '';
										
						 if(!empty($finalOrderData)){
							$charge_percentage = $finalOrderData['charge_percentage'];
							$charge_fixed      = $finalOrderData['charge_fixed'];
							if($finalOrderData['charge_percentage']>0){
								$feeLine  .= number_format((float)$finalOrderData['charge_percentage'], 2, '.', '').'%';
							}
							if($finalOrderData['charge_fixed']>0){
								$feeLine  .= '+'.$storeData['currency_symbol'].number_format((float)$finalOrderData['charge_fixed'], 2, '.', '');
							}
							
						 }
						 $currencyData          = $currencyObj::where('id',$finalOrderData['currency_id'])->get()->toArray();
						 // print_r($finalOrderData);
						 
						 
						 $finalOrderData['products'] = json_decode($finalOrderData['products'],true);
						 $data['feeLine']       = $feeLine;
						 $data['orderData']     = $finalOrderData;
						 $data['store_name']    = $storeData['name'];
						 $data['store_slug']    = $storeData['slug'];
						 $data['show_message']  = "Your last order have been completed successfully.Please find order summary as follow";
						 $data['customer_name'] = $customerData['customer_name'];
						 $data['success']       = true;
						 return view('store.ordercomplete', $data);
						
					}else{
						return redirect('/store/'.$storeData['slug']);
					}
			
		            
	}
	
	
	public function paymentmethodresponsesuccessold(Request $request){
	    
	                
		            $request->session()->forget('orderData');
		            
		            $orderObj         = new Order();
					$currencyObj      = new Currency();
					
		            $customerData     = Session::get('customerData');
					$storeData        = Session::get('storeData');
					$response_message = Session::get('response_message');
					$last_order_id    = Session::get('last_order_id');
					if(isset($last_order_id)){
					   // 3-11-2020
					   $getdatabyorder_id = DB::table('orders')->where('id',$last_order_id)->first();
					   if(isset($getdatabyorder_id))
					   {
					       $msg = 'Your order # '. $last_order_id. 'has been placed successfully';
					       $subject = 'Order Notifications';
					       $email = $getdatabyorder_id->customer_email;
					       
					       $mobile = $getdatabyorder_id->customer_phone_prefix.$getdatabyorder_id->customer_phone;
					       $smsmsg= 'Your%20order%20'.$last_order_id.'%20has%20been%20placed%20successfully';
					     $this->helper->sendemailnotification($email,$subject,$msg);
		                 $this->helper->sendsms($mobile,$smsmsg);
					   }
                        // 3-11-2020
                        
                         $orderDataQuery        = $orderObj::where('id',$last_order_id)->get();
						 $orderData             = $orderDataQuery->toArray();
						 $finalOrderData        = $orderData[0]; 
						 
						 
						 $feeLine      = '';
										
						 if(!empty($finalOrderData)){
							$charge_percentage = $finalOrderData['charge_percentage'];
							$charge_fixed      = $finalOrderData['charge_fixed'];
							if($finalOrderData['charge_percentage']>0){
								$feeLine  .= number_format((float)$finalOrderData['charge_percentage'], 2, '.', '').'%';
							}
							if($finalOrderData['charge_fixed']>0){
								$feeLine  .= '+'.$storeData['currency_symbol'].number_format((float)$finalOrderData['charge_fixed'], 2, '.', '');
							}
							
						 }
						 $currencyData          = $currencyObj::where('id',$finalOrderData['currency_id'])->get()->toArray();
						 
		                $finalOrderData['products'] = json_decode($finalOrderData['products'],true);
		                 $data['feeLine']       = $feeLine;
						 $data['orderData']     = $finalOrderData;
						 $data['orderData']     = $finalOrderData;
						 $data['store_name']    = $storeData['name'];
						 $data['store_slug']    = $storeData['slug'];
						 $data['show_message']  = "Your last order have been completed successfully.Please find order summary as follow";
						 
						 $data['customer_name'] = $customerData['customer_name'];
						 $data['success']       = true;
						 return view('store.ordercomplete', $data);
						
					}else{
						return redirect('/store/'.$storeData['slug']);
					}
			
		            
	}
	public function paymentmethodresponsecancel(Request $request){
		            $request->session()->forget('orderData');
		            $customerData     = Session::get('customerData');
					$storeData        = Session::get('storeData');
					$response_message = Session::get('response_message');
					$last_order_id    = Session::get('last_order_id');
					if(isset($last_order_id)){
						 $data['customer_name'] = $customerData['customer_name'];
						 $data['success']       = false;
						 return view('store.ordercomplete', $data);
					}else{
						return redirect('/store/'.$storeData['slug']);
					}
		            
                   
								
		
	}
	/******************* For getting active payment gateway ******/
	public function getpaymentmethods($currency_id,$transaction_type_id,$activated_for='deposit'){
		    $feesLimits = FeesLimit::with([

                       'currency'       => function ($query){
                            $query->where(['status' => 'Active']);

                        },

                      'payment_method' => function ($q)
                       {
                        $q->where(['status' => 'Active']);

                    },

            ])->where(['transaction_type_id' => $transaction_type_id, 'has_transaction' => 'Yes', 'currency_id' => $currency_id])
            ->get(['payment_method_id']);
			$currencyPaymentMethods          = CurrencyPaymentMethod::where('currency_id', $currency_id)->where('activated_for', 'like', "%".$activated_for."%")->get(['method_id']);
			$query  = CurrencyPaymentMethod::where('currency_id', $currency_id)->where('activated_for', 'like', "%".$activated_for."%")->toSql();
			//echo $query;die;
           return $this->currencyPaymentMethodFeesLimitCurrencies($feesLimits,$currencyPaymentMethods);
	}
	
    public function currencyPaymentMethodFeesLimitCurrencies($feesLimits, $currencyPaymentMethods)

    {

        $selectedCurrencies = [];
		$pObj               = new PaymentMethod();
		
		foreach ($currencyPaymentMethods as $currencyPaymentMethod)

            {
				$paymentData = $pObj::where('id',$currencyPaymentMethod->method_id)->get()->toArray();
				
				
                if(!empty($paymentData)){
                $selectedCurrencies[$currencyPaymentMethod->method_id]['id']   = $paymentData[0]['id'];

                $selectedCurrencies[$currencyPaymentMethod->method_id]['name'] = $paymentData[0]['name'];
				}

                

         }
        /*
		foreach ($feesLimits as $feesLimit)
        {
            foreach ($currencyPaymentMethods as $currencyPaymentMethod)

            {
                if ($feesLimit->payment_method_id == $currencyPaymentMethod->method_id)
                {

                    $selectedCurrencies[$feesLimit->payment_method_id]['id']   = $feesLimit->payment_method_id;

                    $selectedCurrencies[$feesLimit->payment_method_id]['name'] = $feesLimit->payment_method->name;

                }

            }

        }
		*/

        return $selectedCurrencies;

    }

    /*****************************************************************/

        // 3-11-2020
        
        
        public function getproductattributes(Request $request)
        {
           $product_id = $request->pid;
            if($product_id)
            {
                $product_details =DB::table('product_attributes')->where('product_id',$product_id)->get();
                
                if(isset($product_details) && count($product_details))
                 {
                     $final = '';
                     $str ='';
                     foreach($product_details as $attr)
                     {
                         $Attrdetls = DB::table('attributes')->where('id',$attr->attributes)->first();
                         
                         if($Attrdetls)
                         {
                             $str .='<div class="col-md-12">';
                            $str .= '<div class="form-group">';
                            $str .=	'<label>'.$Attrdetls->name.'</label>';
                            $str .=	'<select name="attributes_'.$attr->id.'" id="attribute_values" class="form-control">';
                                if($attr->attributes_values)
                                {
                                    $valuesarray = json_decode($attr->attributes_values,true);
                                    $optionvalue ='';
                                    foreach($valuesarray as $val)
                                    {
                                        $Attrvalues = DB::table('attribute_values')->where('id',$val)->first();
                                        if($Attrvalues)
                                        {
                                            $optionvalue .= '<option value="'.$Attrvalues->id.'">'.$Attrvalues->value.'</option>';
                                        }
                                    }
                                }
                                $str .=$optionvalue;
                         }
                         
                                
                            
                            $str .=	'</select>';
                         $str .='</div>';
                         $str .='</div>';
                     }
                     
                     return $str;
                 }
                else
                {
                    return false;
                }
            }
        }
        // 3-11-2020
        
        public function invoice($id)
        {
            $order = Order::where('id', $id)->where('status', 'success')->where('payment_response', 'success')->first();
            $user = User::where('id', $order->store_user_id)->first();
            $store = Store::where('id', $order->store_id)->first();
            $country = Country::where('id', $store->country)->first();
            $currency = Currency::where('id', $store->currency_id)->first();
            $paidcurrency = Currency::where('id', $order->paid_currency_id)->first();
            $transaction = Transaction::where('transaction_reference_id', $order->unique_id)->first();
            return view('store.invoice', compact('order', 'user', 'store', 'country', 'currency', 'paidcurrency', 'transaction'));
        }
          public function collectPaymentInvoice($id)
        {
            $order = CollectPayment::where('id', $id)->first();
            $user = User::where('id', $order->store_user_id)->first();
            $store = Store::where('id', $order->store_id)->first();
            $country = Country::where('id', $store->country??'')->first();
            $currency = Currency::where('id', $order->currency_id)->first();
            $paidcurrency = Currency::where('id', $order->currency_id)->first();
            $transaction = Transaction::where('uuid', $order->uuid)->first();
            return view('store.collectpaymentInvoice', compact('order', 'user', 'store', 'country', 'currency', 'paidcurrency', 'transaction'));
        }
}

