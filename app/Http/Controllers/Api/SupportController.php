<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Country;
use App\Models\RequestPayment;
use App\Models\Transfer;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\DocumentVerification;
use App\Models\UsersKyc;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\Admin;
use App\Models\EmailTemplate;
use App\Models\File;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\TicketStatus;
use DB;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Language;
use App\Models\LanguageContent;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;
use Aws\Credentials\Credentials;
use Aws\Sns\SnsClient;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 400;
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    public function newSupportTicket(Request $request)
    {
        $this->validate($request, [
            'subject'     => 'required',
            'description' => 'required',
        ]);
        
        $admin = Admin::first(['id']);
        $ticket = new Ticket();
        $ticket->admin_id = $admin->id;
        $ticket->user_id = $request->user_id;
        $ticket->ticket_status_id = 1;
        $ticket->read_status = 1;
        $ticket->subject = $request->subject;
        $ticket->message = $request->description;
        $ticket->code = $code='TIC-' . strtoupper(Str::random(6));
        $ticket->priority = $request->priority;
        $ticket->local_tran_time = $request->local_tran_time;
        $ticket->save();
        
        $userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
      
        $language = Language::where('id', $userdevice->language)->first();
        if(!empty($language)){
            $lang_code = $language->short_name;
            $content = LanguageContent::where(['string' => 'instant_ticket_reply'])->first();
            $message = $content->$lang_code;
        }else{
            $message = "Thanks for contacting us. Our support team will contact you back within 4 hours.";
        }

        if(!empty($ticket->id)){
            $ticket_reply            = new TicketReply();
            $ticket_reply->admin_id  = $admin->id;
            $ticket_reply->user_id   = $request->user_id;
            $ticket_reply->ticket_id = $ticket->id;
            $ticket_reply->message   = $message;
            $ticket_reply->local_tran_time = $request->local_tran_time;
            $ticket_reply->save();
        }
        
        $template = NotificationTemplate::where('temp_id', '25')->where('language_id', $userdevice->language)->first();
        $subject = $template->title;
        $subheader = $template->subheader;
        $message = $template->content;
        
        $msg = str_replace('{ticket}', $ticket->code, $message);
        
        $currency = '9';
        $type = 'ticket';
        $date    = date("Y-m-d h:i:s");
        $this->helper->sendFirabasePush($subject, $msg, $request->user_id, $currency, $type);
        
        Noticeboard::create([
            'tr_id' => null,
            'title' => $subject,
            'content' => $msg,
            'type' => 'push',
            'content_type' => 'ticket',
            'user' => $request->user_id,
            'sub_header' => $subheader,
            'push_date' => $request->local_tran_time,
            'template' => '25',
            'language' => $userdevice->language,
            'ticket' => $ticket->id
        ]);
        
        $adminAllowed = Notification::has_permission([1]);
        foreach($adminAllowed as $admins){
            $name = User::where('id', $request->user_id)->first();
            Notification::insert([
                'user_id'               => $name->id,
                'notification_to'       => $admin->id,
                'notification_type_id'  => 10,
                'notification_type'     => 'App',
                'description'           => 'User '.$name->first_name.' has created a ticket with ticket ID '.$code,
                'url_to_go'             => 'admin/tickets/reply/'.$ticket->id,
                'local_tran_time'       => $request->local_tran_time
            ]);
        }
        
        return response()->json([
            'status'  => $this->successStatus,
            'message' => "Your Ticket has been generated successfully.",
        ]);
    }
    
    public function listSupportTicket(Request $request)
    {
        $data['my_ticket'] = Ticket::where('user_id',$request->user_id)->get();
        $count = Ticket::where(['user_id'=>$request->user_id,'read_status'=>1])->count();
        $data['tickets'] = Ticket::with(['ticket_status:id,name'])
            ->where(['user_id' => $request->user_id])
            ->select('id', 'ticket_status_id', 'code', 'subject', 'priority', 'type', 'local_tran_time', 'created_at', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();
        return response()->json([
            'status'      => $this->successStatus,
            'data'        => $data,
            'count'        => $count,
        ]);
    }
    public function ReadSupportTicket(Request $request)
    {
       $data = Ticket::where('id',$request->ticket_id)->update(['read_status'=>1]);
       return response()->json([
            'status'      => $this->successStatus,
            'mesasage'        => 'Status Update Successfully',
       ]);
    }
    
    public function ticketDetails(Request $request)
    {   
        $id = $request->ticket_id;
        $data['ticket'] = Ticket::with(['ticket_status:id,name','user:id,first_name,last_name,picture'])->find($id);
        $data['ticket_status'] = TicketStatus::get(['id','name']);
        $data['image_url'] = "/public/uploads/ticketFile/";

        $data['ticket_replies'] = TicketReply::with(['file:id,ticket_reply_id,filename,originalname','user:id,first_name,last_name,picture','admin:id,first_name,last_name,picture'])
        ->where(['ticket_id' => $id])->orderBy('id', 'desc')->get();
        return response()->json([
            'status'      => $this->successStatus,
            'data'        => $data,
        ]);
    }
    
    public function sendMessage(Request $request)
    {
        $ticket                   = Ticket::find($request->ticket_id,['id','ticket_status_id','last_reply','admin_id','code']);
        $ticket->ticket_status_id = 1;
        $ticket->last_reply       = date('Y-m-d H:i:s');
        $ticket->save();

        // Store in Ticket Replies Table
        $ticket_reply            = new TicketReply();
        $ticket_reply->admin_id  = $ticket->admin_id;
        $ticket_reply->user_id   = $request->user_id;
        $ticket_reply->ticket_id = $request->ticket_id;
        $ticket_reply->user_type = 'user';
        $ticket_reply->message   = $request->description;
        $ticket_reply->local_tran_time = $request->local_tran_time;
        $ticket_reply->save();
        
        
         $adminAllowed = Notification::has_permission([1]);
                            
                            foreach($adminAllowed as $admin){
                                $name = User::where('id', $request->user_id)->first();
                                Notification::insert([
                                    'user_id'               => $name->id,
                                    'notification_to'       => $ticket->admin_id,
                                    'notification_type_id'  => 11,
                                    'notification_type'     => 'App',
                                    'description'           => 'User '.$name->first_name.' has replied on ticket ID'.$ticket->code,
                                    'url_to_go'             => 'admin/tickets/reply/'.$request->ticket_id,
                                    'local_tran_time'       => $request->local_tran_time
                                ]);
                            }
        

        // Store in Files Table
        if ($request->file)
                {
                    $img = $request->file;
                    $param = $request->user_id;
                    $dirt       = 'uploads/ticketFile';
                    $path       = public_path($dirt);
                    $fileId = $this->createImageFromBase64($img,$param, $path,$ticket->admin_id,$request->ticket_id,$ticket_reply->id);
                   
                }
           
       
        return response()->json([
            'status'      => $this->successStatus,
            'message'     => "Message has been send successfully.",
        ]);
    }
    public function createImageFromBase64($image,$param,$imagedir,$admin_id,$ticket_id,$ticket_reply) {
        if(isset($image) && $image && isset($imagedir) && $imagedir) {
            
             $upload_dir = $imagedir;
            $img =$image;
           
            $type= ".jpg";
            $img = str_replace('data:image/png;base64,', '', $img);
           // $img = str_replace('data:image/*;charset=utf-8;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $datas = base64_decode($img);
            $fileName = strtolower(time() . $type);
            $file = $upload_dir .'/'. $fileName;
            $success = file_put_contents($file, $datas);
            // $img = "https://ewallet.xpay.mv/public/uploads/ticketFile/".$fileName;
            // echo "check this URL : ".$img;
            // die;
            $file                  = new File();
            $file->admin_id        = $admin_id;
            $file->user_id         = $param;
            $file->ticket_id       = $ticket_id;
            $file->ticket_reply_id = $ticket_reply;
            $file->filename        = $fileName;
            $file->originalname    = $fileName;
            $file->type            = $type;
            $file->save();
           return $file->id;
        } else {
            return "";
        }
    }
    
       public function ApikeyData(Request $request)
    {
       // dd($request->all());
       $data=Setting::where('type', 'persona')->pluck('value', 'name')->toArray();
       return response()->json([
            'status'      => $this->successStatus,
            'data'        =>$data,
            'message'     => "Api key credential",
        ]);
    }
    
    public function send_sns(Request $request)
    {
        $awsKey = 'AKIA2UC3CDB6KWKZDXZI';
        $awsSecret = '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';
        $amazonRegion = 'ap-south-1';
        
        $params = array(
            'credentials' => array(
                'key' => $awsKey,
                'secret' => $awsSecret,
            ),
            'region' => $amazonRegion,
            'version' => '2010-03-31'
        );
        $sns = new \Aws\Sns\SnsClient($params);
        $args = array(
            "SenderID" => "aws-sns-monitor",
            "SMSType" => "Transactional",
            "Message" => "checking",
            "PhoneNumber" => "+919625472886" // +cc#######
        );
        $result = $sns->publish($args);
     
        dd($result);
    }
    
    
}
