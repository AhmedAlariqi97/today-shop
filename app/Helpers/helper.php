<?php

use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\Order;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Mail;

    function getCategories(){

        return Category::orderBy('name','ASC')
            ->with('sub_category')
            ->orderBy('id','DESC')
            ->where('status',1)
            ->where('is_featured','Yes')
            ->get();
    }

    function getProductImage($productId){

        return ProductImage::where('product_id',$productId)->first();
    }

    function orderEmail ($orderId) {

        $order = Order::where('id',$orderId)->with('items')->first();

        $mailData = [
            'subject' => 'Thanks for your order',
            'order' => $order
        ];

        Mail::to($order->email)->send(new OrderEmail($mailData));

        // dd($order);
    }



?>
