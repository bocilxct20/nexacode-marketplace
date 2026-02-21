<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function faq()
    {
        return view('pages.faq');
    }

    public function home()
    {
        $categories = \App\Models\Category::orderBy('sort_order')->get();
        $featuredProducts = \App\Models\Product::featured()->inRandomOrder()->take(3)->get();
        $trendingTags = \App\Models\ProductTag::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get();
        
        // Dynamic Stats for Trust Ribbon
        $stats = [
            'products_count' => \App\Models\Product::approved()->count(),
            'authors_count' => \App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'author'))->count(),
            'buyers_count' => \App\Models\Order::distinct('buyer_id')->count('buyer_id'),
            // "Powered" stat: Total sales or unique users who have made purchases
            'powered_count' => \App\Models\Order::count(),
        ];

        // Platform Features: Why NexaCode?
        $features = [
            [
                'title' => 'Vetted & Secure',
                'description' => 'Setiap item diperiksa secara manual oleh tim ahli kami untuk memastikan kualitas kode dan keamanan tingkat tinggi.',
                'icon' => 'shield-check',
                'color' => 'emerald'
            ],
            [
                'title' => 'Lifetime Updates',
                'description' => 'Beli sekali, dapatkan semua update di masa depan secara gratis. Tanpa biaya langganan tambahan untuk aset yang sama.',
                'icon' => 'arrow-path-rounded-square',
                'color' => 'cyan'
            ],
            [
                'title' => 'Expert Support',
                'description' => 'Dukungan langsung dari para author aset untuk membantu kamu dalam implementasi dan kustomisasi produk.',
                'icon' => 'chat-bubble-left-right',
                'color' => 'amber'
            ]
        ];

        // Community Testimonials
        $testimonials = [
            [
                'name' => 'Alex Santoso',
                'role' => 'Fullstack Developer',
                'quote' => '"NexaCode benar-benar menghemat waktu saya. Saya bisa mendeploy MVP startup saya hanya dalam seminggu menggunakan script Laravel premium dari sini."',
                'avatar' => 'Alex+S',
                'color' => 'emerald'
            ],
            [
                'name' => 'Sarah Jessica',
                'role' => 'UI/UX Designer',
                'quote' => '"UI Kit yang saya beli di NexaCode sangat bersih dan dokumentasinya lengkap. Authornya juga sangat membantu saat saya ada pertanyaan kustomisasi."',
                'avatar' => 'Sarah+J',
                'color' => '0ea5e9'
            ],
            [
                'name' => 'Budi Pratama',
                'role' => 'Indie Hacker',
                'quote' => '"Marketplace ternyaman menurut saya. Proses checkout cepat, download instan, dan yang paling penting itemnya sudah diverifikasi keamanannya."',
                'avatar' => 'Budi+P',
                'color' => '6366f1'
            ]
        ];
        
        // Top Rated Authors (Smart Leaderboard)
        $topAuthors = \App\Models\User::whereHas('roles', fn($q) => $q->where('slug', 'author'))
            ->get()
            ->sortByDesc('ranking_score')
            ->take(6);
        
        return view('welcome', compact('categories', 'featuredProducts', 'trendingTags', 'stats', 'features', 'testimonials', 'topAuthors'));
    }
}
