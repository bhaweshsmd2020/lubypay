<?php

namespace App\Http\Controllers\Admin;
use App\DataTables\Admin\PhotoProofsDataTable;
use App\Models\Bank;
use App\Models\PayoutSetting;
use App\DataTables\Admin\AdminsDataTable;
use App\DataTables\Admin\EachUserTransactionsDataTable;
use App\DataTables\Admin\UsersDataTable;
use App\DataTables\Admin\MerchantlistDataTable;
use App\DataTables\Admin\StorelistDataTable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Dispute;
use App\Models\EmailTemplate;
use App\Models\FeesLimit;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WithdrawalDetail;
use App\Models\SalesWithdrawal;
use App\Models\Store;
use App\Models\DocumentVerification;
use App\Repositories\CryptoCurrencyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\UserDetail;
use App\Models\Country;
use App\Models\CountryBank;
use Image;
use App\Models\WalletPayment;
use App\Models\CardPayment;
use App\Models\Card;
use App\Models\CardTopup;
use App\Models\CardTransaction;
use App\Models\CardFee;

class CardController extends Controller
{
    protected $helper;
    protected $email;
    protected $currency;
    protected $user;
    /**
     * The CryptoCurrency repository instance.
     *
     * @var CryptoCurrencyRepository
     */
    protected $cryptoCurrency;

    public function __construct()
    {
        $this->helper         = new Common();
        $this->email          = new EmailController();
        $this->currency       = new Currency();
        $this->user           = new User();
    }

    public function partner()
    {
        $data['menu']     = 'cards';
        $data['sub_menu'] = 'partner';
        
        $general      = Setting::where('type', 'cards')->get()->toArray();
        $data['card'] = $this->helper->key_value('name', 'value', $general);
        return view('admin.cards.config', $data);
    }
    
    public function card_requests()
    {
        $data['menu']     = 'cards';
        $data['sub_menu'] = 'requests';
        
        $cards = Card::get();
        
        $data['cards'] = $cards;
        return view('admin.cards.cardholders', $data);
    }
    
    public function partner_update(Request $request)
    {
        $rules = array(
            'partner_id' => 'required',
            'card_url'  => 'required',
            'card_key'  => 'required',
            'card_secret'  => 'required',
        );

        $fieldNames = array(
            'partner_id' => 'Partner ID',
            'card_url'   => 'Card URL',
            'card_key'   => 'Card Key',
            'card_secret'=> 'Card Secret',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        try
        {
            Setting::where(['name' => 'partner_id'])->update(['value' => $request->partner_id]);
            Setting::where(['name' => 'card_url'])->update(['value' => $request->card_url]);
            Setting::where(['name' => 'card_key'])->update(['value' => $request->card_key]);
            Setting::where(['name' => 'card_secret'])->update(['value' => $request->card_secret]);

            $this->helper->one_time_message('success', 'Partner Settings Updated Successfully');
            return redirect('admin/partner');
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect('admin/users');
        }
    }
    
    public function reloads()
    {
        $data['menu']     = 'cards';
        $data['sub_menu'] = 'reloads';
        
        $data['transactions'] = CardTopup::orderBy('id', 'desc')->get();
        return view('admin.cards.reloads', $data);
    }
    
    public function reload_details($id)
    {
        $data['menu']     = 'cards';
        $data['sub_menu'] = 'reloads';
        
        $data['transaction'] = WalletPayment::where('id', $id)->first();
        return view('admin.cards.reloaddetails', $data);
    }
    
    public function transfers()
    {
        $data['menu']     = 'cards';
        $data['sub_menu'] = 'transfers';
        
        $data['transactions'] = CardTransaction::where('type', 'outcome')->orderBy('id', 'desc')->get();
        return view('admin.cards.transfers', $data);
    }
    
    public function transfer_details($id)
    {
        $data['menu']     = 'cards';
        $data['sub_menu'] = 'transfers';
        
        $data['transaction'] = CardPayment::where('id', $id)->first();
        return view('admin.cards.transferdetails', $data);
    }
    
    public function subscriptions()
    {
        $data['menu']     = 'cards';
        $data['sub_menu'] = 'subscriptions';
        
        $data['subscriptions'] = User::whereNotNull('plan_id')->orderBy('id', 'desc')->get();
        return view('admin.cards.subscriptions', $data);
    }
    
    public function subscription_details($id)
    {
        $data['menu']     = 'cards';
        $data['sub_menu'] = 'subscriptions';
        
        $data['transaction'] = WalletPayment::where('id', $id)->first();
        return view('admin.cards.subscriptiondetails', $data);
    }
    
    public function fees()
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'fees';
        
        $data['fee'] = CardFee::where('id', '1')->first();
        return view('admin.cards.fees', $data);
    }
    
    public function fees_update(Request $request)
    {
        $rules = array(
            'min_limit' => 'required',
            'max_limit'  => 'required',
            'billing_info'  => 'required',
            'recommended_amount'  => 'required'
        );

        $fieldNames = array(
            'min_limit' => 'Minimum Limit',
            'max_limit'   => 'Maximum Limit',
            'billing_info'=> 'Billing Info',
            'recommended_amount'=> 'Recommended Amount',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        try
        {
            CardFee::where('id', '1')->update([
                'min_limit' => $request->min_limit,
                'max_limit' => $request->max_limit,
                'billing_info' => $request->billing_info,
                'recommended_amount' => $request->recommended_amount,
            ]);

            $this->helper->one_time_message('success', 'Card Settings Updated Successfully');
            return redirect('admin/card/fees');
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect('admin/card/fees');
        }
    }
}
