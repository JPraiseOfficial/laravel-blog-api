<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    // Returns all current user's posts
    public function index()
    {
        $posts = request()->user()->posts()
            ->with(['comments', 'likes'])
            ->withCount(['comments', 'likes'])
            ->latest()
            ->paginate(10);

        return response()->json($posts);

    }

    public function store(StorePostRequest $request)
    {
        $post = Post::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post,
        ], 201);
    }

    public function show(Post $post)
    {
        // Load comments and the user who wrote each comment
        $post->load(['comments' => function ($query) {
            $query->latest(); // Show newest comments at the top
        }, 'comments.user:id,name,username', 'comments.likes', 'likes']);

        return response()->json([
            'post' => $post,
        ]);
    }

    public function update(StorePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post,
        ]);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function getOtherUsersPosts(User $user)
    {
        return response()->json([
            $user->posts()
                ->with(['user', 'comments', 'likes'])
                ->withCount(['comments', 'likes'])
                ->latest()
                ->paginate(10)]);
    }

    public function getLatestPosts()
    {
        $posts = Post::with(['user', 'comments', 'likes'])
            ->withCount(['comments', 'likes'])
            ->latest()
            ->paginate(10);

        return response()->json($posts);
    }
}
