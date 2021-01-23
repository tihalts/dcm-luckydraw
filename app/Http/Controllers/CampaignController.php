<?php 

namespace App\Http\Controllers;

use App;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Imports\LossDataImport;


class CampaignController extends Controller 
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
            $campaigns = App\Campaign::where('status' , true);
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
            $campaigns = App\Campaign::where('status' , true);

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
            'campaign_name' => 'required|max:255',
            'campaign_type' => 'required|max:255',
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
                $campaign->min_amount = $request->has('min_amount') ? $request->input('min_amount') : 0;
                $campaign->max_amount = $request->has('max_amount') ? $request->input('max_amount') : 0;
                $campaign->per_day_limit = $request->has('per_day_limit') ? $request->input('per_day_limit') : 0;
                $campaign->per_customer_limit = $request->has('per_customer_limit') ? $request->input('per_customer_limit') : 0;
                $campaign->max_limit = $request->has('max_limit') ? $request->input('max_limit') : 0;
                $campaign->campaign_type = $request->input('campaign_type');
                $campaign->expires_at = $request->input('expires_at');
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
              'min_amount' => $campaign->min_amount ,
              'max_amount' => $campaign->max_amount ,
              'per_day_limit' => $campaign->per_day_limit ,
              'per_customer_limit' => $campaign->per_customer_limit ,
              'max_limit' => $campaign->max_limit,
              'winner_image_url' => asset('storage/' .$campaign->winner_image),
              'background_image_url' => asset('storage/' .$campaign->background_image),
              'campaign_type' => $campaign->campaign_type ,
              'expires_at' => $campaign->expires_at ,
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
            'campaign_type' => 'required|max:255',
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
                $campaign->min_amount = $request->has('min_amount') ? $request->input('min_amount') : $request->input('min_amount');
                $campaign->max_amount = $request->has('max_amount') ? $request->input('max_amount') : $request->input('max_amount');
                $campaign->per_day_limit = $request->has('per_day_limit') ? $request->input('per_day_limit') : $request->input('per_day_limit');
                $campaign->per_customer_limit = $request->has('per_customer_limit') ? $request->input('per_customer_limit') : $request->input('per_customer_limit');
                $campaign->campaign_type = $request->input('campaign_type');
                $campaign->max_limit = $request->has('max_limit') ? $request->input('max_limit') : $campaign->max_limit;
                $campaign->expires_at = $request->input('expires_at');
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
            $campaign = App\Campaign::find($id);
            if(!isset($campaign)){
                $this->response['message'] = "Campaign not found.!.";
            }
            $campaign->update(['status' => false]);

            $this->response["message"] = "Campaign remove successfully.!";
            $this->response["data"] = $campaign->with(['admin']);
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function fetch_template($campaign_id)
    {
        try{
            $template = App\CampaignTemplate::where('campaign_id' , $campaign_id)->first();
            $toArray = [
                'id' => isset($template) ? $template->id : null,
                'campaign_id' => isset($template) ? $template->campaign_id : null,
                'sms' => isset($template) ? $template->sms : null,
                'email' => isset($template) ? $template->email : null,
            ];
            $this->response["data"] = $toArray;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update_template(Request $request , $campaign_id)
    {
        try{
            $template = App\CampaignTemplate::where('campaign_id' , $campaign_id)->first();
            if(!isset($template)){
                $template = new App\CampaignTemplate();
                $template->campaign_id = $campaign_id;
            }
            $template->sms = $request->has('sms') ? $request->input('sms') : null;
            $template->email = $request->has('email') ? $request->input('email') : null;
            $template->save();
            $this->response["data"] = [
                'id' => $template->id,
                'campaign_id' => $template->campaign_id,
                'sms' => $template->sms,
                'email' => $template->email,
            ];
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function loss(Request $request)
    {
        $data = Excel::import(new LossDataImport() , $request->file('gift_file'));
        return response()->json($data, 200);
    }
}