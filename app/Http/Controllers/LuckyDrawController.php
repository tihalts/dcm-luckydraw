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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;


class LuckyDrawController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "itemPerPage" => 15
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
            $luck_draws = new App\LuckyDraw();   
            $this->response["totalItems"] = $luck_draws->count();          
            $this->response['data'] = $luck_draws->latest()
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
            $lucky_draws = new App\LuckyDraw();

            if(isset($filter['searchText'])){
                $lucky_draws = $lucky_draws->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
            }    

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $lucky_draws = $lucky_draws->whereDate('created_at' , '>=' , $start_date)
                                                 ->whereDate('created_at' , '<=' , $end_date);
            }

            $this->response["totalItems"] = $lucky_draws->count();

            if(isset($filter['filter_by'])){
                $lucky_draws = $lucky_draws->orderBy($filter['filter_by'] , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"]);
            } else{
                $lucky_draws = $lucky_draws->orderBy('id' , 'desc');
            }

            $this->response['data'] = $lucky_draws->offset($filter["itemPerPage"] ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
                                                    ->limit($filter["itemPerPage"] ? $filter["itemPerPage"] : $this->filter["itemPerPage"])
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
            'description' => 'required|max:255',
            'start_at' => 'sometimes|required|date',
            'end_at' => 'sometimes|required|date',
            'no_of_winners' => 'required|numeric',
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $lucky_draw = new App\LuckyDraw();
                $lucky_draw->name = $request->input('name');
                $lucky_draw->description = $request->input('description');
                $lucky_draw->no_of_winners = $request->input('no_of_winners');

                if($request->has('is_allow_old_winders')){
                    $lucky_draw->is_allow_old_winders = $request->is_allow_old_winders;
                }

                if($request->has('is_allow_repeat_user')){
                    $lucky_draw->is_allow_repeat_user = $request->is_allow_repeat_user;
                }

                if($request->has('start_at')){
                    $lucky_draw->start_at = $request->input('start_at');
                }

                if($request->has('end_at')){
                    $lucky_draw->end_at = $request->input('end_at');
                }
                $lucky_draw->user_id = Auth::id();
                $lucky_draw->save();

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $lucky_draw;
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
            $lucky_draw = App\LuckyDraw::find($id);
            $this->response["data"] = $lucky_draw;
            
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
            'description' => 'required|max:255',
            'start_at' => 'sometimes|required|date',
            'end_at' => 'sometimes|required|date',
            'no_of_winners' => 'required|numeric',
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $lucky_draw = App\LuckyDraw::find($id);
                $lucky_draw->name = $request->input('name');
                $lucky_draw->description = $request->input('description');
                $lucky_draw->no_of_winners = $request->input('no_of_winners');

                if($request->has('is_allow_old_winders')){
                    $lucky_draw->is_allow_old_winders = $request->is_allow_old_winders;
                }

                if($request->has('is_allow_repeat_user')){
                    $lucky_draw->is_allow_repeat_user = $request->is_allow_repeat_user;
                }

                if($request->has('start_at')){
                    $lucky_draw->start_at = $request->input('start_at');
                }

                if($request->has('end_at')){
                    $lucky_draw->end_at = $request->input('end_at');
                }
                $lucky_draw->user_id = Auth::id();
                $lucky_draw->save();

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $lucky_draw;
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function fetch_setting($id)
    {
        try{
            $template = App\RaffleDrawSetting::where('lucky_draw_id' , $id)->first();
            $toArray = [
                'id' => isset($template) ? $template->id : null,
                'lucky_draw_id' => isset($template) ? $template->campaign_id : null,
                'sms' => isset($template) ? $template->sms : null,
                'email' => isset($template) ? $template->email : null,
                'image' => isset($template->image) ? asset('storage/' .$template->image) : null,
                'send_sms' => isset($template) ? $template->send_sms : 0,
                'send_email' => isset($template) ? $template->send_email : 0,
                'min_amount' => isset($template) ? $template->min_amount : 0,
                'max_points' => isset($template) ? $template->max_points : 0,
            ];
            $this->response["data"] = $toArray;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update_settings(Request $request , $id)
    {
        try{
            $template = App\RaffleDrawSetting::where('lucky_draw_id' , $id)->first();

            if(!isset($template)){
                $template = new App\RaffleDrawSetting();
                $template->lucky_draw_id = $id;
            }

            $template->sms = $request->has('sms') ? $request->input('sms') : null;
            $template->email = $request->has('email') ? $request->input('email') : null;
            $template->send_sms = $request->has('send_sms') ? $request->input('send_sms') : 0;
            $template->send_email = $request->has('send_email') ? $request->input('send_email') : 0;
            $template->min_amount = $request->has('min_amount') ? $request->min_amount : 0;
            $template->max_points = $request->has('max_points') ? $request->max_points : 0;
            
            $template->save();

            if($request->hasFile('background_img')){
                $url = Storage::disk('public')->putFileAs(
                    'raffledraw', $request->file('background_img'),  Uuid::uuid1().'.'.$request->file('background_img')->getClientOriginalExtension()
                );
                $template->update(['image' => $url]);
            }

            $this->response["data"] = [
                'id' => $template->id,
                'lucky_draw_id' => $template->lucky_draw_id,
                'sms' => $template->sms,
                'email' => $template->email,
                'image' => isset($template->image) ? asset('storage/' .$template->image) : null,
            ];
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function destroy($id)
    {
        try{
            $lucky_draw = App\LuckyDraw::find($id);
            if(!isset($luck_draw)){
                $this->response["message"] = 'Raffle draw not found.!';
            }
            $lucky_draw->update(['status' => false]);
            $this->response["message"] = 'Raffle draw remove successfull.!';
            $this->response["data"] = $lucky_draw;
            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function activated($id)
    {
        try{
            $lucky_draw = App\LuckyDraw::find($id);
            if(!isset($lucky_draw)){
                $this->lucky_draw["message"] = 'Raffle draw not found.!';
            }
            $lucky_draw->update(['status' => true]);
            $this->response["message"] = 'Raffle draw activated.!';
            $this->response["data"] = $lucky_draw;
            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}