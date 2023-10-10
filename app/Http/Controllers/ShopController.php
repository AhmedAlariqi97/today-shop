<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null) {
        $categorySelected = '';
        $subCategorySelected = '';
        $brandsArray = [];

        if(!empty($request->get('brand'))) {
            $brandsArray = explode(',',$request->get('brand'));
        }

        // 'ASC'
        $categories = Category::orderBy('name','ASC')->with('sub_category')->where('status',1)->get();
        // 'ASC'
        $brands = Brand::orderBy('name','ASC')->where('status',1)->get();

        $products = Product::where('status',1);

        // 'DESC'
        // Appley Filter here
        if (!empty($categorySlug)) {
            $category = Category::where('slug',$categorySlug)->first();
            $products = $products->where('category_id',$category->id);
            $categorySelected = $category->id;
        }

        if (!empty($subCategorySlug)) {
            $subCategory = SubCategory::where('slug',$subCategorySlug)->first();
            $products = $products->where('sub_category_id',$subCategory->id);
            $subCategorySelected = $subCategory->id;
        }


        $products = $products->orderBy('id','DESC');
        $products = $products->get();

        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['subCategorySelected'] = $subCategorySelected;
        $data['brandsArray'] = $brandsArray;



        return View('front.shop',$data);
    }
}
