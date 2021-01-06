<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelpers;
use Illuminate\Http\Request;
use App\Models\{Charset,Customheaders, Settings};
use URL;

class SettingsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $option_charset = [];

        foreach (Charset::orderBy('charset')->get() as $row) {
            $option_charset[$row->charset] = StringHelpers::charsetList($row->charset);
        }

        $customheaders = Customheaders::get();

        $infoAlert = trans('frontend.hint.settings_index') ? trans('frontend.hint.settings_index') : null;

        return view('admin.settings.index', compact('option_charset', 'customheaders', 'infoAlert'))->with('title', trans('frontend.title.settings_index'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $array = $request->all();
        $array['REQUIRE_SUB_CONFIRMATION'] = $request->input('REQUIRE_SUB_CONFIRMATION') ? 1 : 0;
        $array['SHOW_UNSUBSCRIBE_LINK'] = $request->input('SHOW_UNSUBSCRIBE_LINK') ? 1 : 0;
        $array['REQUEST_REPLY'] = $request->input('REQUEST_REPL') ? 1 : 0;
        $array['NEW_SUBSCRIBER_NOTIFY'] = $request->input('NEW_SUBSCRIBER_NOTIFY') ? 1 : 0;
        $array['RANDOM_SEND'] = $request->input('RANDOM_SEND') ? 1 : 0;
        $array['RENDOM_REPLACEMENT_SUBJECT'] = $request->input('RENDOM_REPLACEMENT_SUBJECT')  ? 1 : 0;
        $array['RANDOM_REPLACEMENT_BODY'] = $request->input('RANDOM_REPLACEMENT_BODY') ? 1 : 0;
        $array['ADD_DKIM'] = $request->input('ADD_DKIM') ? 1 : 0;
        $array['LIMIT_SEND'] = $request->input('LIMIT_SEND') ? 1 : 0;
        $array['REQUEST_REPLY']  = $request->input('REQUEST_REPLY') ? 1 : 0;
        $array['REMOVE_SUBSCRIBER'] = $request->input('REMOVE_SUBSCRIBER') ? 1 : 0;

        foreach ($array as $key => $value) {
           $this->setValue($key, $value);
        }

        if ($request->input('header_name')) {

            Customheaders::truncate();

            for ($i = 0; $i < count($request->header_name); $i++) {
                $name = $request->header_name;
                $value = $request->header_value;
                $name[$i] = trim($name[$i]);
                $value[$i] = trim($value[$i]);

                if (preg_match("/^[\-a-zA-Z]+$/", $name[$i])) {
                    $value[$i] = str_replace(';', '', $value[$i]);
                    $value[$i] = str_replace(':', '', $value[$i]);
                    if ($name[$i] && $value[$i]) {
                        $fields = [
                            'name' => $name[$i],
                            'value'  => $value[$i]
                        ];

                        Customheaders::create($fields);
                    }
                }
            }
        } else {
            Customheaders::truncate();
        }

        return redirect(URL::route('admin.settings.index'))->with('success', trans('message.data_updated'));

    }

    /**
     * @param $key
     * @param $value
     */
    private function setValue($key, $value)
    {
        $setting = Settings::where('name', $key)->first();

        if ($value === null) $value = '';
        if ($key == 'URL' && trim($value) == '')  $value = StringHelpers::getUrl();

        if ($setting) {
            $setting->value = $value;
            $setting->save();
        }
    }
}
