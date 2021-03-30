<?php

namespace App\Providers;

use App\Device;
use App\Order;
use App\Policies\DevicePolicy;
use App\Policies\OrderPolicy;
use App\Policies\UserPolicy;
use App\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Device::class => DevicePolicy::class,
        Order::class => OrderPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin', function ($user) {
            return $user->isAdmin() ? Response::allow() : Response::deny("Du bist kein Admin.");
        });

        Gate::define('teacher', function ($user) {
            return $user->isTeacher() ? Response::allow() : Response::deny("Du bist kein Lehrer.");
        });

        Gate::define('student', function ($user) {
            return $user->isStudent() ? Response::allow() : Response::deny("Du bist kein Schüler.");
        });
    }
}
