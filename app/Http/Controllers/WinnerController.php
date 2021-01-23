<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use PDF;
use Carbon\Carbon;
use App\Events\RaffleDraw;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class WinnerController extends Controller 
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

    public function list($lucky_draw_id , $page = 1)
    {     
        try {           
            $this->response['luck_draw'] = App\LuckyDraw::find($lucky_draw_id);
            $this->response['data'] = App\Customer::whereHas('winner', function($query) use($lucky_draw_id){
                                                        $query->where('lucky_draw_id' , $lucky_draw_id)->where('status' , true);
                                                    })->where('status' , true)->with(['winner'])->get();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function fetch( $lucky_draw_id , $winner_id)
    {
        try {            
            $this->response['data'] = App\Customer::whereHas('winner', function($query) use($winner_id ,  $lucky_draw_id){
                                                        $query->where('customer_id' , $winner_id)->where('lucky_draw_id' , $lucky_draw_id)->where('status' , true);
                                                    })->where('status' , true)->with(['winner'])->first();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function getLuckyDrawPoints($id)
    {
        try{
            $lucky_draw = App\LuckyDraw::find($id);

            $no_of_winners = $lucky_draw->no_of_winners;

            if(!is_numeric($no_of_winners)){
               $this->response["message"] = "Winner field not valid numeric data.";
               return $this->errorResponse($this->response);  
            }
            $winners = 0;

            $lucky_draw_points = App\LuckyDrawPoint::where('status' , 1);
            if(isset($lucky_draw->start_at)){
                $lucky_draw_points = $lucky_draw_points->where('created_at' , '>=' , $lucky_draw->start_at);
            }

            if(isset($lucky_draw->end_at)){
                $lucky_draw_points = $lucky_draw_points->where('created_at' , '<=' , $lucky_draw->end_at);
            }

            if(!$lucky_draw->is_allow_old_winders){
                $lucky_draw_points = $lucky_draw_points->whereDoesntHave('Winners.customer' , function($query) use($lucky_draw){
                    $query->where('lucky_draw_id' , '<>' , $lucky_draw->id)->where('status' , true);
                });
            }
            
            $this->response["data"] = $lucky_draw_points->inRandomOrder()->limit(500)->get(['id' , 'uuid' ]);
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }

    }

    public function getLuckyDrawPointCustomer($lucky_draw_id , $point_id )
    {
        try{
            $point = App\LuckyDrawPoint::find($point_id);
            $customer = App\Customer::where('id' , $point->customer_id)->first();
            $lucky_draw = App\LuckyDraw::find($lucky_draw_id);
            $lucky_draw->update(['is_winner_selected' => true]);
            App\Winner::where('lucky_draw_id' , $lucky_draw_id)->update(['status' => false]);
            $winner = App\Winner::create([
                'uuid' => $point->uuid,
                'lucky_draw_id' => $lucky_draw_id,
                'customer_id' => $point->customer_id,
                'position' => 1,
                'status' => true
            ]);

            event(new RaffleDraw($winner));
            $customer->winner_id = $winner->id;
            $customer['raffle_number'] = $point->uuid;
            $this->response["data"] = $customer;
            $this->response["lucky_draw"] = $lucky_draw;
            $this->response["message"] = "Raffle draw winner selection successfull.!";
            return $this->successResponse($this->response); 

        }catch (\Exception $e) {
            $this->response["message"] = "Raffle draw winner selection faild.!";
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function print($winner_id )
    {
        try{            
            $winner = App\Winner::find($winner_id);
            $customer = App\Customer::where('id' , $winner->customer_id)->first();
            $lucky_draw = App\LuckyDraw::find($winner->lucky_draw_id);

            $pdf = PDF::loadView('exports.raffledraw', ["customer" => $customer, "winner" => $winner, "lucky_draw" => $lucky_draw]);
            //$pdf->setPaper('A4', 'landscape');
            return $pdf->download($customer->fullname . '-'.  $lucky_draw->name . '.pdf'); 

        }catch (\Exception $e) {
            $this->response["message"] = "Raffle draw winner selection faild.!";
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function send_notify(Request $request, $id)
    {
        try{
            $winner = App\Winner::find($id);
        
            event(new RaffleDraw($winner , $request->email ?? null));

            $this->response["data"] = $winner;
            $this->response["message"] = "";
            return $this->successResponse($this->response); 

        }catch (\Exception $e) {
            $this->response["message"] = "";
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function LuckyDrawReset($lucky_draw_id)
    {
        try{
            App\Winner::where('lucky_draw_id' , $lucky_draw_id)->update(['status' => false]);
            $lucky_draw = App\LuckyDraw::find($lucky_draw_id);
            $lucky_draw->update(['is_winner_selected' => false]);
            $this->response["data"] = $lucky_draw; 
            $this->response["message"] = "Raffle draw winner reset successfull.!";
            
            return $this->successResponse($this->response); 

        }catch (\Exception $e) {
            $this->response["message"] = "Raffle draw winner reset faild.!";
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }

    }

    public function selectWinners($id)
    {
        try{
            $lucky_draw = App\LuckyDraw::find($id);

            $no_of_winners = $lucky_draw->no_of_winners;

            if(!is_numeric($no_of_winners)){
               $this->response["message"] = "Winner field not valid numeric data.";
               return $this->errorResponse($this->response);  
            }
            $winners = 0;

            $lucky_draw_points = App\LuckyDrawPoint::where('status' , 1);
            if(isset($lucky_draw->start_at)){
                $lucky_draw_points = $lucky_draw_points->where('created_at' , '>=' , $lucky_draw->start_at);
            }

            if(isset($lucky_draw->end_at)){
                $lucky_draw_points = $lucky_draw_points->where('created_at' , '<=' , $lucky_draw->end_at);
            }

            if(!$lucky_draw->is_allow_old_winders){
                $lucky_draw_points = $lucky_draw_points->whereDoesntHave('Winners.customer' , function($query) use($lucky_draw){
                    $query->where('lucky_draw_id' , '<>' , $lucky_draw->id);
                });
            }

            if($lucky_draw_points->select('customer_id')->distinct()->count('customer_id') < $no_of_winners){
                $this->response["message"] = "Winner not selected!. Min user requirment faild.";
                return $this->errorResponse($this->response);
            }

            
            $data = [];
            $winner = [];
            for ($i=1; $i <= $no_of_winners; $i++) {    
                $selected = $this->SelectWinner($id);
                $data[] = $selected; 
                if(isset($selected)){
                    $winner = App\LuckyDrawPoint::find($selected);
                    $success  = $this->CreateWinner($lucky_draw->id, $winner , $i);
                    //if($success) $winners++;
                }
            }
            
            //$lucky_draw->update(['is_winner_selected' => true]);
            
            $this->response["data"] = $success;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }


    public function SelectWinner($id)
    {
        $selected = 0;
        $lucky_draw = App\LuckyDraw::find($id);

        $no_of_winners = $lucky_draw->no_of_winners;
        $lucky_draw_points = App\LuckyDrawPoint::where('status' , 1);
        if(isset($lucky_draw->start_at)){
            $lucky_draw_points = $lucky_draw_points->where('created_at' , '>=' , $lucky_draw->start_at);
        }

        if(isset($lucky_draw->end_at)){
            $lucky_draw_points = $lucky_draw_points->where('created_at' , '<=' , $lucky_draw->end_at);
        }

        if(!$lucky_draw->is_allow_old_winders){
            $lucky_draw_points = $lucky_draw_points->whereDoesntHave('Winners.customer' , function($query) use($lucky_draw){
                $query->where('lucky_draw_id' , '<>' , $lucky_draw->id);
            });
        }
        if($lucky_draw_points->count() >= 1){
            $first = $lucky_draw_points->first();
            $last = $lucky_draw_points->latest()->first();
            $selected = rand($first->id , $last->id);
            if($lucky_draw_points->where('id' , $selected)->count() == 0){
                $this->SelectWinner($id); 
            }
        }        
        return $selected;
    }

    public function CreateWinner($lucky_draw_id , $winner, $position)
    {
        try{

            App\Winner::create([
                'uuid' => $winner->uuid,
                'lucky_draw_id' => $lucky_draw_id,
                'customer_id' => $winner->customer_id,
                'position' => $position,
                'status' => true
            ]);
            
            return 1;
        }catch (\Exception $e) {
            return $e->getMessage();
        }

    }
}