@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Privacy Policy</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="flex flex-col lg:flex-row gap-12 relative">
        {{-- Sticky Sidebar Navigation --}}
        <aside class="lg:w-64 shrink-0 hidden lg:block">
            <div class="sticky top-24 space-y-1">
                <flux:heading size="sm" class="mb-4 uppercase tracking-widest text-zinc-400">On this page</flux:heading>
                <nav class="flex flex-col gap-1">
                    <a href="#introduction" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">1. Introduction</a>
                    <a href="#information-collect" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">2. Information We Collect</a>
                    <a href="#how-we-use" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">3. How We Use</a>
                    <a href="#data-security" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">4. Data Security</a>
                    <a href="#cookies" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">5. Cookies & Tracking</a>
                    <a href="#third-party" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">6. Third-Party Services</a>
                    <a href="#your-rights" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">7. Your Privacy Rights</a>
                    <a href="#data-retention" class="text-sm py-2 px-3 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 transition-colors">8. Data Retention</a>
                </nav>
            </div>
        </aside>

        <div class="flex-1 max-w-4xl">
            <div class="mb-12">
                <flux:heading size="2xl" class="mb-4">Privacy Policy</flux:heading>
                <flux:subheading>Last updated: {{ date('F d, Y') }}</flux:subheading>
            </div>

            <div class="prose prose-zinc prose-lg dark:prose-invert max-w-none space-y-16">
                {{-- Introduction --}}
                <section id="introduction" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">1. Introduction</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Welcome to NexaCode Marketplace ("we," "our," or "us"). We are committed to protecting your personal information and your right to privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our marketplace platform.
                    </p>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed mt-4">
                        By using NexaCode Marketplace, you agree to the collection and use of information in accordance with this policy. If you do not agree with our policies and practices, please do not use our services.
                    </p>
                </section>

                {{-- Information We Collect --}}
                <section id="information-collect" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">2. Information We Collect</flux:heading>
                    
                    <flux:heading size="md" class="mb-4 mt-8">2.1 Personal Information</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        We collect personal information that you voluntarily provide to us when you:
                    </p>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li>Register for an account</li>
                        <li>Make a purchase or sell products</li>
                        <li>Contact our support team</li>
                        <li>Subscribe to our newsletter</li>
                        <li>Participate in surveys or promotions</li>
                    </ul>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed mt-4">
                        This information may include: name, email address, phone number, billing address, payment information, and profile picture.
                    </p>

                    <flux:heading size="md" class="mb-4 mt-8">2.2 Automatically Collected Information</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        When you visit our marketplace, we automatically collect certain information about your device, including:
                    </p>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li>IP address and browser type</li>
                        <li>Operating system and device information</li>
                        <li>Pages visited and time spent on pages</li>
                        <li>Referring website addresses</li>
                        <li>Clickstream data</li>
                    </ul>
                </section>

                {{-- How We Use Your Information --}}
                <section id="how-we-use" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">3. How We Use Your Information</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        We use the information we collect to:
                    </p>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li>Process and fulfill your orders</li>
                        <li>Manage your account and provide customer support</li>
                        <li>Send you important updates about your purchases and account</li>
                        <li>Process payments and prevent fraudulent transactions</li>
                        <li>Improve our services and user experience</li>
                        <li>Send marketing communications (with your consent)</li>
                        <li>Comply with legal obligations</li>
                        <li>Enforce our Terms of Service</li>
                    </ul>
                </section>

                {{-- Data Security --}}
                <section id="data-security" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">4. Data Security</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. These measures include:
                    </p>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li>SSL/TLS encryption for data transmission</li>
                        <li>Secure payment processing through trusted payment gateways (Midtrans)</li>
                        <li>Regular security audits and updates</li>
                        <li>Access controls and authentication mechanisms</li>
                        <li>Data backup and recovery procedures</li>
                    </ul>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed mt-4">
                        However, no method of transmission over the Internet or electronic storage is 100% secure. While we strive to protect your personal information, we cannot guarantee its absolute security.
                    </p>
                </section>

                {{-- Cookies & Tracking --}}
                <section id="cookies" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">5. Cookies and Tracking Technologies</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        We use cookies and similar tracking technologies to track activity on our marketplace and store certain information. Cookies are files with a small amount of data that are sent to your browser from a website and stored on your device.
                    </p>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed mt-4">
                        You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our service.
                    </p>
                </section>

                {{-- Third-Party Services --}}
                <section id="third-party" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">6. Third-Party Services</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        We may share your information with third-party service providers who perform services on our behalf, including:
                    </p>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li><strong>Payment Processing:</strong> Midtrans for secure payment transactions</li>
                        <li><strong>Email Services:</strong> For sending transactional and marketing emails</li>
                        <li><strong>Analytics:</strong> To understand how users interact with our platform</li>
                        <li><strong>Cloud Storage:</strong> For storing digital products and user data</li>
                    </ul>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed mt-4">
                        These third parties have access to your personal information only to perform these tasks on our behalf and are obligated not to disclose or use it for any other purpose.
                    </p>
                </section>

                {{-- Your Rights --}}
                <section id="your-rights" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">7. Your Privacy Rights</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        You have the following rights regarding your personal information:
                    </p>
                    <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-3 mt-4">
                        <li><strong>Access:</strong> Request a copy of the personal information we hold about you</li>
                        <li><strong>Correction:</strong> Request correction of inaccurate or incomplete information</li>
                        <li><strong>Deletion:</strong> Request deletion of your personal information</li>
                        <li><strong>Objection:</strong> Object to our processing of your personal information</li>
                        <li><strong>Data Portability:</strong> Request transfer of your data to another service</li>
                        <li><strong>Withdraw Consent:</strong> Withdraw your consent at any time</li>
                    </ul>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed mt-4">
                        To exercise these rights, please contact us at <a href="mailto:privacy@nexacode.com" class="text-emerald-500 hover:text-emerald-600 border-b border-emerald-500/30">privacy@nexacode.com</a>
                    </p>
                </section>

                {{-- Data Retention --}}
                <section id="data-retention" class="scroll-mt-24">
                    <flux:heading size="lg" class="mb-6">8. Data Retention</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        We retain your personal information only for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law. When we no longer need your information, we will securely delete or anonymize it.
                    </p>
                </section>

                <hr class="border-zinc-200 dark:border-zinc-800" />

                {{-- Nexa Support Integration --}}
                <section class="not-prose bg-emerald-50/50 dark:bg-emerald-950/20 rounded-3xl p-8 border border-emerald-100 dark:border-emerald-500/20 overflow-hidden relative group">
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all duration-700"></div>
                    
                    <div class="relative z-10">
                        <flux:heading size="lg" class="mb-4 text-emerald-900 dark:text-emerald-100">11. Still have questions?</flux:heading>
                        <p class="text-emerald-700/80 dark:text-emerald-400/80 leading-relaxed mb-6 max-w-2xl">
                            If you have any questions or concerns regarding this Privacy Policy, our dedicated Nexa Support team is here to help you 24/7.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4">
                            <flux:button href="{{ route('support.chat') }}" variant="primary" icon="chat-bubble-left-right" class="px-8 bg-emerald-600 border-none">Start Live Chat</flux:button>
                            <flux:button href="mailto:privacy@nexacode.com" variant="ghost" icon="envelope" class="text-emerald-600 dark:text-emerald-400">
                                privacy@nexacode.com
                            </flux:button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
