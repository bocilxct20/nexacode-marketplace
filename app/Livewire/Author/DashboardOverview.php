<?php

namespace App\Livewire\Author;

use App\Models\Product;
use App\Models\Earning;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardOverview extends Component
{
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        $user = Auth::user();
        $levelService = app(\App\Services\AuthorLevelService::class);
        
        $data = [
            'levelInfo' => $levelService->getProgress($user),
            'subscriptionDaysLeft' => $user->subscription_ends_at ? now()->diffInDays($user->subscription_ends_at, false) : null,
            'finalCommissionRate' => $levelService->getFinalCommissionRate($user),
        ];

        if ($this->readyToLoad) {
            $data = array_merge($data, [
                'availableBalance' => Earning::where('author_id', $user->id)
                    ->where('status', Earning::STATUS_AVAILABLE)
                    ->sum('amount'),
                'pendingBalance' => Earning::where('author_id', $user->id)
                    ->where('status', Earning::STATUS_PENDING)
                    ->sum('amount'),
                'productsCount' => Product::where('author_id', $user->id)->count(),
                'totalEarnings' => Earning::where('author_id', $user->id)->sum('amount'),
                'recentSales' => Earning::where('author_id', $user->id)
                    ->with(['product', 'order'])
                    ->latest()
                    ->take(5)
                    ->get(),
                'averageRating' => Product::where('author_id', $user->id)
                    ->where('avg_rating', '>', 0)
                    ->avg('avg_rating') ?? 0,
                'totalReviews' => Review::whereHas('product', function($q) use ($user) {
                    $q->where('author_id', $user->id);
                })->count(),
                'topProducts' => Product::where('author_id', $user->id)
                    ->orderBy('sales_count', 'desc')
                    ->take(3)
                    ->get(),
                'eliteMetrics' => $user->isElite() ? [
                    'itemViews' => \App\Models\ProductView::whereIn('product_id', function($q) use ($user) {
                        $q->select('id')->from('products')->where('author_id', $user->id);
                    })->where('created_at', '>=', now()->subDays(30))->count(),
                    'previousItemViews' => \App\Models\ProductView::whereIn('product_id', function($q) use ($user) {
                        $q->select('id')->from('products')->where('author_id', $user->id);
                    })->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count(),
                    'purchases' => Earning::where('author_id', $user->id)->where('created_at', '>=', now()->subDays(30))->count(),
                ] : null,
            ]);

            if ($data['eliteMetrics']) {
                $data['eliteMetrics']['conversionRate'] = $data['eliteMetrics']['itemViews'] > 0 
                    ? ($data['eliteMetrics']['purchases'] / $data['eliteMetrics']['itemViews']) * 100 
                    : 0;
                
                $data['eliteMetrics']['itemViewsGrowth'] = $data['eliteMetrics']['previousItemViews'] > 0
                    ? (($data['eliteMetrics']['itemViews'] - $data['eliteMetrics']['previousItemViews']) / $data['eliteMetrics']['previousItemViews']) * 100
                    : 0;

                // Competitor Benchmarking logic
                $primaryTag = \DB::table('product_tag')
                    ->join('products', 'product_tag.product_id', '=', 'products.id')
                    ->where('products.author_id', $user->id)
                    ->select('product_tag_id', \DB::raw('count(*) as count'))
                    ->groupBy('product_tag_id')
                    ->orderBy('count', 'desc')
                    ->first();

                if ($primaryTag) {
                    $tagId = $primaryTag->product_tag_id;
                    $data['eliteMetrics']['primaryCategory'] = \App\Models\ProductTag::find($tagId)->name;
                    
                    // Category Average Conversion Rate
                    $categoryProductIds = \DB::table('product_tag')
                        ->where('product_tag_id', $tagId)
                        ->pluck('product_id');

                    $categoryViews = \App\Models\ProductView::whereIn('product_id', $categoryProductIds)
                        ->where('created_at', '>=', now()->subDays(30))
                        ->count();
                    
                    $categoryPurchases = \App\Models\OrderItem::whereIn('product_id', $categoryProductIds)
                        ->where('created_at', '>=', now()->subDays(30))
                        ->count();

                    $data['eliteMetrics']['categoryAvgConversion'] = $categoryViews > 0 
                        ? ($categoryPurchases / $categoryViews) * 100 
                        : 0;
                    
                    $data['eliteMetrics']['categoryAvgRating'] = Product::whereIn('id', $categoryProductIds)
                        ->where('avg_rating', '>', 0)
                        ->avg('avg_rating') ?? 0;
                }
            }

            // Calculate earnings growth
            $currentMonthEarnings = Earning::where('author_id', $user->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('amount');
            
            $previousMonthEarnings = Earning::where('author_id', $user->id)
                ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
                ->sum('amount');
            
            $data['earningsGrowth'] = $previousMonthEarnings > 0 
                ? (($currentMonthEarnings - $previousMonthEarnings) / $previousMonthEarnings) * 100 
                : 0;

            // Sales Activity Data (Last 14 days)
            $data['salesActivity'] = Earning::where('author_id', $user->id)
                ->where('created_at', '>=', now()->subDays(14))
                ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->pluck('total', 'date');
        }

        return view('livewire.author.dashboard-overview', array_merge(['user' => $user], $data));
    }
}
