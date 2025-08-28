<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Image;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper         = new Common();
    }
    
    protected $data = [];
    
    public function index()
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'paymentmethods';
        $data['paymentmethods'] = PaymentMethod::orderBy('status', 'asc')->get();
        return view('admin.paymentmethods.index', $data);
    }
    
    public function add()
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'paymentmethods';
        return view('admin.paymentmethods.create', $data);
    }
    
    public function store(Request $request)
    {        
        $rules = array(
            'name' => 'required',
            'status' => 'required',
        );

        $fieldNames = array(
            'name' => 'Name', 
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
            $path       = 'uploads/paymentmethods';
            $uploadPath = public_path($path);
            $fileName->move($uploadPath, $uniqueName);            
            $icon = $uniqueName;
        }else{
            $icon = null;
        }

        $slug = Str::slug($request->name); 

        $originalSlug = $slug;
        $counter = 1;

        while (PaymentMethod::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        $paymentmethod = PaymentMethod::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'slug'          => $slug,
            'has_permission' => $request->has_permission ? implode(',', $request->has_permission) : null,
            'icon'          => $icon,
            'status'        => $request->status,
        ]);
        
        $this->helper->one_time_message('success', 'Payment Method added Successfully');
        return redirect('admin/paymentmethods');
    }
    
    public function edit($id)
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'paymentmethods';
        $data['paymentmethod'] = PaymentMethod::where('id', $id)->first();
        return view('admin.paymentmethods.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        $rules = array(
            'name' => 'required',
            'status' => 'required',
        );

        $fieldNames = array(
            'name' => 'Name', 
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
            $path       = 'uploads/paymentmethods';
            $uploadPath = public_path($path);
            $fileName->move($uploadPath, $uniqueName);
            
            $icon = $uniqueName;
        }else{
            $oldIcon = PaymentMethod::where('id', $id)->first();
            $icon = $oldIcon->icon;
        }

        $paymentmethod = PaymentMethod::where('id', $id)->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'has_permission' => $request->has_permission ? implode(',', $request->has_permission) : null,
            'icon'          => $icon,
            'status'        => $request->status,
        ]);
        
        $this->helper->one_time_message('success', 'Payment Method Updated Successfully');
        return redirect('admin/paymentmethods');
    }
    
    public function delete($id)
    {
        $setting = PaymentMethod::where('id', $id)->delete();
        
        $this->helper->one_time_message('success', 'Payment Method Deleted Successfully');
        return redirect('admin/paymentmethods');
    }
}