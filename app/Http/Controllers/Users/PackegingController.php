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

use App\Models\Packeging;


use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;
use Validator;

class PackegingController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function index()
    {
        // echo("hello"); die;
        $data['menu']          = 'packeging';
        $data['sub_menu']      = 'packeging';
        $data['content_title'] = 'Packeging';
        $data['icon']          = 'shopping-basket';
        $data['list']          = $packeging  = Packeging::orderBy('id', 'desc')->where('user_id',Auth::user()->id)->paginate(10);
        
        //check Decimal Thousand Money Format Preference
        $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);

        return view('user_dashboard.Packeging.list', $data);
    }

  

    public function add()
    {
        $data['menu']          = 'packeging';
        $data['sub_menu']      = 'packeging';
        $data['content_title'] = 'Packeging';
        $data['list']          = DB::table('shipping_cost')->where('status',1)->get();
       

        return view('user_dashboard.Packeging.add', $data);
    }
    
    public function edit($id)
    {
         $data['menu']          = 'packeging';
        $data['sub_menu']      = 'packeging';
        $data['content_title'] = 'Packeging';
        $data['list']          = DB::table('shipping_cost')->where('status',1)->get();
        $data['details'] =   Packeging::where('id',$id)->first();
        
      

        return view('user_dashboard.Packeging.edit', $data);
    }
    public function update(Request $request)
    {
        $rules = array(
            'name'  => 'required|unique:packeging,name,'.$request->id,
            'shipping' =>'required',
            'length' =>'required',
             'width' =>'required',
            'height' =>'required',
            'dimension_unit' =>'required',
            'weight' =>'required',
            'weight_unit' =>'required',
        );

        $fieldNames = array(
           
            'name'  => 'Name',
            'shipping' =>'Shipping',
            'length' => 'Length',
            'width' => 'Width',
            'height' => 'Height',
            'dimension_unit' => 'Dimension Uunit',
            'weight' => 'Weight',
            'weight_unit' => 'Weight Unit',
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
                
                $Packeging  = Packeging::find($request->id);
                $Packeging->user_id            = Auth::user()->id;
                 $Packeging->name  = $request->name;
                $Packeging->shipping =$request->shipping;
                $Packeging->length = $request->length;
                $Packeging->width = $request->width;
                $Packeging->height = $request->height;
                $Packeging->dimension_unit = $request->dimension_unit;
                $Packeging->weight = $request->weight;
                $Packeging->weight_unit = $request->weight_unit;
                $Packeging->save();
                
                

                \DB::commit();
                $this->helper->one_time_message('success', __('Packeging Updated Successfully!'));
                return redirect('packeging');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('packeging');
            }
        }
    }
    
    public function store(Request $request)
    {
        $rules = array(
            'name'  => 'required|unique:packeging,name',
            'shipping' =>'required',
            'length' =>'required',
             'width' =>'required',
            'height' =>'required',
            'dimension_unit' =>'required',
            'weight' =>'required',
            'weight_unit' =>'required',
        );

        $fieldNames = array(
           
            'name'  => 'Name',
            'shipping' =>'Shipping',
            'length' => 'Length',
            'width' => 'Width',
            'height' => 'Height',
            'dimension_unit' => 'Dimension Uunit',
            'weight' => 'Weight',
            'weight_unit' => 'Weight Unit',
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

                $Packeging                    = new Packeging();
                $Packeging->user_id            = Auth::user()->id;
                $Packeging->name  = $request->name;
                $Packeging->shipping =$request->shipping;
                $Packeging->length = $request->length;
                $Packeging->width = $request->width;
                $Packeging->height = $request->height;
                $Packeging->dimension_unit = $request->dimension_unit;
                $Packeging->weight = $request->weight;
                $Packeging->weight_unit = $request->weight_unit;
                $Packeging->save();
                
                $rowId = $Packeging->id;

                \DB::commit();
                $this->helper->one_time_message('success', __('Packeging Created Successfully!'));
                return redirect('packeging');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('packeging');
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
