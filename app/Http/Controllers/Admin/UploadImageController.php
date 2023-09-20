<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image'=>'image|max:250|dimensions:max_width=100,max_height=200'
        ]);
        $image = $request->file('image');
        $hashName = $image->hashName();

        $image->move(public_path('/upload'),$hashName);

        return [
            'url'=>"/upload/{$hashName}"
        ];
    }
}
