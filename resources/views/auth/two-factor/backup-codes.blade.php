@php
    $layout = 'layouts.app';
    $profileRoute = 'profile';
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            $layout = 'layouts.admin';
            $profileRoute = 'admin.profile';
        } elseif (auth()->user()->isAuthor()) {
            $layout = 'layouts.author';
            $profileRoute = 'author.profile';
        }
    }
@endphp

@extends($layout)

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        <div>
            <flux:heading size="xl" class="text-center">✅ 2FA Enabled Successfully!</flux:heading>
            <flux:subheading class="text-center mt-2">
                Save your backup codes in a secure location
            </flux:subheading>
        </div>

        <flux:card class="p-8">
            <div class="space-y-6">
                <div class="p-6 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border-2 border-yellow-200 dark:border-yellow-800">
                    <div class="flex gap-3">
                        <div class="text-yellow-600 dark:text-yellow-400 text-2xl">⚠️</div>
                        <div>
                            <flux:heading size="lg" class="text-yellow-900 dark:text-yellow-100">
                                Important: Save These Codes
                            </flux:heading>
                            <p class="mt-2 text-sm text-yellow-800 dark:text-yellow-200">
                                Each backup code can only be used once. Store them in a secure location like a password manager.
                                You'll need them if you lose access to your authenticator app.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 p-6 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                    @foreach($backupCodes as $code)
                        <div class="font-mono text-lg text-center p-3 bg-white dark:bg-zinc-900 rounded border border-zinc-300 dark:border-zinc-700">
                            {{ $code }}
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-3">
                    <flux:button
                        onclick="downloadBackupCodes()"
                        variant="outline"
                        class="flex-1"
                    >
                        📥 Download
                    </flux:button>

                    <flux:button
                        onclick="printBackupCodes()"
                        variant="outline"
                        class="flex-1"
                    >
                        🖨️ Print
                    </flux:button>

                    <flux:button
                        onclick="copyBackupCodes()"
                        variant="outline"
                        class="flex-1"
                    >
                        📋 Copy
                    </flux:button>
                </div>

                <div class="pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button href="{{ route($profileRoute) }}" variant="primary" class="w-full">
                        Continue to Profile
                    </flux:button>
                </div>
            </div>
        </flux:card>
    </div>
</div>

<script>
const backupCodes = @json($backupCodes);

function downloadBackupCodes() {
    const content = `NEXACODE Marketplace - Backup Codes\nGenerated: ${new Date().toLocaleString()}\n\n${backupCodes.join('\n')}\n\nKeep these codes in a secure location. Each code can only be used once.`;
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
    printWindow.document.write('<style>body{font-family:monospace;padding:20px;} .code{padding:10px;margin:5px;border:1px solid #ccc;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h1>NEXACODE Marketplace - Backup Codes</h1>');
    printWindow.document.write('<p>Generated: ' + new Date().toLocaleString() + '</p>');
    backupCodes.forEach(code => {
        printWindow.document.write('<div class="code">' + code + '</div>');
    });
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

function copyBackupCodes() {
    const text = backupCodes.join('\n');
    navigator.clipboard.writeText(text).then(() => {
        alert('Backup codes copied to clipboard!');
    });
}
</script>
@endsection
