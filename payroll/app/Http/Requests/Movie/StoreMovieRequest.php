<?php

namespace App\Http\Requests\Movie;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
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
            'tenPhim'=>'required|string|max:255',
            'trailer'=>'required|string',
            'hinhAnh'=>'required|image|mimes:jpg,jpeg,png|max:2048',
            'moTa'=>'required|string|max:255',
            'ngayKhoiChieu'=>'required|date',
            'danhGia'=>'required|numeric|min:0|max:10',
            'hot'=>'required|boolean',
            'dangChieu'=>'required|boolean',
            'sapChieu'=>'required|boolean'
        ];
    }
}
