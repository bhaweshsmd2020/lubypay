<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\MerchantGroupsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\MerchantGroup;
use App\Models\MerchantGroupDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MerchantGroupController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function index(MerchantGroupsDataTable $dataTable)
    {
        $data['menu'] = 'settings';
        $data['sub_menu'] = 'general';
        $data['sub_sub_menu'] = 'merchant_group';
        
        return $dataTable->render('admin.merchant_group.list', $data);
    }
    
    public function index_document()
    {
        $data['menu'] = 'settings';
        $data['sub_menu'] = 'general';
        $data['sub_sub_menu'] = 'merchant_document';
        
        $data['documents'] = MerchantGroupDocument::all();
        return view('admin.merchant_document.list', $data);
    }
    
    /* public function delete_document($id)
    {
        MerchantGroupDocument::findOrFail($id)->delete();
        return redirect('admin/settings/merchant-document');
    } */
    
    public function delete_document(Request $request)
    {
        $merchantDocument = MerchantGroupDocument::find($request->id);
        if (isset($merchantDocument))
        {
            $merchantDocument->delete();
            $this->helper->one_time_message('success', 'Document Deleted Successfully');
        }
        return redirect('admin/settings/merchant-document');
    }
    
    public function update_document(Request $request)
    {
        if (!$_POST)
        {
            $data['docs'] = MerchantGroupDocument::find($request->id);
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'general';
            $data['sub_sub_menu'] = 'merchant_document';
            return view('admin.merchant_document.edit', $data);
        }
        else if ($_POST)
        {
            $rules = array(
                'name'        => 'required|unique:merchant_group_documents,name,' . $request->id
            );

            $fieldNames = array(
                'name'        => 'Name'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {

                return back()->withErrors($validator)->withInput();
            }
            else
            {
                $merchantGroup              = MerchantGroupDocument::find($request->id);
                $merchantGroup->name        = $request->name;
        		$merchantGroup->save();
        		
        		$this->helper->one_time_message('success', 'Merchant Document Updated Successfully');
                return redirect('admin/settings/merchant-document');
            }
        }
        else
        {
            return redirect('admin/settings/merchant-document');
        }
    }

    public function add(Request $request)
    {
        if (!$_POST)
        {
            $data['docs'] = MerchantGroupDocument::all();
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'general';
            $data['sub_sub_menu'] = 'merchant_group';
            $data['merchantGroups'] = $merchantGroups = MerchantGroup::get();
            //check Decimal Thousand Money Format Preference
            $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);
            return view('admin.merchant_group.add', $data);
        }
        else if ($_POST)
        {
            // dd($request->all());
            $rules = array(
                'name'        => 'required|unique:merchant_groups,name',
                'description' => 'required',
                'fee'         => 'required|numeric',
                'package_fee' => 'required|numeric',
            );

            $fieldNames = array(
                'name'        => 'Name',
                'description' => 'Description',
                'fee'         => 'Fee',
                'package_fee' => 'Package Fee',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                $merchantGroup              = new MerchantGroup();
                $merchantGroup->name        = $request->name;
                $merchantGroup->description = $request->description;
                $merchantGroup->fee         = $request->fee;
                $merchantGroup->package_fee         = $request->package_fee;
                $merchantGroup->is_default  = $request->default;
                $merchantGroup->documents  = $request->documents;
                // dd($merchantGroup);
                $merchantGroup->save();

                if ($merchantGroup->is_default == 'Yes')
                {
                    MerchantGroup::where(['is_default' => 'Yes'])->where('id', '!=', $merchantGroup->id)->update(['is_default' => 'No']);
                }
                $this->helper->one_time_message('success', 'Merchant Group Added Successfully');
                return redirect('admin/settings/merchant-group');
            }
        }
        else
        {
            return redirect('admin/settings/merchant-group');
        }
    }

    public function addDocument(Request $request)
    {
        if (!$_POST)
        {
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'general';
            $data['sub_sub_menu'] = 'merchant_group';
            $data['merchantGroups'] = $merchantGroups = MerchantGroup::get();
            //check Decimal Thousand Money Format Preference
            $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);
            return view('admin.merchant_group.addDoc', $data);
        }
        else if ($_POST)
        {
            // dd($request->all());
            $rules = array(
                'name'        => 'required|unique:merchant_groups,name',
            );

            $fieldNames = array(
                'name'        => 'Name',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                $merchantGroup              = new MerchantGroupDocument();
                $merchantGroup->name        = $request->name;
                $merchantGroup->save();
                $this->helper->one_time_message('success', 'Document Added Successfully');
                return redirect('admin/settings/merchant-group');
            }
        }
        else
        {
            return redirect('admin/settings/merchant-group');
        }
    }

    public function update(Request $request)
    {
        if (!$_POST)
        {
            $data['docs'] = MerchantGroupDocument::all();
            $data['menu'] = 'settings';
            $data['sub_menu'] = 'general';
            $data['sub_sub_menu'] = 'merchant_group';
            $data['merchantGroup'] = $merchantGroup = MerchantGroup::find($request->id);
            //check Decimal Thousand Money Format Preference
            $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);
            return view('admin.merchant_group.edit', $data);
        }
        else if ($_POST)
        {
            $rules = array(
                'name'        => 'required|unique:merchant_groups,name,' . $request->id,
                'description' => 'required',
                'fee'         => 'required|numeric',
                'package_fee' => 'required|numeric',
            );

            $fieldNames = array(
                'name'        => 'Name',
                'description' => 'Description',
                'fee'         => 'Fee',
                'package_fee' => 'Package Fee',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {

                return back()->withErrors($validator)->withInput();
            }
            else
            {
                $merchantGroup              = MerchantGroup::find($request->id,['id','name','description','fee','is_default']);
                $merchantGroup->name        = $request->name;
                $merchantGroup->description = $request->description;
                $merchantGroup->fee         = $request->fee;
                $merchantGroup->is_default  = $request->default;
                $merchantGroup->package_fee  = $request->package_fee;
                $merchantGroup->documents  = $request->documents;
        		$merchantGroup->save();

                if ($merchantGroup->is_default == 'Yes')
                {
                    MerchantGroup::where(['is_default' => 'Yes'])->where('id', '!=', $merchantGroup->id)->update(['is_default' => 'No']);
                }
        		$this->helper->one_time_message('success', 'Merchant Group Updated Successfully');
                return redirect('admin/settings/merchant-group');
            }
        }
        else
        {
            return redirect('admin/settings/merchant-group');
        }
    }

    public function delete(Request $request)
    {
        $merchantGroup = MerchantGroup::find($request->id);
        if (isset($merchantGroup) && $merchantGroup->is_default == 'Yes')
        {
            $this->helper->one_time_message('error', 'Default Group Cannot Be Deleted');
        }
        else
        {
            if (isset($merchantGroup))
            {
                $merchantGroup->delete();
                $this->helper->one_time_message('success', 'Group Deleted Successfully');
            }
        }
        return redirect('admin/settings/merchant-group');
    }
}
