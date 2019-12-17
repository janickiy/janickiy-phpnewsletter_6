<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Attach, Schedule, ScheduleCategory};
use App\Helpers\{SendEmailHelpers,StringHelpers,ResponseHelpers, UpdateHelpers};

class AjaxController extends Controller
{
    public function action(Request $request)
    {
        if ($request->input('action')) {
            switch ($request->input('action')) {

                case 'alert_update':

                    $update = new UpdateHelpers(config('app.locales'), env('VERSION'));

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

                case 'daemonstat':

                    $process = Robots::select('robots.id as id','process.status as status')->join('process','robots.id','=','process.robotId')->get();

                    return ResponseHelpers::jsonResponse(
                        $process
                    );

                    break;

                case 'process':

                    $result = Process::where('robotId',$request->input('id'));

                    if ($result->first()) {
                        $result->status = $request->input('status');
                        $result->save();
                    } else {
                        Process::create(['robotId' => $request->input('id'), 'status' => $request->input('status')]);
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
