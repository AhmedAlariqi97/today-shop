<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
// use Intervention\Image;
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
            $category->is_featured = $request->is_featured;
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
                // $img->resize(450, 600);
                $img->fit(450, 600, function ($constraint) {
                    $constraint->upsize();
                });

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
            // if the record delete it form database
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category not found'
             ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            // 'slug' => 'required|unique:categories,slug'.$category->id.',id'
            'slug' => 'required'
        ]);

        if ($validator->passes()) {


            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->is_featured = $request->is_featured;
            $category->save();

            $oldImage = $category->image;

            // save image here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'-'.time().'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/upload/category/'.$newImageName;
                File::copy($sPath,$dPath);

                //generate image thumbnail
                $dPath = public_path().'/upload/category/thumb/'.$newImageName;
                $img = Image::make($sPath);
                // $img->resize(450, 600);
                $img->fit(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dPath);

                $category->image = $newImageName;
                $category->save();


                //Delete Old Images here
                File::delete(public_path().'/upload/category/thumb/'.$oldImage);
                File::delete(public_path().'/upload/category'.$oldImage);


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

    public function destroy ($categoryId, Request $request) {

        $category = Category::find($categoryId);
        if (empty($category)) {
            return redirect()->route('categories.index');
        }

        //Delete Old Images here
        File::delete(public_path().'/upload/category/thumb/'.$category->image);
        File::delete(public_path().'/upload/category'.$category->image);

        $category->delete();

        $request->session()->flash('success','Category deleted successful');

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    // // Store the file input content in the session
    // public function storeFileContent(Request $request)
    // {
    //     $fileContent = $request->input('file_content');
    //     $request->session()->put('file_content', $fileContent);

    //     return response()->json(['success' => true]);
    // }

    // // Auto-complete functionality
    // public function autocomplete(Request $request)
    // {
    //     $fileContent = $request->session()->get('file_content');

    //     // Perform auto-complete logic

    //     return response()->json(['suggestions' => $fileContent]);
    // }

}
