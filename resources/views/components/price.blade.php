@props(['amount' => 0])

@php $value = (float) $amount; @endphp

@if ($value <= 0)
    <span {{ $attributes->merge(['class' => 'font-semibold text-emerald-600']) }}>Free</span>
@else
    <span {{ $attributes }}>{{ money($value) }}</span>
@endif
