<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Attach, Logs, ReadySent, Schedule, ScheduleCategory, Subscribers, Subscriptions, Templates};
use App\Helpers\{SendEmailHelpers, SettingsHelpers, StringHelpers, ResponseHelpers, UpdateHelpers};
use Illuminate\Support\Facades\Storage;
use Cookie;
use Artisan;
use ZipArchive;

class AjaxController extends Controller
{
    public function action(Request $request)
    {
        if ($request->input('action')) {
            switch ($request->input('action')) {

                case 'start_update':

                    $update = new UpdateHelpers(app()->getLocale(), env('VERSION'));

                    if ($request->p == 'start') {

                        if ($update->getUpdateLink() && Storage::disk('public')->put('update.zip', file_get_contents($update->getUpdateLink()))) {
                            $content['status'] = 'download_completed';
                            $content['result'] = true;
                        } else {
                            $content['status'] = 'failed_to_update';
                            $content['result'] = false;
                        }
                    }

                    if ($request->p == 'update_files') {

                        $zip = new ZipArchive();

                        if (Storage::disk('public')->exists('update.zip') && $zip->open(Storage::disk('public')->path('update.zip')) === TRUE) {
                            if (is_writeable(base_path())) {
                                $zip->extractTo(base_path());
                                $zip->close();
                                $content['status'] = trans('frontend.msg.files_unzipped_successfully');
                                $content['result'] = true;
                            } else {
                                $content['status'] = trans('frontend.msg.directory_not_writeable');
                                $content['result'] = false;
                            }
                        } else {
                            $content['status'] = trans('frontend.msg.cannot_read_zip_archive');
                            $content['result'] = false;
                        }
                    }

                    if ($request->p == 'update_bd') {
                        Artisan::call('migrate', ['--force' => true]);
                        $content['status'] = trans('frontend.msg.update_completed');
                    }

                    if ($request->p == 'clear_cache') {
                        Artisan::call('cache:clear');
                        Artisan::call('route:cache');
                        Artisan::call('route:clear');
                        Artisan::call('view:clear');
                        $content['status'] = trans('frontend.msg.cache_cleared');
                    }

                    return ResponseHelpers::jsonResponse([
                        $content
                    ]);

                    break;

                case 'alert_update':

                    $update = new UpdateHelpers(app()->getLocale(), env('VERSION'));

                    if ($update->checkNewVersion()) {
                        $update_warning = str_replace('%SCRIPTNAME%', trans('frontend.str.script_name'), trans('frontend.str.update_warning'));
                        $update_warning = str_replace('%VERSION%', $update->getVersion(), $update_warning);
                        $update_warning = str_replace('%CREATED%', $update->getCreated(), $update_warning);
                        $update_warning = str_replace('%DOWNLOADLINK%', $update->getDownloadLink(), $update_warning);
                        $update_warning = str_replace('%MESSAGE%', $update->getMessage(), $update_warning);

                        return ResponseHelpers::jsonResponse([
                            ["msg" => $update_warning]
                        ]);
                    }

                    break;

                case 'remove_schedule':

                    Schedule::where('id', $request->input('id'))->delete();
                    ScheduleCategory::where('scheduleId', $request->input('id'))->delete();

                    return ResponseHelpers::jsonResponse([
                        'result' => true, 'id' => $request->input('id')
                    ]);

                    break;

                case 'change_lng':

                    if ($request->input('locale')) {
                        if (in_array($request->input('locale'), \Config::get('app.locales'))) {
                            Cookie::queue(
                                Cookie::forever('lang', $request->input('locale')));
                        }
                    }

                    return ResponseHelpers::jsonResponse([
                        'result' => true
                    ]);

                    break;

                case 'remove_attach':

                    $result = $request->id ? Attach::Remove($request->id) : false;

                    return ResponseHelpers::jsonResponse([
                        'result' => $result
                    ]);

                    break;

                case 'send_test_email':

                    $subject = $request->input('name');
                    $body = $request->input('body');
                    $prior = $request->input('prior');
                    $email = $request->input('email');

                    $errors = [];

                    if (empty($subject)) $errors[] = trans('error.empty_subject');
                    if (empty($body)) $errors[] = trans('error.empty_content');
                    if (empty($email)) $errors[] = trans('error.empty_email');
                    if (!empty($email) && StringHelpers::isEmail($email) === false) $errors[] = trans('error.empty_email');

                    if (count($errors) == 0) {
                        SendEmailHelpers::setBody($body);
                        SendEmailHelpers::setSubject($subject);
                        SendEmailHelpers::setPrior($prior);
                        SendEmailHelpers::setEmail($email);
                        SendEmailHelpers::setToken(md5($email));
                        $result = SendEmailHelpers::sendEmail();
                        $result_send = ['result' => $result['result'] === true ? 'success' : 'error', 'msg' => $result['error'] ? trans('msg.email_wasnt_sent') : trans('msg.email_sent')];

                    } else {
                        $msg = implode(",", $errors);
                        $result_send = ['result' => 'errors', 'msg' => $msg];
                    }

                    return ResponseHelpers::jsonResponse([
                        $result_send
                    ]);

                    break;

                case 'send_out':

                    $fh = fopen(__FILE__, 'r');

                    if (! flock($fh, LOCK_EX | LOCK_NB)) {
                        exit('Script is already running');
                    }

                    if (!$request->input('categoryId')) {
                        return ResponseHelpers::jsonResponse([
                            'result' => false,
                        ]);
                    }

                    if (!$request->input('templateId')) {
                        return ResponseHelpers::jsonResponse([
                            'result' => false,
                        ]);
                    }

                    $logId = $request->input('logId');

                    if ($logId == 0) {
                        $log = Logs::create(['time' => date('Y-m-d H:i:s')]);
                        $logId = $log->id;
                    }

                    $order = SettingsHelpers::getSetting('RANDOM_SEND') == 1 ? 'ORDER BY RAND()' : 'subscribers.id';
                    $limit = SettingsHelpers::getSetting('LIMIT_SEND') == 1 ? "LIMIT " . SettingsHelpers::getSetting('LIMIT_NUMBER') : null;

                    switch (SettingsHelpers::getSetting('INTERVAL_TYPE')) {
                        case "minute":
                            $interval = "(subscribers.timeSent < NOW() - INTERVAL '" . SettingsHelpers::getSetting('INTERVAL_NUMBER') . "' MINUTE)";
                            break;
                        case "hour":
                            $interval = "(subscribers.timeSent < NOW() - INTERVAL '" . SettingsHelpers::getSetting('INTERVAL_NUMBER') . "' HOUR)";
                            break;
                        case "day":
                            $interval = "(subscribers.timeSent < NOW() - INTERVAL '" . SettingsHelpers::getSetting('INTERVAL_NUMBER') . "' DAY)";
                            break;
                        default:
                            $interval = null;
                    }

                    $categoryId = [];

                    foreach ($request->categoryId as $id) {
                        if (is_numeric($id)) {
                            $categoryId[] = $id;
                        }
                    }

                    $templateId = $request->input('templateId');

                    $template = Templates::where('id', $templateId)->first();

                    if ($interval) {
                        $subscribers = Subscribers::select('subscribers.email','subscribers.token','subscribers.id','subscribers.name')
                            ->join('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriberId')
                            ->leftJoin('ready_sent', function ($join) use ($template,$logId) {
                                $join->on('subscribers.id', '=', 'ready_sent.subscriberId')
                                    ->where('ready_sent.templateId', '=', $template->id)
                                    ->where('ready_sent.logId', '=', $logId)
                                    ->where(function ($query) {
                                        $query->where('ready_sent.success', '=', 1)
                                            ->orWhere('ready_sent.success', '=', 0);
                                    });
                            })
                            ->whereIN('subscriptions.categoryId', $categoryId)
                            ->where('subscribers.active', 1)
                            ->whereRaw($interval)
                            ->groupBy('subscribers.id')
                            ->groupBy('subscribers.email')
                            ->groupBy('subscribers.token')
                            ->groupBy('subscribers.name')
                            ->orderByRaw($order)
                            ->limit($limit)
                            ->get();
                    } else {
                        $subscribers = Subscribers::select('subscribers.email','subscribers.token','subscribers.id','subscribers.name')
                            ->join('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriberId')
                            ->leftJoin('ready_sent', function ($join) use ($template,$logId) {
                                $join->on('subscribers.id', '=', 'ready_sent.subscriberId')
                                    ->where('ready_sent.templateId', '=', $template->id)
                                    ->where('ready_sent.logId', '=', $logId)
                                    ->where(function ($query) {
                                        $query->where('ready_sent.success', '=', 1)
                                            ->orWhere('ready_sent.success', '=', 0);
                                    });
                            })
                            ->whereIN('subscriptions.categoryId', $categoryId)
                            ->where('subscribers.active', 1)
                            ->groupBy('subscribers.id')
                            ->groupBy('subscribers.email')
                            ->groupBy('subscribers.token')
                            ->groupBy('subscribers.name')
                            ->orderByRaw($order)
                            ->limit($limit)
                            ->get();
                    }

                    foreach ($subscribers as $subscriber) {
                        SendEmailHelpers::setBody($template->body);
                        SendEmailHelpers::setSubject($template->name);
                        SendEmailHelpers::setPrior($template->prior);
                        SendEmailHelpers::setEmail($subscriber->email);
                        SendEmailHelpers::setToken($subscriber->token);
                        SendEmailHelpers::setSubscriberId($subscriber->id);
                        SendEmailHelpers::setName($subscriber->name);

                        $result = SendEmailHelpers::sendEmail($template->id);

                        $data = [];

                        if ($result['result'] === true) {
                            $data['subscriberId'] = $subscriber->id;
                            $data['email'] = $subscriber->email;
                            $data['templateId'] = $template->id;
                            $data['template'] = $template->name;
                            $data['success'] = 1;
                            $data['scheduleId'] = 0;
                            $data['logId'] = $logId;


                            Subscribers::where('id', $subscriber->id)->update(['timeSent' => date('Y-m-d H:i:s')]);

                        } else {
                            $data['subscriberId'] = $subscriber->id;
                            $data['email'] = $subscriber->email;
                            $data['templateId'] = $template->templateId;
                            $data['template'] = $template->name;
                            $data['success'] = 0;
                            $data['errorMsg'] = $result['error'];
                            $data['scheduleId'] = 0;
                            $data['logId'] = $logId;
                        }

                        ReadySent::create($data);

                        unset($data);
                    }

                    return ResponseHelpers::jsonResponse([
                        'result' => true,
                        'completed' => true,
                    ]);

                    break;

                case 'count_send':

                    if ($request->input('logId') && $request->input('categoryId')) {

                        $categoryId = [];

                        foreach ($request->input('categoryId') as $id) {
                            if (is_numeric($id)) {
                                $categoryId[] = $id;
                            }
                        }

                        $total = Subscriptions::join('subscribers','subscriptions.subscriberId','=','subscribers.id')
                            ->where('subscribers.active',1)
                            ->whereIN('subscriptions.categoryId', $categoryId)
                            ->count();

                        $success = ReadySent::where('logId', $request->input('logId'))
                            ->where('success',1)
                            ->count();

                        $unsuccess = ReadySent::where('logId', $request->input('logId'))
                            ->where('success',0)
                            ->count();

                        $sleep = SettingsHelpers::getSetting('sleep') == 0 ? 0.5 : SettingsHelpers::getSetting('sleep');
                        $timesec = intval(($total - ($success + $unsuccess)) * $sleep);

                        $datetime = new DateTime();
                        $datetime->setTime(0, 0, $timesec);

                        return ResponseHelpers::jsonResponse([
                            'result'  => true,
                            'status'  => 1,
                            'total'   => $total,
                            'success' => $success,
                            'unsuccessful' => $unsuccess,
                            'time'     => $datetime->format('H:i:s'),
                            'leftsend' => round(($success + $unsuccess) / $total * 100, 2),
                        ]);

                    } else {
                        return ResponseHelpers::jsonResponse([
                            'result'  => false,
                        ]);
                    }

                    break;

                case 'log_online':

                    $readySent = ReadySent::orderBy('id','desc')
                        ->where('logId', '>', 0)
                        ->limit(10)
                        ->get();

                    if ($readySent) {

                        $rows = [];

                        foreach($readySent as $row) {
                            $rows[] = [
                                'subscriberId' => $row->subscriberId,
                                "email"   => $row->email,
                                "status"  => $row->success,
                               ];
                        }

                        return ResponseHelpers::jsonResponse([
                            'result' => true,
                            'item' => $rows
                        ]);

                    } else {
                        return ResponseHelpers::jsonResponse([
                            'result'  => false,
                        ]);
                    }

                    break;

            }
        }
    }
}
