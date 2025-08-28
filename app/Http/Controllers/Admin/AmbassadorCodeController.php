<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Http\Helpers\Common;
use App\Models\Admin;
use App\Models\AbassadorCode;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Currency;
use Auth;

class AmbassadorCodeController extends Controller
{
    protected $helper, $email, $user;

    public function __construct()
    {
        $this->helper         = new Common();
        $this->email          = new EmailController();
    }
    
    protected $data = [];
    
    public function index()
    {
        $data['menu'] = 'ambassadorcodes';
        $data['ambassadorcodes'] = AbassadorCode::with('admin')->orderBy('id', 'desc')->get();
        return view('admin.ambassadorcodes.index', $data);
    }
    
    public function add()
    {
        $data['menu'] = 'ambassadorcodes';
        $data['ambassadors'] = Admin::where('role_id', '12')->orderBy('id', 'desc')->get();
        return view('admin.ambassadorcodes.create', $data);
    }
    
    public function store(Request $request)
    {
        $rules = array(
            'code' => 'required',
            'created_for' => 'required',
            'total_uses' => 'required',
            'individual_uses' => 'required',
            'expires_on' => 'required',
            'status' => 'required',            
        );

        $fieldNames = array(
            'code' => 'Abassador Code',
            'created_for' => 'Abassador',
            'total_uses' => 'Total Uses',
            'individual_uses' => 'Individual Uses',
            'expires_on' => 'Expire On',
            'status' => 'Status',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        
        $setting = AbassadorCode::create([
            'code' => $request->code, 
            'fixed_discount' => $request->fixed_discount, 
            'percentage_discount' => $request->percentage_discount, 
            'created_by' => Auth::guard('admin')->user()->id, 
            'created_for' => $request->created_for, 
            'total_uses' => $request->total_uses, 
            'individual_uses' => $request->individual_uses, 
            'expires_on' => $request->expires_on, 
            'status' => $request->status, 
            'description' => $request->description
        ]);
        
        $this->helper->one_time_message('success', 'Abassador Code Created Successfully');
        return redirect('admin/ambassador-codes');
    }
    
    public function edit($id)
    {
        $data['menu']     = 'ambassadorcodes';
        $data['ambassadorcode'] = AbassadorCode::where('id', $id)->first();
        $data['ambassadors'] = Admin::where('role_id', '12')->orderBy('id', 'desc')->get();
        return view('admin.ambassadorcodes.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        $rules = array(
            'code' => 'required',
            'created_for' => 'required',
            'total_uses' => 'required',
            'individual_uses' => 'required',
            'expires_on' => 'required',
            'status' => 'required',            
        );

        $fieldNames = array(
            'code' => 'Abassador Code',
            'created_for' => 'Abassador',
            'total_uses' => 'Total Uses',
            'individual_uses' => 'Individual Uses',
            'expires_on' => 'Expire On',
            'status' => 'Status',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }

        $rs = AbassadorCode::where('id', $id)->update([
            'code' => $request->code, 
            'fixed_discount' => $request->fixed_discount, 
            'percentage_discount' => $request->percentage_discount, 
            'created_by' => Auth::guard('admin')->user()->id, 
            'created_for' => $request->created_for, 
            'total_uses' => $request->total_uses, 
            'individual_uses' => $request->individual_uses, 
            'expires_on' => $request->expires_on, 
            'status' => $request->status, 
            'description' => $request->description
        ]);
        
        $this->helper->one_time_message('success', 'Abassador Code Updated Successfully');
        return redirect('admin/ambassador-codes');
    }
    
    public function delete($id)
    {
        $setting = AbassadorCode::where('id', $id)->delete();
        
        $this->helper->one_time_message('success', 'Abassador Code Deleted Successfully');
        return redirect('admin/ambassador-codes');
    }
}