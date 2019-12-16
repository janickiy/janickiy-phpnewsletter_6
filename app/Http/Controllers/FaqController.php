<?php

namespace App\Http\Controllers;

class FaqController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.faq.index')->with('title', 'FAQ');
    }
}
