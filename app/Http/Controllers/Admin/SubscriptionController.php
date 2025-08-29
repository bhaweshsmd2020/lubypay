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
use App\Models\Subscription;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Currency;

class SubscriptionController extends Controller
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
        $data['menu'] = 'subscriptions';
        $data['subscriptions'] = Subscription::orderBy('id', 'desc')->get();
        $data['currencies'] = Currency::where('status', 'Active')->orderBy('position', 'asc')->get();
        return view('admin.subscription.index', $data);
    }
    
    public function add()
    {
        $data['menu'] = 'subscriptions';
        return view('admin.subscription.create', $data);
    }
    
    public function store(Request $request)
    {
        $rules = array(
            'title' => 'required',
            'description' => 'required',
            'icon' => 'required',
            'duration' => 'required',
            'price' => 'required',
            'featured' => 'required',
            'status' => 'required',
        );

        $fieldNames = array(
            'title' => 'Title',
            'description' => 'Description',
            'icon' => 'Icon',
            'duration' => 'Duration',
            'price' => 'Price',
            'featured' => 'Featured',
            'status' => 'Status',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('icon'))
        {
            $fileName     = $request->file('icon');
            $originalName = $fileName->getClientOriginalName();
            $uniqueName   = strtolower(time() . '.' . $fileName->getClientOriginalExtension());
            $file_extn    = strtolower($fileName->getClientOriginalExtension());
            $path       = 'uploads/subscriptions';
            $uploadPath = public_path($path);
            $fileName->move($uploadPath, $uniqueName);
            
            $icon = url('public').'/'.$path.'/'.$uniqueName;
        }else{
            $icon = null;
        }
        
        $setting = Subscription::create([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $icon,
            'duration' => $request->duration,
            'price' => $request->price,
            'featured' => $request->featured,
            'status' => $request->status
        ]);
        
        $this->helper->one_time_message('success', 'Subscription Created Successfully');
        return redirect('admin/subscriptions');
    }
    
    public function edit($id)
    {
        $data['menu']     = 'subscriptions';
        $data['subscription'] = Subscription::where('id', $id)->first();
        return view('admin.subscription.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        $rules = array(
            'title' => 'required',
            'description' => 'required',
            'duration' => 'required',
            'price' => 'required',
            'featured' => 'required',
            'status' => 'required',
        );

        $fieldNames = array(
            'title' => 'Title',
            'description' => 'Description',
            'duration' => 'Duration',
            'price' => 'Price',
            'featured' => 'Featured',
            'status' => 'Status',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('icon'))
        {
            $fileName     = $request->file('icon');
            $originalName = $fileName->getClientOriginalName();
            $uniqueName   = strtolower(time() . '.' . $fileName->getClientOriginalExtension());
            $file_extn    = strtolower($fileName->getClientOriginalExtension());
            $path       = 'uploads/subscriptions';
            $uploadPath = public_path($path);
            $fileName->move($uploadPath, $uniqueName);
            
            $icon = url('public').'/'.$path.'/'.$uniqueName;
        }else{
            $oldIcon = Subscription::where('id', $id)->first();
            $icon = $oldIcon->icon;
        }

        $rs = Subscription::where('id', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $icon,
            'duration' => $request->duration,
            'price' => $request->price,
            'featured' => $request->featured,
            'status' => $request->status
        ]);
        
        $this->helper->one_time_message('success', 'Subscription Updated Successfully');
        return redirect('admin/subscriptions');
    }
    
    public function delete($id)
    {
        $setting = Subscription::where('id', $id)->delete();
        
        $this->helper->one_time_message('success', 'Subscription Deleted Successfully');
        return redirect('admin/subscriptions');
    }
}

