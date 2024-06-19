<?php

namespace Shankar\AppInstallVerifier\Console;

use Composer\Script\Event;
use Illuminate\Filesystem\Filesystem;

class UninstallHandler
{
    public static function handle(Event $event)
    {
        require __DIR__ . '/../../../../bootstrap/autoload.php';
        $app = require __DIR__ . '/../../../../bootstrap/app.php';
        $kernel = $app->make(Kernel::class);
        $kernel->bootstrap();

        $operation = $event->getOperation();
        $package = method_exists($operation, 'getPackage') ? $operation->getPackage() : $operation->getTargetPackage();
        $packageName = $package->getName();

        if ($packageName === 'vendor/package-name') {
            echo "Package $packageName is about to be uninstalled.\n";

            $fileSystem = new Filesystem();

            // Define the paths to be deleted
            $paths = [
                base_path('database/migrations'),
                base_path('app/Http/Controllers/YourPackageControllers'),
                base_path('resources/views/your-package-views')
            ];

            foreach ($paths as $path) {
                if ($fileSystem->isDirectory($path)) {
                    $fileSystem->deleteDirectory($path);
                    echo "Deleted directory at $path.\n";
                }
            }
        }
    }
}
