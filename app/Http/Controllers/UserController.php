<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        //
    }

    // Get authenticated user's profile or any other user's profile
    public function profile(?string $id = null)
    {
        $id = $id ?? auth()->id();
        $user = User::find($id);

        return response()->json(['user' => $user]);
    }

    // updates users profile
    public function update(UpdateUserRequest $request)
    {
        $user = $request->user();
        $user->update($request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    public function destroy(Request $request)
    {
        $request->user()->tokens()->delete();
        $request->user()->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
