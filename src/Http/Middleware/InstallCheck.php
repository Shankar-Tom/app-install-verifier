<?php

namespace Shankar\AppInstallVerifier\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class InstallCheck
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        if ($host == 'localhost' || $host == '127.0.0.1') {
            return  $next($request);
        }
        $verifyUrl = env('VERIFY_URL', 'Not Set');
        if ($verifyUrl == 'Not Set') {
            abort(400, 'Please set verify url in env file to run the application');
        }
        if (Cache::get('app_installed_on_domain') == true) {
            return  $next($request);
        } else {
            $response =  Http::post($verifyUrl, [
                'app_name' => env('APP_NAME'),
                'url' => $host,
            ]);
            if ($response->successful()) {
                $result = $response->json();
                if ($result['status'] && $result['app_key'] == env('APP_KEY')) {
                    Cache::remember('app_installed_on_domain', 86400, function () {
                        return true;
                    });
                    return  $next($request);
                }
            }
        }
        abort(401);
    }
}
