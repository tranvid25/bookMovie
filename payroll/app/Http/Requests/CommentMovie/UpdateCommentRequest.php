<?php

namespace App\Http\Requests\CommentMovie;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
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
            'comment'=>'required|string|max:255'
        ];
    }
    public function messages()
    {
        return[
            'comment.required'=>'Nội dung bình luận không được để trống',
            'comment.max'=>'Bình luận không quá 500 ký tự'
        ];
    }
}
