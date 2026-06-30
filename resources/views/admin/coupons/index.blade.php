@extends('layouts.admin')

@section('page-title', 'Coupons')

@section('admin')
<div class="grid gap-6 lg:grid-cols-[1fr_360px]">
    {{-- List --}}
    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-5">
            <h2 class="font-display text-lg font-bold text-ink-900">Coupon codes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Code</th>
                        <th class="px-5 py-3 font-semibold">Discount</th>
                        <th class="px-5 py-3 font-semibold">Used</th>
                        <th class="px-5 py-3 font-semibold">Expires</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($coupons as $coupon)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-3"><span class="rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-sm font-bold text-ink-900">{{ $coupon->code }}</span></td>
                            <td class="px-5 py-3 text-slate-600">{{ $coupon->type === 'percent' ? rtrim(rtrim(number_format($coupon->value, 2), '0'), '.').'%' : base_symbol().number_format($coupon->value, 2) }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $coupon->used_count }}{{ $coupon->max_uses ? ' / '.$coupon->max_uses : '' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $coupon->expires_at ? $coupon->expires_at->format('M j, Y') : 'Never' }}</td>
                            <td class="px-5 py-3"><x-status-badge :status="$coupon->isValid() ? 'published' : 'archived'" /></td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Delete this coupon?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No coupons yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5">{{ $coupons->links() }}</div>
    </div>

    {{-- Create --}}
    <div class="card h-fit p-6" x-data="{ code: '{{ $suggestedCode }}', type: 'percent' }">
        <h2 class="font-display text-base font-bold text-ink-900">Create coupon</h2>
        <form method="POST" action="{{ route('admin.coupons.store') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="label">Code</label>
                <div class="flex gap-2">
                    <input name="code" x-model="code" required class="input font-mono uppercase">
                    <button type="button" @click="fetch('{{ route('admin.coupons.generate') }}').then(r => r.json()).then(d => code = d.code)" class="btn-ghost btn-sm flex-shrink-0" title="Generate">🎲</button>
                </div>
            </div>
            <div>
                <label class="label">Type</label>
                <select name="type" x-model="type" class="input">
                    <option value="percent">Percentage (%)</option>
                    <option value="fixed">Fixed amount</option>
                </select>
            </div>
            <div>
                <label class="label">Value <span x-show="type==='percent'">(%)</span></label>
                <input name="value" type="number" step="0.01" min="0" required class="input" placeholder="10">
            </div>
            <div>
                <label class="label">Minimum order <span class="text-slate-400">(optional)</span></label>
                <input name="min_order" type="number" step="0.01" min="0" class="input" placeholder="0">
            </div>
            <div>
                <label class="label">Max uses <span class="text-slate-400">(blank = unlimited)</span></label>
                <input name="max_uses" type="number" min="1" class="input">
            </div>
            <div>
                <label class="label">Expires at <span class="text-slate-400">(optional)</span></label>
                <input name="expires_at" type="date" class="input">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30"> Active
            </label>
            <button type="submit" class="btn-primary btn-md w-full">Create coupon</button>
        </form>
    </div>
</div>
@endsection
