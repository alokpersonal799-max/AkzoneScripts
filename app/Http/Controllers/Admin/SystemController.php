<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SystemController extends Controller
{
    /**
     * Display the System Information page with health monitoring.
     */
    public function index(Request $request): View
    {
        $health = $this->getHealthChecks();
        $errorLog = $this->getErrorLogInfo();

        return view('admin.system.index', [
            'app' => [
                'Name' => setting('site_name', config('app.name')),
                'Version' => config('marketplace.version', '1.0.0'),
                'Laravel Version' => app()->version(),
                'Timezone' => config('app.timezone', 'UTC'),
            ],
            'server' => [
                'Software' => $request->server('SERVER_SOFTWARE') ?: 'Unknown',
                'PHP Version' => PHP_VERSION,
                'IP Address' => $request->server('SERVER_ADDR') ?: $request->ip(),
                'Protocol' => $request->server('SERVER_PROTOCOL') ?: ($request->isSecure() ? 'HTTPS' : 'HTTP'),
                'HTTP Host' => $request->getHttpHost(),
                'Port' => (string) $request->getPort(),
            ],
            'health' => $health,
            'errorLog' => $errorLog,
        ]);
    }

    /**
     * Run health checks on critical system components.
     *
     * @return array<string, array{status: string, label: string, detail: string}>
     */
    protected function getHealthChecks(): array
    {
        $checks = [];

        // PHP Version check
        $phpVersion = PHP_VERSION;
        $phpOk = version_compare($phpVersion, '8.2.0', '>=');
        $checks['php'] = [
            'label' => 'PHP Version',
            'status' => $phpOk ? 'ok' : 'warning',
            'detail' => $phpVersion.($phpOk ? '' : ' (8.2+ recommended)'),
        ];

        // Database connection
        try {
            DB::connection()->getPdo();
            $dbName = DB::connection()->getDatabaseName();
            $checks['database'] = [
                'label' => 'Database Connection',
                'status' => 'ok',
                'detail' => 'Connected to: '.$dbName,
            ];
        } catch (\Throwable $e) {
            $checks['database'] = [
                'label' => 'Database Connection',
                'status' => 'error',
                'detail' => 'Failed: '.class_basename($e).' - '.\Illuminate\Support\Str::limit($e->getMessage(), 80),
            ];
        }

        // Storage writability
        $storagePath = storage_path('app');
        $storageWritable = is_writable($storagePath);
        $checks['storage'] = [
            'label' => 'Storage Writable',
            'status' => $storageWritable ? 'ok' : 'error',
            'detail' => $storageWritable ? 'storage/app is writable' : 'storage/app is NOT writable',
        ];

        // Cache driver check
        try {
            Cache::put('_health_check', true, 10);
            $cacheWorks = Cache::get('_health_check') === true;
            Cache::forget('_health_check');
            $driver = config('cache.default', 'file');
            $checks['cache'] = [
                'label' => 'Cache System',
                'status' => $cacheWorks ? 'ok' : 'warning',
                'detail' => $cacheWorks ? 'Working (driver: '.$driver.')' : 'Cache read/write failed (driver: '.$driver.')',
            ];
        } catch (\Throwable $e) {
            $checks['cache'] = [
                'label' => 'Cache System',
                'status' => 'error',
                'detail' => 'Error: '.\Illuminate\Support\Str::limit($e->getMessage(), 60),
            ];
        }

        // SMTP / Mail config
        $mailDriver = config('mail.default', 'log');
        $mailHost = config('mail.mailers.smtp.host', '');
        if ($mailDriver === 'smtp' && $mailHost) {
            $checks['mail'] = [
                'label' => 'Mail (SMTP)',
                'status' => 'ok',
                'detail' => 'Configured: '.$mailHost.':'.config('mail.mailers.smtp.port', 587),
            ];
        } elseif ($mailDriver === 'log') {
            $checks['mail'] = [
                'label' => 'Mail Driver',
                'status' => 'warning',
                'detail' => 'Using log driver (emails not sent)',
            ];
        } else {
            $checks['mail'] = [
                'label' => 'Mail Driver',
                'status' => 'ok',
                'detail' => 'Driver: '.$mailDriver,
            ];
        }

        // Error log size
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            $logSize = File::size($logPath);
            $logSizeMb = round($logSize / 1024 / 1024, 2);
            $logStatus = $logSizeMb > 50 ? 'error' : ($logSizeMb > 10 ? 'warning' : 'ok');
            $checks['error_log'] = [
                'label' => 'Error Log Size',
                'status' => $logStatus,
                'detail' => $logSizeMb.' MB'.($logStatus !== 'ok' ? ' - consider clearing' : ''),
            ];
        } else {
            $checks['error_log'] = [
                'label' => 'Error Log',
                'status' => 'ok',
                'detail' => 'No log file (clean)',
            ];
        }

        // Disk space
        $diskFree = @disk_free_space(base_path());
        $diskTotal = @disk_total_space(base_path());
        if ($diskFree !== false && $diskTotal !== false && $diskTotal > 0) {
            $usedPercent = round((($diskTotal - $diskFree) / $diskTotal) * 100, 1);
            $freeGb = round($diskFree / 1024 / 1024 / 1024, 1);
            $diskStatus = $usedPercent > 90 ? 'error' : ($usedPercent > 75 ? 'warning' : 'ok');
            $checks['disk'] = [
                'label' => 'Disk Space',
                'status' => $diskStatus,
                'detail' => $freeGb.' GB free ('.$usedPercent.'% used)',
            ];
        }

        // Session driver
        $sessionDriver = config('session.driver', 'file');
        $checks['session'] = [
            'label' => 'Session Driver',
            'status' => 'ok',
            'detail' => ucfirst($sessionDriver),
        ];

        // App debug mode
        $debugMode = config('app.debug', false);
        $checks['debug'] = [
            'label' => 'Debug Mode',
            'status' => $debugMode ? 'warning' : 'ok',
            'detail' => $debugMode ? 'ENABLED (disable in production)' : 'Disabled',
        ];

        return $checks;
    }

    /**
     * Get error log information for display.
     *
     * @return array{exists: bool, size: string, last_entries: array<int, string>}
     */
    protected function getErrorLogInfo(): array
    {
        $logPath = storage_path('logs/laravel.log');
        $info = ['exists' => false, 'size' => '0 KB', 'last_entries' => []];

        if (! File::exists($logPath)) {
            return $info;
        }

        $info['exists'] = true;
        $size = File::size($logPath);
        if ($size >= 1048576) {
            $info['size'] = round($size / 1048576, 2).' MB';
        } else {
            $info['size'] = round($size / 1024, 1).' KB';
        }

        // Read last 50 lines of the log
        try {
            $content = File::get($logPath);
            $lines = explode("\n", trim($content));
            $info['last_entries'] = array_slice($lines, -50);
        } catch (\Throwable $e) {
            $info['last_entries'] = ['Unable to read log file: '.$e->getMessage()];
        }

        return $info;
    }

    /**
     * Flush all framework caches, compiled views and the log file.
     */
    public function clearCache(): RedirectResponse
    {
        $cleared = [];

        foreach (['view:clear', 'cache:clear', 'route:clear', 'config:clear'] as $command) {
            try {
                Artisan::call($command);
                $cleared[] = $command;
            } catch (\Throwable $e) {
                // Ignore individual command failures so one bad cache doesn't block the rest.
            }
        }

        // Truncate the default Laravel log file, if present.
        try {
            $log = storage_path('logs/laravel.log');
            if (File::exists($log)) {
                File::put($log, '');
                $cleared[] = 'error logs';
            }
        } catch (\Throwable $e) {
            // Non-fatal.
        }

        return back()->with('success', 'Caches cleared: '.implode(', ', $cleared).'.');
    }

    /**
     * Clear only the error log file.
     */
    public function clearErrorLog(): RedirectResponse
    {
        try {
            $log = storage_path('logs/laravel.log');
            if (File::exists($log)) {
                File::put($log, '');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not clear error log: '.$e->getMessage());
        }

        return back()->with('success', 'Error log cleared successfully.');
    }
}
