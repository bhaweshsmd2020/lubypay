<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\PhotoProofsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\DocumentVerification;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use App\DataTables\Admin\UserKycDatatable;
use App\DataTables\Admin\MerchantKycDatatable;
use DB;
use Illuminate\Support\Facades\Auth;
use App\DataTables\Admin\UserManualKycDatatable;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;

class PhotoProofController extends Controller
{
    protected $helper;
    protected $documentVerification;
    protected $email;

    public function __construct()
    {
        $this->helper               = new Common();
        $this->documentVerification = new DocumentVerification();
        $this->email                = new EmailController();
    }

    public function index()
    {
        $data['menu']     = 'proofs';
        $data['sub_menu'] = 'photo-proofs';

        $data['documentVerificationStatus'] = $documentVerificationStatus = $this->documentVerification->where(['verification_type' => 'photo'])->select('status')->groupBy('status')->get();

        if (isset($_GET['btn']))
        {
            $data['status'] = $status   = $_GET['status'];

            if (empty($_GET['from']))
            {
                $data['from'] = $from  = null;
                $data['to'] = $to    = null;
                $data['documents'] = (new DocumentVerification())->getPhotoVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();
            }
            else
            {
                $data['from'] = $from  = setDateForDb($_GET['from']);
                $data['to'] = $to    = setDateForDb($_GET['to']);
                $data['documents'] = (new DocumentVerification())->getPhotoVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();
            }
        }
        else
        {
            $data['from'] = $from = null;
            $data['to'] = $to   = null;
            $data['status'] = $status   = 'all';
            $data['documents'] = (new DocumentVerification())->getPhotoVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();
        }
        return view('admin.verifications.photo_proofs.list', $data);
    }

    public function photoProofsCsv()
    {
        $from = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;
        $to   = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;

        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $data['photoProofs'] = $photoProofs = $this->documentVerification->getDocumentVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();
        // dd($photoProofs);

        $datas = [];
        if (!empty($photoProofs))
        {
            foreach ($photoProofs as $key => $value)
            {
                $datas[$key]['Date'] = dateFormat($value->created_at);

                $datas[$key]['User'] = isset($value->user) ? $value->user->first_name . ' ' . $value->user->last_name : "-";

                // $datas[$key]['Identity Type'] = str_replace('_', ' ', ucfirst($value->identity_type));

                // $datas[$key]['Identity Number'] = $value->identity_number;

                if ($value->status == 'approved')
                {
                    $status = 'Approved';
                }
                elseif ($value->status == 'pending')
                {
                    $status = 'Pending';
                }
                elseif ($value->status == 'rejected')
                {
                    $status = 'Rejected';
                }
                $datas[$key]['Status'] = $status;
            }
        }
        else
        {
            $datas[0]['Date']            = '';
            $datas[0]['User']            = '';
            $datas[0]['Identity Type']   = '';
            $datas[0]['Identity Number'] = '';
            $datas[0]['Status']          = '';
        }
        // dd($datas);

        return Excel::create('photo_proofs_list_' . time() . '', function ($excel) use ($datas)
        {
            $excel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $excel->sheet('mySheet', function ($sheet) use ($datas)
            {
                $sheet->cells('A1:E1', function ($cells)
                {
                    $cells->setFontWeight('bold');
                });
                $sheet->fromArray($datas);
            });
        })->download();
    }

    public function photoProofsPdf()
    {
        $data['company_logo'] = getCompanyLogoWithoutSession();

        $from = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;
        $to   = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;

        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $data['photoProofs'] = $photoProofs = $this->documentVerification->getDocumentVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();

        if (isset($from) && isset($to))
        {
            $data['date_range'] = $_GET['startfrom'] . ' To ' . $_GET['endto'];
        }
        else
        {
            $data['date_range'] = 'N/A';
        }

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);

        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);

        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;

        $mpdf->WriteHTML(view('admin.verifications.photo_proofs.photo_proofs_report_pdf', $data));

        $mpdf->Output('photo_proofs_report_' . time() . '.pdf', 'D');
    }

    public function photoProofEdit($id)
    {
        $data['menu']     = 'proofs';
        $data['sub_menu'] = 'photo-proofs';

        $data['documentVerification'] = $documentVerification = DocumentVerification::find($id);
        // dd($documentVerification);

        return view('admin.verifications.photo_proofs.edit', $data);
    }

    public function photoProofUpdate(Request $request)
    {
        // dd($request->all());
        $documentVerification         = DocumentVerification::find($request->id);
        $updated_by = Auth::guard('admin')->user()->id;
        $documentVerification->updated_by = $updated_by??'';
        $documentVerification->status = $request->status;
        $documentVerification->save();

        $user = User::find($request->user_id);
        if ($request->verification_type == 'photo')
        {
            if ($request->status == 'approved')
            {
                $user->photo_verified = true;
            }
            else
            {
                $user->photo_verified = false;
            }
        }
        $user->save();

        if (checkDemoEnvironment() != true)
        {
            $userdevice = DB::table('devices')->where('user_id', $user->id)->first();
            if(!empty($userdevice)){
                $device_lang = $userdevice->language;
            }else{
                $device_lang = Session::get('default_language');
            }

            $photoVerificationEmailTemp = EmailTemplate::where(['temp_id' => 21, 'language_id' => $device_lang, 'type' => 'email'])->select('subject', 'body')->first();

            $photoVerificationEmailSub  = str_replace('{identity/address/photo}', 'Photo', $photoVerificationEmailTemp->subject);
            $photoVerificationEmailBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $photoVerificationEmailTemp->body);
            $photoVerificationEmailBody = str_replace('{identity/address/photo}', 'Photo', $photoVerificationEmailBody);
            $photoVerificationEmailBody = str_replace('{approved/pending/rejected}', ucfirst($request->status), $photoVerificationEmailBody);
            $photoVerificationEmailBody = str_replace('{soft_name}', Session::get('name'), $photoVerificationEmailBody);

            if (checkAppMailEnvironment())
            {
                $this->email->sendEmail($user->email, $photoVerificationEmailSub, $photoVerificationEmailBody);
            }

            /**
             * SMS
             */
            $englishPhotoVerificationSmsTemp = EmailTemplate::where(['temp_id' => 21, 'lang' => 'en', 'type' => 'sms'])->select('subject', 'body')->first();
            $photoVerificationSmsTemp        = EmailTemplate::where(['temp_id' => 21, 'language_id' => Session::get('default_language'), 'type' => 'sms'])->select('subject', 'body')->first();

            if (!empty($photoVerificationSmsTemp->subject) && !empty($photoVerificationSmsTemp->body))
            {
                $photoVerificationSmsSub  = str_replace('{identity/address/photo}', 'Photo', $photoVerificationSmsTemp->subject);
                $photoVerificationSmsBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $photoVerificationSmsTemp->body);
            }
            else
            {
                $photoVerificationSmsSub  = str_replace('{identity/address/photo}', 'Photo', $englishPhotoVerificationSmsTemp->subject);
                $photoVerificationSmsBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $englishIdentityVerificationSmsTemp->body);
            }
            $photoVerificationSmsBody = str_replace('{identity/address/photo}', 'Photo', $photoVerificationSmsBody);
            $photoVerificationSmsBody = str_replace('{approved/pending/rejected}', ucfirst($request->status), $photoVerificationSmsBody);

            if (!empty($user->carrierCode) && !empty($user->phone))
            {
                if (checkAppSmsEnvironment())
                {
                    //sendSMS($user->carrierCode . $user->phone, $photoVerificationSmsBody);

                    /*
                    // Quota Exceeded - rejected - TEST
                    $sendSMS = sendSMS($user->carrierCode . $user->phone, $identityVerificationSmsBody);
                    if ($sendSMS['status'] == false)
                    {
                        $this->helper->one_time_message('error', $sendSMS['message']);
                        return redirect('admin/address-proofs');
                    }
                    */
                }
            }
        }
    	
    	//notification
	    $currency = "9";
        $type = "photo";
        $date = date("Y-m-d h:i:s");
        
    	$userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
    	if(!empty($userdevice)){
            $template = NotificationTemplate::where('temp_id', '18')->where('language_id', $userdevice->language)->first();
            $subject = $template->title;
            $subheader = $template->subheader;
            $message = $template->content;
            
            $sub = str_replace('{status}', $request->status, $subject);
            $subhead = str_replace('{status}', $request->status, $subheader);
            $msg = str_replace('{status}', $request->status, $message);
            $this->helper->sendFirabasePush($sub, $msg, $request->user_id, $currency, $type);
            
            Noticeboard::create([
                'tr_id' => null,
                'title' => $sub,
                'content' => $msg,
                'type' => 'push',
                'content_type' => 'photo',
                'user' => $request->user_id,
                'sub_header' => $subhead,
                'push_date' => $date,
                'template' => '18',
                'language' => $userdevice->language,
                'status' => $request->status
            ]);
    	}
    	
        $this->helper->one_time_message('success', 'Photo Verified Successfully!');
        return redirect('admin/photo-proofs');
    }
    
    public function userskyc()
    {
        $data['menu']     = 'users';
        $data['sub_menu'] = 'manualkyc';
        $data['documentVerificationStatus'] = $documentVerificationStatus = $this->documentVerification->where(['verification_type' => 'photo'])->select('status')->groupBy('status')->get();
        $data['users'] = User::where('role_id', '2')->with(['document_verification:id,user_id,status','kycstatus:user_id,status','role:id,display_name','user_detail:id,user_id,last_login_at,last_login_ip', 'LoginLocation:id,user_id,city,country'])->select('users.*')->orderBy('kyc_submitted_on', 'desc')->get();
        User::where('kyc_read_status', '0')->where('role_id', 2)->whereNotNull('kyc_submitted_on')->update(['kyc_read_status' => '1']);
        return view('admin.users.userskyc', $data);
    }
    
    public function mposmanualkyc()
    {
        $data['menu']     = 'mpos';
        $data['sub_menu'] = 'manualkyc';

        $data['documentVerificationStatus'] = $documentVerificationStatus = $this->documentVerification->where(['verification_type' => 'photo'])->select('status')->groupBy('status')->get();
        $data['users'] = User::where('role_id', '3')->with(['document_verification:id,user_id,status','kycstatus:user_id,status','role:id,display_name','user_detail:id,user_id,last_login_at,last_login_ip', 'LoginLocation:id,user_id,city,country'])->select('users.*')->orderBy('kyc_submitted_on', 'desc')->get();
        User::where('kyc_read_status', '0')->where('role_id', 3)->whereNotNull('kyc_submitted_on')->update(['kyc_read_status' => '1']);
        return view('admin.users.merchantskyc', $data);
    }

    // 02-03-2023
       public function manualkycstatus(UserManualKycDatatable $dataTable)
    {
        $data['menu']     = 'kyc';
        $data['sub_menu'] = 'manual-kyc-status';

        $data['documentVerificationStatus'] = $documentVerificationStatus = $this->documentVerification->where(['verification_type' => 'photo'])->select('status')->groupBy('status')->get();
        // print_r($data['documentVerificationStatus'] );
        // die;
        return $dataTable->render('admin.users.manualuserkyc', $data);
        
        
    }
}
