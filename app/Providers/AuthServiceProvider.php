<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
		 \App\Models\Reply::class => \App\Policies\ReplyPolicy::class,
		 \App\Models\Topic::class => \App\Policies\TopicPolicy::class,
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::guessPolicyNamesUsing(function ($modelClass){
            return "App\Policies\\".class_basename($modelClass).'Policy';
        });

        Horizon::auth(function($request){
//           是否是站长
            return \Auth::user()->hasRole('Founder');
        });

//      Passport路由
        Passport::routes();
//      access_token 过期时间
        Passport::tokensExpireIn(now()->addDays(15));
//      refreshToken 过期时间
        Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}
