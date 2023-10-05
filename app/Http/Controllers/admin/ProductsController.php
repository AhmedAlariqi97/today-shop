<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public function index (Request $request) {

        $products = Product::latest()->paginate();

        if (!empty($request->get('keyword'))) {
            $products = $products->where('name','like','%'.$request->get('keyword').'%');
        }

        // $products = $products->paginate(10);
        $data['products'] = $products;
        return View('admin.product.list',$data);

    }

    public function create () {
        $categories = Category::orderBy('name','ASC')->get();
        $brands = Brand::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;


        return View('admin.product.create',$data);
    }

    public function store (Request $request) {

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',

        ];

        if(!empty($request->track_qty) && $request->track_qty == 'Yes'){

            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()) {

            $products = new Product();
            $products->title = $request->title;
            $products->slug = $request->slug;
            $products->description = $request->description;
            $products->price = $request->price;
            $products->compare_price = $request->compare_price;
            $products->sku = $request->sku;
            $products->barcode = $request->barcode;
            $products->track_qty = $request->track_qty;
            $products->qty = $request->qty;
            $products->status = $request->status;
            $products->category_id = $request->category;
            $products->sub_category_id = $request->sub_category;
            $products->brand_id = $request->brand;
            $products->is_featured = $request->is_featured;
            $products->save();




            $request->session()->flash('success','Product added successful');

            return response()->json([
                'status' => true,
                'message' => 'Product added successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function edite ($brandId, Request $request) {
        // $brand = Brand::find($brandId);
        // if (empty($brand)) {
        //     return redirect()->route('brands.index');
        // }


        // return View('admin.brand.edite', compact('brand'));
    }

    public function update ($brandId, Request $request) {

        // $brand = Brand::find($brandId);
        // if (empty($brand)) {
        //     // if the record delete it form database
        //     return response()->json([
        //         'status' => false,
        //         'notFound' => true,
        //         'message' => 'Brands not found'
        //      ]);
        // }

        // $validator = Validator::make($request->all(),[
        //     'name' => 'required',
        //     // 'slug' => 'required|unique:categories,slug'.$category->id.',id'
        //     'slug' => 'required'
        // ]);

        // if ($validator->passes()) {


        //     $brand->name = $request->name;
        //     $brand->slug = $request->slug;
        //     $brand->status = $request->status;
        //     $brand->save();




        //     $request->session()->flash('success','Brand updated successful');

        //     return response()->json([
        //         'status' => true,
        //         'message' => 'Brand updated successful'
        //     ]);

        // } else {
        //     return response()->json([
        //         'status' => false,
        //         'errors' => $validator->errors()
        //     ]);
        // }
    }

    public function destroy ($brandId, Request $request) {

        // $brand = Brand::find($brandId);
        // if (empty($brand)) {
        //     return redirect()->route('brands.index');
        // }



        // $brand->delete();

        // $request->session()->flash('success','Brand deleted successful');

        // return response()->json([
        //     'status' => true,
        //     'message' => 'Brand deleted successfully'
        // ]);
    }
}
