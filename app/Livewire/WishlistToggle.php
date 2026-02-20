<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistToggle extends Component
{
    public $productId;
    public $isWishlisted = false;
    public $variant = 'ghost'; // ghost or primary

    public function mount($productId, $variant = 'ghost')
    {
        $this->productId = $productId;
        $this->variant = $variant;

        if (Auth::check()) {
            $this->isWishlisted = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $this->productId)
                ->exists();
        }
    }

    public function toggle()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($this->isWishlisted) {
            Wishlist::where('user_id', Auth::id())
                ->where('product_id', $this->productId)
                ->delete();
            $this->isWishlisted = false;
            $this->dispatch('wishlist-updated', ['productId' => $this->productId, 'action' => 'removed']);
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $this->productId,
            ]);
            $this->isWishlisted = true;
            $this->dispatch('wishlist-updated', ['productId' => $this->productId, 'action' => 'added']);
        }
    }

    public function render()
    {
        return view('livewire.wishlist-toggle');
    }
}
