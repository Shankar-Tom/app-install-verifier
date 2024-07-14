<?php

namespace Shankar\AppInstallVerifier\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class InstallCheck
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        if ($host == 'localhost' || $host == '127.0.0.1') {
            return  $next($request);
        }

        if (Cache::get('app_installed_on_domain') == true) {
            return  $next($request);
        } else {
            $jsonFilePath = base_path('vendor/shankar/app-installer-verifier/src/licence_details.json');
            if (!File::exists($jsonFilePath)) {
                return redirect()->route('appinstaller.index');
            }
            $jsonData = json_decode(file_get_contents($jsonFilePath), true);

            $response =  Http::post($jsonData['verify_url'], [
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
