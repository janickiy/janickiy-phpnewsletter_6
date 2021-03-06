<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Category, Schedule, Templates, ScheduleCategory};
use Illuminate\Support\Facades\Validator;
use URL;

class ScheduleController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $schedule = Schedule::get();

        $infoAlert = trans('frontend.hint.schedule_index') ? trans('frontend.hint.schedule_index') : null;

        return view('admin.schedule.index', compact('schedule', 'infoAlert'))->with('title', trans('frontend.title.schedule_index'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $options = Templates::getOption();
        $category_options = Category::getOption();

        $infoAlert = trans('frontend.hint.schedule_create') ? trans('frontend.hint.schedule_create') : null;

        return view('admin.schedule.create_edit', compact('options', 'category_options', 'infoAlert'))->with('title', trans('frontend.title.schedule_index'));

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $rules = [
            'templateId' => 'required|numeric',
            'categoryId' => 'required|array',
            'value_from_start_date' => 'required|date|date_format:d.m.Y H:i',
            'value_from_end_date' => 'required|date|date_format:d.m.Y H:i',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $id = Schedule::create(array_merge($request->all(), ['value_from_start_date' => date("Y-m-d H:i:s", strtotime($request->value_from_start_date)), 'value_from_end_date' => date("Y-m-d H:i:s", strtotime($request->value_from_end_date))]))->id;

        if ($request->categoryId && $id) {
            foreach ($request->categoryId as $categoryId) {
                if (is_numeric($categoryId)) {
                    ScheduleCategory::create(['scheduleId' => $id, 'categoryId' => $categoryId]);
                }
            }
        }

        return redirect(URL::route('admin.schedule.index'))->with('success', trans('message.information_successfully_added'));

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) abort(404);

        $categoryId = [];

        foreach ($schedule->categories as $category) {
            $categoryId[] = $category->id;
        }

        $options = Templates::getOption();
        $category_options = Category::getOption();

        $infoAlert = trans('frontend.hint.schedule_edit') ? trans('frontend.hint.schedule_edit') : null;

        return view('admin.schedule.create_edit', compact('categoryId', 'options', 'category_options', 'schedule', 'infoAlert'))->with('title', trans('frontend.title.schedule_edit'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function update(Request $request)
    {
        $rules = [
            'templateId' => 'required|numeric',
            'categoryId' => 'required|array',
            'value_from_start_date' => 'required|date|date_format:d.m.Y H:i',
            'value_from_end_date' => 'required|date|date_format:d.m.Y H:i',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $schedule = Schedule::find($request->id);

        if (!$schedule) abort(404);

        $schedule->value_from_start_date = date("Y-m-d H:i:s", strtotime($request->value_from_start_date));
        $schedule->value_from_end_date = date("Y-m-d H:i:s", strtotime($request->value_from_end_date));
        $schedule->templateId = $request->templateId;
        $schedule->save();

        ScheduleCategory::where('scheduleId', $request->id)->delete();

        if ($request->categoryId) {
            foreach ($request->categoryId as $categoryId) {
                if (is_numeric($categoryId)) {
                    ScheduleCategory::create(['scheduleId' => $request->id, 'categoryId' => $categoryId]);
                }
            }
        }

        return redirect(URL::route('admin.schedule.index'))->with('success', trans('message.data_updated'));
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        Schedule::where('id', $id)->delete();
        ScheduleCategory::where('scheduleId', $id)->delete();
    }
}
