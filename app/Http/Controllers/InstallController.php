<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class InstallController extends Controller
{
    /**
     * PHP extensions the application strictly requires to run.
     *
     * @var array<int, string>
     */
    protected array $requiredExtensions = [
        'bcmath', 'ctype', 'curl', 'fileinfo', 'json', 'mbstring',
        'openssl', 'pdo', 'pdo_mysql', 'tokenizer', 'xml',
    ];

    /**
     * Extensions that are nice to have (e.g. used by Composer) but are not
     * needed for the application to run, so they never block installation.
     *
     * @var array<int, string>
     */
    protected array $recommendedExtensions = [
        'zip',
    ];

    /*
    |--------------------------------------------------------------------------
    | Step 1 — Requirements
    |--------------------------------------------------------------------------
    */
    public function requirements(): View
    {
        $phpOk = version_compare(PHP_VERSION, '8.2.0', '>=');

        $extensions = [];
        foreach ($this->requiredExtensions as $ext) {
            $extensions[$ext] = extension_loaded($ext);
        }

        $recommended = [];
        foreach ($this->recommendedExtensions as $ext) {
            $recommended[$ext] = extension_loaded($ext);
        }

        // Only the PHP version and required extensions can block installation.
        $passed = $phpOk && ! in_array(false, $extensions, true);

        return view('install.requirements', [
            'step' => 1,
            'phpVersion' => PHP_VERSION,
            'phpOk' => $phpOk,
            'extensions' => $extensions,
            'recommended' => $recommended,
            'passed' => $passed,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Step 2 — Permissions
    |--------------------------------------------------------------------------
    */
    public function permissions(): View
    {
        $paths = [
            '.env' => $this->isWritable(base_path('.env')),
            'storage/framework' => $this->isWritable(storage_path('framework')),
            'storage/logs' => $this->isWritable(storage_path('logs')),
            'bootstrap/cache' => $this->isWritable(base_path('bootstrap/cache')),
        ];

        $passed = ! in_array(false, $paths, true);

        return view('install.permissions', [
            'step' => 2,
            'paths' => $paths,
            'passed' => $passed,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Step 3 — Database
    |--------------------------------------------------------------------------
    */
    public function database(): View
    {
        return view('install.database', [
            'step' => 3,
            'config' => [
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', ''),
                'username' => env('DB_USERNAME', ''),
            ],
        ]);
    }

    public function saveDatabase(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'host' => ['required', 'string'],
            'port' => ['required', 'numeric'],
            'database' => ['required', 'string'],
            'username' => ['required', 'string'],
            'password' => ['nullable', 'string'],
        ]);

        // Try the connection live before persisting anything.
        try {
            $this->applyDatabaseConfig($data);
            DB::connection('mysql')->getPdo();
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Could not connect to the database: '.$e->getMessage());
        }

        // Persist the working credentials to the .env file.
        $this->writeEnv([
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['host'],
            'DB_PORT' => $data['port'],
            'DB_DATABASE' => $data['database'],
            'DB_USERNAME' => $data['username'],
            'DB_PASSWORD' => $data['password'] ?? '',
        ]);

        return redirect()->route('install.import');
    }

    /*
    |--------------------------------------------------------------------------
    | Step 4 — Import (run migrations + optional demo data)
    |--------------------------------------------------------------------------
    */
    public function import(): View
    {
        return view('install.import', ['step' => 4]);
    }

    public function runImport(Request $request): RedirectResponse
    {
        $demo = $request->boolean('demo');

        try {
            $this->applyDatabaseConfig([
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
            ]);

            // Confirm the connection is still good.
            DB::connection('mysql')->getPdo();

            // Build the schema.
            Artisan::call('migrate', ['--force' => true]);

            // Always seed core settings + currencies.
            Artisan::call('db:seed', ['--class' => 'CoreSeeder', '--force' => true]);

            // Optionally load sample categories, products, reviews & demo accounts.
            if ($demo) {
                Artisan::call('db:seed', [
                    '--class' => 'DemoSeeder',
                    '--force' => true,
                ]);
            }
        } catch (Throwable $e) {
            return redirect()->route('install.database')
                ->with('error', 'Database setup failed: '.$e->getMessage());
        }

        // Remember which path was chosen so the finish screen shows the right info.
        session(['install_demo' => $demo]);

        if ($demo) {
            // The demo seeder already created an admin account
            // (admin@akzone.com / password) and demo store settings, so we skip
            // the manual account step and go straight to finish. Persist a sane
            // APP_URL so storefront links resolve correctly.
            $this->writeEnv([
                'APP_URL' => rtrim(env('APP_URL', $this->guessUrl()), '/'),
            ]);

            return redirect()->route('install.finish');
        }

        // Fresh business install — collect the brand name + real admin account.
        return redirect()->route('install.account');
    }

    /*
    |--------------------------------------------------------------------------
    | Step 5 — Site & Admin account
    |--------------------------------------------------------------------------
    */
    public function account(): View
    {
        return view('install.account', [
            'step' => 5,
            'siteUrl' => env('APP_URL', $this->guessUrl()),
        ]);
    }

    public function saveAccount(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_url' => ['required', 'url', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $this->applyDatabaseConfig([
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
            ]);

            User::updateOrCreate(
                ['email' => $data['admin_email']],
                [
                    'name' => $data['admin_name'],
                    'password' => Hash::make($data['admin_password']),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]
            );
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Could not create the admin account: '.$e->getMessage());
        }

        $this->writeEnv([
            'APP_NAME' => $data['site_name'],
            'APP_URL' => rtrim($data['site_url'], '/'),
        ]);

        return redirect()->route('install.finish');
    }

    /*
    |--------------------------------------------------------------------------
    | Step 6 — Finish (lock the installer)
    |--------------------------------------------------------------------------
    */
    public function finish(): View
    {
        // Best-effort: publish the public storage symlink. Ignored if the host
        // disallows symlinks (common on shared hosting) — see README fallback.
        try {
            Artisan::call('storage:link');
        } catch (Throwable $e) {
            // Silently continue; downloads still work, only public assets need it.
        }

        // Drop the lock file so the installer can never run again.
        @file_put_contents(storage_path('installed'), 'Installed at '.now()->toDateTimeString().PHP_EOL);

        $demo = (bool) session('install_demo', false);
        session()->forget('install_demo');

        return view('install.finish', [
            'step' => 6,
            'appUrl' => env('APP_URL', $this->guessUrl()),
            'demo' => $demo,
            'demoEmail' => 'admin@akzone.com',
            'demoPassword' => 'password',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Apply database credentials to the runtime config and reset the connection.
     *
     * @param  array<string, mixed>  $data
     */
    protected function applyDatabaseConfig(array $data): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $data['host'],
            'database.connections.mysql.port' => $data['port'],
            'database.connections.mysql.database' => $data['database'],
            'database.connections.mysql.username' => $data['username'],
            'database.connections.mysql.password' => $data['password'] ?? '',
        ]);

        DB::purge('mysql');
    }

    /**
     * Update (or append) the given key/value pairs in the .env file.
     *
     * @param  array<string, mixed>  $values
     */
    protected function writeEnv(array $values): void
    {
        $path = base_path('.env');

        if (! file_exists($path)) {
            copy(base_path('.env.example'), $path);
        }

        $content = file_get_contents($path);

        foreach ($values as $key => $value) {
            $formatted = $this->formatEnvValue((string) $value);

            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", $key.'='.$formatted, $content);
            } else {
                $content .= PHP_EOL.$key.'='.$formatted;
            }
        }

        file_put_contents($path, $content);
    }

    /**
     * Quote env values that contain spaces or special characters.
     */
    protected function formatEnvValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('/\s|#|"|\'/', $value)) {
            return '"'.addcslashes($value, '"\\').'"';
        }

        return $value;
    }

    /**
     * Determine if a path (or its parent directory, when missing) is writable.
     */
    protected function isWritable(string $path): bool
    {
        if (file_exists($path)) {
            return is_writable($path);
        }

        return is_writable(dirname($path));
    }

    /**
     * Best-effort guess of the application URL from the current request.
     */
    protected function guessUrl(): string
    {
        return request()->getSchemeAndHttpHost();
    }
}
