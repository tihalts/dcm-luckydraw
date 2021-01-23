<?php 

namespace App\Http\Controllers\Report;

use App;
use PDF;
use Excel;
use App\Campaign;
use Carbon\Carbon;
use App\CampaignGroup;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class PurchaseController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "orderby" => "desc",
        "itemPerPage" => 10
    ];    

    public function __construct()
    {
    }

    public function purchases(Request $request)
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }

            //return $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            
            $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }

            
            
            
            $purchases = DB::table('purchases')
                                ->join('customers' , 'purchases.customer_id' , '='  , 'customers.id')
                                // ->whereExists(function ($query) use($start_date , $end_date) {
                                //     $query->select("spin_winners.id")
                                //         ->from('spin_winners')
                                //         ->whereRaw('customers.id = spin_winners.customer_id')
                                //         ->whereBetween('spin_winners.created_at' , [$start_date , $end_date]);
                                // })
                                ->whereBetween('purchases.created_at' , [$start_date , $end_date]);


            $total = DB::table('purchases')
                            ->join('customers' , 'purchases.customer_id' , '='  , 'customers.id')
                            // ->where(function($query) use($start_date , $end_date) {
                            //     if(isset($start_date) && isset($end_date)){
                            //         $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                            //     }                                                
                            // });
                            ->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);

            if($request->type == 'new'){
                $purchases = $purchases->where('customers.created_at' , '>=' , $start_date);
                $total = $total->where('customers.created_at' , '>=' , $start_date);
            }else if($request->type == 'old'){
                $purchases = $purchases->where('customers.created_at' , '<=' , $start_date);
                $total = $total->where('customers.created_at' , '<=' , $start_date);
            }



            if($day_diff <= 30){

                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m-%d') as date , IFNULL(sum(purchases.amount),0) as total,  COUNT(DISTINCT customers.id) as customers")
                                        ->groupBy(DB::Raw('date'));

                $total = $total->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m-%d') as date")
                                        ->groupBy(DB::Raw('date'));

            } elseif($day_diff > 30 && $day_diff <= 365){

                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m') as date , IFNULL(sum(purchases.amount),0) as total,  COUNT(DISTINCT customer_id) as customers")
                                        ->groupBy(DB::Raw('date'));

                $total = $total->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m') as date")
                                        ->groupBy(DB::Raw('date'));

            } elseif($day_diff > 365){

                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y') as date , IFNULL(sum(purchases.amount),0) as total,   COUNT(DISTINCT customer_id) as customers")
                                        ->groupBy(DB::Raw('date'));

                $total = $total->selectRaw("DATE_FORMAT(purchases.created_at, '%Y') as date")
                                        ->groupBy(DB::Raw('date'));
            }                     


            if(isset($filter['country'])){
                $purchases = $purchases->where('customers.country_iso' , $filter['country']);
                $total = $total->where('customers.country_iso' , $filter['country']);
            }

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
                $total = $total->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
                $total = $total->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                                    //->groupBy('customers.id')
                                    ->orderBy('total' , 'desc')
                                    ->take(20)
                                    ->skip(20 * ( $page - 1 ))
                                    ->get();         

            $total = $total->where('purchases.status' , 1);//->groupBy('customers.id');


            $this->response["currentPage"] = $page;
            $this->response["totalItems"] = $total->get()->count();
            $this->response["total_amount"] = $purchases->sum('total');
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $purchases;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function customers(Request $request)
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }
            
            
            $purchases = DB::table('customers')
                            ->select([
                                'customers.id as id',
                                'customers.cpr as cpr',
                                'customers.email as email',
                                'customers.phone_code as phone_code',
                                'customers.mobile as mobile',
                                'customers.country_iso as country_iso',
                                'customers.nationality as nationality',
                                'customers.created_at as created_at',
                                DB::raw("CONCAT(`first_name`,' ', `last_name`) AS fullname"),
                                DB::raw("IFNULL(sum(purchases.amount),0) as total")
                            ])
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            $total = DB::table('customers')
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            if(isset($filter['country'])){
                $purchases = $purchases->where('customers.country_iso' , $filter['country']);
                $total = $total->where('customers.country_iso' , $filter['country']);
            }

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
                $total = $total->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
                $total = $total->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                            ->groupBy('customers.id')
                            ->orderBy('total' , 'desc')
                            ->take(20)
                            ->skip(20 * ( $page - 1 ))
                            ->get();         

            $total = $total->where('purchases.status' , 1)->groupBy('customers.id');


            $this->response["currentPage"] = $page;
            $this->response["totalItems"] = $total->count();
            $this->response["total_amount"] = $total->sum('purchases.amount');
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $purchases;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function shops(Request $request)
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }
            
            
            $purchases = DB::table('shops')
                            ->select([
                                'shops.id as id',
                                'shops.shop_no as shop_no',
                                'shops.name as shop_name',
                                DB::raw("IFNULL(sum(purchases.amount),0) as total")
                            ])
                            ->leftJoin('purchases','purchases.shop_id','=','shops.id')
                            ->leftJoin('customers','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            $total = DB::table('shops')
                            ->leftJoin('purchases','purchases.shop_id','=','shops.id')
                            ->leftJoin('customers','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            if(isset($filter['country'])){
                $purchases = $purchases->where('customers.country_iso' , $filter['country']);
                $total = $total->where('customers.country_iso' , $filter['country']);
            }

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
                $total = $total->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
                $total = $total->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                            ->groupBy('shops.id')
                            ->orderBy('total' , 'desc')
                            ->take(20)
                            ->skip(20 * ( $page - 1 ))
                            ->get();         

            $total = $total->where('purchases.status' , 1);


            $this->response["currentPage"] = $page;
            $this->response["totalItems"] = $total->count();
            $this->response["total_amount"] = $total->sum('purchases.amount');
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $purchases;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function shop_categories(Request $request)
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }
            
            
            $purchases = DB::table('business_types')
                            ->select([
                                'business_types.id as id',
                                'business_types.name as shop_name',
                                DB::raw("IFNULL(sum(purchases.amount),0) as total")
                            ])
                            ->leftJoin('shops','business_types.id','=','shops.id')
                            ->leftJoin('purchases','purchases.shop_id','=','shops.id')
                            ->leftJoin('customers','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            $total = DB::table('business_types')
                            ->leftJoin('shops','business_types.id','=','shops.id')
                            ->leftJoin('purchases','purchases.shop_id','=','shops.id')
                            ->leftJoin('customers','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            if(isset($filter['country'])){
                $purchases = $purchases->where('customers.country_iso' , $filter['country']);
                $total = $total->where('customers.country_iso' , $filter['country']);
            }

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
                $total = $total->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
                $total = $total->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                            ->groupBy('business_types.id')
                            ->orderBy('total' , 'desc')
                            ->take(20)
                            ->skip(20 * ( $page - 1 ))
                            ->get();         

            $total = $total->where('purchases.status' , 1);


            $this->response["currentPage"] = $page;
            $this->response["totalItems"] = $total->count();
            $this->response["total_amount"] = $total->sum('purchases.amount');
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $purchases;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function countries(Request $request)
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }
            
            
            $purchases = DB::table('countries')
                            ->select([
                                'countries.id as id',
                                'countries.name as country_name',
                                DB::raw("IFNULL(sum(purchases.amount),0) as total")
                            ])
                            ->leftJoin('customers','customers.nationality','=','countries.iso')
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')                            
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            $total = DB::table('countries')
                            ->leftJoin('customers','customers.nationality','=','countries.iso')
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')  
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            if(isset($filter['country'])){
                $purchases = $purchases->where('customers.country_iso' , $filter['country']);
                $total = $total->where('customers.country_iso' , $filter['country']);
            }

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
                $total = $total->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
                $total = $total->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                            ->groupBy('countries.id')
                            ->orderBy('total' , 'desc')
                            ->take(20)
                            ->skip(20 * ( $page - 1 ))
                            ->get();         

            $total = $total->where('purchases.status' , 1);


            $this->response["currentPage"] = $page;
            $this->response["totalItems"] = $total->count();
            $this->response["total_amount"] = $total->sum('purchases.amount');
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $purchases;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function promoters(Request $request)
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }
            
            
            $purchases = DB::table('users')
                            ->select([
                                'users.id as id',
                                'users.email as email',
                                'users.phone_code as phone_code',
                                'customers.mobile as mobile',
                                'customers.country_iso as country_iso',
                                'customers.created_at as created_at',
                                DB::raw("CONCAT(`users`.`first_name`,' ', `users`.`last_name`) AS fullname"),
                                DB::raw("IFNULL(sum(purchases.amount),0) as total")
                            ])
                            ->leftJoin('purchases','users.id','=','purchases.user_id')
                            ->leftJoin('customers','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            $total = DB::table('users')
                            ->leftJoin('purchases','users.id','=','purchases.user_id')
                            ->leftJoin('customers','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });           

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
                $total = $total->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
                $total = $total->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            if(isset($filter['user_id'])){
                $purchases = $purchases->whereIn('users.id' , [$filter['user_id']]);
                $total = $total->whereIn('users.id' , [$filter['user_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                            ->groupBy('users.id')
                            ->orderBy('total' , 'desc')
                            ->take(20)
                            ->skip(20 * ( $page - 1 ))
                            ->get();         

            $total = $total->where('purchases.status' , 1)->groupBy('users.id');


            $this->response["currentPage"] = $page;
            $this->response["totalItems"] = $total->count();
            $this->response["total_amount"] = $total->sum('purchases.amount');
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $purchases;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function purchase_export(Request $request , $type = 'excel')
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }

            //return $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            
            $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }

            
            
            
            $purchases = DB::table('purchases')
                                ->join('customers' , 'purchases.customer_id' , '='  , 'customers.id')
                                // ->whereExists(function ($query) use($start_date , $end_date) {
                                //     $query->select("spin_winners.id")
                                //         ->from('spin_winners')
                                //         ->whereRaw('customers.id = spin_winners.customer_id')
                                //         ->whereBetween('spin_winners.created_at' , [$start_date , $end_date]);
                                // })
                                ->whereBetween('purchases.created_at' , [$start_date , $end_date]);

            if($request->type == 'new'){
                $purchases = $purchases->where('customers.created_at' , '>=' , $start_date);
            }else if($request->type == 'old'){
                $purchases = $purchases->where('customers.created_at' , '<=' , $start_date);
            }



            if($day_diff <= 30){

                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m-%d') as Date , IFNULL(sum(purchases.amount),0) as Total,  COUNT(DISTINCT customers.id) as Customers")
                                        ->groupBy(DB::Raw('date'));

            } elseif($day_diff > 30 && $day_diff <= 365){

                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m') as Date , IFNULL(sum(purchases.amount),0) as Total,  COUNT(DISTINCT customer_id) as Customers")
                                        ->groupBy(DB::Raw('date'));

            } elseif($day_diff > 365){

                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y') as Date , IFNULL(sum(purchases.amount),0) as Total,   COUNT(DISTINCT customer_id) as Customers")
                                        ->groupBy(DB::Raw('date'));
            }                     


            if(isset($filter['country'])){
                $purchases = $purchases->where('customers.country_iso' , $filter['country']);
            }

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                                    //->groupBy('customers.id')
                                    ->orderBy('total' , 'desc')
                                    ->get();

            if($type == 'pdf'){
                $pdf = PDF::loadView('exports.report', ["reports" => json_decode(json_encode($purchases), true)]);
                return $pdf->download(Uuid::uuid1(). '.pdf');
            }else {
                return Excel::download(new ReportExport(json_decode(json_encode($purchases), true)), Uuid::uuid1(). '.xlsx');
            }
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function customer_export(Request $request , $type = 'excel')
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }
            
            
            $purchases = DB::table('customers')
                            ->select([
                                //'customers.id as ID',
                                DB::raw("CONCAT(`first_name`,' ', `last_name`) AS FullName"),
                                'customers.cpr as CPR',
                                'customers.email as Email',
                                DB::raw("CONCAT(`customers.phone_code`,'', `customers.mobile`) AS Mobile"),
                                //'customers.country_iso as country_iso',
                               // 'countries.name as CountryName',
                                'customers.created_at as CreatedAt',
                                DB::raw("IFNULL(sum(purchases.amount),0) as Total")
                            ])
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')
                            //->leftJoin('countries','customers.nationality','=','countries.iso')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            if(isset($filter['country'])){
                $purchases = $purchases->where('customers.country_iso' , $filter['country']);
            }

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                            ->groupBy('customers.id')
                            ->orderBy('total' , 'desc')
                            ->get();         

            if($type == 'pdf'){
                $pdf = PDF::loadView('exports.report', ["reports" => json_decode(json_encode($purchases), true)]);
                return $pdf->download(Uuid::uuid1(). '.pdf');
            }else {
                return Excel::download(new ReportExport(json_decode(json_encode($purchases), true)), Uuid::uuid1(). '.xlsx');
            }
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function shop_export(Request $request , $type = 'excel')
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }
            
            
            $purchases = DB::table('shops')
                            ->select([
                                //'shops.id as id',
                                'shops.shop_no as Shop Number',
                                'shops.name as Shop Name',
                                DB::raw("IFNULL(sum(purchases.amount),0) as Total")
                            ])
                            ->leftJoin('purchases','purchases.shop_id','=','shops.id')
                            ->leftJoin('customers','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            if(isset($filter['country'])){
                $purchases = $purchases->where('customers.country_iso' , $filter['country']);
            }

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                            ->groupBy('shops.id')
                            ->orderBy('total' , 'desc')
                            ->get();         

            if($type == 'pdf'){
                $pdf = PDF::loadView('exports.report', ["reports" => json_decode(json_encode($purchases), true)]);
                return $pdf->download(Uuid::uuid1(). '.pdf');
            }else {
                return Excel::download(new ReportExport(json_decode(json_encode($purchases), true)), Uuid::uuid1(). '.xlsx');
            }
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function shop_category_export(Request $request , $type = 'excel')
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }
            
            
            $purchases = DB::table('business_types')
                            ->select([
                                //'business_types.id as id',
                                'business_types.name as Business Type',
                                DB::raw("IFNULL(sum(purchases.amount),0) as Total")
                            ])
                            ->leftJoin('shops','business_types.id','=','shops.id')
                            ->leftJoin('purchases','purchases.shop_id','=','shops.id')
                            ->leftJoin('customers','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            if(isset($filter['country'])){
                $purchases = $purchases->where('customers.country_iso' , $filter['country']);
            }

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                            ->groupBy('business_types.id')
                            ->orderBy('total' , 'desc')
                            ->get();         

            if($type == 'pdf'){
                $pdf = PDF::loadView('exports.report', ["reports" => json_decode(json_encode($purchases), true)]);
                return $pdf->download(Uuid::uuid1(). '.pdf');
            }else {
                return Excel::download(new ReportExport(json_decode(json_encode($purchases), true)), Uuid::uuid1(). '.xlsx');
            }
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function country_export(Request $request , $type = 'excel')
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }
            
            
            $purchases = DB::table('countries')
                            ->select([
                                //'countries.id as id',
                                'countries.name as Country Name',
                                DB::raw("IFNULL(sum(purchases.amount),0) as Total")
                            ])
                            ->leftJoin('customers','customers.nationality','=','countries.iso')
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')                            
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            if(isset($filter['country'])){
                $purchases = $purchases->where('customers.country_iso' , $filter['country']);
            }

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                            ->groupBy('countries.id')
                            ->orderBy('total' , 'desc')
                            ->get();         

            if($type == 'pdf'){
                $pdf = PDF::loadView('exports.report', ["reports" => json_decode(json_encode($purchases), true)]);
                return $pdf->download(Uuid::uuid1(). '.pdf');
            }else {
                return Excel::download(new ReportExport(json_decode(json_encode($purchases), true)), Uuid::uuid1(). '.xlsx');
            }
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function promoter_export(Request $request , $type = 'excel')
    {
        try {  
            $filter = $request->all();
            $start_date = null;
            $end_date = null;
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_id'])){
                $campaign = Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $start_date = Carbon::parse($campaign->start_at);
                    $end_date = Carbon::parse($campaign->end_at);
                }

            } elseif(isset($filter['group_id'])){
                $group = CampaignGroup::find($filter['group_id']);
                if(isset($group)){
                    $start_date = Carbon::parse($group->start_at);
                    $end_date = Carbon::parse($group->end_at);
                }
            }


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            }elseif(!isset($start_date) || !isset($end_date)){
                $start_date = Carbon::parse(now())->subDays(30)->startOfDay();
                $end_date = Carbon::parse(now())->endOfDay();
            }

            $shop_ids = [];
            if(isset($filter['shop_id'])){
                $shop_ids[] = $filter['shop_id'];
            }elseif(isset($filter['category_id'])){
                $shop_ids[] = App\Shop::where('category_id' , $filter['category_id'])->where('status' , 1)->get()->pluck('id');
            }
            
            
            $purchases = DB::table('users')
                            ->select([
                                'users.id as id',
                                'users.email as email',
                                'users.phone_code as phone_code',
                                'customers.mobile as mobile',
                                'customers.country_iso as country_iso',
                                'customers.created_at as created_at',
                                DB::raw("CONCAT(`users`.`first_name`,' ', `users`.`last_name`) AS fullname"),
                                DB::raw("IFNULL(sum(purchases.amount),0) as total")
                            ])
                            ->leftJoin('purchases','users.id','=','purchases.user_id')
                            ->leftJoin('customers','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            });

            if(count($shop_ids)){
                $purchases = $purchases->whereIn('purchases.shop_id' , $shop_ids);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->whereIn('customers.id' ,  [$filter['customer_id']]);
            }

            if(isset($filter['user_id'])){
                $purchases = $purchases->whereIn('users.id' , [$filter['user_id']]);
            }

            $purchases = $purchases->where('purchases.status' , 1)
                            ->groupBy('users.id')
                            ->orderBy('total' , 'desc')
                            ->get();         


            if($type == 'pdf'){
                $pdf = PDF::loadView('exports.report', ["reports" => json_decode(json_encode($purchases), true)]);
                return $pdf->download(Uuid::uuid1(). '.pdf');
            }else {
                return Excel::download(new ReportExport(json_decode(json_encode($purchases), true)), Uuid::uuid1(). '.xlsx');
            }
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}