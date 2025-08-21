<?php
namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\MerchantGroup;
use App\Models\MerchantPayment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\QrCode;
use Auth;use Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;
use Validator;
/// Added By Rahul
use FileHelper;
use App\Models\Store;
use Illuminate\Support\Str;

use App\Models\ShippingCost;
use App\Models\Order;
////////
class OrderController extends Controller
	{
		protected $helper;
		public function __construct()
		{
			$this->helper = new Common();
		}
		public function index()
		{
			$data['menu']          = 'orders';
			$data['sub_menu']      = 'orders';
			$data['content_title'] = 'Orders';
			$data['icon']          = 'user';
			$data['list']          = Order::where(['store_user_id' => Auth::user()->id])->orderBy('id', 'desc')->paginate(10);
			$data['preference']   = getDecimalThousandMoneyFormatPref(['decimal_format_amount']);
			return view('user_dashboard.Order.list', $data);
		}
		
		public function detail($id)
		{
			$data['menu']          = 'orders';
			$data['sub_menu']      = 'orders';
			$data['content_title'] = 'Orders';
			$data['icon']          = 'user';
			$data['orders']      =  Order::find($id);
			
			return view('user_dashboard.Order.detail', $data);
		}
		
		public function changestatus(Request $request)
		{
		    if($request->order_id)
		    {
		        $order_id = $request->order_id;
		        
		        $data = array(
		            'status'=>$request->status
		            );
		            
		           Order::where('id',$order_id)->update($data);
		           
		           return redirect()->back()->with('success','Successfully updated order status !');
		    }
		    else
		    {
		        $this->helper->one_time_message('error', 'Please Select order to change status');
		        return redirect()->back();
		    }
		    
		    
		   
		}

		

}
