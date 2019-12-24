<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Attach, Schedule, ScheduleCategory};
use App\Helpers\{SendEmailHelpers,StringHelpers,ResponseHelpers, UpdateHelpers};
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

                        if ($update->getUpdateLink() && Storage::disk('public')->put('update.zip', file_get_contents($update->getUpdateLink()))){
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

                    Schedule::where('id',$request->input('id'))->delete();
                    ScheduleCategory::where('scheduleId',$request->input('id'))->delete();

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

                    $subject = $request->name;
                    $body = $request->body;
                    $prior = $request->prior;
                    $email = $request->email;

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
                        $result_send = ['result' => $result['result'] === true ? 'success':'error', 'msg' => $result['error'] ? trans('msg.email_wasnt_sent') : trans('msg.email_sent') ];

                    } else {
                        $msg = implode(",", $errors);
                        $result_send = ['result' => 'errors', 'msg' => $msg];
                    }

                    return ResponseHelpers::jsonResponse([
                        $result_send
                    ]);

                    break;

            }
        }
    }
}
