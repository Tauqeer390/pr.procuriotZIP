<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Notifications\UserRegistered;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     *
     * @return [string] message
     */
    public function register(Request $request)
    {
      
        $res = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);
        foreach ($res->errors()->toArray() as $field => $message) {
            $errors[] = [
                'message' => $message[0],
            ];
        }
        if (isset($errors)) {
            return $this->sendError('Validation Error.', $errors);
        }
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $user->save();
        $user->notify(new UserRegistered("Dear ".$user->name .",\n\nYour account have been registered successfully.\n\nThanks,"));
        return $this->sendResponse($user, 'User created successfully.');
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     *
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {

        $res = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user)
        {
            return $this->sendError('User not found.');
        }
        // if($user->is_social == 1) {
        //     return $this->sendError('Please use social login.');
        // }
        if ($user->role != null) {
            return $this->sendError('Admins are not allowed to login.');
        }
        foreach ($res->errors()->toArray() as $field => $message) {
            $errors[] = [
                'message' => $message[0],
            ];
        }
        if (isset($errors)) {
            return $this->sendError('Validation Error.', $errors);
        }
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid Login Credentials.',
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();
        return response()->json([
            'user_id' =>   $user->id,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ]);
    }

    public function socialLogin(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:255',
            'name' => 'required|string',
        ];
        $validate = $this->validateRequest($request, $rules);

        if ($validate == 1) {

            $user = User::where('email', $request->email)->first();
            /* if user is not already created. */
            if ($user == null) {

                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->is_social = 1;
                $user->password = bcrypt($request->email);
                $user->save();
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->token;
                $token->save();
            } else {
                $credentials =array(
                    "email" => $request->email,
                    "password" => $request->email,
                );
                // if($user->is_social != 1)
                // {
                    // return $this->sendError('User already exist.');
                // }
                // if (!Auth::attempt($credentials)) {
                //     return response()->json([
                //         'message' => 'Invalid Login Credentials.',
                //     ], 401);
                // }


                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->token;
                if ($request->remember_me) {
                    $token->expires_at = Carbon::now()->addWeeks(1);
                }
                $token->save();
            }
            return response()->json([
                'user_id' =>   $user->id,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString(),
            ]);
        } else {
            return $this->sendError('Validation Error.', $validate);
        }
    }

}
