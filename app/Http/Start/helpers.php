<?php

namespace App\Http\Start;

class Helpers
{

    /**
     * @param $role
     * @param string $permissions
     * @return bool
     */
	public static function has_permission($role, $permissions = '')
	{
	    if ($role == 'admin') return true;

		$permissions = explode('|', $permissions);

        if (in_array ($role , $permissions)) {
            return true;
        } else {
            return false;
        }
	}
}
