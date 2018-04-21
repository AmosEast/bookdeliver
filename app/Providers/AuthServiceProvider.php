<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //权限控制模块
        $permissions = \App\Models\Permission::with('roles') ->get();
        foreach ($permissions as $permission) {
            gate::define(($permission ->controller).'@'.($permission ->function), function ($user) use($permission) {
                return $user ->isSuperAdmin() || $user ->hasPermission($permission);
            });
        }
    }
}
