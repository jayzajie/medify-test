<?php

namespace App\Support;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MasterItemsExcelExporter
{
    /**
     * Build an Excel workbook for Master Items and return the temporary file path.
     */
    public function create(Collection $items): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Master Items');

        $headers = [
            'A1' => 'No',
            'B1' => 'Nama Kategori',
            'C1' => 'Nama Items',
            'D1' => 'Nama Supplier',
            'E1' => 'Harga',
            'F1' => 'Laba',
            'G1' => 'Harga Jual',
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '999999'],
                ],
            ],
        ]);

        $row = 2;
        foreach ($items as $index => $item) {
            $kategoriNames = $item->kategoriItems
                ->pluck('nama')
                ->filter()
                ->implode(', ');

            $hargaBeli = (float) $item->harga_beli;
            $laba = (float) $item->laba;
            $hargaJual = round($hargaBeli + ($hargaBeli * $laba / 100));

            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValueExplicit(
                "B{$row}",
                $kategoriNames !== '' ? $kategoriNames : '-',
                DataType::TYPE_STRING
            );
            $sheet->setCellValueExplicit("C{$row}", (string) $item->nama, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("D{$row}", (string) $item->supplier, DataType::TYPE_STRING);
            $sheet->setCellValue("E{$row}", $hargaBeli);
            $sheet->setCellValue("F{$row}", $laba);
            $sheet->setCellValue("G{$row}", $hargaJual);

            $row++;
        }

        $lastRow = max(1, $row - 1);

        if ($lastRow >= 2) {
            $sheet->getStyle("A2:G{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->getStyle("A2:A{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("E2:G{$lastRow}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            $sheet->getStyle("E2:G{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'master-items-' . uniqid('', true) . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $filePath;
    }
}
