@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Terms of Service</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="flex flex-col lg:flex-row gap-12 relative">
        {{-- Sticky Sidebar Navigation --}}
        <aside class="lg:w-64 shrink-0 hidden lg:block">
            <div class="sticky top-24 space-y-1">
                <flux:heading size="sm" class="mb-4 uppercase tracking-widest text-zinc-400">On this page</flux:heading>
                <nav class="flex flex-col gap-1">
                    <a href="#agreement" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">1. Agreement to Terms</a>
                    <a href="#overview" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">2. Marketplace Overview</a>
                    <a href="#accounts" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">3. User Accounts</a>
                    <a href="#buying-selling" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">4. Buying & Selling</a>
                    <a href="#payment-refunds" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">5. Payment & Refunds</a>
                    <a href="#intellectual-property" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">6. Intellectual Property</a>
                    <a href="#prohibited" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">7. Prohibited Activities</a>
                    <a href="#liability" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">8. Limitation of Liability</a>
                </nav>
            </div>
        </aside>

        <div class="flex-1 max-w-4xl">
            <div class="mb-12">
                <flux:heading size="2xl" class="mb-4">Terms of Service</flux:heading>
                <flux:subheading>Last updated: {{ date('F d, Y') }}</flux:subheading>
            </div>

            <div class="prose prose-zinc prose-lg dark:prose-invert max-w-none space-y-16">
                {{-- Agreement to Terms --}}
                <section id="agreement" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">1. Agreement to Terms</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        By accessing and using NexaCode Marketplace ("the Platform"), you accept and agree to be bound by the terms and provisions of this agreement. If you do not agree to abide by the above, please do not use this service.
                    </p>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed mt-4">
                        These Terms of Service constitute a legally binding agreement between you and NexaCode Marketplace regarding your use of the Platform.
                    </p>
                </section>

                {{-- Marketplace Overview --}}
                <section id="overview" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">2. Marketplace Overview</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        NexaCode Marketplace is a digital marketplace platform that connects buyers and sellers of digital products, including but not limited to:
                    </p>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li>PHP Scripts and Applications</li>
                        <li>WordPress Themes and Plugins</li>
                        <li>Web Templates and UI Kits</li>
                        <li>Mobile App Templates</li>
                        <li>Graphics and Design Assets</li>
                        <li>Other digital products</li>
                    </ul>
                </section>

                {{-- User Accounts --}}
                <section id="accounts" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">3. User Accounts</flux:heading>
                    
                    <flux:heading size="md" class="mb-4 mt-8">3.1 Account Creation</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        To use certain features of the Platform, you must register for an account. You agree to:
                    </p>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li>Provide accurate, current, and complete information</li>
                        <li>Maintain and update your information to keep it accurate</li>
                        <li>Maintain the security of your password</li>
                        <li>Accept responsibility for all activities under your account</li>
                        <li>Notify us immediately of any unauthorized use</li>
                    </ul>

                    <flux:heading size="md" class="mb-4 mt-8">3.2 Account Termination</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        We reserve the right to suspend or terminate your account if you violate these Terms of Service or engage in fraudulent, illegal, or harmful activities.
                    </p>
                </section>

                {{-- Buying & Selling --}}
                <section id="buying-selling" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">4. Buying and Selling</flux:heading>
                    
                    <flux:heading size="md" class="mb-4 mt-8">4.1 For Buyers</flux:heading>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li>All purchases are final unless otherwise stated</li>
                        <li>You receive a full version with permanent access upon purchase</li>
                        <li>You are responsible for ensuring product compatibility before purchase</li>
                        <li>Downloaded products cannot be returned or refunded after download</li>
                        <li>You may not redistribute, resell, or share purchased products</li>
                    </ul>

                    <flux:heading size="md" class="mb-4 mt-8">4.2 For Sellers (Authors)</flux:heading>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li>You must own all rights to the products you sell</li>
                        <li>Products must be original work or properly licensed</li>
                        <li>You must provide accurate product descriptions</li>
                        <li>You must provide reasonable support to buyers</li>
                        <li>You agree to our commission structure (detailed in Author Agreement)</li>
                        <li>You are responsible for product quality and updates</li>
                    </ul>
                </section>

                {{-- Payment & Refunds --}}
                <section id="payment-refunds" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">5. Payment and Refunds</flux:heading>
                    
                    <flux:heading size="md" class="mb-4 mt-8">5.1 Payment Processing</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        All payments are processed securely through Midtrans, our trusted payment gateway. We accept various payment methods including credit cards, bank transfers, and e-wallets. All prices are displayed in Indonesian Rupiah (IDR).
                    </p>

                    <flux:heading size="md" class="mb-4 mt-8">5.2 Refund Policy</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Refunds may be issued in the following circumstances:
                    </p>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li>Product is significantly different from description</li>
                        <li>Product contains malicious code or security vulnerabilities</li>
                        <li>Product is non-functional and cannot be fixed</li>
                        <li>Duplicate purchase made in error</li>
                    </ul>
                </section>

                {{-- Intellectual Property --}}
                <section id="intellectual-property" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">6. Intellectual Property Rights</flux:heading>
                    
                    <flux:heading size="md" class="mb-4 mt-8">6.1 Platform Content</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        The Platform and its original content, features, and functionality are owned by NexaCode Marketplace and are protected by international copyright, trademark, and other intellectual property laws.
                    </p>

                    <flux:heading size="md" class="mb-4 mt-8">6.2 User Content</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        By uploading products to the Platform, you grant us a non-exclusive, worldwide, royalty-free license to display, distribute, and promote your products on the Platform. You retain all ownership rights to your content.
                    </p>
                </section>

                {{-- Prohibited Activities --}}
                <section id="prohibited" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">7. Prohibited Activities</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        You may not access or use the Platform for any purpose other than that for which we make it available. Prohibited activities include:
                    </p>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li>Uploading malicious code, viruses, or harmful software</li>
                        <li>Infringing on intellectual property rights of others</li>
                        <li>Engaging in fraudulent transactions or chargebacks</li>
                        <li>Harassing, threatening, or abusing other users</li>
                        <li>Attempting to bypass security measures</li>
                        <li>Scraping or data mining without permission</li>
                    </ul>
                </section>

                {{-- Limitation of Liability --}}
                <section id="liability" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">8. Limitation of Liability</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        To the maximum extent permitted by law, NexaCode Marketplace shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including loss of profits, data, or other intangible losses resulting from:
                    </p>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li>Your use or inability to use the Platform</li>
                        <li>Any unauthorized access to your account</li>
                        <li>Any bugs, viruses, or harmful code in products</li>
                        <li>Any errors or omissions in content</li>
                    </ul>
                </section>

                <hr class="border-zinc-200 dark:border-zinc-800" />

                {{-- Nexa Support Integration --}}
                <section class="not-prose bg-emerald-50/50 dark:bg-emerald-950/20 rounded-3xl p-8 border border-emerald-100 dark:border-emerald-500/20 overflow-hidden relative group">
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all duration-700"></div>
                    
                    <div class="relative z-10">
                        <flux:heading size="lg" class="mb-4 text-emerald-900 dark:text-emerald-100">11. Contact Information</flux:heading>
                        <p class="text-emerald-700/80 dark:text-emerald-400/80 leading-relaxed mb-6 max-w-2xl">
                            By using NexaCode, you agree to these terms. If you need clarification on any specific point, please create a ticket in our Nexa Support system.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4">
                            <flux:button href="{{ route('support.chat') }}" variant="primary" icon="chat-bubble-left-right" class="px-8 bg-emerald-600 border-none">Start Live Chat</flux:button>
                            <flux:button href="mailto:legal@nexacode.com" variant="ghost" icon="envelope" class="text-emerald-600 dark:text-emerald-400">
                                legal@nexacode.com
                            </flux:button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
