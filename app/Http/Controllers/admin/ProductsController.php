<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Image;

class ProductsController extends Controller
{
    public function index (Request $request) {

        $products = Product::latest('id')->with('product_images')->paginate();
        // dd($products);
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
            $products->short_description = $request->short_description;
            $products->shipping_returns = $request->shipping_returns;
            $products->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
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



            // Save Gallery Pics
            if(!empty($request->image_array)) {
                foreach ($request->image_array as $temp_image_id) {

                    // for storage name of image from temp image
                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.',$tempImageInfo->name);
                    $ext = last($extArray); //like jpg,gif,png etc


                    $productImage = new ProductImage();
                    $productImage->product_id = $products->id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imageName = $products->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    //Generate Product Thumbnails

                    //Large Image
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destPath = public_path().'/upload/product/large/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->resize(1400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);


                    //small Image
                    $destPath = public_path().'/upload/product/small/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->fit(300, 300);
                    $image->save($destPath);



                }
            }




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

    public function edite ($Id, Request $request) {

        $product = Product::find($Id);

        if (empty($product)) {
            $request->session()->flash('error','Record not found');
            return redirect()->route('products.index');
        }

        // Fetch product images
        $productImages = ProductImage::where('product_id',$product->id)->get();

        // Fetch Related products
        $relatedProducts = [];
        if ($product->related_products != '') {
            $productArray = explode(',',$product->related_products);

            $relatedProducts = Product::whereIn('id',$productArray)->get();
        }

        $subCategories = SubCategory::where('category_id',$product->category_id)->get();





        $categories = Category::orderBy('name','ASC')->get();
        $brands = Brand::orderBy('name','ASC')->get();

        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['product'] = $product;
        $data['subCategories'] = $subCategories;
        $data['productImages'] = $productImages;
        $data['relatedProducts'] = $relatedProducts;


        return View('admin.product.edite',$data);
    }

    public function update ($Id, Request $request) {

        $product = Product::find($Id);

        if (empty($product)) {
            $request->session()->flash('error','Record not found');
            // if the record delete it form database
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Products not found'
             ]);
        }

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,'.$product->id.',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',

        ];

        if(!empty($request->track_qty) && $request->track_qty == 'Yes'){

            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()) {


            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->save();



            $request->session()->flash('success','Product updated successful');

            return response()->json([
                'status' => true,
                'message' => 'Product updated successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function destroy ($Id, Request $request) {

        $products = Product::find($Id);
        if (empty($products)) {
            $request->session()->flash('error','Product not found');
            // return redirect()->route('categories.index');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Product not found'
             ]);
        }

        $productImages = ProductImage::where('product_id',$Id)->get();

        if (!empty($productImages)) {
            foreach ($productImages as $productImage) {
                 //Delete Old Images here
                File::delete(public_path().'/upload/product/large/'.$productImage->image);
                File::delete(public_path().'/upload/product/small/'.$productImage->image);

            }
            ProductImage::where('product_id',$Id)->delete();
        }



        $products->delete();

        $request->session()->flash('success','Product deleted successful');

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    public function getProducts (Request $request) {

        $tempProduct = [];
        if ($request->term != "") {
            $products = Product::where('title','like','%'.$request->term.'%')->get();

            if ($products != null) {
                foreach ($products as $product) {
                    $tempProduct[] = array('id' => $product->id, 'text' => $product->title);
                }

            }
        }

        return response()->json([
            'tags' => $tempProduct,
            'status' => true
        ]);

    }
}
