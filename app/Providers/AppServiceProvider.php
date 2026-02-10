<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // VERCEL FIX: Force Runtime Cloudinary Config if Env Var exists
        if (isset($_ENV['CLOUDINARY_URL'])) {
            $cloudinaryUrl = $_ENV['CLOUDINARY_URL'];
            $components = parse_url($cloudinaryUrl);

            if ($components) {
                config([
                    'filesystems.disks.cloudinary' => [
                        'driver' => 'cloudinary',
                        'cloud_name' => $components['host'],
                        'api_key' => $components['user'],
                        'api_secret' => $components['pass'],
                        'secure' => true,
                        'url' => 'https://res.cloudinary.com/' . $components['host'], // Ensure URL generation works
                    ],
                    'filesystems.default' => 'cloudinary', // Force as default
                ]);
            }
        }
    }
}
