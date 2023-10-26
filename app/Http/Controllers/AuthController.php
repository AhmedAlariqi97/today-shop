<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
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
}
