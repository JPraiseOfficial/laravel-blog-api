<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'post_id' => ['required', 'integer'],
            'parent_comment_id' => ['nullable', 'integer'], // For replies to comments
            'body' => ['required', 'string', 'max:350'],
        ];
    }
}
