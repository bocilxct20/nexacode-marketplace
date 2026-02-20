<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Earning;
use App\Models\Payout;
use App\Models\Review;
use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SecurityService;
use App\Services\AuthorAnalyticsService;
use App\Http\Requests\Author\StoreProductRequest;
use App\Http\Requests\Author\UpdateProductRequest;
use App\Http\Requests\Author\UpdateProfileRequest;
use Illuminate\Support\Str;
use Flux;

class AuthorController extends Controller
{
    protected $analytics;

    public function __construct(AuthorAnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    /**
     * Display the author dashboard overview.
     */
    public function index()
    {
        $user = Auth::user();
        $stats = $this->analytics->getDashboardStats($user);
        $topProducts = $this->analytics->getTopProducts($user);
        
        $recentSales = Earning::where('author_id', $user->id)
            ->with(['product', 'order'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('author.index', array_merge($stats, [
            'user' => $user,
            'recentSales' => $recentSales,
            'topProducts' => $topProducts,
        ]));
    }

    /**
     * Display the author's products.
     */
    public function products()
    {
        $products = Product::where('author_id', Auth::id())->withCount('versions')->latest()->paginate(10);
        return view('author.products', compact('products'));
    }

    public function create()
    {
        $tags = ProductTag::all();
        return view('author.products.create', compact('tags'));
    }

    public function store(StoreProductRequest $request)
    {
        $user = Auth::user();
        $screenshots = $request->screenshots 
            ? array_filter(array_map('trim', explode("\n", $request->screenshots))) 
            : null;

        $product = Product::create([
            'author_id' => $user->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'thumbnail' => $request->thumbnail,
            'screenshots' => $screenshots,
            'video_url' => $request->video_url,
            'price' => $request->price,
            'demo_url' => $request->demo_url,
            'status' => $user->isElite() ? \App\Enums\ProductStatus::APPROVED : \App\Enums\ProductStatus::PENDING,
        ]);

        \Illuminate\Support\Facades\Mail::to(config('mail.aliases.admin'))->queue(new \App\Mail\AdminNewProductSubmission($product));

        if ($request->has('tags')) {
            $product->tags()->attach($request->tags);
        }

        $message = $user->isElite() 
            ? 'Product instantly approved and live on marketplace! Elite perk activated. ✨' 
            : 'Product submitted successfully. Our team will review it shortly.';

        return redirect()->route('author.products')->with('status', $message);
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $tags = ProductTag::all();
        $product->load('tags');
        return view('author.products.edit', compact('product', 'tags'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $screenshots = $request->screenshots 
            ? array_filter(array_map('trim', explode("\n", $request->screenshots))) 
            : null;

        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'demo_url' => $request->demo_url,
            'video_url' => $request->video_url,
            'screenshots' => $screenshots,
            'thumbnail' => $request->thumbnail ?? $product->thumbnail,
        ]);

        if ($request->has('tags')) {
            $product->tags()->sync($request->tags);
        }

        return redirect()->route('author.products')->with('status', 'Product updated successfully.');
    }

    public function createVersion(Product $product)
    {
        $this->authorize('manageVersion', $product);
        return view('author.products.version', compact('product'));
    }

    public function storeVersion(Request $request, Product $product)
    {
        $this->authorize('manageVersion', $product);
        
        $request->validate([
            'version_number' => 'required|string|max:20',
            'changelog' => 'required|string',
        ]);

        $product->versions()->create([
            'version_number' => $request->version_number,
            'changelog' => $request->changelog,
        ]);

        return redirect()->route('author.products')
            ->with('status', "Version {$request->version_number} released successfully.");
    }

    public function earnings()
    {
        $user = Auth::user();
        $earnings = Earning::where('author_id', $user->id)->with('product')->latest()->paginate(10);
        $payouts = Payout::where('author_id', $user->id)->latest()->get();

        return view('author.earnings', compact('earnings', 'payouts'));
    }

    public function reviews()
    {
        $user = Auth::user();
        $reviews = Review::whereHas('product', function($q) use ($user) {
            $q->where('author_id', $user->id);
        })->with(['product', 'buyer'])->latest()->paginate(10);

        return view('author.reviews', compact('reviews'));
    }

    public function profile()
    {
        return view('author.profile');
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);
        Flux::toast(variant: 'success', text: 'Profile updated successfully.');
        return redirect()->route('author.profile');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->update(['password' => bcrypt($validated['password'])]);
        app(SecurityService::class)->recordPasswordChange($user);

        Flux::toast(variant: 'success', text: 'Password updated successfully.');
        return redirect()->route('author.profile');
    }

    public function updateNotificationPreferences(Request $request)
    {
        auth()->user()->update([
            'notification_preferences' => json_encode($request->input('notifications', [])),
        ]);

        Flux::toast(variant: 'success', text: 'Notification preferences updated.');
        return redirect()->route('author.profile');
    }
}
