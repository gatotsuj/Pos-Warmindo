<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyTransactionExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    use Exportable;

    protected $transactions;

    protected $date;

    public function __construct($transactions, $date)
    {
        $this->transactions = $transactions;
        $this->date = $date;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'INVOICE',
            'TIME',
            'ITEMS',
            'TOTAL',
            'PAYMENT',
            'CASHIER',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->invoice_number,
            $transaction->created_at->format('H:i'),
            $transaction->items->sum('quantity'),
            $transaction->formatted_grand_total,
            ucfirst($transaction->payment_method),
            $transaction->user->name,
        ];
    }

    public function title(): string
    {
        return 'Daily Report';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,  // Invoice
            'B' => 12,  // Time
            'C' => 10,  // Items
            'D' => 18,  // Total
            'E' => 18,  // Payment
            'F' => 25,  // Cashier
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = $this->transactions->count() + 1; // +1 for header

        return [
            // Header row styling (mirip bg-gray-50)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 10,
                    'color' => ['rgb' => '6B7280'], // text-gray-500
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F9FAFB'], // bg-gray-50
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E5E7EB'], // divide-gray-200
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = $this->transactions->count() + 1;

                // Styling untuk semua data rows
                for ($row = 2; $row <= $rowCount; $row++) {
                    // Text size untuk semua cell
                    $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                        'font' => ['size' => 11],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Invoice column biru
                    $sheet->getStyle("A{$row}")->getFont()->getColor()->setRGB('2563EB');

                    // Total column bold
                    $sheet->getStyle("D{$row}")->getFont()->setBold(true);

                    // Border bawah setiap row
                    if ($row < $rowCount) {
                        $sheet->getStyle("A{$row}:F{$row}")->getBorders()->getBottom()
                            ->setBorderStyle(Border::BORDER_THIN)
                            ->getColor()->setRGB('E5E7EB');
                    }
                }

                // Border outline
                $sheet->getStyle("A1:F{$rowCount}")->getBorders()->getOutline()
                    ->setBorderStyle(Border::BORDER_MEDIUM)
                    ->getColor()->setRGB('E5E7EB');

                // Row height
                for ($row = 1; $row <= $rowCount; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(24);
                }
            },
        ];
    }
}
