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

class ProfileController extends Controller
{
    protected $helper, $emailController;

    public function __construct()
    {
        $this->helper          = new Common();
        $this->emailController = new EmailController();
    }

    public function profile()
    {
        $data['menu']          = 'profile';
        $data['admin_id']      = $admin_id      = Auth::guard('admin')->user()->id;
        $data['admin_picture'] = $admin_picture = Auth::guard('admin')->user()->picture;

        return view('admin.profile.profile', $data);
    }

    public function profileUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name'  => 'required',
            'picture'    => 'mimes:png,jpg,jpeg,gif,bmp|max:10000',
        ]);

        $data['first_name'] = $request->first_name;
        $data['last_name']  = $request->last_name;
        $data['updated_at'] = date('Y-m-d H:i:s');

        try
        {
            $pic = $request->file('picture');
            if (isset($pic))
            {
                $upload = 'public/uploads/userPic';

                $pic1 = $request->pic;

                if ($pic1 != null)
                {
                    $dir = public_path("uploads/userPic/$pic1");
                    if (file_exists($dir))
                    {
                        unlink($dir);
                    }
                }
                $filename  = time() . '.' . $pic->getClientOriginalExtension();

                $extension = strtolower($pic->getClientOriginalExtension());
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp')
                {
                    $pic = Image::make($pic->getRealPath());
                    $pic->resize(100, 100)->save($upload . '/' . $filename);
                    $data['picture'] = $filename;
                }
                else
                {
                    $this->helper->one_time_message('error', 'Invalid Image Format!');
                }
            }
            Admin::where(['id' => $id])->update($data);
            $this->helper->one_time_message('success', 'Profile Updated Successfully');
            return redirect('admin/profile');
        }
        catch (\Exception $e)
        {
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect('admin/profile');
        }
    }

    public function changePassword()
    {
        $data['menu']     = 'profile';
        $data['admin_id'] = $admin_id = Auth::guard('admin')->user()->id;
        
        $g = new GoogleAuthenticator();
        $secret = $g->generateSecret();
        $data['secret'] = $secret;
        $admin_email = Auth::guard('admin')->user()->email;
        $site_name = env('APP_NAME');
        $data['image'] = GoogleQrUrl::generate($admin_email, $secret, $site_name);
        
        return view('admin.profile.change_password', $data);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_pass' => 'required',
            'new_pass' => 'required',
        ]);

        $admin = Admin::where(['id' => $request->id])->first(['password']);

        $data['password']   = \Hash::make($request->new_pass);
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (\Hash::check($request->old_pass, $admin->password))
        {
            Admin::where(['id' => $request->id])->update($data);

            $this->helper->one_time_message('success', 'Password Updated successfully!');
            return redirect()->intended("admin/change-password");
        }
        else
        {
            $this->helper->one_time_message('error', 'Old Password is Wrong!');
            return redirect()->intended("admin/change-password");
        }
    }

    public function submit2fa(Request $request)
    {
        $admin_id = Auth::guard('admin')->user()->id;
        $user = Admin::findOrFail($admin_id);
        $g = new GoogleAuthenticator();
        $secret = $request->vv;
        if ($request->type == 0) {
            $check = $g->checkcode($user->googlefa_secret, $request->code, 3);
            if ($check) {
                $user->fa_status = 0;
                $user->googlefa_secret = null;
                $user->save();
                
                $this->helper->one_time_message('success', '2fa disabled Successfully');
                return redirect('admin/change-password');
            } else {
                $this->helper->one_time_message('error', 'Invalid code');
                return redirect('admin/change-password');
            }
        } else {
            $check = $g->checkcode($secret, $request->code, 3);
            if ($check) {
                $user->fa_status = 1;
                $user->googlefa_secret = $request->vv;
                $user->save();
                $this->helper->one_time_message('success', '2fa enabled Successfully');
                return redirect('admin/change-password');
            } else {
                $this->helper->one_time_message('error', 'Invalid code');
                return redirect('admin/change-password');
            }
        }
    }   
    
    public function passwordCheck(Request $request)
    {
        $admin = Admin::where(['id' => $request->id])->first();

        if (!\Hash::check($request->old_pass, $admin->password))
        {
            $data['status'] = true;
            $data['fail']   = "Your old password is incorrect!";
        }
        else
        {
            $data['status'] = false;
        }
        return json_encode($data);
    }   

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin');
    }  
}