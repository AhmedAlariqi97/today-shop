<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    public function addToCart(Request $request) {

        $product = Product::with('product_images')->find($request->id);

        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }

        if (Cart::count() > 0) {
            //echo "Product aleady in cart";
            //Products found in cart
            //Check if this product already in the cart
            //Return as message that product already added in your cart
            //if product not found in the cart, then add product in cart

            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach ($cartContent as $item) {
                if ($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }


            if ($productAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' =>
                (!empty($product->product_images)) ? $product->product_images->first() : '']);

                $status = true;
                $message = '<strong>'.$product->title.'</strong> added in cart';
                session()->flash('success',$message);
            } else {
                $status = false;
                $message = $product->title.'already added in cart';
                session()->flash('error',$message);
            }

        } else {
            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' =>
            (!empty($product->product_images)) ? $product->product_images->first() : '']);

            $status = true;
            $message = '<strong>'.$product->title.'</strong> added in cart';
            session()->flash('success',$message);
        }
        // Cart::add('193ad', 'Product 1', 1,9.99);

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function cart() {
        $cartContent = Cart::content();

        // dd($cartContent);
        $data['cartContent'] = $cartContent;
        return view('front.cart',$data);
    }

    public function updateCart(Request $request) {

        $rowId = $request->rowId;
        $qty = $request->qty;

        //check qty available in stock
        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);

        if ($product->track_qty == 'Yes') {

            if ($qty <= $product->qty) {
                Cart::update($rowId,$qty);
                $status = true;
                $message = 'Cart updated successfully';
                session()->flash('success',$message);
            } else {
                $status = false;
                $message = 'Requested qty('.$qty.') not available in stock';
                session()->flash('error',$message);
            }
        } else {
            Cart::update($rowId,$qty);
            $status = true;
            $message = 'Cart updated successfully';
            session()->flash('success',$message);
        }


        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteItem(Request $request) {

        $itemInfo = Cart::get($request->rowId);
        if ($itemInfo == null) {
            $errorMessage = 'Item not found in cart';
            session()->flash('success',$errorMessage);

            return response()->json([
                'status' => false,
                'message' => $errorMessage
            ]);
        }


        Cart::remove($request->rowId);

        $message = 'Item removed front cart successfully';
        session()->flash('success',$message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);


    }

    public function checkout() {

        //-- if cart is empty redirect to cart page
        if (Cart::count() == 0) {
            return redirect()->route('front.cart');
        }

        //-- if user is not logged in then redirect to login page
        if (Auth::check() == false) {

            // storage session url when user click on btn
            if (!session()->has('url.intended')) {
                session(['url.intended' => url()->current()]);
            }

            return redirect()->route('auth.login');
        }

        session()->forget('url.intended');

        $countries = Country::orderBy('name','ASC')->get();

        return view('front.checkout',['countries' => $countries]);
    }

    public function processCheckout(Request $request) {

        $validator = Validator::make($request->all(),[
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'country' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
        ]);

        if ($validator->fails()) {

            $request->session()->flash('success','Product added successful');

            return response()->json([
                'status' => false,
                'message' => 'please fix the errors',
                'errors' => $validator->errors()

            ]);

        } else {
            return response()->json([
                'status' => true,
            ]);
        }
    }


}
