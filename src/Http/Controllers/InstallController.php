<?php

namespace Shankar\AppInstallVerifier\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class InstallController extends Controller
{
    public function index()
    {
        return view('appinstallerverifier::appinstall');
    }

    public function dbsetup(Request $request)
    {
        $request->validate([
            'db_name' => 'required',
            'db_password' => 'nullable',
            'db_username' => 'required',
            'db_host' => 'required'
        ]);
        $this->setEnvironmentValue([
            'DB_HOST' => $request->db_host,
            'DB_DATABASE' => $request->db_name,
            'DB_USERNAME' => $request->db_username,
            'DB_PASSWORD' => $request->db_password,
        ]);

        // Clear configuration cache
        Artisan::call('config:clear');

        // Test database connection
        try {
            DB::connection()->getPdo();
            return response()->json(['success' => true, 'message' => 'Database connection successful.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Could not connect to the database. Error: ' . $e->getMessage()]);
        }

        return response()->json(['success' => true, 'message' => 'Data processed successfully']);
    }

    public function emailsetup(Request $request)
    {
        $validatedData = $request->validate([
            'email_host' => 'required|string',
            'email_port' => 'required|numeric',
            'email_username' => 'required|string',
            'email_password' => 'required|string',
            'email_encryption' => 'nullable|string',
            'email' => 'required|email',
            'sender_name' => 'required'
        ]);

        $this->setEnvironmentValue([
            'MAIL_HOST' => $validatedData['email_host'],
            'MAIL_PORT' => $validatedData['email_port'],
            'MAIL_USERNAME' => $validatedData['email_username'],
            'MAIL_PASSWORD' => $validatedData['email_password'],
            'MAIL_ENCRYPTION' => $validatedData['email_encryption'] ?? '',
            "MAIL_FROM_ADDRESS" => $validatedData['email'],
            "MAIL_FROM_NAME" => $validatedData['sender_name']
        ]);

        // Clear configuration cache
        Artisan::call('config:clear');

        // Test email sending
        try {
            Mail::raw('Testing email configuration from your laravel app.', function ($message) use ($validatedData) {
                $message->subject('Testing email configuration from your laravel app.');
                $message->to('shankargoraise@gmail.com');
            });
            return response()->json(['success' => true, 'message' => 'Email configuration is working.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Could not send test email. Error: ' . $e->getMessage()]);
        }
    }


    public function createlicence(Request $request)
    {
        $validated = $request->validate([
            'verify_url' => 'required|url',
            'licence_code' => 'required',
        ]);

        // Define the path to the JSON file within the package
        $filePath = base_path('vendor/shankar/app-installer-verifier/src/licence_details.json');

        // Convert data array to JSON
        $jsonData = json_encode($validated, JSON_PRETTY_PRINT);

        // Write JSON data to the file
        File::put($filePath, $jsonData);
        $host = $request->getHost();
        $response =  Http::post($request->verify_url, [
            'app_name' => env('APP_NAME'),
            'url' => $host,
            'license_code' => $request->licence_code
        ]);
        if ($response->successful()) {
            $result = $response->json();
            if ($result['status']) {
                Cache::remember('app_installed_on_domain', 86400, function () {
                    return true;
                });
                return redirect($request->redirect_url ?? '/');
            }
        }
        return response()->json(['success' => false, 'message' => 'License details are invalid , please contact to developer']);
    }


    protected function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        foreach ($values as $envKey => $envValue) {
            $str .= "\n"; // Ensure newline at end of file
            $keyPosition = strpos($str, "{$envKey}=");
            $endOfLinePosition = strpos($str, "\n", $keyPosition);
            $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
            if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                // key does not exist, so just append it
                $str .= "{$envKey}={$envValue}\n";
            } else {
                // Replace existing line
                $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
            }
        }

        $str = rtrim($str);
        file_put_contents($envFile, $str);
    }
}
