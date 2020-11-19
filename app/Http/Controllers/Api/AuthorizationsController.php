<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Models\User;
use Overtrue\Socialite\AccessToken;
use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\Api\SocialAuthorizationRequest;

class AuthorizationsController extends Controller
{
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        $driver = \Socialite::driver($type);

        try {
            if ($code = $request->code) {
                $accessToken = $driver->getAccessToken($code);
            } else {
                $tokenData['access_token'] = $request->access_token;

                // 微信需要增加 openid
                if ($type == 'wechat') {
                    $tokenData['openid'] = $request->openid;
                }
                $accessToken = new AccessToken($tokenData);
            }

            $oauthUser = $driver->user($accessToken);
            if(!$oauthUser->getid()){
                throw new \Error(__('auth.param_error'));
            }
        } catch (\Exception $e) {
            throw new \Error(__('auth.param_error'));
        }

        switch ($type) {
            case 'wechat':
                $unionid = $oauthUser->getOriginal()['unionid'] ?? null;
                $openid = $oauthUser->getId() ?? '';
                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::where('weixin_openid', $openid)->get();
                }

                // 没有用户，默认创建一个用户
                if (!$user) {
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid,
                    ]);
                }

                break;
        }
        $token = \Auth::guard('api')->login($user);

        return $this->responseWithToken($token)->setStatusCode(201);
    }
    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
        $credentials['email'] = $username: $credentials['phone'] = $username;

        $credentials['password'] = $request->password;

        if(!$token = \Auth::guard('api')->attempt($credentials))
        {
            throw new AuthenticationException(__('auth.failed'));
        }

        return $this->responseWithToken($token)->setStatusCode(201);
    }

    protected function responseWithToken($token)
    {
        return response()->json([
            [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
            ]
        ]);
    }

    public function update()
    {
        $token = auth('api')->refresh();
        return $this->responseWithToken($token);
    }

    public function destroy()
    {
        auth('api')->logout();
        return response(null, 204);
    }
}
