<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Images;

use Illuminate\Http\Request;

class ImagesController extends Controller
{
    
    public function multipleImage(Request $request)
    {
  
        foreach($request->file('fileName') as $mediaFiles) {

            $file = Storage::disk('public')->put('image/temporaryImages', $mediaFiles);
            $save1= new Images();
            $save1->title = $file;
            $save1->path = $file;
            $save1->save();
            
        }

        return response()->json(['file_uploaded'], 200);
    }
}
