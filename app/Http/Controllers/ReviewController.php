<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Flux;

class ReviewController extends Controller
{
    /**
     * Store a new review.
     */
    public function store(Request $request, Product $product)
    {
        $product->load('author');
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
            'media.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        // Security check: Must have purchased the product
        $order = Order::where('buyer_id', $user->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->whereHas('items', function($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->first();

        // Prevention of self-review
        if ($product->author_id === $user->id) {
            return back()->with('error', 'Kamu tidak bisa memberi ulasan pada produk sendiri.');
        }

        if (!$order) {
            return back()->with('error', 'Kamu harus membeli produk ini terlebih dahulu sebelum meninggalkan ulasan.');
        }

        // Check if already reviewed
        $existing = Review::where('buyer_id', $user->id)->where('product_id', $product->id)->first();
        if ($existing) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('reviews', 'public');
                $mediaPaths[] = $path;
            }
        }

        $review = Review::create([
            'product_id' => $product->id,
            'buyer_id' => $user->id,
            'order_id' => $order ? $order->id : null,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'media' => !empty($mediaPaths) ? $mediaPaths : null,
        ]);

        // Update product average rating
        $avgRating = Review::where('product_id', $product->id)->avg('rating');
        $product->update(['avg_rating' => round($avgRating, 1)]);

        // Notify Author
        \Illuminate\Support\Facades\Mail::to($product->author->email)->queue(new \App\Mail\NewReviewNotification($review));

        return back()->with('status', 'Thank you for your review!');
    }

    /**
     * Author reply to a review.
     */
    public function reply(Request $request, Review $review)
    {
        $validated = $request->validate([
            'reply' => 'required|string|min:5',
        ]);

        $user = Auth::user();

        // Authorization check: Only the author of the product can reply
        if ($review->product->author_id !== $user->id) {
            abort(403);
        }

        $review->update([
            'author_reply' => $validated['reply'],
            'author_replied_at' => now(),
        ]);

        // Notify Buyer
        \Illuminate\Support\Facades\Mail::to($review->buyer->email)->queue(new \App\Mail\ReviewReplyNotification($review));

        Flux::toast(variant: 'success', text: 'Reply posted successfully.');

        return back();
    }
}
