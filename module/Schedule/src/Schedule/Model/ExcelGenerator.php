<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 29/5/2014
 * Time: 1:52 μμ
 */

namespace Schedule\Model;

use PHPExcel;
use PHPExcel_Writer_Excel2007;

class ExcelGenerator
{
    public static function exportExcel($dates, \DateTime $startDate, \DateTime $endDate)
    {
        $excel = new PHPExcel();
        $excel->getProperties()->setCreator("Maarten Balliauw");
        $excel->getProperties()->setLastModifiedBy("Maarten Balliauw");
        $excel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $excel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $excel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
        \PHPExcel_Shared_Font::setAutoSizeMethod(\PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $excel->setActiveSheetIndex(0);
        $columnNum = 1;
        $alphas = range('A', 'Z');
        $doubled = array();
        foreach ($alphas as $letter1) {
            foreach ($alphas as $letter2) {
                $doubled[] = $letter1 . $letter2;
            }
        }
        $alphas = array_merge($alphas, $doubled);
        do {
            $interval = $startDate->diff($endDate);
            $excel->getActiveSheet()->setCellValue($alphas[$columnNum] . '1', $startDate->format('D j/m'));

            $startDate->modify('+1 day');
            $columnNum++;
        } while ($interval->days != 0);

        $j = 2;
        foreach ($dates as $id => $dates) {
            $i = 0;
            $decodedId = \Worker\Entity\Worker::decodeId($id);
            $excel->getActiveSheet()->setCellValue($alphas[$i] . $j, $decodedId["workerFullName"]);
            $i++;
            foreach ($dates as $date => $entry) {
                if (is_array($entry)) {
                    $value = "";
                    for($k = 0; $k < count($entry); $k++){
                        if($k == count($entry)-1){
                            $value .= self::parseEntry($entry[$k]);
                        }else{
                            $value .= self::parseEntry($entry[$k]) . "\n";
                        }
                    }
                }else{
                    $value = self::parseEntry($entry);
                }
                $excel->getActiveSheet()->setCellValue($alphas[$i] . $j, $value);

                $i++;
            }
            $j++;
        }

        for($i = 0; $i<$columnNum+1;$i++){
            for($j = 0; $j < count($dates)+1; $j++){
                $excel->getActiveSheet()->getStyle($alphas[$i].$j)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            }
        }

        foreach ($alphas as $columnID) {
            $excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        $excel->getActiveSheet()->setTitle('Schedule');
        $name = ROOT_PATH . "/data/output/schedule-" . $startDate->format('d-m-Y') . '-' . $endDate->format('d-m-Y') . ".xlsx";
        $objWriter = new PHPExcel_Writer_Excel2007($excel);
        $objWriter->save($name);
        return $name;
    }

    /**
     * @param \Schedule\Entity\Entry $entry
     * @return string
     */
    private static function parseEntry($entry)
    {
        if (!empty($entry)) {
            $exception = $entry->getException();
            $entryValue = $exception === null ? $entry->getStartTime()->format('H:i') . ' - ' . $entry->getEndTime()->format('H:i') : $entry->getException()->getName();
        } else {
            $entryValue = "";
        }
        return $entryValue;
    }

} 