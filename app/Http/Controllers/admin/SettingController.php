<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    //Show View Change password
    public function showChangePassword() {
        return View('admin.change_password');
    }

    //Change password Process
    public function changePassword(Request $request) {


        $validator = Validator::make($request->all(),[
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password'
        ]);

        $id = Auth::guard('admin')->user()->id;

        $admin = User::where('id',$id)->first();

        if ($validator->passes()) {

            if(!Hash::check($request->old_password,$admin->password)) {

                session()->flash('error','Your old password in incorrect, please try again.');

                return response()->json([
                    'status' => true
                ]);
            }

            User::where('id',$id)->update([
                'password' => Hash::make($request->new_password)
            ]);


            session()->flash('success','Your password changed successful');

            return response()->json([
                'status' => true,
                'message' => 'Your password changed successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
