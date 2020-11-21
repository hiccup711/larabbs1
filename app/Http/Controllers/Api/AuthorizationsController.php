<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Models\User;
use Overtrue\Socialite\AccessToken;
use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\AuthorizationServer;
use Laminas\Diactoros\Response as Psr7Response;
use League\OAuth2\Server\Exception\OAuthServerException;
use App\Traits\PassportToken;

class AuthorizationsController extends Controller
{
    use PassportToken;
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
        } catch (\Exception $e) {
            throw new \Error(__('auth.param_error'));
        }

        switch ($type) {
            case 'wechat':
                $unionid = $oauthUser->getOriginal()['unionid'] ?? '';
                $openid = $oauthUser->getId() ?? '';
                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::where('weixin_openid', $openid)->first();
                }
                // 没有用户，默认创建一个用户
                if ($user == []) {
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $openid,
                        'weixin_unionid' => $unionid,
                    ]); 
                }
                break;
        }
        $result = $this->getBearerTokenByUser($user, '1', false);

        return response()->json($result)->setStatusCode(201);

    }
    public function store(AuthorizationRequest $originalRequest, AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        {
            try {
                return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response)->withStatus(201);
            } catch(OAuthServerException $e) {
                throw new AuthenticationException($e->getMessage());
            }
        }
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

    public function update(AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response);
        } catch(OAuthServerException $e) {
            throw new AuthenticationException($e->getmessage());
        }
    }

    public function destroy()
    {
        if (auth('api')->check()) {
            auth('api')->logout();
            return response(null, 204);
        } else {
            throw new AuthenticationException('The token is invalid.');
        }
    }
}
