<?php

namespace App\Livewire\Product;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Flux;

class ReviewForm extends Component
{
    use WithFileUploads;

    public Product $product;
    public $rating = 5;
    public $comment = '';
    public $photos = [];

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:10',
        'photos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function removePhoto($index)
    {
        array_splice($this->photos, $index, 1);
    }

    public function submit()
    {
        $this->validate();

        $user = Auth::user();

        // Security check: Must have purchased the product
        $order = Order::where('buyer_id', $user->id)->whereHas('items', function($q) {
            $q->where('product_id', $this->product->id);
        })->where('status', 'completed')->first();

        if (!$order) {
            Flux::toast(variant: 'danger', text: 'You must purchase this product before leaving a review.');
            return;
        }

        // Check if already reviewed
        $existing = Review::where('buyer_id', $user->id)->where('product_id', $this->product->id)->first();
        if ($existing) {
            Flux::toast(variant: 'danger', text: 'You have already reviewed this product.');
            return;
        }

        $mediaPaths = [];
        if (!empty($this->photos)) {
            foreach ($this->photos as $photo) {
                $path = $photo->store('reviews', 'public');
                $mediaPaths[] = $path;
            }
        }

        Review::create([
            'product_id' => $this->product->id,
            'buyer_id' => $user->id,
            'order_id' => $order->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'media' => !empty($mediaPaths) ? $mediaPaths : null,
        ]);

        // Update product average rating
        $avgRating = Review::where('product_id', $this->product->id)->avg('rating');
        $this->product->update(['avg_rating' => round($avgRating, 1)]);

        $this->reset(['comment', 'photos', 'rating']);
        
        $this->dispatch('review-submitted');
        
        // Use Flux toast via redirect/flash or just direct if possible
        session()->flash('success', 'Thank you for your review!');
        return redirect()->route('products.show', $this->product->slug);
    }

    public function render()
    {
        return view('livewire.product.review-form');
    }
}
