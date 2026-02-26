<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class AuthorRanking extends Component
{
    use WithPagination;

    public function render()
    {
        $authors = User::whereHas('roles', fn($q) => $q->where('slug', 'author'))
            ->addSelect([
                'total_sales' => \App\Models\Product::selectRaw('IFNULL(sum(sales_count), 0)')
                    ->whereColumn('author_id', 'users.id'),
                'total_revenue' => \App\Models\Earning::selectRaw('IFNULL(sum(amount), 0)')
                    ->whereColumn('author_id', 'users.id'),
                'avg_rating' => \App\Models\Product::selectRaw('IFNULL(avg(avg_rating), 0)')
                    ->whereColumn('author_id', 'users.id'),
                'is_elite' => \App\Models\SubscriptionPlan::select('is_elite')
                    ->whereColumn('id', 'users.subscription_plan_id')
                    ->limit(1),
                'allow_trial' => \App\Models\SubscriptionPlan::select('allow_trial')
                    ->whereColumn('id', 'users.subscription_plan_id')
                    ->limit(1),
            ])
            ->with(['products', 'communityBadges'])
            ->withCount('products')
            ->orderByRaw('
                (IFNULL(total_sales, 0) * 15) + 
                (IFNULL(total_revenue, 0) / 10000) + 
                (IFNULL(avg_rating, 0) * 250) + 
                (products_count * 100) + 
                (xp * 0.15) + 
                (CASE WHEN is_elite = 1 THEN 10000 WHEN allow_trial = 1 THEN 3000 ELSE 0 END) DESC
            ')
            ->paginate(12);

        return view('livewire.page.author-ranking', [
            'authors' => $authors
        ])->layout('layouts.app');
    }
}
