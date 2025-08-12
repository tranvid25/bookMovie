<?php

namespace App\Http\Requests\RapChieu;

use Illuminate\Foundation\Http\FormRequest;

class RapChieuRequest extends FormRequest
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
            'tenRap'=>'required|string|max:255',
            'diaChi'=>'required|string|max:255',
            'maTinh_id'=>'required|exists:province,maTinh'
        ];
    }
}
