<?php
/**
 * Created by PhpStorm.
 * User: lining
 * Date: 2018/11/1
 * Time: 1:05 PM
 */
function route_class()
{
    return str_replace('.', '-', \Illuminate\Support\Facades\Route::currentRouteName());
}

function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return str_limit($excerpt, $length);
}

function model_admin_link($title,$model)
{
    return model_link($title,$model,'admin');
}

function model_link($title,$model,$prefix = '')
{
    $model_name = model_plural_name($model);

    //初始化 prefix
    $prefix  = $prefix ? "/$prefix/" : '/';

    // 拼接全量url
    $url = config('app.url') . $prefix . $model_name .'/' . $model->id;

    return '<a href="'.$url.'" target="_blank">'.$title.'</a>';

}


function model_plural_name($model)
{
    // 从实体中获取完成类名，App\Models\User
    $full_class_name = get_class($model);

    // 获取基础类名， App\Models\User -> User
    $class_name = class_basename($full_class_name);

    // 蛇形命名 User -> user , FooBar -> foo_bar
    $snake_case_name = snake_case($class_name);

    // 复数形式 user -> users
    return str_plural($snake_case_name);
}