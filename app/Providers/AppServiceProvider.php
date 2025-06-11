<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    
     
    public function register()
    {
        //check that app is local
        // if ($this->app->isLocal()) {
        //     $this->app->register('Barryvdh\Debugbar\ServiceProvider');
        // } else {
        //     //else register your services you require for production
        //     $this->app['request']->server->set('HTTPS', true);
        // }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       
        $this->loadViewsFrom(resource_path('admin/views'), 'admin');
        $this->loadViewsFrom(resource_path('website/views'), 'website');
    }
}
