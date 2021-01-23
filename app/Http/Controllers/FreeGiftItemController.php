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


class FreeGiftItemController extends Controller 
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
            $giftitems = App\GiftItem::where('status' , true)->where('gift_id' , $request->gift_id);
            $this->response['data'] = $giftitems->with(['gift'])->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = $giftitems->count();
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
            $giftitems = App\GiftItem::where('status' , true)->where('gift_id' , $request->gift_id);

            if(isset($filter['item_status'])){

                if($filter['item_status'] == 'ungifted'){
                    $giftitems = $giftitems->whereNull('gifted_at');
                }

                if($filter['item_status'] == 'gifted'){
                    $giftitems = $giftitems->whereNotNull('gifted_at');
                }

            }

            if(isset($filter['searchText'])){
                $giftitems = $giftitems->where(function($query) use($filter){
                                $query->where('code' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            } 

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $giftitems = $giftitems->whereBetween('gift_at' ,[$start_date , $end_date]);
            }

            $this->response["totalItems"] = $giftitems->count();
            $this->response['data'] = $giftitems->with(['gift'])->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
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

    public function items(Request $request, $page = 1)
    {     
        try {        
            $giftitems = App\GiftItem::where('status' , true)->where('gift_id' , $request->gift_id);
            $filter = $request->all(); 
            if(isset($filter['item_status'])){

                if($filter['item_status'] == 'ungifted'){
                    $giftitems = $giftitems->whereNull('gifted_at');
                }

                if($filter['item_status'] == 'gifted'){
                    $giftitems = $giftitems->whereNotNull('gifted_at');
                }

            }

            if(isset($filter['searchText'])){
                $giftitems = $giftitems->where('code', 'LIKE' , '%'.$filter['searchText'].'%');
            }

            $this->response['data'] = $giftitems->with(['gift'])->get();

            $this->response["currentPage"] = 1;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = $giftitems->count();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function updateItems(Request $request)
    {
        $giftitems = $request->has('items') ? $request->input('items') : [];

        try{

            foreach ($giftitems as $item) {
                $giftitem = App\GiftItem::find($item['id']);
                $giftitem->code = $item['code'];
                $giftitem->gift_at = $item['gift_at'];
                $giftitem->save();
            }

            $this->response["message"] = "Gift Item(s) update successfully.!";
            $this->response["data"] = [];
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function deleteItems(Request $request)
    {
        $ids = $request->has('items') ? $request->input('items') : [];
        try{
            $giftitems = App\GiftItem::whereIn('id' , $ids)->get();
            if(!isset($giftitems)){
                $this->response['message'] = "Gift Item(s) not found.!.";
            }
            App\GiftItem::whereIn('id' , $ids)->update(['status' => false]);

            $this->response["message"] = "Gift Item(s) remove successfully.!";
            $this->response["data"] = [];
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|max:255|unique:gift_items,code',
            'gift_at' => 'required',
            'gift_id' => 'required|exists:gifts,id',
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $giftitem = new App\GiftItem();
                $giftitem->code = $request->input('code');
                $giftitem->gift_at = $request->input('gift_at');
                $giftitem->gift_id = $request->input('gift_id');
                $giftitem->save();
                

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $giftitem;
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
            $giftitem = App\GiftItem::find($id);
            $toArray = [
              'id' => $giftitem->id,
              'code' => $giftitem->code ,
              'gifted_at' => $giftitem->gifted_at ,
              'gift_at' =>  $giftitem->gift_at,
              'gift_id' => $giftitem->gift_id ,
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
            'code' => 'required|unique:gift_items,code,'.$id ,
            'gift_at' => 'required',
            'gift_id' => 'required|exists:gifts,id',       
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $giftitem = App\GiftItem::find($id);
                $giftitem->code = $request->input('code');
                $giftitem->gift_at = $request->input('gift_at');
                $giftitem->gift_id = $request->input('gift_id');
                $giftitem->save();

                $this->response["message"] = "";
                $this->response["data"] = $giftitem;
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
            $giftitem = App\GiftItem::find($id);
            if(!isset($giftitem)){
                $this->response['message'] = "Gift Item not found.!.";
            }
            $giftitem->update(['status' => false]);

            $this->response["message"] = "Campaign remove successfully.!";
            $this->response["data"] = $giftitem->with(['admin']);
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
            $giftitems = App\GiftItem::where('status' , true)
                                   ->where('giftitem_type' , 'scratch_card')
                                   ->where(function($query){
                                      $query->whereDate('expires_at' , '>=' , Carbon::now())
                                            ->orWhereNull('expires_at');
                                   });

            if(isset($filter['searchText'])){
                $giftitems = $giftitems->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }  
            $this->response['data'] = $giftitems->get(['id' , 'name']);
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function ItemReport($gift_id)
    {
        try {       
            $this->response['data']["totalGiftItems"] = App\GiftItem::where('status' , true)
                                                                        ->whereHas( 'gift' , function($query) use($gift_id){
                                                                            $query->where("status" , 1)
                                                                                  ->where("id" , $gift_id);
                                                                        })
                                                                        ->count();

            $this->response['data']["totalUnGiftItems"] = App\GiftItem::where('status' , true)
                                                                        ->whereHas( 'gift' , function($query) use($gift_id){
                                                                            $query->where("status" , 1)
                                                                                  ->where("id" , $gift_id);
                                                                        })
                                                                        ->whereNull('gifted_at')
                                                                        ->count();

            $this->response['data']["todayGiftItems"] = App\GiftItem::where('status' , true)
                                                                        ->whereHas( 'gift' , function($query) use($gift_id){
                                                                            $query->where("status" , 1)
                                                                                  ->where("id" , $gift_id);
                                                                        })
                                                                        ->whereDate('gift_at' , Carbon::now())
                                                                        ->count();

            $this->response['data']["todayGiftedItems"] = App\GiftItem::where('status' , true)
                                                                        ->whereDate('gift_at' , Carbon::now())
                                                                        ->whereHas( 'gift' , function($query) use($gift_id){
                                                                            $query->where("status" , 1)
                                                                                  ->where("id" , $gift_id);
                                                                        })
                                                                        ->whereNotNull('gifted_at')
                                                                        ->count();
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function search_gifts(Request $request)
    {
        try {        
            $filter = $request->all();
            //$giftitems = App\GiftItem::whereNull('gifted_at')->where('status' , true)->where('gift_id' , $request->gift_id);
            $this->response['data'] = App\GiftItem::whereNull('gifted_at')
                                                    ->where('status' , true)
                                                    ->where('gift_id' , $request->gift_id)
                                                    ->where('code' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                    ->with(['gift'])
                                                    ->whereDate('gift_at' , '<=' , now())
                                                    ->get();

            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}