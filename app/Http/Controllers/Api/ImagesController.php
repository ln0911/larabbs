<?php

namespace App\Http\Controllers\Api;


use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Image;
use App\Transformers\ImageTransformer;

class ImagesController extends Controller
{

    /**
     * 上传保存图片
     * @param ImageRequest $request
     * @param ImageUploadHandler $uploader
     * @param Image $image
     * @return \Dingo\Api\Http\Response
     */
    public function store(ImageRequest $request,ImageUploadHandler $uploader , Image $image)
    {
        $user = $this->user();

        $size = $request->type == 'avatar' ? 362 :1024;

        $result = $uploader->save($request->image, str_plural($request->type),$user->id,$size);

        $image->path    = $result['path'];
        $image->type    = $request->type;
        $image->user_id = $user->id;
        $image->save();

        return $this->response->item($image,new ImageTransformer())->setStatusCode(201);
    }
}
