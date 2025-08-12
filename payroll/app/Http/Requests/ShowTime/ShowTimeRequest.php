<?php

namespace App\Http\Requests\ShowTime;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowTimeRequest extends FormRequest
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
            'ngayChieu' => 'required|date',
            'gioChieu' => [
                'required',
                Rule::unique('showtime')->where(function ($query) {
                    return $query->where('gioChieu', $this->gioChieu)
                                 ->where('ngayChieu', $this->ngayChieu)
                                 ->where('maRap', $this->maRap);
                })
            ],
            'giaVeThuong' => 'required|numeric|min:0',
            'giaVeVip' => 'required|numeric|min:0',
            'maPhim' => 'required|exists:movies,maPhim',
            'maRap' => 'required|exists:rapchieu,maRap',
        ];
    }
}
