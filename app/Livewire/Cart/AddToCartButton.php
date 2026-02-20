<?php

namespace App\Livewire\Cart;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Flux;

class AddToCartButton extends Component
{
    public $productId;
    public $inCart = false;

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->checkIfInCart();
    }

    public function checkIfInCart()
    {
        if (Auth::check()) {
            $this->inCart = CartItem::where('user_id', Auth::id())
                ->where('product_id', $this->productId)
                ->exists();
        }
    }

    public function toggle()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($this->inCart) {
            CartItem::where('user_id', Auth::id())
                ->where('product_id', $this->productId)
                ->delete();
            $this->inCart = false;
            Flux::toast(text: 'Produk dihapus dari keranjang.');
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $this->productId,
            ]);
            $this->inCart = true;
            Flux::toast(variant: 'success', text: 'Ditambahkan ke keranjang!');
        }

        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.cart.add-to-cart-button');
    }
}
