<?php
namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GiftsExport implements FromArray, WithHeadings
{
    protected $gifts;

    public function __construct(array $gifts)
    {
        $this->gifts = $gifts;
    }

    public function array(): array
    {
        return $this->gifts;
    }

    public function headings(): array
    {
        return [
            'Campaign Name',
            'Gift Name',
            'Gift Code',
            'Customer Name',
            'Customer CPR',
            'Customer Email',
            'Customer mobile',
            'Provider By',
            'Provider Role',
            'Scratched At',
            'Scratch Created At'
        ];
    }
}