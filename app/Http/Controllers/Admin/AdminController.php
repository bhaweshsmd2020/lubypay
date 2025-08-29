<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use App\Http\Helpers\Common;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\EmailTemplate;
use App\Models\Preference;
use App\Models\Setting;
use App\Models\Role;
use App\Models\RoleUser;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Image;
use Session;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    protected $helper, $emailController;

    public function __construct()
    {
        $this->helper          = new Common();
        $this->emailController = new EmailController();
    }

    public function index()
    {
        $data['menu'] = 'admin_list';

        $data['admins'] = Admin::with('role')
            ->whereHas('role', function($q) {
                $q->where('user_type', 'Admin');
            })
            ->where('role_id', '!=', 12)
            ->get();

        Admin::whereHas('role', function($q) {
                $q->where('user_type', 'Admin');
            })
            ->where('role_id', '!=', 12)
            ->where('read_status', 0)
            ->update(['read_status' => 1]);

        return view('admin.admin.view', $data);
    }

    public function add()
    {
        $data['menu'] = 'admin_list';
        $data['roles'] = $roles = Role::where('user_type', 'Admin')->where('id', '!=', '12')->get();
        return view('admin.admin.add', $data);
    }

    public function store(Request $request)
    {
        $rules = array(
            'first_name'            => 'required',
            'last_name'             => 'required',
            'email'                 => 'required|unique:admins,email',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required',
        );

        $fieldNames = array(
            'first_name'            => 'First Name',
            'last_name'             => 'Last Name',
            'email'                 => 'Email',
            'password'              => 'Password',
            'password_confirmation' => 'Confirm Password',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            $admin             = new Admin();
            $admin->first_name = $request->first_name;
            $admin->last_name  = $request->last_name;
            $admin->email      = $request->email;
            $admin->password   = Hash::make($request->password);
            $admin->role_id    = $request->role;
            $admin->save();
            RoleUser::insert(['user_id' => $admin->id, 'role_id' => $request->role, 'user_type' => 'Admin']);
        }

        if (!isset($request->from_installer))
        {
            $this->helper->one_time_message('success', 'Admin Created Successfully!');
            return redirect()->intended("admin/admins");
        }
    }

    public function edit($id)
    {
        $data['menu']     = 'admin_list';
        $data['admin'] = $users = Admin::find($id);
        $data['roles'] = $roles = Role::where('user_type', "Admin")->where('id', '!=', '12')->get();
        return view('admin.admin.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $rules = array(
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:admins,email,' . $id,
        );

        $fieldNames = array(
            'first_name' => 'First Name',
            'last_name'  => 'Last Name',
            'email'      => 'Email',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);
        if ($validator->fails())
        {
            return back()->withErrors($validator)->withInput();
        }
        else
        {
            $admin             = Admin::find($id);
            $admin->first_name = $request->first_name;
            $admin->last_name  = $request->last_name;
            $admin->email      = $request->email;
            $admin->role_id    = $request->role;
            $admin->save();
            RoleUser::where(['user_id' => $admin->id, 'user_type' => 'Admin'])->update(['role_id' => $request->role]);
            $this->helper->one_time_message('success', 'Admin Updated Successfully!');
            return redirect()->intended("admin/admins");
        }
    }

    public function delete($id)
    {
        $admin = Admin::find($id);
        if ($admin)
        {
            $admin->delete();

            ActivityLog::where(['user_id' => $id])->delete();
            RoleUser::where(['user_id' => $id, 'user_type' => 'Admin'])->delete();

            $this->helper->one_time_message('success', 'Admin Deleted Successfully');
            return redirect()->intended("admin/admins");
        }
    }
}