<?php

namespace App\Http\Controllers\Api;

use App\Models\Image;
use App\Handlers\ImageUploadHandler;
use App\Http\Resources\ImageResource;
use App\Http\Requests\Api\ImageRequest;
use Illuminate\Http\Request;

class ImagesController extends Controller
{
    public function store(ImageRequest $request, ImageUploadHandler $uploader, Image $image)
    {
        $user = $request->user();
//        dd($user);
//        die;
        $size = $request->type == 'avatar' ? 416 : 1024;
        $result = $uploader->save($request->image, \Str::plural($request->type), $user->id, $size);

        $image->path = $result['path'];
        $image->type = $request->type;
        $image->user_id = $user->id;
        $image->save();

        return new ImageResource($image);
    }
}
