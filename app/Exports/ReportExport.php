<?php
namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromArray, WithHeadings
{
    protected $reports;
    protected $headers = [];

    public function __construct(array $reports)
    {
        $this->reports = $reports;
        $this->headers = isset($this->reports[0]) ? array_keys($this->reports[0])  : [];
    }

    public function array(): array
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return $this->headers;
    }
}