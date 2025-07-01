<?php

namespace App\Providers;

use App\Models\Project;
use App\Policies\ProjectPolicy;
use App\Models\Site;
use App\Policies\SitePolicy;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\SubSystem;
use App\Policies\SubSystemPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Site::class => SitePolicy::class,
        User::class => UserPolicy::class,
        SubSystem::class => SubSystemPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}