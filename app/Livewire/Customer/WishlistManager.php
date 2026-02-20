<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Wishlist;

class WishlistManager extends Component
{
    use WithPagination;

    public $readyToLoad = false;

    public function load()
    {
        $this->readyToLoad = true;
    }

    public function removeFromWishlist($wishlistId)
    {
        Wishlist::where('id', $wishlistId)
            ->where('user_id', auth()->id())
            ->delete();

        $this->dispatch('wishlist-updated');

        \Flux::toast(
            variant: 'success',
            heading: 'Item removed',
            text: 'We\'ve removed this item from your saved list.',
        );
    }

    public function render()
    {
        if (!$this->readyToLoad) {
            return view('livewire.customer.wishlist-manager', [
                'wishlist' => Wishlist::whereRaw('1 = 0')->paginate(12),
                'stats' => ['total' => 0]
            ]);
        }

        $wishlist = Wishlist::where('user_id', auth()->id())
            ->with('product')
            ->latest()
            ->paginate(12);

        $stats = [
            'total' => Wishlist::where('user_id', auth()->id())->count(),
        ];

        return view('livewire.customer.wishlist-manager', [
            'wishlist' => $wishlist,
            'stats' => $stats
        ]);
    }
}
