<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index() {
        // 'ASC'
        $categories = Category::orderBy('name','ASC')->with('sub_category')->where('status',1)->get();
        $data['categories'] = $categories;

        // 'ASC'
        $brands = Brand::orderBy('name','ASC')->where('status',1)->get();
        $data['brands'] = $brands;

        // 'DESC'
        $products = Product::orderBy('id','DESC')->where('status',1)->get();
        $data['products'] = $products;



        return View('front.shop',$data);
    }
}
