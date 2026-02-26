<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\HelpArticle;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('products.index');
    }

    public function show(Product $product)
    {
        // Security Guard: Prevent accessing unapproved products via slug
        if (!$product->isApproved()) {
            abort(404);
        }

        $product->load(['author', 'versions', 'reviews.buyer', 'category', 'bundles' => function($q) {
            $q->where('status', 'active')->with('products');
        }]);
        
        // Track view (Global Product Stats)
        $product->trackView();

        // Personalization: Track user interest if logged in
        if (auth()->check() && $product->category_id) {
            \App\Models\UserBehavior::trackView(auth()->id(), $product->category_id);
        }

        // INTERCONNECTION: Fetch related docs based on category
        $relatedDocs = HelpArticle::with('category')->where('is_published', true)
            ->whereHas('category', function($q) use ($product) {
                // Try to match Help Category name with Product Category name
                if ($product->category) {
                    $q->where('name', 'like', '%' . $product->category->name . '%');
                }
            })
            ->latest()
            ->take(3)
            ->get();

        // Fallback to generic docs if nothing found
        if ($relatedDocs->isEmpty()) {
            $relatedDocs = HelpArticle::with('category')->where('is_published', true)->latest()->take(3)->get();
        }
        
        return view('products.show', compact('product', 'relatedDocs'));
    }

    /**
     * Display a listing of the resource for a specific category (tag).
     */
    public function category(Category $category)
    {
        return view('products.index', [
            'category' => $category
        ]);
    }
}
