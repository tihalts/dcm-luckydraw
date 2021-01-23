<?php
namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchasesPDFExport implements FromView
{
    protected $purchases;

    public function __construct(array $purchases)
    {
        $this->purchases = $purchases;
    }


    public function view(): View
    {
        $headers = isset($this->purchases[0]) ? array_keys($this->purchases[0]) : [];
        return view('exports.purchase')->with([ 'headers' => $headers , 'purchases' => $this->purchases ]);
    }
}