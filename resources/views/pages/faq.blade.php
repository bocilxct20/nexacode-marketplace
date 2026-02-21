@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/" separator="slash">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">FAQ</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="mb-12 text-center">
        <flux:heading size="2xl" class="mb-4">Frequently Asked Questions</flux:heading>
        <flux:subheading>Find answers to common questions about NexaCode Marketplace</flux:subheading>
    </div>

    <div class="space-y-12">
        {{-- General Questions --}}
        <section>
            <flux:heading size="lg" class="mb-6">General Questions</flux:heading>
            
            <flux:accordion transition exclusive>
                <flux:accordion.item heading="What is NexaCode Marketplace?">
                    NexaCode Marketplace is a digital marketplace platform where creators can sell and buyers can purchase high-quality digital products including PHP scripts, WordPress themes, web templates, UI kits, and other digital assets. We connect talented developers and designers with customers worldwide.
                </flux:accordion.item>

                <flux:accordion.item heading="How do I create an account?">
                    Creating an account is simple! Click the "Register" button in the top right corner, fill in your details (name, email, password), and verify your email address. You can also sign up using your Google account for faster registration.
                </flux:accordion.item>

                <flux:accordion.item heading="Is NexaCode Marketplace free to use?">
                    Yes, creating an account and browsing products is completely free. Buyers only pay for the products they purchase. Sellers (authors) pay a commission on each sale, which helps us maintain and improve the platform.
                </flux:accordion.item>

                <flux:accordion.item heading="What payment methods do you accept?">
                    We accept various payment methods through our secure payment gateway, Midtrans, including:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Credit and Debit Cards (Visa, Mastercard, JCB)</li>
                        <li>Bank Transfers (BCA, Mandiri, BNI, BRI, Permata)</li>
                        <li>E-Wallets (GoPay, OVO, DANA, ShopeePay)</li>
                        <li>Convenience Stores (Indomaret, Alfamart)</li>
                    </ul>
                    All transactions are processed in Indonesian Rupiah (IDR).
                </flux:accordion.item>

                <flux:accordion.item heading="Is my payment information secure?">
                    Absolutely! We use Midtrans, a PCI-DSS certified payment gateway, to process all transactions. We never store your credit card information on our servers. All payment data is encrypted and transmitted securely using SSL/TLS protocols.
                </flux:accordion.item>
            </flux:accordion>
        </section>

        {{-- Buying Products --}}
        <section>
            <flux:heading size="lg" class="mb-6">Buying Products</flux:heading>
            
            <flux:accordion transition exclusive>
                <flux:accordion.item heading="How do I purchase a product?">
                    To purchase a product:
                    <ol class="list-decimal list-inside mt-2 space-y-1">
                        <li>Browse or search for the product you want</li>
                        <li>Click on the product to view details</li>
                        <li>Click "Buy Now" or "Add to Cart"</li>
                        <li>Complete the checkout process</li>
                        <li>Choose your payment method and complete payment</li>
                        <li>Download your product from your purchases page</li>
                    </ol>
                </flux:accordion.item>

                <flux:accordion.item heading="What do I get when I purchase a product?">
                    When you purchase a product, you receive:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Full version with permanent access</li>
                        <li>All source files and documentation</li>
                        <li>Free updates (if provided by the author)</li>
                        <li>Access to download the product anytime from your account</li>
                        <li>Author support (as specified in product description)</li>
                    </ul>
                </flux:accordion.item>

                <flux:accordion.item heading="Bagaimana kebijakan Refund di NexaCode?">
                    Refund dapat diajukan dalam kondisi tertentu:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Produk tidak berjalan sesuai deskripsi teknis (error fatal).</li>
                        <li>Author tidak memberikan respon support dalam 3 hari kerja.</li>
                        <li>Terdapat celah keamanan atau kode berbahaya pada produk.</li>
                    </ul>
                    **Penting**: Sesuai kebijakan kami, Author memiliki waktu **14 hari** untuk memperbaiki laporan kerusakan sebelum refund diproses sepenuhnya.
                </flux:accordion.item>

                <flux:accordion.item heading="How do I download my purchased products?">
                    After completing your purchase:
                    <ol class="list-decimal list-inside mt-2 space-y-1">
                        <li>Go to your account dashboard</li>
                        <li>Click on "My Purchases"</li>
                        <li>Find your product and click "Download"</li>
                        <li>The latest version will be downloaded automatically</li>
                    </ol>
                    You can re-download your products anytime from your purchases page.
                </flux:accordion.item>

                <flux:accordion.item heading="Apakah akses saya akan kedaluwarsa?">
                    Tidak! Semua produk di NexaCode Marketplace memberikan akses permanen. Tidak ada biaya langganan atau perpanjangan. Sekali beli, milik kamu selamanya.
                </flux:accordion.item>

                <flux:accordion.item heading="Can I use purchased products for client projects?">
                    Ya! Produk yang sudah dibeli dapat digunakan untuk proyek klien. Namun, kamu tidak diperbolehkan menjual kembali atau mendistribusikan ulang produk tersebut sebagai aset mentah.
                </flux:accordion.item>
            </flux:accordion>
        </section>

        {{-- Selling Products --}}
        <section>
            <flux:heading size="lg" class="mb-6">Selling Products</flux:heading>
            
            <flux:accordion transition exclusive>
                <flux:accordion.item heading="How do I become a seller (author)?">
                    To become a seller:
                    <ol class="list-decimal list-inside mt-2 space-y-1">
                        <li>Create an account or log in</li>
                        <li>Go to your dashboard and click "Become an Author"</li>
                        <li>Fill out the author application form</li>
                        <li>Wait for approval (usually within 24-48 hours)</li>
                        <li>Once approved, you can start uploading products</li>
                    </ol>
                </flux:accordion.item>

                <flux:accordion.item heading="Berapa komisi yang didapatkan Author?">
                    NexaCode menawarkan sistem bagi hasil yang sangat kompetitif dan transparan:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>**Basic**: Penjual mendapatkan **80%** dari harga jual.</li>
                        <li>**Pro Author**: Penjual mendapatkan **85%** dari harga jual.</li>
                        <li>**Elite Author**: Penjual mendapatkan **90%** dari harga jual.</li>
                    </ul>
                    Tidak ada biaya tersembunyi. Biaya pemrosesan pembayaran sudah termasuk dalam komisi platform.
                </flux:accordion.item>

                <flux:accordion.item heading="How do I upload a product?">
                    Once you're an approved author:
                    <ol class="list-decimal list-inside mt-2 space-y-1">
                        <li>Go to your Author Dashboard</li>
                        <li>Click "My Products" then "Add New Product"</li>
                        <li>Fill in product details (name, description, category, tags)</li>
                        <li>Upload product files and screenshots</li>
                        <li>Set your pricing</li>
                        <li>Submit for review</li>
                    </ol>
                    Our team will review your product to ensure it meets quality standards.
                </flux:accordion.item>

                <flux:accordion.item heading="Kapan dan bagaimana saya menerima pembayaran?">
                    Kamu bisa menarik saldo hasil penjualan (payout) kapan saja setelah melewati **Masa Tunggu 24 Jam** (keamanan transaksi):
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>**Minimum Payout**: Rp 10.000.</li>
                        <li>**Waktu Proses**: 1-3 hari kerja ke rekening bank lokal atau e-wallet.</li>
                        <li>**Metode**: Transfer Bank (Grup Himbara, BCA, dll).</li>
                    </ul>
                </flux:accordion.item>

                <flux:accordion.item heading="What products can I sell?">
                    You can sell any original digital product you've created, including:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>PHP Scripts and Web Applications</li>
                        <li>WordPress Themes and Plugins</li>
                        <li>HTML/CSS Templates</li>
                        <li>UI Kits and Design Systems</li>
                        <li>Mobile App Templates</li>
                        <li>Graphics and Design Assets</li>
                    </ul>
                    Products must be your original work or properly licensed, and must not contain malicious code.
                </flux:accordion.item>

                <flux:accordion.item heading="Do I need to provide support to buyers?">
                    Yes, authors are expected to provide reasonable support to buyers. This includes:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Answering questions about product usage</li>
                        <li>Fixing bugs and issues</li>
                        <li>Providing documentation</li>
                        <li>Responding to support requests in a timely manner</li>
                    </ul>
                    Good support leads to better reviews and more sales!
                </flux:accordion.item>
            </flux:accordion>
        </section>

        {{-- Payments & Earnings --}}
        <section>
            <flux:heading size="lg" class="mb-6">Payments & Earnings</flux:heading>
            
            <flux:accordion transition exclusive>
                <flux:accordion.item heading="How long does payment processing take?">
                    Payment processing time depends on your chosen payment method:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Credit/Debit Cards: Instant</li>
                        <li>E-Wallets: Instant</li>
                        <li>Bank Transfer: 1-3 business days</li>
                        <li>Convenience Store: After payment confirmation</li>
                    </ul>
                    Once payment is confirmed, you can immediately download your product.
                </flux:accordion.item>

                <flux:accordion.item heading="What happens if my payment fails?">
                    If your payment fails:
                    <ol class="list-decimal list-inside mt-2 space-y-1">
                        <li>Check your payment details and try again</li>
                        <li>Ensure you have sufficient funds</li>
                        <li>Try a different payment method</li>
                        <li>Contact your bank if the issue persists</li>
                        <li>Contact our support team for assistance</li>
                    </ol>
                    Your order will remain pending for 24 hours before being automatically cancelled.
                </flux:accordion.item>

                <flux:accordion.item heading="Can I track my earnings as an author?">
                    Yes! Your Author Dashboard provides real-time earnings tracking, including:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Total earnings</li>
                        <li>Monthly earnings growth</li>
                        <li>Sales count and revenue per product</li>
                        <li>Detailed sales history</li>
                        <li>Withdrawal history</li>
                    </ul>
                </flux:accordion.item>

                <flux:accordion.item heading="Are there any transaction fees?">
                    For buyers, the price you see is the price you pay - no hidden fees. For sellers, we deduct our commission from each sale. Payment processing fees are included in our commission, so there are no additional charges.
                </flux:accordion.item>
            </flux:accordion>
        </section>

        {{-- Downloads & Support --}}
        <section>
            <flux:heading size="lg" class="mb-6">Downloads & Support</flux:heading>
            
            <flux:accordion transition exclusive>
                <flux:accordion.item heading="How many times can I download a product?">
                    Unlimited! Once you purchase a product, you can download it as many times as you need from your "My Purchases" page. This includes re-downloading after updates.
                </flux:accordion.item>

                <flux:accordion.item heading="Do I get free updates?">
                    Yes! When an author releases an update for a product you've purchased, you can download the latest version for free from your purchases page. We recommend checking for updates regularly to get new features and bug fixes.
                </flux:accordion.item>

                <flux:accordion.item heading="What if I have issues with a product?">
                    If you encounter issues:
                    <ol class="list-decimal list-inside mt-2 space-y-1">
                        <li>Check the product documentation first</li>
                        <li>Contact the author through the product page</li>
                        <li>If the author doesn't respond within 48 hours, contact our support team</li>
                        <li>We'll mediate between you and the author to resolve the issue</li>
                    </ol>
                </flux:accordion.item>

                <flux:accordion.item heading="How do I contact support?">
                    You can contact our support team through:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Email: support@nexacode.com</li>
                        <li>Support ticket system in your dashboard</li>
                        <li>Live chat (available during business hours)</li>
                    </ul>
                    We typically respond within 24 hours on business days.
                </flux:accordion.item>

                <flux:accordion.item heading="Can I request a custom product?">
                    While we don't offer custom development services directly, you can:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Contact individual authors to discuss custom work</li>
                        <li>Post a request in our community forum</li>
                        <li>Check if similar products exist that can be customized</li>
                    </ul>
                </flux:accordion.item>
            </flux:accordion>
        </section>

        {{-- Account & Security --}}
        <section>
            <flux:heading size="lg" class="mb-6">Account & Security</flux:heading>
            
            <flux:accordion transition exclusive>
                <flux:accordion.item heading="How do I reset my password?">
                    To reset your password:
                    <ol class="list-decimal list-inside mt-2 space-y-1">
                        <li>Click "Forgot Password" on the login page</li>
                        <li>Enter your email address</li>
                        <li>Check your email for a reset link</li>
                        <li>Click the link and create a new password</li>
                        <li>Log in with your new password</li>
                    </ol>
                </flux:accordion.item>

                <flux:accordion.item heading="Is two-factor authentication available?">
                    Yes! We offer two-factor authentication (2FA) for enhanced security. You can enable 2FA in your account settings. We support:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Email-based OTP (One-Time Password)</li>
                        <li>Authenticator apps (Google Authenticator, Authy)</li>
                    </ul>
                    We strongly recommend enabling 2FA to protect your account.
                </flux:accordion.item>

                <flux:accordion.item heading="Can I delete my account?">
                    Yes, you can request account deletion by contacting our support team. Please note:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Account deletion is permanent and cannot be undone</li>
                        <li>You will lose access to all purchased products</li>
                        <li>Authors must withdraw all earnings before deletion</li>
                        <li>Active products will be removed from the marketplace</li>
                    </ul>
                </flux:accordion.item>

                <flux:accordion.item heading="How do I update my profile information?">
                    To update your profile:
                    <ol class="list-decimal list-inside mt-2 space-y-1">
                        <li>Log in to your account</li>
                        <li>Click on your profile picture or name</li>
                        <li>Select "Profile Settings"</li>
                        <li>Update your information</li>
                        <li>Click "Save Changes"</li>
                    </ol>
                </flux:accordion.item>

                <flux:accordion.item heading="What should I do if my account is compromised?">
                    If you suspect unauthorized access:
                    <ol class="list-decimal list-inside mt-2 space-y-1">
                        <li>Change your password immediately</li>
                        <li>Enable two-factor authentication</li>
                        <li>Review your recent activity and purchases</li>
                        <li>Contact our support team immediately</li>
                        <li>Check for any unauthorized transactions</li>
                    </ol>
                    We take security seriously and will help you secure your account.
                </flux:accordion.item>
            </flux:accordion>
        </section>

        {{-- Still have questions? --}}
        <section class="mt-20">
            <div class="relative overflow-hidden bg-zinc-900 dark:bg-zinc-900 rounded-3xl p-12 text-center shadow-2xl">
                {{-- Decorative Background Elements --}}
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/20 blur-[100px] rounded-full"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-500/20 blur-[100px] rounded-full"></div>

                <div class="relative z-10">
                    <flux:heading size="xl" class="text-white mb-4">Still have questions?</flux:heading>
                    <p class="text-zinc-400 max-w-xl mx-auto mb-8 text-lg">
                        Our dedicated support team is available 24/7 to help you with any technical issues or marketplace inquiries.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <flux:button href="{{ route('support.chat') }}" variant="primary" icon="chat-bubble-left-right" class="px-8 bg-emerald-600 border-none">
                            Start Live Chat
                        </flux:button>
                        <flux:button variant="ghost" icon="envelope" href="mailto:support@nexacode.com" class="px-8 text-white border-white/20 hover:bg-white/10">
                            Email Support
                        </flux:button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
