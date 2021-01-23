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


class RewardPointCampaignController extends Controller 
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
            $campaigns = App\Campaign::where('campaign_type' , 'reward_point');
            $this->response['data'] = $campaigns->withCount('vouchers')->offset($this->filter["itemPerPage"] * ($page - 1))
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
            $campaigns = App\Campaign::where('campaign_type' , 'reward_point');

            if(isset($filter['searchText'])){
                $campaigns = $campaigns->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }    
            $this->response["totalItems"] = $campaigns->count();
            $this->response['data'] = $campaigns->withCount('vouchers')->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
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
            'campaign_name' => 'required|max:255',
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $campaign = new App\Campaign();
                $campaign->name = $request->input('campaign_name');
                $campaign->description = $request->has('description') ? $request->input('description') : null;                
                $campaign->campaign_type = 'reward_point';
                $campaign->start_at = $request->input('start_at');
                $campaign->end_at = $request->input('end_at');  
                $campaign->send_sms = $request->has('send_sms') ? $request->input('send_sms')  : 0;  
                $campaign->send_email = $request->has('send_email') ? $request->input('send_email')  : 0; 
                
                $data = [
                    'amount' => $request->has('amount') ? $request->input('amount') : 0,
                    'day_limit' => $request->has('day_limit') ? $request->input('day_limit') : 0,
                    'customer_limit' => $request->has('customer_limit') ? $request->input('customer_limit') : 0,
                    'max_limit' => $request->has('max_limit') ? $request->input('max_limit') : 0,
                    'voucher_digits' => $request->has('voucher_digits') ? $request->input('voucher_digits') : 6,
                ];

                $campaign->data = $data;
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
            $campaign = App\Campaign::find($id);
            $toArray = [
              'campaign_name' => $campaign->name ,
              'description' => $campaign->description ,
              'amount' => $campaign->data['amount'] ,
              'day_limit' => $campaign->data['day_limit'] ,
              'customer_limit' => $campaign->data['customer_limit'] ,
              'max_limit' => $campaign->data['max_limit'] ,
              'voucher_digits' => isset($campaign->data['voucher_digits']) ? $campaign->data['voucher_digits'] : 6,
              'campaign_type' => $campaign->campaign_type ,
              'start_at' => $campaign->start_at ,
              'end_at' => $campaign->end_at ,
              'send_sms' => $campaign->send_sms ,
              'send_email' => $campaign->send_email ,
            ];
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
            'campaign_name' => 'required|max:255',
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $campaign = App\Campaign::find($id);
                $campaign->name = $request->input('campaign_name');
                $campaign->description = $request->has('description') ? $request->input('description') : $request->input('description');
                $campaign->campaign_type = 'reward_point';
                $campaign->start_at = $request->input('start_at');
                $campaign->end_at = $request->input('end_at');  
                $campaign->send_sms = $request->has('send_sms') ? $request->input('send_sms')  : 0;  
                $campaign->send_email = $request->has('send_email') ? $request->input('send_email')  : 0; 

                $data = $campaign->data;

                $data['amount'] = $request->has('amount') ? $request->input('amount') : $data['amount'];
                $data['day_limit'] = $request->has('day_limit') ? $request->input('day_limit') : $data['day_limit'];
                $data['customer_limit'] = $request->has('customer_limit') ? $request->input('customer_limit') : $data['customer_limit'];
                $data['max_limit'] = $request->has('max_limit') ? $request->input('max_limit') : $data['max_limit'];
                $data['voucher_digits'] = $request->has('voucher_digits') ? $request->input('voucher_digits') : 6;

                $campaign->data = $data;
                $campaign->save();

                $this->response["message"] = "";
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
            $campaign = App\Campaign::find($id);
            if(!isset($campaign)){
                $this->response['message'] = "Campaign not found.!.";
            }
            $campaign->update(['status' => false]);

            $this->response["message"] = "Campaign remove successfully.!";
            $this->response["data"] = $campaign;
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function activated($id)
    {
        try{
            $campaign = App\Campaign::find($id);
            if(!isset($campaign)){
                $this->campaign["message"] = 'Campaign not found.!';
            }
            $campaign->update(['status' => true]);
            $this->response["message"] = 'Campaign activated.!';
            $this->response["data"] = $campaign;
            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function fetchCampaigns(Request $request)
    {
        try {      
            $filter = $request->all(); 
            $campaigns = App\Campaign::where('status' , true)
                                   ->where('campaign_type' , 'reward_point')
                                   ->where(function($query){
                                      $query->whereDate('start_at' , '<=' , Carbon::now())
                                            ->whereDate('end_at' , '>=' , Carbon::now());
                                   });

            if(isset($filter['searchText'])){
                $campaigns = $campaigns->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }  
            $campaigns = $campaigns->get();
            $toArray = [];
            foreach ($campaigns as $campaign) {
                $toArray[] = [
                    "id" => $campaign->id,
                    "name" => $campaign->name,
                    "digits" => isset($campaign->data['voucher_digits']) ? $campaign->data['voucher_digits'] : 6,
                ];
            }
            $this->response['data'] = $toArray;
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}