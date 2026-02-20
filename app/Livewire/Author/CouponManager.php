<?php

namespace App\Livewire\Author;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Flux;

class CouponManager extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $editingCouponId = null;

    // Form fields
    public $code;
    public $description;
    public $type = 'percentage';
    public $value;
    public $min_purchase;
    public $usage_limit;
    public $expires_at;
    public $status = 'active';
    public $selectedProducts = [];

    protected $rules = [
        'code' => 'required|string|max:50|unique:coupons,code',
        'type' => 'required|in:percentage,fixed',
        'value' => 'required|numeric|min:0',
        'min_purchase' => 'nullable|numeric|min:0',
        'usage_limit' => 'nullable|integer|min:1',
        'expires_at' => 'nullable|date|after:now',
        'status' => 'required|in:active,inactive',
    ];

    public function mount()
    {
        // No longer aborting 403, we will handle this in the UI for upsell purposes.
    }

    public function create()
    {
        if (!Auth::user()->isPro() && !Auth::user()->isElite()) {
            $this->dispatch('toast', variant: 'warning', heading: 'Upgrade Required', text: 'You need a Pro or Elite plan to create coupons.');
            return;
        }

        $this->reset(['code', 'description', 'type', 'value', 'min_purchase', 'usage_limit', 'expires_at', 'status', 'selectedProducts', 'editingCouponId']);
        $this->showCreateModal = true;
    }

    public function edit(Coupon $coupon)
    {
        // Plan check
        if (!Auth::user()->isPro() && !Auth::user()->isElite()) {
             $this->dispatch('toast', variant: 'warning', heading: 'Upgrade Required', text: 'You need a Pro or Elite plan to edit coupons.');
             return;
        }

        // Ownership check (Security: IDOR Protection)
        if ($coupon->author_id !== Auth::id()) {
            $this->dispatch('toast', variant: 'danger', heading: 'Access Denied', text: 'Unauthorized action.');
            return;
        }

        $this->editingCouponId = $coupon->id;
        $this->code = $coupon->code;
        $this->description = $coupon->description;
        $this->type = $coupon->type;
        $this->value = $coupon->value;
        $this->min_purchase = $coupon->min_purchase;
        $this->usage_limit = $coupon->usage_limit;
        $this->expires_at = $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : null;
        $this->status = $coupon->status === 'expired' ? 'active' : $coupon->status;
        $this->selectedProducts = $coupon->products->pluck('id')->toArray();
        
        $this->showCreateModal = true;
    }

    public function save()
    {
        // Plan check
        if (!Auth::user()->isPro() && !Auth::user()->isElite()) {
            $this->dispatch('toast', variant: 'warning', heading: 'Upgrade Required', text: 'Action denied. Please upgrade your plan.');
            return;
        }

        $this->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $this->editingCouponId,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'author_id' => Auth::id(),
            'code' => strtoupper($this->code),
            'description' => $this->description,
            'type' => $this->type,
            'value' => $this->value,
            'min_purchase' => $this->min_purchase,
            'usage_limit' => $this->usage_limit,
            'expires_at' => $this->expires_at,
            'status' => $this->status,
        ];

        if ($this->editingCouponId) {
            $coupon = Coupon::where('id', $this->editingCouponId)->where('author_id', Auth::id())->first();
            
            if (!$coupon) {
                $this->dispatch('toast', variant: 'danger', heading: 'Error', text: 'Coupon not found or unauthorized.');
                return;
            }

            $coupon->update($data);
            $coupon->products()->sync($this->selectedProducts);
            $message = 'Coupon updated successfully.';
        } else {
            $coupon = Coupon::create($data);
            $coupon->products()->sync($this->selectedProducts);
            $message = 'Coupon created successfully.';
        }

        $this->showCreateModal = false;
        $this->dispatch('toast', variant: 'success', heading: 'Success', text: $message);
    }

    public function toggleStatus(Coupon $coupon)
    {
        // Plan & Ownership check
        if (!Auth::user()->isPro() && !Auth::user()->isElite()) return;
        if ($coupon->author_id !== Auth::id()) return;

        $newStatus = $coupon->status === 'active' ? 'inactive' : 'active';
        $coupon->update(['status' => $newStatus]);
        $this->dispatch('toast', variant: 'success', heading: 'Status Updated', text: "Coupon is now {$newStatus}.");
    }

    public function delete(Coupon $coupon)
    {
        // Plan & Ownership check
        if (!Auth::user()->isPro() && !Auth::user()->isElite()) return;
        if ($coupon->author_id !== Auth::id()) return;

        $coupon->delete();
        $this->dispatch('toast', variant: 'success', heading: 'Deleted', text: 'Coupon has been removed.');
    }

    public function render()
    {
        return view('livewire.author.coupon-manager', [
            'coupons' => Coupon::where('author_id', Auth::id())
                ->withCount('usage')
                ->latest()
                ->paginate(10),
            'availableProducts' => Product::where('author_id', Auth::id())->get(),
        ])->layout('layouts.author');
    }
}
