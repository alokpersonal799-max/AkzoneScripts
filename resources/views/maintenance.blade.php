<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
</head>
<body class="flex min-h-screen items-center justify-center bg-ink-900 px-4 font-sans text-slate-300 antialiased">
    <div class="dot-grid absolute inset-0 opacity-40"></div>
    <div class="relative max-w-lg text-center">
        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-brand-300">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" /></svg>
        </span>
        <h1 class="mt-6 font-display text-3xl font-extrabold text-white">We'll be right back</h1>
        <p class="mt-4 text-slate-400">{{ $message ?: 'We are performing scheduled maintenance. Please check back shortly.' }}</p>
    </div>
</body>
</html>
