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