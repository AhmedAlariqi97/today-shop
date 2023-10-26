<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Pages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
{
    public function index (Request $request) {
        $pages = Pages::latest();

        if (!empty($request->get('keyword'))) {
            $pages = $pages->where('name','like','%'.$request->get('keyword').'%');
        }

        $pages = $pages->paginate(10);
        // $data['categories'] = $categories;
        return View('admin.pages.list',compact('pages'));

    }

    public function create () {
        return View('admin.pages.create');
    }

    public function store (Request $request) {

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:pages'
        ]);

        if ($validator->passes()) {

            $page = new Pages();
            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();


            session()->flash('success','Page added successful');

            return response()->json([
                'status' => true,
                'message' => 'Page added successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function edite ($pageId, Request $request) {
        $page = Pages::find($pageId);
        if (empty($page)) {
            return redirect()->route('pages.index');
        }


        return View('admin.pages.edite', compact('page'));
    }

    public function update ($pageId, Request $request) {

        $page = Pages::find($pageId);
        if (empty($page)) {
            // if the record delete it form database
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Page not found'
             ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required'
        ]);

        if ($validator->passes()) {


            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();

            session()->flash('success','Page updated successful');

            return response()->json([
                'status' => true,
                'message' => 'Page updated successful'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy ($pageId, Request $request) {

        $page = Pages::find($pageId);

        if ($page == null) {
            session()->flash('error','Page not found');

            return response()->json([
                'status' => true
            ]);
        }

        if (empty($page)) {
            return redirect()->route('pages.index');
        }

        $page->delete();

        session()->flash('success','Page deleted successful');

        return response()->json([
            'status' => true,
            'message' => 'Page deleted successfully'
        ]);
    }
}
