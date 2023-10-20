<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandsController extends Controller
{
    public function index (Request $request) {
        $brands = Brand::latest();

        if (!empty($request->get('keyword'))) {
            $brands = $brands->where('name','like','%'.$request->get('keyword').'%');
        }

        $brands = $brands->paginate(10);
        // $data['categories'] = $categories;
        return View('admin.brand.list',compact('brands'));

    }

    public function create () {
        return View('admin.brand.create');
    }

    public function store (Request $request) {

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands'
        ]);

        if ($validator->passes()) {

            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();




            $request->session()->flash('success','Brand added successful');

            return response()->json([
                'status' => true,
                'message' => 'Brand added successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function edite ($brandId, Request $request) {
        $brand = Brand::find($brandId);
        if (empty($brand)) {
            return redirect()->route('brands.index');
        }

        return View('admin.brand.edite', compact('brand'));
    }

    public function update ($brandId, Request $request) {

        $brand = Brand::find($brandId);
        if (empty($brand)) {
            // if the record delete it form database
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Brands not found'
             ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            // 'slug' => 'required|unique:categories,slug'.$category->id.',id'
            'slug' => 'required'
        ]);

        if ($validator->passes()) {


            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();




            $request->session()->flash('success','Brand updated successful');

            return response()->json([
                'status' => true,
                'message' => 'Brand updated successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy ($brandId, Request $request) {

        $brand = Brand::find($brandId);
        if (empty($brand)) {
            return redirect()->route('brands.index');
        }



        $brand->delete();

        $request->session()->flash('success','Brand deleted successful');

        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully'
        ]);
    }
}
