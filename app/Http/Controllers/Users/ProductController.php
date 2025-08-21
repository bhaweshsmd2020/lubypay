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

class ProductController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function index()
    {
        // echo("hello"); die;
        $data['menu']          = 'product';
        $data['sub_menu']      = 'product';
        $data['content_title'] = 'Product';
        $data['icon']          = 'shopping-basket';
        $data['list']          = $products  = Product::with(['appInfo', 'currency:id,code','category'])->where(['userid' => Auth::user()->id])->orderBy('id', 'desc')->paginate(10);

        //check Decimal Thousand Money Format Preference
        $data['preference'] = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);

        return view('user_dashboard.Product.list', $data);
    }

    //Standard Merchant QrCode - starts
    public function generateStandardMerchantPaymentQrCode(Request $request)
    {
        // dd($request->all());
        $qrCode           = QrCode::where(['object_id' => $request->merchantId, 'object_type' => 'standard_merchant', 'status' => 'Active'])->first(['id', 'secret']);
        $merchantCurrency = Currency::where('id', $request->merchantDefaultCurrency)->first(['code']); // NEW - THIS IS ADDED AS in PAYMONEY 2.1 THERE WAS DEFAULT CURRENCY WHEN QRCODE WAS DONE
        if (empty($qrCode))
        {
            $createMerchantQrCode              = new QrCode();
            $createMerchantQrCode->object_id   = $request->merchantId;
            $createMerchantQrCode->object_type = 'standard_merchant';
            $createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->paymentAmount . '-' . str_random(6));
            $createMerchantQrCode->status      = 'Active';
            $createMerchantQrCode->save();
            return response()->json([
                'status' => true,
                'secret' => urlencode($createMerchantQrCode->secret),
            ]);
        }
        else
        {
            //Make existing qr-code inactive
            $qrCode->status = 'Inactive';
            $qrCode->save();

            //create a new qr-code entry on each update, after making status 'Inactive'
            $createMerchantQrCode              = new QrCode();
            $createMerchantQrCode->object_id   = $request->merchantId;
            $createMerchantQrCode->object_type = 'standard_merchant';
            $createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->paymentAmount . '-' . str_random(6));
            $createMerchantQrCode->status      = 'Active';
            $createMerchantQrCode->save();
            return response()->json([
                'status' => true,
                'secret' => urlencode($createMerchantQrCode->secret),
            ]);
        }
    }
    //Standard Merchant QrCode - ends

    //Express Merchant QrCode - starts
    public function generateExpressMerchantQrCode(Request $request)
    {
        // dd($request->all());

        //merchantDefaultCurrencyId
        // $qrCode = QrCode::where(['object_id' => $request->merchantId, 'status' => 'Active'])->first(['id','secret']);
        $qrCode           = QrCode::where(['object_id' => $request->merchantId, 'object_type' => 'express_merchant', 'status' => 'Active'])->first(['id', 'secret']);
        $merchantCurrency = Currency::where('id', $request->merchantDefaultCurrencyId)->first(['code']); // NEW - THIS IS ADDED AS in PAYMONEY 2.1 THERE WAS DEFAULT CURRENCY WHEN QRCODE WAS DONE
        if (empty($qrCode))
        {
            $createMerchantQrCode              = new QrCode();
            $createMerchantQrCode->object_id   = $request->merchantId;
            $createMerchantQrCode->object_type = 'express_merchant';
            $createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->clientId . str_random(6));
            $createMerchantQrCode->status      = 'Active';
            $createMerchantQrCode->save();
            return response()->json([
                'status' => true,
                'secret' => urlencode($createMerchantQrCode->secret),
            ]);
        }
        else
        {
            return response()->json([
                'status' => true,
                'secret' => urlencode($qrCode->secret),
            ]);
        }
    }

    public function updateExpressMerchantQrCode(Request $request)
    {
        // dd($request->all());

        $qrCode = QrCode::where(['object_id' => $request->merchantId, 'object_type' => 'express_merchant', 'status' => 'Active'])->first(['id', 'secret']);

        $merchantCurrency = Currency::where('id', $request->merchantDefaultCurrencyId)->first(['code']); // NEW - THIS IS ADDED AS in PAYMONEY 2.1 THERE WAS DEFAULT CURRENCY WHEN QRCODE WAS DONE
        if (empty($qrCode))
        {
            $createMerchantQrCode              = new QrCode();
            $createMerchantQrCode->object_id   = $request->merchantId;
            $createMerchantQrCode->object_type = 'express_merchant';
            $createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->clientId . str_random(6));
            $createMerchantQrCode->status      = 'Active';
            $createMerchantQrCode->save();
            return response()->json([
                'status' => true,
                'secret' => urlencode($createMerchantQrCode->secret),
            ]);
        }
        else
        {
            // //Make existing qr-code inactive
            $qrCode->status = 'Inactive';
            $qrCode->save();

            //create a new qr-code entry on each update, after making status 'Inactive'
            $createMerchantQrCode              = new QrCode();
            $createMerchantQrCode->object_id   = $request->merchantId;
            $createMerchantQrCode->object_type = 'express_merchant';
            $createMerchantQrCode->secret      = convert_string('encrypt', $createMerchantQrCode->object_type . '-' . $request->merchantId . '-' . $merchantCurrency->code . '-' . $request->clientId . str_random(6));
            $createMerchantQrCode->status      = 'Active';
            $createMerchantQrCode->save();
            return response()->json([
                'status' => true,
                'secret' => urlencode($createMerchantQrCode->secret),
            ]);
        }
    }
    //Express Merchant QrCode - ends

    //Print Merchant QrCode - starts
    public function printMerchantQrCode($merchantId, $objectType)
    {
        $this->helper->printQrCode($merchantId, $objectType);
    }
    //Print Merchant QrCode - ends

    public function add()
    {
        $data['menu']     = 'product';
        $data['sub_menu'] = 'product';

        //pm_v2.3
        $data['activeCurrencies'] = $activeCurrencies = Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'code']);
        $data['defaultWallet']    = $defaultWallet    = Wallet::where(['user_id' => auth()->user()->id, 'is_default' => 'Yes'])->first(['currency_id']);
        
         $data['categories']    =Categories::where('user_id',Auth::user()->id)->where('active',1)->get();
         $data['attributes']    =Attributes::where('user_id',Auth::user()->id)->where('active',1)->get();
         $data['attributesvalues']    =AttributeValues::where('active',1)->get();
         
         
        return view('user_dashboard.Product.add', $data);
    }
    
    public function edit($id)
    {
        $data['menu']     = 'product';
        $data['sub_menu'] = 'product';
        $data['details'] =   Product::where('id',$id)->first();
        // echo $data['details']['name'];
        // echo "<pre>";
        // print_r($data['details']);
        // die;
        //pm_v2.3
        $data['activeCurrencies'] = $activeCurrencies = Currency::where(['status' => 'Active', 'type' => 'fiat'])->get(['id', 'code']);
        $data['defaultWallet']    = $defaultWallet    = Wallet::where(['user_id' => auth()->user()->id, 'is_default' => 'Yes'])->first(['currency_id']);
        
         $data['categories']    =Categories::where('user_id',Auth::user()->id)->where('active',1)->get();
         $data['attributes']    =Attributes::where('user_id',Auth::user()->id)->where('active',1)->get();
         $data['attributesvalues']    =AttributeValues::where('user_id',Auth::user()->id)->where('active',1)->get();
         
         

        return view('user_dashboard.Product.edit', $data);
    }
    public function update(Request $request)
    {
        $attributes    =Attributes::where('active',1)->get();
        
       
        $rules = array(
            'product_id'    => 'required|unique:product,user_product_id,'.$request->id,
            'product_name'  => 'required',
            'price'         => 'required',
            /* 'type'          => 'required',
            'note'          => 'required', */
            'image'          => 'mimes:png,jpg,jpeg,gif,bmp',
        );

        $fieldNames = array(
            'product_id'    => 'Product ID',
            'product_name'  => 'Product Name',
            'price'         => 'Product Price',
            'description'   => 'Description',
            'quantity'      => 'Quantity',
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
                    $dir = public_path("/user_dashboard/product/");
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
                
                $Product  = Product::find($request->id);
                $Product->userid            = Auth::user()->id;
                $Product->user_product_id   = $request->product_id;
                $Product->category_id       = $request->category_id;
                
                $Product->name              = $request->product_name;
                $Product->price             = $request->price;
                $Product->description       = $request->description;
                $Product->quantity          = $request->quantity;
                $Product->image             = $filename;
                $Product->save();
                
                
                 foreach($attributes as $attr)
                {
                    if($request->input('attributes_'.$attr->id))
                    {
                        $check = DB::table('product_attributes')->where('product_id',$request->id)->where('attributes',$attr->id)->first();
                        $data = array(
                            'product_id'=>$request->id,
                            'attributes' =>$attr->id,
                            'attributes_values'=>json_encode($request->input('attributes_'.$attr->id))
                            );
                        if($check)
                        {
                            DB::table('product_attributes')->where('product_id',$request->id)->where('attributes',$attr->id)->update($data);
                        }
                        else
                        {
                            DB::table('product_attributes')->insert($data);
                        }
                        
                    }
                }
                

                \DB::commit();
                $this->helper->one_time_message('success', __('Product Updated Successfully!'));
                return redirect('products');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('products');
            }
        }
    }
    
    public function store(Request $request)
    {
        
        $attributes    =Attributes::where('active',1)->get();
        
        $rules = array(
            'product_id'    => 'required',
            'product_name'  => 'required',
            'category_id'  => 'required',
           
            'price'         => 'required',
            /* 'type'          => 'required',
            'note'          => 'required', */
            'image'          => 'mimes:png,jpg,jpeg,gif,bmp',
        );

        $fieldNames = array(
            'product_id'    => 'Product ID',
            'category_id'   =>'Category',
           
            'product_name'  => 'Product Name',
            'price'         => 'Product Price',
            'description'   => 'Description',
            'quantity'      => 'Quantity',
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
                    $dir = public_path("/user_dashboard/product/");
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

                $Product                    = new Product();
                $Product->userid            = Auth::user()->id;
                $Product->user_product_id   = $request->product_id;
                $Product->category_id       = $request->category_id;
                // $Product->attribute_value_ids   = (count($request->attribute_values) > 0) ? json_encode($request->attribute_values) :null;
                $Product->name              = $request->product_name;
                $Product->price             = $request->price;
                $Product->description       = $request->description;
                $Product->quantity          = $request->quantity;
                $Product->image             = $filename;
                $Product->save();
                
                $rowId = $Product->id;
                
                foreach($attributes as $attr)
                {
                    if($request->input('attributes_'.$attr->id))
                    {
                        $check = DB::table('product_attributes')->where('product_id',$Product->id)->where('attributes',$attr->id)->first();
                        $data = array(
                            'product_id'=>$Product->id,
                            'attributes' =>$attr->id,
                            'attributes_values'=>json_encode($request->input('attributes_'.$attr->id))
                            );
                        if($check)
                        {
                            DB::table('product_attributes')->where('product_id',$Product->id)->where('attributes',$attr->id)->update($data);
                        }
                        else
                        {
                            DB::table('product_attributes')->insert($data);
                        }
                        
                    }
                }
                
                
                $urlData = 'checkout?id='.$rowId;
                
                $productToUpdate = Product::find($rowId);
                
                $productToUpdate->url               = $urlData;
                $productToUpdate->url_data          = $urlData;
                $productToUpdate->save();
                

                \DB::commit();
                $this->helper->one_time_message('success', __('Product Created Successfully!'));
                return redirect('products');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('products');
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
