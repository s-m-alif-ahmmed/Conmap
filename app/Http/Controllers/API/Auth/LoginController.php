<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return $this->error('The provided credentials do not match our records.',401,[
                'email' => 'The provided credentials do not match our records.'
            ]);
        }

        if (Auth::user()->email_verified_at === null){
            return $this->error('Email not verified.',403);
        }

        $user = Auth::user();
        if ($user->role == 'Admin' || $user->role == 'Super Admin'){
            return $this->error('You are not authorized to access this page.', 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Login Successful',
            'token_type' => 'Bearer',
            'token' => $user->createToken('AuthToken')->plainTextToken,
            'data' => $user
        ]);
    }

    public function userDetails()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error('Unauthorized access.', 401);
        }

        // Check if user is subscribed (if it's a boolean field)
        if ($user->subscribed) {
            $user->load('subscribed');
        }

        return $this->success('User details retrieved successfully.', $user);
    }

    public function logout(Request $request)
    {
        try {
            // Revoke the current user’s token
            $request->user()->currentAccessToken()->delete();
            // Return a response indicating the user was logged out
            return $this->ok('Logged out successfully.');
        }catch (\Exception $exception){
            return $this->error($exception->getMessage(),500);
        }
    }
}
