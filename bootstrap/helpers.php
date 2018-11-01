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