<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

       // save product on wishlist table
       $wishlist = new Wishlist;
       $wishlist->user_id = Auth::user()->id;
       $wishlist->product_id = $request->id;
       $wishlist->save();

       return response()->json([
        'status' => true,
        'message' => 'Product added in your wishlist'
    ]);

    }

    public function wishlist() {

        return View('auth.account.wishlist');
    }
}


