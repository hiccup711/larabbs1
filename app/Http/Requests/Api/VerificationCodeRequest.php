<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class VerificationCodeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cache_key' => 'required|string',
            'captcha_code' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
            'cache_key' => '图片验证码 key',
            'captcha_code' => '图片验证码'
        ];
    }
}
