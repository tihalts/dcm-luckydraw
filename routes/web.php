<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::get('/purchase/mirror', function () {
    return view('layout.mirror' , [ "user_role" => Auth::user()->role ]);
});

Route::get('/getcountries', 'HomeController@listCountries');

Route::get('scratch-card/view', function () {
    return view('layout.scratch-card');
});

Route::group(['prefix'=>'/' , 'namespace' => 'Auth'], function(){ 

    //Authenticate Admin User 
    Route::get('/login', 'LoginController@login')->name('login');
    Route::post('/login', 'LoginController@Authenticate');
    Route::get('/logout', 'LoginController@logout');
    Route::get('/forgot-password', 'LoginController@ForgotPasswordView');
    Route::post('/forgot-password' , 'LoginController@sendResetLinkEmail');
    Route::get('/reset-password' , 'LoginController@ResetView');
    Route::post('/reset-password' , 'LoginController@ResetPassword');
    Route::get('/admin/create', 'LoginController@RegisterUser');
});

Route::group(['prefix'=>'/'  , 'middleware' => ['auth']], function(){    

    //Admin Users
    Route::get('/users', 'HomeController@index');
    Route::get('/users/create', 'HomeController@index');
    Route::get('/users/{id}/edit', 'HomeController@index');
    Route::get('/user/list/{page?}', 'UserController@list');
    Route::post('/user/search/{page?}', 'UserController@search');
    Route::post('/user/create', 'UserController@store');
    Route::post('/user/edit/{id}', 'UserController@update');
    Route::get('/user/fetch/{id}', 'UserController@fetch');
    Route::delete('/user/remove/{id}', 'UserController@destroy');
    Route::get('/user/activated/{id}', 'UserController@Activated');

    //Promoter Users
    Route::get('/promoters', 'HomeController@index');
    Route::get('/promoters/create', 'HomeController@index');
    Route::get('/promoters/{id}/edit', 'HomeController@index');
    Route::get('/promoter/list/{page?}', 'PromoterController@list');
    Route::post('/promoter/search/{page?}', 'PromoterController@search');
    Route::post('/promoter/create', 'PromoterController@store');
    Route::post('/promoter/edit/{id}', 'PromoterController@update');
    Route::get('/promoter/fetch/{id}', 'PromoterController@fetch');
    Route::delete('/promoter/remove/{id}', 'PromoterController@destroy');
    Route::get('/promoter/activated/{id}', 'PromoterController@Activated');
    Route::post('/promoter/change-password/{id}' , 'PromoterController@change_password');

    //Supervisor Users
    Route::get('/supervisors', 'HomeController@index');
    Route::get('/supervisors/create', 'HomeController@index');
    Route::get('/supervisors/{id}/edit', 'HomeController@index');
    Route::get('/supervisor/list/{page?}', 'SupervisorController@list');
    Route::post('/supervisor/search/{page?}', 'SupervisorController@search');
    Route::post('/supervisor/create', 'SupervisorController@store');
    Route::post('/supervisor/edit/{id}', 'SupervisorController@update');
    Route::get('/supervisor/fetch/{id}', 'SupervisorController@fetch');
    Route::delete('/supervisor/remove/{id}', 'SupervisorController@destroy');
    Route::get('/supervisor/activated/{id}', 'SupervisorController@Activated');
    Route::post('/supervisor/change-password/{id}' , 'SupervisorController@change_password');

    //Lucky Draw 
    Route::get('/lucky-draws', 'HomeController@index');
    Route::get('/lucky-draws/create', 'HomeController@index');
    Route::get('/lucky-draws/{id}/edit', 'HomeController@index');
    Route::get('/lucky-draw/list/{page?}', 'LuckyDrawController@list');
    Route::post('/lucky-draw/search/{page?}', 'LuckyDrawController@search');
    Route::post('/lucky-draw/create', 'LuckyDrawController@store');
    Route::post('/lucky-draw/edit/{id}', 'LuckyDrawController@update');
    Route::get('/lucky-draw/fetch/{id}', 'LuckyDrawController@fetch');
    Route::get('/lucky-draw/{id}/select/winners' , 'WinnerController@SelectWinners');  
    Route::get('/lucky-draw/{id}/winner/{customer_id}' , 'WinnerController@getWinner');    
    Route::get('/lucky-draw/{lucky_draw_id}/points' , 'WinnerController@getLuckyDrawPoints');    
    Route::get('/lucky-draw/{lucky_draw_id}/reset' , 'WinnerController@LuckyDrawReset');    
    Route::get('/lucky-draw/{lucky_draw_id}/point/{point_id}/customer' , 'WinnerController@getLuckyDrawPointCustomer');  

    Route::delete('/lucky-draw/remove/{lucky_draw_id}' , 'LuckyDrawController@destroy');
    Route::get('/lucky-draw/activated/{lucky_draw_id}' , 'LuckyDrawController@activated');

    Route::get('/lucky-draws/{id}/winners' , 'HomeController@index');
    Route::get('/lucky-draws/{id}/select/winners' , 'HomeController@index');
    Route::get('/lucky-draws/{lucky_draw_id}/winner/{winner_id}' , 'HomeController@index');
    Route::get('/winner/list/{id}/{page?}' , 'WinnerController@list');
    Route::get('/winner/fetch/{lucky_draw_id}/{winner_id}' , 'WinnerController@fetch');

    Route::get('/lucky-draws/{id}/settings', 'HomeController@index');
    Route::post('/raffledraw/setting/update/{lucky_draw_id}' , 'LuckyDrawController@update_settings');
    Route::get('/raffledraw/setting/fetch/{lucky_draw_id}' , 'LuckyDrawController@fetch_setting');
    Route::get('/sendwinner/notification/{lucky_draw_id}' , 'WinnerController@send_notify');
    Route::get('/winner-print/pdf/{winner_id}' , 'WinnerController@print');

    //Purchase Points
    Route::get('/purchase-points', 'HomeController@index');
    Route::get('/purchase-point/list/{page?}', 'PurchasePointController@list');
    Route::post('/purchase-point/search/{page?}', 'PurchasePointController@search');
    Route::post('/purchase-point/create', 'PurchasePointController@store');
    Route::post('/purchase-point/edit/{id}', 'PurchasePointController@update');
    Route::get('/purchase-point/fetch/{id}', 'PurchasePointController@fetch');

    Route::get('/app-settings', 'HomeController@index');
    Route::get('/get/settings', 'SettingController@list');
    Route::post('/update/settings', 'SettingController@store');//Lucky Draw 
    Route::post('/registration/email/preview', 'SettingController@emailpreview');
    Route::get('/get/gift/settings', 'SettingController@getGift');
    Route::post('/update/gift-settings', 'SettingController@saveGift'); 
    Route::get('/get/purchase/settings', 'SettingController@getPurchase');
    Route::post('/update/purchase-settings', 'SettingController@savePurchase');
    Route::get('/get/spin/settings', 'SettingController@getSpinner');
    Route::post('/update/spin-settings', 'SettingController@saveSpin');
    Route::get('/image/setting/{type}', 'SettingController@image');   

    Route::get('/countries', 'HomeController@index');
    Route::get('/country/list/{page?}', 'CountryController@list');
    Route::post('/country/search/{page?}', 'CountryController@search');
    Route::post('/country/create', 'CountryController@store');
    Route::post('/country/edit/{id}', 'CountryController@update');
    Route::get('/country/fetch/{id}', 'CountryController@fetch');

    Route::delete('/purchase/remove/{id}', 'PurchaseController@destroy');

    Route::get('/raffledraw/{id}/select/winners', 'HomeController@RaffleDraw');

    //Campaign
    Route::get('/campaigns', 'HomeController@index');
    Route::get('/campaigns/create', 'HomeController@index');
    Route::get('/campaigns/{id}/edit', 'HomeController@index');
    Route::get('/campaign/list/{page?}', 'CampaignController@list');
    Route::post('/campaign/search/{page?}', 'CampaignController@search');
    Route::post('/campaign/create', 'CampaignController@store');
    Route::post('/campaign/edit/{id}', 'CampaignController@update');
    Route::get('/campaign/fetch/{id}', 'CampaignController@fetch');
    Route::delete('/campaign/remove/{id}', 'CampaignController@destroy');

    Route::get('/scratch-card-campaigns/{id}/templates', 'HomeController@index');
    Route::get('/reward-point-campaigns/{id}/templates', 'HomeController@index');
    Route::get('/campaign/template/fetch/{id}', 'CampaignController@fetch_template');
    Route::post('/campaign/template/update/{id}', 'CampaignController@update_template');

    //Business Types
    Route::get('/business-types', 'HomeController@index');
    Route::get('/business-types/create', 'HomeController@index');
    Route::get('/business-types/{id}/edit', 'HomeController@index');
    Route::get('/business-type/list/{page?}', 'BusinessTypeController@list');
    Route::post('/business-type/search/{page?}', 'BusinessTypeController@search');
    Route::post('/business-type/create', 'BusinessTypeController@store');
    Route::post('/business-type/edit/{id}', 'BusinessTypeController@update');
    Route::get('/business-type/fetch/{id}', 'BusinessTypeController@fetch');
    Route::delete('/business-type/remove/{id}', 'BusinessTypeController@destroy');

    //Shops
    Route::get('/shops', 'HomeController@index');
    Route::get('/shops/create', 'HomeController@index');
    Route::get('/shops/{id}/edit', 'HomeController@index');
    Route::get('/shops/{id}/purchases', 'HomeController@index');
    Route::get('/shop/list/{page?}', 'ShopController@list');
    Route::post('/shop/search/{page?}', 'ShopController@search');
    Route::post('/shop/create', 'ShopController@store');
    Route::post('/shop/edit/{id}', 'ShopController@update');
    Route::get('/shop/fetch/{id}', 'ShopController@fetch');
    Route::delete('/shop/remove/{id}', 'ShopController@destroy');
    Route::get('/fetch/shop/business-types', 'ShopController@getBusinessTypes');

    Route::get('/activities', 'HomeController@index');
    Route::get('/activity/logs', 'HomeController@Activities');

    Route::get('/promoter/{id}/purchases', 'HomeController@index');
    Route::get('/promoter/{id}/scratch-cards', 'HomeController@index');
    Route::get('/promoter/{id}/raffle-draws', 'HomeController@index');
    Route::get('/promoter/{id}/vouchers', 'HomeController@index');
    Route::get('/promoter/{id}/details', 'PromoterController@info');

    //Route::get('/purchase/mirror', 'HomeController@index');
    Route::get('/report/purchases', 'HomeController@index');
    Route::get('/report/customers', 'HomeController@index');
    Route::get('/report/gifts', 'HomeController@index');
    Route::get('/report/vouchers', 'HomeController@index');


    Route::get('/reports/customer/by-countries', 'HomeController@index');
    Route::get('/reports/customer/by-vouchers', 'HomeController@index');
    Route::get('/reports/customer/by-gifts', 'HomeController@index');

    Route::get('/reports/voucher/by-campaigns', 'HomeController@index');
    Route::get('/reports/voucher/by-days', 'HomeController@index');
    Route::get('/reports/voucher/by-promoters', 'HomeController@index');

    Route::get('/reports/gift/by-campaigns', 'HomeController@index');
    Route::get('/reports/gift/by-days', 'HomeController@index');
    Route::get('/reports/gift/by-promoters', 'HomeController@index');


    Route::get('/reports/customer/by-countries', 'HomeController@index');
    Route::get('/reports/customer/by-countries', 'HomeController@index');
    Route::get('/reports/customer/by-countries', 'HomeController@index');
    Route::get('/reports/customer/by-countries', 'HomeController@index');
    Route::get('/reports/customer/by-countries', 'HomeController@index');
    Route::get('/reports/customer/by-countries', 'HomeController@index');
    Route::get('/reports/customer/by-countries', 'HomeController@index');

    Route::get('/reports/sales' , 'HomeController@index');
    Route::get('/reports/sale/by-shops' , 'HomeController@index');
    Route::get('/reports/sale/by-categories' , 'HomeController@index');
    Route::get('/reports/sale/by-promoters' , 'HomeController@index');
    Route::get('/reports/sale/by-customers' , 'HomeController@index');
    Route::get('/reports/sale/by-countries' , 'HomeController@index');
    Route::get('/reports/sale/by-campaigns' , 'HomeController@index');
    
});

Route::group(['prefix'=>'/' , 'middleware' => 'auth'], function(){ 
    
    //Scratch Card Campaigns
    Route::get('/scratch-card-campaigns', 'HomeController@index');
    Route::get('/scratch-card-campaigns/create', 'HomeController@index');
    Route::get('/scratch-card-campaigns/{id}/edit', 'HomeController@index');
    Route::get('/scratch-card-campaign/list/{page?}', 'ScratchCardCampaignController@list');
    Route::post('/scratch-card-campaign/search/{page?}', 'ScratchCardCampaignController@search');
    Route::post('/scratch-card-campaign/create', 'ScratchCardCampaignController@store');
    Route::post('/scratch-card-campaign/edit/{id}', 'ScratchCardCampaignController@update');
    Route::get('/scratch-card-campaign/fetch/{id}', 'ScratchCardCampaignController@fetch');
    Route::delete('/scratch-card-campaign/remove/{id}', 'ScratchCardCampaignController@destroy');
    Route::get('/scratch-card-campaign/activated/{id}' , 'ScratchCardCampaignController@activated');
    Route::get('/campaigns/{id}/move/unused/gifts', 'ScratchCardCampaignController@move_unused_gifts');

    Route::post('/campaigns/gift/imports', 'ScratchCardCampaignController@import');
    Route::get('/campaigns/{id}/gifts/imports', 'HomeController@index');

    //Reward Point Campaigns
    Route::get('/reward-point-campaigns', 'HomeController@index');
    Route::get('/reward-point-campaigns/create', 'HomeController@index');
    Route::get('/reward-point-campaigns/{id}/edit', 'HomeController@index');
    Route::get('/reward-point-campaign/list/{page?}', 'RewardPointCampaignController@list');
    Route::post('/reward-point-campaign/search/{page?}', 'RewardPointCampaignController@search');
    Route::post('/reward-point-campaign/create', 'RewardPointCampaignController@store');
    Route::post('/reward-point-campaign/edit/{id}', 'RewardPointCampaignController@update');
    Route::get('/reward-point-campaign/fetch/{id}', 'RewardPointCampaignController@fetch');
    Route::delete('/reward-point-campaign/remove/{id}', 'RewardPointCampaignController@destroy');
    Route::get('/reward-point-campaign/activated/{id}' , 'RewardPointCampaignController@activated');  

    //Reward Point Settings
    Route::post('/reward-point-campaign/update/settings', 'RewardPointCampaignController@update');
    Route::get('/reward-point-campaign/fetch/settings', 'RewardPointCampaignController@fetch');
    

    //Gifts
    Route::get('/campaigns/{campaign_id}/gifts', 'HomeController@index');
    Route::get('/campaigns/{campaign_id}/gifts/create', 'HomeController@index');
    Route::get('/campaigns/{campaign_id}/gifts/{id}/edit', 'HomeController@index');
    Route::get('/gift/list/{page?}', 'GiftController@list');
    Route::post('/gift/search/{page?}', 'GiftController@search');
    Route::post('/gift/create', 'GiftController@store');
    Route::post('/gift/edit/{id}', 'GiftController@update');
    Route::get('/gift/fetch/{id}', 'GiftController@fetch');
    Route::delete('/gift/remove/{id}', 'GiftController@destroy');
    Route::get('/campaign/gift/{campaign_id}/report', 'GiftController@ItemReport');
    
    //Gifts
    Route::get('/gifts/{gift_id}/items', 'HomeController@index');
    Route::get('/gifts/{gift_id}/items/create', 'HomeController@index');
    Route::get('/gifts/{gift_id}/items/{id}/edit', 'HomeController@index');
    Route::get('/gift/item/list/{page?}', 'GiftItemController@list');
    Route::post('/gift/item/search/{page?}', 'GiftItemController@search');
    Route::post('/gift/item/create', 'GiftItemController@store');
    Route::post('/gift/item/edit/{id}', 'GiftItemController@update');
    Route::get('/gift/item/fetch/{id}', 'GiftItemController@fetch');
    Route::delete('/gift/item/remove/{id}', 'GiftItemController@destroy');
    Route::get('/gift/item/{id}/report', 'GiftItemController@ItemReport');

    Route::get('/gifts/{gift_id}/items/edit', 'HomeController@index');
    Route::get('/gift/items/list', 'GiftItemController@items');
    Route::post('/gift/items', 'GiftItemController@updateItems');
    Route::delete('/gift/items', 'GiftItemController@deleteItems');
    
    Route::get('/shop/settings/create', 'ShopController@create_shops');

    Route::get('/dashboard', 'HomeController@index');

    Route::get('/get/dashboard/data', 'HomeController@getDashboardData');
    Route::get('/change/password', 'HomeController@index');
    Route::post('/update/password', 'SettingController@update_password');
    Route::get('/user-register-email-preview', 'SettingController@prev_view');

    Route::get('/raffle-draw-email-preview/{id}', 'SettingController@prev_view1');
    Route::get('/scratch-card-email-preview/{id}', 'SettingController@prev_view2');
    Route::get('/voucher-email-preview/{id}', 'SettingController@prev_view3');
    
    //Customer Users
    Route::get('/customers', 'HomeController@index');
    Route::get('/customers/create', 'HomeController@index');
    Route::get('/customers/{id}/edit', 'HomeController@index');
    Route::get('/customers/create/purchase', 'HomeController@index');
    Route::get('/customers/{customer_id}/voucher/create', 'HomeController@index');
    Route::get('/customers/{customer_id}/spin-and-wins', 'HomeController@index');
    Route::get('/customers/{customer_id}/scratchcards', 'HomeController@index');
    Route::get('/customer/list/{page?}', 'CustomerController@list');
    Route::post('/customer/search/{page?}', 'CustomerController@search');
    Route::post('/customer/create', 'CustomerController@store');
    Route::post('/customer/edit/{id}', 'CustomerController@update');
    Route::get('/customer/fetch/{id}', 'CustomerController@fetch');
    Route::delete('/customer/remove/{id}', 'CustomerController@destroy');
    Route::post('/customer/create/purchase', 'CustomerController@purchase');

    Route::get('/customer/{id}/purchases', 'HomeController@index');
    Route::get('/customer/{id}/scratch-cards', 'HomeController@index');
    Route::get('/customer/{id}/raffle-draws', 'HomeController@index');
    Route::get('/customer/{id}/shop-for-free-report', 'HomeController@index');
    Route::get('/customer/{id}/vouchers', 'HomeController@index');
    Route::get('/customer/{id}/details', 'CustomerController@info');
    Route::get('/customer/{id}/details', 'CustomerController@info');
    Route::post('/customer/{id}/raffle-draw/search/{page?}', 'CustomerController@raffledraw');

    Route::get('/customer/{customer_id}/scratch-cards', 'HomeController@index');
    Route::get('/customer/scratch-card/list', 'ScratchCardController@list');
    Route::get('/customer/{customer_id}/scratch-card/count', 'CustomerController@ScratchCardCount');
    Route::get('/customer/{customer_id}/skip/scratch-cards', 'ScratchCardController@skipcards');
    
       
    Route::get('/fetch/scratch-card/campaigns', 'ScratchCardCampaignController@fetchCampaigns');
    Route::get('/fetch/reward-point/campaigns', 'RewardPointCampaignController@fetchCampaigns');
    
    Route::get('/chart/reports', 'HomeController@index');
    
    //Purchases
    Route::get('/purchases', 'HomeController@index');
    Route::get('/purchases/create', 'HomeController@index');
    Route::get('/purchase/list/{page?}', 'PurchaseController@list');
    Route::post('/purchase/search/{page?}', 'PurchaseController@search');
    Route::post('/purchase/create', 'PurchaseController@store');
    Route::get('/purchase/fetch/{id}', 'PurchaseController@fetch');
    Route::post('/purchase-customer/search', 'PurchaseController@searchCustomer');
    Route::get('/get-purchase-points' , 'PurchaseController@getPointSettings');

    //Vouchers
    Route::get('/vouchers', 'HomeController@index');
    Route::get('/vouchers/create', 'HomeController@index');
    Route::get('/vouchers/{id}/edit', 'HomeController@index');
    Route::get('/voucher/list/{page?}', 'VoucherController@list');
    Route::post('/voucher/search/{page?}', 'VoucherController@search');
    Route::post('/voucher/create', 'VoucherController@store');
    Route::post('/voucher/edit/{id}', 'VoucherController@update');
    Route::get('/voucher/fetch/{id}', 'VoucherController@fetch');
    Route::delete('/voucher/remove/{id}', 'VoucherController@destroy');    
    Route::get('/fetch/voucher/limit/{id}', 'VoucherController@VoucherLimit');
    Route::get('/purchases/voucher/{id}', 'HomeController@index');
    Route::get('/voucher/redeemed/{id}', 'VoucherController@redeemed'); 
    
    Route::get('/fetch/shops', 'ShopController@getShops');
    Route::get('/shop/{id}/details', 'ShopController@shop_details');

    //Reward Points
    Route::get('/reward-points', 'HomeController@index');
    Route::get('/reward-points/{id}/vouchers', 'HomeController@index');
    Route::get('/reward-point/list/{page?}', 'RewardPointController@list');
    Route::post('/reward-point/edit/{id}', 'RewardPointController@update');
    Route::post('/reward-point/search/{page?}', 'RewardPointController@search');
    Route::post('/reward-point/voucher/create', 'RewardPointController@store');
    Route::post('/reward-point/{id}/vouchers', 'RewardPointController@vouchers');
    Route::delete('/reward-point/remove/{id}', 'RewardPointController@destroy');
    Route::get('/campaign/{campaign_id}/customer/{customer_id}/voucher/limit', 'RewardPointController@limit');
    Route::get('/reward-point/voucher/redeemed/{id}', 'RewardPointController@redeemed'); 

    //Scratch Cards
    Route::get('/scratch-cards', 'HomeController@index');
    Route::get('/scratch-cards/{id}/vouchers', 'HomeController@index');
    Route::get('/scratch-card-campaigns/{id}/winners', 'HomeController@index');
    Route::get('/scratch-card/list/{page?}', 'ScratchCardController@list');
    Route::post('/scratch-card/search/{page?}', 'ScratchCardController@search');
    Route::delete('/scratch-card/remove/{id}', 'ScratchCardController@destroy');
    Route::get('/scratch-card/accept/request', 'ScratchCardController@accepted');
    Route::get('/scratch-card/{id}/scratched', 'ScratchCardController@scratched');
    Route::get('/scratch/card/list', 'ScratchCardController@customer_cards');
    Route::get('/scratch/card/{id}/scratched-view', 'ScratchCardController@show');
    Route::get('/customer/{id}/cratchcards', 'ScratchCardController@ScratchCards');
    Route::get('/scratch-card/winner/notification/{id}', 'ScratchCardController@send_gift_nofity');
    

    Route::get('/customers/{id}/scratch-cards', 'HomeController@index');
    Route::get('/customer/{id}/unredeemed/scratch-cards', 'ScratchCardController@unredeemed_cards');
    Route::post('/customer/{id}/scratch-card/infos', 'ScratchCardController@cardinfos');
    Route::get('/customer/{id}/scratched/cards', 'ScratchCardController@today_gifts');

    Route::get('/get-purchase-cards' , 'PurchaseController@ScratchCard');


    Route::get('/purchases/report' , 'HomeController@index');
    Route::get('/scratchcards/report' , 'HomeController@index');
    Route::get('/vouchers/report' , 'HomeController@index');
    Route::get('/gifts/report' , 'HomeController@index');    

    Route::get('/fetch/purchase/action' , 'PurchaseController@fetch_action');
    Route::post('/update/purchase/action' , 'PurchaseController@action');
    Route::get('/update/user-action/{customer_id}' , 'PurchaseController@customer_action');
    Route::delete('/remove/purchase/action' , 'PurchaseController@destory_action');

    Route::get('/customer/purchase/reports' , 'CustomerReports\PurchaseController@customers');
    Route::get('/customer/voucher/reports' , 'CustomerReports\PurchaseController@vouchers');
    Route::get('/customer/countrywise/reports' , 'CustomerReports\PurchaseController@countries');
    Route::get('/customer/scratchcard-by-campaign/reports' , 'CustomerReports\PurchaseController@scratchcardByCampaign');
    Route::get('/customer/voucher-by-campaign/reports' , 'CustomerReports\PurchaseController@voucherByCampaign');

    Route::get('/reports/purchase-by-customers' , 'HomeController@index');
    Route::get('/reports/purchase-by-amounts' , 'HomeController@index');
    Route::get('/report/purchase-by-customers' , 'PurchaseReportController@purchaseByCustomer');
    Route::get('/report/purchase-by-amounts' , 'PurchaseReportController@purchaseAmountCustomer');

    Route::get('/campaign/gifts/report' , 'GiftReportController@GiftReports');
});

Route::group(['prefix'=>'/' , 'namespace' => 'Reports' , 'middleware' => ['auth']], function(){ 
    
    Route::get('/purchase/reports' , 'PurchaseController@list');
    Route::get('/purchase/exports' , 'PurchaseController@export');
    Route::get('/purchase/{type}/exports' , 'PurchaseController@export_pdf');
    Route::get('/fetch/users' , 'PurchaseController@searchUsers');
    Route::get('/fetch/campaigns' , 'PurchaseController@searchCampaigns');
    Route::get('/fetch/purchase/linechart' , 'PurchaseController@line_chart');

    Route::get('/scratch-card/reports' , 'ScratchCardController@list');
    Route::get('/scratch-card/exports' , 'ScratchCardController@export');
    Route::get('/scratch-card/{type}/exports' , 'ScratchCardController@export_pdf');
    Route::get('/fetch/scratch-card/linechart' , 'ScratchCardController@line_chart');

    Route::get('/voucher/reports' , 'VoucherController@list');
    Route::get('/voucher/exports' , 'VoucherController@export');
    Route::get('/voucher/{type}/exports' , 'VoucherController@export_pdf');
    Route::get('/fetch/voucher/linechart' , 'VoucherController@line_chart');
    Route::get('/voucher/by-promoters' , 'VoucherController@voucherByPromoters');

    Route::get('/gift/reports' , 'GiftController@list');
    Route::get('/gift/exports' , 'GiftController@export');
    Route::get('/gift/{type}/exports' , 'GiftController@export_pdf');
    Route::get('/fetch/gifts' , 'GiftController@search');
    Route::get('/fetch/gift/linechart' , 'GiftController@ItemReport');

    //Customer Report
    Route::get('/customer/top-country/list' , 'CustomerController@Countries');
    Route::get('/customer/top-countries' , 'CustomerController@TopCountries');
    Route::get('/customer/sale-by-country/list' , 'CustomerController@CountrySaleList');
    Route::get('/customer/sale-by-countries' , 'CustomerController@SaleByCountries');

    Route::get('/customer/customer-wise-vouchers' , 'CustomerController@CustomerWiseVouchers');
    Route::get('/customer/date-wise-vouchers' , 'CustomerController@DateWiseVouchers');
    Route::get('/customer/campaign-wise-vouchers' , 'CustomerController@CampaignWiseVouchers');

    //Sale Reports
    Route::get('/sale/reports' , 'PurchaseController@saleReports');
    Route::get('/sale/report-by-shop' , 'PurchaseController@saleByShop');
    Route::get('/sale/report-by-category' , 'PurchaseController@saleByCategory');
    Route::get('/sale/report-by-customer' , 'PurchaseController@saleByCustomers');
    Route::get('/sale/report-by-promoter' , 'PurchaseController@saleByPromoters');
    Route::get('/sale/top-reports' , 'PurchaseController@sales');
    Route::get('/sale/report-by-campaign' , 'PurchaseController@campaign');

    //Gift Reports
    Route::get('/gift/by-campaigns' , 'GiftController@campaigns');
    Route::get('/gift/by-promoters' , 'GiftController@promoters');
    Route::get('/gift/by-days' , 'GiftController@Days');

    Route::get('/reports/customer/by-country/list' , 'CustomerListController@Countries');
    Route::get('/reports/customer/by-voucher/list' , 'CustomerListController@Vouchers');
    Route::get('/reports/customer/by-gift/list' , 'CustomerListController@Gifts');

    Route::get('/reports/customer/by-country/{type}' , 'CustomerListController@export_country_pdf');
    Route::get('/reports/customer/by-voucher/{type}' , 'CustomerListController@export_voucher_pdf');
    Route::get('/reports/customer/by-gift/{type}' , 'CustomerListController@export_gift_pdf');

    Route::get('/reports/voucher/by-campaign/list' , 'VoucherListController@Campaigns');
    Route::get('/reports/voucher/by-day/list' , 'VoucherListController@Days');
    Route::get('/reports/voucher/by-promoter/list' , 'VoucherListController@Promoters');

    Route::get('/reports/voucher/by-campaign/{type}' , 'VoucherListController@export_campaign_pdf');
    Route::get('/reports/voucher/by-day/{type}' , 'VoucherListController@export_days_pdf');
    Route::get('/reports/voucher/by-promoter/{type}' , 'VoucherListController@export_promoter_pdf');

    Route::get('/reports/gift/by-campaign/list' , 'GiftListController@Campaigns');
    Route::get('/reports/gift/by-day/list' , 'GiftListController@Days');
    Route::get('/reports/gift/by-promoter/list' , 'GiftListController@Promoters');

    Route::get('/reports/gift/by-campaign/{type}' , 'GiftListController@export_campaign_pdf');
    Route::get('/reports/gift/by-day/{type}' , 'GiftListController@export_days_pdf');
    Route::get('/reports/gift/by-promoter/{type}' , 'GiftListController@export_promoter_pdf');

    Route::get('/reports/purchase/by-day/list' , 'PurchaseListController@Days');
    Route::get('/reports/purchase/by-shop/list' , 'PurchaseListController@Shops');
    Route::get('/reports/purchase/by-category/list' , 'PurchaseListController@Categories');
    Route::get('/reports/purchase/by-promoter/list' , 'PurchaseListController@Promoters');
    Route::get('/reports/purchase/by-customer/list' , 'PurchaseListController@Customers');
    Route::get('/reports/purchase/by-country/list' , 'PurchaseListController@Countries');
    Route::get('/reports/purchase/by-campaign/list' , 'PurchaseListController@Campaigns');

    Route::get('/reports/purchase/by-day/{type}' , 'PurchaseListController@export_day_pdf');
    Route::get('/reports/purchase/by-shop/{type}' , 'PurchaseListController@export_shop_pdf');
    Route::get('/reports/purchase/by-category/{type}' , 'PurchaseListController@export_category_pdf');
    Route::get('/reports/purchase/by-promoter/{type}' , 'PurchaseListController@export_promoter_pdf');
    Route::get('/reports/purchase/by-customer/{type}' , 'PurchaseListController@export_customer_pdf');
    Route::get('/reports/purchase/by-country/{type}' , 'PurchaseListController@export_country_pdf');
    Route::get('/reports/purchase/by-campaign/{type}' , 'PurchaseListController@export_campaign_pdf');    
});

Route::get('/purchase-reports' , 'HomeController@index');
Route::get('/purchase-customer-reports' , 'HomeController@index');
Route::get('/purchase-shop-reports' , 'HomeController@index');
Route::get('/purchase-shop-category-reports' , 'HomeController@index');
Route::get('/purchase-campaign-reports' , 'HomeController@index');
Route::get('/purchase-country-reports' , 'HomeController@index');
Route::get('/purchase-promoter-reports' , 'HomeController@index');
Route::get('/reports' , 'HomeController@index');

Route::group(['prefix'=>'/' , 'namespace' => 'Report' , 'middleware' => ['auth']], function(){   
    

    Route::get('/purchase-report' , 'PurchaseController@purchases');
    Route::get('/purchase-customer-report' , 'PurchaseController@customers');
    Route::get('/purchase-shop-report' , 'PurchaseController@shops');
    Route::get('/purchase-shop-category-report' , 'PurchaseController@shop_categories');
    Route::get('/purchase-campaign-report' , 'PurchaseController@campaigns');
    Route::get('/purchase-country-report' , 'PurchaseController@countries');
    Route::get('/purchase-promoter-report' , 'PurchaseController@promoters');

    Route::get('/purchase-report/{type}' , 'PurchaseController@purchase_export');
    Route::get('/purchase-customer-report/{type}' , 'PurchaseController@customer_export');
    Route::get('/purchase-shop-report/{type}' , 'PurchaseController@shop_export');
    Route::get('/purchase-shop-category-report/{type}' , 'PurchaseController@shop_category_export');
    Route::get('/purchase-campaign-report/{type}' , 'PurchaseController@campaign_export');
    Route::get('/purchase-country-report/{type}' , 'PurchaseController@country_export');
    Route::get('/purchase-promoter-report/{type}' , 'PurchaseController@promoter_export');


});

Route::group(['prefix'=>'/' ,  'middleware' => ['auth']], function(){ 


    //Scratch Cards
    Route::get('/spin-and-wins', 'HomeController@index');
    Route::get('/spin-and-wins/{id}/vouchers', 'HomeController@index');
    Route::get('/spin-and-wins/campaigns/{id}/winners', 'HomeController@index');
    Route::get('/spinner/winner/list/{page?}', 'SpinWinnerController@list');
    Route::post('/spinner/winner/search/{page?}', 'SpinWinnerController@search');
    Route::delete('/spinner/winner/remove/{id}', 'SpinWinnerController@destroy');
    Route::get('/spinner/winner/accept/request', 'SpinWinnerController@accepted');
    Route::get('/spinner/winner/{id}/spun', 'SpinWinnerController@scratched');
    Route::get('/spinner/winner/card/list', 'SpinWinnerController@customer_cards');
    Route::get('/spinner/winner/card/{id}/scratched-view', 'SpinWinnerController@show');
    Route::get('/customer/{id}/spin/completed', 'SpinWinnerController@SpunCompleted');
    Route::get('/spinner/winner/notification/{id}', 'SpinWinnerController@send_gift_nofity');

    Route::get('/spinner/activated/{id}' , 'SpinnerController@activated');
    Route::get('/spinner/{id}/move/unused/gifts', 'SpinnerController@move_unused_gifts');

    //Spin And Win
    Route::get('/spin-and-wins', 'HomeController@index');
    Route::get('/spin-and-wins/create', 'HomeController@index');
    Route::get('/spin-and-wins/{id}/edit', 'HomeController@index');
    Route::get('/spinner/list/{page?}', 'SpinnerController@list');
    Route::post('/spinner/search/{page?}', 'SpinnerController@search');
    Route::post('/spinner/create', 'SpinnerController@store');
    Route::post('/spinner/edit/{id}', 'SpinnerController@update');
    Route::get('/spinner/fetch/{id}', 'SpinnerController@fetch');
    Route::delete('/spinner/remove/{id}', 'SpinnerController@destroy');
    Route::post('/spinner/gift-item/create', 'SpinnerController@create_item');
    Route::post('/spinner/gift-item/edit/{id}', 'SpinnerController@update_item');
    Route::get('/spinner/gift-item/fetch/{id}', 'SpinnerController@fetch_item');
    Route::delete('/spinner/gift-item/{id}', 'SpinnerController@destory_item');

    //Spinner Gifts
    Route::get('/spin-and-wins/{spinner_id}/gifts', 'HomeController@index');
    Route::get('/spin-and-wins/{spinner_id}/gifts/create', 'HomeController@index');
    Route::get('/spin-and-wins/{spinner_id}/gifts/{id}/edit', 'HomeController@index');
    Route::get('/spinner/gift/list/{page?}', 'SpinGiftController@list');
    Route::post('/spinner/gift/search/{page?}', 'SpinGiftController@search');
    Route::post('/spinner/gift/create', 'SpinGiftController@store');
    Route::post('/spinner/gift/edit/{id}', 'SpinGiftController@update');
    Route::get('/spinner/gift/fetch/{id}', 'SpinGiftController@fetch');
    Route::delete('/spinner/gift/remove/{id}', 'SpinGiftController@destroy');
    Route::get('/spinner/gift/{spinner_id}/report', 'SpinGiftController@ItemReport');

    //Spin And Win Gift Items
    Route::get('/spin-and-wins/gifts/{gift_id}/items', 'HomeController@index');
    Route::get('/spin-and-wins/gifts/{gift_id}/items/create', 'HomeController@index');
    Route::get('/spin-and-wins/gifts/{gift_id}/items/{id}/edit', 'HomeController@index');
    Route::get('/spinner/gift/item/list/{page?}', 'SpinGiftItemController@list');
    Route::post('/spinner/gift/item/search/{page?}', 'SpinGiftItemController@search');
    Route::post('/spinner/gift/item/create', 'SpinGiftItemController@store');
    Route::post('/spinner/gift/item/edit/{id}', 'SpinGiftItemController@update');
    Route::get('/spinner/gift/item/fetch/{id}', 'SpinGiftItemController@fetch');
    Route::delete('/spinner/gift/item/remove/{id}', 'SpinGiftItemController@destroy');
    Route::get('/spinner/gift/item/{id}/report', 'SpinGiftItemController@ItemReport');

    Route::post('/customer/spinandwins/search/{page?}', 'SpinWinnerController@search');

    Route::get('/spin-and-wins/gifts/{gift_id}/items/edit', 'HomeController@index');
    Route::get('/spinner/gift/items/list', 'SpinGiftItemController@items');
    Route::post('/spinner/gift/items', 'SpinGiftItemController@updateItems');
    Route::delete('/spinner/gift/items', 'SpinGiftItemController@deleteItems');

    Route::post('/spinner/gift/imports', 'SpinnerController@import');
    Route::get('/spin-and-wins/{id}/gifts/imports', 'HomeController@index');

    Route::get('/spinwinners', 'HomeController@index');

    Route::get('/customers/{id}/spin-and-win', 'HomeController@index');
    Route::get('/customer/{id}/unspun/spinners', 'SpinWinnerController@unspuns');
    Route::post('/customer/{id}/scratch-card/infos', 'SpinWinnerController@cardinfos');
    Route::get('/customer/{id}/spun/gifts', 'SpinWinnerController@today_gifts');

    Route::get('/fetch/spinner/action' , 'PurchaseController@fetch_spinner_action');
    Route::post('/update/spinner/action' , 'PurchaseController@spinner_action');
    Route::get('/update/user-spinner/{customer_id}' , 'PurchaseController@customer_spinner_action');
    Route::delete('/remove/spinner/action' , 'PurchaseController@destory_spinner_action');

    //Reports 
    Route::get('/spinandwin/sale-reports' , 'HomeController@index');
    Route::get('/spinandwin/list' , 'SpinnerReport\SaleController@spinners');
    Route::post('/spinandwin/sale-report/list/{id?}' , 'SpinnerReport\SaleController@index');
    Route::post('/spinandwin/total-sale-report' , 'SpinnerReport\SaleController@totalReport');
    Route::get('/spinandwin/sale/report/exports' , 'SpinnerReport\SaleController@exports');
    Route::get('/spinandwin/sale/{type}/report' , 'SpinnerReport\SaleController@pdf');

    Route::get('/campaign-loss/imports' , 'HomeController@index');
    Route::post('/campaign-loss/imports' , 'CampaignController@loss');


    //Customer Reports 
    Route::get('/spinandwin/customer-reports' , 'HomeController@index');
    Route::post('/spinandwin/customer-report/list/{id?}' , 'SpinnerReport\CustomerController@index');
    Route::post('/spinandwin/total-customer-report' , 'SpinnerReport\CustomerController@totalReport');
    Route::get('/spinandwin/customer/report/exports' , 'SpinnerReport\CustomerController@exports');
    Route::get('/spinandwin/customer/{type}/report' , 'SpinnerReport\CustomerController@pdf');


    Route::get('/spin-and-win', function()
    {
       return view('layout.spin-and-win');
    });

});

Route::group(['prefix'=>'/' ,  'middleware' => ['auth']], function(){ 

    Route::get('/admin/create', 'FreeGiftCampaignController@list');

    //Free Gift Campaigns
    Route::get('/campaign-groups/{id}/free-gift-campaigns', 'HomeController@index');
    Route::get('/campaign-groups/{id}/create-free-gift-campaigns', 'HomeController@index');
    Route::get('/campaign-groups/{id}/free-gift-campaigns/create', 'HomeController@index');
    Route::get('/campaign-groups/{group_id}/free-gift-campaigns/{id}/edit', 'HomeController@index');
    Route::get('/free-gift-campaign/list/{page?}', 'FreeGiftCampaignController@list');
    Route::post('/free-gift-campaign/search/{page?}', 'FreeGiftCampaignController@search');
    Route::post('/free-gift-campaign/create', 'FreeGiftCampaignController@store');
    Route::post('/free-gift-campaign/edit/{id}', 'FreeGiftCampaignController@update');
    Route::get('/free-gift-campaign/fetch/{id}', 'FreeGiftCampaignController@fetch');
    Route::delete('/free-gift-campaign/remove/{id}', 'FreeGiftCampaignController@destroy');
    Route::get('/free-gift-campaign/activated/{id}' , 'FreeGiftCampaignController@activated'); 
    Route::post('/search-free-gift-campaign' , 'FreeGiftCampaignController@search_gifts');
    Route::post('/assign-free-gift-item' , 'FreeGiftCampaignController@assign_gift'); 
    Route::post('/customer-free-gift/edit/{id}', 'FreeGiftCampaignController@update_gift');

    //Free Gift Settings
    Route::post('/free-gift-campaign/update/settings', 'FreeGiftCampaignController@update');
    Route::get('/free-gift-campaign/fetch/settings', 'FreeGiftCampaignController@fetch');

    //Campaign Gifts
    Route::get('/campaign-groups', 'HomeController@index');
    Route::get('/campaign-groups/create', 'HomeController@index');
    Route::get('/campaign-groups/{id}/edit', 'HomeController@index');
    Route::get('/campaign-group/list/{page?}', 'CampaignGroupController@list');
    Route::post('/campaign-group/search/{page?}', 'CampaignGroupController@search');
    Route::post('/campaign-group/create', 'CampaignGroupController@store');
    Route::post('/campaign-group/edit/{id}', 'CampaignGroupController@update');
    Route::get('/campaign-group/fetch/{id}', 'CampaignGroupController@fetch');
    Route::delete('/campaign-group/remove/{id}', 'CampaignGroupController@destroy');

    //Gifts
    Route::get('/free-gift-campaigns/{campaign_id}/gifts', 'HomeController@index');
    Route::get('/free-gift-campaigns/{campaign_id}/gifts/create', 'HomeController@index');
    Route::get('/free-gift-campaigns/{campaign_id}/gifts/{id}/edit', 'HomeController@index');
    Route::get('/free-gift/list/{page?}', 'FreeGiftController@list');
    Route::post('/free-gift/search/{page?}', 'FreeGiftController@search');
    Route::post('/free-gift/create', 'FreeGiftController@store');
    Route::post('/free-gift/edit/{id}', 'FreeGiftController@update');
    Route::get('/free-gift/fetch/{id}', 'FreeGiftController@fetch');
    Route::delete('/free-gift/remove/{id}', 'FreeGiftController@destroy');
    Route::get('/campaign/free-gift/{campaign_id}/report', 'FreeGiftController@ItemReport');    

    //Free Gift Items
    Route::get('/free-gifts/{gift_id}/items', 'HomeController@index');
    Route::get('/free-gifts/{gift_id}/items/create', 'HomeController@index');
    Route::get('/free-gifts/{gift_id}/items/{id}/edit', 'HomeController@index');
    Route::get('/free-gift/item/list/{page?}', 'GiftItemController@list');
    Route::post('/free-gift/item/search/{page?}', 'GiftItemController@search');
    Route::post('/free-gift/item/create', 'GiftItemController@store');
    Route::post('/free-gift/item/edit/{id}', 'GiftItemController@update');
    Route::get('/free-gift/item/fetch/{id}', 'GiftItemController@fetch');
    Route::delete('/free-gift/item/remove/{id}', 'GiftItemController@destroy');
    Route::get('/free-gift/item/{id}/report', 'GiftItemController@ItemReport');

    Route::get('/free-gifts/{gift_id}/items/edit', 'HomeController@index');
    Route::get('/free-gift/items/list', 'GiftItemController@items');
    Route::post('/free-gift/items', 'GiftItemController@updateItems');
    Route::delete('/free-gift/items', 'GiftItemController@deleteItems');

    Route::delete('/free-gift/items', 'GiftItemController@deleteItems');
    Route::delete('/free-gift/items', 'GiftItemController@deleteItems');

    Route::post('/free-gift-group/create', 'FreeGiftCampaignController@create_group');
    Route::post('/free-gift-group/list', 'FreeGiftCampaignController@list_group');
    Route::get('/free-campaign-group/list', 'FreeGiftCampaignController@list_group');

    Route::post('/free-gift-item/search/list', 'FreeGiftItemController@search_gifts');

    Route::get('/create-free-gift-campaigns', 'HomeController@index');

    Route::get('/customer-free-gifts', 'HomeController@index');
    Route::get('/customer-free-gift/list', 'CampaignGroupController@customer_gifts');

    Route::get('/customer-free-gifts/reports', 'HomeController@index');
    Route::get('/customer-free-gift/reports', 'CampaignGroupController@reports'); 
    Route::get('/customer-free-gift/export', 'CampaignGroupController@export');    

    Route::get('/free-gift/mirror', function () {
        return view('layout.free-gift-mirror' , [ "user_role" => Auth::user()->role ]);
    });

    Route::get('/get-gift', function(){
       return App\Gift::with('campaign')
                              ->inRandomOrder()
                              ->withCount('items')
                              ->whereHas('items', function (Builder $query) {
                                $query->whereNull('gifted_at');
                            }, '>', 0)
                              ->limit(1)
                              ->first();
    });


    Route::get('/get-campaign', function(){
        return App\Campaign::whereDate('start_at' , '<=' , Carbon::now())
        ->whereDate('end_at' , '>=' , Carbon::now())
        ->where('campaign_type' , 'free_shop')
        ->whereHas('Gifts.Items', function (Builder $query) {
            $query->whereNull('gifted_at');
        }, '>', 0)
          ->get();
     });
 
    

    
});