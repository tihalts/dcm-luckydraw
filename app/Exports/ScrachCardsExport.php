<?php
namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ScratchCardsExport implements FromArray, WithHeadings
{
    protected $ScratchCards;

    public function __construct(array $ScratchCards)
    {
        $this->ScratchCards = $ScratchCards;
    }

    public function array(): array
    {
        return $this->ScratchCards;
    }

    public function headings(): array
    {
        return [
            'Campaign Name',
            'Customer Name',
            'Customer CPR',
            'Customer Email',
            'Customer mobile',
            'Gift Name',
            'Gift Code',
            'Provider By',
            'Provider Role',
            'Scratched At',
            'Created At'
        ];
    }
}