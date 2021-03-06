<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Smtp};
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer;
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
     * @throws PHPMailer\Exception
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

        if ($this->checkConnection($request->host, $request->email, $request->username, $request->password, $request->port, $request->authentication, $request->secure, $request->timeout) === false) {
            $validator->after(function ($validator) {
                $validator->errors()->add('connection', trans('message.unable_connect_to_smtp'));
            });
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Smtp::create($request->all());

        return redirect(URL::route('admin.smtp.index'))->with('success', trans('message.information_successfully_added'));
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

        return view('admin.smtp.create_edit', compact('smtp', 'infoAlert'))->with('title', trans('frontend.title.smtp_edit'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws PHPMailer\Exception
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

        if ($this->checkConnection($request->host, $request->email, $request->username, $request->password, $request->port, $request->authentication, $request->secure, $request->timeout) === false) {
            $validator->after(function ($validator) {
                $validator->errors()->add('connection', trans('message.unable_connect_to_smtp'));
            });
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $smtp = Smtp::find($request->id);

        if (!$smtp) abort(404);

        $smtp->host = $request->input('host');
        $smtp->email = $request->input('email');
        $smtp->username = $request->input('username');
        $smtp->password = $request->input('password');
        $smtp->port = $request->input('port');
        $smtp->authentication = $request->input('authentication');
        $smtp->secure = $request->input('secure');
        $smtp->timeout = $request->input('timeout');
        $smtp->save();

        return redirect(URL::route('admin.smtp.index'))->with('success', trans('message.data_updated'));
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

    /**
     * @param $host
     * @param $email
     * @param $username
     * @param $password
     * @param $port
     * @param $authentication
     * @param $secure
     * @param int $timeout
     * @return bool
     * @throws PHPMailer\Exception
     */
    public function checkConnection($host, $email, $username, $password, $port, $authentication, $secure, $timeout = 5)
    {
        $m = new PHPMailer\PHPMailer();
        $m->isSMTP();
        $m->Host = $host;
        $m->Port = $port;

        if ($password) $m->SMTPAuth = true;
        else
            $m->SMTPAuth = false;

        $m->SMTPKeepAlive = true;
        $m->SMTPSecure = $secure;
        $m->AuthType = $authentication;
        $m->Username = $username;
        $m->Password = $password;
        $m->Timeout = $timeout;
        $m->From = $email;
        $m->FromName = $email;

        if ($m->smtpConnect()) {
            $m->smtpClose();
            return true;
        } else {
            return false;
        }
    }
}
