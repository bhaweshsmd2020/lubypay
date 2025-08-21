<?php

namespace App\Http\Controllers\Admin;
use App\DataTables\Admin\PhotoProofsDataTable;
use App\Models\Bank;
use App\Models\PayoutSetting;
use App\DataTables\Admin\AdminsDataTable;
use App\DataTables\Admin\EachUserTransactionsDataTable;
use App\DataTables\Admin\UsersDataTable;
use App\DataTables\Admin\MerchantlistDataTable;
use App\DataTables\Admin\StorelistDataTable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Dispute;
use App\Models\EmailTemplate;
use App\Models\FeesLimit;
use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WithdrawalDetail;
use App\Models\SalesWithdrawal;
use App\Models\Store;
use App\Models\DocumentVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\UserDetail;
use App\Models\Country;
use App\Models\CountryBank;
use App\Models\Categories;
use Image;
use App\Models\Product;
use App\Models\Order;

class StoreController extends Controller
{
    protected $helper;
    protected $email;
    protected $currency;
    protected $user;
    protected $cryptoCurrency;

    public function __construct()
    {
        $this->helper         = new Common();
        $this->email          = new EmailController();
        $this->currency       = new Currency();
        $this->user           = new User();
        $this->transfer = new Transfer();
        $this->withdrawal = new Withdrawal();
        $this->documentVerification = new DocumentVerification();
    }
    
    public function index()
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'store_list';
        $data['stores'] = Store::orderBy('id', 'desc')->get();
        Store::where('read_status', '0')->update(['read_status' => '1']);
        return view('admin.stores.index', $data);
    }
    
    public function edit($id)
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'store_list';
        $data['store'] = Store::find($id);
        $data['countries'] = Country::get();
        return view('admin.stores.edit', $data);
    }
    
    public function update(Request $request)
    {
        $rules = array(
            'name' => 'required',
        );

        $fieldNames = array(
            'name' => 'Name',
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
                $picture = $request->image;
                if (isset($picture)){
                    $ext      = strtolower($picture->getClientOriginalExtension());
                    $filename = time() . '.' . $ext;
                    $dir1 = public_path('/uploads/store/' . $filename);
                    
                    $img = Image::make($picture->getRealPath());
                    $img->resize(100, 100)->save($dir1);
                }else{
                    $store_detail = Store::where('id', $request->id)->first();
                    if(!empty($store_detail)){
                        $filename = $store_detail->image;
                    }else{
                        $filename = null;
                    }
                }
                
                $store = Store::where('id', $request->id)->update([
                    "name"         => $request->name,
                    "description"  => $request->description,
                    "address"      => $request->address,
                    "country"      => $request->country,
                    "state"        => $request->state,
                    "city"         => $request->city,
                    "postalcode"   => $request->postalcode,
                    "tax"          => $request->tax,
                    "image"        => $filename,
                ]);

                $this->helper->one_time_message('success', 'Store Updated Successfully');
                return redirect('admin/store-list');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('admin/store-list');
            }
        }
    }
    
    public function destroy($id)
    {
        $store = Store::find($id);
        if ($store)
        {
            try
            {
                $store = Store::where('id', $id)->delete();

                $this->helper->one_time_message('success', 'Store Deleted Successfully');
                return redirect('admin/store-list');
            }
            catch (\Exception $e)
            {
                \DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect('admin/store-list');
            }
        }
    }
    
    public function category($id)
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'store_list';
        
        $data['store_detail'] = $store_detail = Store::where('id', $id)->first();
        $data['categories'] = Categories::where('user_id', $store_detail->user_id)->orderBy('id', 'desc')->get();
        return view('admin.stores.category.index', $data);
    }
    
    public function categorycreate($id)
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'store_list';
        $data['store_detail'] = $store_detail = Store::where('id', $id)->first();
        return view('admin.stores.category.create', $data);
    }

    public function categorystore(Request $request, $id)
    {
        $rules = array(
            'name' => 'required',
            'description'  => 'required',
        );

        $fieldNames = array(
            'name' => 'Name',
            'description'  => 'Description',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            $user = Store::where('id', $id)->first();
            
            $picture = $request->image;
            if (isset($picture)){
                $ext      = strtolower($picture->getClientOriginalExtension());
                $filename = time() . '.' . $ext;
                $dir1 = public_path('/user_dashboard/categories/thumb/' . $filename);
                
                $img = Image::make($picture->getRealPath());
                $img->resize(100, 100)->save($dir1);
            }else{
                $filename = null;
            }
            
            $rs = Categories::create([
                'name' => $request->name,
                'description' => $request->description,
                'user_id' => $user->user_id,
                'image' => $filename
            ]);
            
            $this->helper->one_time_message('success', 'Store Category Added Successfully');
            return redirect('admin/store/category/list/' . $id);
        }
    }

    public function categoryedit($id, $cat_id)
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'store_list';
        
        $data['store_detail'] = $store_detail = Store::where('id', $id)->first();
        $data['category'] = $users = Categories::find($cat_id);
        return view('admin.stores.category.edit', $data);
    }

    public function categoryupdate(Request $request)
    {
        $rules = array(
            'name' => 'required',
            'description'  => 'required',
        );

        $fieldNames = array(
            'name' => 'Name',
            'description'  => 'Description',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            $user = Store::where('id', $request->store_id)->first();
            
            $picture = $request->image;
            if (isset($picture)){
                $ext      = strtolower($picture->getClientOriginalExtension());
                $filename = time() . '.' . $ext;
                $dir1 = public_path('/user_dashboard/categories/thumb/' . $filename);
                
                $img = Image::make($picture->getRealPath());
                $img->resize(100, 100)->save($dir1);
            }else{
                $cat_detail = Categories::where('id', $request->cat_id)->where('user_id', $user->user_id)->first();
                if(!empty($cat_detail)){
                    $filename = $cat_detail->image;
                }else{
                    $filename = null;
                }
            }
            
            $rs = Categories::where('id', $request->cat_id)->where('user_id', $user->user_id)->update([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $filename
            ]);
            
            $this->helper->one_time_message('success', 'Store Category Updated Successfully');
            return redirect('admin/store/category/list/' . $request->store_id);
        }
    }
    
    public function categorydestroy($id, $cat_id)
    {
        $store = Categories::where('id', $cat_id)->delete();

        $this->helper->one_time_message('success', 'Category Deleted Successfully');
        return redirect('admin/store/category/list/' . $id);
    }
    
    public function product($id)
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'store_list';
        
        $data['store_detail'] = $store_detail = Store::where('id', $id)->first();
        $data['currency'] = Currency::where('id', $store_detail->currency_id)->first();
        $data['products'] = Product::where('userid', $store_detail->user_id)->orderBy('id', 'desc')->get();
        return view('admin.stores.products.index', $data);
    }
    
    public function productcreate($id)
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'store_list';
        $data['store_detail'] = $store_detail = Store::where('id', $id)->first();
        $data['categories'] = Categories::where('user_id', $store_detail->user_id)->orderBy('id', 'desc')->get();
        return view('admin.stores.products.create', $data);
    }

    public function productstore(Request $request, $id)
    {
        $rules = array(
            'name' => 'required',
            'description'  => 'required',
        );

        $fieldNames = array(
            'name' => 'Name',
            'description'  => 'Description',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            $user = Store::where('id', $id)->first();
            
            $picture = $request->image;
            if (isset($picture)){
                $ext      = strtolower($picture->getClientOriginalExtension());
                $filename = time() . '.' . $ext;
                $dir1 = public_path('/user_dashboard/product/thumb/' . $filename);
                
                $img = Image::make($picture->getRealPath());
                $img->resize(100, 100)->save($dir1);
            }else{
                $filename = null;
            }
            
        	$product_sku = $request->product_sku;
        	$category_id = $request->category_id;
        	$name = $request->name;
        	$price = $request->price;
        	$description = $request->description;
        	$quantity = $request->quantity;
        	$discount_type = $request->discount_type;
        	$discount = $request->discount;
        	$image = $request->image;
            
            $data = Product::create([
        	    'userid' => $user->user_id,
        	    'user_product_id' => $product_sku,
                'category_id' => $category_id,
                'name' => $name,
                'price' => $price,
                'description' => $description,
                'quantity' => $quantity,
                'discount_type' => $discount_type,
                'discount' => $discount,
                'image' => $filename,
        	]);
        	
        	$product_id = $data->id;
        	$urlData = 'checkout?id='.$product_id;
            $productToUpdate = Product::find($product_id);
            $productToUpdate->url               = $urlData;
            $productToUpdate->url_data          = $urlData;
            $productToUpdate->save();
            
            $this->helper->one_time_message('success', 'Store Product Added Successfully');
            return redirect('admin/store/product/list/' . $id);
        }
    }

    public function productedit($id, $prod_id)
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'store_list';
        
        $data['store_detail'] = $store_detail = Store::where('id', $id)->first();
        $data['categories'] = Categories::where('user_id', $store_detail->user_id)->orderBy('id', 'desc')->get();
        $data['product'] = Product::find($prod_id);
        return view('admin.stores.products.edit', $data);
    }

    public function productupdate(Request $request)
    {
        $rules = array(
            'name' => 'required',
        );

        $fieldNames = array(
            'name' => 'Name',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            $user = Store::where('id', $request->store_id)->first();
            
            $picture = $request->image;
            if (isset($picture)){
                $ext      = strtolower($picture->getClientOriginalExtension());
                $filename = time() . '.' . $ext;
                $dir1 = public_path('/user_dashboard/product/thumb/' . $filename);
                
                $img = Image::make($picture->getRealPath());
                $img->resize(100, 100)->save($dir1);
            }else{
                $cat_detail = Product::where('id', $request->prod_id)->where('userid', $user->user_id)->first();
                if(!empty($cat_detail)){
                    $filename = $cat_detail->image;
                }else{
                    $filename = null;
                }
            }
            
        	$product_sku = $request->product_sku;
        	$category_id = $request->category_id;
        	$name = $request->name;
        	$price = $request->price;
        	$description = $request->description;
        	$quantity = $request->quantity;
        	$discount_type = $request->discount_type;
        	$discount = $request->discount;
                    
        	$rs = Product::where('id', $request->prod_id)->where('userid', $user->user_id)->update([
        	    'user_product_id' => $product_sku,
                'category_id' => $category_id,
                'name' => $name,
                'price' => $price,
                'description' => $description,
                'quantity' => $quantity,
                'discount_type' => $discount_type,
                'discount' => $discount,
                'image' => $filename,
        	]);
            
            $this->helper->one_time_message('success', 'Store Product Updated Successfully');
            return redirect('admin/store/product/list/' . $request->store_id);
        }
    }
    
    public function productdestroy($id, $prod_id)
    {
        $store = Product::where('id', $prod_id)->delete();

        $this->helper->one_time_message('success', 'Product Deleted Successfully');
        return redirect('admin/store/product/list/' . $id);
    }
    
    public function orders($id)
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'store_list';
        
        $data['store_detail'] = $store_detail = Store::where('id', $id)->first();
        $data['currency'] = Currency::where('id', $store_detail->currency_id)->first();
        $data['orders'] = Order::where('store_id', $id)->orderBy('id', 'desc')->get();
        return view('admin.stores.orders.index', $data);
    }
    
    public function ordersedit($id, $ord_id)
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'store_list';
        
        $data['store_detail'] = $store_detail = Store::where('id', $id)->first();
        $data['currency'] = Currency::where('id', $store_detail->currency_id)->first();
        $data['order'] = $order = Order::where('id', $ord_id)->first();
        $data['order_products'] = json_decode($order->products);
        $data['paid_currency'] = Currency::where('id', $order->paid_currency_id)->first();
        $data['products'] = Product::where('userid', $store_detail->user_id)->orderBy('id', 'desc')->get();
        $data['countries'] = Country::orderBy('id', 'desc')->get();
        return view('admin.stores.orders.edit', $data);
    }
    
    public function ordersinvoice($id, $ord_id)
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'store_list';
        $data['order'] = $order = Order::where('id', $ord_id)->where('status', 'success')->where('payment_response', 'success')->first();
        $data['user'] = $user = User::where('id', $order->store_user_id)->first();
        $data['store'] = $store = Store::where('id', $order->store_id)->first();
        $data['country'] = $country = Country::where('id', $store->country)->first();
        $data['currency'] = $currency = Currency::where('id', $store->currency_id)->first();
        $data['paidcurrency'] = $paidcurrency = Currency::where('id', $order->paid_currency_id)->first();
        $data['transaction'] = $transaction = Transaction::where('transaction_reference_id', $order->unique_id)->first();
        return view('admin.stores.orders.invoice', $data);
    }
}