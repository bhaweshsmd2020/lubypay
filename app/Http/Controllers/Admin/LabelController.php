<?php

namespace App\Http\Controllers\Admin;
use App\DataTables\Admin\PhotoProofsDataTable;
use App\Models\Bank;
use App\Models\PayoutSetting;
use App\DataTables\Admin\AdminsDataTable;
use App\DataTables\Admin\EachUserTransactionsDataTable;
use App\DataTables\Admin\UsersDataTable;
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
use App\Models\DocumentVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use DB;
use App\DataTables\Admin\AllLogsDataTable;
use App\Models\LanguageContent;

class LabelController extends Controller
{
    protected $helper, $email, $currency, $user;
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
    }
    
    protected $data = [];
    
    public function index()
    {
        $data['menu']     = 'language';
        $data['sub_menu']     = 'language_contents';
        $data['allabels'] = LanguageContent::orderby('id','DESC')->get();
        return view('admin.labels.index', $data);
    }
    
    public function create()
    {
        $data['menu']     = 'labels';
        return view('admin.labels.create', $data);
    }
    
    public function store(Request $request)
    {
        // dd($request->all());
        
        $validation = Validator::make($request->all(), [
            'string'                => 'required',
            'english'               => 'required',
            'active'                => 'required',
        ]);

        if ($validation->fails())
        {
            return redirect()->back()->withErrors($validation->errors());
        }
        else
        {
            $check = LanguageContent::where('string', $request->input('string'))->first();
            if(empty($check)){
                $rs = LanguageContent::create([
                    'string'    => $request->input('string'),
                    'en'   => $request->input('english'),
                    'es'   => $request->input('spanish'),
                    'pt' => $request->input('portogues'),
                    'vn'     => $request->input('vietnamese'),
                    'active'    => $request->input('active'),
                ]); 
        
                $this->helper->one_time_message('success', 'Label Created Successfully');
                return redirect('admin/labels');
            }else{
                $this->helper->one_time_message('error', 'Label already exists');
                return redirect()->back();
            }
        }
    }
    
    public function edit(Request $request, $id)
    {
        $data['menu']     = 'labels';
        $data['allabels'] = LanguageContent::where('id', $id)->orderby('id','DESC')->first();
        return view('admin.labels.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        $rules = array(
            'string'                => 'required',
            'english'               => 'required',
            'active'                => 'required',
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
                $data = [
                    'string'    => $request->input('string'),
                    'en'   => $request->input('english'),
                    'es'   => $request->input('spanish'),
                    'pt' => $request->input('portogues'),
                    'vn'     => $request->input('vietnamese'),
                    'active'    => $request->input('active'),
                ];
               
                $rs = LanguageContent::where(['id'=> $id])->update($data);
        
                $this->helper->one_time_message('success', 'Label Updated Successfully');
                return redirect('admin/labels');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('admin/labels');
            }
        }
    }
}