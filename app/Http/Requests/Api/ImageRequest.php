<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
{
    public function rules()
    {
        $rules =  [
            'type' => 'required|string|in:avatar,topic',
        ];

        if($this->type == 'avatar')
        {
            $rules['image'] = 'required|mimes:jpeg,bmp,png,gif|dimensions:min_width=200,min_height=200';
        } else {
            $rules['image'] = 'required|mimes:jpeg,bmp,png,gif';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'image.dimensions' => '图片清晰度不够，宽和高至少200像素以上'
        ];
    }
}