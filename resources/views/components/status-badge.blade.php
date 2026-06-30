@props(['status' => 'pending'])

@php
    $map = [
        'completed' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
        'published' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
        'pending'   => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        'draft'     => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
        'failed'    => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
        'refunded'  => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
        'archived'  => 'bg-slate-100 text-slate-500 ring-1 ring-slate-200',
        'admin'     => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200',
        'user'      => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
    ];
    $classes = $map[$status] ?? 'bg-slate-100 text-slate-600 ring-1 ring-slate-200';
@endphp

<span {{ $attributes->merge(['class' => "inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {$classes}"]) }}>{{ $status }}</span>
