<?php

namespace Shankar\AppInstallVerifier\Console;

use Composer\Script\Event;

class UninstallHandler
{
    /**
     * Handle the uninstallation process.
     *
     * @param \Composer\Script\Event $event
     * @return void
     */
    public static function handle(Event $event)
    {
        file_put_contents('debug.log', "Uninstall event triggered.\n", FILE_APPEND);

        $operation = $event->getOperation();
        $package = $operation->getPackage();
        $packageName = $package->getName();

        if ($packageName === 'vendor/package-name') {
            file_put_contents('debug.log', "Package $packageName is about to be uninstalled.\n", FILE_APPEND);
            echo "Package $packageName is about to be uninstalled.\n";
        }
    }
}
