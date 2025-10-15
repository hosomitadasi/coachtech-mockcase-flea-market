<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'postcode' => ['required', 'integer'],
            'address' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'postcode.required' => '郵便番号を入力してください',
            'postcode.integer' => '数値を入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
