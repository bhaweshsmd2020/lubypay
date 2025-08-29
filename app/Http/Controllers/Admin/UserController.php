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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\UserDetail;
use App\Models\Country;
use App\Models\CountryBank;
use Image;
use App\Models\StripeIntent;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;
use Illuminate\Support\Facades\Auth;
use App\Models\Card;
use App\Models\ApplyCard;
use App\Models\EmailConfig;
use App\Models\Device;

class UserController extends Controller
{
    protected $helper;
    protected $email;
    protected $currency;
    protected $user;
    protected $cryptoCurrency;

    public function __construct()
    {
        $this->helper         = new Common();
        $this->email          = new EmailController();
        $this->currency       = new Currency();
        $this->user           = new User();
        $this->transfer = new Transfer();
        $this->withdrawal = new Withdrawal();
        $this->documentVerification = new DocumentVerification();
        
        $setting = EmailConfig::first();
        $this->admin_email = $setting->notification_email;
    }
    
    public function postFunction($url, $headers, $payloads)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloads));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        
        $result = curl_exec($ch);
        if ($result === false) {
            dd([
                'error' => curl_error($ch),
                'info' => curl_getinfo($ch)
            ]);
        }
        curl_close($ch);
        $response = json_decode($result, true);
        return $response;
    }
    
    public function updatebiller(Request $request)
    {
        if($request->service_id == 2)
        {
            $insrt = DB::table('store_user_bills')->where('service_id',$request->service_id)->where('user_id',$request->user_id)->update([
                'bill_name'     => $request->bill_name,
                'account_num'   => $request->account_num,
                'meter_num'     => $request->meter_num,
                ]);
        }elseif($request->service_id == 1)
        {
             $insrt = DB::table('store_user_bills')->where('service_id',$request->service_id)->where('user_id',$request->user_id)->update([
                'bill_name'     => $request->bill_name,
                'account_num'   => $request->account_num,
                'number'        => $request->number,
                ]);
        }elseif($request->service_id == 4)
        {
            $insrt = DB::table('store_user_bills')->where('service_id',$request->service_id)->where('user_id',$request->user_id)->update([
            'bill_name'     => $request->bill_name,
            'account_num'   => $request->account_num,
            ]);
        }elseif($request->service_id == 3)
        {
             $insrt = DB::table('store_user_bills')->where('service_id',$request->service_id)->where('user_id',$request->user_id)->update([
            'bill_name'     => $request->bill_name,
            'account_num'   => $request->account_num,
            ]);
        }
        return redirect()->back()->with('success','Biller Update Successfully');
       
    }
    public function allstorebill($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        $data['biller']   = DB::table('store_user_bills')->where('user_id',$id)->get();
        return view('admin.users.biller.index', $data);
    }
    public function deleteBill($id)
    {
         $delete   = DB::table('store_user_bills')->where('id',$id)->delete();
         return redirect()->back()->with('success','Bill Delete Successfully');
    }
    public function index()
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        $data['users'] = User::where('role_id', 2)->with(['document_verification:id,user_id,status','role:id,display_name','user_detail:id,user_id,last_login_at,last_login_ip'])->latest()->select('users.*')->get();
        User::where('read_status', '0')->where('role_id', 2)->update(['read_status' => '1']);
        return view('admin.users.index', $data);
    }
    
    public function MerchantList()
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'merchant_list';
        $data['users'] = User::where('role_id',3)->with(['document_verification:id,user_id,status','role:id,display_name','user_detail:id,user_id,last_login_at,last_login_ip'])->latest()->select('users.*')->get();
        User::where('read_status', '0')->where('role_id', 3)->update(['read_status' => '1']);
        return view('admin.users.merchantlist', $data);
    }

    public function create()
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        $data['roles'] = Role::select('id', 'display_name')->where('user_type', "User")->get();
        $data['countries'] = Country::where('status', '1')->get();

        return view('admin.users.create', $data);
    }

    public function store(Request $request)
    {
        if ($_POST)
        {
            $rules = array(
                'first_name'            => 'required',
                'last_name'             => 'required',
                'email'                 => 'required',
                'password'              => 'required|confirmed',
                'password_confirmation' => 'required',
                'status'                => 'required',
            );

            $fieldNames = array(
                'first_name'            => 'First Name',
                'last_name'             => 'Last Name',
                'email'                 => 'Email',
                'password'              => 'Password',
                'password_confirmation' => 'Confirm Password',
                'status'                => 'Status',
            );
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                $check_email = User::where(['email' => $request->email, 'role_id' => $request->usertype])->exists();
                if ($check_email)
                {
                    return back()->withErrors(__("The email has already been taken!"))->withInput();
                }
                
                if (isset($request->carrierCode))
                {
                    $chec_phone = User::where(['phone' => $request->phone, 'carrierCode' => $request->carrierCode, 'role_id' => $request->usertype])->first();
                }
                else
                {
                    $chec_phone = User::where(['phone' => $request->phone, 'role_id' => $request->usertype])->first();
                }
        
                if ($chec_phone)
                {
                    return back()->withErrors(__("The phone number has already been taken!"))->withInput();
                }
                
                $default_currency = Setting::where('name', 'default_currency')->first(['value']);

                try
                {
                    \DB::beginTransaction();
                    
                    if($request->usertype == '2'){
                        $user_type = 'user';
                    }else{
                        $user_type = 'merchant';
                    }

                    // Create user
                    $user = $this->user->createNewUser($request, $user_type);
                    $user->address_verified  = '1';
                    $user->identity_verified = '1';
                    $user->photo_verified    = '1';
                    $user->video_verified    = '1';
                    $user->kyc_status    = '1';
                    $user->email_status = 1;
                    $user->phone_status = 1;
                    $user->save(); // Save the user instance
                    // Assigning user_type and role id to new user
                    RoleUser::insert(['user_id' => $user->id, 'role_id' => $user->role_id, 'user_type' => 'User']);

                    // Create user detail
                    $this->user->createUserDetail($request,$user->id);

                    // Create user's default wallet
                    $this->user->createUserDefaultWallet($user->id, $default_currency->value);

                    // // Create user's crypto wallet/wallets address
                    // $this->user->generateUserCryptoWalletAddress($user);

                    // // Create user's crypto wallet/wallets address
                    // $generateUserCryptoWalletAddress = $this->user->generateUserCryptoWalletAddress($user);
                    // if ($generateUserCryptoWalletAddress['status'] == 401)
                    // {
                    //     \DB::rollBack();
                    //     $this->helper->one_time_message('error', $generateUserCryptoWalletAddress['message']);
                    //     return redirect('admin/users');
                    // }

                    $userEmail          = $user->email;
                    $userFormattedPhone = $user->formattedPhone;

                    // Process Registered User Transfers
                    $this->user->processUnregisteredUserTransfers($userEmail, $userFormattedPhone, $user, $default_currency->value);

                    // Process Registered User Request Payments
                    $this->user->processUnregisteredUserRequestPayments($userEmail, $userFormattedPhone, $user, $default_currency->value);

                    // Email verification
                    if (!$user->user_detail->email_verification)
                    {
                        if (checkVerificationMailStatus() == "Enabled")
                        {
                            if (checkAppMailEnvironment())
                            {
                                $emainVerificationArr = $this->user->processUserEmailVerification($user);
                                try
                                {
                                    $this->email->sendEmail($emainVerificationArr['email'], $emainVerificationArr['subject'], $emainVerificationArr['message']);

                                    \DB::commit();
                                    $this->helper->one_time_message('success', 'An email has been sent to ' . $user->email . ' with verification code!');
                                    return redirect('admin/users');
                                }
                                catch (\Exception $e)
                                {
                                    \DB::rollBack();
                                    $this->helper->one_time_message('error', $e->getMessage());
                                    return redirect('admin/users');
                                }
                            }
                        }
                    }

                    \DB::commit();
                    $this->helper->one_time_message('success', 'User Created Successfully');
                    return redirect('admin/users');
                }
                catch (\Exception $e)
                {
                    \DB::rollBack();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect('admin/users');
                }
            }
        }
    }

    public function edit($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        $data['users'] = $users = User::find($id);
        $data['roles'] = $roles = Role::select('id', 'display_name')->where('user_type', "User")->get();
        $data['countries'] = Country::where('status', '1')->get();
        return view('admin.users.edit', $data);
    }
    
    public function address_edit($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_address';

        $data['users'] = $users = User::find($id);
        // dd($users);

        $data['roles'] = $roles = Role::select('id', 'display_name')->where('user_type', "User")->get();
             
        $data['users_details'] = $users_details = UserDetail::where('user_id',$id)->first();

        $data['countries']=DB::table('countries')->where('status',1)->get();

        return view('admin.users.address_edit', $data);
    }
    
    public function activity_logs($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        $data['logs'] = ActivityLog::where('user_id', $id)->where('type', 'User')->orderBy('id', 'desc')->get();
        $data['users'] = User::where('id', $id)->first();
        return view('admin.users.activitylogs', $data);
    }

    public function update(Request $request)
    {
        if ($_POST)
        {
            $rules = array(
                'first_name' => 'required',
                'last_name'  => 'required',
                'email'      => 'required',
            );

            $fieldNames = array(
                'first_name' => 'First Name',
                'last_name'  => 'Last Name',
                'email'      => 'Email',
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
                    
                    $check_email = User::where('email', $request->email)->where('role_id', $request->usertype)->where('id', '!=', $request->id)->exists();
                    if ($check_email)
                    {
                        return back()->withErrors(__("The email has already been taken!"))->withInput();
                    }
                    
                    if (isset($request->carrierCode))
                    {
                        $chec_phone = User::where('phone', $request->phone)->where('carrierCode', $request->carrierCode)->where('role_id', $request->usertype)->where('id', '!=', $request->id)->first();
                    }
                    else
                    {
                        $chec_phone = User::where('phone', $request->phone)->where('role_id', $request->usertype)->where('id', '!=', $request->id)->first();
                    }
            
                    if ($chec_phone)
                    {
                        return back()->withErrors(__("The phone number has already been taken!"))->withInput();
                    }
                    
                    $user             = User::find($request->id);
                    
                    $pic = $request->file('picture');
                    if (isset($pic))
                    {
                        $upload = 'public/user_dashboard/profile';
        
                        $pic1 = $request->picture;
        
                        if ($pic1 != null)
                        {
                            $dir = public_path("user_dashboard/profile/$pic1");
                            if (file_exists($dir))
                            {
                                unlink($dir);
                            }
                        }
                        $filename  = time() . '.' . $pic->getClientOriginalExtension();
        
                        $extension = strtolower($pic->getClientOriginalExtension());
                        if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp')
                        {
                            $pic = Image::make($pic->getRealPath());
                            $pic->resize(100, 100)->save($upload . '/' . $filename);
                        }
                        else
                        {
                            $this->helper->one_time_message('error', 'Invalid Image Format!');
                        }
                    }else{
                        $filename = $user->picture;
                    }
                    
                    $user->first_name = $request->first_name;
                    $user->last_name  = $request->last_name;
                    $user->middle_name  = $request->middle_name;
                    $user->second_last_name  = $request->second_last_name;
                    $user->email      = $request->email;
                    $user->role_id    = $request->usertype;
                    $user->email_status    = $request->email_status;
                    $user->phone_status    = $request->phone_status;
                    $user->picture    = $filename;
                    
                    if($request->kyc_status == '1'){
                        $user->address_verified  = '1';
                        $user->identity_verified = '1';
                        $user->photo_verified    = '1';
                        $user->video_verified    = '1';
                    }else{
                        $user->address_verified  = '0';
                        $user->identity_verified = '0';
                        $user->photo_verified    = '0';
                        $user->video_verified    = '0';
                    }

                    $formattedPhone = ltrim($request->phone, '0');
                    if (!empty($request->phone))
                    {
                        /*phone*/
                        $user->phone          = preg_replace("/[\s-]+/", "", $formattedPhone);
                        $user->defaultCountry = $request->user_defaultCountry;
                        $user->carrierCode    = $request->user_carrierCode;
                        $user->formattedPhone = '+'.$request->user_carrierCode.$request->phone;
                        /**/
                    }
                    else
                    {
                        $user->phone          = null;
                        $user->defaultCountry = null;
                        $user->carrierCode    = null;
                        $user->formattedPhone = null;
                    }

                    if (!is_null($request->password) && !is_null($request->password_confirmation))
                    {
                        $user->password = \Hash::make($request->password);
                    }

                    // Send mail to user for Status change
                    if ($request->status != $user->status)
                    {
                        //update user status
                        $user->status = $request->status;
                        $user->failed_attempt = '0';
                        
                        if($request->status == 'Active'){
                            StripeIntent::where('cus_id', $user->stripe_cus_id)->delete();
                        }

                        $userdevice = DB::table('devices')->where('user_id', $user->id)->first();
                        if(!empty($userdevice)){
                            $device_lang = $userdevice->language;
                        }else{
                            $device_lang = getDefaultLanguage();
                        }

                        $sender_info           = EmailTemplate::where(['temp_id' => 29, 'language_id' => $device_lang, 'type' => 'email'])->select('subject', 'body')->first();

                        $sender_subject = $sender_info->subject;
                        $sender_msg     = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $sender_info->body);
                        $sender_msg = str_replace('{status}', $user->status, $sender_msg);
                        $sender_msg = str_replace('{soft_name}', getCompanyName(), $sender_msg);
                        if (checkAppMailEnvironment())
                        {
                            try
                            {
                                $this->email->sendEmail($user->email, $sender_subject, $sender_msg);
                            }
                            catch (\Exception $e)
                            {
                                \DB::rollBack();
                                $this->helper->one_time_message('error', $e->getMessage());
                                return redirect('admin/users');
                            }
                        }
                    }
                    $user->status_reason = $request->status_reason;
                    $user->save();
                    
                    $userdevice = Device::where('user_id', $request->id)->first();
                    if(!empty($userdevice)){
                        $device_lang = $userdevice->language;
                    }else{
                        $device_lang = getDefaultLanguage();
                    }
                    
                    if($request->kyc_status == '1'){
                        
                        $url = env('BASE_URL').'user-status';
        
                        $headers = [
                            'Host: cards.lubypay.com',
                            'Content-Type: application/json',
                        ];
                        
                        $payloads = [
                            'user_email' => $user->email,
                            'platform' => 'Sandbox Ewallet'
                        ];
                        
                        $kyc = $this->postFunction($url, $headers, $payloads);
                        if(!empty($kyc['data']['card_user_status'])){
                            if($kyc['data']['card_user_status'] == 'INVITE_PENDING'){
                                $this->helper->one_time_message('success', 'Invite is in pending!');
                                return back();
                            }elseif($kyc['data']['card_user_status'] == 'INVITE_EXPIRED'){
                                $this->helper->one_time_message('success', 'Invite has expired!');
                                return back();
                            }elseif($kyc['data']['card_user_status'] == 'USER_ACTIVE'){
                                User::where('id', $request->id)->update([
                                    'card_user_id' => $kyc['data']['card_user_id'], 
                                    'card_user_status' => $kyc['data']['card_user_status'],
                                    'kyc_status' => '1',
                                    'kyc_verified_on' => now()->toDateTimeString()
                                ]);
                                
                                if(!empty($kyc['data']['plan_data'])){
                                    User::where('id', $request->id)->update([
                                        'plan_data' => $kyc['data']['plan_data'],
                                        'plan_id' => $kyc['data']['plan_id'],
                                        'plan_name' => $kyc['data']['plan_name'],
                                        'plan_amount' => $kyc['data']['plan_amount'],
                                        'will_expire' => $kyc['data']['will_expire']
                                    ]);
                                    
                                    $apply_card = ApplyCard::where('user_id', $request->id)->first();
                                    if(empty($apply_card)){
                                        $cardUser = User::find($request->id);
                                        $cardUserDetail = UserDetail::where('user_id', $request->id)->first();
                                        $cardUserCountry = Country::where('id', $cardUserDetail->country)->first();
                                        
                                        $card = ApplyCard::create([
                                            'user_id' => $request->id,
                                            'address_line' => $cardUserDetail->address_1,
                                            'city' => $cardUserDetail->city,
                                            'state' => $cardUserDetail->state,
                                            'country' => $cardUserCountry->name,
                                            'postal_code' => $cardUserDetail->zip_code
                                        ]);
                                        
                                        $cardurl = env('BASE_URL').'platform-cards';
                                        
                                        $cards = $this->postFunction($cardurl, $headers, $payloads);
                                        if(count($cards['data']) > 0){
                                            foreach($cards['data'] as $card){
                                                Card::create([
                                                    'user_id' => $request->id,
                                                    'invite_id' => $card['invite_id'],
                                                    'card_holder' => $card['name_on_card'],
                                                    'card_user_id' => $card['user_id'],
                                                    'card_id' => $card['card_id'],
                                                    'card' => $card['card'],
                                                    'last_four' => $card['last4'],
                                                    'type' => $card['type'],
                                                    'amount' => $card['available_limits'],
                                                    'card_number' => $card['full_card_number'],
                                                    'card_cvv' => $card['card_cvv'],
                                                    'expiry_month' => $card['exp_month'],
                                                    'expiry_year' => $card['exp_year'],
                                                    'currency' => $card['currency'],
                                                    'status' => 'success',
                                                    'card_status' => $card['status'],
                                                    'applied_from' => 'web'
                                                ]);
                                            }
                                        }
                                    }
                            
                                    $template = NotificationTemplate::where('temp_id', '40')->where('language_id', $device_lang)->first();
                                    $subject = $template->title;
                                    $subheader = $template->subheader;
                                    $message = $template->content;
                                    
                                    $this->helper->sendFirabasePush($subject, $message, $request->id, '9', 'push');
                                    
                                    Noticeboard::create([
                                        'tr_id' => null,
                                        'title' => $subject,
                                        'content' => $message,
                                        'type' => 'push',
                                        'content_type' => 'kyc',
                                        'user' => $request->id,
                                        'sub_header' => $subheader,
                                        'push_date' => $request->local_tran_time,
                                        'template' => '40',
                                        'language' => $device_lang
                                    ]);
                                }
                            }
                        }
                    }else{
                        User::where('id', $request->id)->update(['kyc_status' => $request->kyc_status]);
                    }
                    
                    $check_document = DocumentVerification::where('user_id', $request->id)->first();
                    if(!empty($check_document && $request->kyc_status != 0)){
                        if($request->kyc_status == '1'){
                            $document_status = 'approved';
                        }elseif($request->kyc_status == '2'){
                            $document_status = 'pending';
                        }elseif($request->kyc_status == '3'){
                            $document_status = 'rejected';
                        }
                        
                        DocumentVerification::where('user_id', $request->id)->update(['updated_by' => Auth::guard('admin')->user()->id, 'status' => $document_status]);
                        
                        $email_user = User::find($request->id);
                        
                        $identityVerificationEmailTemp = EmailTemplate::where(['temp_id' => 21, 'language_id' => $device_lang, 'type' => 'email'])->select('subject', 'body')->first();
                        $identityVerificationEmailSub  = str_replace('{identity/address/photo}', 'Account', $identityVerificationEmailTemp->subject);
                        $identityVerificationEmailBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $identityVerificationEmailTemp->body);
                        $identityVerificationEmailBody = str_replace('{identity/address/photo}', 'account', $identityVerificationEmailBody);
                        $identityVerificationEmailBody = str_replace('{approved/pending/rejected}', ucfirst($document_status), $identityVerificationEmailBody);
                        $identityVerificationEmailBody = str_replace('{soft_name}', getCompanyName(), $identityVerificationEmailBody);
                        $this->email->sendEmail($email_user->email, $identityVerificationEmailSub, $identityVerificationEmailBody);
                    }
                    
                    RoleUser::where(['user_id' => $request->id, 'user_type' => 'User'])->update(['role_id' => $request->usertype]); //by tuhin

                    \DB::commit();

                    $this->helper->one_time_message('success', 'User Updated Successfully');
                    return redirect()->back();
                }
                catch (\Exception $e)
                {
                    \DB::rollBack();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect('admin/users');
                }
            }
        }
    }
    
    public function address_update(Request $request)
    {
        if ($_POST)
        {

            $rules = array(
                'city' => 'required',
                'state'  => 'required',
                'country'      => 'required',
            );

           
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {

                try
                {
                    \DB::beginTransaction();
                    
                    $countries = Country::where('id',$request->country)->first();
                    $userupdate                = User::where('id',$request->id)->first();
                    $userupdate->defaultCountry=$countries->name;
                    $userupdate->save();
                    
                    $user             = UserDetail::where('user_id',$request->id)->first();
                    $user->city = $request->city;
                    $user->state  = $request->state;
                    $user->country  = $request->country;
                    $user->country_id  = $request->country;
                    $user->address_1  = $request->address_1;
                    $user->address_2  = $request->address_2;
                    $user->zip_code  = $request->zip_code;
                    
                    $user->save();

                    \DB::commit();

                    $this->helper->one_time_message('success', 'Address Updated Successfully');
                    return redirect('admin/users');
                }
                catch (\Exception $e)
                {
                    \DB::rollBack();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect('admin/users');
                }
            }
        }
    }

    /* Start of Admin Depsosit */
    public function eachUserDeposit($id, Request $request)
    {
        setActionSession();

        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        // $data['users']           = $users           = User::find($id);
        $data['users']           = $users           = User::find($id, ['id', 'first_name', 'last_name']);
        $data['payment_met']     = $payment_met     = PaymentMethod::where(['name' => 'Stripe', 'status' => 'Active'])->first(['id', 'name']);
        $data['active_currency'] = $activeCurrency = Currency::where(['status' => 'Active'])->get(['id', 'status', 'code']);
        if($payment_met) {
        $feesLimitCurrency       = FeesLimit::where(['transaction_type_id' => Deposit, 'payment_method_id' => $payment_met->id, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction']);
        } else {
        $feesLimitCurrency       = array(); 
        }
        // dd($feesLimitCurrency);
        $data['activeCurrencyList'] = $this->currencyList($activeCurrency, $feesLimitCurrency);

        //check Decimal Thousand Money Format Preference
        $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);

        if ($_POST)
        {
            $currency               = Currency::where(['id' => $request->currency_id, 'status' => 'Active'])->first(['symbol']);
            $request['currSymbol']  = $currency->symbol;
            $amount                 = $request->amount;
            $request['totalAmount'] = $amount + $request->fee;
            session(['transInfo' => $request->all()]);
            $data['transInfo'] = $transInfo = $request->all();

            //check amount and limit
            $feesDetails = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $request->currency_id, 'payment_method_id' => $transInfo['payment_method'], 'has_transaction' => 'Yes'])
                ->first(['min_limit', 'max_limit']);
            if (@$feesDetails->max_limit == null)
            {
                if ((@$amount < @$feesDetails->min_limit))
                {
                    $data['error'] = 'Minimum amount ' . formatNumber($feesDetails->min_limit);
                    $this->helper->one_time_message('error', $data['error']);
                    return view('admin.users.deposit.create', $data);
                }
            }
            else
            {
                if ((@$amount < @$feesDetails->min_limit) || (@$amount > @$feesDetails->max_limit))
                {
                    $data['error'] = 'Minimum amount ' . formatNumber($feesDetails->min_limit) . ' and Maximum amount ' . formatNumber($feesDetails->max_limit);
                    $this->helper->one_time_message('error', $data['error']);
                    return view('admin.users.deposit.create', $data);
                }
            }
            return view('admin.users.deposit.confirmation', $data);
        }
        return view('admin.users.deposit.create', $data);
    }

    //Extended function below - deposit
    public function currencyList($activeCurrency, $feesLimitCurrency)
    {
        //echo "<pre>"; print_r($feesLimitCurrency); die;
        $selectedCurrency = [];
        foreach ($activeCurrency as $aCurrency)
        {
            foreach ($feesLimitCurrency as $flCurrency)
            {
                if ($aCurrency->id == $flCurrency->currency_id && $aCurrency->status == 'Active' && $flCurrency->has_transaction == 'Yes')
                {
                    $selectedCurrency[$aCurrency->id]['id']   = $aCurrency->id;
                    $selectedCurrency[$aCurrency->id]['code'] = $aCurrency->code;
                }
            }
        }
        return $selectedCurrency;
    }
    /* End of Admin Depsosit */

    public function eachUserDepositSuccess(Request $request)
    {
        // dd($request->all());

        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        $user_id = $request->user_id;

        //Check Session - starts
        $sessionValue = session('transInfo');
        if (empty($sessionValue))
        {
            return redirect("admin/users/deposit/create/$user_id");
        }
        //Check Session - ends

        actionSessionCheck();

        $amount  = $sessionValue['amount'];
        $uuid    = unique_code();
        $feeInfo = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $sessionValue['currency_id'], 'payment_method_id' => $sessionValue['payment_method']])
            ->first(['charge_percentage', 'charge_fixed']);
        //charge percentage calculation
        $p_calc = (($amount) * (@$feeInfo->charge_percentage) / 100);

        try
        {
            \DB::beginTransaction();
            //Deposit
            $deposit                    = new Deposit();
            $deposit->user_id           = $user_id;
            $deposit->currency_id       = $sessionValue['currency_id'];
            $deposit->payment_method_id = $sessionValue['payment_method'];
            $deposit->uuid              = $uuid;
            $deposit->charge_percentage = @$feeInfo->charge_percentage ? $p_calc : 0;
            $deposit->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
            $deposit->amount            = $amount;
            $deposit->status            = 'Success';
            $deposit->save();

            //Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = $sessionValue['currency_id'];
            $transaction->payment_method_id        = $sessionValue['payment_method'];
            $transaction->transaction_reference_id = $deposit->id;
            $transaction->transaction_type_id      = Deposit;
            $transaction->uuid                     = $uuid;
            $transaction->subtotal                 = $amount;
            $transaction->percentage               = @$feeInfo->charge_percentage ? @$feeInfo->charge_percentage : 0;
            $transaction->charge_percentage        = $deposit->charge_percentage;
            $transaction->charge_fixed             = $deposit->charge_fixed;
            $transaction->total                    = $amount + $deposit->charge_percentage + $deposit->charge_fixed;
            $transaction->status                   = 'Success';
            $transaction->save();

            //Wallet
            $wallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $sessionValue['currency_id']])->first(['id', 'balance']);
            if (empty($wallet))
            {
                $createWallet              = new Wallet();
                $createWallet->user_id     = $user_id;
                $createWallet->currency_id = $sessionValue['currency_id'];
                $createWallet->balance     = $amount;
                $createWallet->is_default  = 'No';
                $createWallet->save();
            }
            else
            {
                $wallet->balance = ($wallet->balance + $amount);
                $wallet->save();
            }
            \DB::commit();

            $data['transInfo']['currSymbol'] = $transaction->currency->symbol;
            $data['transInfo']['subtotal']   = $transaction->subtotal;
            $data['transInfo']['id']         = $transaction->id;
            $data['user_id']                 = $user_id;
            $data['name']                    = $sessionValue['fullname'];

            Session::forget('transInfo');
            clearActionSession();
            return view('admin.users.deposit.success', $data);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            Session::forget('transInfo');
            clearActionSession();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect("admin/users/deposit/create/$user_id");
        }
    }

    public function eachUserdepositPrintPdf($transaction_id)
    {
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);

        $data['transactionDetails'] = $transactionDetails = Transaction::with(['payment_method:id,name', 'currency:id,symbol'])
            ->where(['id' => $transaction_id])
            ->first(['uuid', 'created_at', 'status', 'currency_id', 'payment_method_id', 'subtotal', 'charge_percentage', 'charge_fixed', 'total']);

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
        $mpdf->WriteHTML(view('admin.users.deposit.depositPrintPdf', $data));
        $mpdf->Output('deposit_' . time() . '.pdf', 'I'); //
    }

    /* Start of Admin Withdraw */
    public function eachUserWithdraw($id, Request $request)
    {
        setActionSession();

        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        // $data['users']       = $users       = User::find($id);
        $data['users']       = $users       = User::find($id, ['id', 'first_name', 'last_name']);
        $data['payment_met'] = $payment_met = PaymentMethod::where('name', 'LubyPay')->first(['id', 'name']);
        $payment_met_id      = $payment_met->id??'';
        $data['wallets']     = $wallets     = $users->wallets()->whereHas('active_currency', function ($q) use ($payment_met_id)
        {
            $q->whereHas('fees_limit', function ($query) use ($payment_met_id)
            {
                $query->where('has_transaction', 'Yes')->where('transaction_type_id', Withdrawal)->where('payment_method_id', $payment_met_id);
            });
        })
            ->with(['active_currency:id,code', 'active_currency.fees_limit:id,currency_id']) //Optimized
            ->get(['id', 'currency_id']);
        
        
        $data['payment_methods'] = $payment_methods = PayoutSetting::with(['paymentMethod:id,name'])
                ->where(['user_id' => $id])
                ->get(['id', 'type', 'email', 'account_name', 'account_number', 'bank_name']);
                
                
        $data['payment_method_id'] = $request->payment_method;
        $data['user_id'] =$id;
                
                
        //check Decimal Thousand Money Format Preference
        $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);

        if ($_POST)
        {
            $amount                 = $request->amount;
            $currency               = Currency::where(['id' => $request->currency_id])->first(['symbol']);
            $request['currSymbol']  = $currency->symbol??'MVR';
            $request['totalAmount'] = $request->amount + $request->fee;
            session(['transInfo' => $request->all()]);
            $data['transInfo'] = $transInfo = $request->all();

            //backend validation starts
            $request['transaction_type_id'] = Withdrawal;
            $request['currency_id']         = $request->currency_id;
            $request['payment_method_id']   = $request->payment_method;
            
           
            
            $amountFeesLimitCheck           = $this->amountFeesLimitCheck($request);
            
            if ($amountFeesLimitCheck)
            {
                if ($amountFeesLimitCheck->getData()->success->status == 200)
                {
                    if ($amountFeesLimitCheck->getData()->success->totalAmount > $amountFeesLimitCheck->getData()->success->balance)
                    {
                        $data['error'] = "Insufficient Balance!";
                        $this->helper->one_time_message('error', $data['error']);
                        return view('admin.users.withdraw.create', $data);
                    }
                }
                elseif ($amountFeesLimitCheck->getData()->success->status == 401)
                {
                    $data['error'] = $amountFeesLimitCheck->getData()->success->message;
                    $this->helper->one_time_message('error', $data['error']);
                    return view('admin.users.withdraw.create', $data);
                }
            }
            //backend valdation ends
            return view('admin.users.withdraw.confirmation', $data);
        }
        return view('admin.users.withdraw.create', $data);
    }
    
    public function eachUserWithdrawold($id, Request $request)
    {
        setActionSession();

        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        // $data['users']       = $users       = User::find($id);
        $data['users']       = $users       = User::find($id, ['id', 'first_name', 'last_name']);
        $data['payment_met'] = $payment_met = PaymentMethod::where(['name' => 'Mts'])->first(['id', 'name']);
        $payment_met_id      = $payment_met->id??'';
        $data['wallets']     = $wallets     = $users->wallets()->whereHas('active_currency', function ($q) use ($payment_met_id)
        {
            $q->whereHas('fees_limit', function ($query) use ($payment_met_id)
            {
                $query->where('has_transaction', 'Yes')->where('transaction_type_id', Withdrawal)->where('payment_method_id', $payment_met_id);
            });
        })
            ->with(['active_currency:id,code', 'active_currency.fees_limit:id,currency_id']) //Optimized
            ->get(['id', 'currency_id']);

        //check Decimal Thousand Money Format Preference
        $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);

        if ($_POST)
        {
            $amount                 = $request->amount;
            $currency               = Currency::where(['id' => $request->currency_id])->first(['symbol']);
            $request['currSymbol']  = $currency->symbol;
            $request['totalAmount'] = $request->amount + $request->fee;
            session(['transInfo' => $request->all()]);
            $data['transInfo'] = $transInfo = $request->all();

            //backend validation starts
            $request['transaction_type_id'] = Withdrawal;
            $request['currency_id']         = $request->currency_id;
            $request['payment_method_id']   = $request->payment_method;
            $amountFeesLimitCheck           = $this->amountFeesLimitCheck($request);
            if ($amountFeesLimitCheck)
            {
                if ($amountFeesLimitCheck->getData()->success->status == 200)
                {
                    if ($amountFeesLimitCheck->getData()->success->totalAmount > $amountFeesLimitCheck->getData()->success->balance)
                    {
                        $data['error'] = "Insufficient Balance!";
                        $this->helper->one_time_message('error', $data['error']);
                        return view('admin.users.withdraw.create', $data);
                    }
                }
                elseif ($amountFeesLimitCheck->getData()->success->status == 401)
                {
                    $data['error'] = $amountFeesLimitCheck->getData()->success->message;
                    $this->helper->one_time_message('error', $data['error']);
                    return view('admin.users.withdraw.create', $data);
                }
            }
            //backend valdation ends
            return view('admin.users.withdraw.confirmation', $data);
        }
        return view('admin.users.withdraw.create', $data);
    }

    public function amountFeesLimitCheck(Request $request)
    {
        $amount      = $request->amount;
        $feesDetails = FeesLimit::where(['transaction_type_id' => $request->transaction_type_id, 'currency_id' => $request->currency_id, 'payment_method_id' => $request->payment_method_id])
            ->first(['min_limit', 'max_limit', 'charge_percentage', 'charge_fixed']);
        $wallet = Wallet::where(['currency_id' => $request->currency_id, 'user_id' => $request->user_id])->first(['balance']);

        if ($request->transaction_type_id == Withdrawal)
        {
            //Wallet Balance Limit Check Starts here
            $checkAmount = $amount + $feesDetails->charge_fixed + $feesDetails->charge_percentage;
            if (@$wallet)
            {
                if ((@$checkAmount) > (@$wallet->balance) || (@$wallet->balance < 0))
                {
                    $success['message'] = "Insufficient Balance!";
                    $success['status']  = '401';
                    return response()->json(['success' => $success]);
                }
            }
            //Wallet Balance Limit Check Ends here
        }

        //Amount Limit Check Starts here
        if (empty($feesDetails))
        {
            $feesPercentage            = 0;
            $feesFixed                 = 0;
            $totalFess                 = $feesPercentage + $feesFixed;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['totalFees']      = $totalFess;
            $success['totalFeesHtml']  = formatNumber($totalFess);
            $success['totalAmount']    = $totalAmount;
            $success['pFees']          = $feesPercentage;
            $success['pFeesHtml']      = formatNumber($feesPercentage);
            $success['fFees']          = $feesFixed;
            $success['fFeesHtml']      = formatNumber($feesFixed);
            $success['min']            = 0;
            $success['max']            = 0;
            $success['balance']        = 0;
        }
        else
        {
            if (@$feesDetails->max_limit == null)
            {
                if ((@$amount < @$feesDetails->min_limit))
                {
                    $success['message'] = 'Minimum amount ' . formatNumber($feesDetails->min_limit);
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
                    $success['message'] = 'Minimum amount ' . formatNumber($feesDetails->min_limit) . ' and Maximum amount ' . formatNumber($feesDetails->max_limit);
                    $success['status']  = '401';
                }
                else
                {
                    $success['status'] = 200;
                }
            }
            $feesPercentage            = $amount * ($feesDetails->charge_percentage / 100);
            $feesFixed                 = $feesDetails->charge_fixed;
            $totalFess                 = $feesPercentage + $feesFixed;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['totalFees']      = $totalFess;
            $success['totalFeesHtml']  = formatNumber($totalFess);
            $success['totalAmount']    = $totalAmount;
            $success['pFees']          = $feesDetails->charge_percentage;
            $success['pFeesHtml']      = formatNumber($feesDetails->charge_percentage);
            $success['fFees']          = $feesDetails->charge_fixed;
            $success['fFeesHtml']      = formatNumber($feesDetails->charge_fixed);
            $success['min']            = $feesDetails->min_limit;
            $success['max']            = $feesDetails->max_limit;
            $success['balance']        = @$wallet->balance ? @$wallet->balance : 0;
        }
        //Amount Limit Check Ends here
        return response()->json(['success' => $success]);
    }

    public function eachUserWithdrawSuccess(Request $request)
    {
        // dd($request->all());

        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        $user_id = $request->user_id;

        //Check Session - starts
        $sessionValue = session('transInfo');
        // dd($sessionValue);
        if (empty($sessionValue))
        {
            return redirect("admin/users/withdraw/create/$user_id");
        }
        //Check Session - ends

        actionSessionCheck();

        $uuid    = unique_code();
        $feeInfo = FeesLimit::where(['transaction_type_id' => Withdrawal, 'currency_id' => $sessionValue['currency_id'], 'payment_method_id' => $sessionValue['payment_method']])
            ->first(['charge_percentage', 'charge_fixed']);
        $p_calc = (($sessionValue['amount']) * (@$feeInfo->charge_percentage) / 100); //charge percentage calculation

        try
        {
            \DB::beginTransaction();
            //Withdrawal
            $withdrawal                    = new Withdrawal();
            $withdrawal->user_id           = $user_id;
            $withdrawal->currency_id       = $sessionValue['currency_id'];
            $withdrawal->payment_method_id = $sessionValue['payment_method'];
            $withdrawal->uuid              = $uuid;
            $withdrawal->charge_percentage = @$feeInfo->charge_percentage ? $p_calc : 0;
            $withdrawal->charge_fixed      = @$feeInfo->charge_fixed ? @$feeInfo->charge_fixed : 0;
            $withdrawal->subtotal          = ($sessionValue['amount'] - ($p_calc + (@$feeInfo->charge_fixed)));
            $withdrawal->amount            = $sessionValue['amount'];
            $withdrawal->status            = 'Success';
            $withdrawal->save();

            //Transaction
            $transaction                           = new Transaction();
            $transaction->user_id                  = $user_id;
            $transaction->currency_id              = $sessionValue['currency_id'];
            $transaction->payment_method_id        = $sessionValue['payment_method'];
            $transaction->transaction_reference_id = $withdrawal->id;
            $transaction->transaction_type_id      = Withdrawal;
            $transaction->uuid                     = $uuid;
            $transaction->subtotal                 = $withdrawal->amount;
            $transaction->percentage               = @$feeInfo->charge_percentage ? @$feeInfo->charge_percentage : 0;
            $transaction->charge_percentage        = $withdrawal->charge_percentage;
            $transaction->charge_fixed             = $withdrawal->charge_fixed;
            $transaction->total                    = '-' . ($withdrawal->amount + $withdrawal->charge_percentage + $withdrawal->charge_fixed);
            $transaction->status                   = 'Success';
            $transaction->save();

            //Wallet
            $wallet = Wallet::where(['user_id' => $user_id, 'currency_id' => $sessionValue['currency_id']])->first();
            if (!empty($wallet))
            {
                $wallet->balance = ($wallet->balance - ($withdrawal->amount + $withdrawal->charge_percentage + $withdrawal->charge_fixed));
                $wallet->save();
            }
            \DB::commit();

            $data['transInfo']['currSymbol'] = $transaction->currency->symbol;
            $data['transInfo']['subtotal']   = $transaction->subtotal;
            $data['transInfo']['id']         = $transaction->id;
            $data['user_id']                 = $user_id;
            $data['name']                    = $sessionValue['fullname'];

            Session::forget('transInfo');
            clearActionSession();
            return view('admin.users.withdraw.success', $data);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            Session::forget('transInfo');
            clearActionSession();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect("users/withdraw/create/$user_id");
        }
    }

    public function eachUserWithdrawPrintPdf($trans_id)
    {
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);

        $data['transactionDetails'] = $transactionDetails = Transaction::with(['payment_method:id,name', 'currency:id,symbol'])
            ->where(['id' => $trans_id])->first(['uuid', 'created_at', 'status', 'currency_id', 'payment_method_id', 'subtotal', 'charge_percentage', 'charge_fixed', 'total']);

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
        $mpdf->WriteHTML(view('admin.users.withdraw.withdrawalPrintPdf', $data));
        $mpdf->Output('payout_' . time() . '.pdf', 'I');
    }
    /* End of Admin Withdraw */

/* Start of Admin Crypto Send */
    public function eachUserCryptoSend($id, Request $request)
    {
        setActionSession();
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        $data['users']    = $users    = User::find($id, ['id', 'first_name', 'last_name']);

        // Get active crypto currencies
        $data['activeCryptoCurrencies'] = $activeCryptoCurrencies = $this->cryptoCurrency->getActiveCryptoCurrencies();
        // dd($activeCryptoCurrencies);

        if ($_POST)
        {
            // dd($request->all());

            $res = $this->cryptoSendReceiveConfirm($data, $request, 'send');
            if ($res['status'] == 401)
            {
                $this->helper->one_time_message('error', $res['message']);
                return redirect('admin/users/crypto/send/' . $request->user_id);
            }
            //for confirm page only
            $data['cryptoTrx'] = $res['cryptoTrx'];
            return view('admin.users.crypto.send.confirmation', $data);
        }
        return view('admin.users.crypto.send.create', $data);
    }

    public function eachUserCryptoSendSuccess(Request $request)
    {
        // .env - APP_DEMO - check
        if (checkDemoEnvironment() == true)
        {
            $this->helper->one_time_message('error', 'Crypto Send is not possible on demo site.');
            return redirect('admin/users/crypto/send/' . $request->user_id);
        }

        $res = $this->cryptoSendReceiveSuccess($request, 'send');
        if ($res['status'] == 401)
        {
            $this->helper->one_time_message('error', $res['message']);
            return redirect('admin/users/crypto/send/' . $res['user_id']);
        }
        return view('admin.users.crypto.send.success', $res['data']);
    }

    /**
     * Generate pdf print for merchant crypto sent & received
     */
    public function merchantCryptoSentReceivedTransactionPrintPdf($id)
    {
        $id                  = decrypt($id);
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);
        $data['transaction'] = $transaction = Transaction::with(['currency:id,symbol', 'cryptoapi_log:id,object_id,payload,confirmations'])->where(['id' => $id])->first();
        // dd($transaction);

        // Get crypto api log details for Crypto_Sent & Crypto_Received (via custom relationship)
        if (!empty($transaction->cryptoapi_log))
        {
            $getCryptoDetails = $this->cryptoCurrency->getCryptoPayloadConfirmationsDetails($transaction->transaction_type_id, $transaction->cryptoapi_log->payload, $transaction->cryptoapi_log->confirmations);
            if (count($getCryptoDetails) > 0)
            {
                // For "Tracking block io account receiver address changes, if amount is sent from other payment gateways like CoinBase, CoinPayments, etc"
                if (isset($getCryptoDetails['senderAddress']))
                {
                    $data['senderAddress'] = $getCryptoDetails['senderAddress'];
                }
                $data['receiverAddress'] = $getCryptoDetails['receiverAddress'];
                $data['confirmations']   = $getCryptoDetails['confirmations'];
            }
        }

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
        $mpdf->WriteHTML(view('user_dashboard.transactions.crypto_sent_received', $data));
        $mpdf->Output('crypto-sent-received_' . time() . '.pdf', 'I'); // this will output data
    }

    // Extended Functions (Crypto Send)- starts
    //Get merchant network address, merchant network balance and user network address
    public function getMerchantUserNetworkAddressWithMerchantBalance(Request $request)
    {
        // dd($request->all());

        try
        {
            $user_id = $request->user_id;
            $network = $request->network;

            //Get merchant network address
            $merchantAddress = $this->cryptoCurrency->getMerchantNetworkAddress($network);
            // dd($merchantAddress);

            //Check merchant network address
            $checkMerchantNetworkAddress = $this->cryptoCurrency->checkNetworkAddressValidity($network, $merchantAddress);
            if (!$checkMerchantNetworkAddress)
            {
                return response()->json([
                    'status'  => 400,
                    'message' => 'Invalid merchant ' . $network . ' address',
                ]);
            }

            //Get merchant network address balance
            $merchantAddressBalance = $this->cryptoCurrency->getUserCryptoAddressBalance($network, $merchantAddress);

            //Get Use Wallet Address
            $getUserNetworkWalletAddress = $this->cryptoCurrency->getUserNetworkWalletAddress($user_id, $network);
            if ($getUserNetworkWalletAddress->getData()->status == 200)
            {
                //Check user network wallet address
                $checkUserAddress = $this->cryptoCurrency->checkNetworkAddressValidity($network, $getUserNetworkWalletAddress->getData()->userAddress);
                if (!$checkUserAddress)
                {
                    return response()->json([
                        'status'  => 400,
                        'message' => 'Invalid user ' . $network . ' address',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'status'  => 400,
                    'message' => $getUserNetworkWalletAddress->getData()->message,
                ]);
            }
            return response()->json([
                'status'                 => 200,
                'merchantAddress'        => $merchantAddress,
                'merchantAddressBalance' => $merchantAddressBalance,
                'userAddress'            => $getUserNetworkWalletAddress->getData()->userAddress,
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status'  => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    //validate merchant Address Balance Against Amount
    public function validateMerchantAddressBalanceAgainstAmount(Request $request)
    {
        // dd($request->all());
        try
        {
            $validateMerchantAddressBalance = $this->cryptoCurrency->validateNetworkAddressBalance($request->network, $request->amount, $request->merchantAddress, $request->userAddress);
            if (!$validateMerchantAddressBalance['status'])
            {
                return response()->json([
                    'status'      => 400,
                    'message'     => 'Network fee ' . $validateMerchantAddressBalance['network-fee'] . ' and amount ' . $request->amount . ' exceeds your ' . strtoupper($request->network) . ' balance',
                ]);
            }
            else
            {
                return response()->json([
                    'status'      => 200,
                ]);
            }
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status'  => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
    // Extended Functions (Crypto Send)- ends
/* End of Admin Crypto Send */

/* Start of Admin Crypto Receive */
    public function eachUserCryptoReceive($id, Request $request)
    {
        setActionSession();

        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        $data['users']    = $users    = User::find($id, ['id', 'first_name', 'last_name']);

        // Get active crypto currencies
        $data['activeCryptoCurrencies'] = $activeCryptoCurrencies = $this->cryptoCurrency->getActiveCryptoCurrencies();

        if ($_POST)
        {
            // dd($request->all());

            $res = $this->cryptoSendReceiveConfirm($data, $request, 'receive');
            if ($res['status'] == 401)
            {
                $this->helper->one_time_message('error', $res['message']);
                return redirect('admin/users/crypto/receive/' . $request->user_id);
            }
            //for confirm page only
            $data['cryptoTrx'] = $res['cryptoTrx'];
            return view('admin.users.crypto.receive.confirmation', $data);
        }
        return view('admin.users.crypto.receive.create', $data);
    }

    public function eachUserCryptoReceiveSuccess(Request $request)
    {
        // .env - APP_DEMO - check
        if (checkDemoEnvironment() == true)
        {
            $this->helper->one_time_message('error', 'Crypto Receive is not possible on demo site.');
            return redirect('admin/users/crypto/send/' . $request->user_id);
        }

        $res = $this->cryptoSendReceiveSuccess($request, 'receive');
        if ($res['status'] == 401)
        {
            $this->helper->one_time_message('error', $res['message']);
            return redirect('admin/users/crypto/receive/' . $res['user_id']);
        }
        return view('admin.users.crypto.receive.success', $res['data']);
    }
    // Extended Functions (Crypto Receive)- starts
    //Get user network address, user network balance and merchant network address
    public function getUserNetworkAddressBalanceWithMerchantNetworkAddress(Request $request)
    {
        // dd($request->all());
        try
        {
            $user_id = $request->user_id;
            $network = $request->network;

            //Get Use Wallet Address
            $getUserNetworkWalletAddress = $this->cryptoCurrency->getUserNetworkWalletAddress($user_id, $network);
            if ($getUserNetworkWalletAddress->getData()->status == 200)
            {
                //Check user network wallet address
                $checkUserAddress = $this->cryptoCurrency->checkNetworkAddressValidity($network, $getUserNetworkWalletAddress->getData()->userAddress);
                if (!$checkUserAddress)
                {
                    return response()->json([
                        'status'  => 400,
                        'message' => 'Invalid user ' . $network . ' address',
                    ]);
                }

                //Get user network address balance
                $userAddressBalance = $this->cryptoCurrency->getUserCryptoAddressBalance($network, $getUserNetworkWalletAddress->getData()->userAddress);

                //Get merchant network address
                $merchantAddress = $this->cryptoCurrency->getMerchantNetworkAddress($network);

                return response()->json([
                    'status'             => 200,
                    'userAddress'        => $getUserNetworkWalletAddress->getData()->userAddress,
                    'userAddressBalance' => $userAddressBalance,
                    'merchantAddress'    => $merchantAddress,
                ]);
            }
            else
            {
                return response()->json([
                    'status'  => 400,
                    'message' => $getUserNetworkWalletAddress->getData()->message,
                ]);
            }
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status'  => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    //validate merchant Address Balance Against Amount
    public function validateUserAddressBalanceAgainstAmount(Request $request)
    {
        // dd($request->all());
        try
        {
            $validateUserAddressBalance = $this->cryptoCurrency->validateNetworkAddressBalance($request->network, $request->amount, $request->userAddress, $request->merchantAddress);
            if (!$validateUserAddressBalance['status'])
            {
                return response()->json([
                    'status'      => 400,
                    'message'     => 'Network fee ' . $validateUserAddressBalance['network-fee'] . ' and amount ' . $request->amount . ' exceeds your ' . strtoupper($request->network) . ' balance',
                ]);
            }
            else
            {
                return response()->json([
                    'status'      => 200,
                ]);
            }
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status'  => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
    // Extended Functions (Crypto Receive)- ends
/* End of Admin Crypto Receive */

// Extended Functions (Crypto Send & Receive)- starts
    public function cryptoSendReceiveConfirm($data, $request, $type)
    {

        // dd($request->all());

        $userId          = $request->user_id;
        $network         = $request->network;
        $amount          = $request->amount;
        $merchantAddress = $request->merchantAddress;
        $userAddress     = $request->userAddress;
        $currency        = $this->currency->getCurrency(['code' => $network], ['id', 'symbol']);

        //merge currency symbol with request array
        $request->merge(['currency-symbol' => $currency->symbol]);
        $request->merge(['currency-id' => $currency->id]);
        $request->merge(['user-full-name' => $data['users']->first_name . ' ' . $data['users']->last_name]);

        //unset users & cryptoCurrenciesSettings - not needed in confirm page
        unset($data['users'], $data['cryptoCurrenciesSettings']);

        //Form back-end validations - starts
            if ($type === 'send')
            {
                $rules = array(
                    'merchantAddress' => 'required',
                    'merchantBalance' => 'required',
                    'userAddress'     => 'required',
                    'amount'          => 'required',
                );
                $fieldNames = array(
                    'merchantAddress' => 'Merchant Address',
                    'merchantBalance' => 'Merchant Balance',
                    'userAddress'     => 'User Address',
                    'amount'          => 'Amount',
                );
            }
            elseif ($type === 'receive')
            {
                $rules = array(
                    'userAddress'     => 'required',
                    'userBalance'     => 'required',
                    'merchantAddress' => 'required',
                    'amount'          => 'required',
                );
                $fieldNames = array(
                    'userAddress'     => 'User Address',
                    'userBalance'     => 'User Balance',
                    'merchantAddress' => 'User Address',
                    'amount'          => 'Amount',
                );
            }
        //Form back-end validations - ends

        //Backend validation of wallet currency code/network & amount - starts
            if (($network == 'DOGE' || $network == 'DOGETEST') && $amount < 2)
            {
                return [
                    'message' => "The minimum amount must be 2 $network",
                    'status'  => 401,
                ];
            }
            elseif (($network == 'BTC' || $network == 'BTCTEST') && $amount < 0.00002)
            {
                return [
                    'message' => "The minimum amount must be 0.00002 $network",
                    'status'  => 401,
                ];
            }
            elseif (($network == 'LTC' || $network == 'LTCTEST') && $amount < 0.0002)
            {
                return [
                    'message' => "The minimum amount must be 0.0002 $network",
                    'status'  => 401,
                ];
            }
        //Backend validation of wallet currency code/network & amount - ends

        //Backend validation of merchant & user network address validity & correct address - starts
            //Backend validation of merchant network address validity
            $checkMerchantNetworkAddress = $this->cryptoCurrency->checkNetworkAddressValidity($network, $merchantAddress);
            if (!$checkMerchantNetworkAddress)
            {
                return [
                    'message' => 'Invalid merchant ' . $network . ' address',
                    'status'  => 401,
                ];
            }
            //Backend validation of correct merchant network address
            $getMerchantNetworkAddress = $this->cryptoCurrency->getMerchantNetworkAddress($network);
            if ($merchantAddress != $getMerchantNetworkAddress)
            {
                return [
                    'message' => 'Incorrect merchant ' . $network . ' address',
                    'status'  => 401,
                ];
            }
            //Backend validation of user network address validity
            $checkUserNetworkAddress = $this->cryptoCurrency->checkNetworkAddressValidity($network, $userAddress);
            if (!$checkUserNetworkAddress)
            {
                return [
                    'message' => 'Invalid user ' . $network . ' address',
                    'status'  => 401,
                ];
            }
            //Backend validation of correct user network address
            $getUserNetworkWalletAddress  = $this->cryptoCurrency->getUserNetworkWalletAddress($userId, $network);
            if ($userAddress != $getUserNetworkWalletAddress->getData()->userAddress)
            {
                return [
                    'message' => 'Incorrect user ' . $network . ' address',
                    'status'  => 401,
                ];
            }
        //Backend validation of merchant & user network address validity & correct address - ends

        //Backend validation of merchant & user network address balance - starts
            if ($type === 'send')
            {
                //Backend merchant network address balance
                $getMerchantNetworkAddressBalance = $this->cryptoCurrency->getUserCryptoAddressBalance($network, $this->cryptoCurrency->getMerchantNetworkAddress($network));
                if ($request->merchantBalance != $getMerchantNetworkAddressBalance)
                {
                    return [
                        'message' => 'Incorrect merchant ' . $network . ' balance',
                        'status'  => 401,
                    ];
                }
                //Backend merchant network address balance against amount
                $validateAddressBlnce = $this->validateMerchantAddressBalanceAgainstAmount($request);
            }
            elseif ($type === 'receive')
            {
                //Backend user network address balance
                $getUserNetworkAddressBalance = $this->cryptoCurrency->getUserCryptoAddressBalance($network, $getUserNetworkWalletAddress->getData()->userAddress);
                if ($request->userBalance != $getUserNetworkAddressBalance)
                {
                    return [
                        'message' => 'Incorrect user ' . $network . ' balance',
                        'status'  => 401,
                    ];
                }
                //Backend user network address balance against amount
                $validateAddressBlnce = $this->validateUserAddressBalanceAgainstAmount($request);
            }
            if ($validateAddressBlnce->getData()->status == 400)
            {
                return [
                    'message' => $validateAddressBlnce->getData()->message,
                    'status'  => 401,
                ];
            }
        //Backend validation of merchant & user network address balance - ends

        $validator = \Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);
        if ($validator->fails())
        {
            return [
                'message' => $validator,
                'status'  => 401,
            ];
        }
        else
        {
            // dd($request->all());

            //Call network fee API of block io
            if ($type === 'send')
            {
                $getNetworkFeeEstimate = $this->cryptoCurrency->getNetworkFeeEstimate($network, $userAddress, $amount);
            }
            elseif ($type === 'receive')
            {
                $getNetworkFeeEstimate = $this->cryptoCurrency->getNetworkFeeEstimate($network, $merchantAddress, $amount);
            }

            //merge network fee with request array
            $request->merge(['network-fee' => $getNetworkFeeEstimate]);

            //Put data in session for success page
            session(['cryptoTrx' => $request->all()]);

            //for confirm page only
            $data['cryptoTrx'] = $request->only('currency-symbol', 'amount', 'network-fee', 'user_id', 'user-full-name');

            return [
                'cryptoTrx' => $data['cryptoTrx'],
                'status'    => 200,
            ];
        }
    }

    public function cryptoSendReceiveSuccess($request, $type)
    {
        // dd($request->all());

        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        //Check Session - starts
        $user_id   = $request->user_id;
        $cryptoTrx = session('cryptoTrx');
        if (empty($cryptoTrx))
        {
            return [
                'message' => null,
                'user_id' => $user_id,
                'status'  => 401,
            ];
        }
        //Check Session - ends

        //Backend validation of sender crypto wallet balance -- for multiple tab submit
        $request['network']         = $cryptoTrx['network'];
        $request['merchantAddress'] = $cryptoTrx['merchantAddress'];
        $request['userAddress']     = $cryptoTrx['userAddress'];
        $request['amount']          = $cryptoTrx['amount'];
        if ($type === 'send')
        {
            $validateAddressBlnceSuccess = $this->validateMerchantAddressBalanceAgainstAmount($request);
        }
        elseif ($type === 'receive')
        {
            $validateAddressBlnceSuccess = $this->validateUserAddressBalanceAgainstAmount($request);
        }

        if ($validateAddressBlnceSuccess->getData()->status == 400)
        {
            return [
                'message' => $validateAddressBlnceSuccess->getData()->message,
                'user_id' => $user_id,
                'status'  => 401,
            ];
        }
        else
        {
            try
            {
                //
                $uniqueCode = unique_code();
                $arr        = [
                    'walletCurrencyCode' => $cryptoTrx['network'],
                    'amount'             => $cryptoTrx['amount'],
                    'networkFee'         => $cryptoTrx['network-fee'],
                    'userId'             => null,
                    'endUserId'          => null,
                    'currencyId'         => $cryptoTrx['currency-id'],
                    'currencySymbol'     => $cryptoTrx['currency-symbol'],
                    'uniqueCode'         => $uniqueCode,
                ];

                if ($type === 'send')
                {
                    $arr['senderAddress']   = $cryptoTrx['merchantAddress'];
                    $arr['receiverAddress'] = $cryptoTrx['userAddress'];
                    $arr['endUserId']       = $cryptoTrx['user_id'];
                }
                elseif ($type === 'receive')
                {
                    $arr['senderAddress']   = $cryptoTrx['userAddress'];
                    $arr['receiverAddress'] = $cryptoTrx['merchantAddress'];
                    $arr['userId']          = $cryptoTrx['user_id'];
                }

                // dd($arr);

                try
                {
                    //Call withdraw API of block io
                    $withdrawInfo = $this->cryptoCurrency->withdrawOrSendAmountToReceiverAddress($arr['walletCurrencyCode'], $arr['senderAddress'], $arr['receiverAddress'], $arr['amount'], $arr['uniqueCode']);
                }
                catch (\Exception $e)
                {
                    return [
                        'message' => $e->getMessage(),
                        'user_id' => $user_id,
                        'status'  => 401,
                    ];
                }

                \DB::beginTransaction();

                //Create Merchant Crypto Transaction
                $createCryptoTransactionId = $this->cryptoCurrency->createCryptoTransaction($arr);

                //Create merchant new withdrawal/Send/Receive crypt api log
                $arr['transactionId']    = $createCryptoTransactionId;
                $arr['withdrawInfoData'] = $withdrawInfo->data;
                if ($type === 'send')
                {
                    //need this for showing send address against Crypto Receive Type Transaction in user/admin panel
                    $arr['withdrawInfoData']->senderAddress = $cryptoTrx['merchantAddress'];

                    //need this for nodejs websocket server
                    $arr['withdrawInfoData']->receiverAddress = $cryptoTrx['userAddress'];
                }
                elseif ($type === 'receive')
                {
                    $arr['withdrawInfoData']->senderAddress = $cryptoTrx['userAddress'];

                    $arr['withdrawInfoData']->receiverAddress = $cryptoTrx['merchantAddress'];
                }
                $this->cryptoCurrency->createWithdrawalOrSendCryptoApiLog($arr);

                //Update Sender/Receiver Network Address Balance
                if ($type === 'receive')
                {
                    $this->cryptoCurrency->getUpdatedSendWalletBalance($arr);
                }

                \DB::commit();

                //for success page
                // Recommended
                // $cryptConfirmationsArr = [
                //     'BTC'      => 3,
                //     'BTCTEST'  => 3,
                //     'DOGE'     => 10,
                //     'DOGETEST' => 10,
                //     'LTC'      => 5,
                //     'LTCTEST'  => 5,
                // ];

                // Initially after 1 confirmations of blockio response, websocket queries will be executed
                $cryptConfirmationsArr = [
                    'BTC'      => 1,
                    'BTCTEST'  => 1,
                    'DOGE'     => 1,
                    'DOGETEST' => 1,
                    'LTC'      => 1,
                    'LTCTEST'  => 1,
                ];
                $data['confirmations']      = $cryptConfirmationsArr[$arr['walletCurrencyCode']];
                $data['walletCurrencyCode'] = $arr['walletCurrencyCode'];
                $data['receiverAddress']    = $arr['receiverAddress'];
                $data['currencySymbol']     = $arr['currencySymbol'];
                $data['amount']             = $arr['amount'];
                $data['transactionId']      = $arr['transactionId'];
                if ($type === 'send')
                {
                    $data['userId'] = $arr['endUserId'];
                }
                elseif ($type === 'receive')
                {
                    $data['userId'] = $arr['userId'];
                }
                $data['user_full_name'] = $cryptoTrx['user-full-name'];
                //

                //clear cryptoTrx from session
                session()->forget(['cryptoTrx']);
                clearActionSession();
                return [
                    'data'   => $data,
                    'status' => 200,
                ];
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                //clear cryptoTrx from session
                session()->forget(['cryptoTrx']);
                clearActionSession();
                return [
                    'message' => $e->getMessage(),
                    'user_id' => $user_id,
                    'status'  => 401,
                ];
            }
        }
    }
// Extended Functions (Crypto Send & Receive)- ends

    public function eachUserTransaction($id, EachUserTransactionsDataTable $dataTable)
    {
        $data['menu']         = 'users';
        $data['sub_menu']     = 'users_list';
        $data['users']        = $users        = User::find($id);
        $eachUserTransactions = Transaction::where(function ($q) use ($id)
        {
            $q->where(['user_id' => $id])->orWhere(['end_user_id' => $id]);
        });
        $data['t_status']   = $t_status   = $eachUserTransactions->select('status')->groupBy('status')->get();
        $data['t_currency'] = $t_currency = $eachUserTransactions->select('currency_id')->groupBy('currency_id')->get();
        $data['t_type']     = $t_type     = $eachUserTransactions->select('transaction_type_id')->groupBy('transaction_type_id')->get();

        if (isset($_GET['btn']))
        {
            // dd($_GET);
            $data['status']   = $_GET['status'];
            $data['currency'] = $_GET['currency'];
            $data['type']     = $_GET['type'];

            if (empty($_GET['from']))
            {
                // dd('empty');
                $data['from'] = null;
                $data['to']   = null;
                // dd($transactions);
            }
            else
            {
                // dd('not empty');
                $data['from'] = $_GET['from'];
                $data['to']   = $_GET['to'];
            }
        }
        else
        {
            // dd('init');
            $data['from'] = null;
            $data['to']   = null;

            $data['status']   = 'all';
            $data['currency'] = 'all';
            $data['type']     = 'all';
        }
        
        // get all sales transactions
        
        $ids = [];
        
        $requested_for_withdrawal = SalesWithdrawal::where(['user_id' => $id])->get();
        
        if($requested_for_withdrawal->count() > 0){
            foreach($requested_for_withdrawal as $transaction){
                array_push($ids, $transaction->transaction_id);
            }
        
            $not_withdrawn = Transaction::where(['transaction_type_id' => '12', 'status' => 'Success', 'user_id' => $id])->whereNotIn('id', $ids)->get();
        }
        
        $data['sale_transactions'] = Transaction::where(['transaction_type_id' => '12', 'status' => 'Success', 'user_id' => $id])->get();
        $data['withdrawable_transactions'] = count($ids) > 0 ? $not_withdrawn : null;
        
        return $dataTable->with('user_id', $id)->render('admin.users.eachusertransaction', $data); //passing $id to dataTable ass user_id
    }

    public function eachUserWallet($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        $data['wallets'] = $wallets = Wallet::with('currency:id,type,code')->where(['user_id' => $id])->orderBy('id', 'desc')->get();
        $data['users']   = User::find($id);
        return view('admin.users.eachuserwallet', $data);
    }

    public function eachUserTicket($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        $data['tickets'] = $tickets = Ticket::where(['user_id' => $id])->orderBy('id', 'desc')->get();
        $data['users']   = User::find($id);
        return view('admin.users.eachuserticket', $data);
    }

    public function eachUserDispute($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        $data['disputes'] = $disputes = Dispute::where(['claimant_id' => $id])->orWhere(['defendant_id' => $id])->orderBy('id', 'desc')->get();

        $data['users'] = User::find($id);

        return view('admin.users.eachuserdispute', $data);
    }

    public function destroy($id)
    {
        // $id = decrypt($id);

        $user = User::find($id);
        if ($user)
        {
            try
            {
                \DB::beginTransaction();
                // Deleting Non-Relational Table Entries

                // Delete User wallet address {crypto sent, crypyo received and wallet address} object type api logs
                $this->cryptoCurrency->deleteWalletAddressCryptoSentCryptoReceivedApiLogs($user->wallets);

                ActivityLog::where(['user_id' => $id])->delete();
                RoleUser::where(['user_id' => $id, 'user_type' => 'User'])->delete();

                $user->delete();

                \DB::commit();

                $this->helper->one_time_message('success', 'User Deleted Successfully');
                return redirect('admin/users');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('admin/users');
            }
        }
    }

    public function postEmailCheck(Request $request)
    {

        if (isset($request->admin_id) || isset($request->user_id))
        {
            if (isset($request->type) && $request->type == "admin-email")
            {
                $req_id = $request->admin_id;
                $email  = Admin::where(['email' => $request->email])->where(function ($query) use ($req_id)
                {
                    $query->where('id', '!=', $req_id);
                })->exists();
            }
            else
            {
                $req_id = $request->user_id;
                $email  = User::where(['email' => $request->email])->where(function ($query) use ($req_id)
                {
                    $query->where('id', '!=', $req_id);
                })->exists();
            }
        }
        else
        {
            if (isset($request->type) && $request->type == "admin-email")
            {
                $email = Admin::where(['email' => $request->email])->exists();
            }
            else
            {
                $email = User::where(['email' => $request->email])->exists();
            }
        }

        if ($email)
        {
            $data['status'] = true;
            $data['fail']   = "The email has already been taken!";
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "Email Available!";
        }
        return json_encode($data);
    }
    
    public function duplicateEmailCheck(Request $request)
    {
        $email = $request->email;
        $usertype = $request->usertype;
        
        if($request->user_id){
            $check_email = User::where('email', $email)->where('role_id', $usertype)->where('id', '!=', $request->user_id)->exists();
        }else{
            $check_email = User::where(['email' => $email, 'role_id' => $usertype])->exists();
        }
        
        if ($check_email)
        {
            $data['status'] = true;
            $data['fail']   = __('The email has already been taken!');
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "Email Available!";
        }
        return json_encode($data);
    }

    public function duplicatePhoneNumberCheck(Request $request)
    {
        $carrierCode = $request->carrierCode;
        $phone = $request->phone;
        $usertype = $request->usertype;
        
        if($request->id){
            if (isset($carrierCode))
            {
                $check_phone = User::where('phone', $phone)->where('carrierCode', $carrierCode)->where('role_id', $usertype)->where('id', '!=', $request->id)->first();
            }
            else
            {
                $check_phone = User::where('phone', $phone)->where('role_id', $usertype)->where('id', '!=', $request->id)->first();
            }
        }else{
            if (isset($carrierCode))
            {
                $check_phone = User::where(['phone' => $phone, 'carrierCode' => $carrierCode, 'role_id' => $usertype])->first();
            }
            else
            {
                $check_phone = User::where(['phone' => $phone, 'role_id' => $usertype])->first();
            }
        }

        if ($check_phone)
        {
            $data['status'] = true;
            $data['fail']   = "The phone number has already been taken!";
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "The phone number is Available!";
        }
        return json_encode($data);
    }
    
    public function checkDuplicateUser(Request $request)
    {
        $email = $request->email;
        $carrierCode = $request->carrierCode;
        $phone = $request->phone;
        $usertype = $request->usertype;
        
        if($request->user_id){
            $check_email = User::where('email', $email)->where('role_id', $usertype)->where('id', '!=', $request->user_id)->exists();
            
            if (isset($carrierCode))
            {
                $check_phone = User::where('phone', $phone)->where('carrierCode', $carrierCode)->where('role_id', $usertype)->where('id', '!=', $request->user_id)->first();
            }
            else
            {
                $check_phone = User::where('phone', $phone)->where('role_id', $usertype)->where('id', '!=', $request->user_id)->first();
            }
        }else{
            $check_email = User::where(['email' => $email, 'role_id' => $usertype])->exists();
            
            if (isset($carrierCode))
            {
                $check_phone = User::where(['phone' => $phone, 'carrierCode' => $carrierCode, 'role_id' => $usertype])->first();
            }
            else
            {
                $check_phone = User::where(['phone' => $phone, 'role_id' => $usertype])->first();
            }
        }
        
        if($check_email)
        {
            $data['status'] = true;
            $data['fail']   = __('The email has already been taken!');
            return json_encode($data);
        }

        if ($check_phone)
        {
            $data['status'] = true;
            $data['fail']   = "The phone number has already been taken!";
            return json_encode($data);
        }
        
        $data['status']  = false;
        $data['success'] = "The User is Available!";
        return json_encode($data);
    }
    
    public function duplicatePhoneNumberCheck1(Request $request)
    {
        // dd($request->all());
        $req_id = $request->id;

        if (isset($req_id))
        {
            $user = User::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone), 'carrierCode' => $request->carrierCode])->where(function ($query) use ($req_id)
            {
                $query->where('id', '!=', $req_id);
            })->first(['phone', 'carrierCode']);
        }
        else
        {
            // dd('no id');
            $user = User::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone), 'carrierCode' => $request->carrierCode])->first(['phone', 'carrierCode']);
        }

        // if (!empty($user->phone) && !empty($user->carrierCode))
        // {
        //     $data['status'] = true;
        //     $data['fail']   = "The phone number has already been taken!";
        // }
        // else
        // {
        //     $data['status']  = false;
        //     $data['success'] = "The phone number is Available!";
        // }
        return json_encode($user);
    }
    
    public function photoproofdummy($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';

        $data['disputes'] = $disputes = Dispute::where(['claimant_id' => $id])->orWhere(['defendant_id' => $id])->orderBy('id', 'desc')->get();

        $data['users'] = User::find($id, ['id', 'first_name', 'last_name']);

        return view('admin.users.eachuserdispute', $data);
    }
    
    public function photoproof(PhotoProofsDataTable $dataTable,$id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        
        $data['documentVerificationStatus']  = $this->documentVerification->where(['verification_type' => 'photo','user_id'=>$id])->groupBy('status')->get();
        
        
         $data['users'] = User::find($id, ['id', 'first_name', 'last_name']);
         
        // return $dataTable->render('admin.verifications.photo_proofs.list', $data);
        return view('admin.users.photoproof', $data);
    }
    
    public function addressproof($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        
        $data['documentVerificationStatus']  = $this->documentVerification->where(['verification_type' => 'address','user_id'=>$id])->groupBy('status')->get();
        
        
         $data['users'] = User::find($id, ['id', 'first_name', 'last_name']);
         
        // return $dataTable->render('admin.verifications.photo_proofs.list', $data);
        return view('admin.users.addressproof', $data);
    }
    
    public function idproof($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        
        $data['documentVerificationStatus'] = $this->documentVerification->where(['verification_type' => 'identity','user_id'=>$id])->groupBy('status')->get();
        
        $data['users'] = User::find($id, ['id', 'first_name', 'last_name']);
        return view('admin.users.idproof', $data);
    }
    
    public function bankdetails($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        
        $data['bankdetails'] = PayoutSetting::with('user','payment_methods')->where(['user_id'=>$id])->get();
        $data['users'] = User::find($id);
        $data['banks'] = CountryBank::where('user_id', $id)->get();
        
        return view('admin.users.bankdetails', $data);
    }
    
    public function unlink_device()
    {
        $data['menu']     = 'unlinkdevice';
        $data['users'] = User::where('request_device', '0')->get();
        User::where('lnkdev_read_status', '0')->where('request_device', '0')->update(['lnkdev_read_status' => '1']);
        return view('admin.users.unlinkdevice', $data);
    }
    
    public function approve_device($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'clear_device';
        User::where('id', $id)->update(['request_device' => '1', 'device_id' => null]);
        
        $adminAllowed = Notification::has_permission([1]);
                    
        foreach($adminAllowed as $admin){
            $name = User::where('id', $id)->first();
            Notification::insert([
                'user_id'               => $id,
                'notification_to'       => $admin->agent_id,
                'notification_type_id'  => 1,
                'notification_type'     => 'App',
                'description'           => 'User '.$name->first_name.' is allowed to create different account from the same device.',
                'url_to_go'             => 'admin/users/edit/'.$id,
                'local_tran_time'       => null
            ]);
        }

        $userdevice = DB::table('devices')->where('user_id', $id)->first();
        if(!empty($userdevice)){
            $template = NotificationTemplate::where('temp_id', '33')->where('language_id', $userdevice->language)->first();
            $subject = $template->title;
            $subheader = $template->subheader;
            $message = $template->content;
            
            $type = 'cleardevice';
            $currency = '9';
            
            $date    = date("m-d-Y h:i");
            $this->helper->sendFirabasePush($subject,$message,$id, $currency, $type);
            
            Noticeboard::create([
                'tr_id' => null,
                'title' => $subject,
                'content' => $message,
                'type' => 'push',
                'content_type' => $type,
                'user' => $id,
                'sub_header' => $subheader,
                'push_date' => $date,
                'template' => '33',
                'language' => $userdevice->language,
            ]);
        }
        
        return back();
    }
    
    public function transfercreate(Request $request,$id="")
    {
        $user_id = $id;
        //set the session for validating the action
        setActionSession();

       $data['menu']     = 'users';
        $data['sub_menu'] = 'users_list';
        $data['users']        = $users        = User::find($id, ['id', 'first_name', 'last_name', 'type']);
        if (!$_POST)
        {
            /*Check Whether Currency is Activated in feesLimit*/
            $data['walletList'] = Wallet::where(['user_id' => $id])
                ->whereHas('active_currency', function ($q)
            {
                    $q->whereHas('fees_limit', function ($query)
                {
                        $query->where('transaction_type_id', Transferred)->where('has_transaction', 'Yes')->select('currency_id', 'has_transaction');
                    });
                })
                ->with(['active_currency:id,code', 'active_currency.fees_limit:id,currency_id']) //Optimized by parvez - for pm v2.3
                ->get(['id', 'currency_id', 'is_default']);
            // dd($data['walletList']);
            $data['user_id'] = $id;
            //check Decimal Thousand Money Format Preference
            $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);
            
            return view('admin.moneytransfer.create', $data);
        }
        else if ($_POST)
        {
            // dd($request->all());

            $rules = array(
                'amount'   => 'required|numeric',
                'receiver' => 'required',
                'note'     => 'required',
            );

            $fieldNames = array(
                'amount'   => __("Amount"),
                'receiver' => __("Recipient"),
                'note'     => __("Note"),
            );

            //instantiating message array
            $messages = [
                //
            ];

            // backend Validation - starts
            if ($request->sendMoneyProcessedBy == 'email')
            {
                //check if valid email
                $rules['receiver'] = 'required|email';
            }
            elseif ($request->sendMoneyProcessedBy == 'phone')
            {
                //check if valid phone
                $myStr = explode('+', $request->receiver);
                if ($request->receiver[0] != "+" || !is_numeric($myStr[1]))
                {
                    return back()->withErrors(__("Please enter valid phone (ex: +12015550123)"))->withInput();
                }
            }
            elseif ($request->sendMoneyProcessedBy == 'email_or_phone')
            {
                $myStr = explode('+', $request->receiver);
                //valid number is not entered
                if ($request->receiver[0] != "+" || !is_numeric($myStr[1]))
                {
                    //check if valid email or phone
                    $rules['receiver'] = 'required|email';
                    $messages          = [
                        'email' => __("Please enter valid email (ex: user@gmail.com) or phone (ex: +12015550123)"),
                    ];
                }
            }

            //Own Email or phone validation + Receiver/Recipient is suspended/Inactive validation
            $transferUserEmailPhoneReceiverStatusValidate = $this->transferUserEmailPhoneReceiverStatusValidate($request);
            if ($transferUserEmailPhoneReceiverStatusValidate)
            {
                if ($transferUserEmailPhoneReceiverStatusValidate->getData()->status == true || $transferUserEmailPhoneReceiverStatusValidate->getData()->status == 404)
                {
                    return back()->withErrors(__($transferUserEmailPhoneReceiverStatusValidate->getData()->message))->withInput();
                }
            }

            //Amount Limit Check validation
            $request['wallet_id']           = $request->wallet;
            $request['transaction_type_id'] = Transferred;
            $amountLimitCheck               = $this->amountLimitCheck($request);
            // dd($amountLimitCheck->getData());
            if ($amountLimitCheck->getData()->success->status == 200)
            {
                if ($amountLimitCheck->getData()->success->totalAmount > $amountLimitCheck->getData()->success->balance)
                {
                    return back()->withErrors(__("Not have enough balance !"))->withInput();
                }
            }
            else
            {
                return back()->withErrors(__($amountLimitCheck->getData()->success->message))->withInput();
            }
            //backend validation ends

            $validator = Validator::make($request->all(), $rules, $messages);
            $validator->setAttributeNames($fieldNames);
            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                //Validation passed
                $wallet                          = Wallet::with(['currency:id,symbol'])->where(['id' => $request->wallet, 'user_id' => $id])->first(['currency_id', 'balance']);
                $request['currency_id']          = $wallet->currency->id;
                $request['currSymbol']           = $wallet->currency->symbol;
                $request['totalAmount']          = $request->amount + $request->fee;
                $request['sendMoneyProcessedBy'] = $request->sendMoneyProcessedBy;
                $request['user_id']          = $user_id;
                
                session(['transInfo' => $request->all()]);
                $data['transInfo'] = $request->all();
            }
            return view('admin.moneytransfer.confirmation', $data);
        }
    }
    
    
    public function checkProcessedBy()
    {
       
        return response()->json([
            'status'      => true,
            'processedBy' => $this->helper->getPrefProcessedBy(),
        ]);
    }
    
     //Send Money - Email/Phone validation
    public function transferUserEmailPhoneReceiverStatusValidate(Request $request)
    {
        // dd($request->all());
        
        $user_id = $request->user_id;
        $getuser = User::where('id',$user_id)->first();

        $phoneRegex = $this->helper->validatePhoneInput(trim($request->receiver));
        if ($phoneRegex)
        {
            //Check phone number exists or not
            $user = User::where(['id' => $user_id])->first(['formattedPhone']);
            if (empty($user->formattedPhone))
            {
                return response()->json([
                    'status'  => 404,
                    'message' => __("Please set your phone number first!"),
                ]);
            }

            //Check own phone number
            if ($request->receiver ==$getuser->formattedPhone)
            {
                return response()->json([
                    'status'  => true,
                    'message' => __("You Cannot Send Money To Yourself!"),
                ]);
            }

            //Check Receiver/Recipient is suspended/inactive - if entered phone number
            $receiver = User::where(['formattedPhone' => $request->receiver])->first(['status']);
            if (!empty($receiver))
            {
                if ($receiver->status == 'Suspended')
                {
                    return response()->json([
                        'status'  => true,
                        'message' => __("The recipient is suspended!"),
                    ]);
                }
                elseif ($receiver->status == 'Inactive')
                {
                    return response()->json([
                        'status'  => true,
                        'message' => __("The recipient is inactive!"),
                    ]);
                }
            }
        }
        else
        {
            //Check own phone email
            if ($request->receiver == $getuser->email)
            {
                return response()->json([
                    'status'  => true,
                    'message' => __("You Cannot Send Money To Yourself!"),
                ]);
            }

            //Check Receiver/Recipient is suspended/inactive - if entered email
            $receiver = User::where(['email' => trim($request->receiver)])->first(['status']);
            if (!empty($receiver))
            {
                if ($receiver->status == 'Suspended')
                {
                    return response()->json([
                        'status'  => true,
                        'message' => __("The recipient is suspended!"),
                    ]);
                }
                elseif ($receiver->status == 'Inactive')
                {
                    return response()->json([
                        'status'  => true,
                        'message' => __("The recipient is inactive!"),
                    ]);
                }
            }
        }
    }
    
    
    
     public function amountLimitCheck(Request $request)
    {
        $amount      = $request->amount;
        $wallet_id   = $request->wallet_id;
        // $user_id     = Auth::user()->id;
        $user_id     = $request->user_id;
        $wallet      = Wallet::where(['id' => $wallet_id, 'user_id' => $user_id])->first(['currency_id', 'balance']);
        $currency_id = $wallet->currency_id;
        $feesDetails = FeesLimit::where(['transaction_type_id' => $request->transaction_type_id, 'currency_id' => $currency_id])->first(['max_limit', 'min_limit', 'charge_percentage', 'charge_fixed']);

        //Code for Amount Limit starts here
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
            $success['totalFeesHtml']  = formatNumber($totalFess);
            $success['totalAmount']    = $totalAmount;
            $success['pFees']          = $feesPercentage;
            $success['fFees']          = $feesFixed;
            $success['pFeesHtml']      = formatNumber($feesPercentage);
            $success['fFeesHtml']      = formatNumber($feesFixed);
            $success['min']            = 0;
            $success['max']            = 0;
            $success['balance']        = $wallet->balance;
            $success['user_id']        = $user_id;
        }
        else
        {
            $feesPercentage            = $amount * ($feesDetails->charge_percentage / 100);
            $feesFixed                 = $feesDetails->charge_fixed;
            $totalFess                 = $feesPercentage + $feesFixed;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['totalFees']      = $totalFess;
            $success['totalFeesHtml']  = formatNumber($totalFess);
            $success['totalAmount']    = $totalAmount;
            $success['pFees']          = $feesDetails->charge_percentage;
            $success['fFees']          = $feesDetails->charge_fixed;
            $success['pFeesHtml']      = formatNumber($feesDetails->charge_percentage);
            $success['fFeesHtml']      = formatNumber($feesDetails->charge_fixed);
            $success['min']            = $feesDetails->min_limit;
            $success['max']            = $feesDetails->max_limit;
            $success['balance']        = $wallet->balance;
            
             $success['user_id']        = $user_id;
        }
        //Code for Fees Limit Ends here
        return response()->json(['success' => $success]);
    }

    //Send Money - Confirm
    public function sendMoneyConfirm(Request $request,$id="")
    {
       
       
        $data['menu']    = 'send_receive';
        $data['submenu'] = 'send';

        $sessionValue = session('transInfo');
        if (empty($sessionValue))
        {
            return redirect('admin/users/moneytransfer/'.$id);
        }

        //initializing session
        actionSessionCheck();

        //Data - Wallet Balance Again Amount Check
        $total_with_fee          = $sessionValue['amount'] + $sessionValue['fee'];
        $currency_id             = session('transInfo')['currency_id'];
        // $user_id                 = auth()->user()->id;
         $user_id                = $id;
        $feesDetails             = $this->helper->getFeesLimitObject([], Transferred, $sessionValue['currency_id'], null, null, ['charge_percentage', 'charge_fixed']);
        $senderWallet            = $this->helper->getUserWallet([], ['user_id' => $user_id, 'currency_id' => $currency_id], ['id', 'balance']);
        $p_calc                  = $sessionValue['amount'] * (@$feesDetails->charge_percentage / 100);
        $processedBy             = $sessionValue['sendMoneyProcessedBy'];
        $request_wallet_currency = $sessionValue['currency_id'];
        $unique_code             = unique_code();
        $emailFilterValidate     = $this->helper->validateEmailInput(trim($sessionValue['receiver']));
        $phoneRegex              = $this->helper->validatePhoneInput(trim($sessionValue['receiver']));
        $userInfo                = $this->helper->getEmailPhoneValidatedUserInfo($emailFilterValidate, $phoneRegex, trim($sessionValue['receiver']));
        $arr                     = [
            'emailFilterValidate' => $emailFilterValidate,
            'phoneRegex'          => $phoneRegex,
            'processedBy'         => $processedBy,
            'user_id'             => $user_id,
            'userInfo'            => $userInfo,
            'currency_id'         => $request_wallet_currency,
            'uuid'                => $unique_code,
            'fee'                 => $sessionValue['fee'],
            'amount'              => $sessionValue['amount'],
            'note'                => trim($sessionValue['note']),
            'receiver'            => trim($sessionValue['receiver']),
            'charge_percentage'   => $feesDetails->charge_percentage,
            'charge_fixed'        => $feesDetails->charge_fixed,
            'p_calc'              => $p_calc,
            'total'               => $total_with_fee,
            'senderWallet'        => $senderWallet,
        ];
        $data['transInfo']['receiver']   = $sessionValue['receiver'];
        $data['transInfo']['currSymbol'] = $sessionValue['currSymbol'];
        $data['transInfo']['amount']     = $sessionValue['amount'];
        $data['userPic']                 = isset($userInfo) ? $userInfo->picture : '';
        $data['receiverName']            = isset($userInfo) ? $userInfo->first_name . ' ' . $userInfo->last_name : '';
        
        $data['user_id']            = $id;

        //Get response
        $response = $this->transfer->processSendMoneyConfirmation($arr, 'web');
        // dd($response);
        if ($response['status'] != 200)
        {
            if (empty($response['transactionOrTransferId']))
            {
                // dd($response['transactionOrTransferId']);
                session()->forget('transInfo');
                $this->helper->one_time_message('error', $response['ex']['message']);
                return redirect('admin/users/moneytransfer/',$id);
            }
            $data['errorMessage'] = $response['ex']['message'];
        }
        $data['transInfo']['trans_id'] = $response['transactionOrTransferId'];

        //clearing session
        session()->forget('transInfo');
        clearActionSession();
        return view('admin.moneytransfer.success', $data);
    }
    
    public function transferPrintPdf($trans_id)
    {
        $data['companyInfo']        = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);
        $data['transactionDetails'] = Transaction::with(['end_user:id,first_name,last_name', 'currency:id,symbol,code'])
            ->where(['id' => $trans_id])
            ->first(['transaction_type_id', 'end_user_id', 'currency_id', 'uuid', 'created_at', 'status', 'subtotal', 'charge_percentage', 'charge_fixed', 'total', 'note']);

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'                 => 'utf-8',
            'format'               => 'A3',
            'orientation'          => 'P',
            'shrink_tables_to_fit' => 0,
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('admin.moneytransfer.transferPaymentPdf', $data));
        $mpdf->Output('sendMoney_' . time() . '.pdf', 'I'); // this will output data
    }
    
    // 29-10-2020
    
    public function set_limit()
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'set_limit';  
        $data['currency'] = Currency::where('status','Active')->get();
        $data['is_kyc']   = DB::table('set_kyc_limit')->where('is_kyc',0)->get();
        $data['is_kyc_daily']   = DB::table('set_kyc_limit')->where(['type'=>'Daily','daily_limit_currency'=>9,'is_kyc'=>0])->first();
        $data['is_kyc_month']   = DB::table('set_kyc_limit')->where(['type'=>'Monthly','daily_limit_currency'=>9,'is_kyc'=>0])->first();
        //with kyc
        $data['without_kyc_daily']   = DB::table('set_kyc_limit')->where(['type'=>'Daily','daily_limit_currency'=>9,'is_kyc'=>1])->first();
        $data['without_kyc_month']   = DB::table('set_kyc_limit')->where(['type'=>'Monthly','daily_limit_currency'=>9,'is_kyc'=>1])->first();
        $data['with_kyc']   = DB::table('set_kyc_limit')->where('is_kyc',1)->get();
        return view('admin.users.set_limit', $data);
    }
    
    public function store_kyc_limit(Request $request)
    {
        
        if($request->type == "Daily")
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'Daily','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->first();
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'Daily','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->without_kyc,
                      'type'                      => 'Daily',
                      'display_name'              => 'Daily Limit',
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }elseif($request->type == "Monthly")
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'Monthly','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->first();
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'Monthly','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->without_kyc,
                      'type'                      => 'Monthly',
                      'display_name'              => 'Monthly Limit',
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }elseif($request->type== 'AddFund')
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'AddFund','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->first();
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'AddFund','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->without_kyc,
                      'type'                      => 'AddFund',
                      'display_name'              => 'Max Add Fund',
                      'daily_limit'               => $request->daily_limit,   
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }elseif($request->type== 'Pmonthtrans')
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'Pmonthtrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->first();
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'Pmonthtrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->without_kyc,
                      'type'                      => 'Pmonthtrans',
                      'display_name'              => 'Per Month Transaction',
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }elseif($request->type== 'Localtrans')
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'Localtrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->first();
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'Localtrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->without_kyc,
                      'type'                      => 'Localtrans',
                      'display_name'              => 'Local Transaction',
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }else
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'Intertrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->first();
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'Intertrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->without_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->without_kyc,
                      'type'                      => 'Intertrans',
                      'display_name'              => 'International Transactiom',
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }
        return redirect()->back()->with('success','Limit Set Successfully...');
        
    }
     public function store_with_kyc_limit(Request $request)
    {
       
        if($request->type == "Daily")
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'Daily','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->first();
            // print_r($check);
            // die;
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'Daily','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->with_kyc,
                      'type'                      => 'Daily',
                      'display_name'              => 'Daily Limit',
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }elseif($request->type == "Monthly")
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'Monthly','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->first();
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'Monthly','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->with_kyc,
                      'type'                      => 'Monthly',
                      'display_name'              => 'Monthly Limit',
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }elseif($request->type== 'AddFund')
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'AddFund','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->first();
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'AddFund','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->with_kyc,
                      'type'                      => 'AddFund',
                      'display_name'              => 'Max Add Fund',
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }elseif($request->type== 'Pmonthtrans')
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'Pmonthtrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->first();
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'Pmonthtrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->with_kyc,
                      'type'                      => 'Pmonthtrans',
                      'display_name'              => 'Per Month Transaction',
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }elseif($request->type== 'Localtrans')
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'Localtrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->first();
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'Localtrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->with_kyc,
                      'type'                      => 'Localtrans',
                      'display_name'              => 'Local Transaction',
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }else
        {
            $currency = $request->daily_limit_currency;
            $check    = DB::table('set_kyc_limit')->where(['type'=>'Intertrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->first();
            if($check)
            {
                DB::table('set_kyc_limit')->where(['type'=>'Intertrans','daily_limit_currency'=>$currency,'is_kyc'=>$request->with_kyc])->update([
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }else
            {
                DB::table('set_kyc_limit')->insert([
                      'is_kyc'                    => $request->with_kyc,
                      'type'                      => 'Intertrans',
                      'display_name'              => 'International Transaction',
                      'daily_limit'               => $request->daily_limit,
                      'daily_limit_currency'      => $request->daily_limit_currency
                    ]);
            }
        }
        return redirect()->back()->with('success','Limit Set Successfully...');
    }
    
    public function make_trust($id)
    {
        $update = User::where('id',$id)->update(['is_charity'=>1]);
        $this->helper->one_time_message('success', 'Add in Charity list Successfully...');
       // return redirect('admin/users');
        return redirect()->back()->with('success','Add in Charity list Successfully...');
    }
    public function make_user($id)
    {
        $update = User::where('id',$id)->update(['is_charity'=>0]);
        $this->helper->one_time_message('success', 'Move to normal user list Successfully...');
       // return redirect('admin/users');
        return redirect()->back()->with('success','Move to normal user list Successfully...');
    }
    
    public function cardKyc($id)
    {
        $user = User::find($id);
        
        $url = env('BASE_URL').'user-status';
        
        $headers = [
            'Host: cards.lubypay.com',
            'Content-Type: application/json',
        ];
        
        $payloads = [
            'user_email' => $user->email,
            'platform' => 'Sandbox Ewallet'
        ];
        
        $kyc = $this->postFunction($url, $headers, $payloads);
        if(empty($kyc['data'])){
            $this->helper->one_time_message('error', 'Card KYC not initiated!');
            return back();
        }else{
            if($kyc['data']['card_user_status'] == 'INVITE_PENDING'){
                $this->helper->one_time_message('success', 'Invite is in pending!');
                return back();
            }elseif($kyc['data']['card_user_status'] == 'INVITE_EXPIRED'){
                $this->helper->one_time_message('success', 'Invite has expired!');
                return back();
            }elseif($kyc['data']['card_user_status'] == 'USER_ACTIVE'){
                User::where('id', $id)->update([
                    'card_user_id' => $kyc['data']['card_user_id'], 
                    'card_user_status' => $kyc['data']['card_user_status'],
                    'kyc_status' => '1',
                    'kyc_verified_on' => now()->toDateTimeString()
                ]);
                
                if(!empty($kyc['data']['plan_data'])){
                    User::where('id', $id)->update([
                        'plan_data' => $kyc['data']['plan_data'],
                        'plan_id' => $kyc['data']['plan_id'],
                        'plan_name' => $kyc['data']['plan_name'],
                        'plan_amount' => $kyc['data']['plan_amount'],
                        'will_expire' => $kyc['data']['will_expire']
                    ]);
                    
                    $apply_card = ApplyCard::where('user_id', $id)->first();
                    if(empty($apply_card)){
                        $cardUser = User::find($id);
                        $cardUserDetail = UserDetail::where('user_id', $id)->first();
                        $cardUserCountry = Country::where('id', $cardUserDetail->country)->first();
                        
                        $card = ApplyCard::create([
                            'user_id' => $id,
                            'address_line' => $cardUserDetail->address_1,
                            'city' => $cardUserDetail->city,
                            'state' => $cardUserDetail->state,
                            'country' => $cardUserCountry->name,
                            'postal_code' => $cardUserDetail->zip_code
                        ]);
                        
                        $cardurl = env('BASE_URL').'platform-cards';
                        
                        $cards = $this->postFunction($cardurl, $headers, $payloads);
                        if(count($cards['data']) > 0){
                            foreach($cards['data'] as $card){
                                Card::create([
                                    'user_id' => $id,
                                    'invite_id' => $card['invite_id'],
                                    'card_holder' => $card['name_on_card'],
                                    'card_user_id' => $card['user_id'],
                                    'card_id' => $card['card_id'],
                                    'card' => $card['card'],
                                    'last_four' => $card['last4'],
                                    'type' => $card['type'],
                                    'amount' => $card['available_limits'],
                                    'card_number' => $card['full_card_number'],
                                    'card_cvv' => $card['card_cvv'],
                                    'expiry_month' => $card['exp_month'],
                                    'expiry_year' => $card['exp_year'],
                                    'currency' => $card['currency'],
                                    'status' => 'success',
                                    'card_status' => $card['status'],
                                    'applied_from' => 'web'
                                ]);
                            }
                        }
                    }
                }
                
                $this->helper->one_time_message('success', 'Card KYC approved.');
                return back();
            }
        }
    }
}
