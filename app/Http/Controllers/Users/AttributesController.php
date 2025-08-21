<?php


namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;


use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\User;
use App\Models\Product;
use App\Models\Wallet;

use App\Models\Categories;
use App\Models\Attributes;
use App\Models\AttributeValues;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;
use Validator;

class AttributesController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function index()
    {
        // echo("hello"); die;
        $data['menu']          = 'attributes';
        $data['sub_menu']      = 'attributes';
        $data['content_title'] = 'Attributes';
        $data['icon']          = 'shopping-basket';
        $data['list']          = $attributes  = Attributes::orderBy('id', 'desc')->where('user_id',Auth::user()->id)->paginate(10);
        
        $data['listvalues']          = $attributevalues  = AttributeValues::orderBy('id', 'desc')->where('user_id',Auth::user()->id)->with('attribute')->paginate(10);

        //check Decimal Thousand Money Format Preference
        $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);

        return view('user_dashboard.Attributes.list', $data);
    }

  

    public function add()
    {
        $data['menu']     = 'attributes';
        $data['sub_menu'] = 'attributes';
        $data['content_title'] = 'Attributes';
        
       

        return view('user_dashboard.Attributes.add', $data);
    }
    
    public function edit($id)
    {
        $data['menu']     = 'attributes';
        $data['sub_menu'] = 'attributes';
        $data['details'] =   Attributes::where('id',$id)->first();
        
      

        return view('user_dashboard.Attributes.edit', $data);
    }
    public function update(Request $request)
    {
        $rules = array(
            'id'    => 'required|unique:attributes,id,'.$request->id,
            'name'  => 'required',
           
        );

        $fieldNames = array(
            'id'    => 'ID',
            'name'  => 'Name',
            
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

                $filename = null;
                $picture  = $request->image;
                if (isset($picture))
                {
                    $dir = public_path("/user_dashboard/categories/");
                    $ext = $picture->getClientOriginalExtension();
                    // dd($ext);
                    $filename = time() . '.' . $ext;

                    if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp')
                    {
                        $img = Image::make($picture->getRealPath());
                        $img->resize(200, 200)->save($dir . '/' . $filename);
                        $img->resize(200, 200)->save($dir . '/thumb/' . $filename);
                    }
                    else
                    {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }

                // $Product                    = new Product();
                // Merchant::find($request->id, ['id', 'currency_id', 'business_name', 'site_url', 'note', 'logo']);
                
                $Attributes  = Attributes::find($request->id);
                $Attributes->user_id            = Auth::user()->id;
                $Attributes->name              = $request->name;
                $Attributes->save();
                
                

                \DB::commit();
                $this->helper->one_time_message('success', __('Attributes Updated Successfully!'));
                return redirect('attributes');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('attributes');
            }
        }
    }
    
    public function store(Request $request)
    {
        $rules = array(
            'name'  => 'required',
        );

        $fieldNames = array(
           
            'name'  => 'Name',
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

                $filename = null;
                $picture  = $request->image;
                if (isset($picture))
                {
                    $dir = public_path("/user_dashboard/categories/");
                    $ext = $picture->getClientOriginalExtension();
                    // dd($ext);
                    $filename = time() . '.' . $ext;

                    if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp')
                    {
                        $img = Image::make($picture->getRealPath());
                        $img->resize(200, 200)->save($dir . '/' . $filename);
                        $img->resize(200, 200)->save($dir . '/thumb/' . $filename);
                    }
                    else
                    {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }

                $Attributes                    = new Attributes();
                $Attributes->user_id            = Auth::user()->id;
                $Attributes->name              = $request->name;
                $Attributes->save();
                
                $rowId = $Attributes->id;

                \DB::commit();
                $this->helper->one_time_message('success', __('Attributes Created Successfully!'));
                return redirect('attributes');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('attributes');
            }
        }
    }

    // attributes values   
    public function addvalue()
    {
        $data['menu']     = 'attributes';
        $data['sub_menu'] = 'attributes';
        $data['content_title'] = 'Attributes';
        $data['list']          = $attributes  = Attributes::where('user_id',Auth::user()->id)->orderBy('id', 'desc')->get();
       return view('user_dashboard.Attributes.addvalue', $data);
    }
    
    public function editvalue($id)
    {
        $data['menu']     = 'attributes';
        $data['sub_menu'] = 'attributes';
        $data['details']  =   AttributeValues::where('id',$id)->first();
        $data['list']     = $attributes  = Attributes::where('user_id',Auth::user()->id)->orderBy('id', 'desc')->get();
        return view('user_dashboard.Attributes.editvalue', $data);
    }
    public function updatevalue(Request $request)
    {
        $rules = array(
            'id'    => 'required|unique:attribute_values,id,'.$request->id,
            'value'  => 'required|unique:attribute_values,value,'.$request->id,
        );

        $fieldNames = array(
            'id'    => 'ID',
            'value'  => 'Value',
            
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

                $filename = null;
                $picture  = $request->image;
                if (isset($picture))
                {
                    $dir = public_path("/user_dashboard/categories/");
                    $ext = $picture->getClientOriginalExtension();
                    // dd($ext);
                    $filename = time() . '.' . $ext;

                    if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp')
                    {
                        $img = Image::make($picture->getRealPath());
                        $img->resize(200, 200)->save($dir . '/' . $filename);
                        $img->resize(200, 200)->save($dir . '/thumb/' . $filename);
                    }
                    else
                    {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }

                // $Product                    = new Product();
                // Merchant::find($request->id, ['id', 'currency_id', 'business_name', 'site_url', 'note', 'logo']);
                
                $Attributes  = AttributeValues::find($request->id);
                $Attributes->user_id            = Auth::user()->id;
                $Attributes->value              = $request->value;
                $Attributes->attribute_id       = $request->attribute_id;
                $Attributes->save();
                
                

                \DB::commit();
                $this->helper->one_time_message('success', __('Attribute Values Updated Successfully!'));
                return redirect('attributes');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('attributes');
            }
        }
    }
    
    public function storevalue(Request $request)
    {
        $rules = array(
            'attribute_id'  => 'required',
            'value'  => 'required|unique:attribute_values,value',
        );

        $fieldNames = array(
           
            'value'  => 'Value',
            'attribute_id' => 'Attribute ID',
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

                $filename = null;
                $picture  = $request->image;
                if (isset($picture))
                {
                    $dir = public_path("/user_dashboard/categories/");
                    $ext = $picture->getClientOriginalExtension();
                    // dd($ext);
                    $filename = time() . '.' . $ext;

                    if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp')
                    {
                        $img = Image::make($picture->getRealPath());
                        $img->resize(200, 200)->save($dir . '/' . $filename);
                        $img->resize(200, 200)->save($dir . '/thumb/' . $filename);
                    }
                    else
                    {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }

                $AttributeValues                    = new AttributeValues();
                $AttributeValues->user_id            = Auth::user()->id;
                $AttributeValues->value              = $request->value;
                $AttributeValues->attribute_id              = $request->attribute_id;
                $AttributeValues->save();
                
                $rowId = $AttributeValues->id;

                \DB::commit();
                $this->helper->one_time_message('success', __('Attribute Values Created Successfully!'));
                return redirect('attributes');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('attributes');
            }
        }
    }

   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
    public function detail($id)
    {
        $data['menu']          = 'merchant';
        $data['sub_menu']      = 'merchant';
        $data['content_title'] = 'Merchant';
        $data['icon']          = 'user';
        $data['merchant']      = $merchant      = Merchant::find($id);
        $data['defaultWallet'] = $defaultWallet = Wallet::with(['currency:id,code'])->where(['user_id' => $merchant->user->id, 'is_default' => 'Yes'])->first(['currency_id']); //new
        if (!isset($merchant) || $merchant->user_id != Auth::user()->id)
        {
            abort(404);
        }
        return view('user_dashboard.Merchant.detail', $data);
    }

   
}
