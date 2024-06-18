<?php

namespace Shankar\AppInstallVerifier;

use Illuminate\Support\ServiceProvider;

class AppInstallVerifierServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    public function register()
    {
        // Binding services into the container
    }
}
