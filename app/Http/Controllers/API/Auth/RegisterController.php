<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    use ApiResponse;


    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'confirm_password' => 'required',
        ]);

        if ($request->password !== $request->confirm_password)
        {
            return $this->error('Passwords do not match');
        }

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Send OTP
            $otp = $this->send_otp($user);

            if (!$otp) {
                throw new \Exception('Failed to send OTP.');
            }

            DB::commit();
            return $this->success('Registered successfully.', ['otp' => $otp->token,'email' => $user->email], 201);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function send_otp(User $user,$mailType = 'verify')
    {
        $otp  = (new Otp)->generate($user->email, 'numeric', 6, 60);
        $message = $mailType === 'verify' ? 'Verify Your Email Address' : 'Reset Your Password';
        \Mail::to($user->email)->send(new \App\Mail\OTP($otp->token,$user,$message,$mailType));
        return $otp;
    }

    public function resend_otp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            if($user){
                $otp = $this->send_otp($user);
                return $this->success('OTP send successfully.',['otp' => $otp->token],201);
            }else{
                return $this->error('Email not found',404);
            }
        }catch (\Exception $exception){
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function verify_otp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|string|digits:6',
        ]);
        try {
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return $this->error('Email not found',404);
            }

            if($user->email_verified_at !== null){
                return $this->error('Email already verified',404);
            }

            $verify = (new Otp)->validate($request->email, $request->otp);
            if($verify->status){
                $user->email_verified_at = now();
                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Email verified successfully',
                    'token_type' => 'Bearer',
                    'token' => $user->createToken('AuthToken')->plainTextToken,
                    'data' => $user
                ]);
            }else{
                return $this->error($verify->message,404);
            }
        }catch (\Exception $exception){
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function forgot_password(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        try {
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return $this->error('Email not found',404);
            }
            $otp = $this->send_otp($user,'forget');
            return $this->success('OTP send successfully.',['otp' => $otp->token],201);
        }catch (\Exception $exception){
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function forgot_verify_otp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|string|digits:6',
        ]);

        $verify = (new Otp)->validate($request->email, $request->otp);
        if($verify->status){
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return Helper::jsonErrorResponse('Email not found',404);
            }
            $user->reset_password_token = \Str::random(40);
            $user->reset_password_token_expire_at = Carbon::now()->minutes(15);
            $user->save();
            return $this->success('OTP verified successfully',[
                'token' => $user->reset_password_token,
            ],201);
        }else{
            return $this->error($verify->message,404);
        }
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        try {
            $user = User::where('email',$request->email)->where('reset_password_token', $request->token)->first();

            if(!$user){
                return $this->error('Invalid Token',404);
            }
            if ($user->reset_password_token_expire_at > Carbon::now()) {
                return $this->error('Token expired', 404);
            }
            $user->password = bcrypt($request->password);
            $user->reset_password_token = null;
            $user->reset_password_token_expire_at = null;
            $user->save();
            return $this->ok('Password reset successfully');
        }catch (\Exception $exception){
            return $this->error($exception->getMessage(),404);
        }
    }
}
