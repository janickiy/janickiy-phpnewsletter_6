<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{ReadySent, Redirect, Schedule, Subscribers, Category, Subscriptions};
use ImageCreateTrueColor;

class FrontendController extends Controller
{
    /**
     * @param $subscriber
     * @param $template
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function pic($subscriber, $template)
    {
        ReadySent::where('templateId', $template)->where('subscriberId', $subscriber)->update(['readmail' => 1]);

        $im = imagecreatetruecolor(1, 1);

        imagefilledrectangle($im, 0, 0, 99, 99, 0xFFFFFF);
        header('Content-Type: image/gif');

        imagegif($im);
        imagedestroy($im);
        exit;
    }

    /**
     * @param $ref
     * @param $subscriber
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectLog($ref, $subscriber)
    {
        if (!$ref) abort(404);

        $url = isset($ref) ? base64_decode($ref) : '';

        $subscribers = Subscribers::find($subscriber);

        $data['url'] = $url;
        $data['time'] = date("Y-m-d H:i:s");
        $data['email'] = isset($subscribers->email) ? $subscribers->email : 'test';

        Redirect::create($data);

        return redirect($url);
    }

    /**
     * @param $subscriber
     * @param $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function unsubscribe($subscriber, $token)
    {
        $subscriber = Subscribers::find($subscriber);

        if (!$subscriber) abort(404);
        if ($subscriber->token != $token) abort(400);

        $subscriber->active = 0;
        $subscriber->save();

        return view('frontend.unsubscribe');
    }

    /**
     * @param $subscriber
     * @param $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subscribe($subscriber, $token)
    {
        $subscriber = Subscribers::find($subscriber);

        if (!$subscriber) abort(404);
        if ($subscriber->token != $token) abort(400);

        $subscriber->active = 1;
        $subscriber->save();

        return view('frontend.subscribe');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form()
    {
        $category = Category::get();

        return view('frontend.subform', compact('category'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addSub(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:subscribers|max:255',
            'categoryId' => 'array|nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return json_encode([
                'result' => 'errors',
                'msg' => $validator->messages()]);
        }

        $id = Subscribers::create(array_merge($request->all(), ['active' => 1, 'token' => StringHelpers::token()]))->id;

        if ($request->categoryId && $id) {
            foreach ($request->categoryId as $categoryId) {
                if (is_numeric($categoryId)) {
                    Subscriptions::create(['subscriberId' => $id, 'categoryId' => $categoryId]);
                }
            }
        }

        return json_encode([
            'result' => 'success',
            'msg' => trans('frontend.msg.subscription_is_formed')
        ]);
    }

    public function test()
    {
        $schedule = Schedule::where('value_from_start_date' , '<=' , Carbon::now()->toDateTimeString())
            ->where('value_from_end_date', '>=', Carbon::now()->toDateTimeString())
            ->get();

        foreach ($schedule as $row) {
            var_dump($row);
            exit;
        }



    }
}
