<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{Category, Subscribers, Subscriptions, Charset};
use App\Helpers\StringHelpers;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use URL;

class SubscribersController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.subscribers.index')->with('title', trans('frontend.title.subscribers_index'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $options = [];

        foreach (Category::orderBy('name')->get() as $row) {
            $options[$row->id] = $row->name;
        }

        return view('admin.subscribers.create_edit', compact('options'))->with('title', trans('frontend.title.subscribers_create'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:subscribers',
            'categoryId' => 'array|nullable'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {

            $id = Subscribers::create(array_merge($request->all(), ['timeSent' => date('Y-m-d H:i:s'),'active' => 1, 'token' => StringHelpers::token()]))->id;

            if ($request->categoryId && $id) {
                foreach ($request->categoryId as $categoryId) {
                    if (is_numeric($categoryId)) {
                        Subscriptions::create(['subscriberId' => $id, 'categoryId' => $categoryId]);
                    }
                }
            }

            return redirect(URL::route('admin.subscribers.index'))->with('success', trans('message.information_successfully_added'));
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $subscriber = Subscribers::where('id', $id)->first();

        if (!$subscriber) abort(404);

        $options = [];

        foreach (Category::orderBy('name')->get() as $row) {
            $options[$row->id] = $row->name;
        }

        $subscriberCategoryId = [];

        foreach ($subscriber->subscriptions as $subscription) {
            $subscriberCategoryId[] = $subscription->categoryId;
        }

        return view('admin.subscribers.create_edit', compact('options', 'subscriber', 'subscriberCategoryId'))->with('title', trans('frontend.title.subscribers_edit'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        if (!is_numeric($request->id)) abort(500);

        $rules = [
            'email' => 'required|email|unique:subscribers,email,' . $request->id,
            'categoryId' => 'array|nullable'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {

            if ($request->categoryId) {

                Subscriptions::where('subscriberId', $request->id)->delete();

                foreach ($request->categoryId as $categoryId) {
                    if (is_numeric($categoryId)) Subscriptions::create(['subscriberId' => $request->id, 'categoryId' => $categoryId]);
                }
            }

            $data['name'] = $request->input('name');
            $data['email'] = $request->input('email');

            Subscribers::where('id', $request->id)->update($data);

            return redirect(URL::route('admin.subscribers.index'))->with('success', trans('message.data_updated'));
        }
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        Subscriptions::where('subscriberId', $id)->delete();
        Subscribers::where('id', $id)->delete();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function import()
    {
        @set_time_limit(0);

        $charsets = [];

        foreach (Charset::get() as $row) {
            $charsets[$row->charset] = $row->charset;
        }

        $category_options = [];

        foreach (Category::get() as $row) {
            $category_options[$row->id] = $row->name;
        }

        $maxUploadFileSize = StringHelpers::maxUploadFileSize();

        return view('admin.subscribers.import', compact('charsets', 'category_options', 'maxUploadFileSize'))->with('title', trans('frontend.title.subscribers_import'));

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function importSubscribers(Request $request)
    {
        $rules = [
            'import' => 'required|mimes:csv,xlsx,xls,txt',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {

            $extension = strtolower($request->file('import')->getClientOriginalExtension());

            switch ($extension) {
                case 'csv':
                case 'xls':
                case 'xlsx':

                    $result = $this->importFromExcel($request);

                    break;

                default:

                    $result = $this->importFromText($request);
            }

            if ($result === false)
                return redirect(URL::route('admin.subscribers.index'))->with('error', trans('message.error_import_file'));
            else
                return redirect(URL::route('admin.subscribers.index'))->with('success', trans('message.import_completed') . $result);

        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function export()
    {
        $options = [];

        foreach (Category::orderBy('name')->get() as $row) {
            $options[$row->id] = $row->name;
        }

        return view('admin.subscribers.export', compact('options'))->with('title', trans('frontend.title.subscribers_export'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportSubscribers(Request $request)
    {
        $request->export_type;
        $subscribers = $this->getSubscribersList($request->categoryId);

        if ($request->export_type == 'text') {
            $ext = 'txt';
            $filename = 'emailexport' . date("d_m_Y") . '.txt';

            if ($subscribers) {
                $contents = '';
                foreach ($subscribers as $subscriber) {
                    $contents .= "" . $subscriber->email . " " . $subscriber->name . "\r\n";
                }
            }
        } elseif ($request->export_type == 'excel') {

            $ext = 'xlsx';
            $filename = 'emailexport' . date("d_m_Y") . '.xlsx';
            $oSpreadsheet_Out = new Spreadsheet();

            $oSpreadsheet_Out->getProperties()->setCreator('Alexander Yanitsky')
                ->setLastModifiedBy('PHP Newsletter')
                ->setTitle('Office 2007 XLSX Document')
                ->setSubject('Office 2007 XLSX Document')
                ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
                ->setKeywords('office 2007 openxml php')
                ->setCategory('Email export file')
            ;

            // Add some data
            $oSpreadsheet_Out->setActiveSheetIndex(0)
                ->setCellValue('A1', 'User email')
                ->setCellValue('B2', 'Name')
            ;

            $i = 0;

            foreach ($subscribers as $subscriber) {
                $i++;

                $oSpreadsheet_Out->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $subscriber->email)
                    ->setCellValue('B'.$i, $subscriber->name)
                ;
            }

            $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('A')->setWidth(30);
            $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('B')->setWidth(30);

            $oWriter = IOFactory::createWriter($oSpreadsheet_Out, 'Xlsx');
            ob_start();
            $oWriter->save('php://output');
            $contents = ob_get_contents();
            ob_end_clean();
        }

        if ($request->compress == 'zip'){

            $fout = fopen("php://output", "wb");

            if ($fout !== false){
                fwrite($fout, "\x1F\x8B\x08\x08".pack("V", '')."\0\xFF", 10);

                $oname = str_replace("\0", "", $filename);
                fwrite($fout, $oname."\0", 1+strlen($oname));

                $fltr = stream_filter_append($fout, "zlib.deflate", STREAM_FILTER_WRITE, -1);
                $hctx = hash_init("crc32b");

                if (!ini_get("safe_mode")) set_time_limit(0);

                hash_update($hctx, $contents);
                $fsize = strlen($contents);

                fwrite($fout, $contents, $fsize);

                stream_filter_remove($fltr);

                $crc = hash_final($hctx, TRUE);

                fwrite($fout, $crc[3] . $crc[2] . $crc[1] . $crc[0], 4);
                fwrite($fout, pack("V", $fsize), 4);

                fclose($fout);

                return response('',200, [
                    'Content-Type' => 'application/zip',
                    'Content-Disposition' => 'filename=emailexport_' . date("d_m_Y") . '.zip',
                ]);
            }

        } else {

            return response($contents, 200, [
                'Content-Disposition' => 'attachment; filename=' . $filename,
                'Cache-Control' => 'max-age=0',
                'Content-Type' => StringHelpers::getMimeType($ext),
            ]);
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function removeAll()
    {
        Subscribers::truncate();
        Subscriptions::truncate();

        return redirect(URL::route('admin.subscribers.index'))->with('success', trans('message.data_successfully_deleted'));

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function status(Request $request)
    {
        $temp = [];

        foreach ($request->activate as $id) {
            if (is_numeric($id)) {
                $temp[] = $id;
            }
        }

        switch ($request->action) {
            case  0 :
            case  1 :

                Subscribers::whereIN('id', $temp)->update(['active' => $request->action]);

                break;

            case 2 :

                Subscribers::whereIN('id', $temp)->delete();

                break;
        }

        return redirect(URL::route('admin.subscribers.index'))->with('success', trans('message.actions_completed'));
    }

    /**
     * @param $f
     * @return bool|int
     */
    private function importFromText($f)
    {
        if (!($fp = @fopen($f->file('import'), "rb"))) {
            return false;
        } else {
            $buffer = fread($fp, filesize($f->file('import')));
            fclose($fp);
            $tok = strtok($buffer, "\n");

            while ($tok) {
                $tok = strtok("\n");
                $strtmp[] = $tok;
            }

            $count = 0;

            foreach ($strtmp as $val) {
                $str = $val;

                if ($f->charset) {
                    $str = StringHelper::convertEncoding($str, 'utf-8', $f->charset);
                }

                preg_match('/([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)/uis', $str, $out);

                $email = isset($out[0]) ? $out[0] : '';
                $name = str_replace($email, '', $str);
                $email = strtolower($email);
                $name = trim($name);

                if (strlen($name) > 250) {
                    $name = '';
                }

                if ($email) {
                    $subscriber = Subscribers::where('email', 'like', $email)->first();

                    if ($subscriber) {
                        Subscriptions::where('subscriberId', $subscriber->id)->delete();

                        if ($f->categoryId) {
                            foreach ($f->categoryId as $id) {
                                if (is_numeric($id)) {
                                    Subscriptions::create(['subscriberId' => $subscriber->id, 'categoryId' => $id]);
                                }
                            }
                        }
                    } else {

                        $data['name'] = $name;
                        $data['email'] = $email;
                        $data['token'] = StringHelpers::token();
                        $data['timeSent'] = date('Y-m-d H:i:s');
                        $data['active'] = 1;

                        $insertId = Subscribers::create($data)->id;

                        if ($insertId) $count++;

                        if ($f->categoryId) {
                            foreach ($f->categoryId as $id) {
                                if (is_numeric($id)) {
                                    Subscriptions::create(['subscriberId' => $insertId, 'categoryId' => $id]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $count;
    }

    /**
     * @param $f
     * @return bool|int
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private function importFromExcel($f)
    {
        $ext = strtolower($f->file('import')->getClientOriginalExtension());

        if ($ext == 'csv') {
            $reader = new Csv();

            if ($ext == 'csv' && $f->charset) {
                $reader->setInputEncoding($f->charset);
            }

        } elseif ($ext == 'xlsx') {
            $reader = new Xlsx();
        } else {
            $reader = new Xls();
        }

        $count = 0;

        $spreadsheet = $reader->load($f->file('import'));

        if (!$spreadsheet) return false;

        $allDataInSheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        foreach ($allDataInSheet as $dataInSheet) {
            $email = trim($dataInSheet['A']);
            $name = trim($dataInSheet['B']);

            if (StringHelpers::isEmail($email)) {
                $subscribers = Subscribers::where('email', 'like', $email)->first();

                if ($subscribers && $f->categoryId) {
                    Subscriptions::where('subscriberId', $subscribers->id)->delete();

                    foreach ($f->categoryId as $category) {
                        if (is_numeric($category)) {
                            $data = [
                                'subscriberId' => $subscribers->id,
                                'categoryId' => $category,
                            ];

                            Subscriptions::create($data);
                        }
                    }
                } else {
                    $subscribersData = [
                        'name' => $name,
                        'email' => $email,
                        'active' => 1,
                         'timeSent' => date('Y-m-d H:i:s'),
                        'token' => StringHelpers::token()
                    ];

                    $insertId = Subscribers::create($subscribersData)->id;

                    if ($f->categoryId) {
                        foreach ($f->categoryId as $category) {
                            if (is_numeric($category)) {
                                $data = [
                                    'subscriberId' => $insertId,
                                    'categoryId' => $category,
                                ];

                                Subscriptions::create($data);
                            }
                        }
                    }

                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * @param array $categoryId
     * @return mixed
     */
    private function getSubscribersList($categoryId = [])
    {
        if ($categoryId) {
            $temp = [];
            foreach ($categoryId as $id) {
                if (is_numeric($id)) {
                    $temp[] = $id;
                }
            }

            $subscribers = Subscribers::select('subscribers.name','subscribers.email')
                ->leftJoin('subscriptions', function($join) {
                    $join->on('subscribers.id', '=', 'subscriptions.subscriberId');
                })
                ->where('subscribers.active','=',1)
                ->whereIn('subscriptions.categoryId',$temp)
                ->groupBy('subscribers.email')
                ->groupBy('subscribers.id')
                ->groupBy('subscribers.name')
                ->get();
        } else {
            $subscribers = Subscribers::select('name','email')
                ->where('active','=',1)
                ->get();
        }

        return $subscribers;
    }
}
