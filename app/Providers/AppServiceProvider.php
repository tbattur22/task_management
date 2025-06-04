<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');

            // Trust all proxies (Render uses reverse proxy/load balancer)
            Request::setTrustedProxies(
                ['0.0.0.0/0'], // Trust all proxy IPs
                SymfonyRequest::HEADER_X_FORWARDED_ALL
            );
        }
    }
}
