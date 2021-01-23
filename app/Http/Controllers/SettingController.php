<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class SettingController extends Controller 
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
            $settings = App\SiteSetting::where('status' , true)->get()->pluck('value' , 'key');
            $site_settings = [];
            $site_settings['email'] = isset($settings['email']) ? $settings['email'] : "";
            $site_settings['sms'] = isset($settings['sms']) ? $settings['sms'] : "";
            $site_settings['send_sms'] = isset($settings['send_sms']) ? $settings['send_sms'] : false;
            $site_settings['send_email'] = isset($settings['send_email']) ? $settings['send_email'] : false;
            $this->response['data'] = $site_settings;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'sms' => 'required',
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {

                App\SiteSetting::updateOrCreate(['key' => 'sms'] , ['value' => $request->sms]);
                App\SiteSetting::updateOrCreate(['key' => 'email'] , ['value' => $request->email]);
                App\SiteSetting::updateOrCreate(['key' => 'send_sms'] , ['value' => $request->has('send_sms') ? $request->send_sms : false]);
                App\SiteSetting::updateOrCreate(['key' => 'send_email'] , ['value' => $request->has('send_email') ? $request->send_sms : false]);
                               
                $this->response["message"] = Lang::get('');
                $settings = App\SiteSetting::where('status' , true)->get()->pluck('value' , 'key');
                $site_settings = [];
                $site_settings['email'] = isset($settings['email']) ? $settings['email'] : "";
                $site_settings['sms'] = isset($settings['sms']) ? $settings['sms'] : "";
                $site_settings['send_sms'] = isset($settings['send_sms']) ? $settings['send_sms'] : 0;
                $site_settings['send_email'] = isset($settings['send_email']) ? $settings['send_email'] : 0;
                $this->response['data'] = $site_settings;
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update_password(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'current' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        try {
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = "Password update failed.!";
                return $this->errorResponse($this->response);
            } 
            $user = App\User::find(Auth::id());

            if (!Hash::check($request->current, $user->password)) {
                $this->response['message'] = 'Current password does not match';
                return $this->errorResponse($this->response);
            }

            $user->password = Hash::make($request->password);
            $user->save();
            $this->response["message"] = "Password Update Successfully.!";
            $this->response['data'] = [];
            return $this->successResponse($this->response);

        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function show($id)
    {

    }

    public function prev_view()
    {
        $markdown = App\SiteSetting::where('key' , 'email')->first();
        return view('template' , ['html' => isset($markdown) ? $markdown->value : ""]);
    }

    public function prev_view1($id)
    {
        $markdown =  App\RaffleDrawSetting::where('lucky_draw_id' , $id)->first();
        return view('template' , ['html' => isset($markdown) ? $markdown->email : ""]);
    }

    public function prev_view2($campaign_id)
    {
        $markdown = App\CampaignTemplate::where('campaign_id' , $campaign_id)->first();
        return view('template' , ['html' => isset($markdown) ? $markdown->email : ""]);
    }

    public function prev_view3($campaign_id)
    {
        $markdown = App\CampaignTemplate::where('campaign_id' , $campaign_id)->first();
        return view('template' , ['html' => isset($markdown) ? $markdown->email : ""]);
    }

    public function getGift($page = 1)
    {     
        try {            
            $settings = App\SiteSetting::where('status' , true)->get()->pluck('value' , 'key');
            $site_settings['gift_logo_img'] = isset($settings['gift_logo_img']) ? asset('storage/' .$settings['gift_logo_img']) : null;
            $site_settings['gift_bg_img'] = isset($settings['gift_bg_img']) ? asset('storage/' .$settings['gift_bg_img']) : null;
            $this->response['data'] = $site_settings;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function saveGift(Request $request)
    {
        try {
            if($request->hasFile('gift_bg_img')){
                $bg_img = Storage::disk('public')->putFileAs(
                    'settings', $request->file('gift_bg_img'), Uuid::uuid1().'.'.$request->file('gift_bg_img')->getClientOriginalExtension()
                );
                App\SiteSetting::updateOrCreate(['key' => 'gift_bg_img'] , ['value' => $bg_img]);
            }

            if($request->hasFile('gift_logo_img')){
                $logo_img = Storage::disk('public')->putFileAs(
                    'settings', $request->file('gift_logo_img'), Uuid::uuid1().'.'.$request->file('gift_logo_img')->getClientOriginalExtension()
                );
                App\SiteSetting::updateOrCreate(['key' => 'gift_logo_img'] , ['value' => $logo_img]);
            }
            $settings = App\SiteSetting::where('status' , true)->get()->pluck('value' , 'key');
            $site_settings['gift_bg_img'] = isset($settings['gift_bg_img']) ? asset('storage/' .$settings['gift_bg_img'] ): null;
            $site_settings['gift_logo_img'] = isset($settings['gift_logo_img']) ? asset('storage/' .$settings['gift_logo_img'] ): null;
            $this->response['data'] = $site_settings;
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function getPurchase($page = 1)
    {     
        try {            
            $settings = App\SiteSetting::where('status' , true)->get()->pluck('value' , 'key');
            $site_settings['purchase_logo_img'] = isset($settings['purchase_logo_img']) ? asset('storage/' .$settings['purchase_logo_img']) : null;
            $site_settings['purchase_bg_img'] = isset($settings['purchase_bg_img']) ? asset('storage/' .$settings['purchase_bg_img']) : null;
            $this->response['data'] = $site_settings;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function savePurchase(Request $request)
    {
        try {
            if($request->hasFile('purchase_bg_img')){
                $bg_img = Storage::disk('public')->putFileAs(
                    'settings', $request->file('purchase_bg_img'), Uuid::uuid1().'.'.$request->file('purchase_bg_img')->getClientOriginalExtension()
                );
                App\SiteSetting::updateOrCreate(['key' => 'purchase_bg_img'] , ['value' => $bg_img]);
            }

            if($request->hasFile('purchase_logo_img')){
                $logo_img = Storage::disk('public')->putFileAs(
                    'settings', $request->file('purchase_logo_img'), Uuid::uuid1().'.'.$request->file('purchase_logo_img')->getClientOriginalExtension()
                );
                App\SiteSetting::updateOrCreate(['key' => 'purchase_logo_img'] , ['value' => $logo_img]);
            }
            $settings = App\SiteSetting::where('status' , true)->get()->pluck('value' , 'key');
            $site_settings['purchase_logo_img'] = isset($settings['purchase_logo_img']) ? asset('storage/' .$settings['purchase_logo_img']) : null;
            $site_settings['purchase_bg_img'] = isset($settings['purchase_bg_img']) ? asset('storage/' . $settings['purchase_bg_img']) : null;
            $this->response['data'] = $site_settings;
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function getSpinner($page = 1)
    {     
        try {            
            $settings = App\SiteSetting::where('status' , true)->get()->pluck('value' , 'key');
            $site_settings['spin_logo_img'] = isset($settings['spin_logo_img']) ? asset('storage/' .$settings['spin_logo_img']) : null;
            $site_settings['spin_bg_img'] = isset($settings['spin_bg_img']) ? asset('storage/' .$settings['spin_bg_img']) : null;
            $this->response['data'] = $site_settings;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function saveSpin(Request $request)
    {
        try {
            if($request->hasFile('spin_bg_img')){
                $bg_img = Storage::disk('public')->putFileAs(
                    'settings', $request->file('spin_bg_img'), Uuid::uuid1().'.'.$request->file('spin_bg_img')->getClientOriginalExtension()
                );
                App\SiteSetting::updateOrCreate(['key' => 'spin_bg_img'] , ['value' => $bg_img]);
            }

            if($request->hasFile('spin_logo_img')){
                $logo_img = Storage::disk('public')->putFileAs(
                    'settings', $request->file('spin_logo_img'), Uuid::uuid1().'.'.$request->file('spin_logo_img')->getClientOriginalExtension()
                );
                App\SiteSetting::updateOrCreate(['key' => 'spin_logo_img'] , ['value' => $logo_img]);
            }
            $settings = App\SiteSetting::where('status' , true)->get()->pluck('value' , 'key');
            $site_settings['spin_logo_img'] = isset($settings['spin_logo_img']) ? asset('storage/' .$settings['spin_logo_img']) : null;
            $site_settings['spin_bg_img'] = isset($settings['spin_bg_img']) ? asset('storage/' . $settings['spin_bg_img']) : null;
            $this->response['data'] = $site_settings;
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function image($type)
    {
        $setting = App\SiteSetting::where('key' , $type)->where('status' , true)->first();

        return response()->file('storage/' . $setting->value);
    }
}
