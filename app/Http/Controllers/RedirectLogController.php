<?php

namespace App\Http\Controllers;

use App\Models\RedirectLog;
use App\Helpers\StringHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RedirectLogController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.redirect_log.index')->with('title',trans('frontend.title.redirect_log_index'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function clear()
    {
        RedirectLog::truncate();

        return redirect(URL::route('admin.redirect_log.index'))->with('success', trans('message.statistics_cleared'));
    }

    /**
     * @param $url
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function download($url)
    {
        $ext = 'xlsx';
        $filename = 'redirect_log_' . date("d_m_Y") . '.xlsx';
        $oSpreadsheet_Out = new Spreadsheet();

        $redirectLog = RedirectLog::where('url',$url)->get();

        if (!$redirectLog) abort(404);

        $oSpreadsheet_Out->getProperties()->setCreator('Alexander Yanitsky')
            ->setLastModifiedBy('PHP Newsletter')
            ->setTitle(trans('str.redirect_log'))
            ->setSubject('Office 2007 XLSX Document')
            ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Redirect Log file');

        $oSpreadsheet_Out->setActiveSheetIndex(0)
            ->setCellValue('A1', 'E-mail')
            ->setCellValue('B1', trans('str.time'));

        $i = 0;

        foreach ($redirectLog as $row) {
            $i++;

            $oSpreadsheet_Out->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $row->email)
                ->setCellValue('B' . $i, $row->created_at);
        }

        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('B')->setWidth(25);

        $oWriter = IOFactory::createWriter($oSpreadsheet_Out, 'Xlsx');
        ob_start();
        $oWriter->save('php://output');
        $contents = ob_get_contents();
        ob_end_clean();

        return response($contents, 200, [
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Cache-Control' => 'max-age=0',
            'Content-Type' => StringHelpers::getMimeType($ext),
        ]);
    }

    /**
     * @param $url
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function info($url)
    {
        return view('admin.redirect_log.info', compact('url'))->with('title', trans('frontend.title.redirect_log_inf'));
    }
}
