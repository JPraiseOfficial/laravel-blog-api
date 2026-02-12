<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email_or_username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $loginType = filter_var($request->email_or_username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginType, $request->email_or_username)->first();

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
            'new_password' => ['required', 'confirmed', PasswordRule::min(6)->letters()->numbers()->symbols()],
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

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $existingEmail = User::where('email', $request->email)->first();

        if (! $existingEmail) {
            return response()->json(['message' => 'Email does not exist'], 404);
        }

        // Delete all password reset token before adding a new one
        PasswordResetToken::where('email', $request->email)->delete();

        // Create Password token and store in the database
        $token = Str::random(40);

        PasswordResetToken::create([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        $frontendUrl = env('APP_URL').'/reset-password?token='.$token.'&email='.$request->email;

        return response()->json(
            [
                'message' => 'Password Reset link has been sent successfully',
                'reset_link' => $frontendUrl,
            ],
            200
        );
    }

    public function frontendResetPasswordRedirect(string $token, Request $request)
    {
        $frontendUrl = env('APP_URL');

        return redirect($frontendUrl.'?token='.$token.'&email='.$request->email);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $request->validated();

        $resetToken = PasswordResetToken::where('email', $request->email)->first();

        if (! $resetToken || ! Hash::check($request->token, $resetToken->token)) {
            return response()->json(['message' => 'Invalid or Expired Token'], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->update(['password' => $request->password]);
        }

        $resetToken->delete();

        return response()->json(['message' => 'Password has been successfully reset'], 200);
    }
}
