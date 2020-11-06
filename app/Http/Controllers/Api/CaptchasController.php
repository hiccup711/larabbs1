<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchaRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Mews\Captcha\Captcha;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request, Captcha $captchaBuilder)
    {
//        captcha是一个键值对 key => img，并且已经将验证码存入到了缓存中
        $captcha = $captchaBuilder->create('flat', true);
        $key = 'captcha_'. Str::random(15);
        $expiredAt = now()->addMinutes(2);
        Cache::put($key, ['phone' => $request->phone, 'captcha' => $captcha['key']], $expiredAt);
        $result = [
            'cache_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_img' => $captcha['img']
        ];
        return response()->json($result)->setStatusCode(201);
    }
}
