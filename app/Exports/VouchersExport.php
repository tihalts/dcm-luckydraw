<?php
namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VouchersExport implements FromArray, WithHeadings
{
    protected $vouchers;

    public function __construct(array $vouchers)
    {
        $this->vouchers = $vouchers;
    }

    public function array(): array
    {
        return $this->vouchers;
    }

    public function headings(): array
    {
        return [
            'Campaign Name',
            'Customer Name',
            'Customer CPR',
            'Customer Email',
            'Customer mobile',
            'Voucher Code',
            'Voucher Amount',
            'Provider By',
            'Provider Role',
            'Redeemed At',
            'Created At'
        ];
    }
}