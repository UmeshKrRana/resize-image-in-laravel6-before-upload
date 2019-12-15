<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ImageResize;
use App\Image;

class ImageResizeController extends Controller
{
    public function index() {
        return view('fileUpload');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image                   =       $request->file('image');
        $input['imagename']      =       time().'.'.$image->extension();

        $destinationPath         =       public_path('/uploads/thumbnail');

        $img                     =       ImageResize::make($image->path());


        // --------- [ Resize Image ] ---------------

        $img->resize(150, 100, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$input['imagename']);


        // ----------- [ Uploads Image in Original Form ] ----------
        
        $destinationPath        =        public_path('/uploads/original');

        $image->move($destinationPath, $input['imagename']);

        // store into database table
        Image::create(['img' => $input['imagename'], 'thumbnail_img' => $input['imagename']]);

        return back()
            ->with('success', 'Image Uploaded successfully')
            ->with('imageName', $input['imagename']);
    }
}
