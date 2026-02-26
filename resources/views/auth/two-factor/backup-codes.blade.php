@extends('layouts.auth')

@section('title', '2FA Backup Codes')

@section('side-testimonial')
    "Think of backup codes as your master keys. Store them offline, keep them private, and they will never fail you."
@endsection

@section('side-author', 'Ahmad Dani Saputra')
@section('side-role', 'Creator of NEXACODE')

@section('content')
    <div class="w-full max-w-lg space-y-8">
        <div class="text-center">
            <flux:heading size="xl">✅ Security Active</flux:heading>
            <flux:subheading class="mt-2 text-emerald-600 dark:text-emerald-400 font-bold">
                2FA has been successfully enabled.
            </flux:subheading>
        </div>

        <flux:card class="p-8">
            <div class="space-y-6">
                <div class="p-5 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-200 dark:border-amber-800">
                    <div class="flex gap-4">
                        <flux:icon name="exclamation-triangle" variant="solid" class="size-6 text-amber-600 dark:text-amber-500 shrink-0" />
                        <div>
                            <flux:heading size="sm" class="text-amber-900 dark:text-amber-100 font-bold">
                                Crucial: Save Your Backup Codes
                            </flux:heading>
                            <p class="mt-1 text-xs text-amber-800/80 dark:text-amber-200/80 leading-relaxed font-medium">
                                These codes are your ONLY way to access your account if you lose your authenticator app. Store them in a password manager or print them.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                    @foreach($backupCodes as $code)
                        <div class="font-mono text-sm tracking-widest text-center p-3 bg-white dark:bg-zinc-950 rounded-xl border border-zinc-200 dark:border-zinc-800 font-bold shadow-sm">
                            {{ $code }}
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-2">
                    <flux:button onclick="downloadBackupCodes()" variant="outline" size="sm" class="flex-1 font-bold">
                        <flux:icon name="arrow-down-tray" variant="micro" class="mr-1.5" />
                        Download
                    </flux:button>

                    <flux:button onclick="printBackupCodes()" variant="outline" size="sm" class="flex-1 font-bold">
                        <flux:icon name="printer" variant="micro" class="mr-1.5" />
                        Print
                    </flux:button>

                    <flux:button onclick="copyBackupCodes()" variant="outline" size="sm" class="flex-1 font-bold">
                        <flux:icon name="document-duplicate" variant="micro" class="mr-1.5" />
                        Copy
                    </flux:button>
                </div>

                <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button href="{{ auth()->user()->isAdmin() ? route('admin.profile') : (auth()->user()->isAuthor() ? route('author.profile') : route('profile')) }}" variant="primary" class="w-full font-black h-11 shadow-lg shadow-indigo-500/20">
                        Securely Finish Setup
                    </flux:button>
                </div>
            </div>
        </flux:card>
    </div>

    <script>
    const backupCodes = @json($backupCodes);

    function downloadBackupCodes() {
        const content = `NEXACODE - Backup Codes\nGenerated: ${new Date().toLocaleString()}\n\n${backupCodes.join('\n')}\n\nKeep these codes in a secure location. Each code can only be used once.`;
        const blob = new Blob([content], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'nexacode-backup-codes.txt';
        a.click();
        URL.revokeObjectURL(url);
    }

    function printBackupCodes() {
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Backup Codes</title>');
        printWindow.document.write('<style>body{font-family:monospace;padding:40px;text-align:center;} .grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:40px;} .code{padding:15px;border:1px solid #ddd;border-radius:8px;font-size:1.2rem;font-weight:bold;}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h1>NEXACODE - 2FA Backup Codes</h1>');
        printWindow.document.write('<p>Generated: ' + new Date().toLocaleString() + '</p>');
        printWindow.document.write('<div class="grid">');
        backupCodes.forEach(code => {
            printWindow.document.write('<div class="code">' + code + '</div>');
        });
        printWindow.document.write('</div>');
        printWindow.document.write('<p style="margin-top:40px;color:#666;">Each code can only be used once. Treat these as your master keys.</p>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }

    function copyBackupCodes() {
        const text = backupCodes.join('\n');
        navigator.clipboard.writeText(text).then(() => {
            window.Flux.toast({ heading: 'Success', text: 'Backup codes copied to clipboard!' });
        });
    }
    </script>
@endsection
