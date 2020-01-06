<?php

namespace App\Http\Start;

use Illuminate\Support\Facades\Storage;
use App\Helpers\LicenseHelpers;

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

        if (in_array($role, $permissions)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public static function check_license()
    {
        $result = true;

        $license = new LicenseHelpers(app()->getLocale(), env('VERSION'));
        $domain = (substr($_SERVER["SERVER_NAME"], 0, 4)) == "www." ? str_replace('www.', '', $_SERVER["SERVER_NAME"]) : $_SERVER["SERVER_NAME"];

        if (Storage::exists($license::licenseKey)) {
            $lisenseInfo = $license->getLicenseInfo();

            if ($lisenseInfo['domain'] != $domain) {
                $license->makeLicensekey(env('LICENSE_KEY'));
            }

            if ($lisenseInfo['license_type'] == 'demo' && $domain == $lisenseInfo['domain']) {
                if (round((strtotime($lisenseInfo['date_to']) - strtotime(date("Y-m-d H:i:s"))) / 3600 / 24) < 0) {
                    return false;
                }
            }
        } else {
            $license->makeLicensekey(env('LICENSE_KEY'));
        }


        return $result;
    }
}
