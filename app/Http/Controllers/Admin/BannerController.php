<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\MerchantsDataTable;
use App\DataTables\Admin\MerchantsRequestDataTable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\MerchantApp;
use App\Models\MerchantBusinessDetail;
use App\Models\MerchantDocument;
use App\Models\MerchantGroup;
use App\Models\MerchantGroupDocument;
use App\Models\MerchantPackages;
use App\Models\MerchantPayment;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\File;
use DB;
use App\Models\AppPage;

class BannerController extends Controller
{
    protected $helper;
    protected $email;
    protected $merchant;

    public function __construct()
    {
        $this->helper   = new Common();
        $this->email    = new EmailController();
        $this->merchant = new Merchant();
    }

    public function index()
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'banner';
        $data['pages'] = AppPage::where('status', 'Active')->get();
        $data['lists'] = DB::table('banner')->orderBy('banner_id', 'desc')->get();
        return view('admin.banner.list', $data);
    }
    
    public function add()
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'banner';
        $data['pages'] = AppPage::where('status', 'Active')->get();
        return view('admin.banner.add', $data);
    }
    
    public function store(Request $request)
    {
        
        $rules = array(
            'banner_image'          => 'required',
        );

        $fieldNames = array(
            'banner_image' => 'Banner IMage',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
           
            $data = array(
            'banner_title'      =>$request->banner_title,
            'banner_text'       =>$request->banner_text,
            'app_page'          =>$request->app_page,
            'position'          =>$request->position,
            'language'          =>$request->language,
            'redirect_url'      =>$request->redirect_url,
            'status'            =>$request->status,
            'app_redirect'      =>$request->app_redirect,
            'platform'          =>$request->platform,
            );
            if ($request->hasFile('banner_image'))
            {
                $fileName     = $request->file('banner_image');
                $originalName = $fileName->getClientOriginalName();
                $uniqueName   = strtolower(time() . '.' . $fileName->getClientOriginalExtension());
                $file_extn    = strtolower($fileName->getClientOriginalExtension());
                $path       = 'uploads/banner';
                $uploadPath = public_path($path); //problem
                $fileName->move($uploadPath, $uniqueName);
                
                $data['banner_image'] = $uniqueName;
            }
            
            $id = DB::table('banner')->insert($data);
            if($id)
            {
                $this->helper->one_time_message('success', 'Banner Saved Successfully!');
                return redirect('admin/banner');
            }
            else
            {
                $this->helper->one_time_message('error', 'Something Wrong try Again !');
                return redirect()->back();
            } 
        }
    }
    
    public function edit($id)
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'banner';
        $data['pages'] = AppPage::where('status', 'Active')->get();
        $data['banners'] = DB::table('banner')->where('banner_id',$id)->first();
       
        return view('admin.banner.edit', $data);
    }
    
    public function update(Request $request)
    {
            $data = array(
            'banner_title'=>$request->banner_title,
            'banner_text'=>$request->banner_text,
            'app_page'=>$request->app_page,
            'position'=>$request->position,
            'language'          =>$request->language,
            'redirect_url'      =>$request->redirect_url,
            'status'=>$request->status,
            'app_redirect'=>$request->app_redirect,
            'platform' =>$request->platform,
            );
            if ($request->hasFile('banner_image'))
            {
                $fileName     = $request->file('banner_image');
                $originalName = $fileName->getClientOriginalName();
                $uniqueName   = strtolower(time() . '.' . $fileName->getClientOriginalExtension());
                $file_extn    = strtolower($fileName->getClientOriginalExtension());
                $path       = 'uploads/banner';
                $uploadPath = public_path($path); //problem
                $fileName->move($uploadPath, $uniqueName);
                
                $data['banner_image'] =  $uniqueName;
            }
            
            $id = DB::table('banner')->where('banner_id',$request->id)->update($data);
            if($id)
            {
                $this->helper->one_time_message('success', 'Banner Updated Successfully!');
                return redirect('admin/banner');
            }
            else
            {
                $this->helper->one_time_message('error', 'Something Wrong try Again !');
                return redirect()->back();
            } 
    }
    
    
    
    
    public function delete($id)
    {
        $id = DB::table('banner')->where('banner_id',$id)->delete();
        if($id)
        {
            $this->helper->one_time_message('success', 'Banner Deleted Successfully!');
            return redirect('admin/banner');
        }
        else
        {
            $this->helper->one_time_message('error', 'Something Wrong try Again !');
            return redirect()->back();
        } 
    }
    
    
    
    
    
    
    
    
    
    
    
    
    












}
