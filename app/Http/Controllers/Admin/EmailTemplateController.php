<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use App\Models\Language;

class EmailTemplateController extends Controller
{

    protected $helper;
    public function __construct()
    {
        $this->helper = new Common();
    }

    public function index($id)
    {
        $data['menu']     = 'email';
        $data['sub_menu'] = 'email_template';

        $data['list_menu'] = 'menu-' . $id;

        $data['tempId']    = $id;

        $data['temp_Data'] = $temp_Data = EmailTemplate::where(['temp_id' => $id, 'type' => 'email'])->get();
        $data['languages'] = Language::where('status', 'Active')->get();
        
        return view('admin.email_templates.index', $data);
    }

    public function update(Request $request, $id)
    {
        $languages = Language::where('status', 'Active')->get();
        foreach($languages as $language){
            $short_name = $language->short_name;
            $data[] = $request->$short_name;
        }
        
        $array = $data;
        array_unshift($array, "");
        unset($array[0]);
        $count = count($array) + 1;
        
        for ($i = 1; $i < $count; $i++)
        {
            if($array[$i] != null)
            {
                EmailTemplate::where(['temp_id' => $id, 'language_id' => $i])->update($array[$i]);
            }
        }

        $this->helper->one_time_message('success', 'Email Template Updated successfully!');
        return redirect()->intended('admin/template/' . $id);
    }
}
