<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\News::class => \App\Policies\NewsPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Administrator::class => \App\Policies\AdminPolicy::class,
        \App\Models\Comment::class => \App\Policies\CommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Auth::macro('anyCheck', function () {
            return Auth::guard('admin')->check() || Auth::check();
        });

        Auth::macro('currentUser', function () {
            if (Auth::guard('admin')->check()) {
                return Auth::guard('admin')->user();
            } elseif (Auth::check()) {
                return Auth::user();
            }
            return null;
        });
    }
}
