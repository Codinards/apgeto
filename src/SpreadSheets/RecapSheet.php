<?php

namespace App\SpreadSheets;

use App\Controller\BaseController;
use App\Entity\Main\Funds\DebtInterest;
use App\Entity\Main\Funds\DebtRenewal;
use App\Tools\AppConstants;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecapSheet implements SheetInterface
{
    public function __construct(private BaseController  $baseController)
    {
    }
    public function create(?string $folder, bool $isFileSheetName = false): void
    {
        $year = ($date = new \DateTime())->format("Y");
        $accountRepo = $this->baseController->getAccountRepository();
        $alignment =                 [
            'horizontal'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'     => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'textRotation' => 0,
            // 'shrinkToFit'     => TRUE
        ];

        $interests = $this->collection($this->baseController
            ->getInterestRepository()->findBy(['year' => $year]))
            ->groupBy(fn (DebtInterest $interest) => $interest->getDebt()->getId());
        $renewals = $this->collection($this->baseController
            ->getRenewalRepository()->findBy(['year' => $year]))
            ->groupBy(fn (DebtRenewal $renewal) => $renewal->getAccount()->getId());
        $accounts = $accountRepo->findAll();
        $spreadSheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setTitle("RECAPITULATIF");

        $sheet->mergeCells("A1:J2")
            ->setCellValue("A1", "ASSOCIATION BAFOU ")
            // ->getStyle("A1:J2")
            // ->getBorders()
            // ->getOutline()
            // ->setBorderStyle(Border::BORDER_THICK)
            // ->setColor(new Color())
        ;
        $sheet = $this->setBorder($sheet, "A1:J2");
        $sheet
            ->mergeCells("A3:J3")
            ->setCellValue("A3", "MALABO, G.E.")
            // ->getStyle("A3:J3")
            // ->getBorders()
            // ->getOutline()
            // ->setBorderStyle(Border::BORDER_THICK)
            // ->setColor(new Color())
        ;
        $sheet = $this->setBorder($sheet, "A3:J3");
        $sheet->mergeCells("A4:J4")
            ->setCellValue("A4", "RECAPITULATIF DES FONDS")
            // ->getStyle("A4:J4")
            // ->getBorders()
            // ->getOutline()
            // ->setBorderStyle(Border::BORDER_THICK)
            // ->setColor(new Color())
        ;
        $sheet = $this->setBorder($sheet, "A4:J4");
        $sheet->setCellValue("A5", "NUM")
            ->setCellValue("B5", "NOMS ET PRENOMS")
            ->setCellValue("C5", "FONDS TOTALS")
            ->setCellValue("D5", "FONDS REELS")
            ->setCellValue("E5", "ASSURANCES")
            ->setCellValue("F5", "DETTES")
            ->setCellValue("G5", "CUMUL_RECOND")
            ->setCellValue("H5", "CUMUL_INTERET")
            ->setCellValue("I5", "INTERET RECU");
        // ->setCellValue("J5", "AUTRES");
        $totalBalance = 0;
        $sheet = $this->setBorder($sheet, "A5");
        $sheet = $this->setBorder($sheet, "B5");
        $sheet = $this->setBorder($sheet, "C5");
        $sheet = $this->setBorder($sheet, "D5");
        $sheet = $this->setBorder($sheet, "E5");
        $sheet = $this->setBorder($sheet, "F5");
        $sheet = $this->setBorder($sheet, "G5");
        $sheet = $this->setBorder($sheet, "H5");
        $sheet = $this->setBorder($sheet, "I5");
        // $sheet = $this->setBorder($sheet, "J5");

        $sheet->getStyle("A1:J5")
            ->getAlignment()->applyFromArray($alignment);
        $initialIndex = 6;
        foreach ($accounts as $key => $account) {
            $index = $key + $initialIndex;
            $sheet->setCellValue("A" . $index, $key + 1);
            $sheet = $this->setBorder($sheet, "A" . $index, Border::BORDER_THIN);
            $sheet->setCellValue("B" . $index, $account->getUser()->getName());
            $sheet = $this->setBorder($sheet, "B" . $index, Border::BORDER_THIN);
            $sheet->setCellValue("C" . $index, "='" . ($balance = $account->getUser()->getName()) . "'!E90");
            $sheet = $this->setBorder($sheet, "C" . $index, Border::BORDER_THIN);
            $totalBalance += $balance;
            // $realAmount = $balance >= AppConstants::$INSURRANCE_AMOUNT
            //     ? $balance - AppConstants::$INSURRANCE_AMOUNT
            //     : ($balance < 0 ? $balance : 0);
            $sheet->setCellValue("D" . $index,  "=SI(C" . $index . ">=100000;C" . $index . "-100000;0)");
            $sheet = $this->setBorder($sheet, "D" . $index, Border::BORDER_THIN);
            $sheet->setCellValue(
                "E" . $index,
                "=SI(C" . $index . ">=100000;100000;SI(c" . $index . "<=0;0;c" . $index . ")"
                // $balance < 0
                //     ? 0
                //     : ($balance > AppConstants::$INSURRANCE_AMOUNT
                //         ? $balance - AppConstants::$INSURRANCE_AMOUNT
                //         : $balance)
            );
            $sheet = $this->setBorder($sheet, "E" . $index, Border::BORDER_THIN);
            $sheet->setCellValue("F" . $index, "='" . ($balance = $account->getUser()->getName()) . "'!I90");
            $sheet = $this->setBorder($sheet, "F" . $index, Border::BORDER_THIN);
            $recond = $renewals->get($account->getId());
            // /** @var Collection $recond */
            // if ($recond) {
            //     $sheet->setCellValue("G", $recond->sum(
            //         fn (DebtRenewal $renewal) => $renewal->getAmount()
            //     ));
            // }
            $sheet->setCellValue("G" . $index, "='" . ($balance = $account->getUser()->getName()) . "'!J90");
            $sheet = $this->setBorder($sheet, "G" . $index, Border::BORDER_THIN);
            // $inter = $renewals->get($account->getId());
            // /** @var Collection $inter */
            // if ($inter) {
            //     $sheet->setCellValue("H", $inter->sum(
            //         fn (DebtInterest $renewal) => $renewal->getInterest()
            //     ));
            // }
            $sheet->setCellValue("H" . $index, "='" . ($balance = $account->getUser()->getName()) . "'!H90");
            $sheet = $this->setBorder($sheet, "H" . $index, Border::BORDER_THIN);

            $sheet->setCellValue("I" . $index, 0);
            $sheet = $this->setBorder($sheet, "I" . $index, Border::BORDER_THIN);
        }

        $totalIndex = $index + 10;
        $sheet->mergeCells("A" . $totalIndex . ":B" . $totalIndex)
            ->setCellValue("A" . $totalIndex, "TOTALS")
            ->setCellValue("C" . $totalIndex, "=SOMME(C" . $initialIndex . ":C" . $totalIndex - 1 . ")")
            ->setCellValue("D" . $totalIndex, "=SOMME(D" . $initialIndex . ":D" . $totalIndex - 1 . ")")
            ->setCellValue("E" . $totalIndex, "=SOMME(E" . $initialIndex . ":E" . $totalIndex - 1 . ")")
            ->setCellValue("F" . $totalIndex, "=SOMME(F" . $initialIndex . ":F" . $totalIndex - 1 . ")")
            ->setCellValue("G" . $totalIndex, "=SOMME(G" . $initialIndex . ":G" . $totalIndex - 1 . ")")
            ->setCellValue("H" . $totalIndex, "=SOMME(H" . $initialIndex . ":H" . $totalIndex - 1 . ")")
            ->setCellValue("I" . $totalIndex, "=SOMME(I" . $initialIndex . ":I" . $totalIndex - 1 . ")");
        $sheet = $this->setBorder($sheet, "A" . $totalIndex . ":B" . $totalIndex);
        // $sheet = $this->setBorder($sheet, "B" . $totalIndex);
        $sheet = $this->setBorder($sheet, "C" . $totalIndex);
        $sheet = $this->setBorder($sheet, "D" . $totalIndex);
        $sheet = $this->setBorder($sheet, "E" . $totalIndex);
        $sheet = $this->setBorder($sheet, "F" . $totalIndex);
        $sheet = $this->setBorder($sheet, "G" . $totalIndex);
        $sheet = $this->setBorder($sheet, "H" . $totalIndex);
        $sheet = $this->setBorder($sheet, "I" . $totalIndex);

        $sheet->mergeCells("B" . $totalIndex + 2 . ":E" . $totalIndex + 2)
            ->setCellValue("B" . $totalIndex + 2, "TABLEAU RECAPITULATIF");

        // $sheet = $this->setBorder($sheet, "A" . $totalIndex + 2);

        $sheet->mergeCells("B" . $totalIndex + 3 . ":C" . $totalIndex + 3)
            ->setCellValue("B" . $totalIndex + 3, "FOND GLOBAL " . $date->format("Y"));
        $sheet = $this->setBorder($sheet, "B" . $totalIndex + 3);
        $sheet->mergeCells("D" . $totalIndex + 3 . ":E" . $totalIndex + 3)
            ->setCellValue("D" . $totalIndex + 3, "=C" . $totalIndex);
        $sheet = $this->setBorder($sheet, "D" . $totalIndex + 3);

        $sheet->mergeCells("B" . $totalIndex + 3 . ":C" . $totalIndex + 3)
            ->setCellValue("B" . $totalIndex + 3, "FOND REEL " . $date->format("Y") . "");
        $sheet = $this->setBorder($sheet, "B" . $totalIndex + 3);
        $sheet->mergeCells("D" . $totalIndex + 3 . ":E" . $totalIndex + 3)
            ->setCellValue("D" . $totalIndex + 3, "=C" . $totalIndex);
        $sheet = $this->setBorder($sheet, "D" . $totalIndex + 3);
        // $sheet = $this->setBorder($sheet, "C" . $totalIndex + 2);
        // $sheet = $this->setBorder($sheet, "D" . $totalIndex + 2);
        // $sheet = $this->setBorder($sheet, "E" . $totalIndex + 2);
        // $sheet = $this->setBorder($sheet, "F" . $totalIndex + 2);
        // $sheet = $this->setBorder($sheet, "G" . $totalIndex + 2);
        // $sheet = $this->setBorder($sheet, "H" . $totalIndex + 2);
        // $sheet = $this->setBorder($sheet, "I" . $totalIndex + 2);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadSheet);
        // if (!file_exists($folder)) {
        //     mkdir($folder, recursive: true);
        // }
        // $writer->save($folder . "\Fond " . $date->format("d-m-Y") . ".xlsx");
    }

    private function collection(array $data = []): Collection
    {
        return new Collection($data);
    }

    private function setBorder(Worksheet $sheet, string $range, string $borderStyle = Border::BORDER_THICK): Worksheet
    {
        $sheet->getStyle($range)
            ->getBorders()
            ->getOutline()
            ->setBorderStyle($borderStyle)
            ->setColor(new Color());
        return $sheet;
    }
}
