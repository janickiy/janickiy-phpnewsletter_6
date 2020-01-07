<?php

namespace App\Http\Controllers;

use App\Helpers\{StringHelpers,LicenseHelpers};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use URL;

class ExpiredController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $infoAlert = trans('frontend.hint.expired_index') ? trans('frontend.hint.expired_index') : null;

        return view('admin.expired.index', compact('infoAlert'))->with('title', trans('frontend.title.expired'));
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

            $licenseKey = $request->input('license_key');

            $update = new LicenseHelpers(app()->getLocale(), env('VERSION'));
            $check = $update->checkLicenseKey($licenseKey);

            if (isset($check['error'])) {
                $check['error'] = str_replace('LICENSE_IS_USED', trans('license.error.license_is_used'), $check['error']);
                $check['error'] = str_replace('LICENSE_NOT_FOUND', trans('license.error.license_not_found'), $check['error']);
                $check['error'] = str_replace('ERROR_CHECKING_LICENSE', trans('license.error.error_checking_license'), $check['error']);

                return redirect(URL::route('admin.update.index'))->with('error',  $check['error']);
            }

            $update->updateLicensekey($licenseKey);

            StringHelpers::setEnvironmentValue('LICENSE_KEY', $licenseKey);

            return redirect(URL::route('admin.expired.index'))->with('success', trans('message.data_updated'));
        }
    }
}
