<?php

namespace App\Http\Controllers\Auth;

use Session;
use App\User;
//use Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
        if (Auth::viaRemember()) {

            if(!Auth::user()->status){
                return view("auth.login");
            }

            return redirect()->intended('/dashboard');
        }else{
            return view("auth.login");
        }
    }

    public function Register() 
    {
        return view("auth.register");
    }

    public function Authenticate(Request $request)
    {
        $remember = $request->has('remember_me');  
        
        if (Auth::attempt(['email' =>  $request->input('email'), 'password' => $request->input('password') , 'status' => 1], $remember)) {
            return redirect()->intended('/dashboard');
        }else{
            return view("auth.login" , ["error" => "You have entered an invalid username or password."]);
        }
    }

    public function RegisterUser(Request $request)
    {
        //Create a new Company Admin User
        try{
            $admin = User::where('email' , 'admin@example.com')->first();
            if (!isset($admin)) {  
                $admin = new User();
                $admin->first_name = 'admin';
                $admin->last_name = 'user';
                $admin->email = 'admin@example.com';
                $admin->password = Hash::make('123456');
                $admin->mobile = '9874563210';
                $admin->role = 'admin';
                $admin->country_iso = 'in';
                $admin->phone_code = '91';
                $admin->save();
                return redirect('/login');
            }else{
                return  "Admin Created successfully." ;
            }

            
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        Auth::logout();

        return redirect('/login');
    }

    public function ForgotPasswordView()
    {        
        return view("auth.forgot-password");
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if( ! $validator->fails() )
        {
            if( $user = User::where('email', $request->email )->first() )
            {
                Mail::send(new ResetPasswordEmail($user));
                Session::flash('status', 'Reset link send your email successfull.!');
                return redirect()->back();
            }
        }
        Session::flash('error_message', "Your password reset successfull.!");
        return redirect()->back()->with(['error_message' => "Email address doesn't exist.!"]);
    }

    public function ResetView(Request $request)
    {
        try{
            if($request->has('token') && $request->has('email') ){
                if( $email_token = DB::table('password_resets')->where('email' , $request->email)->where('token' , $request->token)->first() ){
                    return view("auth.change-password")->with(['email' => $request->email , 'token' => $request->token ]);
                }
            }  
            Session::flash('error_message', "Your reset link was expired.!");
            return view("auth.forgot-password");
        }catch (\Exception $e){
            Session::flash('error_message', "Your reset link was expired.!");
            return view("auth.forgot-password");
        }
    }

    public function ResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        if($validator->fails() ){
            Session::flash('error_message', "Password doesn't match.!");
            return view("auth.change-password")->with(['email' => $request->email , 'token' => $request->token]);
        }

        try{
            $email_token = DB::table('password_resets')->where('email' , $request->email)->where('token' , $request->token)->first();
            $user = User::where('email' , $email_token->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            Session::flash('status', "Your password reset successfull.!");
            return view("auth.login");
        }catch (\Exception $e){
            Session::flash('error_message', "Your reset link was expired.!");
            return view("auth.change-password")->with(['email' => $request->email , 'token' => $request->token]);
        }
    }
}
