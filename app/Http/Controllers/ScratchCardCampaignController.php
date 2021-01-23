<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Excel;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Imports\CampaignGiftsImport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mockery\CountValidator\Exact;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;


class ScratchCardCampaignController extends Controller 
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
            $campaigns = App\Campaign::where('campaign_type' , 'scratch_card');
            $campaigns = $campaigns->withCount('winners')->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();

            $this->response['data'] = $campaigns->setAppends(['total_gift_items' , 'total_ungift_items' , 'today_gift_items', 'today_gifted_items' , 'yesterday_gift_items', 'yesterday_gifted_items']);
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
            $campaigns = App\Campaign::where('campaign_type' , 'scratch_card');

            if(isset($filter['searchText'])){
                $campaigns = $campaigns->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }    
            $this->response["totalItems"] = $campaigns->count();
            $campaigns = $campaigns->withCount('winners')->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
                                            ->offset(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
                                            ->limit(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] : $this->filter["itemPerPage"])
                                            ->get();

            $this->response['data'] = $campaigns->setAppends(['total_gift_items' , 'total_ungift_items' , 'today_gift_items', 'today_gifted_items' , 'yesterday_gift_items', 'yesterday_gifted_items']);
                                            
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
                $campaign->campaign_type = 'scratch_card';
                $campaign->start_at = $request->input('start_at');
                $campaign->end_at = $request->input('end_at'); 
                $campaign->send_sms = $request->has('send_sms') ? $request->input('send_sms')  : 0;  
                $campaign->send_email = $request->has('send_email') ? $request->input('send_email')  : 0; 
                
                $data = [
                    'amount' => $request->has('amount') ? $request->input('amount') : 0,
                    'day_limit' => $request->has('day_limit') ? $request->input('day_limit') : 0,
                    'customer_limit' => $request->has('customer_limit') ? $request->input('customer_limit') : 0,
                    'max_limit' => $request->has('max_limit') ? $request->input('max_limit') : 0,
                    'max_winners' => $request->has('max_winners') ? $request->input('max_winners') : 0,
                    'probability' => $request->has('probability') ? $request->input('probability') : 10,
                ];

                if($request->hasFile('background_img')){
                    $data['background_image'] = Storage::disk('public')->putFileAs(
                        'campaigns', $request->file('background_img'), Uuid::uuid1().'.'.$request->file('background_img')->getClientOriginalExtension()
                    );
                }

                if($request->hasFile('looser_img')){
                    $data['looser_image'] = Storage::disk('public')->putFileAs(
                        'campaigns', $request->file('looser_img'), Uuid::uuid1().'.'.$request->file('looser_img')->getClientOriginalExtension()
                    );
                }

                if($request->hasFile('winner_img')){
                    $data['winner_image'] = Storage::disk('public')->putFileAs(
                        'campaigns', $request->file('winner_img'), Uuid::uuid1().'.'.$request->file('winner_img')->getClientOriginalExtension()
                    );
                }

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
              'winner_image_url' => isset($campaign->data['winner_image']) ? asset('storage/' .$campaign->data['winner_image']) : null,
              'looser_image_url' => isset($campaign->data['looser_image']) ? asset('storage/' .$campaign->data['looser_image']) : null,
              'background_image_url' => isset($campaign->data['background_image']) ? asset('storage/' .$campaign->data['background_image']) : null,
              'probability' => isset($campaign->data['probability']) ? $campaign->data['probability'] : 10,
              'campaign_type' => $campaign->campaign_type ,
              'max_winners' => 0,//$campaign->data['max_winners'],
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
                $campaign->campaign_type = 'scratch_card';
                $campaign->start_at = $request->input('start_at');
                $campaign->end_at = $request->input('end_at');  
                $campaign->send_sms = $request->has('send_sms') ? $request->input('send_sms')  : 0;  
                $campaign->send_email = $request->has('send_email') ? $request->input('send_email')  : 0;  

                $data = $campaign->data;

                $data['amount'] = $request->has('amount') ? $request->input('amount') : $data['amount'];
                $data['day_limit'] = $request->has('day_limit') ? $request->input('day_limit') : $data['day_limit'];
                $data['customer_limit'] = $request->has('customer_limit') ? $request->input('customer_limit') : $data['customer_limit'];
                $data['max_limit'] = $request->has('max_limit') ? $request->input('max_limit') : $data['max_limit'];
                $data['max_winners'] = $request->has('max_winners') ? $request->input('max_winners') : $data['max_winners'];
                $data['probability'] = $request->has('probability') ? $request->input('probability') : 10;
                

                if($request->hasFile('background_img')){
                    $data['background_image'] = Storage::disk('public')->putFileAs(
                        'campaigns', $request->file('background_img'), Uuid::uuid1().'.'.$request->file('background_img')->getClientOriginalExtension()
                    );
                }

                if($request->hasFile('looser_img')){
                    $data['looser_image'] = Storage::disk('public')->putFileAs(
                        'campaigns', $request->file('looser_img'), Uuid::uuid1().'.'.$request->file('looser_img')->getClientOriginalExtension()
                    );
                }

                if($request->hasFile('winner_img')){
                    $data['winner_image'] = Storage::disk('public')->putFileAs(
                        'campaigns', $request->file('winner_img'), Uuid::uuid1().'.'.$request->file('winner_img')->getClientOriginalExtension()
                    );
                }

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
                                   ->where('campaign_type' , 'scratch_card')
                                   ->where(function($query){
                                        $query->whereDate('start_at' , '<=' , Carbon::now())
                                            ->whereDate('end_at' , '>=' , Carbon::now());
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

    public function move_unused_gifts($id)
    {
        try {      
            $unsed_gifts = App\GiftItem::where('status' , true)
                                        ->whereHas('gift'  ,function($query) use($id){
                                            $query->where('campaign_id' , $id)
                                                  ->where('status' , true);
                                        })
                                        ->whereNull('gifted_at')
                                        ->whereDate('gift_at' , '<' ,Carbon::now())
                                        ->update(['gift_at' => Carbon::now()]);

            $this->response['data'] = $unsed_gifts;
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function import(Request $request)
    {
        try {  
            $campaign = App\Campaign::find($request->input('campaign_id')); 
            // if($request->hasFile('gift_file')){
            //     $path = $request->file('gift_file')->getRealPath();

            //     $data = Excel::load($path)->get(); 
            //     if($data->count() > 0)
            //     {
            //         foreach($data->toArray() as $key => $row)
            //         {  
            //             if(!isset($row['name']) || !isset($row['code']) || !isset($row['start'])) continue;
            //             $gift = App\Gift::updateOrCreate([
            //                     'name' => $row['name'], 
            //                     'campaign_id' => $campaign->id
            //                 ] , 
            //                 [
            //                     //'code' => str_random(6),
            //                     'description' => isset($row['description']) ? $row['description'] : $row['name'],
            //                     'start_at' => $campaign->start_at,
            //                     'no_of_gifts' => 0,
            //                     'end_at' => $campaign->end_at,
            //                     'status' => true
            //                 ]);    
                            
            //              if(App\GiftItem::where('code' , $row['code'])->count() == 0){
            //                  App\GiftItem::create([
            //                     'code' => $row['code'],
            //                     'gift_at' => $row['start'],
            //                     'gift_id' => $gift->id,
            //                     'status' => true
            //                  ]);
            //              }
            //         }
            //     }
            // }
            $data = Excel::import(new CampaignGiftsImport($campaign) , $request->file('gift_file'));
            $this->response['data'] = $data;
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }      
    }
}