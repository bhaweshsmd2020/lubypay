<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Session;
use Validator;
use Intervention\Image\Facades\Image;
use Auth;
use App\Models\AppPage;

class ApppageController extends Controller
{
    protected $helper;
 
    public function __construct()
    {
        $this->helper         = new Common();
    }
    
    public function index()
    {
        $data['menu'] = 'settings';
        $data['sub_menu'] = 'app-store-credentials';
        $data['sub_sub_menu'] = 'app_pages';
        
        $data['pages'] = AppPage::orderBy('id', 'desc')->get();
        return view('admin.app_pages.index', $data);
    }
    
    public function add()
    {
        $data['menu'] = 'settings';
        $data['sub_menu'] = 'app-store-credentials';
        $data['sub_sub_menu'] = 'app_pages';
        
        return view('admin.app_pages.create', $data);
    }
    
    public function store(Request $request)
    {
        $rules = array(
            'app_page' => 'required',
            'page_name' => 'required'
        );

        $fieldNames = array(
            'app_page' => 'App Page',
            'page_name' => 'Page Name',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
         
        $rs = AppPage::create([
            'app_page'  =>$request->app_page,
            'page_name' =>$request->page_name,
            'status'    =>$request->status,
        ]);
        
        $this->helper->one_time_message('success', 'App Page Created Successfully!');
        return redirect('admin/apppages');
    }
    
    public function edit($id)
    {
        $data['menu'] = 'settings';
        $data['sub_menu'] = 'app-store-credentials';
        $data['sub_sub_menu'] = 'app_pages';
        
        $data['page'] = AppPage::where('id', $id)->first();
        return view('admin.app_pages.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        $rules = array(
            'app_page' => 'required',
            'page_name' => 'required'
        );

        $fieldNames = array(
            'app_page' => 'App Page',
            'page_name' => 'Page Name',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
         
        $rs = AppPage::where('id', $id)->update([
            'app_page'  =>$request->app_page,
            'page_name' =>$request->page_name,
            'status'    =>$request->status,
        ]);
        
        $this->helper->one_time_message('success', 'App Page Updated Successfully!');
        return redirect('admin/apppages');
    }
    
    public function delete($id)
    {
        $rs = AppPage::where('id', $id)->delete();
        
        $this->helper->one_time_message('success', 'App Page Deleted Successfully!');
        return redirect('admin/apppages');
    }
}
