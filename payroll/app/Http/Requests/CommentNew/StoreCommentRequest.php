<?php

namespace App\Http\Requests\CommentNew;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'comment'=>'required|string|max:255',
            'maBaiViet'=>'required|integer|exists:tbnews,maBaiViet',
            'parent_id'=>'nullable|integer|exists:comment,maComment'
        ];
    }
}
