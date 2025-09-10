<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\Country;
use App\Models\CryptoapiLog;
use App\Models\DeviceLog;
use App\Models\DocumentVerification;
use App\Models\EmailTemplate;
use App\Models\File;
use App\Models\Merchant;
use App\Models\MerchantBusinessDetail;
use App\Models\MerchantDocument;
use App\Models\MerchantGroup;
use App\Models\MerchantGroupDocument;
use App\Models\MerchantPackages;
use App\Models\Preference;
use App\Models\TimeZone;
use App\Models\QrCode;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Notification;
use App\Models\UserDetail;
use App\Models\Wallet;
use App\Models\Kycdatastore;
use App\Models\Order;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;

class WalletController extends Controller
{
    protected $helper;
    protected $twoFa;
    protected $email;
    protected $cryptoapiLog;
    protected $cryptoCurrency;

    
    public function dashboard()
    {
        // dd(session()->all());
        $data['menu']  = 'dashboard';
        $data['title'] = 'Dashboard';
        $transaction          = new Transaction();
        $data['transactions'] = $transaction->dashboardTransactionList();
        // dd($data['transactions']);
        $data['wallets'] = $wallets = Wallet::with('currency:id,type,logo,code,status')->where(['user_id' => Auth::user()->id])->orderBy('balance', 'ASC')->get(['id', 'currency_id', 'balance', 'is_default']);
        return view('user_dashboard.layouts.dashboard', $data);
    }

    public function wallet()
    {
        $data['menu']  = 'wallet';   
        $data['title'] = 'Wallet';

        $data['wallets'] = Wallet::with('currency:id,type,logo,code,status')
            ->where('user_id', Auth::id())
            ->orderBy('balance', 'ASC')
            ->paginate(10, ['id', 'user_id', 'currency_id', 'balance', 'is_default', 'created_at']);

        return view('user_dashboard.layouts.wallet', $data);
    }




}
