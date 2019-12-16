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
        return view('admin.miscellaneous.cron_job_list')->with('title', '');
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
