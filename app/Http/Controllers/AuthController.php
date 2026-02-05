<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'emailOrUsername' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $loginType = filter_var($request->emailOrUsername, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginType, $request->emailOrUsername)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid login credentials.',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(6)->letters()->numbers()->symbols()],
        ]);

        $request->user()->update([
            'password' => $request->new_password,
        ]);

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return response()->json(['message' => 'Email verified successfully!']);
    }

    public function sendEmailVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Email Verification link sent!']);

    }
}
