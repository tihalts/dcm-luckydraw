<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\SpinnerGiftsImport;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class SpinnerController extends Controller 
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
            $spinwins = App\Spinner::where('status' , true);
            $spinwins = $spinwins->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();
            $this->response['data'] =  $spinwins->setAppends(['total_gift_items' , 'total_ungift_items' , 'today_gift_items', 'today_gifted_items' , 'yesterday_gift_items', 'yesterday_gifted_items']);

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = $spinwins->count();
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
            $spinwins = App\Spinner::where('status' , true);

            if(isset($filter['active'])){
                if($filter['active'] != 'all')
                   $spinwins = $spinwins->where('active_status' , $filter['active']);
            }

            if(isset($filter['searchText'])){
                $spinwins = $spinwins->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }    
            $this->response["totalItems"] = $spinwins->count();
            $this->response['data'] = $spinwins->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
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
            'spin_and_win_name' => 'required|max:255',
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $spinwin = new App\Spinner();
                $spinwin->name = $request->input('spin_and_win_name');
                $spinwin->description = $request->has('description') ? $request->input('description') : null;
                $spinwin->send_sms =  $request->has('send_sms') ? $request->input('send_sms') : null;
                $spinwin->sms =  $request->has('sms') ? $request->input('sms') : null;
                $spinwin->send_email =  $request->has('send_email') ? $request->input('send_email') : null;
                $spinwin->email =  $request->has('email') ? $request->input('email') : null;
                $spinwin->start_at = $request->has('start_at') ?  $request->input('start_at') : null;
                $spinwin->end_at = $request->has('end_at') ?  $request->input('end_at') : null;

                $data = [
                    'min_amount' => $request->has('min_amount') ? $request->input('min_amount') : 0,
                    'customer_limit' => $request->has('customer_limit') ? $request->input('customer_limit') : 0,
                    'customer_gift_limit' => $request->has('customer_gift_limit') ? $request->input('customer_gift_limit') : 0,
                    'probability' => $request->has('probability') ? $request->input('probability') : 10,
                ];

                $spinwin->data = $data;
                $spinwin->save();
                

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $spinwin;
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
            $spinwin = App\Spinner::find($id);
            $spinwin->load(['gifts' , 'items.gift']);

            $toArray = [
              'id' => $spinwin->id,
              'spin_and_win_name' => $spinwin->name ,
              'description' => $spinwin->description ,
              'min_amount' => isset($spinwin->data['min_amount']) ? $spinwin->data['min_amount'] : 0,
              'customer_limit' => isset($spinwin->data['customer_limit']) ? $spinwin->data['customer_limit'] : 0 ,
              'customer_gift_limit' => isset($spinwin->data['customer_gift_limit']) ?  $spinwin->data['customer_gift_limit'] : 0,
              'probability' => isset($spinwin->data['probability']) ? $spinwin->data['probability'] : 5,
              'send_email' => $spinwin->send_email ,
              'email' => $spinwin->email ,
              'send_sms' => $spinwin->send_sms ,
              'sms' => $spinwin->sms ,
              'start_at' => $spinwin->start_at ,
              'end_at' => $spinwin->end_at ,
            ];

            $toArray['gifts'] = $spinwin->gifts;
            $toArray['items'] = $spinwin->items;
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
            'spin_and_win_name' => 'required|max:255',
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $spinwin = App\Spinner::find($id);
                $spinwin->name = $request->input('spin_and_win_name');
                $spinwin->description = $request->has('description') ? $request->input('description') : null;
                $spinwin->send_sms =  $request->has('send_sms') ? $request->input('send_sms') : null;
                $spinwin->sms =  $request->has('sms') ? $request->input('sms') : null;
                $spinwin->send_email =  $request->has('send_email') ? $request->input('send_email') : null;
                $spinwin->email =  $request->has('email') ? $request->input('email') : null;
                $spinwin->start_at = $request->has('start_at') ?  $request->input('start_at') : null;
                $spinwin->end_at = $request->has('end_at') ?  $request->input('end_at') : null;
                $data = $spinwin->data;

                $data['min_amount'] = $request->has('min_amount') ? $request->input('min_amount') : $data['min_amount'];
                $data['customer_limit'] = $request->has('customer_limit') ? $request->input('customer_limit') : $data['customer_limit'];
                $data['customer_gift_limit'] = $request->has('customer_gift_limit') ? $request->input('customer_gift_limit') : 0;
                $data['probability'] = $request->has('probability') ? $request->input('probability') : 5;
                $spinwin->data = $data;
                $spinwin->save();


                $this->response["message"] = Lang::get('');
                $this->response["data"] = $spinwin;
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
            $spinwin = App\Spinner::find($id);
            if(!isset($spinwin)){
                $this->response['message'] = "Spinner not found.!.";
            }
            $spinwin->update(['status' => false]);

            $this->response["message"] = "Spinner remove successfully.!";
            $this->response["data"] = $spinwin->with(['admin']);
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function create_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'bg_color' => 'required|max:255',
            'text_color' => 'required|max:255',
            //'gift_id' => 'required',
            'spinner_id' => 'required',
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $spinwin = new App\SpinnerItem();
                $spinwin->name = $request->input('name');
                $spinwin->bg_color = $request->has('bg_color') ? $request->input('bg_color') : null;
                $spinwin->text_color =  $request->has('text_color') ? $request->input('text_color') : null;
                $spinwin->gift_id =  $request->has('gift_id') ? $request->input('gift_id') : null;
                $spinwin->spinner_id =  $request->input('spinner_id');
                $spinwin->save();
                
                $spinner = App\Spinner::find($request->input('spinner_id'));
                
                $this->response["message"] = Lang::get('');
                $this->response["data"] = $spinner->load('items.gift');
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function fetch_item($id)
    {
        try{
            $spinwin = App\SpinnerItem::find($id);

            $spinwin->load(['gift']);

            $toArray = [
              'id' => $spinwin->id,
              'name' => $spinwin->name ,
              'bg_color' => $spinwin->bg_color ,
              'text_color' => $spinwin->text_color ,
              'gift_id' => $spinwin->gift_id ,
              'spinner_id' => $spinwin->spinner_id ,
            ];

            $toArray['gift'] = $spinwin->with(['gift']);

            $this->response["data"] = $toArray;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update_item(Request $request , $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'bg_color' => 'required|max:255',
            'text_color' => 'required|max:255',
            //'gift_id' => 'required',
            'spinner_id' => 'required',
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $spinwin = App\SpinnerItem::find($id);
                $spinwin->name = $request->input('name');
                $spinwin->bg_color = $request->has('bg_color') ? $request->input('bg_color') : null;
                $spinwin->text_color =  $request->has('text_color') ? $request->input('text_color') : null;
                $spinwin->gift_id =  $request->has('gift_id') ? $request->input('gift_id') : null;
                $spinwin->spinner_id =  $request->input('spinner_id');
                $spinwin->save();


                $spinner = App\Spinner::find($request->input('spinner_id'));
                $this->response["message"] = Lang::get('');
                $this->response["data"] = $spinner->load('items.gift');
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function destory_item($id)
    {
        try{
            $spinwin = App\SpinnerItem::find($id);
            if(!isset($spinwin)){
                $this->response['message'] = "Spinner Item not found.!.";
            }
            $spinwin->update(['status' => false]);

            $this->response["message"] = "Spinner item remove successfully.!";
            $spinner = App\Spinner::find($spinwin->spinner_id);
            $spinner->with(['gifts' , 'items' , 'items.gift']);
            $this->response["data"] = $spinner->load('items.gift');
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function move_unused_gifts($id)
    {
        try {      
            $unsed_gifts = App\SpinGiftItem::where('status' , true)
                                        ->whereHas('gift'  ,function($query) use($id){
                                            $query->where('spinner_id' , $id)
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
            $spinner = App\Spinner::find($request->input('spinner_id')); 
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
            $data = Excel::import(new SpinnerGiftsImport($spinner) , $request->file('gift_file'));
            $this->response['data'] = $data;
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }      
    }

   
}