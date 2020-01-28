<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use URL;
use Hash;

class UsersController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $infoAlert = trans('frontend.hint.users_index') ? trans('frontend.hint.users_index') : null;

        return view('admin.users.index', compact('infoAlert'))->with('title', trans('frontend.title.users_index'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $options = [
            'admin' => trans('frontend.str.admin'),
            'moderator' => trans('frontend.str.moderator'),
            'editor' => trans('frontend.str.editor'),
        ];

        $infoAlert = trans('frontend.hint.users_create') ? trans('frontend.hint.users_create') : null;

        return view('admin.users.create_edit', compact('options', 'infoAlert'))->with('title', trans('frontend.title.users_create'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $rules = [
            'login' => 'required|unique:users|min:3|max:255',
            'name' => 'required',
            'role' => 'required',
            'password' => 'required|min:6',
            'password_again' => 'required|min:6|same:password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {

            User::create(array_merge($request->all(), ['password' => Hash::make($request->password)]));

            return redirect(URL::route('admin.users.index'))->with('success', trans('message.information_successfully_added'));
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) abort(404);

        $options = [
            'admin' => trans('frontend.str.admin'),
            'moderator' => trans('frontend.str.moderator'),
            'editor' => trans('frontend.str.editor'),
        ];

        $infoAlert = trans('frontend.hint.users_edit') ? trans('frontend.hint.users_edit') : null;

        return view('admin.users.create_edit', compact('user', 'options', 'infoAlert'))->with('title', trans('frontend.title.users_edit'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        if (!is_numeric($request->id)) abort(500);

        $rules = [
            'login' => 'required|max:255|unique:users,login,' . $request->id,
            'name' => 'required',
            'password' => 'min:6|nullable',
            'password_again' => 'min:6|same:password|nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            $data['login'] = $request->input('login');
            $data['name'] = $request->input('name');
            if (!empty($request->role)) $data['role'] = $request->input('role');

            if (!empty($request->password)) {
                $data['password'] = Hash::make($request->password);
            }

            User::where('id', $request->id)->update($data);

            return redirect(URL::route('admin.users.index'))->with('success', trans('message.data_updated'));
        }
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        if ($id != Auth::id()) User::where('id', $id)->delete();
    }
}
