@extends('layouts.admin')

@section('page-title', 'Storage Provider')

@section('admin')
    <div class="mx-auto max-w-3xl"
         x-data="{ provider: '{{ old('storage_provider', $provider) }}' }">
        <div class="mb-6">
            <h1 class="font-display text-2xl font-extrabold text-ink-900">Storage Provider</h1>
            <p class="mt-1 text-sm text-slate-500">Choose where product images and downloadable files are stored.</p>
        </div>

        <form method="POST" action="{{ route('admin.storage.update') }}" class="card p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="storage_provider" class="label">Storage Provider</label>
                <select id="storage_provider" name="storage_provider" x-model="provider" class="input">
                    @foreach ($providers as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Info note --}}
            <div class="mt-5 rounded-2xl border border-brand-100 bg-brand-50/60 p-5 text-sm text-brand-800">
                <p class="flex items-start gap-2 font-semibold">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
                    When you change the storage provider, you must move all files from those paths to the new storage provider.
                </p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="font-bold text-brand-900">Local</p>
                        <ul class="mt-1 list-disc space-y-0.5 pl-5 font-mono text-xs text-brand-700">
                            <li>public/images/editor/</li>
                            <li>public/images/items/</li>
                            <li>public/files/items/</li>
                            <li>storage/app/files/</li>
                            <li>storage/app/files/items/</li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-bold text-brand-900">S3 and others</p>
                        <ul class="mt-1 list-disc space-y-0.5 pl-5 font-mono text-xs text-brand-700">
                            <li>images/editor/</li>
                            <li>images/items/</li>
                            <li>files/items/</li>
                            <li>files/</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Cloud credentials (shown only for non-local providers) --}}
            <div x-show="provider !== 'local'" x-cloak class="mt-6 space-y-5 border-t border-slate-100 pt-6">
                @unless ($s3Available)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        <span class="font-semibold">Heads up:</span> the AWS S3 SDK is not installed yet. Run
                        <code class="rounded bg-amber-100 px-1.5 py-0.5 font-mono text-xs">composer require league/flysystem-aws-s3-v3</code>
                        on your server before saving a cloud provider.
                    </div>
                @endunless

                <p class="text-sm text-slate-500" x-show="provider === 'spaces'">For DigitalOcean Spaces, set the endpoint like <code class="font-mono text-xs">https://nyc3.digitaloceanspaces.com</code> and region to your datacenter (e.g. <code class="font-mono text-xs">nyc3</code>).</p>
                <p class="text-sm text-slate-500" x-show="provider === 'r2'">For Cloudflare R2, set the endpoint to <code class="font-mono text-xs">https://&lt;account-id&gt;.r2.cloudflarestorage.com</code> and region to <code class="font-mono text-xs">auto</code>.</p>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="storage_s3_key" class="label">Access Key ID</label>
                        <input id="storage_s3_key" name="storage_s3_key" type="text" value="{{ old('storage_s3_key', $s3['key']) }}" class="input" autocomplete="off">
                    </div>
                    <div>
                        <label for="storage_s3_secret" class="label">Secret Access Key</label>
                        <input id="storage_s3_secret" name="storage_s3_secret" type="password" value="{{ old('storage_s3_secret', $s3['secret']) }}" class="input" autocomplete="off" placeholder="••••••••">
                    </div>
                    <div>
                        <label for="storage_s3_bucket" class="label">Bucket name</label>
                        <input id="storage_s3_bucket" name="storage_s3_bucket" type="text" value="{{ old('storage_s3_bucket', $s3['bucket']) }}" class="input">
                    </div>
                    <div>
                        <label for="storage_s3_region" class="label">Region</label>
                        <input id="storage_s3_region" name="storage_s3_region" type="text" value="{{ old('storage_s3_region', $s3['region']) }}" class="input" placeholder="us-east-1 / nyc3 / auto">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="storage_s3_endpoint" class="label">Endpoint <span class="text-slate-400">(leave blank for Amazon S3)</span></label>
                        <input id="storage_s3_endpoint" name="storage_s3_endpoint" type="text" value="{{ old('storage_s3_endpoint', $s3['endpoint']) }}" class="input" placeholder="https://...">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="storage_s3_url" class="label">Public URL base <span class="text-slate-400">(optional CDN / public bucket URL)</span></label>
                        <input id="storage_s3_url" name="storage_s3_url" type="text" value="{{ old('storage_s3_url', $s3['url']) }}" class="input" placeholder="https://cdn.example.com">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="storage_s3_path_style" value="1" {{ old('storage_s3_path_style', $s3['path_style']) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                    Use path-style endpoint (recommended for Spaces / R2 / MinIO)
                </label>
            </div>

            <div class="mt-6 flex justify-end border-t border-slate-100 pt-6">
                <button type="submit" class="btn-primary btn-lg">Save storage settings</button>
            </div>
        </form>

        {{-- Separate form so method spoofing (PUT) doesn't clash with the test POST --}}
        <form method="POST" action="{{ route('admin.storage.test') }}" class="mt-4 flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-5 py-4">
            @csrf
            <p class="text-sm text-slate-500"><strong>Save</strong> your settings first, then test — this does a real write / read / delete on your storage.</p>
            <button type="submit" class="btn-ghost btn-md flex-shrink-0">Test connection</button>
        </form>
    </div>
@endsection
