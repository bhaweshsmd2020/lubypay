<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\CountriesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\CountryPayout;
use App\Models\Label;

class CountryController extends Controller
{
    protected $helper;

    public function __construct() 
    {
        $this->helper = new Common();
    }

    public function index()
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'country';
        $data['countries'] = Country::get();
        return view('admin.countries.view', $data);
    }

    public function add(Request $request)
    {
        if (!$_POST)
        {
            $data['menu']     = 'configurations';
            $data['sub_menu'] = 'country';
            return view('admin.countries.add', $data);
        }
        else if ($_POST)
        {
            $this->validate($request, [
                'short_name'  => 'required|unique:countries,short_name',
                'name'        => 'required',
                'iso3'        => 'required|max:3',
                'number_code' => 'required|numeric',
                'phone_code'  => 'required|numeric',
            ]);

            $country              = new Country();
            $country->short_name  = $request->short_name;
            $country->name        = $request->name;
            $country->iso3        = $request->iso3;
            $country->number_code = $request->number_code;
            $country->phone_code  = $request->phone_code;
            $country->save();
            $this->helper->one_time_message('success', 'Country Added Successfully');
            return redirect('admin/settings/country');
        }

    }

    public function update(Request $request)
    {
        if (!$_POST)
        {
            $data['menu']     = 'configurations';
            $data['sub_menu'] = 'country';
            $data['result'] = Country::find($request->id);
            return view('admin.countries.edit', $data);
        }
        else if ($_POST)
        {
            $this->validate($request, [
                'short_name'  => 'required|unique:countries,short_name,' . $request->id,
                'name'        => 'required',
                'iso3'        => 'required|max:3',
                'number_code' => 'required|numeric',
                'phone_code'  => 'required|numeric',
                'status'  => 'required|numeric',
            ]);

            $country              = Country::find($request->id);
            $country->short_name  = $request->short_name;
            $country->name        = $request->name;
            $country->iso3        = $request->iso3;
            $country->number_code = $request->number_code;
            $country->phone_code  = $request->phone_code;
            $country->status  = $request->status;
            $country->save();
            $this->helper->one_time_message('success', 'Country Updated Successfully');
            return redirect('admin/settings/country');
        }
    }

    public function delete(Request $request)
    {
        Country::find($request->id)->delete();
        $this->helper->one_time_message('success', 'Country Deleted Successfully');
        return redirect('admin/settings/country');
    }
    
    public function add_label(Request $request)
    {
        if (!$_POST)
        {
            $data['menu']     = 'configurations';
            $data['sub_menu'] = 'country';
            $data['result'] = Country::find($request->id);
            $data['labels'] = Label::where('status', '1')->get();
            // $data['labels'] = CountryPayout::where('country', $request->id)->orderBy('sort_by', 'asc')->get();
            return view('admin.countries.payout', $data);
        }
        else if ($_POST)
        {
            $payout_methods = $request->label;
            foreach($payout_methods as $k=>$payout_method){
                $rs = CountryPayout::create([
                    'payout_method' => $payout_method,
                    'country' => $request->country,
                    'sort_by' => ++$k
                ]);
            }
            
            $this->helper->one_time_message('success', 'Country Payout Updated Successfully');
            return back();
        }
    }
    
    public function edit_label(Request $request, $id)
    {
        $check = CountryPayout::where('payout_method', $id)->where('country', $request->country)->first();
        if(!empty($check)){
            $update = CountryPayout::where('payout_method', $id)->where('country', $request->country)->update([
                'sort_by' => $request->sort_by,
                'status' => $request->status,
                'required' => $request->required
            ]);
        }else{
            $update = CountryPayout::create([
                'payout_method' => $id,
                'country' => $request->country,
                'sort_by' => $request->sort_by,
                'status' => $request->status,
                'required' => $request->required
            ]);
        }
        
        $this->helper->one_time_message('success', 'Country Payout updated Successfully');
        return back();
    }
    
    public function delete_label($id)
    {
        CountryPayout::where('id', $id)->delete();
        $this->helper->one_time_message('success', 'Country Payout Deleted Successfully');
        return back();
    }
    
    public function kyc_methods(Request $request, $id)
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'country';
        $data['result'] = Country::find($request->id);
        return view('admin.countries.kyc', $data);
    }
    
    public function edit_kyc_methods(Request $request, $id)
    {
        $data['menu']   = 'KYC Methods';
        
        $update = Country::where('id', $id)->update([
            'automatic_kyc' => $request->automatic_kyc,
            'manual_kyc' => $request->manual_kyc
        ]);
        
        $this->helper->one_time_message('success', 'KYC Method updated Successfully');
        return redirect('admin/settings/country');
    }
}
