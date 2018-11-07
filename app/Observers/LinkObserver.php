<?php
/**
 * Created by PhpStorm.
 * User: lining
 * Date: 2018/11/7
 * Time: 3:12 PM
 */
namespace App\Observers;

use App\Models\Link;
use Cache;
class LinkObserver{


    public function saved(Link $link)
    {
        Cache::forget($link->cache_key);
    }



}