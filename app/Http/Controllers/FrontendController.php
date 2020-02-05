<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{ReadySent, Redirect, Subscribers, Category, Subscriptions};
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
        $subscribers = Subscribers::where('id', $subscriber);
        $result = $subscribers->first();

        if ($result) {
            if ($result->token != $token) abort(400);

            $subscribers->update(['active' => 0]);
        }

        return view('frontend.unsubscribe')->with('title', trans('frontend.title.unsubscribe'));

    }

    /**
     * @param $subscriber
     * @param $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subscribe($subscriber, $token)
    {
        $subscribers = Subscribers::where('id', $subscriber);
        $result = $subscribers->first();

        if (!$result) abort(404);

        if ($result->token != $token) abort(400);

        $subscribers->update(['active' => 1]);

        return view('frontend.subscribe')->with('title', trans('frontend.title.subscribe'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form()
    {
        $category = Category::get();

        return view('frontend.subform', compact('category'))->with('title', trans('frontend.title.subform'));
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
}
