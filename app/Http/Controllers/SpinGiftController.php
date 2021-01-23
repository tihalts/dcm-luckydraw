<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;


class SpinGiftController extends Controller 
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

    public function list(Request $request, $page = 1)
    {     
        try {        
            $campaigns = App\SpinGift::withCount(["items as total_gifts" , "items as gifted_items" => function ($query) {
                                        $query->whereNotNull('gifted_at');
                                    }])
                                    ->where('status' , true)->where('spinner_id' , $request->spinner_id);

            $this->response['data'] = $campaigns->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->orderBy('id' , "desc")
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
            $campaigns = App\SpinGift::where('status' , true)
                                   ->where('spinner_id' , $request->spinner_id)
                                   ->withCount(["items as total_gifts" , "items as gifted_items" => function ($query) {
                                        $query->whereNotNull('gifted_at');
                                    }]);

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
            'gift_name' => 'required|max:255',
            'spinner_id' => 'required',
            'code' => 'required|unique:spin_gifts,code',
            //'no_of_gifts' => 'required|numeric',  
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $campaign = new App\SpinGift();
                $campaign->name = $request->input('gift_name');
                $campaign->code = $request->input('code');
                $campaign->no_of_gifts = 0;//$request->input('no_of_gifts');
                $campaign->description = $request->has('description') ? $request->input('description') : null;                
                $campaign->spinner_id = $request->input('spinner_id');
                // $campaign->start_at = $request->input('start_at');
                // $campaign->end_at = $request->input('end_at');

                if($request->hasFile('gift_img')){
                    $campaign->image = Storage::disk('public')->putFileAs(
                        'campaigns', $request->file('gift_img'),  Uuid::uuid1().'.'.$request->file('gift_img')->getClientOriginalExtension()
                    );
                }

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
            $campaign = App\SpinGift::find($id);
            $toArray = [
              'gift_name' => $campaign->name ,
              'description' => $campaign->description ,
              'no_of_gifts' => $campaign->no_of_gifts ,
              'code' => $campaign->code ,
              'gift_img' =>  isset($campaign->image) ? asset('storage/' .$campaign->image) : null,
              'spinner_id' => $campaign->spinner_id ,
              'start_at' => $campaign->start_at ,
              'end_at' => $campaign->end_at ,
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
            'gift_name' => 'required|max:255',
            'code' => 'required|unique:spin_gifts,code,'.$id ,
            //'no_of_gifts' => 'required|numeric',         
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $campaign = App\SpinGift::find($id);
                $campaign->name = $request->input('gift_name');
                $campaign->code = $request->input('code');
                $campaign->no_of_gifts = 0;//$request->input('no_of_gifts');
                $campaign->description = $request->has('description') ? $request->input('description') : null;                
                //$campaign->spinner_id = $request->input('spinner_id');
                // $campaign->start_at = $request->input('start_at');
                // $campaign->end_at = $request->input('end_at');

                if($request->hasFile('gift_img')){
                    $campaign->image = Storage::disk('public')->putFileAs(
                        'campaigns', $request->file('gift_img'), Uuid::uuid1().'.'.$request->file('gift_img')->getClientOriginalExtension()
                    );
                }
 
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
            $campaign = App\SpinGift::find($id);
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

    public function fetchCampaigns(Request $request)
    {
        try {      
            $filter = $request->all(); 
            $campaigns = App\SpinGift::where('status' , true)
                                   ->where('campaign_type' , 'scratch_card')
                                   ->where(function($query){
                                      $query->whereDate('expires_at' , '>=' , Carbon::now())
                                            ->orWhereNull('expires_at');
                                   });

            if(isset($filter['searchText'])){
                $campaigns = $campaigns->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }  
            $this->response['data'] = $campaigns->get(['id' , 'name']);
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function ItemReport($spinner_id)
    {
        try {       
            $this->response['data']["totalGiftItems"] = App\SpinGiftItem::where('status' , true)
                                                                        ->whereHas('gift' , function($query) use($spinner_id) {
                                                                            $query->where("spinner_id" , $spinner_id)
                                                                                  ->where("status" , 1);
                                                                        })
                                                                        ->count();

            $this->response['data']["totalUnGiftItems"] = App\SpinGiftItem::where('status' , true)
                                                                        ->whereHas('gift' , function($query) use($spinner_id) {
                                                                            $query->where("spinner_id" , $spinner_id)
                                                                                ->where("status" , 1);
                                                                        })
                                                                        ->whereNull('gifted_at')
                                                                        ->count();

            $this->response['data']["todayGiftItems"] = App\SpinGiftItem::where('status' , true)
                                                                        ->whereHas( 'gift' , function($query) use($spinner_id) {
                                                                            $query->where("spinner_id" , $spinner_id)
                                                                                  ->where("status" , 1);
                                                                        })
                                                                        ->whereNull('gifted_at')
                                                                        ->whereDate('gift_at' , Carbon::now())
                                                                        ->count();

            $this->response['data']["todayGiftedItems"] = App\SpinGiftItem::where('status' , true)
                                                                        ->whereDate('gift_at' , Carbon::now())
                                                                        ->whereHas('gift' , function($query) use($spinner_id) {
                                                                            $query->where("spinner_id" , $spinner_id)
                                                                                  ->where("status" , 1);
                                                                        })
                                                                        ->whereNotNull('gifted_at')
                                                                        ->count();
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}