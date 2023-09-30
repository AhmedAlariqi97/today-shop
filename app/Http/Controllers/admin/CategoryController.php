<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Image;
use Spatie\FlareClient\Http\Exceptions\NotFound;

class CategoryController extends Controller
{
    public function index (Request $request) {
        $categories = Category::latest();

        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name','like','%'.$request->get('keyword').'%');
        }

        $categories = $categories->paginate(10);
        // $data['categories'] = $categories;
        return View('admin.category.list',compact('categories'));

    }

    public function create () {
        return View('admin.category.create');
    }

    public function store (Request $request) {

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories'
        ]);

        if ($validator->passes()) {

            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            // save image here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/upload/category/'.$newImageName;
                File::copy($sPath,$dPath);

                //generate image thumbnail
                $dPath = public_path().'/upload/category/thumb/'.$newImageName;
                $img = Image::make($sPath);
                $img->resize(450, 600);
                $img->save($dPath);

                $category->image = $newImageName;
                $category->save();


            }


            $request->session()->flash('success','Category added successful');

            return response()->json([
                'status' => true,
                'message' => 'Category added successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function edite ($categoryId, Request $request) {
        $category = Category::find($categoryId);
        if (empty($category)) {
            return redirect()->route('categories.index');
        }


        return View('admin.category.edite', compact('category'));
    }

    public function update ($categoryId, Request $request) {

        $category = Category::find($categoryId);
        if (empty($category)) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category not found'
             ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories,slug'.$category->id.',id'
        ]);

        if ($validator->passes()) {


            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            // save image here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/upload/category/'.$newImageName;
                File::copy($sPath,$dPath);

                //generate image thumbnail
                $dPath = public_path().'/upload/category/thumb/'.$newImageName;
                $img = Image::make($sPath);
                $img->resize(450, 600);
                $img->save($dPath);

                $category->image = $newImageName;
                $category->save();


            }


            $request->session()->flash('success','Category updated successful');

            return response()->json([
                'status' => true,
                'message' => 'Category updated successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function delete () {

    }

}
