<?php

namespace App\Http\Controllers;

use App\Helpers\{StringHelpers, LicenseHelpers};
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
        $update = new LicenseHelpers(app()->getLocale(), env('VERSION'));

        $button_update = '';
        $msg_no_update = '';

        if ($update->checkUpgrade() && $update->checkTree()) {
            $button_update = trans('frontend.str.button_update');
            $button_update = str_replace('%NEW_VERSION%', $update->getUpgradeVersion(), $button_update);
            $button_update = str_replace('%SCRIPT_NAME%', trans('frontend.str.script_name'), $button_update);
        } else {
            $msg_no_update = trans('frontend.str.no_updates');
            $msg_no_update = str_replace('%SCRIPT_NAME%', trans('frontend.str.script_name'), $msg_no_update);
            $msg_no_update = str_replace('%NEW_VERSION%', env('VERSION'), $msg_no_update);
        }

        $infoAlert = trans('frontend.hint.update_index') ? trans('frontend.hint.update_index') : null;

        return view('admin.update.index', compact('button_update', 'msg_no_update', 'infoAlert'))->with('title', trans('frontend.title.update'));
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
        }

        $licenseKey = $request->input('license_key');

        $update = new LicenseHelpers(app()->getLocale(), env('VERSION'));
        $check = $update->checkLicenseKey($licenseKey);

        if (isset($check['error']) && in_array($_SERVER['REMOTE_ADDR'], ['::1', '127.0.0.1']) == false) {
            $check['error'] = str_replace('LICENSE_IS_USED', trans('license.error.license_is_used'), $check['error']);
            $check['error'] = str_replace('LICENSE_NOT_FOUND', trans('license.error.license_not_found'), $check['error']);
            $check['error'] = str_replace('ERROR_CHECKING_LICENSE', trans('license.error.error_checking_license'), $check['error']);

            return redirect(URL::route('admin.update.index'))->with('error', $check['error']);
        }

        $update->updateLicensekey($licenseKey);

        StringHelpers::setEnvironmentValue('LICENSE_KEY', $licenseKey);

        return redirect(URL::route('admin.update.index'))->with('success', trans('message.data_updated'));
    }
}
