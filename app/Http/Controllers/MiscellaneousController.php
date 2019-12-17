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
        $cronJob[] = ['description' => 'Email sender, runs each minute', 'cron' => '/usr/bin/php -q /home/phpnewsletter/artisan email:send >/dev/null 2>&1'];
        $cronJob[] = ['description' => 'Resending unsent emails, runs each 10 minutes', 'cron' => '/usr/bin/php -q /home/phpnewsletter/artisan email:unsent >/dev/null 2>&1'];

        return view('admin.miscellaneous.cron_job_list', compact('cronJob'))->with('title', 'Crontab');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function phpinfo()
    {
        $phpinfo = StringHelpers::phpinfoArray();

        return view('admin.miscellaneous.phpinfo', compact('phpinfo'))->with('title', 'PHP Info');
    }
}
