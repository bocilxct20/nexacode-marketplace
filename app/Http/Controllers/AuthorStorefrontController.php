<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class AuthorStorefrontController extends Controller
{
    /**
     * Display the public profile of an author.
     */
    public function show($identifier)
    {
        $user = User::where('username', $identifier)
            ->orWhere('id', $identifier)
            ->firstOrFail();
        // Ensure the user is an author
        if (!$user->hasRole('author')) {
            abort(404);
        }

        $totalSales   = Product::where('author_id', $user->id)->sum('sales_count');
        $avgRating    = Product::where('author_id', $user->id)->avg('avg_rating');
        $productCount = Product::where('author_id', $user->id)->approved()->count();
        $isElite      = $user->isElite();
        $followerCount = $user->followers()->count();
        $memberSince  = $user->created_at->format('M Y');

        $reputation        = $user->reputation;

        return view('authors.show', compact(
            'user', 
            'totalSales', 
            'avgRating', 
            'isElite', 
            'productCount', 
            'followerCount', 
            'memberSince',
            'reputation'
        ));
    }
}
