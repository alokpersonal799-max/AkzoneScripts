<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.mail') }}" class="space-y-5">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Email / SMTP</h2>
        <p class="text-sm text-slate-500">Configure outgoing email. When disabled, emails are written to the log file.</p>

        <label class="flex items-center gap-2 rounded-xl border border-slate-200 p-4 text-sm font-semibold text-ink-900">
            <input type="checkbox" name="mail_enabled" value="1" {{ setting('mail_enabled') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
            Send email via SMTP
        </label>

        <div class="grid gap-5 sm:grid-cols-2">
            <div><label class="label">SMTP host</label><input name="mail_host" value="{{ old('mail_host', setting('mail_host')) }}" class="input" placeholder="smtp.gmail.com"></div>
            <div><label class="label">Port</label><input name="mail_port" value="{{ old('mail_port', setting('mail_port')) }}" class="input" placeholder="587"></div>
            <div><label class="label">Username</label><input name="mail_username" value="{{ old('mail_username', setting('mail_username')) }}" class="input"></div>
            <div><label class="label">Password</label><input name="mail_password" type="password" value="{{ old('mail_password', setting('mail_password')) }}" class="input"></div>
            <div>
                <label class="label">Encryption</label>
                <select name="mail_encryption" class="input">
                    @foreach (['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'None'] as $v => $l)
                        <option value="{{ $v }}" {{ setting('mail_encryption') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div><label class="label">From address</label><input name="mail_from_address" type="email" value="{{ old('mail_from_address', setting('mail_from_address')) }}" class="input"></div>
            <div><label class="label">From name</label><input name="mail_from_name" value="{{ old('mail_from_name', setting('mail_from_name')) }}" class="input" placeholder="{{ setting('site_name') }}"></div>
        </div>

        <button type="submit" class="btn-primary btn-md">Save email settings</button>
    </form>
</div>
