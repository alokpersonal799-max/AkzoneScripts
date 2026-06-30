@props(['amount' => 0])

@php
    $symbol = config('marketplace.currency_symbol', '$');
    $value = (float) $amount;
@endphp

@if ($value <= 0)
    <span {{ $attributes->merge(['class' => 'font-semibold text-emerald-600']) }}>Free</span>
@else
    <span {{ $attributes }}>{{ $symbol }}{{ number_format($value, 2) }}</span>
@endif
