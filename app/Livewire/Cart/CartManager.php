<?php

namespace App\Livewire\Cart;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductBundle;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Flux;

class CartManager extends Component
{
    public $readyToLoad = false;

    protected $listeners = [
        'itemAddedToCart' => 'loadCart',
        'bundleAddedToCart' => 'addBundleToCart',
        'cartUpdated' => '$refresh'
    ];

    public function loadCart()
    {
        $this->readyToLoad = true;
    }

    public function addToCart($productId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $product = Product::findOrFail($productId);

        // Check if already in cart
        $exists = CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            Flux::toast(variant: 'warning', text: 'Produk sudah ada di keranjang.');
            return;
        }

        CartItem::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
        ]);

        $this->dispatch('cartUpdated');
        Flux::toast(variant: 'success', text: 'Produk ditambahkan ke keranjang!');
    }

    public function addBundleToCart($bundleId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $bundle = ProductBundle::active()->findOrFail($bundleId);

        // Check if already in cart
        $exists = CartItem::where('user_id', Auth::id())
            ->where('bundle_id', $bundleId)
            ->exists();

        if ($exists) {
            Flux::toast(variant: 'warning', text: 'Bundel sudah ada di keranjang.');
            return;
        }

        CartItem::create([
            'user_id' => Auth::id(),
            'bundle_id' => $bundleId,
        ]);

        $this->dispatch('cartUpdated');
        Flux::toast(variant: 'success', text: 'Bundel ditambahkan ke keranjang!');
    }

    public function removeItem($itemId)
    {
        $item = CartItem::where('user_id', Auth::id())->findOrFail($itemId);
        $item->delete();

        $this->dispatch('cartUpdated');
        Flux::toast(text: 'Item dihapus dari keranjang.');
    }

    public function clearCart()
    {
        CartItem::where('user_id', Auth::id())->delete();
        $this->dispatch('cartUpdated');
        Flux::toast(text: 'Keranjang dikosongkan.');
    }

    public function render()
    {
        $items = Auth::check() 
            ? CartItem::with(['product', 'bundle.products'])->where('user_id', Auth::id())->get()
            : collect([]);

        $total = $items->sum(function($item) {
            if ($item->bundle_id) {
                return $item->bundle->price;
            }
            return $item->product ? $item->product->price : 0;
        });

        return view('livewire.cart.cart-manager', [
            'items' => $items,
            'total' => $total,
        ]);
    }
}
