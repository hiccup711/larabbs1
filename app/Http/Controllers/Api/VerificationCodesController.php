<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Overtrue\EasySms\EasySms;
use Illuminate\Support\Str;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;


class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $captchaData = Cache::get($request->cache_key);

        if(!$captchaData)
        {
            abort(403, '验证码已失效');
        }
        if(!captcha_api_check($request->captcha_code, $captchaData['captcha']))
        {
            Cache::forget($request->cache_key);
            throw new AuthenticationException('验证码错误');
        }
        $phone = $request->phone;

        if(!app()->environment('production'))
        {
            $code = '1234';
        } else {
    //        生成4位随机数，左侧补0
            $code = str_pad(random_int(1,9999), 4, 0, STR_PAD_LEFT);

            try{
                $result = $easySms->send($phone,[
                    'template' => config('easysms.gateways.aliyun.templates.register'),
                    'data' => [
                        'code' => $code
                    ],
                ]);
            } catch (NoGatewayAvailableException $exception){
                $message = $exception->get('aliyun')->getMessage();
                abort(500, $message?: '短信发送异常');
            }
        }

        $key = 'verificationCode_'. Str::random(15);
        $expiredAt = now()->addMinutes(5);
        Cache::put($key, ['phone' => $phone, 'code' => $code] ,$expiredAt);
        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
