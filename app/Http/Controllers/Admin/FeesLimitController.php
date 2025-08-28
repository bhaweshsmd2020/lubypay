<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\FeesLimit;
use App\Models\PaymentMethod;
use App\Models\TransactionType;
use App\Models\Subscription;
use App\Models\ChargeRange;
use Illuminate\Http\Request;

class FeesLimitController extends Controller
{
    protected $helper;
    protected $currency;

    public function __construct()
    {
        $this->helper   = new Common();
        $this->currency = new Currency();
    }

    private function getAllowedTransactionTypes()
    {
        $permissions = PaymentMethod::whereNotNull('has_permission')->pluck('has_permission')->toArray();

        $types = [];
        foreach ($permissions as $permission) {
            $types = array_merge($types, explode(',', $permission));
        }

        return array_unique(array_map('strtolower', $types));
    }

    public function limitList($tab, $subs, $id)
    {
        $data['menu']       = 'subscriptions';     
        $data['list_menu']  = $tab;

        $transactionType = TransactionType::where('slug', $tab)->firstOrFail();
        $data['transaction_type'] = $transaction_type = $transactionType->id;
        $data['transaction_name'] = $transactionType->name;
        $data['trans_type'] = $transactionType->type;
        $data['transactionTypeList'] = TransactionType::where('status', '1')->orderBy('orderby', 'asc')->get();
        $data['currency']     = $this->currency->getCurrency(['id' => $id], ['id', 'default', 'name']);
        $data['currencyList'] = $this->currency->getAllCurrencies(['status' => 'Active', 'type' => 'fiat'], ['id', 'default', 'name']);
        $data['subscription'] = Subscription::where('id', $subs)->first();
        $data['subscriptionList'] = Subscription::where('status', '1')->get();
        $currency_id = $id;

        $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);

        $allowedTabs = $this->getAllowedTransactionTypes();

        if (in_array(strtolower($tab), $allowedTabs)) {
            $data['payment_methods'] = PaymentMethod::with(['fees_limit' => function ($q) use ($transaction_type, $currency_id, $subs) {
                $q->where('transaction_type_id', $transaction_type)
                ->where('currency_id', $currency_id)
                ->where('subscription_id', $subs);
            }])
            ->whereRaw("FIND_IN_SET(?, has_permission)", [$tab])
            ->where('status', 'Active')
            ->get();

            return view('admin.feeslimits.deposit_limit', $data);
        } else {
            $data['feeslimit'] = FeesLimit::where([
                'transaction_type_id' => $transaction_type,
                'currency_id'         => $currency_id,
                'subscription_id'     => $subs
            ])->first();

            return view('admin.feeslimits.deposit_limit_single', $data);
        }
    }

    public function updateDepositLimit(Request $request)
    {
        $allowedTabs = $this->getAllowedTransactionTypes();
        $tab = strtolower($request->tabText);

        if (in_array($tab, $allowedTabs)) {
            foreach ($request->payment_method_id as $key => $methodId) {
                $feeslimit = FeesLimit::firstOrNew([
                    'transaction_type_id' => $request->transaction_type_id,
                    'currency_id'         => $request->currency_id,
                    'subscription_id'     => $request->subscription_id,
                    'payment_method_id'   => $methodId,
                ]);

                $feeslimit->currency_id              = $request->currency_id;
                $feeslimit->transaction_type_id      = $request->transaction_type_id;
                $feeslimit->transaction_type         = $request->transaction_type ?? null;
                $feeslimit->has_transaction          = $request->defaultCurrency ? 'Yes' : ($request->has_transaction[$methodId] ?? 'No');
                $feeslimit->subscription_id          = $request->subscription_id ?? null;
                $feeslimit->payment_method_id        = $methodId;
                $feeslimit->min_balance              = $request->min_balance[$key] ?? 0;
                $feeslimit->description              = $request->description[$key] ?? null;
                $feeslimit->recom_amt                = $request->recom_amt[$key] ?? 0;
                $feeslimit->min_limit                = $request->min_limit[$key] ?? 0;
                $feeslimit->max_limit                = $request->max_limit[$key] ?? 0;
                $feeslimit->charge_percentage        = $request->charge_percentage[$key] ?? 0;
                $feeslimit->charge_fixed             = $request->charge_fixed[$key] ?? 0;
                $feeslimit->second_min_limit         = $request->second_min_limit[$key] ?? 0;
                $feeslimit->second_max_limit         = $request->second_max_limit[$key] ?? 0;
                $feeslimit->second_charge_percentage = $request->second_charge_percentage[$key] ?? 0;
                $feeslimit->second_charge_fixed      = $request->second_charge_fixed[$key] ?? 0;
                $feeslimit->card_limit               = $request->card_limit[$key] ?? 0;                
                $feeslimit->save();               
            }
        } else {
            $feeslimit = FeesLimit::firstOrNew([
                'transaction_type_id' => $request->transaction_type_id,
                'currency_id'         => $request->currency_id,
                'subscription_id'     => $request->subscription_id,
            ]);

            $feeslimit->currency_id              = $request->currency_id;
            $feeslimit->transaction_type_id      = $request->transaction_type_id;
            $feeslimit->transaction_type         = $request->transaction_type ?? null;
            $feeslimit->has_transaction          = $request->defaultCurrency ? 'Yes' : ($request->has_transaction ?? 'No');
            $feeslimit->subscription_id          = $request->subscription_id ?? null;
            $feeslimit->min_balance              = $request->min_balance ?? 0;
            $feeslimit->description              = $request->description ?? null;
            $feeslimit->recom_amt                = $request->recom_amt ?? 0;         
            $feeslimit->min_limit                = $request->min_limit ?? 0;
            $feeslimit->max_limit                = $request->max_limit ?? 0;
            $feeslimit->charge_percentage        = $request->charge_percentage ?? 0;
            $feeslimit->charge_fixed             = $request->charge_fixed ?? 0;
            $feeslimit->second_min_limit         = $request->second_min_limit ?? 0;
            $feeslimit->second_max_limit         = $request->second_max_limit ?? 0;
            $feeslimit->second_charge_percentage = $request->second_charge_percentage ?? 0;
            $feeslimit->second_charge_fixed      = $request->second_charge_fixed ?? 0;
            $feeslimit->card_limit               = $request->card_limit ?? 0;                
            $feeslimit->save();
        }

        return back()->with('success', __('Fees & limits updated successfully.'));
    }

    public function getFesslimitDetails(Request $request)
    {
        try {
            $subscription_id     = $request->subscription_id;
            $currency_id         = $request->currency_id;
            $transaction_type    = $request->transaction_type;
            $transaction_type_id = $request->transaction_type_id;
            $tab                 = strtolower($request->tab);

            $allowedTabs = $this->getAllowedTransactionTypes();

            $defaultFees = [
                'charge_percentage'        => "0.00",
                'charge_fixed'             => "0.00",
                'second_charge_percentage' => "0.00",
                'second_charge_fixed'      => "0.00",
                'min_limit'                => "1.00",
                'max_limit'                => "",
                'second_min_limit'         => "",
                'second_max_limit'         => "",
                'card_limit'               => "1",
                'min_balance'              => "1.00",
            ];

            $normalizeFees = function ($fees) use ($defaultFees) {
                if (!$fees) {
                    return $defaultFees;
                }

                foreach ($defaultFees as $key => $val) {
                    $fees->$key = $fees->$key ?? $val;
                }

                return $fees;
            };

            if (in_array($tab, $allowedTabs)) {
                $paymentMethods = PaymentMethod::with(['fees_limit' => function ($q) use ($transaction_type_id, $transaction_type, $currency_id, $subscription_id) {
                        $q->where('transaction_type_id', $transaction_type_id)
                        ->where('transaction_type', $transaction_type)
                        ->where('currency_id', $currency_id)
                        ->where('subscription_id', $subscription_id);
                    }])
                    ->whereRaw("FIND_IN_SET(?, has_permission)", [$tab])
                    ->where('status', 'Active')
                    ->get();

                $feeslimitArr = [];
                foreach ($paymentMethods as $method) {
                    $feeslimitArr[$method->id] = $normalizeFees($method->fees_limit);
                }

                return response()->json([
                    'status'              => 200,
                    'subscription_id'     => $subscription_id,
                    'transaction_type'    => $transaction_type,
                    'transaction_type_id' => $transaction_type_id,
                    'feeslimit'           => $feeslimitArr,
                ]);
            }else {
                $feeslimit = FeesLimit::where([
                    'transaction_type_id' => $transaction_type_id,
                    'transaction_type'    => $transaction_type,
                    'currency_id'         => $currency_id,
                    'subscription_id'     => $subscription_id,
                ])->first();

                return response()->json([
                    'status'              => 200,
                    'subscription_id'     => $subscription_id,
                    'transaction_type'    => $transaction_type,
                    'transaction_type_id' => $transaction_type_id,
                    'feeslimit'           => $normalizeFees($feeslimit),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSpecificCurrencyDetails(Request $request)
    {
        $data             = [];
        $transaction_type = $request->transaction_type;
        $currency_id      = $request->currency_id;

        if ($transaction_type == 1)
        {
            $feeslimit = PaymentMethod::with(['fees_limit' => function ($q) use ($transaction_type, $currency_id)
            {
                $q->where('transaction_type_id', '=', $transaction_type)->where('currency_id', '=', $currency_id);
            }])
                ->whereNotIn('id', [9]) // BlockIo
                ->where(['status' => 'Active'])
                ->get(['id', 'name']);
        }
        else if ($transaction_type == 2)
        {
            $feeslimit = PaymentMethod::with(['fees_limit' => function ($q) use ($transaction_type, $currency_id)
            {
                $q->where('transaction_type_id', '=', $transaction_type)->where('currency_id', '=', $currency_id);
            }])
                ->whereNotIn('id', [2, 4, 5, 7, 8, 9]) // ['Stripe', '2Checkout', 'PayUMoney', 'Coinpayments', 'Payeer', 'BlockIo'] respectively
                ->where(['status' => 'Active'])
                ->get(['id', 'name']);
        }
        else
        {
            $feeslimit = FeesLimit::where(['transaction_type_id' => $transaction_type, 'currency_id' => $currency_id])->first();
        }

        $currency = $this->currency->getCurrency(['id' => $currency_id], ['id', 'name', 'symbol']);
        if ($currency && $feeslimit)
        {
            $data['status']    = 200;
            $data['currency']  = $currency;
            $data['feeslimit'] = $feeslimit;
        }
        else
        {
            $data['status']   = 401;
            $data['currency'] = $currency;
        }
        return $data;
        exit();
    }
}
