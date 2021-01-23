<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class VoucherController extends Controller 
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

    public function list(Request $request , $page = 1)
    {     
        try {        
            $vouchers = App\Voucher::where('status' , true);

            if($request->has('customer_id')){
                $vouchers = $vouchers->where('customer_id' , $request->input('customer_id'));
            }

            if($request->has('campaign_id')){
                $vouchers = $vouchers->where('campaign_id' , $request->input('campaign_id'));
            }
            
            $this->response["totalItems"] = $vouchers->count();
            $this->response['data'] = $vouchers->with(['customer'])->latest()
                                                ->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            
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
            $vouchers = App\Voucher::where('status' , true);

            if(isset($filter['searchText'])){
                $vouchers = $vouchers->where(function($query) use($filter){
                                            $query->where('code' , 'LIKE' , '%'.$filter['searchText'].'%');
                                        });
            }   
            
            if(isset($filter['customer_id'])){
                $vouchers = $vouchers->where('customer_id' , $filter['customer_id']);
            }    


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $vouchers = $vouchers->whereDate('created_at' , '>=' , $start_date)
                                                 ->whereDate('created_at' , '<=' , $end_date);
            }
            
            $this->response["totalItems"] = $vouchers->count();

            if(isset($filter['filter_by'])){
                $vouchers = $vouchers->orderBy($filter['filter_by'] , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"]);
            } else{
                $vouchers = $vouchers->orderBy('id' , 'desc');
            }

            $this->response['data'] = $vouchers->with(['customer'])->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
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
            'voucher_code' => 'required|max:255',
            'purchase_id' => 'required',
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $purchase = App\Purchase::find($request->input('purchase_id'));
                $voucher = new App\Voucher();
                $voucher->code = $request->input('voucher_code');
                $voucher->purchase_id = $request->input('purchase_id');
                $voucher->customer_id =  $purchase->customer_id;
                $voucher->status = true;
                $voucher->save();
                

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $voucher;
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
            $voucher = App\Voucher::with(['customer'])->find($id);
            $this->response["data"] = $voucher;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function redeemed($voucher_id)
    {
        try{
            $voucher = App\Voucher::find($voucher_id);
            $voucher->update(['redeemed_at' => Carbon::now()]);
            $this->response["data"] = $voucher;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update(Request $request, $company_id , $id)
    {
    }

    public function destroy($id)
    {
        try{
            $voucher = App\Voucher::find($id);
            if(!isset($voucher)){
                $this->response['message'] = "Voucher not found.!.";
            }
            $voucher->update(['status' => false]);

            $this->response["message"] = "Voucher remove successfully.!";
            $this->response["data"] = $voucher->with(['admin']);
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}