<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorageController extends Controller
{
    /**
     * Supported storage providers. All non-local providers share the
     * S3-compatible API, so they use the same credential fields.
     *
     * @var array<string, string>
     */
    public const PROVIDERS = [
        'local' => 'Local',
        's3' => 'Amazon S3 Cloud Storage',
        'spaces' => 'DigitalOcean Spaces',
        'r2' => 'Cloudflare R2 Object Storage',
    ];

    /**
     * Show the storage provider settings page.
     */
    public function index(): View
    {
        return view('admin.storage.index', [
            'providers' => self::PROVIDERS,
            'provider' => Setting::get('storage_provider', 'local'),
            's3' => [
                'key' => Setting::get('storage_s3_key', ''),
                'secret' => Setting::get('storage_s3_secret', ''),
                'region' => Setting::get('storage_s3_region', ''),
                'bucket' => Setting::get('storage_s3_bucket', ''),
                'endpoint' => Setting::get('storage_s3_endpoint', ''),
                'url' => Setting::get('storage_s3_url', ''),
                'path_style' => Setting::get('storage_s3_path_style', '0') === '1',
            ],
            's3Available' => class_exists(\Aws\S3\S3Client::class),
        ]);
    }

    /**
     * Persist the storage provider settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'storage_provider' => ['required', 'in:'.implode(',', array_keys(self::PROVIDERS))],
            'storage_s3_key' => ['nullable', 'string', 'max:255'],
            'storage_s3_secret' => ['nullable', 'string', 'max:255'],
            'storage_s3_region' => ['nullable', 'string', 'max:100'],
            'storage_s3_bucket' => ['nullable', 'string', 'max:255'],
            'storage_s3_endpoint' => ['nullable', 'string', 'max:255'],
            'storage_s3_url' => ['nullable', 'string', 'max:255'],
        ]);

        // When switching to a cloud provider, require the core credentials.
        if ($data['storage_provider'] !== 'local') {
            $request->validate([
                'storage_s3_key' => ['required', 'string'],
                'storage_s3_secret' => ['required', 'string'],
                'storage_s3_bucket' => ['required', 'string'],
            ], [], [
                'storage_s3_key' => 'access key',
                'storage_s3_secret' => 'secret key',
                'storage_s3_bucket' => 'bucket name',
            ]);

            if (! class_exists(\Aws\S3\S3Client::class)) {
                return back()->withInput()->with('error', 'The AWS S3 SDK is not installed. Run "composer require league/flysystem-aws-s3-v3" on your server, then try again.');
            }
        }

        Setting::put('storage_provider', $data['storage_provider'], 'storage');
        foreach (['storage_s3_key', 'storage_s3_secret', 'storage_s3_region', 'storage_s3_bucket', 'storage_s3_endpoint', 'storage_s3_url'] as $key) {
            Setting::put($key, $data[$key] ?? '', 'storage');
        }
        Setting::put('storage_s3_path_style', $request->boolean('storage_s3_path_style') ? '1' : '0', 'storage');

        return back()->with('success', 'Storage settings saved. Remember to move existing files to the new provider so previous uploads keep working.');
    }

    /**
     * Test the currently saved storage connection with a write → read → delete
     * round-trip on a temporary file.
     */
    public function test(): RedirectResponse
    {
        $provider = Setting::get('storage_provider', 'local');
        $name = '__akzone_conn_test_'.uniqid().'.txt';
        $payload = 'akzone-connection-test';

        try {
            if ($provider === 'local') {
                $disk = \Illuminate\Support\Facades\Storage::disk('public');
            } else {
                if (! class_exists(\Aws\S3\S3Client::class)) {
                    return back()->with('error', 'The AWS S3 SDK is not installed. Run "composer require league/flysystem-aws-s3-v3" first.');
                }

                $key = Setting::get('storage_s3_key');
                $secret = Setting::get('storage_s3_secret');
                $bucket = Setting::get('storage_s3_bucket');

                if (! $key || ! $secret || ! $bucket) {
                    return back()->with('error', 'Please save your access key, secret and bucket before testing.');
                }

                $disk = \Illuminate\Support\Facades\Storage::build([
                    'driver' => 's3',
                    'key' => $key,
                    'secret' => $secret,
                    'region' => Setting::get('storage_s3_region') ?: 'us-east-1',
                    'bucket' => $bucket,
                    'endpoint' => Setting::get('storage_s3_endpoint') ?: null,
                    'use_path_style_endpoint' => Setting::get('storage_s3_path_style') === '1',
                    'throw' => true,
                ]);
            }

            $disk->put($name, $payload);
            $readBack = $disk->get($name);
            $disk->delete($name);

            if ($readBack === $payload) {
                return back()->with('success', '✅ Connection successful! Write, read and delete all worked on the '.self::PROVIDERS[$provider].' storage.');
            }

            return back()->with('error', 'Wrote a test file but the read-back did not match. Check bucket permissions.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Connection failed: '.$e->getMessage());
        }
    }
}
