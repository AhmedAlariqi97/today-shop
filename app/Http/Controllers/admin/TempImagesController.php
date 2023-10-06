<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Image;

class TempImagesController extends Controller
{
    public function create(Request $request){


        if ($request->image) {
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $newName = time().'.'.$ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'/temp',$newName);

            //generate image thumbnail
            $sourcePath = public_path().'/temp/'.$newName;
            $destPath = public_path().'/temp/thumb/'.$newName;
            $image = Image::make($sourcePath);
            // $img->resize(450, 600);
            $image->fit(300, 300);
            $image->save($destPath);

            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'ImagePath' => asset('/temp/thumb/'.$newName),
                'message' => 'image uploaded successfully'
            ]);
        }
    }
}
