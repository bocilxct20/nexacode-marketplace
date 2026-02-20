<?php

namespace App\Livewire\Admin\Affiliate;

use App\Models\Coupon;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class AffiliateCouponManager extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingCouponId = null;

    // Form fields
    public $code;
    public $affiliate_id;
    public $type = 'percentage';
    public $value = 10;
    public $status = 'active';

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.affiliate.affiliate-coupon-manager', [
            'coupons' => Coupon::whereNotNull('affiliate_id')
                ->with('affiliate')
                ->latest()
                ->paginate(10),
            'affiliates' => User::whereNotNull('affiliate_code')->get(),
        ]);
    }

    public function create()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->reset(['code', 'affiliate_id', 'type', 'value', 'status', 'editingCouponId']);
        $this->showModal = true;
    }

    public function edit(Coupon $coupon)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->editingCouponId = $coupon->id;
        $this->code = $coupon->code;
        $this->affiliate_id = $coupon->affiliate_id;
        $this->type = $coupon->type;
        $this->value = $coupon->value;
        $this->status = $coupon->status->value;
        $this->showModal = true;
    }

    public function save()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $this->editingCouponId,
            'affiliate_id' => 'required|exists:users,id',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'code' => strtoupper($this->code),
            'affiliate_id' => $this->affiliate_id,
            'type' => $this->type,
            'value' => $this->value,
            'status' => $this->status,
        ];

        Coupon::updateOrCreate(['id' => $this->editingCouponId], $data);

        $this->showModal = false;
        Flux::toast(
            variant: 'success',
            heading: 'Success',
            text: 'Affiliate coupon saved successfully.'
        );
    }

    public function delete($id)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        Coupon::findOrFail($id)->delete();
        Flux::toast(
            variant: 'success',
            heading: 'Deleted',
            text: 'Coupon removed successfully.'
        );
    }
}
