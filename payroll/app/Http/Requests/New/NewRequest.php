<?php

namespace App\Http\Requests\New;

use Illuminate\Foundation\Http\FormRequest;

class NewRequest extends FormRequest
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
            'tieuDe'      => 'required|string|max:255',
            'tacGia'      => 'required|string|max:100',
            'noiDungPhu'  => 'nullable|string',
            'noiDung'     => 'required|string',
            'hinhAnh'     => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'fileName'    => 'nullable|string|max:255',
            'theLoai'     => 'required|string|max:100',
            'maPhim'      => 'nullable|integer|exists:movies,id',
        ];
    }
    public function messages(): array
    {
        return [
            'tieuDe.required'     => 'Vui lòng nhập tiêu đề.',
            'tacGia.required'     => 'Vui lòng nhập tên tác giả.',
            'noiDung.required'    => 'Vui lòng nhập nội dung.',
            'hinhAnh.image'       => 'Hình ảnh không hợp lệ.',
            'hinhAnh.mimes'       => 'Chỉ cho phép hình ảnh JPG, JPEG, PNG hoặc GIF.',
            'hinhAnh.max'         => 'Hình ảnh không được vượt quá 2MB.',
            'maPhim.integer'      => 'Mã phim phải là số.',
            'maPhim.exists'       => 'Mã phim không tồn tại trong hệ thống.',
        ];
    }
}
