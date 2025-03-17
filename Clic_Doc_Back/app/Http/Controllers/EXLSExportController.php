<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EXLSExportController extends Controller
{
    public function exportTransactions(Request $request, $doctor_id)
    {
        $doctor = DB::table('users')->where('id', $doctor_id)->value('name');

        // Query logic remains the same
        $paymentsQuery = DB::table('payments')
            ->join('factures as f', 'f.id', '=', 'payments.facture_id')
            ->join('consultations as c', 'c.id', '=', 'f.consultation_id')
            ->join('patients as p', 'p.id', '=', 'c.patient_id')
            ->where('c.doctor_id', $doctor_id)
            ->select(
                'payments.created_at',
                'payments.amount',
                'payments.type',
                'f.uid as facture_uid',
                'p.name as patient_name'
            );

        $chargesQuery = DB::table('charges')
            ->select(
                'charges.created_at',
                DB::raw("charges.montant as amount"),
                DB::raw("'Sorties' as type"),
                DB::raw("NULL as facture_uid"),
                DB::raw("NULL as patient_name")
            );

        $transactions = $paymentsQuery
            ->union($chargesQuery)
            ->orderBy('created_at', 'desc')
            ->get();

        // Create spreadsheet with enhanced styling
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', "Rapport des Transactions - Dr. $doctor");
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '0092C5']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA']
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Add date range if applicable
        $sheet->setCellValue('A2', 'Date d\'exportation: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Set headers with enhanced styling
        $headers = ['#', 'UID de Facture', 'Patient', 'Montant', 'Type', 'Date'];
        $row = 4;
        foreach (array_values($headers) as $idx => $header) {
            $col = chr(65 + $idx);
            $sheet->setCellValue("{$col}{$row}", $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Style header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0092C5']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '0092C5']
                ]
            ]
        ];
        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($headerStyle);
        $sheet->getRowDimension($row)->setRowHeight(25);

        // Add data with alternating row colors
        $dataRow = 5;
        foreach ($transactions as $index => $transaction) {
            $rowData = [
                $index + 1,
                $transaction->facture_uid ?? 'N/A',
                $transaction->patient_name ?? 'N/A',
                $transaction->amount,
                $transaction->type,
                date('d/m/Y H:i', strtotime($transaction->created_at))
            ];

            // Write data
            foreach (array_values($rowData) as $idx => $value) {
                $col = chr(65 + $idx);
                $sheet->setCellValue("{$col}{$dataRow}", $value);
            }

            // Style amount column
            $sheet->getStyle("D{$dataRow}")->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            // Alternating row colors
            $fillColor = $dataRow % 2 === 0 ? 'F8FBFE' : 'FFFFFF';
            $sheet->getStyle("A{$dataRow}:F{$dataRow}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0']
                    ]
                ]
            ]);

            // Style Type column (Different colors for Entrées/Sorties)
            $typeColor = $transaction->type === 'Sorties' ? '10B981' : 'EF4444';
            $sheet->getStyle("E{$dataRow}")->applyFromArray([
                'font' => ['color' => ['rgb' => $typeColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            $sheet->getRowDimension($dataRow)->setRowHeight(22);
            $dataRow++;
        }

        // Add totals
        $lastRow = $dataRow - 1;
        $totalRow = $dataRow + 1;
        
        // Calculate Total Entrées
        $sheet->setCellValue("C{$totalRow}", "Total Entrées:");
        $sheet->setCellValue("D{$totalRow}", "=SUMIF(E5:E{$lastRow},\"<>Sorties\",D5:D{$lastRow})");
        $sheet->getStyle("D{$totalRow}")->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        
        // Calculate Total Sorties
        $sheet->setCellValue("C" . ($totalRow + 1), "Total Sorties:");
        $sheet->setCellValue("D" . ($totalRow + 1), "=SUMIF(E5:E{$lastRow},\"Sorties\",D5:D{$lastRow})");
        $sheet->getStyle("D" . ($totalRow + 1))->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        // Style totals
        $sheet->getStyle("C{$totalRow}:D" . ($totalRow + 1))->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA']
            ]
        ]);

        // Final touches
        // $sheet->setShowGridlines(false);
        $sheet->getPageSetup()->setFitToWidth(1);
        
        // Headers for download
        $filename = "transactions_" . Str::slug($doctor) . "_" . now()->format('Y-m-d') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


    public function exportPatients(Request $request)
{
    // Query patients data from the database
    $patients = DB::table('patients')
        ->select(
            'uid',
            'sex',
            'name',
            'surname',
            'date_of_birth',
            'phone',
            'blood_type',
            'CIN',
            DB::raw("IF(coverage, 'Oui', 'Non') as coverage"),
            'coverage_type',
            'coverage_number'
        )
        ->get();

    // Create a new spreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set the headers
    $headers = [
        '#',
        'UID',
        'Sexe',
        'Nom',
        'Prénom',
        'Date de Naissance',
        'Téléphone',
        'Groupe Sanguin',
        'CIN',
        'Couverture',
        'Type de Couverture',
        'Numéro de Couverture'
    ];

    // Apply header styling
    $sheet->fromArray($headers, NULL, 'A1');
    $sheet->getStyle('A1:L1')->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '0092C5']
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
        ]
    ]);
    $sheet->getRowDimension(1)->setRowHeight(30);

    // Add patient data
    $row = 2;
    foreach ($patients as $index => $patient) {
        $sheet->fromArray([
            $index + 1,
            $patient->uid,
            $patient->sex,
            $patient->name,
            $patient->surname,
            $patient->date_of_birth,
            $patient->phone,
            $patient->blood_type,
            $patient->CIN,
            $patient->coverage,
            $patient->coverage_type,
            $patient->coverage_number
        ], NULL, "A{$row}");

        // Alternate row coloring
        $fillColor = $row % 2 === 0 ? 'F8FBFE' : 'FFFFFF';
        $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => $fillColor]
            ],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ]);
        $row++;
    }

    // Auto-size columns
    foreach (range('A', 'L') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Add border styling
    $lastRow = $row - 1;
    $sheet->getStyle("A1:L{$lastRow}")->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => 'E2E8F0']
            ]
        ]
    ]);

    // Prepare the file for download
    $filename = 'Liste_Patients_' . now()->format('Y-m-d') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}


}