<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Request;
use Illuminate\Auth\AuthenticationException;
use App\Models\User;
use App\Models\Image;
use App\Http\Resources\UserResource;


class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);
        if(!$verifyData)
        {
            abort(403, '验证码已失效');
        }
        if(!hash_equals($verifyData['code'], $request->verification_code))
        {
//            401
            throw new AuthenticationException('验证码错误');
        }
        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => $request->password
        ]);

        \Cache::forget($request->verification_key);

        return new UserResource($user);
    }

    public function show(User $user, Request $request)
    {
        return new UserResource($user);
    }

    public function me(Request $request)
    {
        return (new UserResource($request->user()))->showSensitiveFields();
    }

    public function update(UserRequest $request)
    {
        $user = $request->user();

        $attributes = $request->only(['name', 'email', 'introduction', 'registration_id']);

        if($request->avatar_image_id)
        {
            $image = Image::find($request->avatar_image_id);
            $attributes['avatar'] = $image->path;
        }

        $user->update($attributes);

        return (new UserResource($user))->showSensitiveFields();
    }

    public function activedUsersIndex(User $user)
    {
        UserResource::wrap('data');
        return UserResource::collection($user->getActiveUsers());
    }
}
