<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#334155;">
    <div style="max-width:560px;margin:0 auto;padding:32px 16px;">
        <div style="text-align:center;margin-bottom:24px;">
            <span style="font-size:22px;font-weight:800;color:#0b1120;">{{ setting('site_name', config('app.name')) }}</span>
        </div>
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:32px;">
            @yield('email')
        </div>
        <p style="text-align:center;color:#94a3b8;font-size:12px;margin-top:24px;">
            &copy; {{ date('Y') }} {{ setting('site_name', config('app.name')) }}. All rights reserved.
        </p>
    </div>
</body>
</html>
