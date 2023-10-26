<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index (Request $request) {

        $users = User::latest('created_at');

        if (!empty($request->get('keyword'))) {
            $users = $users->where('name','like','%'.$request->get('keyword').'%');
        }

        $users = $users->paginate(10);
        // $data['categories'] = $categories;
        return View('admin.user.list',compact('users'));

    }

    public function create () {
        return View('admin.user.create');
    }

    public function store (Request $request) {

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required',
            'password' => 'required|min:5'

        ]);

        if ($validator->passes()) {

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->status = $request->password;
            $user->save();




            $request->session()->flash('success','User added successful');

            return response()->json([
                'status' => true,
                'message' => 'User added successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function edite ($userId, Request $request) {

        $user = User::find($userId);
        if (empty($user)) {
            return redirect()->route('users.index');
        }

        return View('admin.user.edite', compact('user'));
    }

    public function update ($userId, Request $request) {

        $user = User::find($userId);
        if (empty($user)) {
            // if the record delete it form database
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Users not found'
             ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$userId.',id',
            'phone' => 'required',

        ]);

        if ($validator->passes()) {


            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;

            if ($request->password != '') {
                $user->password = Hash::make($request->password);
            }

            $user->status = $request->status;
            $user->save();




            $request->session()->flash('success','User updated successful');

            return response()->json([
                'status' => true,
                'message' => 'User updated successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy ($userId, Request $request) {

        $user = User::find($userId);
        if (empty($user)) {
            return redirect()->route('users.index');
        }



        $user->delete();

        $request->session()->flash('success','user deleted successful');

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
