<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Country;
use App\Models\Currency;
use App\Models\EmailTemplate;
use App\Models\FeesLimit;
use App\Models\NotificationSetting;
use App\Models\PaymentMethod;
use App\Models\PayoutSetting;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\SalesWithdrawal;
use Auth;
use Illuminate\Http\Request;
use Session;
use Validator;
use App\Models\PendingTransaction;

class WithdrawalController extends Controller
{
    protected $helper;
    protected $withdrawal;
    protected $email;

    public function __construct()
    {
        $this->helper     = new Common();
        $this->email      = new EmailController();
        $this->withdrawal = new Withdrawal();
    }

    //Payout Setting starts
    public function payouts()
    {
        setActionSession();
        $data['menu']    = 'payout';
        $data['payouts'] = Withdrawal::with(['payment_method:id,name', 'withdrawal_detail:id,withdrawal_id,account_name,account_number,bank_name', 'currency:id,code']) //optimized by parvez
            ->where(['user_id' => auth()->user()->id])->orderBy('withdrawals.created_at', 'desc')
            ->select('id', 'created_at', 'payment_method_id', 'amount', 'subtotal', 'currency_id', 'status', 'payment_method_info', 'uuid')
            ->paginate(10);

        // if no payout setting
        $data['payoutSettings'] = PayoutSetting::where(['user_id' => auth()->user()->id])->get(['id']);
        return view('user_dashboard.withdrawal.payouts', $data);
    }
    
    public function payoutsDetail($id)
    {
        setActionSession();
        $data['menu']    = 'payout';
        $data['payouts'] = Withdrawal::with(['payment_method:id,name', 'withdrawal_detail:id,withdrawal_id,account_name,account_number,bank_name,type,email', 'currency:id,code', 'sales_withdrawal:unique_order_id']) //optimized by parvez
            ->where(['id' => $id])
            ->select('id', 'created_at', 'payment_method_id', 'amount', 'subtotal', 'currency_id', 'status', 'payment_method_info', 'uuid')->get();
            
        // $sales_withdrawn = SalesWithdrawal::where(['withdrawal_id' => $id])->get();
        // $data['transaction_details'] = $sales_withdrawn->count() > 0 ? json_decode($sales_withdrawn, true) : null;
        
        $sales_withdrawns = SalesWithdrawal::where(['withdrawal_id' => $id])->get();
        $sales_withdrawn = json_decode($sales_withdrawns, true);
        
        $order_ids = [];
        foreach($sales_withdrawn as $sales){
            array_push($order_ids, $sales['unique_order_id']);
        }
        $sales_transaction = json_decode(Transaction::where(['transaction_type_id' => '12'])->whereIn('uuid', $order_ids)->get(), true);
        
        $buyer_ids = [];
        foreach($sales_transaction as $sales){
            array_push($buyer_ids, $sales['end_user_id']);
        }
        
        $data['buyer_details'] = $buyer = User::whereIn('id', $buyer_ids)->get();
        $data['sales_transaction'] = $sales_withdrawns->count() > 0 ? $sales_transaction : null;
        
        return view('user_dashboard.withdrawal.detail', $data);
    }

    public function payoutSetting()
    {
        $data['menu']           = 'payout';
        $data['payoutSettings'] = PayoutSetting::with(['paymentMethod:id,name'])
        ->where(['user_id' => auth()->user()->id])
        ->paginate(10);
        $data['countries']      = Country::get(['id', 'name']);
        $data['paymentMethods'] = PaymentMethod::whereNotIn('id', [1, 2, 4, 5, 7, 8, 9])->where(['status' => 'Active'])->get(['id', 'name']);
        return view('user_dashboard.withdrawal.payoutSetting', $data);
    }

    public function payoutSettingStore(Request $request)
    {

        $rules = array(
            'type'  => 'required',
            'email' => 'nullable|email',
        );
        $fieldNames = array(
            'type'  => 'Type',
            'email' => 'Email',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {

            $type                   = $request->type;
            $payoutSetting          = new PayoutSetting();
            $payoutSetting->type    = $type;
            $payoutSetting->user_id = auth()->user()->id;
            if ($type == 6)
            {
                $payoutSetting->account_name        = $request->account_name;
                $payoutSetting->account_number      = $request->account_number;
                $payoutSetting->swift_code          = $request->swift_code;
                $payoutSetting->bank_name           = $request->bank_name;
                $payoutSetting->bank_branch_name    = $request->branch_name;
                $payoutSetting->bank_branch_city    = $request->branch_city;
                $payoutSetting->bank_branch_address = $request->branch_address;
                $payoutSetting->country             = $request->country;
            }
            elseif ($type == 8)
            {
                $payoutSetting->account_number = $request->payeer_account_no;
            }
            elseif ($type == 9)
            {
                $payoutSetting->account_number = $request->perfect_money_account_no;
            }
            else
            {
                $payoutSetting->email = $request->email;
            }
            $payoutSetting->save();

            $this->helper->one_time_message('success', __('Payout Setting Created Successfully!'));
            return back();
        }
    }

    public function payoutSettingUpdate(Request $request)
    {
        $id      = $request->setting_id;
        $setting = PayoutSetting::find($id);
        if (!$setting)
        {
            $this->helper->one_time_message('error', __('Payout Setting not found !'));
            return back();
        }
        if ($setting->type == 6)
        {
            $setting->account_name        = $request->account_name;
            $setting->account_number      = $request->account_number;
            $setting->bank_branch_name    = $request->bank_name;
            $setting->bank_branch_city    = $request->branch_city;
            $setting->bank_branch_address = $request->branch_address;
            $setting->country             = $request->country;
            $setting->swift_code          = $request->swift_code;
            $setting->bank_name           = $request->bank_name;
        }
        elseif ($setting->type == 8)
        {
            $setting->account_number = $request->payeer_account_no;
        }
        elseif ($setting->type == 9)
        {
            $setting->account_number = $request->perfect_money_account_no;
        }
        else
        {
            $setting->email = $request->email;
        }
        $setting->save();

        $this->helper->one_time_message('success', __('Payout Setting Updated Successfully!'));
        return back();
    }

    public function payoutSettingDestroy(Request $request)
    {
        $id = $request->id;
        //used auth to verify payout of auth user
        $payout = auth()->user()->payoutSettings->where('id', $id)->first();
        $payout->delete();

        $this->helper->one_time_message('success', __('Payout Setting Deleted Successfully!'));
        return back();
    }
    //Payout Setting ends

    //Payout - starts
    // public function withdrawalCreate(Request $request)
    // {
    //     setActionSession();
    //     $data['menu'] = 'withdrawal';

    //     if (!$_POST)
    //     {
    //         $transaction      = new Transaction();
    //         $status = 'all';
    //         $type   = 'all';
    //         $wallet = 'all';
            
    //         $data['from'] = $from = null;
    //         $data['to']   = $to = null;
            
    //         $data['requested_for_withdrawals'] = SalesWithdrawal::where(['user_id' => auth()->user()->id])->get(['unique_order_id']);
    
    //         $data['transactions'] = $transaction->getSalesTransactions($from, $to, $type, $wallet, $status, $data['requested_for_withdrawals']);
    //         $data['status']       = $status;
    //         $data['payment_methods'] = $payment_methods = PayoutSetting::with(['paymentMethod:id,name'])
    //             ->where(['user_id' => auth()->user()->id])
    //             ->get(['id', 'type', 'email', 'account_name', 'account_number', 'bank_name']);
    //         // dd($payment_methods);

    //         $data['defaultCurrency'] = Wallet::where('user_id', auth()->user()->id)->where('is_default', 'Yes')->first(['id', 'currency_id']);

    //         //check Decimal Thousand Money Format Preference
    //         $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);
            
    //         // return $dataTable->with('user_id', auth()->user()->id)->render('user_dashboard.withdrawal.create', $data); //passing $id to dataTable ass user_id

    //         return view('user_dashboard.withdrawal.create', $data);
    //     }
    //     else
    //     {
    //         // dd($request->all());
    //         $rules = array(
    //             'amount'            => 'required|numeric|check_wallet_balance',
    //             'payout_setting_id' => 'required',
    //             'currency_id'       => 'required',
    //         );
    //         $fieldNames = array(
    //             'amount'            => 'Amount',
    //             'payout_setting_id' => 'Payment method',
    //             'currency_id'       => 'Currency',
    //         );

    //         $validator = Validator::make($request->all(), $rules);
    //         $validator->setAttributeNames($fieldNames);

    //         if ($validator->fails())
    //         {
    //             return back()->withErrors($validator)->withInput();
    //         }
    //         else
    //         {
    //             //backend validation starts
    //             $request['transaction_type_id'] = Withdrawal;
    //             $myResponse                     = $this->withdrawalAmountLimitCheck($request);
    //             // dd($myResponse->getData()->success->status);
    //             if ($myResponse)
    //             {
    //                 if ($myResponse->getData()->success->status == 200)
    //                 {
    //                     if ($myResponse->getData()->success->totalAmount > $myResponse->getData()->success->balance)
    //                     {
    //                         return back()->withErrors(__("Not have enough balance !"))->withInput();
    //                     }
    //                 }
    //                 elseif ($myResponse->getData()->success->status == 401)
    //                 {
    //                     return back()->withErrors($myResponse->getData()->success->message)->withInput();
    //                 }
    //             }
    //             //backend valdation ends

    //             $wallet = Wallet::with(['currency:id,symbol'])->where(['user_id' => auth()->user()->id, 'currency_id' => $request->currency_id])->first(['currency_id']);
    //             if ($wallet)
    //             {
                    
    //                 $data['transInfo']['currSymbol'] = $wallet->currency->symbol;
    //                 $data['transInfo']['amount']     = $request->amount;

    //                 $feesInfo = FeesLimit::where(['transaction_type_id' => Withdrawal, 'currency_id' => $wallet->currency_id, 'payment_method_id' => $request->payment_method_id])
    //                     ->first(['charge_percentage', 'charge_fixed']);
    //                 // dd($feesInfo);
                    
    //                 if($request->withdraw_source == 'shop'){
    //                     $feesPercentage            = 0;
    //                     $feesFixed                 = 0;
    //                     $transactions_to_withdraw  = $request->transactions_list;
    //                     $transactions_order        = $request->transactions_order_list;
    //                 } else {
    //                     $feesPercentage            = $feesInfo->charge_percentage;
    //                     $feesFixed                 = $feesInfo->charge_fixed;
    //                     $transactions_to_withdraw  = null;
    //                     $transactions_order        = null;
    //                 }

    //                 $percentageCalc = $request->amount * ($feesPercentage / 100); // $request->amount * ($feesInfo->charge_percentage / 100);
    //                 $fee            = $percentageCalc + $feesFixed; // $percentageCalc + $feesInfo->charge_fixed;

    //                 $data['transInfo']['fee']            = $fee;
    //                 $data['transInfo']['totalAmount']    = $request->amount + $fee;
    //                 $data['transInfo']['payout_setting'] = $payout_setting = PayoutSetting::find($request->payout_setting_id);
    //                 // dd($payout_setting->paymentMethod->name);

    //                 //saving in sessions
    //                 $withdrawalData['payout_setting_id']   = $request->payout_setting_id;
    //                 $withdrawalData['currency_id']         = $request->currency_id;
    //                 $withdrawalData['totalAmount']         = $request->amount + $fee;
    //                 $withdrawalData['amount']              = $request->amount;
    //                 $withdrawalData['payment_method_info'] = $request->payment_method_info;
    //                 $withdrawalData['payment_method_id']   = $request->payment_method_id;
    //                 $withdrawalData['transactions_to_withdraw']   = $transactions_to_withdraw;
    //                 $withdrawalData['transactions_order']   = $transactions_order;
    //                 session(['withdrawalData' => $withdrawalData]);

    //                 return view('user_dashboard.withdrawal.confirmation', $data);
    //             }
    //         }
    //     }
    // }
    
    public function withdrawalCreate(Request $request)
    {
        setActionSession();
        $data['menu'] = 'withdrawal';

        if (!$_POST)
        {
            $data['payment_methods'] = $payment_methods = PayoutSetting::with(['paymentMethod:id,name'])
                ->where(['user_id' => auth()->user()->id])
                ->get(['id', 'type', 'email', 'account_name', 'account_number', 'bank_name']);
            // dd($payment_methods);

            $data['defaultCurrency'] = Wallet::where('user_id', auth()->user()->id)->where('is_default', 'Yes')->first(['id', 'currency_id']);

            //check Decimal Thousand Money Format Preference
            $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);

            return view('user_dashboard.withdrawal.create', $data);
        }
        else
        {
            // dd($request->all());
            $rules = array(
                'amount'            => 'required|numeric|check_wallet_balance',
                'payout_setting_id' => 'required',
                'currency_id'       => 'required',
            );
            $fieldNames = array(
                'amount'            => 'Amount',
                'payout_setting_id' => 'Payment method',
                'currency_id'       => 'Currency',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                //backend validation starts
                $request['transaction_type_id'] = Withdrawal;
                $myResponse                     = $this->withdrawalAmountLimitCheck($request);
                // dd($myResponse->getData()->success->status);
                if ($myResponse)
                {
                    if ($myResponse->getData()->success->status == 200)
                    {
                        if ($myResponse->getData()->success->totalAmount > $myResponse->getData()->success->balance)
                        {
                            return back()->withErrors(__("Not have enough balance !"))->withInput();
                        }
                    }
                    elseif ($myResponse->getData()->success->status == 401)
                    {
                        return back()->withErrors($myResponse->getData()->success->message)->withInput();
                    }
                }
                //backend valdation ends

                $wallet = Wallet::with(['currency:id,symbol'])->where(['user_id' => auth()->user()->id, 'currency_id' => $request->currency_id])->first(['currency_id']);
                if ($wallet)
                {
                    $data['transInfo']['currSymbol'] = $wallet->currency->symbol;
                    $data['transInfo']['amount']     = $request->amount;

                    $feesInfo = FeesLimit::where(['transaction_type_id' => Withdrawal, 'currency_id' => $wallet->currency_id, 'payment_method_id' => $request->payment_method_id])
                        ->first(['charge_percentage', 'charge_fixed']);
                    // dd($feesInfo);

                    $percentageCalc = $request->amount * ($feesInfo->charge_percentage / 100);
                    $fee            = $percentageCalc + $feesInfo->charge_fixed;

                    $data['transInfo']['fee']            = $fee;
                    // $data['transInfo']['totalAmount']    = $request->amount + $fee;
                    $data['transInfo']['totalAmount']    = $request->amount;

                    $data['transInfo']['payout_setting'] = $payout_setting = PayoutSetting::find($request->payout_setting_id);
                    // dd($payout_setting->paymentMethod->name);

                    //saving in sessions
                    $withdrawalData['payout_setting_id']   = $request->payout_setting_id;
                    $withdrawalData['currency_id']         = $request->currency_id;
                    // $withdrawalData['totalAmount']         = $request->amount + $fee;
                    $withdrawalData['totalAmount']         = $request->amount;

                    $withdrawalData['amount']              = $request->amount;
                    $withdrawalData['payment_method_info'] = $request->payment_method_info;
                    $withdrawalData['payment_method_id']   = $request->payment_method_id;
                    session(['withdrawalData' => $withdrawalData]);

                    return view('user_dashboard.withdrawal.confirmation', $data);
                }
            }
        }
    }

    //get Withdrawal FeesLimits Active Currencies
    public function getWithdrawalFeesLimitsActiveCurrencies(Request $request)
    {
        $payment_met_id      = $request->payment_method_id;
        $transaction_type_id = $request->transaction_type_id;

        $wallets = Wallet::where(['user_id' => auth()->user()->id])->whereHas('active_currency', function ($q) use ($payment_met_id, $transaction_type_id)
        {
            $q->whereHas('fees_limit', function ($query) use ($payment_met_id, $transaction_type_id)
            {
                $query->where('has_transaction', 'Yes')->where('transaction_type_id', $transaction_type_id)->where('payment_method_id', $payment_met_id);
            });
        })
            ->with(['active_currency:id,code', 'active_currency.fees_limit:id,currency_id']) //Optimized
            ->get(['currency_id', 'is_default']);
        // dd($wallets);

        $arr        = [];
        $currencies = $wallets->map(function ($wallet) //map acts as foreach but we can customize the index as preferred
            {
                $arr['id']             = $wallet->active_currency->id;
                $arr['code']           = $wallet->active_currency->code;
                $arr['default_wallet'] = $wallet->is_default;
                return $arr;
            });
        // dd($currencies);
        $success['currencies'] = $currencies;
        return response()->json(['success' => $success]);
    }

    //Code for withdrawal Amount Limit Check
    public function withdrawalAmountLimitCheck(Request $request)
    {
        // dd($request->all());
        
        $withdraw_source = $request->withdraw_source;

        $amount      = $request->amount;
        $user_id     = auth()->user()->id;
        $feesDetails = FeesLimit::where(['transaction_type_id' => $request->transaction_type_id, 'payment_method_id' => $request->payment_method_id, 'currency_id' => $request->currency_id])
            ->first(['max_limit', 'min_limit', 'charge_percentage', 'charge_fixed']);
        // dd($feesDetails);

        if (@$feesDetails->max_limit == null)
        {
            if ((@$amount < @$feesDetails->min_limit))
            {
                $success['message'] = __('Minimum amount ') . formatNumber($feesDetails->min_limit);
                $success['status']  = '401';
            }
            else
            {
                $success['status'] = 200;
            }
        }
        else
        {
            if ((@$amount < @$feesDetails->min_limit) || (@$amount > @$feesDetails->max_limit))
            {
                $success['message'] = __('Minimum amount ') . formatNumber($feesDetails->min_limit) . __(' and Maximum amount ') . formatNumber($feesDetails->max_limit);
                $success['status']  = '401';
            }
            else
            {
                $success['status'] = 200;
            }
        }
        //Code for Amount Limit ends here

        //Code for Fees Limit Starts here
        if (empty($feesDetails))
        {
            $feesPercentage            = 0;
            $feesFixed                 = 0;
            $totalFess                 = $feesPercentage + $feesFixed;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['totalFees']      = $totalFess;
            $success['totalHtml']      = formatNumber($totalFess);
            $success['totalAmount']    = $totalAmount;
            $success['pFees']          = $feesPercentage;
            $success['fFees']          = $feesFixed;
            $success['pFeesHtml']      = formatNumber($feesPercentage);
            $success['fFeesHtml']      = formatNumber($feesFixed);
            $success['min']            = 0;
            $success['max']            = 0;
            $success['balance']        = 0;
        }
        else
        {
            
            if($withdraw_source == 'shop'){
                $feesPercentage            = 0;
                $feesFixed                 = 0;
            } else {
                $feesPercentage            = $amount * ($feesDetails->charge_percentage / 100);
                $feesFixed                 = $feesDetails->charge_fixed;
            }
        
            $feesPercentage            = $feesPercentage; // $amount * ($feesDetails->charge_percentage / 100);
            $feesFixed                 = $feesFixed; // $feesDetails->charge_fixed;
            $totalFess                 = $feesPercentage + $feesFixed;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['totalFees']      = $totalFess;
            $success['totalHtml']      = formatNumber($totalFess);
            $success['totalAmount']    = $totalAmount;
            $success['pFees']          = $feesPercentage; // $feesDetails->charge_percentage;
            $success['fFees']          = $feesFixed; // $feesDetails->charge_fixed;
            $success['pFeesHtml']      = formatNumber($feesPercentage); // formatNumber($feesDetails->charge_percentage);
            $success['fFeesHtml']      = formatNumber($feesFixed); // formatNumber($feesDetails->charge_fixed);
            $success['min']            = $feesDetails->min_limit;
            $success['max']            = $feesDetails->max_limit;
            $wallet                    = Wallet::where(['currency_id' => $request->currency_id, 'user_id' => $user_id])->first(['balance']);
            $success['balance']        = @$wallet->balance ? @$wallet->balance : 0;
        }
        return response()->json(['success' => $success]);
    }

    // public function withdrawalConfirmation(Request $request)
    // {
    //     // dd($request->all());

    //     $sessionValue = Session::get('withdrawalData');
    //     if (empty($sessionValue))
    //     {
    //         return redirect('payout');
    //     }

    //     actionSessionCheck();

    //     $user_id             = auth()->user()->id;
    //     $uuid                = unique_code();
    //     $payout_setting_id   = $sessionValue['payout_setting_id'];
    //     $currency_id         = $sessionValue['currency_id'];
    //     $totalAmount         = $sessionValue['totalAmount'];
    //     $amount              = $sessionValue['amount'];
    //     $payment_method_info = $sessionValue['payment_method_info'];
    //     $transactions_to_withdraw = $sessionValue['transactions_to_withdraw'];
    //     $transactions_order  = $sessionValue['transactions_order'];
    //     $payment_method_id   = $sessionValue['payment_method_id']; //new
    //     $payoutSetting       = $this->helper->getPayoutSettingObject(['paymentMethod:id'], ['id' => $payout_setting_id], ['*']);
    //     $wallet              = $this->helper->getUserWallet(['currency:id,symbol'], ['user_id' => $user_id, 'currency_id' => $currency_id], ['id', 'balance', 'currency_id']);
    //     $feeInfo             = $this->helper->getFeesLimitObject([], Withdrawal, $wallet->currency_id, $payment_method_id, null, ['charge_percentage', 'charge_fixed']);
    //     $feePercentage       = $amount * (@$feeInfo->charge_percentage / 100); //correct calc
    //     $arr                 = [
    //         'user_id'             => $user_id,
    //         'wallet'              => $wallet,
    //         'currency_id'         => $wallet->currency_id,
    //         'payment_method_id'   => $payoutSetting->paymentMethod->id,
    //         'payoutSetting'       => $payoutSetting,
    //         'uuid'                => $uuid,
    //         'percentage'          => $feeInfo->charge_percentage,
    //         'charge_percentage'   => $feePercentage,
    //         'charge_fixed'        => $feeInfo->charge_fixed,
    //         'amount'              => $amount,
    //         'totalAmount'         => $totalAmount,
    //         'subtotal'            => $amount - ($feePercentage + $feeInfo->charge_fixed),
    //         'payment_method_info' => $payment_method_info,
    //         'transactions_to_withdraw' => $transactions_to_withdraw,
    //         'transactions_order' => $transactions_order
    //     ];
    //     $data['currencySymbol'] = $wallet->currency->symbol;
    //     $data['amount']         = $arr['subtotal'];

    //     //Get response
    //     $response = $this->withdrawal->processPayoutMoneyConfirmation($arr, 'web');
    //     if ($response['status'] != 200)
    //     {
    //         if (empty($response['withdrawalTransactionId']))
    //         {
    //             Session::forget('withdrawalData');
    //             $this->helper->one_time_message('error', $response['ex']['message']);
    //             return redirect('payout');
    //         }
    //         // $data['errorMessage'] = $response['ex']['message'];
    //     }
    //     $data['transactionId'] = $response['withdrawalTransactionId'];

    //     //clear session
    //     Session::forget('withdrawalData');
    //     clearActionSession();
    //     return view('user_dashboard.withdrawal.success', $data);
    // }
    
    public function withdrawalConfirmation(Request $request)
    {
        // dd($request->all());

        $sessionValue = Session::get('withdrawalData');
        if (empty($sessionValue))
        {
            return redirect('payout');
        }

        actionSessionCheck();

        $user_id             = auth()->user()->id;
        $uuid                = unique_code();
        $payout_setting_id   = $sessionValue['payout_setting_id'];
        $currency_id         = $sessionValue['currency_id'];
        $totalAmount         = $sessionValue['totalAmount'];
        $amount              = $sessionValue['amount'];
        $payment_method_info = $sessionValue['payment_method_info'];
        $payment_method_id   = $sessionValue['payment_method_id']; //new
        $payoutSetting       = $this->helper->getPayoutSettingObject(['paymentMethod:id'], ['id' => $payout_setting_id], ['*']);
        $wallet              = $this->helper->getUserWallet(['currency:id,symbol'], ['user_id' => $user_id, 'currency_id' => $currency_id], ['id', 'balance', 'currency_id']);
        $feeInfo             = $this->helper->getFeesLimitObject([], Withdrawal, $wallet->currency_id, $payment_method_id, null, ['charge_percentage', 'charge_fixed']);
        $feePercentage       = $amount * (@$feeInfo->charge_percentage / 100); //correct calc
        $arr                 = [
            'user_id'             => $user_id,
            'wallet'              => $wallet,
            'currency_id'         => $wallet->currency_id,
            'payment_method_id'   => $payoutSetting->paymentMethod->id,
            'payoutSetting'       => $payoutSetting,
            'uuid'                => $uuid,
            'percentage'          => $feeInfo->charge_percentage,
            'charge_percentage'   => $feePercentage,
            'charge_fixed'        => $feeInfo->charge_fixed,
            'amount'              => $amount,
            'totalAmount'         => $totalAmount,
            'subtotal'            => $amount - ($feePercentage + $feeInfo->charge_fixed),
            'payment_method_info' => $payment_method_info,
        ];
        $data['currencySymbol'] = $wallet->currency->symbol;
        $data['amount']         = $arr['subtotal'];
        
        // Check Fraud
        $pending_transaction                           = new PendingTransaction();
        $pending_transaction->user_id                  = $user_id;
        $pending_transaction->currency_id              = $arr['currency_id'];
        $pending_transaction->payment_method_id        = $arr['payment_method_id'];
        $pending_transaction->transaction_reference_id = $arr['uuid'];
        $pending_transaction->transaction_type_id      = Withdrawal;
        $pending_transaction->uuid                     = $arr['uuid'];;
        $pending_transaction->subtotal                 = $arr['subtotal'];
        $pending_transaction->percentage               = $arr['percentage'];
        $pending_transaction->charge_percentage        = $arr['charge_percentage'];
        $pending_transaction->charge_fixed             = $arr['charge_fixed'];
        $pending_transaction->total                    = $arr['totalAmount'];
        $pending_transaction->ip_address               = request()->ip();
        $pending_transaction->status                   = 'Pending';
        $pending_transaction->save();
        
        $response_fraud = $this->helper->check_fraud($pending_transaction->id);
        if(!empty($response_fraud->id)){
                    
            if(!empty($response_fraud->transactions_hour)){
                $message = 'You have exceed allowed number of transactions per hour.';
            }elseif(!empty($response_fraud->transactions_day)){
                $message = 'You have exceed allowed number of transactions per day.';
            }elseif(!empty($response_fraud->amount_hour)){
                $message = 'You have exceed allowed amount limit per Hour.';
            }elseif(!empty($response_fraud->amount_day)){
                $message = 'You have exceed allowed amount limit per Day.';
            }elseif(!empty($response_fraud->amount_week)){
                $message = 'You have exceed allowed amount limit per Week.';
            }elseif(!empty($response_fraud->amount_month)){
                $message = 'You have exceed allowed amount limit per Month.';
            }elseif(!empty($response_fraud->same_amount)){
                $message = 'You transaction is rejected due to repeating same amount multiple times.';
            }elseif(!empty($response_fraud->email_day)){
                $message = 'You transaction is rejected due to repeat transactions on same account.';
            }elseif(!empty($response_fraud->ipadd_day)){
                $message = 'You transaction is rejected due to repeat transactions on same IP.';
            }elseif(!empty($response_fraud->user_created_at)){
                $message = 'You transaction is rejected as per new account limitations. Please try after some days.';
            }
            
            $delete_trans = PendingTransaction::where('id', $pending_transaction->id)->delete();
        
            Session::forget('withdrawalData');
            $this->helper->one_time_message('error', $message);
            return redirect('payout');
        }

        //Get response
        $response = $this->withdrawal->processPayoutMoneyConfirmation($arr, 'web');
        if ($response['status'] != 200)
        {
            if (empty($response['withdrawalTransactionId']))
            {
                Session::forget('withdrawalData');
                $this->helper->one_time_message('error', $response['ex']['message']);
                return redirect('payout');
            }
            // $data['errorMessage'] = $response['ex']['message'];
        }
        $data['transactionId'] = $response['withdrawalTransactionId'];

        //clear session
        Session::forget('withdrawalData');
        clearActionSession();
        return view('user_dashboard.withdrawal.success', $data);
    }

    public function withdrawalPrintPdf($trans_id)
    {
        $data['companyInfo']        = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);
        $data['transactionDetails'] = Transaction::with(['payment_method:id,name', 'currency:id,symbol'])
            ->where(['id' => $trans_id])
            ->first(['uuid', 'created_at', 'status', 'currency_id', 'payment_method_id', 'subtotal', 'charge_percentage', 'charge_fixed', 'total']);
        // dd($data['transactionDetails']);

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('user_dashboard.withdrawal.withdrawalPaymentPdf', $data));
        $mpdf->Output('sendMoney_' . time() . '.pdf', 'I'); //
    }
    //Payout - ends
}
