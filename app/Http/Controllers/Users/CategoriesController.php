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

class CategoriesController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function index()
    {
        // echo("hello"); die;
        $data['menu']          = 'categories';
        $data['sub_menu']      = 'categories';
        $data['content_title'] = 'Category';
        $data['icon']          = 'shopping-basket';
        $data['list']          = $category  = Categories::orderBy('id', 'desc')->where('user_id',Auth::user()->id)->paginate(10);

        //check Decimal Thousand Money Format Preference
        $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);

        return view('user_dashboard.Category.list', $data);
    }

  

    public function add()
    {
        $data['menu']     = 'categories';
        $data['sub_menu'] = 'categories';
        $data['content_title'] = 'Category';
        
       

        return view('user_dashboard.Category.add', $data);
    }
    
    public function edit($id)
    {
        $data['menu']     = 'categories';
        $data['sub_menu'] = 'categories';
        $data['details'] =   Categories::where('id',$id)->first();
        
      

        return view('user_dashboard.Category.edit', $data);
    }
    public function update(Request $request)
    {
        $rules = array(
            'id'    => 'required|unique:categories,id,'.$request->id,
            'name'  => 'required',
            'image'          => 'mimes:png,jpg,jpeg,gif,bmp',
        );

        $fieldNames = array(
            'id'    => 'ID',
            'name'  => 'Name',
            'image'         => 'The file must be an image (png, jpg, jpeg,gif,bmp)',
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
                
                $Categories  = Categories::find($request->id);
                $Categories->user_id            = Auth::user()->id;
                $Categories->name              = $request->name;
                $Categories->description       = $request->description;
                $Categories->image             = $filename;
                $Categories->save();
                
                

                \DB::commit();
                $this->helper->one_time_message('success', __('Product Updated Successfully!'));
                return redirect('categories');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('categories');
            }
        }
    }
    
    public function store(Request $request)
    {
        $rules = array(
            
            'name'  => 'required',
            'image'          => 'mimes:png,jpg,jpeg,gif,bmp',
        );

        $fieldNames = array(
           
            'name'  => 'Name',
            'image'         => 'The file must be an image (png, jpg, jpeg,gif,bmp)',
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

                $Categories                    = new Categories();
                $Categories->user_id            = Auth::user()->id;
                $Categories->name              = $request->name;
                $Categories->order              = $request->order;
                $Categories->description       = $request->description;
                $Categories->image             = $filename;
                $Categories->save();
                
                $rowId = $Categories->id;
                
              

                \DB::commit();
                $this->helper->one_time_message('success', __('Category Created Successfully!'));
                return redirect('categories');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('categories');
            }
        }
    }

    // public function edit($id)
    // {
    //     $data['menu']             = 'merchant';
    //     $data['sub_menu']         = 'merchant';
    //     $data['content_title']    = 'Merchant';
    //     $data['icon']             = 'user';
    //     $data['activeCurrencies'] = $activeCurrencies = Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'code']);
    //     $data['merchant']         = $merchant         = Merchant::with('currency:id,code')->find($id);
    //     $data['defaultWallet']    = $defaultWallet    = Wallet::with(['currency:id,code'])->where(['user_id' => $merchant->user->id, 'is_default' => 'Yes'])->first(['currency_id']); //new
    //     if (!isset($merchant) || $merchant->user_id != Auth::user()->id)
    //     {
    //         abort(404);
    //     }
    //     return view('user_dashboard.Merchant.edit', $data);
    // }

    // public function update(Request $request)
    // {
    //     // dd($request->all());
    //     $rules = array(
    //         'business_name' => 'required|unique:merchants,business_name,' . $request->id,
    //         'site_url'      => 'required|url',
    //         'note'          => 'required',
    //         'logo'          => 'mimes:png,jpg,jpeg,gif,bmp',
    //     );

    //     $fieldNames = array(
    //         'business_name' => 'Business Name',
    //         'site_url'      => 'Site url',
    //         'note'          => 'Note',
    //         'logo'          => 'The file must be an image (png, jpg, jpeg, gif,bmp)',
    //     );

    //     $validator = Validator::make($request->all(), $rules);
    //     $validator->setAttributeNames($fieldNames);

    //     if ($validator->fails())
    //     {
    //         return back()->withErrors($validator)->withInput();
    //     }
    //     else
    //     {
    //         $picture  = $request->logo;
    //         $filename = null;

    //         try
    //         {
    //             \DB::beginTransaction();

    //             if (isset($picture))
    //             {
    //                 $dir      = public_path("/user_dashboard/merchant/");
    //                 $ext      = $picture->getClientOriginalExtension();
    //                 $filename = time() . '.' . $ext;

    //                 if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp')
    //                 {
    //                     $img = Image::make($picture->getRealPath());
    //                     $img->resize(100, 80)->save($dir . '/' . $filename);
    //                     $img->resize(70, 70)->save($dir . '/thumb/' . $filename);
    //                 }
    //                 else
    //                 {
    //                     $this->helper->one_time_message('error', 'Invalid Image Format!');
    //                 }
    //             }
    //             $Merchant                = Merchant::find($request->id, ['id', 'currency_id', 'business_name', 'site_url', 'note', 'logo']);
    //             $Merchant->currency_id   = $request->currency_id; //2.3
    //             $Merchant->business_name = $request->business_name;
    //             $Merchant->site_url      = $request->site_url;
    //             $Merchant->note          = $request->note;
    //             if ($filename != null)
    //             {
    //                 $Merchant->logo = $filename;
    //             }
    //             $Merchant->save();

    //             \DB::commit();
    //             $this->helper->one_time_message('success', __('Merchant Updated Successfully!'));
    //             return redirect('merchants');
    //         }
    //         catch (\Exception $e)
    //         {
    //             \DB::rollBack();
    //             $this->helper->one_time_message('error', $e->getMessage());
    //             return redirect('merchants');
    //         }
    //     }
    // }

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

    public function payments()
    {
        $data['menu']              = 'merchant_payment';
        $data['sub_menu']          = 'merchant_payment';
        $data['content_title']     = 'Merchant payments';
        $data['icon']              = 'user';
        $merchant                  = Merchant::where('user_id', Auth::user()->id)->pluck('id')->toArray();
        $data['merchant_payments'] = MerchantPayment::with(['merchant:id,business_name', 'payment_method:id,name', 'currency:id,code'])->whereIn('merchant_id', $merchant)
            ->select('id', 'created_at', 'merchant_id', 'payment_method_id', 'order_no', 'amount', 'charge_percentage', 'charge_fixed', 'total', 'currency_id', 'status')
            ->orderBy('id', 'desc')->paginate(15);

        return view('user_dashboard.Merchant.payments', $data);
    }
}
