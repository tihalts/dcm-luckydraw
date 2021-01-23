<?php
namespace App\Imports;

use App\Gift;
use App\Campaign;
use App\GiftItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CampaignGiftsImport implements ToCollection , WithHeadingRow
{
    private $campaign = null; 
    
    public function __construct(Campaign $campaign) { 
        $this->campaign = $campaign; 
    }

    public function collection(Collection $rows)
    {
        $campaign = $this->campaign;
        foreach ($rows as $row) 
        {
            if(!isset($row['name']) || !isset($row['code']) || !isset($row['start'])) continue;
            $gift = Gift::updateOrCreate([
                    'name' => $row['name'], 
                    'campaign_id' => $campaign->id
                ] , 
                [
                    'code' => isset($row['gift_code']) ? $row['gift_code'] : null,
                    'description' => isset($row['description']) ? $row['description'] : $row['name'],
                    'start_at' => $campaign->start_at,
                    'no_of_gifts' => 0,
                    'end_at' => $campaign->end_at,
                    'status' => true
                ]);   
             if(GiftItem::where('code' , $row['code'])->count() == 0){
                 GiftItem::create([
                    'code' => $row['code'],
                    'gift_at' => $this->transformDate($row['start']),
                    'gift_id' => $gift->id,
                    'status' => true
                 ]);
             }else{
                GiftItem::where('code' , $row['code'])->whereNull('gifted_at')->update(['gift_id' => $gift->id , 'gift_at' => $this->transformDate($row['start'])]);
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