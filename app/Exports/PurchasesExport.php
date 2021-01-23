<?php
namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchasesExport implements FromArray, WithHeadings
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
            '#',
            'Shop Name',
            'Category Name',
            'Purchase Amount',
            'Customer Name',
            'Customer CPR',
            'Customer Email',
            'Customer mobile',
            'Provider By',
            'Provider Role',
            'Created At'
        ];
    }
}