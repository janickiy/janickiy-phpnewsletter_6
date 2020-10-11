<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Category, ScheduleCategory, Subscriptions};
use Illuminate\Support\Facades\Validator;
use URL;

class CategoryController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $infoAlert = trans('frontend.hint.category_index') ? trans('frontend.hint.category_index') : null;

        return view('admin.category.index', compact('infoAlert'))->with('title', trans('frontend.title.category_index'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $infoAlert = trans('frontend.hint.category_create') ? trans('frontend.hint.category_create') : null;

        return view('admin.category.create_edit', compact('infoAlert'))->with('title', trans('frontend.title.category_create'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:category|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Category::create($request->all());

        return redirect(URL::route('admin.category.index'))->with('success', trans('message.information_successfully_added'));

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $category = Category::where('id', $id)->first();

        if (!$category) abort(404);

        $infoAlert = trans('frontend.hint.category_create') ? trans('frontend.hint.category_create') : null;

        return view('admin.category.create_edit', compact('category', 'infoAlert'))->with('title', trans('frontend.title.category_edit'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|max:25|unique:category,name,' . $request->id,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category = Category::find($request->id);

        if (!$category) abort(404);

        $category->name = $request->input('name');
        $category->save();

        return redirect(URL::route('admin.category.index'))->with('success', trans('message.data_updated'));
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        Subscriptions::where('categoryId', $id)->delete();
        ScheduleCategory::where('categoryId', $id)->delete();
        Category::where('id', $id)->delete();
    }
}
