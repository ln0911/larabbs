<?php
/**
 * Created by PhpStorm.
 * User: lining
 * Date: 2018/11/6
 * Time: 5:28 PM
 */
namespace App\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract{


    public function transform(Category $category)
    {
        return [
            'id'          => $category->id,
            'name'        => $category->name,
            'description' => $category->description
        ];
    }



}