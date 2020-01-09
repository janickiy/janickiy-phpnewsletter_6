<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Attach, Logs, Process, ReadySent, Schedule, ScheduleCategory, Subscribers, Subscriptions, Templates};
use App\Helpers\{SendEmailHelpers, SettingsHelpers, StringHelpers, ResponseHelpers, LicenseHelpers};
use Illuminate\Support\Facades\Storage;
use Cookie;
use Artisan;
use ZipArchive;
use DateTime;

class AjaxController extends Controller
{
    public function action(Request $request)
    {
        if ($request->input('action')) {
            switch ($request->input('action')) {

                case 'start_update':

                    $update = new LicenseHelpers(app()->getLocale(), env('VERSION'));

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

                    $update = new LicenseHelpers(app()->getLocale(), env('VERSION'));

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
                        $result_send = ['result' => $result['result'] === true ? 'success' : 'error', 'msg' => $result['error'] ? trans('frontend.msg.email_wasnt_sent') : trans('frontend.msg.email_sent')];
                    } else {
                        $msg = implode(",", $errors);
                        $result_send = ['result' => 'errors', 'msg' => $msg];
                    }

                    $data['subscriberId'] = 0;
                    $data['email'] = $email;
                    $data['templateId'] = 0;
                    $data['template'] = $subject;
                    $data['success'] = 0;
                    $data['errorMsg'] = $result['result'] !== true ? $result['error'] : '';
                    $data['scheduleId'] = 0;
                    $data['logId'] = 0;

                    ReadySent::create($data);

                    return ResponseHelpers::jsonResponse(
                        $result_send
                    );

                    break;

                case 'send_out':

                    $fh = fopen(__FILE__, 'r');

                    if (!flock($fh, LOCK_EX | LOCK_NB)) {
                        exit('Script is already running');
                    }

                    if (!$request->templateId || !$request->categoryId) {
                        return ResponseHelpers::jsonResponse([
                            'result' => false,
                        ]);
                    }

                    $logId = $request->input('logId');

                    if ($logId == 0) {
                        return ResponseHelpers::jsonResponse([
                            'result' => false,
                        ]);
                    }

                    $this->updateProcess('start');

                    $mailcount = 0;

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

                    $templateId = [];

                    foreach ($request->templateId as $id) {
                        if (is_numeric($id)) {
                            $templateId[] = $id;
                        }
                    }

                    $templates = Templates::whereIN('id', $templateId)->get();

                    foreach ($templates as $template) {

                        if ($interval) {
                            $subscribers = Subscribers::select('subscribers.email', 'subscribers.token', 'subscribers.id', 'subscribers.name')
                                ->join('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriberId')
                                ->leftJoin('ready_sent', function ($join) use ($template, $logId) {
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
                            $subscribers = Subscribers::select('subscribers.email', 'subscribers.token', 'subscribers.id', 'subscribers.name')
                                ->join('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriberId')
                                ->leftJoin('ready_sent', function ($join) use ($template, $logId) {
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

                            if ($this->getProcess() == 'stop' || $this->getProcess() == 'pause') {
                                return ResponseHelpers::jsonResponse([
                                    'result' => true,
                                    'completed' => true,
                                ]);
                            }

                            if (SettingsHelpers::getSetting('sleep') > 0)
                                sleep(SettingsHelpers::getSetting('sleep'));

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

                                $mailcount++;

                                Subscribers::where('id', $subscriber->id)->update(['timeSent' => date('Y-m-d H:i:s')]);

                            } else {
                                $data['subscriberId'] = $subscriber->id;
                                $data['email'] = $subscriber->email;
                                $data['templateId'] = $template->id;
                                $data['template'] = $template->name;
                                $data['success'] = 0;
                                $data['errorMsg'] = $result['error'];
                                $data['scheduleId'] = 0;
                                $data['logId'] = $logId;
                            }

                            ReadySent::create($data);

                            unset($data);

                            if (SettingsHelpers::getSetting('LIMIT_SEND') == 1 && SettingsHelpers::getSetting('LIMIT_NUMBER') == $mailcount) {

                                $this->updateProcess('stop');

                                return ResponseHelpers::jsonResponse([
                                    'result' => true,
                                    'completed' => true,
                                ]);
                            }
                        }
                    }

                    if (SettingsHelpers::getSetting('LIMIT_SEND') == 1 && SettingsHelpers::getSetting('LIMIT_NUMBER') == $mailcount) {

                        $this->updateProcess('stop');

                        return ResponseHelpers::jsonResponse([
                            'result' => true,
                            'completed' => true,
                        ]);
                    }

                    $this->updateProcess('stop');

                    return ResponseHelpers::jsonResponse([
                        'result' => true,
                        'completed' => true,
                    ]);

                    break;

                case 'count_send':

                    if (!$request->logId || !$request->categoryId) {
                        return ResponseHelpers::jsonResponse([
                            'result' => false,
                        ]);
                    }

                    $categoryId = [];

                    foreach ($request->categoryId as $id) {
                        if (is_numeric($id)) {
                            $categoryId[] = $id;
                        }
                    }

                    $logId = $request->input('logId');

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

                    if ($interval) {
                        $total = Subscriptions::select('subscribers.id')
                            ->join('subscribers', 'subscriptions.subscriberId', '=', 'subscribers.id')
                            ->where('subscribers.active', 1)
                            ->whereIN('subscriptions.categoryId', $categoryId)
                            ->whereRaw($interval)
                            ->groupBy('subscribers.id')
                            ->limit($limit)
                            ->get()
                            ->count();
                    } else {
                        $total = Subscriptions::select('subscribers.id')
                            ->join('subscribers', 'subscriptions.subscriberId', '=', 'subscribers.id')
                            ->where('subscribers.active', 1)
                            ->whereIN('subscriptions.categoryId', $categoryId)
                            ->groupBy('subscribers.id')
                            ->limit($limit)
                            ->get()
                            ->count();
                    }

                    $success = ReadySent::where('logId', $logId)
                        ->where('success', 1)
                        ->count();

                    $unsuccess = ReadySent::where('logId', $logId)
                        ->where('success', 0)
                        ->count();

                    $sleep = SettingsHelpers::getSetting('sleep') == 0 ? 0.5 : SettingsHelpers::getSetting('sleep');
                    $timesec = intval(($total - ($success + $unsuccess)) * $sleep);

                    $datetime = new DateTime();
                    $datetime->setTime(0, 0, $timesec);

                    return ResponseHelpers::jsonResponse([
                        'result' => true,
                        'status' => 1,
                        'total' => $total,
                        'success' => $success,
                        'unsuccessful' => $unsuccess,
                        'time' => $datetime->format('H:i:s'),
                        'leftsend' => $total > 0 ? round(($success + $unsuccess) / $total * 100, 2) : 0,
                    ]);

                    break;

                case 'log_online':

                    $readySent = ReadySent::orderBy('id', 'desc')
                        ->where('logId', '>', 0)
                        ->limit(5)
                        ->get();

                    if ($readySent) {

                        $rows = [];

                        foreach ($readySent as $row) {
                            $rows[] = [
                                'subscriberId' => $row->subscriberId,
                                "email" => $row->email,
                                "status" => $row->success == 1 ? trans('frontend.str.sent') : trans('frontend.str.not_sent'),
                            ];
                        }

                        return ResponseHelpers::jsonResponse([
                            'result' => true,
                            'item' => $rows
                        ]);

                    } else {
                        return ResponseHelpers::jsonResponse([
                            'result' => false,
                        ]);
                    }

                    break;

                case 'start_mailing':

                    $log = Logs::create(['time' => date('Y-m-d H:i:s')]);
                    $logId = $log->id;

                    return ResponseHelpers::jsonResponse([
                        'result' => true,
                        'logId' => $logId
                    ]);

                    break;

                case 'process':

                    if ($request->command) {

                        $this->updateProcess($request->command);

                        return ResponseHelpers::jsonResponse([
                            'result' => true,
                            'command' => $request->command
                        ]);

                    } else {
                        return ResponseHelpers::jsonResponse([
                            'result' => false,
                        ]);
                    }

                    break;

            }
        }
    }

    /**
     * @return string
     */
    private function getProcess()
    {
        $process = Process::where('userId', \Auth::user('web')->id)->first();

        if (isset($process->command)) {
            return $process->command;
        } else {
            $process = new Process();
            $process->command = 'start';
            $process->userId = \Auth::user('web')->id;
            $process->save();

            return 'start';
        }
    }

    /**
     * @param $command
     */
    private function updateProcess($command)
    {
        $result = Process::where('userId', \Auth::user('web')->id);

        if ($result->first()) {
            $result->update(['command' => $command]);
        } else {
            $process = new Process();
            $process->command = $command;
            $process->userId = \Auth::user('web')->id;
            $process->save();
        }
    }
}
