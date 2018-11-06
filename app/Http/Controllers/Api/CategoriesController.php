<?php

namespace App\Http\Controllers\Api;


use App\Models\Category;
use App\Transformers\CategoryTransformer;

class CategoriesController extends Controller
{
    /**
     * get Categories
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        return $this->response->collection(Category::all(), new CategoryTransformer());
    }
}
