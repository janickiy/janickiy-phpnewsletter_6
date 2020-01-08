<?php

namespace App\Http\Controllers;

class FaqController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $infoAlert = trans('frontend.hint.faq_index') ? trans('frontend.hint.faq_index') : null;

        return view('admin.faq.index', compact('infoAlert'))->with('title', 'FAQ');
    }
}
