<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $comments = Comment::where('user_id', request()->user()->id)
            ->with(['post:id,user_id,title,body', 'post.user:id,name,username'])
            ->withCount(['likes'])
            ->latest()
            ->paginate(10);

        return response()->json($comments);
    }

    public function store(CommentRequest $request)
    {
        // Check if the parent is already a reply
        $parentComment = Comment::find($request->parent_comment_id);

        if ($parentComment && $parentComment->parent_id !== null) {
            return response()->json(['message' => 'You cannot reply to a reply.'], 422);
        }

        $data = $request->validated();

        $data['parent_id'] = $request->parent_comment_id;
        $data['user_id'] = $request->user()->id;

        $comment = Comment::create($data);

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment,
        ], 201);
    }

    public function show(Comment $comment)
    {
        return response()->json([
            'comment' => $comment->load(['likes', 'user', 'post']),
        ]);
    }

    public function update(CommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update($request->validated());

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment,
        ]);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
