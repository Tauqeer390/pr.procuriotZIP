<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Mail\ResetPasswordMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    use ApiResponseTrait;

    public function forgotPassword(Request $request)
    {

        $res = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);
        foreach ($res->errors()->toArray() as $field => $message) {
            $errors[] = [
                'message' => $message[0],
            ];
        }
        if (isset($errors)) {
            return $this->sendError('Validation Error.', $errors);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError('User not found.');
        }
        $pin = rand(1000, 9999);
        $user->pin = $pin;
        $user->save();
        $mail = Mail::to($user->email)->send(new ResetPasswordMail($user, $pin));
        if (Mail::failures()) {
            return $this->sendError('Mail not sent.');
        } else {
            return Response::json(array("status" => 400, "message" => "We have Emailed You Password Reset Pin.", "data" => array()));
        }

    }

    public function verifyPin(Request $request)
    {
        $rules = [
            'pin' => 'required',
        ];
        $validate = $this->validateRequest($request, $rules);
        if ($validate == 1) {

            $user = User::where('email', $request->email)->first();
            if ($user->pin == $request->pin) {
                return $this->sendResponse(null, 'Please enter your new password.');
            } else {
                return $this->sendError('Invalid Pin.');
            }

        } else {
            return $this->sendError('Validation Error.', $validate);
        }
    }

    public function resetPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string|confirmed',
        ];
        $validate = $this->validateRequest($request, $rules);
        if ($validate == 1) {
            $user = User::where('email', $request->email)->first();
            $password = $request->password;
            $user->forceFill([
                'password' => Hash::make($password),
            ])->setRememberToken(Str::random(60));
            $user->pin = null;
            $user->save();
            return $this->sendResponse($user, 'Password reset successfully.');
        } else {
            return $this->sendError('Validation Error.', $validate);
        }

    }
}
