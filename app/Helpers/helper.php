<?php

use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\Pages;
use App\Models\ProductImage;
use App\Models\Wishlist;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

    // get categories in poblic scope

    function getCategories(){

        return Category::orderBy('name','ASC')
            ->with('sub_category')
            ->orderBy('id','DESC')
            ->where('status',1)
            ->where('is_featured','Yes')
            ->get();
    }

     // get product images in poblic scope

    function getProductImage($productId){

        return ProductImage::where('product_id',$productId)->first();
    }

     // send order email to customer and admin, when complete checkout process in poblic scope

    function orderEmail ($orderId, $userType="customer") {

        $order = Order::where('id',$orderId)->with('items')->first();

        if ($userType == 'customer') {

            $subject = 'Thanks for your order';
            $email = $order->email;

        } else {

            $subject = 'You have received an order';
            $email = env('ADMIN_EMAIL');
        }

        $mailData = [
            'subject' => $subject,
            'order' => $order,
            'userType' => $userType
        ];

        Mail::to($email)->send(new OrderEmail($mailData));

        // dd($order);
    }

     // get country in poblic scope

    function getCountryInfo($id){
        return Country::where('id',$id)->first();
    }

     // get pages in poblic scope

    function staticPages() {
        $pages = Pages::orderBy('name','ASC')->get();
        return $pages;
    }

     // get numbers of items inside the cart in poblic scope

    function getCartItemsNumber() {
        $counts = Cart::count();
        return $counts;
    }

    // get numbers of items inside the wishlist in poblic scope

    function getWishlistItemsNumber() {

        return Wishlist::where('user_id',Auth::user()->id)->count();

    }



?>
