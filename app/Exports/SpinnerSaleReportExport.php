<?php
namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SpinnerSaleReportExport implements FromArray, WithHeadings
{
    protected $purchases;

    public function __construct(array $purchases)
    {
        $this->purchases = $purchases;
    }

    public function array(): array
    {
        return $this->purchases;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Total Purchase',
            'Total Amount',
        ];
    }
}