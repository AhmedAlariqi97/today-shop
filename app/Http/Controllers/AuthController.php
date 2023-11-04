<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordEmail;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Dflydev\DotAccessData\data;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        $userId = Auth::user()->id;

        $user = User::where('id',Auth::user()->id)->first();

        $countries = Country::orderBy('name','ASC')->get();

        $customerAddress = CustomerAddress::where('user_id',$userId)->first();

        return view('auth.account.profile',[
            'user' => $user,
            'countries' => $countries,
            'customerAddress' => $customerAddress
        ]);
    }

    public function updateProfile(Request $request) {

        $userId = Auth::user()->id;
        $user = User::find($userId);

        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$userId.',id',
            // 'email' => 'required|email|unique:users',
            'phone' => 'required'
        ]);

        if ($validator->passes()) {

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();


            session()->flash('success','Your Profile updated successful');

            return response()->json([
                'status' => true,
                'message' => 'User Profile updated successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function updateAddress(Request $request) {

        $userId = Auth::user()->id;
        // $customerAddress = CustomerAddress::find($userId);

        $validator = Validator::make($request->all(),[
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required'
        ]);

        if ($validator->passes()) {

            // $customerAddress->first_name = $request->first_name;
            // $customerAddress->last_name = $request->last_name;
            // $customerAddress->email = $request->email;
            // $customerAddress->country = $request->country;
            // $customerAddress->address = $request->address;
            // $customerAddress->city = $request->city;
            // $customerAddress->state = $request->state;
            // $customerAddress->zip = $request->zip;
            // $customerAddress->mobile = $request->mobile;
            // $customerAddress->save();


            CustomerAddress::updateOrCreate(
                ['user_id' => $userId],
                [
                    'user_id' => $userId,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country_id' => $request->country,
                    'address' => $request->address,
                    'apartment' => $request->apartment,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip' => $request->zip,
                ]);


            session()->flash('success','Your address data updated successful');

            return response()->json([
                'status' => true,
                'message' => 'User address data updated successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
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

    //Show View Change password
    public function showChangePassword() {
        return View('auth.account.change_password');
    }

    //Change password Process
    public function changePassword(Request $request) {


        $validator = Validator::make($request->all(),[
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password'
        ]);

        if ($validator->passes()) {

            $user = User::select('id','password')->where('id',Auth::user()->id)->first();

            if(!Hash::check($request->old_password,$user->password)) {

                session()->flash('error','Your old password in incorrect, please try again.');

                return response()->json([
                    'status' => true
                ]);
            }

            User::where('id',$user->id)->update([
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

    public function forgotPassword(){

        return View('auth.forgot_password');
    }

    public function processForgotPassword(Request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return redirect()->route('auth.forgotPassword')->withInput()->withErrors($validator);
        }

        $token = Str::random(60);

        \DB::table('password_reset_tokens')->where('email',$request->email)->delete();

        \DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        // send email here

        $user = User::where('email',$request->email)->first();

        $formData = [
            'token' => $token,
            'user' => $user,
            'subject' => 'You have requested to reset your password'
        ];

        Mail::to($request->email)->send(new ResetPasswordEmail($formData));

        return redirect()->route('auth.forgotPassword')->with('success','Please check your inbox to reset your password>');


    }

    public function resetPassword($token) {

        $tokenExits = \DB::table('password_reset_tokens')->where('token',$token)->first();

        if ($tokenExits == null) {
            return redirect()->route('auth.forgotPassword')->with('error','Invalid Request');
        }

        return View('auth.reset_password',[
            'token' => $token
        ]);
    }

    public function processResetPassword(Request $request) {

        $token = $request->token;

        $tokenObj = \DB::table('password_reset_tokens')->where('token',$token)->first();

        if ($tokenObj == null) {
            return redirect()->route('auth.forgotPassword')->with('error','Invalid Request');
        }

        $user = User::where('email',$tokenObj->email)->first();

        $validator = Validator::make($request->all(),[
            'new_password' => 'required|min:5',
            'password_confirmation' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return redirect()->route('auth.resetPassword',$token)->withErrors($validator);
        }

        User::where('id',$user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        \DB::table('password_reset_tokens')->where('email',$user->email)->delete();

        return redirect()->route('auth.login')->with('success','You have successfully updated your password');
    }
}
