<?php

namespace App\Modules\Catalog\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;

class CatalogMigrationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Infrastructure/Persistence/Migrations');
    }
}
