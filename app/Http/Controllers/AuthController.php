<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login() {
        return view('auth.login');
    }

    public function authenticate(Request $request){

        $validator = Validator::make($request->all(),[
           'email' => 'required|email',
           'password' => 'required'
        ]);

        if($validator->passes()) {

            if(Auth::attempt(['email' => $request->email,'password'=>
                 $request->password],$request->get('remember'))) {

                    // get session url when user click on btn
                    if (session()->has('url.intended')) {
                        return redirect(session()->get('url.intended'));
                    }

                    return redirect()->route('account.profile');
            }
            else {
                return redirect()->route('auth.login')
                ->withInput($request->only('email'))
                ->with('error','Either Email/Password is incorrect');
            }

        } else {
            return redirect()->route('auth.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }
    }

    public function register() {

        return view('auth.register');
    }

    public function processRegister(Request $request) {

        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required|min:5|confirmed'
        ]);

        if ($validator->passes()) {

            $newUser = new User();
            $newUser->name = $request->name;
            $newUser->email = $request->email;
            $newUser->phone = $request->phone;
            $newUser->password = Hash::make($request->password);
            $newUser->save();


            session()->flash('success','Register successfully');

            return response()->json([
                'status' => true,
                'message' => 'Register successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function profile() {
        return view('auth.account.profile');
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('auth.login')
        ->with('success','You successfully logged out');;
    }

    // order's user

    public function orders() {

        $user = Auth::user();

        $orders = Order::where('user_id',$user->id)->orderBy('created_at','DESC')->get();

        $data['orders'] = $orders;

        return view('auth.account.orders', $data);
    }

    // order detials

    public function orderDetial($id) {

        $user = Auth::user();

        $orders = Order::where('user_id',$user->id)->where('id',$id)->first();

        $orderItems = OrderItem::where('order_id',$id)->get();

        $orderItemsCount = OrderItem::where('order_id',$id)->count();

        $data['orderItemsCount'] = $orderItemsCount;

        $data['orders'] = $orders;

        $data['orderItems'] = $orderItems;

        return view('auth.account.order_detials', $data);
    }
}
