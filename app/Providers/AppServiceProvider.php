<?php

namespace App\Providers;

use App\Services\Nyt\NytClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NytClient::class, function ($app) {
            $apiKey = config('nyt.api_key');

            if (!$apiKey) {
                throw new \RuntimeException('NYT_API_KEY is not set in environment.');
            }

            return new NytClient(
                $apiKey,
                config('nyt.base_url')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
