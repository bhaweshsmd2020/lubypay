<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\MerchantPayment;
use App\Models\QrCode;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use DB;
use App\Http\Helpers\Common;
use App\Models\Store;
use App\Models\Order;
use App\Models\UserDetail;
use App\Models\Cart;
use App\Models\FeesLimit;
use App\Http\Controllers\Users\EmailController;
use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\Product;
use App\Models\TransDeviceInfo;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;
use Illuminate\Support\Str;
use App\Models\Setting;

class QrCodeController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 201;
    protected $helper;
    protected $email;
    
    public function __construct()
    {
        $this->helper     = new Common();
        $this->email      = new EmailController();
        
        $setting = Setting::first();
        $this->admin_email = $setting->notification_email;
    }

    public function getUserQrCode()
    {
        $qrCode = QrCode::where(['object_id' => request('user_id'), 'object_type' => 'user', 'status' => 'Active'])->first(['secret']);
        if (!empty($qrCode))
        {
            return response()->json([
                'status' => $this->successStatus,
                'secret' => urlencode($qrCode->secret),
            ]);
        }
        else
        {
            return response()->json([
                'status' => $this->unauthorisedStatus,
            ]);
        }
    }

    public function addOrUpdateUserQrCode()
    {
        $user_id = request('user_id');
        $user    = User::where(['id' => $user_id, 'status' => 'Active'])->first(['id', 'formattedPhone', 'email', 'type']);
        $qrCode  = QrCode::where(['object_id' => $user_id, 'object_type' => $user->type, 'status' => 'Active'])->first(['id', 'secret']);
        if (empty($qrCode))
        {
            $createUserQrCode              = new QrCode();
            $createUserQrCode->object_id   = $user_id;
            $createUserQrCode->object_type = $user->type;
            if (!empty($user->formattedPhone))
            {
                $createUserQrCode->secret = convert_string('encrypt', $createUserQrCode->object_type . '-' . $user->email . '-' . $user->formattedPhone . '-' . Str::random(6));
            }
            else
            {
                $createUserQrCode->secret = convert_string('encrypt', $createUserQrCode->object_type . '-' . $user->email . '-' . Str::random(6));
            }
            $createUserQrCode->status = 'Active';
            $createUserQrCode->save();

            return response()->json([
                'status' => $this->successStatus,
                'secret' => urlencode($createUserQrCode->secret),
            ]);
        }
        else
        {
            $qrCode->status = 'Inactive';
            $qrCode->save();

            $createUserQrCode              = new QrCode();
            $createUserQrCode->object_id   = $user_id;
            $createUserQrCode->object_type = $user->type;
            if (!empty($user->formattedPhone))
            {
                $createUserQrCode->secret = convert_string('encrypt', $createUserQrCode->object_type . '-' . $user->email . '-' . $user->formattedPhone . '-' . Str::random(6));
            }
            else
            {
                $createUserQrCode->secret = convert_string('encrypt', $createUserQrCode->object_type . '-' . $user->email . '-' . Str::random(6));
            }
            $createUserQrCode->status = 'Active';
            $createUserQrCode->save();

            return response()->json([
                'status' => $this->successStatus,
                'secret' => urlencode($createUserQrCode->secret),
            ]);
        }
    }

    public function performQrCodeOperationApi(Request $request)
    {
        $qrCode = QrCode::where(['secret' => $request->resultText, 'status' => 'Active'])->whereIn('object_type', ['standard_merchant', 'express_merchant', 'user', 'merchant', 'store', 'order'])->first();
        
        if (isset($qrCode) && $qrCode->status == 'Active')
        {
            $result   = convert_string('decrypt', request('resultText'));
            $data     = explode('-', $result);
            $userType = $data[0];
            if ($userType == 'standard_merchant')
            {
                $merchantId                  = $data[1];
                $merchantDefaultCurrencyCode = $data[2];
                $merchantPaymentAmount       = $data[3];
                $merchantPaymentAmount = $merchantPaymentAmount;

                return response()->json([
                    'status'                      => true,
                    'userType'                    => $userType,
                    'merchantId'                  => $merchantId,
                    'merchantDefaultCurrencyCode' => $merchantDefaultCurrencyCode,
                    'merchantPaymentAmount'       => $merchantPaymentAmount,
                ]);
            }
            elseif ($userType == 'express_merchant')
            {
                $merchantId                  = $data[1];
                $merchantDefaultCurrencyCode = $data[2];

                return response()->json([
                    'status'                      => true,
                    'userType'                    => $userType,
                    'merchantId'                  => $merchantId,
                    'merchantDefaultCurrencyCode' => $merchantDefaultCurrencyCode,
                ]);
            }
            elseif ($userType == 'user')
            {
                $receiverEmail = $data[1];
                $receiverPhone = $data[2];
                return response()->json([
                    'status'        => true,
                    'userType'      => $userType,
                    'receiverEmail' => $receiverEmail,
                    'receiverPhone' => $receiverPhone,
                ]);
            }
            elseif ($userType == 'merchant')
            {
                $user_id = $qrCode->object_id;
                $store = Store::where('user_id', $user_id)->first();
                $wallet = Wallet::where('user_id', $user_id)->where('currency_id', $store->currency_id)->first();
                $activeCurrency = Currency::where('id', $wallet->currency_id)->first();
                
                $walletRes['id'] = $wallet->id;
                $walletRes['user_id'] = $wallet->user_id;
                $walletRes['currency_id'] = $wallet->currency_id;
                $walletRes['balance'] = $wallet->balance;
                $walletRes['is_default'] = $wallet->is_default;
                $walletRes['is_collect_payment'] = $wallet->is_collect_payment;
                $walletRes['logo'] = $activeCurrency->logo;
                $walletRes['symbol'] = $activeCurrency->symbol;
                $walletRes['name'] = $activeCurrency->name;
                $walletRes['base_url'] = env('CURRENCY_LOGO');

                $receiverEmail = $data[1];
                $receiverPhone = $data[2];
                return response()->json([
                    'status'        => true,
                    'userType'      => $userType,
                    'receiverEmail' => $receiverEmail,
                    'receiverPhone' => $receiverPhone,
                    'wallet'        => $walletRes
                ]);
            }
            elseif ($userType == 'store')
            {
                $userId                  = $data[1];

                return response()->json([
                    'status'    => true,
                    'userType'  => $userType,
                    'store_id'    => $userId,
                ]);
            }
            elseif ($userType == 'order')
            {
                $orderId                  = $data[1];
                
                $order_details = Order::where('id', $orderId)->first();
                $currency_details = Currency::where('id', $order_details->currency_id)->first();
                $store_details = Store::where('id', $order_details->store_id)->first();

                return response()->json([
                    'status'    => true,
                    'userType'  => $userType,
                    'order_id'  => $orderId,
                    'amount'    => $order_details->total_amount,
                    'currency_id' => $order_details->currency_id,
                    'currency'  => $currency_details->symbol,
                    'store_id'  => $order_details->store_id,
                    'store_user_id' => $order_details->store_user_id,
                    'store_name' => $store_details->name,
                    'store_image' => url('public/uploads/store/'.$store_details->image),
                ]);
            }
            else
            {
                return response()->json([
                    'var'     => $request->resultText,
                    'status'  => 404,
                    'message' => 'Invalid QR Code1!',
                ]);
            }
        }
        else
        {
            return response()->json([
                'var'     => $request->resultText,
                'status'  => 401,
                'message' => 'Invalid QR Cod2!',
            ]);
        }
    }

    public function performSendMoneyRequestMoneyQrCodeOperationApi()
    {
        $qrCode = QrCode::where(['secret' => request('resultText'), 'object_type' => 'user', 'status' => 'Active'])->first(['status']);
        if (isset($qrCode) && $qrCode->status == 'Active')
        {
            $result   = convert_string('decrypt', request('resultText'));
            $data     = explode('-', $result);
            $userType = $data[0];

            if ($userType == 'user')
            {
                $receiverEmail = $data[1];
                return response()->json([
                    'status'        => true,
                    'userType'      => $userType,
                    'receiverEmail' => $receiverEmail,
                ]);
            }
            else
            {
                return response()->json([
                    'status'  => 404,
                    'message' => 'Invalid User!',
                ]);
            }
        }
        else
        {
            return response()->json([
                'status'  => 404,
                'message' => 'Invalid QR Code!',
            ]);
        }
    }

    //Standard Merchant QR Code Payment - starts
    public function performMerchantPaymentQrCodeReviewApi()
    {
        // dd(request()->all());

        //Check merchant
        $merchant = Merchant::find(request('merchantId'), ['id', 'user_id', 'fee', 'business_name']);
        if (!$merchant)
        {
            return response()->json(
                [
                    'status'  => 201,
                    'message' => 'Merchant does not exist!',
                ]
            );
        }

        //merchant cannot make payment to himself
        if ($merchant->user_id == request('user_id'))
        {
            return response()->json(
                [
                    'status'  => 201,
                    'message' => 'Merchant cannot make payment to himself!',
                ]
            );
        }

        //Check currency
        $curr = Currency::where('code', request('merchantDefaultCurrencyCode'))->first(['id', 'symbol']);
        if (!$curr)
        {
            return response()->json(
                [
                    'status'  => 201,
                    'message' => 'Currency - ' . request('merchantDefaultCurrencyCode') . ' - not found!',
                ]
            );
        }

        //Check user's wallets against merchant wallet
        $acceptedCurrency = [];
        $wallets          = Wallet::with(['user:id', 'currency:id,code'])->where(['user_id' => request('user_id')])->get(['currency_id']);
        foreach ($wallets as $wallet)
        {
            $acceptedCurrency[] = $wallet->currency->code;
        }
        if (!in_array(request('merchantDefaultCurrencyCode'), $acceptedCurrency))
        {
            return response()->json(
                [
                    'status'  => 201,
                    'message' => 'You do not have ' . request('merchantDefaultCurrencyCode') . ' wallet. Please exchange to ' . request('merchantDefaultCurrencyCode') . ' wallet!',
                ]
            );
        }

        //Check Balance
        $merchantPaymentAmount = request('merchantPaymentAmount');
        $senderWallet          = Wallet::where(['user_id' => request('user_id'), 'currency_id' => $curr->id])->first(['balance']);
        if ($senderWallet->balance < $merchantPaymentAmount)
        {
            return response()->json([
                'status'  => 201,
                'message' => 'Sorry, not enough funds to perform the operation!',
            ]);
        }

        //Data for success below
        $merchantCalculatedChargePercentageFee = ($merchant->fee * $merchantPaymentAmount) / 100;

        return response()->json([
            'status'                                => 200,
            'merchantBusinessName'                  => $merchant->business_name,
            'merchantPaymentCurrencySymbol'         => $curr->symbol,
            'merchantPaymentAmount'                 => $merchantPaymentAmount,
            'merchantCalculatedChargePercentageFee' => $merchantCalculatedChargePercentageFee,
            //below needed for merchant payment submit
            'merchantActualFee'                     => $merchant->fee,
            'merchantCurrencyId'                    => $curr->id,
            'merchantUserId'                        => $merchant->user_id,
        ]);
    }

    public function performMerchantPaymentQrCodeSubmit()
    {
        $unique_code           = unique_code();
        $merchantPaymentAmount = request('merchantPaymentAmount');
        $merchantActualFee     = request('merchantActualFee');
        $merchantCurrencyId    = request('merchantCurrencyId');
        $merchantUserId        = request('merchantUserId');
        $merchantId            = request('merchantId');
        $user_id               = request('user_id');

        $p_calc = ($merchantActualFee * $merchantPaymentAmount) / 100;

        try
        {
            \DB::beginTransaction();

            //Merchant Payment
            $merchantPayment                    = new MerchantPayment();
            $merchantPayment->merchant_id       = $merchantId;
            $merchantPayment->currency_id       = $merchantCurrencyId;
            $merchantPayment->payment_method_id = 1;
            $merchantPayment->user_id           = $user_id;
            $merchantPayment->gateway_reference = $unique_code;
            $merchantPayment->order_no          = '';
            $merchantPayment->item_name         = '';
            $merchantPayment->uuid              = $unique_code;
            $merchantPayment->charge_percentage = $p_calc;
            $merchantPayment->charge_fixed      = 0;
            $merchantPayment->amount            = $merchantPaymentAmount - $p_calc;
            $merchantPayment->total             = $merchantPaymentAmount;
            $merchantPayment->status            = 'Success';
            $merchantPayment->save();

            //Payment_Sent
            $transaction_A                           = new Transaction();
            $transaction_A->user_id                  = $user_id;
            $transaction_A->end_user_id              = $merchantUserId;
            $transaction_A->currency_id              = $merchantCurrencyId;
            $transaction_A->payment_method_id        = 1;
            $transaction_A->merchant_id              = $merchantId;
            $transaction_A->uuid                     = $unique_code;
            $transaction_A->transaction_reference_id = $merchantPayment->id;
            $transaction_A->transaction_type_id      = Payment_Sent;
            $transaction_A->subtotal                 = $merchantPaymentAmount;
            $transaction_A->percentage               = $merchantActualFee;
            $transaction_A->charge_percentage        = 0;
            $transaction_A->charge_fixed             = 0;
            $transaction_A->total                    = '-' . ($merchantPayment->charge_percentage + $merchantPayment->amount);
            $transaction_A->status                   = 'Success';
            $transaction_A->save();

            //Payment_Received
            $transaction_B                           = new Transaction();
            $transaction_B->user_id                  = $merchantUserId;
            $transaction_B->end_user_id              = $user_id;
            $transaction_B->currency_id              = $merchantCurrencyId;
            $transaction_B->payment_method_id        = 1;
            $transaction_B->merchant_id              = $merchantId;
            $transaction_B->uuid                     = $unique_code;
            $transaction_B->transaction_reference_id = $merchantPayment->id;
            $transaction_B->transaction_type_id      = Payment_Received;
            $transaction_B->subtotal                 = $merchantPaymentAmount - ($p_calc);
            $transaction_B->percentage               = $merchantActualFee; //fixed
            $transaction_B->charge_percentage        = $p_calc;
            $transaction_B->charge_fixed             = 0;
            $transaction_B->total                    = $merchantPayment->charge_percentage + $merchantPayment->amount;
            $transaction_B->status                   = 'Success';
            $transaction_B->save();

            //updating sender/user wallet
            $senderWallet          = Wallet::where(['user_id' => $user_id, 'currency_id' => $merchantCurrencyId])->first(['id', 'balance', 'user_id']);
            $senderWallet->balance = $senderWallet->balance - $merchantPaymentAmount;
            $senderWallet->save();

            //updating/Creating merchant wallet
            $merchantWallet          = Wallet::where(['user_id' => $merchantUserId, 'currency_id' => $merchantCurrencyId])->first(['id', 'balance']);
            if (empty($merchantWallet))
            {
                $wallet              = new Wallet();
                $wallet->user_id     = $merchantUserId;
                $wallet->currency_id = $merchantCurrencyId;
                $wallet->balance     = ($merchantPaymentAmount - $p_calc);
                $wallet->is_default  = 'No';
                $wallet->save();
            }
            else
            {
                $merchantWallet->balance = $merchantWallet->balance + ($merchantPaymentAmount - $p_calc);
                $merchantWallet->save();
            }

            \DB::commit();
            
            $userdevices               = DB::table('devices')->where(['user_id' => $user_id])->first();
            if(isset($userdevices) && $userdevices->fcm_token)
            {
                $msg= 'Your transaction of amount '.$merchantPaymentAmount. ' to '.$merchantUserId .' successfully done.';
            	//echo "<pre>"; print_r($userdevices); die;
            	$notifyData   = array (
            	'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
            	'title'         => 'New message from CaribPay',
            	'content'       => $msg,
            	'type'          => 'Message',
            	// Require for auto fetch incoming request push.
            	'payload'       => array (//'post' => $data->created_at
            		)
            	);
            // 	$datanotice= array('title'=>'New message from CaribPay','content'=>$msg,'type'=>'push','content_type'=>'QRsendmoney','user'=>$user_id);
            	DB::table('noticeboard')->insert($datanotice);
            // 	$this->helper->sendFCMPush($notifyData);
            }

            return response()->json([
                'status' => $this->successStatus,
            ]);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => $e->getMessage(),
            ]);
        }
    }
    //Standard Merchant QR Code Payment - ends

    //Express Merchant QR Code Payment - starts
    public function performExpressMerchantPaymentMerchantCurrencyUserWalletsReviewApi()
    {
        // dd(request()->all());

        //Check merchant
        $merchant = Merchant::find(request('expressMerchantId'), ['id', 'user_id', 'fee', 'business_name']);
        if (!$merchant)
        {
            return response()->json(
                [
                    'status'  => 201,
                    'message' => 'Merchant does not exist!',
                ]
            );
        }

        //merchant cannot make payment to himself
        if ($merchant->user_id == request('user_id'))
        {
            return response()->json(
                [
                    'status'  => 201,
                    'message' => 'Merchant cannot make payment to himself!',
                ]
            );
        }

        //Check currency
        $curr = Currency::where('code', request('expressMerchantPaymentCurrencyCode'))->first(['id', 'symbol']);
        if (!$curr)
        {
            return response()->json(
                [
                    'status'  => 201,
                    'message' => 'Currency - ' . request('expressMerchantPaymentCurrencyCode') . ' - not found!',
                ]
            );
        }

        //Check user's wallets against merchant wallet
        $acceptedCurrency = [];
        $wallets          = Wallet::with(['user:id', 'currency:id,code'])->where(['user_id' => request('user_id')])->get(['currency_id']);
        foreach ($wallets as $wallet)
        {
            $acceptedCurrency[] = $wallet->currency->code;
        }
        if (!in_array(request('expressMerchantPaymentCurrencyCode'), $acceptedCurrency))
        {
            return response()->json(
                [
                    'status'  => 201,
                    'message' => 'You do not have ' . request('expressMerchantPaymentCurrencyCode') . ' wallet. Please exchange to ' . request('expressMerchantPaymentCurrencyCode') . ' wallet!',
                ]
            );
        }

        return response()->json([
            'status'                               => 200,
            //below needed for merchant payment submit
            'expressMerchantBusinessName'          => $merchant->business_name,
            'expressMerchantPaymentCurrencyId'     => $curr->id,
            'expressMerchantPaymentCurrencySymbol' => $curr->symbol,
            'expressMerchantActualFee'             => $merchant->fee,
            'expressMerchantUserId'                => $merchant->user_id,
        ]);
    }

    public function performExpressMerchantPaymentAmountReviewApi()
    {
        // dd(request()->all());

        //Check Balance
        $expressMerchantPaymentAmount = request('expressMerchantPaymentAmount');
        $senderWallet                 = Wallet::where(['user_id' => request('user_id'), 'currency_id' => request('expressMerchantPaymentCurrencyId')])->first(['balance']);
        if ($senderWallet->balance < $expressMerchantPaymentAmount)
        {
            return response()->json([
                'status'  => 201,
                'message' => 'Sorry, not enough funds to perform the operation!',
            ]);
        }
        //Data for success below
        $expressMerchantCalculatedChargePercentageFee = (request('expressMerchantActualFee') * $expressMerchantPaymentAmount) / 100;

        return response()->json([
            'status'                                       => 200,
            'expressMerchantCalculatedChargePercentageFee' => $expressMerchantCalculatedChargePercentageFee,
        ]);
    }

    public function performExpressMerchantPaymentQrCodeSubmit()
    {
        // dd(request()->all());

        $unique_code                      = unique_code();
        $expressMerchantPaymentAmount     = request('expressMerchantPaymentAmount');
        $expressMerchantActualFee         = request('expressMerchantActualFee');
        $expressMerchantPaymentCurrencyId = request('expressMerchantPaymentCurrencyId');
        $expressMerchantUserId            = request('expressMerchantUserId');
        $expressMerchantId                = request('expressMerchantId');
        $user_id                          = request('user_id');

        $p_calc = ($expressMerchantActualFee * $expressMerchantPaymentAmount) / 100;

        try
        {
            \DB::beginTransaction();

            //Merchant Payment
            $merchantPayment                    = new MerchantPayment();
            $merchantPayment->merchant_id       = $expressMerchantId;
            $merchantPayment->currency_id       = $expressMerchantPaymentCurrencyId;
            $merchantPayment->payment_method_id = 1;
            $merchantPayment->user_id           = $user_id;
            $merchantPayment->gateway_reference = $unique_code;
            $merchantPayment->order_no          = '';
            $merchantPayment->item_name         = '';
            $merchantPayment->uuid              = $unique_code;
            $merchantPayment->charge_percentage = $p_calc;
            $merchantPayment->charge_fixed      = 0;
            $merchantPayment->amount            = $expressMerchantPaymentAmount - $p_calc;
            $merchantPayment->total             = $expressMerchantPaymentAmount;
            $merchantPayment->status            = 'Success';
            $merchantPayment->save();

            //Payment_Sent
            $transaction_A                           = new Transaction();
            $transaction_A->user_id                  = $user_id;
            $transaction_A->end_user_id              = $expressMerchantUserId;
            $transaction_A->currency_id              = $expressMerchantPaymentCurrencyId;
            $transaction_A->payment_method_id        = 1;
            $transaction_A->merchant_id              = $expressMerchantId;
            $transaction_A->uuid                     = $unique_code;
            $transaction_A->transaction_reference_id = $merchantPayment->id;
            $transaction_A->transaction_type_id      = Payment_Sent;
            $transaction_A->subtotal                 = $expressMerchantPaymentAmount;
            $transaction_A->percentage               = $expressMerchantActualFee;
            $transaction_A->charge_percentage        = 0;
            $transaction_A->charge_fixed             = 0;
            $transaction_A->total                    = '-' . ($merchantPayment->charge_percentage + $merchantPayment->amount);
            $transaction_A->status                   = 'Success';
            $transaction_A->save();

            //Payment_Received
            $transaction_B                           = new Transaction();
            $transaction_B->user_id                  = $expressMerchantUserId;
            $transaction_B->end_user_id              = $user_id;
            $transaction_B->currency_id              = $expressMerchantPaymentCurrencyId;
            $transaction_B->payment_method_id        = 1;
            $transaction_B->merchant_id              = $expressMerchantId;
            $transaction_B->uuid                     = $unique_code;
            $transaction_B->transaction_reference_id = $merchantPayment->id;
            $transaction_B->transaction_type_id      = Payment_Received;
            $transaction_B->subtotal                 = $expressMerchantPaymentAmount - ($p_calc);
            $transaction_B->percentage               = $expressMerchantActualFee; //fixed
            $transaction_B->charge_percentage        = $p_calc;
            $transaction_B->charge_fixed             = 0;
            $transaction_B->total                    = $merchantPayment->charge_percentage + $merchantPayment->amount;
            $transaction_B->status                   = 'Success';
            $transaction_B->save();

            //updating sender/user wallet
            $senderWallet          = Wallet::where(['user_id' => $user_id, 'currency_id' => $expressMerchantPaymentCurrencyId])->first(['id', 'balance', 'user_id']);
            $senderWallet->balance = $senderWallet->balance - $expressMerchantPaymentAmount;
            $senderWallet->save();

            //updating/Creating merchant wallet
            $merchantWallet = Wallet::where(['user_id' => $expressMerchantUserId, 'currency_id' => $expressMerchantPaymentCurrencyId])->first(['id', 'balance']);
            if (empty($merchantWallet))
            {
                $wallet              = new Wallet();
                $wallet->user_id     = $expressMerchantUserId;
                $wallet->currency_id = $expressMerchantPaymentCurrencyId;
                $wallet->balance     = ($expressMerchantPaymentAmount - $p_calc);
                $wallet->is_default  = 'No';
                $wallet->save();
            }
            else
            {
                $merchantWallet->balance = $merchantWallet->balance + ($expressMerchantPaymentAmount - $p_calc);
                $merchantWallet->save();
            }

            \DB::commit();
            
            $userdevices               = DB::table('devices')->where(['user_id' => $user_id])->first();
            if(isset($userdevices) && $userdevices->fcm_token)
            {
                $msg= 'Your transaction of amount '.$expressMerchantPaymentAmount. ' to '.$expressMerchantUserId .' successfully done.';
            	//echo "<pre>"; print_r($userdevices); die;
            	$notifyData   = array (
            	'device_tokens' => (isset($userdevices) && $userdevices->fcm_token) ? array($userdevices->fcm_token) : [],
            	'title'         => 'New message from CaribPay',
            	'content'       => $msg,
            	'type'          => 'Message',
            	// Require for auto fetch incoming request push.
            	'payload'       => array (//'post' => $data->created_at
            		)
            	);
            	$datanotice= array('title'=>'New message from CaribPay','content'=>$msg,'type'=>'push','content_type'=>'QRexpresssendmoney','user'=>$user_id);
            	DB::table('noticeboard')->insert($datanotice);
            // 	$this->helper->sendFCMPush($notifyData);
            }

            return response()->json([
                'status' => $this->successStatus,
            ]);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => $e->getMessage(),
            ]);
        }
    }
    //Express Merchant QR Code Payment - ends
    
    public function performStoreMerchantPaymentMerchantCurrencyUserWalletsReviewApi(Request $request)
    {
        $store_id = $request->store_user_id;
        $user_id = $request->user_id;
        
        if ($store_id == $user_id)
        {
            return response()->json(
                [
                    'status'  => 201,
                    'message' => 'Merchant cannot make payment to himself!',
                ]
            );
        }

        $store = Store::where('user_id', $store_id)->first();
        if (!$store)
        {
            return response()->json(
                [
                    'status'  => 201,
                    'message' => 'Store does not exist!',
                ]
            );
        }

        //Check currency
        $curr = Currency::where('id', $store->currency_id)->first();
        if (!$curr)
        {
            return response()->json(
                [
                    'status'  => 201,
                    'message' => 'Currency not found!',
                ]
            );
        }

        return response()->json([
            'status'                => 200,
            'store_name'            => $store->name,
            'store_currency'        => $curr->id,
            'store_currency_symbol' => $curr->symbol,
            'store_id'              => $store->id,
            'store_user_id'         => $store->user_id,
            'user_id'               => $user_id,
        ]);
    }
    
    public function performStoreMerchantPaymentQrCodeSubmit(Request $request)
    {
        $unique_code   = unique_code();
        $userType      = $request->userType;
        $order_id      = $request->order_id;
        $amount        = $request->amount;
        $currency_id   = $request->currency_id;
        $store_id      = $request->store_id;
        $store_user_id = $request->store_user_id;
        $store_name    = $request->store_name;
        $user_id       = $request->user_id;
        $user_currency_id = $request->user_currency_id;
        $local_tran_time  = $request->local_tran_time;
        
        $order_check = Order::where('id', $order_id)->first();
        if(!empty($order_check) && $order_check->status == 'success'){
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => 'Order already placed.',
            ]);
        }
        
        if($currency_id != $user_currency_id){
            $from_currency = Currency::where('id', $currency_id)->first();
            $to_currency = Currency::where('id', $user_currency_id)->first();
           
            $toWalletCurrency = $this->helper->getCurrencyObject(['id' => $user_currency_id], ['exchange_from', 'code', 'rate', 'symbol']);
            if (!empty($toWalletCurrency))
            {
                if ($toWalletCurrency->exchange_from == "local")
                {
                    $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $currency_id], ['rate', 'symbol']);
                    $defaultCurrency    = $this->helper->getCurrencyObject(['default' => 1], ['rate']);
                    $toWalletRate       = ($defaultCurrency->rate / $fromWalletCurrency->rate) * $toWalletCurrency->rate;
                }
                else
                {
                    $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $user_currency_id], ['rate', 'symbol']);
                    $exchangevalue = getCurrencyRate($from_currency->code, $toWalletCurrency->code);
                    $toWalletRate = $exchangevalue;
                }
                $getAmountMoneyFormat = $toWalletRate * $amount;
                $new_amount = number_format((float)$getAmountMoneyFormat, 2, '.', '');
            }else{
                $new_amount = number_format((float)$amount, 2, '.', '');
            }
        }else{
            $new_amount = number_format((float)$amount, 2, '.', '');
        }
        
        $order_detail = Order::where('id', $order_id)->first();
        $user = User::where('id', $user_id)->first();
        $userdetail = UserDetail::where('user_id', $user_id)->first();
        
        $wallet_detail = Wallet::where('currency_id', $user_currency_id)->where('user_id', $user_id)->first();
        if(empty($wallet_detail)){
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => 'Wallet not exists.',
            ]);
        }
        
        if($wallet_detail->balance < $new_amount){
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => 'Insufficient Fund.',
            ]);
        }

        try
        {
            $feeInfo       = FeesLimit::where(['transaction_type_id' => '33', 'currency_id' => $currency_id])->first();
            $feePercent    = $amount * ($feeInfo->charge_percentage / 100);
            $feePercentage = number_format((float)$feePercent, 2, '.', '');
        
            //Payment_Sent
            $transaction_A                           = new Transaction();
            $transaction_A->user_id                  = $user_id;
            $transaction_A->end_user_id              = $store_user_id;
            $transaction_A->currency_id              = $user_currency_id;
            $transaction_A->payment_method_id        = 1;
            $transaction_A->merchant_id              = null;
            $transaction_A->uuid                     = $unique_code;
            $transaction_A->transaction_reference_id = $order_detail->unique_id;
            $transaction_A->transaction_type_id      = Payment_Sent;
            $transaction_A->subtotal                 = $new_amount;
            $transaction_A->percentage               = 0;
            $transaction_A->charge_percentage        = 0;
            $transaction_A->charge_fixed             = 0;
            $transaction_A->total                    = '-' . ($new_amount);
            $transaction_A->status                   = 'Success';
            $transaction_A->local_tran_time          = $local_tran_time;
            $transaction_A->ip_address               = request()->ip();
            $transaction_A->save();
            
            $rs = TransDeviceInfo::create([
                'user_id' => $user_id, 
                'trans_id' => $transaction_A->id, 
                'device_id' => $request->device_id, 
                'app_ver' => $request->app_ver, 
                'device_name' => $request->device_name, 
                'device_manufacture' => $request->device_manufacture, 
                'device_model' => $request->device_model, 
                'os_ver' => $request->os_ver, 
                'device_os' => $request->device_os, 
                'ip_address' => request()->ip(),
            ]);

            //Payment_Received
            $transaction_B                           = new Transaction();
            $transaction_B->user_id                  = $store_user_id;
            $transaction_B->end_user_id              = $user_id;
            $transaction_B->currency_id              = $currency_id;
            $transaction_B->payment_method_id        = 1;
            $transaction_B->merchant_id              = null;
            $transaction_B->uuid                     = $unique_code;
            $transaction_B->transaction_reference_id = $order_detail->unique_id;
            $transaction_B->transaction_type_id      = "34";
            $transaction_B->subtotal                 = $amount;
            $transaction_B->percentage               = 0;
            $transaction_B->charge_percentage        = 0;
            $transaction_B->store_fee                = $feePercentage;
            $transaction_B->charge_fixed             = 0;
            $transaction_B->total                    = $amount;
            $transaction_B->status                   = 'Success';
            $transaction_B->local_tran_time          = $local_tran_time;
            $transaction_B->ip_address               = request()->ip();
            $transaction_B->save();
            
            $rs = TransDeviceInfo::create([
                'user_id' => $store_user_id, 
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

            //updating sender/user wallet
            $senderWallet          = Wallet::where(['user_id' => $user_id, 'currency_id' => $user_currency_id])->first();
            $senderWallet->balance = $senderWallet->balance - $new_amount;
            $senderWallet->save();

            //updating/Creating merchant wallet
            $merchantWallet = Wallet::where(['user_id' => $store_user_id, 'currency_id' => $currency_id])->first();
            if (empty($merchantWallet))
            {
                $wallet              = new Wallet();
                $wallet->user_id     = $store_user_id;
                $wallet->currency_id = $currency_id;
                $wallet->balance     = $amount - $feePercentage;
                $wallet->is_default  = 'No';
                $wallet->save();
            }
            else
            {
                $merchantWallet->balance = $merchantWallet->balance + ($amount - $feePercentage);
                $merchantWallet->save();
            }
            
            $update_order = Order::where('id', $order_id)->update([
                'status' => 'success',
                'user_id'       => $user->id,
                'customer_name' => $user->first_name.' '.$user->last_name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'customer_phone_prefix' => $user->carrierCode,
                'customer_address1' => $userdetail->address_1,
                'customer_address2' => $userdetail->address_2,
                'customer_zipcode' => $userdetail->zip_code,
                'customer_city' => $userdetail->city,
                'customer_state' => $userdetail->state,
                'customer_country' => $userdetail->country_id,
                'paid_amount' => $new_amount,
                'paid_currency_id' => $user_currency_id,
                'payment_response' => 'success',
                'local_tran_time' => $local_tran_time,
                'ip_address' => request()->ip(),
            ]);
            
            $clear_cart = Cart::where('store_id', $store_id)->delete();
            
            $allproducts = json_decode($order_detail->products);
            foreach($allproducts as $allproduct){
                $product = Product::where('id', $allproduct->product_id)->first();
                
                if(!empty($product)){
                    $update_stock = Product::where('id', $allproduct->product_id)->update([
                        'quantity' => $product->quantity - $allproduct->qty,
                    ]);
                }
            }
            
            $qrCode_update = QrCode::where(['object_id' => $store_user_id, 'object_type' => 'order', 'status' => 'Active'])->update(['status' => 'Inactive']);
            
            $user = User::where('id', $user_id)->first();
	        $store = Store::where('user_id', $store_user_id)->first();
	        $store_user = User::where('id', $store_user_id)->first();
            $type = "mpos";
            $date    = date("m-d-Y h:i");
            
            $user_currency = Currency::where('id', $user_currency_id)->first();
            
            //Notification to user
            $userdevice = DB::table('devices')->where('user_id', $user_id)->first();
            if(!empty($userdevice)){
                $device_lang = $userdevice->language;
            }else{
                $device_lang = getDefaultLanguage();
            }

            $template = NotificationTemplate::where('temp_id', '14')->where('language_id', $device_lang)->first();
            $subject = $template->title;
            $subheader = $template->subheader;
            $message = $template->content;
            
            $umsg = str_replace('{currency}', $user_currency->code, $message);
            $umsg = str_replace('{amount}', number_format($new_amount, 2, '.', ','), $umsg);
            $umsg = str_replace('{receiver}', $store->name, $umsg);
            
            $this->helper->sendFirabasePush($subject, $umsg, $user_id, $user_currency_id, $type);
            
            Noticeboard::create([
                'tr_id' => $transaction_A->id,
                'title' => $subject,
                'content' => $umsg,
                'type' => 'push',
                'content_type' => 'mpos',
                'user' => $user_id,
                'sub_header' => $subheader,
                'push_date' => $request->local_tran_time,
                'template' => '14',
                'language' => $device_lang,
                'currency' => $user_currency->code,
                'amount' => number_format($new_amount, 2, '.', ','),
                'receiver' => $store->name
            ]);
            
        	//Email to user
        	$twoStepVerification = EmailTemplate::where([
                'temp_id'     => 36,
                'language_id' => $device_lang,
                'type'        => 'email',
            ])->select('subject', 'body')->first();
           
            $twoStepVerification_sub = $twoStepVerification->subject;
            $twoStepVerification_msg = str_replace('{user}', $user->first_name.' '.$user->last_name, $twoStepVerification->body);
            $twoStepVerification_msg = str_replace('{store_name}', $store->name, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{order_id}', '#'.$order_detail->unique_id, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{currency}', $user_currency->code, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{pay_method}', 'QR Payment', $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{date}', $date, $twoStepVerification_msg);
            $twoStepVerification_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerification_msg);
            $this->email->sendEmail($user->email, $twoStepVerification_sub, $twoStepVerification_msg);
            
            $store_currency = Currency::where('id', $currency_id)->first();
            
            //Notification to Merchant
            $storeuserdevice = DB::table('devices')->where('user_id', $store_user_id)->first();
            if(!empty($storeuserdevice)){
                $store_device_lang = $storeuserdevice->language;
            }else{
                $store_device_lang = getDefaultLanguage();
            }
            $template = NotificationTemplate::where('temp_id', '17')->where('language_id', $store_device_lang)->first();
            $st_subject = $template->title;
            $st_subheader = $template->subheader;
            $st_message = $template->content;
            
            $msg = str_replace('{currency}', $store_currency->code, $st_message);
            $msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $msg);
            $msg = str_replace('{sender}', $user->first_name.' '.$user->last_name, $msg);
            
            $this->helper->sendFirabasePush($st_subject, $msg, $store_user_id, $currency_id, $type);
            
            Noticeboard::create([
                'tr_id' => $transaction_B->id,
                'title' => $st_subject,
                'content' => $msg,
                'type' => 'push',
                'content_type' => 'mpos',
                'user' => $store_user_id,
                'sub_header' => $st_subheader,
                'push_date' => $request->local_tran_time,
                'template' => '17',
                'language' => $store_device_lang,
                'currency' => $store_currency->code,
                'amount' => number_format($amount, 2, '.', ','),
                'sender' => $user->first_name.' '.$user->last_name
            ]);
            
        	//Email to Merchant
        	if(!empty($store_user->email)){
            	$twoStepVerificationmerc = EmailTemplate::where([
                    'temp_id'     => 43,
                    'language_id' => $store_device_lang,
                    'type'        => 'email',
                ])->select('subject', 'body')->first();
               
                $twoStepVerificationmerc_sub = $twoStepVerificationmerc->subject;
                $twoStepVerificationmerc_msg = str_replace('{store_name}', $store->name, $twoStepVerificationmerc->body);
                $twoStepVerificationmerc_msg = str_replace('{order_id}', '#'.$order_detail->unique_id, $twoStepVerificationmerc_msg);
                $twoStepVerificationmerc_msg = str_replace('{currency}', $user_currency->code, $twoStepVerificationmerc_msg);
                $twoStepVerificationmerc_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $twoStepVerificationmerc_msg);
                $twoStepVerificationmerc_msg = str_replace('{pay_method}', 'Card Payment', $twoStepVerificationmerc_msg);
                $twoStepVerificationmerc_msg = str_replace('{date}', $date, $twoStepVerificationmerc_msg);
                $twoStepVerificationmerc_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerificationmerc_msg);
                $this->email->sendEmail($store_user->email, $twoStepVerificationmerc_sub, $twoStepVerificationmerc_msg);
        	}
        	
        	// Email / Notification to admin
            $adminAllowed = Notification::has_permission([1]);
                                
            foreach($adminAllowed as $admin){
                Notification::insert([
                    'user_id'               => $user_id,
                    'notification_to'       => $admin->agent_id,
                    'notification_type_id'  => 1,
                    'notification_type'     => 'Web',
                    'description'           => $user->first_name.' '.$user->last_name.' has done a payment of '.$user_currency->code .' '.$new_amount.' to '.$store->name,
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
                $twoStepVerificationadm_msg = str_replace('{order_id}', '#'.$order_detail->unique_id, $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{currency}', $user_currency->code, $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{amount}', number_format($amount, 2, '.', ','), $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{pay_method}', 'Card Payment', $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{date}', $date, $twoStepVerificationadm_msg);
                $twoStepVerificationadm_msg = str_replace('{soft_name}', getCompanyName(), $twoStepVerificationadm_msg);
                $this->email->sendEmail($admin->email, $twoStepVerificationadm_sub, $twoStepVerificationadm_msg);
        	}
        	
        	$order_details = Order::where('id', $order_id)->first();
        	$currency = Currency::where('id', $order_details->currency_id)->first();
        	$paid_currency = Currency::where('id', $order_details->paid_currency_id)->first();
        	$userWallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $user_currency_id])->first();
        	
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
            $order_details->payment_mode = 'Wallet';
            $order_details->transaction_id = $transaction_A->uuid;
            $order_details->updated_balance = $userWallet->balance;

            return response()->json([
                'status' => $this->successStatus,
                'message' => 'Order placed successfully.',
                'data' => $order_details
            ]);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => $e->getMessage(),
            ]);
        }
    }
    
    public function qrwallet(Request $request)
    {
        $from_currency = $request->from_currency;  // from which the amount is deducting
        $amount = $request->amount; // from the amount is deducting
        $wallet = $request->wallet; // to which the amount is adding
        
        $current_balance = Wallet::where('id', $wallet)->first();
        $currency_id = $current_balance->currency_id;
        $currency = Currency::where('id', $from_currency)->first();
        $new_curr = Currency::where('id', $currency_id)->first();
       
        $toWalletCurrency = $this->helper->getCurrencyObject(['id' => $currency_id], ['exchange_from', 'code', 'rate', 'symbol']);
        
        if (!empty($toWalletCurrency))
        {
            if ($toWalletCurrency->exchange_from == "local")
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $from_currency], ['rate', 'symbol']);
                $defaultCurrency    = $this->helper->getCurrencyObject(['default' => 1], ['rate']);
                $toWalletRate       = ($defaultCurrency->rate / $fromWalletCurrency->rate) * $toWalletCurrency->rate;
            }
            else
            {
                $fromWalletCurrency = $this->helper->getCurrencyObject(['id' => $currency_id], ['rate', 'symbol']);
                $exchangevalue = getCurrencyRate($currency->code, $toWalletCurrency->code);
                $toWalletRate = $exchangevalue;
            }
            $getAmountMoneyFormat = $toWalletRate * (request('amount'));
            $new_amount = number_format((float)$getAmountMoneyFormat, 2, '.', '');
        }
        
        if(!empty($current_balance->balance) && $current_balance->balance >= $new_amount){
            $result['amount'] = $new_amount;
            $result['total'] = number_format((float)$new_amount, 2, '.', '');
            $result['currency'] = $new_curr->code;
            $result['currency_id'] = $currency_id;
            
            return response()->json([
                'status'  => $this->successStatus,
                'message' => 'Wallet details fetched successfully.',
                'data'    => $result,
            ]);
        }else{
            return response()->json([
                'status'  => $this->unauthorisedStatus,
                'message' => 'Insufficient Fund!',
                'data'    => null
            ]);
        }
    }
}
