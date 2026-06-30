@props(['status' => 'pending'])

@php
    $map = [
        'completed' => 'bg-emerald-500/15 text-emerald-300',
        'published' => 'bg-emerald-500/15 text-emerald-300',
        'pending'   => 'bg-amber-500/15 text-amber-300',
        'draft'     => 'bg-slate-500/15 text-slate-300',
        'failed'    => 'bg-rose-500/15 text-rose-300',
        'refunded'  => 'bg-rose-500/15 text-rose-300',
        'archived'  => 'bg-slate-500/15 text-slate-400',
        'admin'     => 'bg-indigo-500/15 text-indigo-300',
        'user'      => 'bg-slate-500/15 text-slate-300',
    ];
    $classes = $map[$status] ?? 'bg-slate-500/15 text-slate-300';
@endphp

<span {{ $attributes->merge(['class' => "mt-1 inline-block rounded-full px-2.5 py-0.5 text-xs font-medium capitalize {$classes}"]) }}>{{ $status }}</span>
