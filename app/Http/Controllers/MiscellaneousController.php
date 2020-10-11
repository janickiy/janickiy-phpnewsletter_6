<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelpers;

class MiscellaneousController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cron_job_list()
    {
        $infoAlert = trans('frontend.hint.cron_job_list') ? trans('frontend.hint.cron_job_list') : null;

        $path = base_path() ? base_path() . '/artisan' : ' /home/phpnewsletter/artisan';

        $cronJob[] = ['description' => 'Email sender, runs each minute', 'cron' => '/usr/bin/php -q ' . $path . ' email:send >/dev/null 2>&1'];
        $cronJob[] = ['description' => 'Resending unsent emails, runs each 10 minutes', 'cron' => '/usr/bin/php -q ' . $path . ' email:unsent >/dev/null 2>&1'];

        return view('admin.miscellaneous.cron_job_list', compact('cronJob', 'infoAlert'))->with('title', 'Crontab');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function phpinfo()
    {
        $phpinfo = StringHelpers::phpinfoArray();
        $infoAlert = trans('frontend.hint.phpinfo') ? trans('frontend.hint.phpinfo') : null;

        return view('admin.miscellaneous.phpinfo', compact('phpinfo', 'infoAlert'))->with('title', 'PHP Info');
    }
}
