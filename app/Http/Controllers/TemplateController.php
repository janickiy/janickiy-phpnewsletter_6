<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelpers;
use Illuminate\Http\Request;
use App\Models\{Templates, Attach};
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use URL;

class TemplateController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $infoAlert = trans('frontend.hint.template_index') ? trans('frontend.hint.template_index') : null;

        return view('admin.template.index', compact('infoAlert'))->with('title', trans('frontend.title.template_index'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.template.create_edit')->with('title', trans('frontend.title.template_create'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'body' => 'required',
            'prior' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {

            $id = Templates::create($request->all())->id;
            $attachFile = $request->file('attachfile');

            if (isset($attachFile)) {
                foreach ($attachFile as $file) {
                    $filename = StringHelpers::randomText(10) . '.' . $file->getClientOriginalExtension();

                    Storage::putFileAs(Attach::DIRECTORY, $file, $filename);

                    if (Storage::putFileAs(Attach::DIRECTORY, $file, $filename)) {
                        $attach = [
                            'name' => $file->getClientOriginalName(),
                            'file_name' => $filename,
                            'templateId' => $id,
                        ];

                        Attach::create($attach);
                    }
                }
            }

            return redirect(URL::route('admin.template.index'))->with('success', trans('message.information_successfully_added'));
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $template = Templates::where('id', $id)->first();

        if (!$template) abort(404);

        $attachment = $template->attach;

        return view('admin.template.create_edit', compact('template','attachment'))->with('title', trans('frontend.title.template_edit'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        if (!is_numeric($request->id)) abort(500);

        $rules = [
            'name' => 'required|max:255',
            'body' => 'required',
            'prior' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {

            $attachFile = $request->file('attachfile');

            if (isset($attachFile)) {
                foreach ($attachFile as $file) {
                    $filename = StringHelpers::randomText(10) . '.' . $file->getClientOriginalExtension();

                    Storage::putFileAs(Attach::DIRECTORY, $file, $filename);

                    if (Storage::putFileAs(Attach::DIRECTORY, $file, $filename)) {
                        $attach = [
                            'name' => $file->getClientOriginalName(),
                            'file_name' => $filename,
                            'templateId' => $request->id,
                        ];

                        Attach::create($attach);
                    }
                }
            }

            $data['name'] = $request->input('name');
            $data['body'] = $request->input('body');
            $data['prior'] = $request->input('prior');

            Templates::where('id',$request->id)->update($data);

            return redirect(URL::route('admin.template.index'))->with('success', trans('message.data_updated'));
        }
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        $q = Templates::where('id', $id);

        if ($q->exists()) {
            foreach ($q->first()->attach as $a) {
                if (isset($a->id) && $a->id) Attach::Remove($a->id);
            }

            $q->delete();
        }
    }
}
