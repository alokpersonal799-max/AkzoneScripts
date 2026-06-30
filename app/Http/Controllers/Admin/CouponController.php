<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        return view('admin.coupons.index', [
            'coupons' => Coupon::latest()->paginate(15),
            'suggestedCode' => Coupon::generateCode(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCoupon($request);
        Coupon::create($data);

        return back()->with('success', 'Coupon "'.$data['code'].'" created.');
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $data = $this->validateCoupon($request, $coupon);
        $coupon->update($data);

        return back()->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }

    /**
     * Return a fresh random code (for the "Generate" button).
     */
    public function generate(): JsonResponse
    {
        return response()->json(['code' => Coupon::generateCode()]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateCoupon(Request $request, ?Coupon $coupon = null): array
    {
        $codeRule = 'unique:coupons,code'.($coupon ? ','.$coupon->id : '');

        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', $codeRule],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_order' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['min_order'] = $data['min_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
