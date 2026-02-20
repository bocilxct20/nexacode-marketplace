<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Buyer Reports</flux:heading>
            <flux:subheading>Manage and review reports from authors about problematic buyers</flux:subheading>
        </div>
    </div>

    {{-- Filters --}}
    <flux:card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:field>
                <flux:label>Search</flux:label>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by buyer or author name..." icon="magnifying-glass" />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="filterStatus">
                    <option value="all">All Reports</option>
                    <option value="pending">Pending Review</option>
                    <option value="resolved">Resolved/Dismissed</option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Category</flux:label>
                <flux:select wire:model.live="filterCategory">
                    <option value="all">All Categories</option>
                    <option value="abusive_language">Bahasa Kasar</option>
                    <option value="spam">Spam</option>
                    <option value="refund_abuse">Refund Abuse</option>
                    <option value="payment_issues">Payment Issues</option>
                    <option value="other">Other</option>
                </flux:select>
            </flux:field>
        </div>
    </flux:card>

    {{-- Reports Table --}}
    <flux:card class="p-0 overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Report ID</flux:table.column>
                <flux:table.column>Author</flux:table.column>
                <flux:table.column>Buyer</flux:table.column>
                <flux:table.column>Category</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                {{-- Loading Skeletons --}}
                @foreach(range(1, 10) as $i)
                    <flux:table.row wire:loading wire:target="search, filterStatus, filterCategory">
                        <flux:table.cell><flux:skeleton class="w-12 h-3" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:skeleton class="size-6 rounded-full" />
                                <flux:skeleton class="w-24 h-3" />
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:skeleton class="size-6 rounded-full" />
                                <flux:skeleton class="w-24 h-3" />
                            </div>
                        </flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-20 h-5 rounded-full" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-16 h-5 rounded-full" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-24 h-3" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-24 h-8 rounded-md" /></flux:table.cell>
                    </flux:table.row>
                @endforeach

                {{-- Actual Data --}}
                @forelse($reports as $report)
                    <flux:table.row :key="$report->id" wire:loading.remove wire:target="search, filterStatus, filterCategory">
                        <flux:table.cell class="font-mono text-xs">#{{ $report->id }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:avatar size="xs" :name="$report->author?->name" :initials="$report->author?->initials" />
                                <span class="font-medium">{{ $report->author?->name ?? 'Unknown' }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:avatar size="xs" :name="$report->buyer?->name" :initials="$report->buyer?->initials" />
                                <span class="font-medium">{{ $report->buyer?->name ?? 'Unknown' }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="match($report->category) {
                                'abusive_language' => 'red',
                                'spam' => 'amber',
                                'refund_abuse' => 'rose',
                                'payment_issues' => 'blue',
                                default => 'zinc'
                            }">
                                {{ $report->category_label }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" :color="$report->status_color">
                                {{ $report->status_label }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-500">
                            {{ $report->created_at->format('d M Y, H:i') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="sm" variant="ghost" wire:click="selectReport({{ $report->id }})">
                                View Details
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row wire:loading.remove wire:target="search, filterStatus, filterCategory">
                        <flux:table.cell colspan="7" class="text-center py-12">
                            <flux:icon.shield-exclamation variant="outline" class="w-12 h-12 mx-auto mb-4 text-zinc-300 dark:text-zinc-600" />
                            <flux:subheading>No reports found</flux:subheading>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if($reports->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $reports->links() }}
            </div>
        @endif
    </flux:card>

    {{-- Report Detail Modal --}}
    <flux:modal name="report-detail" wire:model="showDetailModal" class="max-w-2xl">
        @if($selectedReport)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Report Details #{{ $selectedReport->id }}</flux:heading>
                    <flux:subheading>Review and take action on this report</flux:subheading>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:label>Author (Reporter)</flux:label>
                        <div class="flex items-center gap-2 mt-2">
                            <flux:avatar :name="$selectedReport->author?->name" :initials="$selectedReport->author?->initials" />
                            <div>
                                <div class="font-medium">{{ $selectedReport->author?->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $selectedReport->author?->email }}</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <flux:label>Buyer (Reported)</flux:label>
                        <div class="flex items-center gap-2 mt-2">
                            <flux:avatar :name="$selectedReport->buyer?->name" :initials="$selectedReport->buyer?->initials" />
                            <div>
                                <div class="font-medium">{{ $selectedReport->buyer?->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $selectedReport->buyer?->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <flux:separator />

                <div class="space-y-4">
                    <div>
                        <flux:label>Category</flux:label>
                        <flux:badge :color="match($selectedReport->category) {
                            'abusive_language' => 'red',
                            'spam' => 'amber',
                            'refund_abuse' => 'rose',
                            'payment_issues' => 'blue',
                            default => 'zinc'
                        }" class="mt-2">
                            {{ $selectedReport->category_label }}
                        </flux:badge>
                    </div>

                    <div>
                        <flux:label>Reason</flux:label>
                        <div class="mt-2 p-4 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800">
                            <p class="text-sm leading-relaxed">{{ $selectedReport->reason }}</p>
                        </div>
                    </div>

                    <div>
                        <flux:label>Status</flux:label>
                        <flux:badge :color="$selectedReport->status_color" class="mt-2">
                            {{ $selectedReport->status_label }}
                        </flux:badge>
                    </div>

                    @if($selectedReport->admin)
                        <div>
                            <flux:label>Handled By</flux:label>
                            <div class="flex items-center gap-2 mt-2">
                                <flux:avatar size="xs" :name="$selectedReport->admin->name" :initials="$selectedReport->admin->initials" />
                                <span class="text-sm">{{ $selectedReport->admin->name }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <flux:separator />

                <flux:field>
                    <flux:label>Admin Notes</flux:label>
                    <flux:description>Add internal notes about this report</flux:description>
                    <flux:textarea wire:model="adminNotes" rows="3" placeholder="Add your notes here..." />
                </flux:field>

                <div class="flex gap-2">
                    <flux:button variant="ghost" class="flex-1" @click="showDetailModal = false">Close</flux:button>
                    @if($selectedReport->status !== 'dismissed')
                        <flux:button variant="subtle" class="flex-1" wire:click="markAsDismissed">Dismiss Report</flux:button>
                    @endif
                    @if($selectedReport->status !== 'resolved')
                        <flux:button variant="primary" class="flex-1" wire:click="markAsResolved">Mark as Resolved</flux:button>
                    @endif
                </div>
            </div>
        @endif
    </flux:modal>
</div>
