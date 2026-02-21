<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CaptchaController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Admin\AuthorApproval;
use App\Livewire\Auth\VerifyOtp;
use App\Livewire\Dashboard\AuthorRequest;
use App\Http\Controllers\AuthorStorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/authors/ranking', \App\Livewire\Page\AuthorRanking::class)->name('page.author-ranking');

// Legal & Support Pages
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms-of-service', [PageController::class, 'terms'])->name('terms');

// Modern Help Center (Hierarchical)
Route::get('/help', \App\Livewire\Help\HelpIndex::class)->name('help.index');
Route::get('/help/{slug}', \App\Livewire\Help\CategoryDetail::class)->name('help.category');
Route::get('/help/{categorySlug}/{articleSlug}', \App\Livewire\Help\ArticleDetail::class)->name('help.article');

Route::get('/faq', [PageController::class, 'faq'])->name('faq'); // Deprecated, redirects to /help maybe later

// Captcha image endpoint — accessible to guests, rate-limited to 30 req/min
Route::get('/captcha/image', [CaptchaController::class, 'generate'])
    ->name('captcha.image')
    ->middleware('throttle:30,1');

Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/register', Register::class)->name('register')->middleware(['guest', 'throttle:10,1']);
Route::get('/forgot-password', \App\Livewire\Auth\ForgotPassword::class)->name('password.request')->middleware('guest');
Route::get('/reset-password/{token}', \App\Livewire\Auth\ResetPassword::class)->name('password.reset')->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/onboarding', \App\Livewire\Auth\Onboarding::class)->name('onboarding');
});

Route::get('/verify-otp', VerifyOtp::class)->name('verification.notice')->middleware('auth');

// Social Login — provider dibatasi hanya google dan github (provider lain → 404)
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('social.redirect')
    ->whereIn('provider', ['google', 'github']);
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('social.callback')
    ->whereIn('provider', ['google', 'github']);

// ── 2FA Routes ───────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    // Verify page (diperlukan saat login jika 2FA aktif)
    Route::get('/two-factor/verify', [\App\Http\Controllers\Auth\TwoFactorController::class, 'showVerify'])
        ->name('two-factor.verify');
    Route::post('/two-factor/verify', [\App\Http\Controllers\Auth\TwoFactorController::class, 'verify'])
        ->name('two-factor.verify.submit')
        ->middleware('throttle:10,1'); // max 10 percobaan per menit

    // Setup & Enable (dari halaman profile/settings)
    Route::get('/two-factor/setup', [\App\Http\Controllers\Auth\TwoFactorController::class, 'setup'])
        ->name('two-factor.setup');
    Route::post('/two-factor/enable', [\App\Http\Controllers\Auth\TwoFactorController::class, 'enable'])
        ->name('two-factor.enable');

    // Disable
    Route::post('/two-factor/disable', [\App\Http\Controllers\Auth\TwoFactorController::class, 'disable'])
        ->name('two-factor.disable');

    // Regenerate backup codes
    Route::post('/two-factor/backup-codes/regenerate', [\App\Http\Controllers\Auth\TwoFactorController::class, 'regenerateBackupCodes'])
        ->name('two-factor.backup-codes.regenerate');

    // ── Email Change Confirmation ────────────────────────────────────────
    Route::get('/email/change/confirm/{token}', [\App\Http\Controllers\Auth\EmailChangeController::class, 'confirm'])
        ->name('email.change.confirm');

    // ── Login History ────────────────────────────────────────────────────
    Route::get('/account/login-history', \App\Livewire\Account\LoginHistory::class)
        ->name('account.login-history');
});

Route::get('/authors/{identifier}', [AuthorStorefrontController::class, 'show'])->name('authors.show');

Route::get('/items', [ProductController::class, 'index'])->name('products.index');
Route::get('/items/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Product & Checkout Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', \App\Livewire\Checkout\CartCheckout::class)->name('checkout.index');
    Route::get('/checkout/{product:slug}', [CheckoutController::class, 'checkout'])->name('checkout.show');
    Route::post('/checkout/{product:slug}', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/payment/{order}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::get('/orders/{order}/invoice', [CheckoutController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::post('/payment/{order}/confirm', [CheckoutController::class, 'confirm'])->name('payment.confirm');
    Route::post('/payment/{order}/upload-proof', [CheckoutController::class, 'uploadPaymentProof'])->name('payment.upload-proof')->middleware('throttle:5,1');
    Route::post('/payment/{order}/cancel', [CheckoutController::class, 'cancel'])->name('payment.cancel');
    
    // Midtrans Checkout Routes (POST only — GET /checkout is handled by CartCheckout above)
    Route::post('/checkout', [CheckoutController::class, 'midtransProcess'])->name('checkout.midtrans.process');
    Route::get('/checkout/success', [CheckoutController::class, 'paymentSuccess'])->name('checkout.success');
    Route::get('/checkout/pending', [CheckoutController::class, 'paymentPending'])->name('checkout.pending');
    Route::get('/checkout/failed', [CheckoutController::class, 'paymentFailed'])->name('checkout.failed');
    
    Route::post('/items/{product:slug}/buy', [CheckoutController::class, 'buy'])->name('products.buy');
    Route::post('/items/{product:slug}/review', [ReviewController::class, 'store'])->name('products.review')->middleware('throttle:5,1');
    Route::get('/items/{product:slug}/download', [DownloadController::class, 'download'])->name('products.download');
});

// Midtrans Webhook (no auth required)
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])->name('midtrans.webhook');

Route::get('/categories/{category:slug}', [ProductController::class, 'category'])->name('categories.show');

// Customer Dashboard Routes
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [DashboardController::class, 'orders'])->name('dashboard.orders');
    Route::get('/wishlist', [DashboardController::class, 'wishlist'])->name('dashboard.wishlist');
    Route::get('/become-author', AuthorRequest::class)->name('become-author');
});

// Root Level Buyer Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/messages', \App\Livewire\Customer\BuyerChatManager::class)->name('inbox');
});

// Unified Profile Routes (All Roles)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', \App\Livewire\Global\NotificationCenter::class)->name('notifications');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/notifications', [ProfileController::class, 'updateNotificationPreferences'])->name('profile.notifications');
    
    // Two-Factor Authentication Routes
    Route::get('/two-factor/setup', [\App\Http\Controllers\Auth\TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/two-factor/enable', [\App\Http\Controllers\Auth\TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/disable', [\App\Http\Controllers\Auth\TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::post('/two-factor/backup-codes', [\App\Http\Controllers\Auth\TwoFactorController::class, 'regenerateBackupCodes'])->name('two-factor.backup-codes');
    
    // Security Dashboard Routes
    Route::get('/security', [\App\Http\Controllers\SecurityDashboardController::class, 'index'])->name('security.dashboard');
    Route::get('/security/logs', [\App\Http\Controllers\SecurityDashboardController::class, 'logs'])->name('security.logs');
    Route::get('/security/logs/export', [\App\Http\Controllers\SecurityDashboardController::class, 'exportLogs'])->name('security.logs.export');
});

// Buyer Routes (integrated with homepage)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/purchases', function () { return view('purchases.index'); })->name('purchases.index');
    Route::get('/orders', function () { return redirect()->route('purchases.index'); }); // Redirect legacy
    Route::get('/downloads', function () { return view('purchases.downloads'); })->name('downloads.index');
    Route::get('/wishlist', function () { return view('purchases.wishlist'); })->name('wishlist.index');
});

// Support & Refund Routes (for all authenticated users)
Route::middleware(['auth', 'verified'])->group(function () {
    // Support System
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/support/create', [SupportController::class, 'create'])->name('support.create');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [SupportController::class, 'show'])->name('support.show');
    Route::get('/support-chat', [SupportController::class, 'chat'])->name('support.chat');

    // Refund System
    Route::get('/orders/{order}/refund', [RefundController::class, 'create'])->name('refunds.create');
    Route::post('/orders/{order}/refund', [RefundController::class, 'store'])->name('refunds.store')->middleware('throttle:5,1');

    // Affiliate Hub
    Route::get('/affiliate', \App\Livewire\Affiliate\AffiliateDashboard::class)->name('affiliate.dashboard');
});

// Author Dashboard
Route::middleware(['auth', 'verified'])->prefix('author')->group(function () {
    Route::get('/', [AuthorController::class, 'index'])->name('author.dashboard');
    Route::get('/items', function () { return view('author.products'); })->name('author.products');
    Route::get('/items/{product}/versions/create', [AuthorController::class, 'createVersion'])->name('author.versions.create');
    Route::post('/items/{product}/versions', [AuthorController::class, 'storeVersion'])->name('author.versions.store');

    Route::get('/support', function () { return view('author.support.index'); })->name('author.support');
    Route::get('/refunds', function () { return view('author.refunds.index'); })->name('author.refunds');
    Route::get('/earnings', function () { return view('author.earnings'); })->name('author.earnings');
    Route::get('/withdrawals', function () { return view('author.earnings'); })->name('author.withdrawals');
    Route::get('/sales', function () { return view('author.earnings'); })->name('author.sales');
    Route::get('/settings', function () { return redirect()->route('author.profile'); })->name('author.settings');
    Route::get('/reviews', function () { return view('author.reviews'); })->name('author.reviews');
    Route::post('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('author.reviews.reply')->middleware('throttle:5,1');
    Route::get('/coupons', \App\Livewire\Author\CouponManager::class)->name('author.coupons');
    Route::get('/bundles', \App\Livewire\Author\BundleManager::class)->name('author.bundles');

    // Author Products (used in email links)
    Route::get('/items/create', function () { return view('author.products'); })->name('author.products.create');
    Route::get('/items/{product}', function ($product) { return view('author.products'); })->name('author.products.show');
    Route::get('/items/{product}/edit', function ($product) { return view('author.products'); })->name('author.products.edit');

    Route::get('/chat', \App\Livewire\Author\AuthorChatManager::class)->name('author.chat');
    Route::get('/plans', \App\Livewire\Author\PlanSelection::class)->name('author.plans');
    Route::get('/insights', \App\Livewire\Author\AnalyticsDashboard::class)->name('author.insights');
    Route::get('/register', \App\Livewire\Dashboard\AuthorRequest::class)->name('author.register');

    // Author Profile
    Route::get('/profile', [AuthorController::class, 'profile'])->name('author.profile');
    Route::put('/profile', [AuthorController::class, 'updateProfile'])->name('author.profile.update');
    Route::put('/profile/password', [AuthorController::class, 'updatePassword'])->name('author.profile.password');
    Route::put('/profile/notifications', [AuthorController::class, 'updateNotificationPreferences'])->name('author.profile.notifications');
});

// Admin Moderation
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/moderation', function () { return view('admin.moderation'); })->name('admin.moderation');
    Route::get('/moderation/{product}', function () { return view('admin.moderation'); })->name('admin.products.moderate');
    Route::get('/author-requests', function () {
        return view('admin.author-requests');
    })->name('admin.author-requests');
    Route::get('/users', function () { return view('admin.users'); })->name('admin.users');
    Route::get('/orders', function () { return view('admin.orders'); })->name('admin.orders');
    Route::get('/categories', \App\Livewire\Admin\CategoryManager::class)->name('admin.categories');
    Route::get('/products', function () { return view('admin.products'); })->name('admin.products');
    Route::get('/payouts', function () { return view('admin.payouts'); })->name('admin.payouts');
    Route::get('/buyer-reports', \App\Livewire\Admin\BuyerReportsManager::class)->name('admin.buyer-reports');
    Route::get('/refunds', \App\Livewire\Admin\RefundManager::class)->name('admin.refunds');
    Route::get('/subscriptions', \App\Livewire\Admin\SubscriptionManager::class)->name('admin.subscriptions');
    Route::get('/settings', function () { return view('admin.settings'); })->name('admin.settings');
    Route::get('/mail-manager', \App\Livewire\Admin\MailManager::class)->name('admin.mail-manager');
    Route::get('/flash-sales', \App\Livewire\Admin\FlashSaleManager::class)->name('admin.flash-sales');
    
    // Profile Management
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/profile/password', [AdminController::class, 'updatePassword'])->name('admin.profile.password');
    Route::put('/profile/notifications', [AdminController::class, 'updateNotificationPreferences'])->name('admin.profile.notifications');
    
    // Moderation actions are now handled via Livewire in ModerationManager
    
    Route::get('/payment-methods', function () { return view('admin.payment-methods.index'); })->name('admin.payment-methods.index');
    
    // Payment and Order Verification are now handled via Livewire modals
    
    Route::get('/analytics', \App\Livewire\Admin\Analytics\Dashboard::class)->name('admin.analytics');
    Route::get('/analytics/export', [App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('admin.analytics.export');
    
    Route::get('/chat', \App\Livewire\Admin\AdminChatManager::class)->name('admin.chat');

    // Help Center CMS
    Route::get('/help/categories', function () { return view('admin.help.categories'); })->name('admin.help.categories');
    Route::get('/help/articles', function () { return view('admin.help.articles'); })->name('admin.help.articles');
    Route::get('/help/articles/create', \App\Livewire\Admin\Help\ArticleEditor::class)->name('admin.help.articles.create');
    Route::get('/help/articles/{article}/edit', \App\Livewire\Admin\Help\ArticleEditor::class)->name('admin.help.articles.edit');

    // Affiliate Management
    Route::get('/affiliate/payouts', \App\Livewire\Admin\Affiliate\AffiliatePayoutManager::class)->name('admin.affiliate.payouts');
    Route::get('/affiliate/coupons', \App\Livewire\Admin\Affiliate\AffiliateCouponManager::class)->name('admin.affiliate.coupons');
});


