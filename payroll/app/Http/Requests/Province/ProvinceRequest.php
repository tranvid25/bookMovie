<?php

namespace App\Http\Requests\Province;

use Illuminate\Foundation\Http\FormRequest;

class ProvinceRequest extends FormRequest
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
           'tenTinh'=>'required|string|max:255',
           'hinhAnh' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
           'googlemap'=>'nullable|string|max:512'
        ];
    }
}
