<?php

namespace Shankar\AppInstallVerifier;

use Illuminate\Support\ServiceProvider;

use  Shankar\AppInstallVerifier\Http\Middleware\InstallCheck;

class AppInstallVerifierServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'appinstallerverifier');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $router = $this->app['router'];
        $router->aliasMiddleware('install.check', InstallCheck::class);
        $router->pushMiddlewareToGroup('web', InstallCheck::class);
    }

    public function register()
    {
        // Binding services into the container
    }
}
