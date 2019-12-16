<?php

namespace App\Http\Controllers;

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
        return view('admin.update.index')->with('title', trans('frontend.title.update'));
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
