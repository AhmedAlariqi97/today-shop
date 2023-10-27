<?php

namespace App\Http\Controllers;

use App\Mail\ContactEmail;
use App\Models\Pages;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    public function index() {
        $products = Product::where('is_featured','Yes')->orderBy('id','DESC')->where('status',1)->take(8)->get();
        $data['featureProducts'] = $products;

        // 'ASC'
        $latestProducts = Product::orderBy('id','DESC')->where('status',1)->take(8)->get();
        $data['latestProducts'] = $latestProducts;

        return View('front.home',$data);
    }

    public function addToWishlist(Request $request) {

       if (Auth::check() == false) {

            session(['url.intended' => url()->previous()]);

            return response()->json([

                'status' => false,
            ]);
       }

       $product = Product::where('id',$request->id)->first();

       if ($product == null) {
          return response()->json([
            'status' => false,
            'message' => '<div class="alert alert-danger">Product not found</div>'
          ]);
       }

       Wishlist::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id,
            ],
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id,
            ]
        );


        // save product on wishlist table
        //    $wishlist = new Wishlist;
        //    $wishlist->user_id = Auth::user()->id;
        //    $wishlist->product_id = $request->id;
        //    $wishlist->save();

        return response()->json([
            'status' => true,
            'message' => '<div class="alert alert-success"><strong>"'.$product->title.'"</strong> added in your wishlist</div>'
        ]);

    }

    public function wishlist() {

        $wishlists = Wishlist::where('user_id',Auth::user()->id)->with('product')->get();

        $data['wishlists'] = $wishlists;
        return View('auth.account.wishlist', $data);
    }

    public function removeProductFromWishlist(Request $request) {

        $wishlist = Wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->first();

        if ($wishlist == null) {
            session()->flash('error','Product already removed');

            return response()->json([
                'status' => false
            ]);

        } else {

            // Wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->delete();
            $wishlist->delete();

            session()->flash('success','Product deleted from your wishlist successful');
            return response()->json([
                'status' => true
            ]);

        }

    }

    public function page($slug) {

        $page = Pages::where('slug',$slug)->first();

        if($page == null) {
            abort(404);
        }

        return View('front.page',[
            'page' => $page
        ]);
    }

    public function sendContactEmail(Request $request) {

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required|min:10'
        ]);

        if ($validator->passes()) {

            $mailData = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'mail_subject' => 'You have received a contact email'
            ];

            $admin = User::where('id',1)->first();

            Mail::to($admin->email)->send(new ContactEmail($mailData));


            session()->flash('success','Your subject sended successful');

            return response()->json([
                'status' => true,
                'message' => 'Your subject sended successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}


