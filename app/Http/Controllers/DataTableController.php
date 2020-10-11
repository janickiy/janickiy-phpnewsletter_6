<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\{Smtp, Templates, Category, Subscribers, Schedule, ReadySent, Redirect};
use App\Helpers\StringHelpers;
use Illuminate\Support\Facades\Auth;
use DataTables;
use URL;
use Helpers;

class DataTableController extends Controller
{

    /**
     * @return mixed
     */
    public function getTemplates()
    {
        $row = Templates::with('attach')->get();

        return Datatables::of($row)

            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="check" value="' . $row->id . '" name="templateId[]">';
            })

            ->addColumn('action', function ($row) {
                $editBtn = '<a title="' . trans('frontend.str.edit') . '" class="btn btn-xs btn-primary"  href="' . URL::route('admin.template.edit', ['id' => $row->id]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                return $editBtn;

            })

            ->editColumn('name', function ($row) {
                $body = preg_replace('/(<.*?>)|(&.*?;)/', '', $row->body);
                return $row->name . '<br><br><small class="text-muted">' . StringHelpers::shortText($body, 500) . '</small>';
            })

            ->editColumn('prior', function ($row) {
                return Templates::getPrior($row->id);
            })

            ->editColumn('attach.id', function ($row) {
                return isset($row->attach) && count($row->attach) > 0 ? trans('frontend.str.yes') : trans('frontend.str.no');
            })

            ->rawColumns(['action', 'name', 'checkbox'])->make(true);
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        $row = Category::selectRaw('category.id,category.name,count(subscriptions.categoryId) as subcount')
            ->leftJoin('subscriptions', 'category.id', '=', 'subscriptions.categoryId')
            ->groupBy('category.id')
            ->groupBy('category.name');

        return Datatables::of($row)
            ->addColumn('action', function ($row) {
                $editBtn = '<a title="' . trans('frontend.str.edit') . '" class="btn btn-xs btn-primary" href="' . URL::route('admin.category.edit', ['id' => $row->id]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                $deleteBtn = '<a class="btn btn-xs btn-danger deleteRow" id="' . $row->id . '"><span class="fa fa-remove"></span></a>';

                return '<div class="nobr"> ' .$editBtn . $deleteBtn . '</div>';
            })

            ->rawColumns(['action'])->make(true);
    }

    /**
     * @return mixed
     */
    public function getSubscribers()
    {
        $row = Subscribers::query();

        return Datatables::of($row)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="check" value="' . $row->id . '" name="activate[]">';
            })

            ->editColumn('active', function ($row) {
                return $row->active == 1 ? trans('frontend.str.yes') : trans('frontend.str.no');
            })

            ->editColumn('activeStatus', function ($row) {
                return $row->active;
            })

            ->addColumn('action', function ($row) {
                $editBtn = '<a title="' . trans('frontend.str.edit') . '" class="btn btn-xs btn-primary"  href="' . URL::route('admin.subscribers.edit', ['id' => $row->id]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                return $editBtn;
            })

            ->rawColumns(['action', 'checkbox'])->make(true);
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        $row = User::query();

        return Datatables::of($row)

            ->addColumn('action', function ($row) {
                $editBtn = '<a title="' . trans('frontend.str.edit') . '" class="btn btn-xs btn-primary"  href="' . URL::route('admin.users.edit', ['id' => $row->id]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';

                if ($row->id != Auth::id())
                    $deleteBtn = '<a class="btn btn-xs btn-danger deleteRow" id="' . $row->id . '"><span class="fa fa-remove"></span></a>';
                else
                    $deleteBtn = '';

                return '<div class="nobr"> ' .$editBtn . $deleteBtn . '</div>';
            })

            ->editColumn('role', function ($row) {

                switch ($row->role) {
                    case 'admin':
                        $role = trans('frontend.str.admin');
                        break;
                    case 'editor':
                        $role = trans('frontend.str.editor');
                        break;
                    case 'moderator':
                        $role = trans('frontend.str.moderator');
                        break;
                    default:
                        $role = '';
                }

                return $role;
            })

            ->rawColumns(['action', 'id'])->make(true);
    }

    /**
     * @return mixed
     */
    public function getSmtp()
    {
        $row = Smtp::query();

        return Datatables::of($row)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" value="' . $row->id . '" name="activate[]">';
            })
            ->editColumn('active', function ($row) {
                return $row->active == 1 ? trans('frontend.str.yes') : trans('frontend.str.no');
            })
            ->addColumn('action', function ($row) {
                $editBtn = '<a title="' . trans('frontend.str.edit') . '" class="btn btn-xs btn-primary"  href="' . URL::route('admin.smtp.edit', ['id' => $row->id]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                $deleteBtn = '<a class="btn btn-xs btn-danger deleteRow" id="' . $row->id . '"><span class="fa fa-remove"></span></a>';

                return '<div class="nobr"> ' .$editBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['action', 'checkbox'])->make(true);
    }

    /**
     * @return mixed
     */
    public function getLog()
    {
        $row = Schedule::selectRaw('schedule.id, schedule.value_from_start_date, schedule.value_from_end_date, count(ready_sent.id) as count,sum(ready_sent.success=1) as sent,sum(ready_sent.readMail=1) as read_mail')
            ->join('ready_sent', 'schedule.id', '=', 'ready_sent.scheduleId')
            ->groupBy('ready_sent.scheduleId')
            ->groupBy('schedule.value_from_start_date')
            ->groupBy('schedule.value_from_end_date')
            ->groupBy('schedule.id')
        ;

        return Datatables::of($row)
            ->editColumn('count', function ($row) {
                return '<a href="' . URL::route('admin.log.info', ['id' => $row->id]) . '">' . $row->count . '</a>';
            })
            ->addColumn('unsent', function ($row) {
                return $row->count - $row->sent;
            })
            ->editColumn('read_mail', function ($row) {
                return $row->read_mail ? $row->read_mail : 0;
            })
            ->addColumn('report', function ($row) {
                return Helpers::has_permission(Auth::user()->role,'admin') ? '<a href="' . URL::route('admin.log.report', ['id' => $row->id]) . '">' . trans('frontend.str.download') . '</a>' : '';
            })
            ->rawColumns(['count', 'report'])->make(true);
    }

    /**
     * @return mixed
     */
    public function getInfoLog($id = null)
    {
        $row = $id ? ReadySent::where('scheduleId', $id) : ReadySent::query();

        return Datatables::of($row)

            ->editColumn('success', function ($row) {
                return $row->success == 1 ? trans('frontend.str.send_status_yes') : trans('frontend.str.send_status_no');
            })

            ->editColumn('readMail', function ($row) {
                return $row->readMail == 1 ? trans('frontend.str.yes') : trans('frontend.str.no');
            })

            ->addColumn('status', function ($row) {
                return $row->success;
            })

            ->addColumn('read', function ($row) {
                return $row->readMail;
            })

            ->make(true);

    }

    /**
     * @return mixed
     */
    public function getRedirectLog()
    {
        $row = Redirect::query()
            ->selectRaw('url,COUNT(email) as count')
            ->groupBy('url')
            ->distinct()
        ;

        return Datatables::of($row)

            ->editColumn('count', function ($row) {
                return '<a href="' . URL::route('admin.redirect.info', ['url' => base64_encode($row->url)]) . '">' . $row->count . '</a>';
            })
            ->addColumn('report', function ($row) {
                return Helpers::has_permission(Auth::user()->role,'admin') ? '<a href="' . URL::route('admin.redirect.report', ['url' => base64_encode($row->url)]) . '">' . trans('frontend.str.download') . '</a>' : '';
            })
            ->rawColumns(['count', 'report'])->make(true);

    }

    /**
     * @param $url
     * @return mixed
     */
    public function getInfoRedirectLog($url)
    {
        $url = base64_decode($url);

        $row = Redirect::query()->where('url', $url);

        return Datatables::of($row)

            ->make(true);
    }
}
