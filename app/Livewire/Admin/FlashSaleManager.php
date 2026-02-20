<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\FlashSale;

class FlashSaleManager extends Component
{
    public $title;
    public $discount_percentage = 10;
    public $starts_at;
    public $ends_at;
    public $banner_message;
    public $is_active = false;

    public $editingSaleId = null;

    public function mount()
    {
        $this->starts_at = now()->format('Y-m-d\TH:i');
        $this->ends_at = now()->addDays(7)->format('Y-m-d\TH:i');
    }

    public function save()
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        $this->validate([
            'title' => 'required|string|max:255',
            'discount_percentage' => 'required|integer|min:1|max:99',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'banner_message' => 'nullable|string|max:255',
        ]);

        FlashSale::updateOrCreate(
            ['id' => $this->editingSaleId],
            [
                'title' => $this->title,
                'discount_percentage' => $this->discount_percentage,
                'starts_at' => $this->starts_at,
                'ends_at' => $this->ends_at,
                'is_active' => $this->is_active,
                'banner_message' => $this->banner_message,
            ]
        );

        $this->reset(['title', 'discount_percentage', 'banner_message', 'is_active', 'editingSaleId']);
        $this->dispatch('toast', variant: 'success', heading: 'Flash Sale Saved', text: 'Marketplace discounts updated.');
    }

    public function delete($id)
    {
        if (!auth()->user()?->isAdmin()) abort(403);
        FlashSale::find($id)->delete();
        $this->dispatch('toast', variant: 'success', heading: 'Flash Sale Removed', text: 'Discount event deleted.');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.flash-sale-manager', [
            'sales' => FlashSale::latest()->get()
        ]);
    }
}
