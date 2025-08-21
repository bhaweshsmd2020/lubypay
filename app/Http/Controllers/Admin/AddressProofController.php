<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AddressProofsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\DocumentVerification;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use DB;
use App\Models\NotificationTemplate;
use App\Models\Noticeboard;

class AddressProofController extends Controller
{
    protected $helper;
    protected $documentVerification;
    protected $email;

    public function __construct()
    {
        $this->helper              = new Common();
        $this->addressVerification = new DocumentVerification();
        $this->email               = new EmailController();
    }

    public function index()
    {
        $data['menu']     = 'proofs';
        $data['sub_menu'] = 'address-proofs';

        $data['documentVerificationStatus'] = $documentVerificationStatus = $this->addressVerification->where(['verification_type' => 'address'])->select('status')->groupBy('status')->get();
        
        if (isset($_GET['btn']))
        {
            $data['status'] = $status   = $_GET['status'];

            if (empty($_GET['from']))
            {
                $data['from'] = $from  = null;
                $data['to'] = $to    = null;
                $data['documents'] = (new DocumentVerification())->getAddressVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();
            }
            else
            {
                $data['from'] = $from  = setDateForDb($_GET['from']);
                $data['to'] = $to    = setDateForDb($_GET['to']);
                $data['documents'] = (new DocumentVerification())->getAddressVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();
            }
        }
        else
        {
            $data['from'] = $from = null;
            $data['to'] = $to   = null;
            $data['status'] = $status   = 'all';
            $data['documents'] = (new DocumentVerification())->getAddressVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();
        }
        
        return view('admin.verifications.address_proofs.list', $data);
    }

    public function addressProofsCsv()
    {
        $from   = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;
        $to     = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;
        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $data['addressProofs'] = $addressProofs = $this->addressVerification->getAddressVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();
        // dd($addressProofs);

        $datas = [];
        if (!empty($addressProofs))
        {
            foreach ($addressProofs as $key => $value)
            {
                $datas[$key]['Date'] = dateFormat($value->created_at);
                $datas[$key]['User'] = isset($value->user) ? $value->user->first_name . ' ' . $value->user->last_name : "-";
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
            $datas[0]['Date']   = '';
            $datas[0]['User']   = '';
            $datas[0]['Status'] = '';
        }
        return Excel::create('address_proofs_list_' . time() . '', function ($excel) use ($datas)
        {
            $excel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $excel->sheet('mySheet', function ($sheet) use ($datas)
            {
                $sheet->cells('A1:C1', function ($cells)
                {
                    $cells->setFontWeight('bold');
                });
                $sheet->fromArray($datas);
            });
        })->download();
    }

    public function addressProofsPdf()
    {
        $data['company_logo'] = getCompanyLogoWithoutSession();
        $from                 = !empty($_GET['startfrom']) ? setDateForDb($_GET['startfrom']) : null;
        $to                   = !empty($_GET['endto']) ? setDateForDb($_GET['endto']) : null;
        $status               = isset($_GET['status']) ? $_GET['status'] : null;

        $data['addressProofs'] = $addressProofs = $this->addressVerification->getAddressVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();
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
        $mpdf->WriteHTML(view('admin.verifications.address_proofs.address_proofs_report_pdf', $data));
        $mpdf->Output('address_proofs_report_' . time() . '.pdf', 'D');
    }

    public function addressProofEdit($id)
    {
        $data['menu']     = 'proofs';
        $data['sub_menu'] = 'address-proofs';

        $data['documentVerification'] = $documentVerification = DocumentVerification::find($id);
        return view('admin.verifications.address_proofs.edit', $data);
    }

    public function addressProofUpdate(Request $request)
    {
        // dd($request->all());
        $documentVerification         = DocumentVerification::find($request->id);
        $updated_by = Auth::guard('admin')->user()->id;
        $documentVerification->updated_by = $updated_by??'';
        $documentVerification->status = $request->status;
        $documentVerification->save();

        $user = User::find($request->user_id);
        if ($request->verification_type == 'address')
        {
            if ($request->status == 'approved')
            {
                $user->address_verified = true;
            }
            else
            {
                $user->address_verified = false;
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
            
            $addressVerificationEmailTemp = EmailTemplate::where(['temp_id' => 21, 'language_id' => $device_lang, 'type' => 'email'])->select('subject', 'body')->first();
            
            $addressVerificationEmailSub = str_replace('{identity/address/photo}', 'Address', $addressVerificationEmailTemp->subject);
            $addressVerificationEmailBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $addressVerificationEmailTemp->body);
            $addressVerificationEmailBody = str_replace('{identity/address/photo}', 'Address', $addressVerificationEmailBody);
            $addressVerificationEmailBody = str_replace('{approved/pending/rejected}', ucfirst($request->status), $addressVerificationEmailBody);
            $addressVerificationEmailBody = str_replace('{soft_name}', Session::get('name'), $addressVerificationEmailBody);

            if (checkAppMailEnvironment())
            {
                $this->email->sendEmail($user->email, $addressVerificationEmailSub, $addressVerificationEmailBody);
            }

            /**
             * SMS
             */
            $englishAddressVerificationSmsTemp = EmailTemplate::where(['temp_id' => 21, 'lang' => 'en', 'type' => 'sms'])->select('subject', 'body')->first();
            $addressVerificationSmsTemp        = EmailTemplate::where(['temp_id' => 21, 'language_id' => Session::get('default_language'), 'type' => 'sms'])->select('subject', 'body')->first();

            if (!empty($addressVerificationSmsTemp->subject) && !empty($addressVerificationSmsTemp->body))
            {
                $addressVerificationSmsSub  = str_replace('{identity/address/photo}', 'Address', $addressVerificationSmsTemp->subject);
                $addressVerificationSmsBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $addressVerificationSmsTemp->body);
            }
            else
            {
                $addressVerificationSmsSub  = str_replace('{identity/address/photo}', 'Address', $englishAddressVerificationSmsTemp->subject);
                $addressVerificationSmsBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $englishAddressVerificationSmsTemp->body);
            }
            $addressVerificationSmsBody = str_replace('{identity/address/photo}', 'Address', $addressVerificationSmsBody);
            $addressVerificationSmsBody = str_replace('{approved/pending/rejected}', ucfirst($request->status), $addressVerificationSmsBody);

            if (!empty($user->carrierCode) && !empty($user->phone))
            {
                if (checkAppSmsEnvironment())
                {
                    sendSMS($user->carrierCode . $user->phone, $addressVerificationSmsBody);

                    // // Quota Exceeded - rejected - TEST
                    // $sendSMS = sendSMS($user->carrierCode . $user->phone, $addressVerificationSmsBody);
                    // dd($sendSMS);
                    // if ($sendSMS['status'] == false)
                    // {
                    //     $this->helper->one_time_message('error', $sendSMS['message']);
                    //     return redirect('admin/address-proofs');
                    // }
                }
            }
            //
        }
        
        
        //notification
    	$currency = "9";
        $type = "address";
        $date = date("Y-m-d h:i:s");
        
    	$userdevice = DB::table('devices')->where('user_id', $request->user_id)->first();
    	if(!empty($userdevice)){
            $template = NotificationTemplate::where('temp_id', '19')->where('language_id', $userdevice->language)->first();
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
                'content_type' => 'address',
                'user' => $request->user_id,
                'sub_header' => $subhead,
                'push_date' => $date,
                'template' => '19',
                'language' => $userdevice->language,
                'status' => $request->status
            ]);
    	}
        
        $this->helper->one_time_message('success', 'Address Verified Successfully!');
        return redirect('admin/address-proofs');
    }
}
