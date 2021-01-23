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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use App\Imports\CampaignGiftsImport;
use Illuminate\Database\Eloquent\Builder;

class FreeGiftCampaignController extends Controller
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
            $filter = $request->all();
            $campaigns = App\Campaign::where('campaign_type' , 'free_shop');

            if(isset($filter['group_id']))
            $campaigns = $campaigns->where('group_id' , $filter['group_id']);

            $this->response['data'] = $campaigns->with('group')->withCount('GiftVouchers')
                                                ->offset($this->filter["itemPerPage"] * ($page - 1))
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
            $campaigns = App\Campaign::where('campaign_type' , 'free_shop')->with('group');

            if(isset($filter['searchText'])){
                $campaigns = $campaigns->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }

            if(isset($filter['group_id']))
            $campaigns = $campaigns->where('group_id' , $filter['group_id']);

            $this->response["totalItems"] = $campaigns->count();
            $this->response['data'] = $campaigns->withCount('GiftVouchers')->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
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
            'type' => 'required',
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
                $campaign->campaign_type = 'free_shop';
                $campaign->start_at = $request->input('start_at');
                $campaign->end_at = $request->input('end_at');
                $campaign->send_sms = $request->has('send_sms') ? $request->input('send_sms')  : 0;
                $campaign->send_email = $request->has('send_email') ? $request->input('send_email')  : 0;
                $campaign->group_id =  $request->has('group_id') ? $request->input('group_id')  : null;

                $data = [
                    'months' => $request->has('months') ? $request->input('months') : 0,
                    'years' => $request->has('years') ? $request->input('years') : 0,
                    'dates' => $request->has('dates') ? $request->input('dates') : 0,
                    'day_limit' => $request->has('day_limit') ? $request->input('day_limit') : 0,
                    'customer_limit' => $request->has('customer_limit') ? $request->input('customer_limit') : 0,
                    'max_limit' => $request->has('max_limit') ? $request->input('max_limit') : 0,
                    'type' => $request->has('type') ? $request->input('type') : null,
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

            $campaign->load('group');

            $toArray = [
              'campaign_name' => $campaign->name ,
              'description' => $campaign->description ,
              'months' => $campaign->data['months'] ,
              'years' => $campaign->data['years'] ,
              'dates' => $campaign->data['dates'] ,
              'day_limit' => $campaign->data['day_limit'] ,
              'customer_limit' => $campaign->data['customer_limit'] ,
              'max_limit' => $campaign->data['max_limit'] ,
              'type' => $campaign->data['type'] ?? null,
              'campaign_type' => $campaign->campaign_type ,
              'start_at' => $campaign->start_at ,
              'end_at' => $campaign->end_at ,
              'send_sms' => $campaign->send_sms ,
              'send_email' => $campaign->send_email ,
              'group_id' => $campaign->group_id ,
              'group' => $campaign->group
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
                $campaign->campaign_type = 'free_shop';
                $campaign->start_at = $request->input('start_at');
                $campaign->end_at = $request->input('end_at');
                $campaign->send_sms = $request->has('send_sms') ? $request->input('send_sms')  : 0;
                $campaign->send_email = $request->has('send_email') ? $request->input('send_email')  : 0;
                $campaign->group_id =  $request->has('group_id') ? $request->input('group_id')  : null;

                $data = $campaign->data;

                $data['type'] = $request->has('type') ? $request->input('type') : $data['type'];
                $data['months'] = $request->has('months') ? $request->input('months') : $data['months'];
                $data['dates'] = $request->has('dates') ? $request->input('dates') : $data['dates'];
                $data['years'] = $request->has('years') ? $request->input('years') : $data['years'];
                $data['day_limit'] = $request->has('day_limit') ? $request->input('day_limit') : $data['day_limit'];
                $data['customer_limit'] = $request->has('customer_limit') ? $request->input('customer_limit') : $data['customer_limit'];
                $data['max_limit'] = $request->has('max_limit') ? $request->input('max_limit') : $data['max_limit'];

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

    public function search_gifts(Request $request)
    {
        //$user = App\Customer::where('cpr' , $request->input('cpr'))->first();

        $customer_id = $request->has('id') ? $request->input('id') : null;

        $country = App\Country::where('iso' , $request->input('country_iso'))->first();

        $request->merge([ 'mobile_number' => !Str::contains($request->input('mobile') , $request->input('dialCode')) && $request->input('dialCode') ? $request->input('dialCode'). $request->input('mobile') : $request->input('mobile')]);

        if(isset($customer_id)){

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:customers,email,' .$customer_id,
                'mobile' => 'required|unique:customers,mobile,'.$customer_id,
                'mobile_number' => 'required|unique:customers,mobile,'.$customer_id,
                'cpr' => 'required|unique:customers,cpr,'.$customer_id,
                'country_iso' => 'required|exists:countries,iso'
            ]);

        } else {

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:customers,email',
                'mobile'  => 'required|unique:customers,mobile',
                'mobile_number'  => 'required|unique:customers,mobile',
                'cpr' => 'required|unique:customers,cpr',
                'country_iso' => 'required|exists:countries,iso'
                //'password' => 'required|min:6',
            ]);

        }

        $this->response["title"] = Lang::get('');

        if ($validator->fails()) {
            $this->response["errors"] = $validator->errors();
            $this->response["old_data"] = $request->all();
            $this->response["message"] = Lang::get('');
            return $this->errorResponse($this->response);
        }

        $user = App\Customer::find($customer_id);

        if(!isset($user))
        $user = new App\Customer();

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->mobile = !Str::contains($request->input('mobile') , $request->input('dialCode')) && $request->input('dialCode') ? $request->input('dialCode'). $request->input('mobile') : $request->input('mobile') ;
        $user->cpr = $request->input('cpr');
        $user->country_iso = $request->input('country_iso');
        $user->nationality = $request->has('nationality.iso') ? $request->input('nationality.iso') : "";
        $user->dob = $request->has('dob') ? $request->input('dob') : null;
        $user->phone_code = $country->phone_code;
        $user->password = Hash::make(Str::random(8));
        $user->created_user_id = Auth::id();
        $user->save();

        $gifts = $this->findGifts($user);
        $this->response['data'] = $gifts;
        $this->response['customer'] = $user;

        return $this->successResponse($this->response);
    }

    public function findGifts(App\Customer $user)
    {
        $campaigns = App\Campaign::whereDate('start_at' , '<=' , Carbon::now())
                              ->whereDate('end_at' , '>=' , Carbon::now())
                              ->where('campaign_type' , 'free_shop')
                              ->where('status' , true)
                              ->whereHas('Gifts.Items' , function(Builder $query){
                                    $query->whereNull('gifted_at')->where('status' , true);
                                  }, '>', 0)
                                ->get();

        $month =  substr($user->cpr, 2, 2);
        $year =  substr($user->cpr, 0, 2);

        $gift = null;


        if(count($campaigns) == 0){
            $this->response['message'] =  'Currently no free campaign found.!';
        }

        foreach($campaigns as $campaign){

            $type = isset($campaign->data['type']) ? $campaign->data['type'] : "" ;

            if($type == 'month'){
                if(isset($campaign->data['months'])){
                    if(in_array($month , $campaign->data['months'])){
                        //$this->shuffleGift($campaign ,$user->id);
                        $gift = $this->shuffleGift($campaign, $user->id);
                        if(isset($gift)) break;
                    }
                }

            }else if($type == 'year'){

                if(isset($campaign->data['years'])){
                    if(in_array($year , $campaign->data['years'])){
                        //$this->shuffleGift($campaign , $user->id);
                        //continue;
                        $gift = $this->shuffleGift($campaign, $user->id);
                        if(isset($gift)) break;
                    }
                }

            }else if($type == 'date'){

                if(isset($campaign->data['dates']) && isset($user->dob)){
                    $date =  date('d', strtotime($user->dob));
                    $date .=  date('m', strtotime($user->dob));
                    // if($user->dob >= $campaign->start_at && $user->dob <= $campaign->end_at){
                    //     $gift = $this->shuffleGift($campaign, $user->id);
                    //     if(isset($gift)) break;
                    // }
                    // if(array_search($user->dob , $campaign->data['dates'])){
                    //     //$this->shuffleGift($campaign, $user->id);
                    //     //continue;
                    //     return $campaign;
                    // }

                    if(in_array($date , $campaign->data['dates'])){
                        $gift = $this->shuffleGift($campaign, $user->id);
                        if(isset($gift)) break;
                    }
                }
            }
        }

        return $gift;//App\GiftVoucher::with('gift')->where('user_id' , $user->id)->where('is_gifted' , false)->get();

    }

    public function shuffleGift(App\Campaign $campaign , $user_id)
    {
        $data = $campaign->data;
        $max_limit = false;
        $day_limit = false;
        $customer_limit = false;
        //return $campaign;

        if(isset($data['max_limit'])){
            $campaign_gifts = App\GiftVoucher::where('campaign_id' , $campaign->id)->where('status' , true)->count();

            if($data['max_limit'] > $campaign_gifts || $data['max_limit'] == 0){
                $max_limit = true;
            }
        }
        if(isset($data['customer_limit'])){
            $user_gifts = App\GiftVoucher::where('campaign_id' , $campaign->id)
                                           ->where('customer_id' , $user_id)
                                           ->where('status' , true)
                                           ->count();

            if($data['customer_limit'] > $user_gifts || $data['customer_limit'] == 0){
                $customer_limit = true;
            }
        }
        if(isset($data['day_limit'])){
            $date_gifts = App\GiftVoucher::where('campaign_id' , $campaign->id)->whereDate('created_at' , Carbon::now())->where('status' , true)->count();

            if($data['day_limit'] > $date_gifts || $data['day_limit'] == 0){
                $day_limit = true;
            }
        }

        if(!$customer_limit){
            $this->response['message'] = 'Customer gift limit exceeded.!';
        }

        if(!$day_limit){
            $this->response['message'] =  'Day gift limit exceeded.!';
        }

        if(!$max_limit){
            $this->response['message'] =  'Maximum gift limit exceeded.!';
        }

        if($customer_limit && $day_limit && $max_limit){
            $gift = App\Gift::where('campaign_id' , $campaign->id)
                              //->where('no_of_gifts' , '>' , 0)
                              ->with('campaign')
                              ->whereHas('items', function (Builder $query) {
                                    $query->whereNull('gifted_at');
                                }, '>', 0)
                              ->inRandomOrder()
                              ->limit(1)
                              ->first();

            // if(isset($gift)){
            //     App\GiftVoucher::create([
            //         'gift_id' => $gift->id,
            //         'code' => $gift->code,
            //         'customer_id' => $user_id,
            //         'campaign_id' => $campaign->id,
            //         'user_id' => Auth::id(),
            //         'is_gifted' => false,
            //         'status' =>true
            //     ]);

            //     $gift->decrement('no_of_gifts');
            // }

            return $gift;

        }

        return null;


    }

    public function assign_gift(Request $request)
    {
       try{
            if(!$request->has('gift_id') || !$request->has('gift_item_id') || !$request->has('user_id')){
                $this->response['message'] = "Free gift item not found.!.";
                return $this->invalidResponse($this->response);
            }
            $gift = App\Gift::whereHas('items', function (Builder $query) {
                                $query->whereNull('gifted_at');
                            }, '>', 0)
                            ->where('id' , $request->input('gift_id'))
                            ->where('status' , true)
                            ->first();


            $item = App\GiftItem::whereNull('gifted_at')->where('id' , $request->input('gift_item_id'))->first();

            if(!isset($gift) || !isset($item)){
                $this->response['message'] = "Free gift item not found.!.";
                return $this->invalidResponse($this->response);
            }

            $user_id = $request->user_id;

            $user_gifts = App\GiftVoucher::where('campaign_id', $gift->campaign_id)->where('customer_id', $user_id)->count();
            $campaign = App\Campaign::find($gift->campaign_id);
            $customer_limit = $campaign->data['customer_limit'] ?? 0;

            if($user_gifts >= $customer_limit && $customer_limit != 0){
                $this->response['message'] = "Free gift customer limit exceeded.!.";
                return $this->invalidResponse($this->response);
            }


            if(isset($gift)){
                App\GiftVoucher::create([
                    'gift_id' => $gift->id,
                    'code' => $item->code,
                    'customer_id' => $request->user_id,
                    'campaign_id' => $gift->campaign_id,
                    'user_id' => Auth::id(),
                    'is_gifted' => true,
                    'status' =>true
                ]);

                //$gift->decrement('no_of_gifts');
                $item->update(['gifted_at' => Carbon::now()]);
            }

            $this->response["message"] = "Free Gift added successfully.!";
            $this->response["data"] = [];
            return $this->successResponse($this->response);

       }catch (\Exception $e) {
            $this->response['message'] = "Free gift item not found.!";
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
                                   ->where('campaign_type' , 'free_shop')
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

    public function create_group(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);


        try {
            $this->response["title"] = Lang::get('');

            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $group = new App\CampaignGroup();
                $group->name = $request->input('name');
                $group->status = true;
                $group->save();


                $this->response["message"] = Lang::get('');
                $this->response["data"] = $group;
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function list_group(Request $request)
    {
        try {
            $filter = [];
            $filter['searchText'] = $request->has('searchText') ? $request->searchText : "";
            //$campaigns = App\CampaignGroup::where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
            $this->response['data'] = App\CampaignGroup::where('name' , 'LIKE' , '%'.$filter['searchText'].'%')->where('status' , true)->get();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function fetch_gift($id)
    {
        try{
            $voucher = App\GiftVoucher::with(['customer'])->find($id);
            $toArray = [
              'gift_code' => $voucher->code ,
              'customer' => $voucher->customer ,
            ];
            $this->response["data"] = $toArray;

            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update_gift(Request $request , $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:gift_vouchers,code,' .$id,
            'code' => 'required|exists:gift_items,code',
        ]);


        try {
            $this->response["title"] = Lang::get('');

            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $voucher = App\GiftVoucher::find($id);
                if(isset($voucher)){
                    //$voucher->update(['code' => $request->input('gift_code')]);
                    $voucher->code = $request->input('code');
                    if($request->has('promoter_id')){
                        $voucher->user_id = $request->input('promoter_id');
                    }

                    if($request->has('created_at')){
                        $voucher->created_at = $request->input('created_at');
                    }
                    $voucher->save();
                }


                $this->response["message"] = Lang::get('');
                $this->response["data"] = $voucher;
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}
