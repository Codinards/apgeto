<?php

namespace App\SpreadSheets;

class SpreadSheet implements SheetInterface
{
    public function create(?string $folder): void
    {
        $spreadSheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // $firstSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadSheet, "First Sheet");
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setTitle("First Sheet");
        $sheet->setCellValue("A1", "First Sheet A1");
        $sheet->setCellValue("A2", "First Sheet A1");
        $sheet->setCellValue("A3", 3);
        $secondSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadSheet, "Second Sheet");
        $secondSheet->setCellValue("A1", "Second Sheet A1");
        $secondSheet->setCellValue("A2", "Second Sheet A2");
        $secondSheet->setCellValue("A3", 3);
        $spreadSheet->addSheet($secondSheet);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadSheet);
        $writer->save($folder . "\\test.xlsx");
    }
}
