<?php

namespace App\Http\Controllers;

use App\Helpers\UpdateHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use URL;

class UpdateController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $update = new UpdateHelpers(app()->getLocale(), env('VERSION'));

        $button_update = '';
        $msg_no_update = '';

        if ($update->checkUpgrade() && $update->checkTree()){
            $button_update = trans('frontend.str.button_update');
            $button_update = str_replace('%NEW_VERSION%', $update->getUpgradeVersion(), $button_update);
            $button_update = str_replace('%SCRIPT_NAME%', trans('frontend.str.script_name'), $button_update);
        } else {
            $msg_no_update = trans('frontend.str.no_updates');
            $msg_no_update = str_replace('%SCRIPT_NAME%', trans('frontend.str.script_name'), $msg_no_update);
            $msg_no_update = str_replace('%NEW_VERSION%', env('VERSION'), $msg_no_update);
        }

        return view('admin.update.index', compact('button_update','msg_no_update'))->with('title', trans('frontend.title.update'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addLicenseKey(Request $request)
    {
        $rules = [
            'license_key' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {

            $path = base_path('.env');

            $env = file_get_contents($path);
            $env = str_replace( env('LICENSE_KEY'), $request->input('license_key'), $env);

            file_put_contents($path, $env);

            return redirect(URL::route('admin.update.index'))->with('success', trans('message.data_updated'));
        }
    }
}
