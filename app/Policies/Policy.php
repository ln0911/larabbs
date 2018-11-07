<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function before($user, $ability)
	{
	    // if ($user->isSuperAdmin()) {
	    // 		return true;
	    // }
        // 如果拥有管理内容的权限的话，授权通过
        if($user->can('manage_contents')){
            return true;
        }
	}
}
