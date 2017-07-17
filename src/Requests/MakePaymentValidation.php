<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MakePaymentValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|min:3',
            'ref' => 'required|numeric|min:3',
            'mobile' => 'required|numeric|min:6'
        ];
    }
}
