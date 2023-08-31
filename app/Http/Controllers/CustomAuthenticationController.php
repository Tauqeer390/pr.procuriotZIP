<?php

namespace App\Http\Controllers;

use Mail;
use console;
use Session;
use App\User;
use App\Venue;
use Throwable;
use Validator;
use App\Beacon;
use App\Gateway;
use App\Category;
use App\Organization;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\PasswordResetMail;
use App\Mail\AccountVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class CustomAuthenticationController extends Controller
{

    public function loginview(Request $request){
        return view('login');
    }
    public function register(Request $request){
        return view('register');
    }

    public function googleMap(Request $request){
        return view('google_maps_code');
    }
//    public function venuelist(Request $request){
//
//        $data = Venue::where('vendor_id', Auth::id())->get();
//        $venues = Venue::where('vendor_id', Auth::id())->get();
//        return view('venues2', compact('data','venues'));
//    }
    public function venuelist(Request $request){

        $loggedInAdmin = User::find(Auth::id());

        $data = Venue::where('organization', $loggedInAdmin->company)->get();
        $venues = Venue::where('organization', $loggedInAdmin->company)->get();

        return view('venues2', compact('data','venues'));
    }

    public function venuelistForModerator(Request $request){

        $loggedInAdmin = User::find(Auth::id());

        $data = Venue::where('organization', $loggedInAdmin->company)->get();
        $venues = Venue::where('organization', $loggedInAdmin->company)->get();

        return view('venuesForModerators', compact('data','venues'));
    }

    public function EditVenue2($venue_id){ 
       $data = Venue::find($venue_id);
       $categories = Category::all();
        return view('edit_venue', compact('data','categories','venue_id'));
}

    public function addVendor(Request $request){

        $this->validate($request, [
            'first_name'    =>  'required|string|max:15',
            'last_name'     =>   'required|string|max:15',
            'email'         =>  'required|unique:users,email',
            'password'      => 'required|min:8',
            'company'       => 'required'
        ]);

        $form_data = array(
            'first_name'        =>  $request->first_name,
            'last_name'         =>  $request->last_name,
            'email'             =>   $request->email,
            'password'          =>  bcrypt($request->password),
            'company'          =>   $request->company,
        );

    //   $newUser =   User::create($form_data);


        // $details = [
        //     'body' => 'click on the below link to verify your procuriot account',
        //     'link' => '/verify/account/'. $newUser->created_at . '/'. $newUser->id,
        // ];

        $newUser = User::create($form_data);

        // Generate a unique verification token or identifier
        $verificationToken = sha1($newUser->id . $newUser->email . time());
    
        $details = [
            'body' => 'Click on the below link to verify your Procuriot account',
            'link' => '/verify/account/' . $verificationToken,
        ];

        Mail::to($request->email)->send(new AccountVerification($details));

        return back()->with('success', 'Registered successfully , please confirm your email first and then login');

    }

    public function authenticate(Request $request) {

        $request->validate([
            'email'    =>  'required|email',
            'password'     =>  'required'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            if(Auth::user()->active!=true && Auth::user()->active!=1){

                return back()->with('error', 'Please verify your email first');
            }

            $userId = Auth::id();
            $role = Auth::user()->role;
            $email = Auth::user()->email;
            $name = Auth::user()->first_name.' '.Auth::user()->last_name;
            $request->session()->put('authentication_bareer', $userId);
            session(['key' => $userId]);
            $request->session()->put('role', $role);
            $request->session()->put('name', $name);
            $request->session()->put('email', $email);
//            return redirect('/venues');
            if($role=='superadmin'){
                return redirect('/organizations');
            }else if($role=='admin'){
                return redirect('/venues/create');
            }else if($role=='moderator'){
                return redirect('/venues/list/for/moderator');
            }

        }
        else {
            return back()->with('error', 'Wrong email or password');
        }
    }

    public function instructions(Request $request){
        return view('instructions');
    }


    public function dashboard(Request $request){
        $data = Gateway::where('vendor_id', Auth::id())->first();

        if($data){
            $beaconsdata = Beacon::where('gateway_id', $data->id)->where('user_id', Auth::id())->get();
//            return view('welcome', compact('data','beaconsdata'));
            return view('instructions', compact('data','beaconsdata'));
        }
        else
            {
                $locations = Venue::where('vendor_id', Auth::id())->get();

//            return view('welcom2', compact('locations'));
            return view('instructions', compact('locations'));
        }

    }

//    public function beaconsData($id){
//
//        $data = Gateway::where('id', $id)->where('vendor_id', Auth::id())->first();
//
//        $locations = Venue::where('vendor_id', Auth::id())->get();
//
//        $beaconsdata = Beacon::where('gateway_id', $id)->where('user_id', Auth::id())->get();
//
//        $distances = ['Near','Far'];
//        return view('welcome', compact('data','beaconsdata', 'locations', 'distances'));
//    }


    public function beaconsData($id){

        $loggedInAdmin = User::find(Auth::id());

        $data = Gateway::where('id', $id)->first();

        $locations = Venue::where('organization', $loggedInAdmin->company)->get();

        $beaconsdata = Beacon::where('gateway_id', $id)->where('organization', $loggedInAdmin->company)->get();

        $distances = ['Near','Far'];
        return view('welcome', compact('data','beaconsdata', 'locations', 'distances'));
    }

    public function instructions2(Request $request){
        return view('superadmin_instruction');
    }
    public function instructions3(Request $request){
        return view('moderator_nstruction');
    }
    public function beaconsDataForModerator(Request $request){

        $loggedInAdmin = User::find(Auth::id());

//        $data = Venue::where('organization', $loggedInAdmin->company)->get();

$beaconsdata = Beacon::where('organization', $loggedInAdmin->company)->get();

        $distances = ['Near','Far'];
        return view('beaconsForModerator', compact('beaconsdata', 'distances'));
    }

    public function forgetpassword(Request $request){
        return view('forgetpassword');
    }

    public function SendNewPassword(Request $request){

        $validator = Validator::make($request->all(), [
            'password_reset_email'     =>  'required|email|exists:users,email'
        ]);


        if ($validator->passes()) {
            $userData =  User::where('email' , $request->password_reset_email)->first();

            $details = [
                'title' => 'Password Reset Mail',
                'body' => 'click on the below link to reset your satori account password',
//            'link' => 'http://gosatoriapp.com/password/reset/view/'. $request->password_reset_email,
                'link' => 'http://gosatoriapp.com/password/reset/view/'. $userData->created_at,
            ];

            Mail::to($request->password_reset_email)->send(new PasswordResetMail($details));

            return response()->json(['success' => 'password reset link has been send to your email']);
        }
        else
        {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

    }


    public function updatePassword(Request $request) {

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        else {

            try {

                if(User::where('created_at', $request->create_time)
                    ->update([
                        'password' => bcrypt($request->password)
                    ])){

                    return back()->with('success', 'Password updated');
                }
                else {
                    return back()->with('error', 'Password not updated..account may be deleted ');
                }

            } catch (Throwable $e) {
                console.log('Project update error ' . $e );
            }

        }

    }

    public function updateProfile(Request $request){

        if($request->picture != ''){
            $path = public_path().'/profiles/';

            $file = $request->picture;
            $filename = time() . $file->getClientOriginalName();

            if($file->move($path, $filename)){

                if(User::where('id', Auth::id())
                    ->update([
                        'profile_image' => $filename
                    ]))
                {
                    return response()->json(['success' => 'Updated successfully']);
                }
                else{
                    return response()->json(['response_message' => 'Profile not updated in database']);
                }
            }else{
                return response()->json(['response_message' => 'Profile not updated']);
            }

        }
        else{
            return response()->json(['response_message' => 'Profile picture could not empty']);
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->forget('authentication_bareer');
        $request->session()->forget('role');
        $request->session()->forget('name');
        $request->session()->forget('email');
        return redirect('/');
    }
     public function resetPasswordApi(Request $request){
        $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed',
    ]);
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
            $user->save();
        }
    );
    return $status === Password::PASSWORD_RESET
                ? back()->with('success', 'Password reset successfully.')
                : back()->withErrors(['email' => [__($status)]]);
    }

}
