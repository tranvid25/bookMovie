<?php

namespace App\Http\Requests\CommentMovie;

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
           'comment'=>'required|string|max:500',
           'maPhim'=>'required|integer|exists:movies,maPhim',
           'parent_id'=>'nullable'
        ];
    }
    public function messages()
    {
        return [
            'comment.required' => 'Nội dung bình luận không được để trống',
            'comment.max' => 'Bình luận không quá 500 ký tự',
            'maPhim.required' => 'Phim không hợp lệ',
            'maPhim.exists' => 'Phim không tồn tại',
            'parent_id.exists' => 'Bình luận cha không tồn tại',
        ];
    }
}
