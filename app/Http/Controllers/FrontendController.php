<?php

namespace App\Http\Controllers;

use App\Helpers\{SendEmailHelpers, StringHelpers, SettingsHelpers};
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
        $subscriber = Subscribers::find($subscriber);

        if (!$subscriber) abort(404);
        if ($subscriber->token != $token) abort(400);

        $email = $subscriber->email;

        $subscriber->active = 0;
        $subscriber->save();

        $msg = str_replace('%EMAIL%', $email, trans('frontend.str.address_has_been_deleted'));

        return view('frontend.unsubscribe', compact('msg'));
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

        return view('frontend.subform', compact('category'))->with('title', 'Subform');
    }

    /**
     * @param Request $request
     * @return false|string
     * @throws \PHPMailer\PHPMailer\Exception
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

        $token = StringHelpers::token();

        $id = Subscribers::create(array_merge($request->all(), ['active' => SettingsHelpers::getSetting('REQUIRE_SUB_CONFIRMATION') == 1 ? 0 : 1, 'token' => $token]))->id;

        if ($id) {
            if (SettingsHelpers::getSetting('REQUIRE_SUB_CONFIRMATION') == 1) {
                SendEmailHelpers::setSubject(SettingsHelpers::getSetting('SUBJECT_TEXT_CONFIRM'));

                $CONFIRM = SettingsHelpers::getSetting('URL') . "subscribe/" . $id . "/" . $token;
                $msg = str_replace(array("\r\n", "\r", "\n"), '<br>', SettingsHelpers::getSetting('TEXT_CONFIRMATION'));
                $msg = str_replace('%CONFIRM%', $CONFIRM, $msg);

                SendEmailHelpers::setBody($msg);
                SendEmailHelpers::setEmail($request->email);
                SendEmailHelpers::setToken($token);
                SendEmailHelpers::setSubscriberId($id);
                SendEmailHelpers::setName($request->name);
                SendEmailHelpers::setUnsub(false);
                SendEmailHelpers::setTracking(false);
                SendEmailHelpers::sendEmail();
            }

            if (SettingsHelpers::getSetting('NEW_SUBSCRIBER_NOTIFY') == 1) {
                $subject = trans('frontend.str.notification_newuser');
                $subject = str_replace('%SITE%', $_SERVER['SERVER_NAME'], $subject);
                $msg = trans('frontend.str.notification_newuser') . "\nName: " . $request->name . " \nE-mail: " . $request->email . "\n";
                $msg = str_replace('%SITE%', $_SERVER['SERVER_NAME'], $msg);

                SendEmailHelpers::setSubject($subject);
                SendEmailHelpers::setBody($msg);
                SendEmailHelpers::setEmail(SettingsHelpers::getSetting('EMAIL'));
                SendEmailHelpers::setName(SettingsHelpers::getSetting('FROM'));
                SendEmailHelpers::setTracking(false);
                SendEmailHelpers::setUnsub(false);
                SendEmailHelpers::sendEmail();
            }

            if ($request->categoryId) {
                 foreach ($request->categoryId as $categoryId) {
                     if (is_numeric($categoryId)) {
                         Subscriptions::create(['subscriberId' => $id, 'categoryId' => $categoryId]);
                     }
                 }
            }
        }

        return json_encode([
            'result' => 'success',
            'msg' => trans('frontend.msg.subscription_is_formed')
        ]);
    }

	 /**
     * @return false|string
     */
    public function getCategories()
    {
        $category = Category::orderBy('name', 'desc')->get();

        return json_encode(['items' => $category]);
    }
}
