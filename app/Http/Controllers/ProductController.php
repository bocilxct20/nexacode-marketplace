<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductTag;
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

        $product->load(['author', 'versions', 'reviews.buyer', 'tags', 'bundles' => function($q) {
            $q->where('status', 'active')->with('products');
        }]);
        
        // Track view
        $product->trackView();
        
        return view('products.show', compact('product'));
    }

    /**
     * Display a listing of the resource for a specific category (tag).
     */
    public function category(ProductTag $category)
    {
        return view('products.index', [
            'category' => $category
        ]);
    }
}
