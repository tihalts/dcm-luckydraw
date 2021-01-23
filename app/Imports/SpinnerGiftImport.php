<?php
namespace App\Imports;

use App\SpinGift;
use App\Spinner;
use App\SpinGiftItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SpinnerGiftsImport implements ToCollection , WithHeadingRow
{
    private $spinner = null; 
    
    public function __construct(Spinner $spinner) { 
        $this->spinner = $spinner; 
    }

    public function collection(Collection $rows)
    {
        $spinner = $this->spinner;
        foreach ($rows as $row) 
        {
            if(!isset($row['name']) || !isset($row['code']) || !isset($row['start'])) continue;
            $gift = SpinGift::updateOrCreate([
                    'name' => $row['name'], 
                    'spinner_id' => $spinner->id
                ] , 
                [
                    //'code' => str_random(6),
                    'description' => isset($row['description']) ? $row['description'] : $row['name'],
                    'start_at' => $spinner->start_at,
                    'no_of_gifts' => 0,
                    'end_at' => $spinner->end_at,
                    'status' => true
                ]);   
             if(SpinGiftItem::where('code' , $row['code'])->count() == 0){
                SpinGiftItem::create([
                    'code' => $row['code'],
                    'gift_at' => $this->transformDate($row['start']),
                    'gift_id' => $gift->id,
                    'status' => true
                 ]);
             }
        }
    }

    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}