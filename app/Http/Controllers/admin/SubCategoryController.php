<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;

class SubCategoryController extends Controller
{
    public function index (Request $request) {
        $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
        ->latest('sub_categories.id')
        ->leftJoin('categories','categories.id','sub_categories.category_id');

        if (!empty($request->get('keyword'))) {
            $subCategories = $subCategories->where('sub_categories.name','like','%'.$request->get('keyword').'%');
            $subCategories = $subCategories->orWhere('categories.name','like','%'.$request->get('keyword').'%');
        }

        $subCategories = $subCategories->paginate(10);
        // $data['categories'] = $categories;
        return View('admin.sub_category.list',compact('subCategories'));

    }

    public function create () {
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        return View('admin.sub_category.create',$data);
    }

    public function store (Request $request) {

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required'
        ]);

        if ($validator->passes()) {

            $subcategory = new SubCategory();
            $subcategory->name = $request->name;
            $subcategory->slug = $request->slug;
            $subcategory->status = $request->status;
            $subcategory->category_id = $request->category;
            $subcategory->save();

            // save image here
            // if (!empty($request->image_id)) {
            //     $tempImage = TempImage::find($request->image_id);
            //     $extArray = explode('.',$tempImage->name);
            //     $ext = last($extArray);

            //     $newImageName = $category->id.'.'.$ext;
            //     $sPath = public_path().'/temp/'.$tempImage->name;
            //     $dPath = public_path().'/upload/category/'.$newImageName;
            //     File::copy($sPath,$dPath);

            //     //generate image thumbnail
            //     $dPath = public_path().'/upload/category/thumb/'.$newImageName;
            //     $img = Image::make($sPath);
            //     // $img->resize(450, 600);
            //     $img->fit(450, 600, function ($constraint) {
            //         $constraint->upsize();
            //     });

            //     $img->save($dPath);

            //     $category->image = $newImageName;
            //     $category->save();


            // }


            $request->session()->flash('success','Sub Category added successful');

            return response()->json([
                'status' => true,
                'message' => 'Sub Category added successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function edite ($subCategoryId, Request $request) {
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
       
        return View('admin.sub_category.edite',$data);
    }

    public function update ($subCategoryId, Request $request) {

        $subCategory = subCategory::find($subCategoryId);
        if (empty($subCategory)) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Sub Category not found'
             ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            // 'slug' => 'required|unique:categories,slug'.$category->id.',id'
            'slug' => 'required'
        ]);

        if ($validator->passes()) {


            $subcategory = new SubCategory();
            $subcategory->name = $request->name;
            $subcategory->slug = $request->slug;
            $subcategory->status = $request->status;
            $subcategory->category_id = $request->category;
            $subcategory->save();


            $request->session()->flash('success','Sub Category updated successful');

            return response()->json([
                'status' => true,
                'message' => 'Sub Category updated successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy ($subCategoryId, Request $request) {

        $subCategory = Category::find($subCategoryId);
        if (empty($subCategory)) {
            return redirect()->route('sub-categories.index');
        }



        $subCategory->delete();

        $request->session()->flash('success','Sub Category deleted successful');

        return response()->json([
            'status' => true,
            'message' => 'Sub Category deleted successfully'
        ]);
    }
}
