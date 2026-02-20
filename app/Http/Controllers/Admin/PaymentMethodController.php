<?php

namespace App\Http\Controllers\Admin;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of payment methods
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('sort_order')->get();
        
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    /**
     * Show the form for creating a new payment method
     */
    public function create()
    {
        return view('admin.payment-methods.form');
    }

    /**
     * Store a newly created payment method
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:bank_transfer,qris,ewallet',
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'qris_static' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'instructions' => 'nullable|array',
            'instructions.*' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('payment-logos', 'public');
        }

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('status', 'Payment method created successfully.');
    }

    /**
     * Show the form for editing the specified payment method
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.form', compact('paymentMethod'));
    }

    /**
     * Update the specified payment method
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'type' => 'required|in:bank_transfer,qris,ewallet',
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'qris_static' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'instructions' => 'nullable|array',
            'instructions.*' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($paymentMethod->logo) {
                Storage::disk('public')->delete($paymentMethod->logo);
            }
            $validated['logo'] = $request->file('logo')->store('payment-logos', 'public');
        }

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('status', 'Payment method updated successfully.');
    }

    /**
     * Remove the specified payment method
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        // Delete logo if exists
        if ($paymentMethod->logo) {
            Storage::disk('public')->delete($paymentMethod->logo);
        }

        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('status', 'Payment method deleted successfully.');
    }

    /**
     * Toggle active status
     */
    public function toggle(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update([
            'is_active' => !$paymentMethod->is_active,
        ]);

        return back()->with('status', 'Payment method status updated.');
    }
}
