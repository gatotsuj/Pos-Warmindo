<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyTransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $transactions;

    protected $month;

    public function __construct($transactions, $month)
    {
        $this->transactions = $transactions;
        $this->month = $month;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'Month',
            'Invoice',
            'Date',
            'Items',
            'Grand Total',
            'Payment Method',
            'Cashier',
        ];
    }

    public function map($transaction): array
    {
        return [
            $this->month->format('Y-m'),
            $transaction->invoice_number,
            $transaction->created_at->format('Y-m-d'),
            $transaction->items->sum('quantity'),
            $transaction->grand_total,
            $transaction->payment_method,
            $transaction->user->name,
        ];
    }

    public function title(): string
    {
        return 'Monthly Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Format currency
        ];
    }
}
