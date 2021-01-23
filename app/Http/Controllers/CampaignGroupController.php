<?php 

namespace App\Http\Controllers;

use App;
use PDF;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use App\Exports\FreeGiftVoucherExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;


class CampaignGroupController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "itemPerPage" => 15,
        "orderby" => "desc",
        "active" => "all",
    ];

    public function __construct()
    {        
        
    }
    
    public function index()
    {

    }

    public function list($page = 1)
    {     
        try {        
            $campaigns = App\CampaignGroup::where('status' , true);
            $this->response['data'] = $campaigns->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = $campaigns->count();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function search(Request $request , $page = 1)
    {
        try {      
            $filter = $request->all(); 
            $campaigns = App\CampaignGroup::where('status' , true);

            if(isset($filter['active'])){
                if($filter['active'] != 'all')
                   $campaigns = $campaigns->where('active_status' , $filter['active']);
            }

            if(isset($filter['searchText'])){
                $campaigns = $campaigns->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }    
            $this->response["totalItems"] = $campaigns->count();
            $this->response['data'] = $campaigns->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
                                            ->offset(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
                                            ->limit(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] : $this->filter["itemPerPage"])
                                            ->get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $filter;
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'start_at'      => 'required|date|before:end_at',
            'end_at'        => 'date|after:start_at',
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $campaign = new App\CampaignGroup();
                $campaign->name = $request->input('name');
                $campaign->description = $request->has('description') ? $request->input('description') : null;
                $campaign->start_at = $request->input('start_at') ?? null;
                $campaign->end_at = $request->input('end_at') ?? null; 
                $campaign->save();
                

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $campaign;
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function show($id)
    {

    }

    public function fetch($id)
    {
        try{
            $campaign = App\CampaignGroup::find($id);
            $toArray = [
              'name' => $campaign->name ,
              'description' => $campaign->description ,
              'start_at' => $campaign->start_at ,
              'end_at' => $campaign->end_at ,
              'status' => $campaign->status ,
            ];

            $this->response["message"] = Lang::get('');
            $this->response["data"] = $toArray;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update(Request $request , $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'start_at'      => 'required|date|before:end_at',
            'end_at'        => 'date|after:start_at',
        ]);

        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $campaign = App\CampaignGroup::find($id);
                $campaign->name = $request->input('name');
                $campaign->description = $request->has('description') ? $request->input('description') : $request->input('description');
                $campaign->start_at = $request->input('start_at') ?? null;
                $campaign->end_at = $request->input('end_at') ?? null; 
                $campaign->save();

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $campaign;
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function destroy($id)
    {
        try{
            $campaign = App\CampaignGroup::find($id);
            if(!isset($campaign)){
                $this->response['message'] = "Campaign Group not found.!.";
            }
            $campaign->update(['status' => false]);

            $this->response["message"] = "Campaign Group remove successfully.!";
            $this->response["data"] = $campaign;
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function customer_gifts(Request $request , $page = 1)
    {
        try {      
            $filter = $request->all();  
            $gifts = App\GiftVoucher::where('status' , true);

            if(isset($filter['campaign_id'])){
                $gifts = $gifts->whereHas('campaign', function (Builder $query)  use($filter){
                    $query->where('id', $filter['campaign_id']);
                });
            }else if(isset($filter['group_id'])){
                $gifts = $gifts->whereHas('campaign.group', function (Builder $query)  use($filter){
                    $query->where('id', $filter['group_id']);
                });
            }

            $gifts->with(['customer' , 'campaign.group' , 'provider' , 'gift' , 'item']);

            $this->response['data'] = $gifts->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = $gifts->count();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function reports(Request $request)
    {
        try{
            $filter = $request->all();  
            $page = isset($filter['page']) ? $filter['page'] : 1;
            $filter['type'] = isset($filter['type']) ? $filter['type'] : 'all';
            
            $gifts = App\GiftVoucher::where('status' , true);

            if(isset($filter['campaign_id'])){
                $gifts = $gifts->whereHas('campaign', function (Builder $query)  use($filter){
                    $query->where('id', $filter['campaign_id']);
                });
            }else if(isset($filter['group_id'])){
                $gifts = $gifts->whereHas('campaign.group', function (Builder $query)  use($filter){
                    $query->where('id', $filter['group_id']);
                });
            }

            if(isset($filter['customer_id'])){
                $gifts = $gifts->whereHas('customer', function (Builder $query)  use($filter){
                    $query->where('customer_id', $filter['customer_id']);
                });
            }

            if(isset($filter['country_iso'])){
                $gifts = $gifts->whereHas('customer', function (Builder $query)  use($filter){
                    $query->where('country_iso', $filter['country_iso']);
                });
            }

            if($filter['type'] == 'old'){
                $gifts = $gifts->has('customer.purchases');
            }


            if($filter['type'] == 'new'){
                $gifts = $gifts->doesnthave('customer.purchases');
            }

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $gifts = $gifts->whereBetween('created_at' ,[$start_date , $end_date]);
            }

            $gifts->with(['customer' , 'campaign.group' , 'provider' , 'gift' , 'item']);


            $this->response["totalItems"] = $gifts->count();

            $this->response['data'] = $gifts->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["new_users"] = $this->giftUsers($filter , 'new');
            $this->response["old_users"] = $this->giftUsers($filter , 'old');
            $this->response["total_users"] = $this->giftUsers($filter);
            $this->response['user'] = Auth::user();
            return $this->successResponse($this->response);

        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }       
    }

    public function giftUsers($filter , $type = 'all')
    {
        try{

            $customers = App\Customer::where('status' , true)->whereHas('FreeGifts', function (Builder $query){
                $query->where('status',true);
            });

            if(isset($filter['campaign_id'])){
                $customers = $customers->whereHas('FreeGifts.campaign', function (Builder $query)  use($filter){
                    $query->where('id', $filter['campaign_id']);
                });
            }else if(isset($filter['group_id'])){
                $customers = $customers->whereHas('FreeGifts.campaign.group', function (Builder $query)  use($filter){
                    $query->where('id', $filter['group_id']);
                });
            }

            if(isset($filter['customer_id'])){
                $customers = $customers->where('id', $filter['customer_id']);
            }

            if($type == 'old'){
                $customers = $customers->has('purchases');
            }


            if($type == 'new'){
                $customers = $customers->doesnthave('purchases');
            }

            if(isset($filter['country_iso'])){
                $customers = $customers->whereHas('customer', function (Builder $query)  use($filter){
                    $query->where('country_iso', $filter['country_iso']);
                });
            }

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $customers = $customers->whereBetween('created_at' ,[$start_date , $end_date]);
            }

            //$customers->with(['customer' , 'campaign.group' , 'provider' , 'gift' , 'item']);

            return $customers->count();
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }       
    }

    public function export(Request $request) 
    {
        try { 
            $export = [];     
            $filter = $request->all();  
            $filter['type'] = isset($filter['type']) ? $filter['type'] : 'all';
            $filter['export'] = isset($filter['export']) ? $filter['export'] : 'excel';
            $gifts = App\GiftVoucher::where('status' , true);
            
            if(isset($filter['campaign_id'])){
                $gifts = $gifts->whereHas('campaign', function (Builder $query)  use($filter){
                    $query->where('id', $filter['campaign_id']);
                });
            }else if(isset($filter['group_id'])){
                $gifts = $gifts->whereHas('campaign.group', function (Builder $query)  use($filter){
                    $query->where('id', $filter['group_id']);
                });
            }

            if(isset($filter['customer_id'])){
                $gifts = $gifts->whereHas('customer', function (Builder $query)  use($filter){
                    $query->where('customer_id', $filter['customer_id']);
                });
            }

            if($filter['type'] == 'old'){
                $gifts = $gifts->has('customer.purchases');
            }


            if($filter['type'] == 'new'){
                $gifts = $gifts->doesnthave('customer.purchases');
            }

            if(isset($filter['country_iso'])){
                $gifts = $gifts->whereHas('customer', function (Builder $query)  use($filter){
                    $query->where('country_iso', $filter['country_iso']);
                });
            }

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $gifts = $gifts->whereBetween('created_at' ,[$start_date , $end_date]);
            }

            $gifts->with(['customer','campaign.group' , 'provider' , 'gift' , 'item']);

            $gifts = $gifts->get()->toArray(); 

            foreach($gifts as $key => $voucher){
                $export[$key]['Group'] = $voucher['campaign']['group']['name'];
                $export[$key]['Campaign'] = $voucher['campaign']['name'];
                if($filter['export'] == 'pdf'){
                    $export[$key]['customer']['Name'] = $voucher['customer']['fullname'];
                    $export[$key]['customer']['CPR'] = $voucher['customer']['cpr'];
                    $export[$key]['customer']['Email'] = $voucher['customer']['email'];
                    $export[$key]['customer']['mobile'] = $voucher['customer']['mobile'];
                    $export[$key]['Provider']['Name'] = isset($voucher['provider']['fullname']) ? $voucher['provider']['fullname'] : null;
                    $export[$key]['Provider']['Role'] = isset($voucher['provider']['role']) ? $voucher['provider']['role'] : null;
                }else{
                    $export[$key]['Customer Name'] = $voucher['customer']['fullname'];
                    $export[$key]['Customer CPR'] = $voucher['customer']['cpr'];
                    $export[$key]['Customer Email'] = $voucher['customer']['email'];
                    $export[$key]['Customer mobile'] = $voucher['customer']['mobile'];
                    $export[$key]['Provider By'] = isset($voucher['provider']['fullname']) ? $voucher['provider']['fullname'] : null;
                    $export[$key]['Provider Role'] = isset($voucher['provider']['role']) ? $voucher['provider']['role'] : null;
                }
                
                $export[$key]['Voucher Code'] = $voucher['code'];
                
                $export[$key]['Created At'] = $voucher['created_at'];

            }
            if($filter['export'] == 'pdf'){
                $pdf = PDF::loadView('exports.report', ["reports" => $export]);
                //$pdf->setPaper('A4', 'landscape');
                return $pdf->download('country_customers.pdf');
            }else{
                return Excel::download(new ReportExport($export), 'gift_vouchers.xlsx');
            }
            
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}