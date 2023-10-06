<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\File;

class ProductImagesController extends Controller
{
    public function update(Request $request)
    {
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'NULL';
        $productImage->save();

        $imageName = $request->product_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
        $productImage->image = $imageName;
        $productImage->save();

        //Large Image
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

        return response()->json([
            'status' => true,
            'image_id' => $productImage->id,
            'ImagePath' => asset('/upload/product/small/'.$productImage->image),
            'message' => 'Image saved successfully'
        ]);
    }

    public function destroy (Request $request) {

        $productImage = ProductImage::find($request->id);

        if (empty($productImage)) {
            return response()->json([
                'status' => false,
                'message' => 'Image note found'
            ]);
        }


        //Delete Old Images here
        File::delete(public_path().'/upload/product/large/'.$productImage->image);
        File::delete(public_path().'/upload/product/small/'.$productImage->image);

        $productImage->delete();


        return response()->json([
            'status' => true,
            'message' => 'Image deleted successfully'
        ]);
    }
}
