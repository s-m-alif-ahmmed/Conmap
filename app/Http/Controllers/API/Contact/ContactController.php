<?php

namespace App\Http\Controllers\API\Contact;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    use ApiResponse;

    public function send(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        if (!$validator) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $admins = User::where('role', 'Super Admin')->get();

        if ($admins->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No admin user found.',
            ], 500);
        }

        // Send email to each admin
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new ContactMail(
                    $request->input('name'),
                    $request->input('email'),
                    $request->input('message'),
                ));
            } catch (\Exception $e) {
                \Log::warning("Failed to send contact mail to {$admin->email}: " . $e->getMessage());
                continue; // Skip and continue to next admin
            }
        }


        return $this->success('Mail sent Successfully!', [], 200);

    }

}
