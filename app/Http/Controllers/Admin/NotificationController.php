<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\Admin\NotificationsDataTable;
use App\Http\Helpers\Common;
use App\Models\Notification;
use App\Models\NotificationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use DB;
use App\Models\AppPage;

class NotificationController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }
    
    public function index()
    {
        $data['menu']     = 'notifications';
        $data['sub_menu'] = 'notifications_list';
        $data['notifications'] = Notification::orderBy('id', 'desc')->get();
        return view('admin.notifications.index', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  [int]                       $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $notification = Notification::findOrFail($id);

        if($notification) {
            $notification->clicked = 1;
        }

        if ($notification->save())
        {
            return redirect($notification->url_to_go);
        }
    }
    
    public function updateall()
    {
        $notification = Notification::where('clicked', '0')->update(['clicked' => '1']);
        return redirect()->back()->with('success', 'All notifications marked as read successfully'); 
    }
    
    public function updateread(Request $request)
    {
        $notifications = $request->check;
        if(!empty($notifications)){
            foreach($notifications as $notification){
                Notification::where('id', $notification)->update(['clicked' => '1']);
            }
        }
        
        return redirect()->back()->with('success', 'All notifications marked as read successfully'); 
    }
    
    
    
    // 12-11-2020
    
    public function pushsms()
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'pushsms';
       
        $data['users'] = User::where('type', "user")->get();
        $data['offer'] = DB::table('customer_offers')->orderBy('id', 'desc')->get();
        $data['pages'] = AppPage::where('status', 'Active')->get();
        return view('admin.notifications.pushsms', $data);
    }
    
    public function offerpush(NotificationsDataTable $dataTable)
    {
        $data['offer'] = DB::table('customer_offers')->orderBy('id','desc')->get();
        foreach($data['offer'] as $value)
        {
            $time_offe = date('Y-m-d',strtotime($value->start_time));
            $time_curr = date('Y-m-d');
            if($time_curr == $time_offe)
            {
                $userdevices = DB::table('devices')->where('device_id' ,'!=','')->where('fcm_token' ,'!=','')->get();
                foreach($userdevices as $devices)
                {
                    if($devices->user_id != 0)
                    {
                      $datanotice1= array('title'=>$value->offer_title,'content'=>$value->offer_desc,'type'=>'push','content_type'=>'offer','user'=>$devices->user_id,'sub_header'=>"New Offer",'push_date'=>date('Y-d-m'));
        	          DB::table('noticeboard')->insert($datanotice1);
                    }
                    $send = $this->helper->sendFirabasePush_Offer($value->offer_title,$value->offer_desc,$devices->user_id,$value->offer_image);
                }
            }
        }
    }
    
    public function sendpushsms(Request $request)
    {
       
        $rules = array(
            // 'title'            => 'required',
            // 'message'          => 'required',
            // 'add_url'          => 'required',
            'offer_image'         =>'required',
            'date_time'        => 'required',
            'date_time_start'        => 'required'
            );

            $fieldNames = array(
            // 'title'         => 'Title',
            // 'message'       => 'Message',
            // 'add_url'       => 'Add Url',
            'date_time'       => 'Add offer end date',
            'add_url'       => 'Add offer start date'
            );
            $validator = Validator::make($request->all(), $rules);
            // $validator->setAttributeNames($fieldNames);

            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                //  $expire_time = date('h-i-s', strtotime($request->date_time));
               $expire_date = date('Y-m-d h-i-s', strtotime($request->date_time));
               $start_date  = date('Y-m-d h-i-s', strtotime($request->date_time_start));
               
               
               if ($request->offer_image !='') 
    		   {
    		      
    		       $first_image = $request->offer_image;
    			 $image = $request->file('offer_image');
    			 $first_image = rand(00000,99999).'.'.$image->getClientOriginalExtension();
    			 $destinationPath = public_path('uploads/offer_image');
    			 $image->move($destinationPath, $first_image);
    			 $first_image ="uploads/offer_image/".$first_image;
    		   }else{
    			   $first_image = "uploads/offer_image/xpay.png";
    		   }
    		  // dd('dsdsds');
    		    $with_base_path = $first_image;
                $insert = DB::table('customer_offers')->insert([
                    'start_time'=>$start_date,
                    'expire_date_time'=>$expire_date,
                    'offer_url'=>$request->add_url,
                    'offer_id'=>uniqid(),
                    'offer_title'=>$request->title,
                    'offer_desc'=>$request->message,
                    'offer_image'=>$with_base_path,
                    'app_redirect'=>$request->app_redirect,
                    'app_page'=>$request->app_page,
                    'language'=>$request->language,
                    'platform'=>$request->platform
                ]);
                
                $counter = 0;
                $subject = $request->title;
                $subheader = $request->message;
                $userdevices = DB::table('devices')->where('device_id' ,'!=','')->where('fcm_token' ,'!=','')->get();
                // dd(json_encode(json_decode($userdevices),true));
                foreach($userdevices as $devices)
                {
                //     if($devices->user_id != 0)
                //     {
                //       $datanotice1= array('title'=>$subject,'content'=>$subheader,'type'=>'push','content_type'=>'offer','user'=>$devices->user_id,'sub_header'=>"New Offer",'push_date'=>date('Y-d-m'));
        	       //   DB::table('noticeboard')->insert($datanotice1);
                //     }
                     
                //     $send = $this->helper->sendFirabasePush_Offer($subject,$subheader,$devices->user_id,$with_base_path);
                    $counter++;
                }
               if($counter >0)
                    {
                      return redirect()->back()->with('success', 'Offer Add Successfully'); 
                    }
                    else
                    {
                     return redirect()->back()->with('error', 'Offer cant add Successfully'); 
                    }
                    
                }
     }
    // 12-11-2020
    public function deleteOffer($id)
    {
        $delete = DB::table('customer_offers')->where('id',$id)->delete();
        return redirect()->back()->with('success','Offer Delete Successfully...');
    }
    
    public function editOffer($id)
    {
        $data['menu']     = 'configurations';
        $data['sub_menu'] = 'pushsms';
        $data['offerData'] = DB::table('customer_offers')->where('id', $id)->first();
        $data['pages'] = AppPage::where('status', 'Active')->get();
        return view('admin.notifications.edit', $data);
    }
    
    public function updateOffer(NotificationsDataTable $dataTable, Request $request, $id)
    {
        $rules = array(
            // 'offer_image'            => 'required',
            'date_time'          => 'required',
            // 'add_url'          => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            if(isset($request->offer_image)){
               
                
                $first_image = $request->offer_image;
    			 $image = $request->file('offer_image');
    			 $first_image = rand(00000,99999).'.'.$image->getClientOriginalExtension();
    			 $destinationPath = public_path('uploads/offer_image');
    			 $image->move($destinationPath, $first_image);
    			 $file ="uploads/offer_image/".$first_image;
                
                
            }
            else
            {
              $offer_image = DB::table('customer_offers')->find($id)->offer_image;
    
              if($offer_image){
                $file = $offer_image;
              }
              else{
                $file='';
              }
            }
            
            $expire_date = date('Y-m-d h-i-s', strtotime($request->date_time));
                
            $data_offer = [
               'expire_date_time' => $expire_date,
               'offer_url' => $request->add_url, 
               'offer_title' => $request->title,
               'offer_desc' => $request->message,
               'app_page'  =>$request->app_page,
               'app_redirect'=>$request->app_redirect,
               'offer_image' => $file,
               'language'=>$request->language,
               'platform'=>$request->platform,
            ];

            $rs = DB::table('customer_offers')->where(['id'=> $id])->update($data_offer);
           
            $data['menu']     = 'pushsms';
            $data['sub_menu'] = 'pushsms';
           
            $data['users'] = User::where('type', "user")->get();
            $data['offer'] = DB::table('customer_offers')->get();
            return redirect('admin/pushsms')->with('success', 'Offer Updated Successfully'); 
            // return $dataTable->render('admin.notifications.pushsms', $data);
        }
    }
    
    public function add_offer_image(Request $request)
    {
        $data = $request->image;
        $image_array_1 = explode(";", $data);
        $image_array_2 = explode(",", $image_array_1[1]);
        $data = base64_decode($image_array_2[1]);
        $path = public_path('uploads/offer_image/');
        $img  = "uploads/offer_image/".time() . '.png';
        $image_name = $path . time() . '.png';
        file_put_contents($image_name, $data);
        echo $img;

        //dd($request->all());
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
