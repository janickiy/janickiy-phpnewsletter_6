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

        return view('admin.schedule.index', compact('schedule'))->with('title', trans('frontend.title.schedule_index'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $options = [];

        foreach (Templates::get() as $row) {
            $options[$row->id] = $row->name;
        }

        $category_options = [];

        foreach (Category::get() as $row) {
            $category_options[$row->id] = $row->name;
        }

        return view('admin.schedule.create_edit', compact('options', 'category_options'))->with('title', trans('frontend.title.schedule_index'));

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
        } else {
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
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $schedule = Schedule::where('id', $id)->first();

        if (!$schedule) abort(404);

        $categoryId = [];

        foreach ($schedule->categories as $category) {
            $categoryId[] =  $category->id;
        }

        $options = [];

        foreach (Templates::get() as $row) {
            $options[$row->id] = $row->name;
        }

        $category_options = [];

        foreach (Category::get() as $row) {
            $category_options[$row->id] = $row->name;
        }

        return view('admin.schedule.create_edit', compact('categoryId', 'options', 'category_options', 'schedule'))->with('title', trans('frontend.title.schedule_edit'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function update(Request $request)
    {
        if (!is_numeric($request->id)) return abort(500);

        $rules = [
            'templateId' => 'required|numeric',
            'categoryId' => 'required|array',
            'value_from_start_date' => 'required|date|date_format:d.m.Y H:i',
            'value_from_end_date' => 'required|date|date_format:d.m.Y H:i',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            $data['value_from_start_date'] = date("Y-m-d H:i:s", strtotime($request->value_from_start_date));
            $data['value_from_end_date'] = date("Y-m-d H:i:s", strtotime($request->value_from_end_date));
            $date['templateId'] = $request->templateId;

            Schedule::where('id', $request->id)->update($data);
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
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        Schedule::where('id', $id)->delete();
        ScheduleCategory::where('scheduleId',$id)->delete();
    }
}
