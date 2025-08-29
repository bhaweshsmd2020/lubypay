<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Image;
use Illuminate\Support\Str;

class TransactionTypeController extends Controller
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
        $data['sub_menu'] = 'transactiontypes';
        $data['transactiontypes'] = TransactionType::orderBy('orderby', 'asc')->get();
        return view('admin.transactiontypes.index', $data);
    }
    
    public function add()
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'transactiontypes';
        return view('admin.transactiontypes.create', $data);
    }
    
    public function store(Request $request)
    {        
        $rules = array(
            'name' => 'required',
            'type' => 'required',
            'status' => 'required',
        );

        $fieldNames = array(
            'name' => 'Name', 
            'type' => 'Type',
            'status' => 'Status',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }

        $slug = Str::slug($request->name); 

        $originalSlug = $slug;
        $counter = 1;

        while (TransactionType::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        $transactiontype = TransactionType::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'slug'          => $slug,
            'type'          => $request->type,
            'status'        => $request->status,
            'orderby'       => $request->orderby,
        ]);
        
        $this->helper->one_time_message('success', 'Transaction Type added Successfully');
        return redirect('admin/transactiontypes');
    }
    
    public function edit($id)
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'transactiontypes';
        $data['transactiontype'] = TransactionType::where('id', $id)->first();
        return view('admin.transactiontypes.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        $rules = array(
            'name' => 'required',
            'type' => 'required',
            'status' => 'required',
        );

        $fieldNames = array(
            'name' => 'Name', 
            'type' => 'Type',
            'status' => 'Status',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }

        $transactiontype = TransactionType::where('id', $id)->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'type'          => $request->type,
            'status'        => $request->status,
            'orderby'       => $request->orderby,
        ]);
        
        $this->helper->one_time_message('success', 'Transaction Type Updated Successfully');
        return redirect('admin/transactiontypes');
    }
    
    public function delete($id)
    {
        $setting = TransactionType::where('id', $id)->delete();
        
        $this->helper->one_time_message('success', 'Transaction Type Deleted Successfully');
        return redirect('admin/transactiontypes');
    }
}