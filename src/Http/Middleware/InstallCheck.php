<?php

namespace  Shankar\AppInstallVerifier\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InstallCheck
{
    public function handle(Request $request, Closure $next)
    {
        dd("Middleware Working");
    }
}
