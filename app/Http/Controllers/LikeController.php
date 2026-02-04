<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikeRequest;
use App\Models\Like;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LikeController extends Controller
{
    use AuthorizesRequests;

    public function toggle(LikeRequest $request)
    {
        $validated = $request->validated();

        // Map the string to the Model class
        $models = [
            'post' => \App\Models\Post::class,
            'comment' => \App\Models\Comment::class,
            'author' => \App\Models\User::class,
        ];

        $attributes = [
            'user_id' => $request->user()->id,
            'likeable_id' => $validated['id'],
            'likeable_type' => $models[$validated['type']],
        ];

        // Logic to like or unlike a post, comment, or author
        $like = Like::where($attributes)->first();

        if ($like) {
            $this->authorize('unlike', $like);
            $like->delete();

            return response()->json(['status' => 'unliked']);
        }

        Like::create($attributes);

        return response()->json(['status' => 'liked']);
    }
}
