<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\InventoryMaster;
use App\Policies\InventoryPolicy;

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
    InventoryMaster::class => InventoryPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {


    }
}

