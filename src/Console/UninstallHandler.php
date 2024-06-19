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
        $packageName = $event->getOperation()->getPackage()->getName();

        // Checking the package name
        if ($packageName === 'shankar/app-installer-verifier') {
            // Uninstalltion logic
            echo "Package $packageName is being uninstalled.\n";
        }
    }
}
