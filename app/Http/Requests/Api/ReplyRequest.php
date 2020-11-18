<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ReplyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'body' => 'min:2|required'
        ];
    }

    public function messages()
    {
        return [
            // Validation messages
        ];
    }
}
