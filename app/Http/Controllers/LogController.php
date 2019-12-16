<?php

namespace App\Http\Controllers;

use App\Models\ReadySent;
use \App\Helpers\StringHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use URL;

class LogController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.log.index')->with('title', trans('frontend.title.log_index'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function clear()
    {
        ReadySent::truncate();

        return redirect(URL::route('admin.log.index'))->with('success', trans('message.log_cleared'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function download($id)
    {
        $ext = 'xlsx';
        $filename = 'log' . date("d_m_Y") . '.xlsx';
        $oSpreadsheet_Out = new Spreadsheet();

        $readySent = ReadySent::where('scheduleId', $id)->get();

        if (!$readySent) abort(404);

        $totalfaild = ReadySent::where('scheduleId', $id)->where('success', 0)->count();
        $readmail = ReadySent::where('scheduleId', $id)->where('readMail', 1)->count();
        $totaltime = ReadySent::selectRaw('sec_to_time(UNIX_TIMESTAMP(max(created_at)) - UNIX_TIMESTAMP(min(created_at))) as totaltime')->where('scheduleId', $id)->first();
        // $totaltime  = '';
        $total = $readySent->count();

        if ($total > 0) {
            $success = $total - $totalfaild;
            $count = 100 * $success / $total;
        } else {
            $count = 0;
            $total = 0;
        }

        $oSpreadsheet_Out->getProperties()->setCreator('Alexander Yanitsky')
            ->setLastModifiedBy('PHP Newsletter')
            ->setTitle(trans('frontend.str.log'))
            ->setSubject('Office 2007 XLSX Document')
            ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Log file');

        // Add some data
        $oSpreadsheet_Out->setActiveSheetIndex(0)
            ->setCellValue('A1', trans('frontend.str.total') . ": $total\n" . trans('frontend.str.sent') . ": " . $count . "%\n" . trans('frontend.str.spent_time') . ": $totaltime->totaltime\n" . trans('frontend.str.read') . ": " . $readmail)
            ->setCellValue('A2', trans('frontend.str.email'))
            ->setCellValue('B2', trans('frontend.str.time'))
            ->setCellValue('C2', trans('frontend.str.status'))
            ->setCellValue('A2', trans('frontend.str.mailer'))
            ->setCellValue('B2', trans('frontend.str.email'))
            ->setCellValue('C2', trans('frontend.str.time'))
            ->setCellValue('D2', trans('frontend.str.status'))
            ->setCellValue('E2', trans('frontend.str.read'))
            ->setCellValue('F2', trans('frontend.str.error'))
            ->mergeCells('A1:F1');

        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(['wrapText' => TRUE]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A2')->getFill()->applyFromArray(['setRGB' => 'E3DA62']);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('B2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A1')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEEEEE']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('B2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('C2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('D2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('E2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('F2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);

        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('B2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('C2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('D2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('E2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('F2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);

        $i = 2;

        foreach ($readySent as $row) {
            $i++;

            $oSpreadsheet_Out->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $row->template)
                ->setCellValue('B' . $i, $row->email)
                ->setCellValue('C' . $i, $row->created_at)
                ->setCellValue('D' . $i, $row->success == 1 ? trans('frontend.str.send_status_yes') : trans('frontend.str.send_status_no'))
                ->setCellValue('E' . $i, $row->readMail == 1? trans('frontend.str.yes') : trans('frontend.str.no'))
                ->setCellValue('F' . $i, $row->errorMsg);

            $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('D' . $i)->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
            $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('E' . $i)->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        }

        $oSpreadsheet_Out->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(70);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('F')->setWidth(35);

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
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function info($id)
    {
        return view('admin.log.info', compact('id'))->with('title', trans('frontend.title.log_info'));
    }
}
