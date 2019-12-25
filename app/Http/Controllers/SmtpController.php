<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Smtp};
use Illuminate\Support\Facades\Validator;
use URL;

class SmtpController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $infoAlert = trans('frontend.hint.smtp_index') ? trans('frontend.hint.smtp_index') : null;

        return view('admin.smtp.index', compact('infoAlert'))->with('title', trans('frontend.title.smtp_index'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $infoAlert = trans('frontend.hint.smtp_create') ? trans('frontend.hint.smtp_create') : null;

        return view('admin.smtp.create_edit', compact('infoAlert'))->with('title', trans('frontend.title.smtp_create'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $rules = [
            'host' => 'required|max:255',
            'username' => 'required',
            'email' => 'required|email',
            'port' => 'required|numeric',
            'timeout' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {

            Smtp::create($request->all());

            return redirect(URL::route('admin.smtp.index'))->with('success', trans('message.information_successfully_added'));
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $smtp = Smtp::where('id', $id)->first();

        if (!$smtp) abort(404);

        $infoAlert = trans('frontend.hint.smtp_edit') ? trans('frontend.hint.smtp_edit') : null;

        return view('admin.smtp.create_edit', compact('smtp','infoAlert'))->with('title', trans('frontend.title.smtp_edit'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $rules = [
            'host' => 'required|max:255',
            'username' => 'required',
            'email' => 'required|email',
            'port' => 'required|numeric',
            'timeout' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            $data['host'] = $request->input('host');
            $data['email'] = $request->input('email');
            $data['username'] = $request->input('username');
            $data['password'] = $request->input('password');
            $data['port'] = $request->input('port');
            $data['authentication'] = $request->input('authentication');
            $data['secure'] = $request->input('secure');
            $data['timeout'] = $request->input('timeout');

            Smtp::where('id', $request->id)->update($data);

            return redirect(URL::route('admin.smtp.index'))->with('success', trans('message.data_updated'));
        }
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        Smtp::where('id', $id)->delete();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function status(Request $request)
    {
        $temp = [];

        foreach ($request->activate as $id) {
            if (is_numeric($id)) {
                $temp[] = $id;
            }
        }

        switch ($request->action) {
            case  0 :
            case  1 :

                Smtp::whereIN('id', $temp)->update(['active' => $request->action]);

                break;

            case 2 :

                Smtp::whereIN('id', $temp)->delete();

                break;
        }

        return redirect(URL::route('admin.smtp.index'))->with('success', trans('message.actions_completed'));

    }
}
