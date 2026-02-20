<div class="space-y-8">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}" separator="slash">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Author Requests</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="font-black">Author Applications</flux:heading>
            <flux:subheading>Review and moderate requests from users to become sellers.</flux:subheading>
        </div>
    </div>

    @if (session('status'))
        <script>
            Flux.toast({
                variant: 'success',
                heading: 'Success',
                text: '{{ session('status') }}'
            });
        </script>
    @endif


    <flux:card class="space-y-6">
        <flux:table :paginate="$this->requests" container:class="max-h-80">
            <flux:table.columns>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Information</flux:table.column>
                <flux:table.column>Portfolio</flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Applied On</flux:table.column>
                <flux:table.column align="right">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                {{-- Loading Skeletons --}}
                @foreach(range(1, 5) as $i)
                    <flux:table.row wire:loading wire:target="sort">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <flux:skeleton class="size-8 rounded-full" />
                                <div class="space-y-1">
                                    <flux:skeleton class="w-32 h-3" />
                                    <flux:skeleton class="w-24 h-2" />
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-full h-4" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-24 h-8 rounded-md" /></flux:table.cell>
                        <flux:table.cell><flux:skeleton class="w-24 h-3" /></flux:table.cell>
                        <flux:table.cell align="right"><flux:skeleton class="w-16 h-8 rounded-md" /></flux:table.cell>
                    </flux:table.row>
                @endforeach

                {{-- Actual Data --}}
                @forelse($this->requests as $request)
                    <flux:table.row :key="$request->id" wire:loading.remove wire:target="sort">
                        <flux:table.cell variant="strong">
                            <button wire:click="viewRequest({{ $request->id }})" class="flex items-center gap-3 text-left hover:opacity-80 transition-opacity">
                                <flux:avatar :initials="$request->user->initials" size="xs" />
                                <div>
                                    <div class="font-bold">{{ $request->user->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $request->user->email }}</div>
                                </div>
                            </button>
                        </flux:table.cell>
                        <flux:table.cell class="max-w-xs">
                            <div class="text-sm line-clamp-2" title="{{ $request->message }}">
                                {{ $request->message }}
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($request->portfolio_url)
                                <flux:button href="{{ $request->portfolio_url }}" target="_blank" variant="ghost" size="sm" icon="external-link">
                                    View Link
                                </flux:button>
                            @else
                                <span class="text-xs text-zinc-400">N/A</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-500">
                            {{ $request->created_at->format('M d, g:i A') }}
                        </flux:table.cell>
                        <flux:table.cell align="right">
                            <flux:button wire:click="viewRequest({{ $request->id }})" variant="subtle" size="sm" icon="pencil-square" inset="top bottom">
                                Review
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row wire:loading.remove wire:target="sort">
                        <flux:table.cell colspan="5" class="text-center py-12 text-zinc-500">
                            No pending author applications at the moment.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <div class="mt-4">
    </div>

    {{-- Review Application Modal --}}
    <flux:modal name="review-application" class="md:w-[600px]">
        @if($selectedRequest)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Review Author Application</flux:heading>
                    <flux:text class="mt-2">Reviewing request from {{ $selectedRequest->user->name }}.</flux:text>
                </div>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 space-y-4">
                    <div>
                        <flux:label>Application Message</flux:label>
                        <div class="mt-1 text-sm text-zinc-700 dark:text-zinc-300 italic">
                            "{{ $selectedRequest->message }}"
                        </div>
                    </div>
                    
                    @if($selectedRequest->portfolio_url)
                        <div>
                            <flux:label>Portfolio / Store Link</flux:label>
                            <div class="mt-1">
                                <flux:button href="{{ $selectedRequest->portfolio_url }}" target="_blank" variant="subtle" size="sm" icon="external-link">
                                    {{ $selectedRequest->portfolio_url }}
                                </flux:button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-2">
                    <flux:label>User Information</flux:label>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-xs">
                            <span class="text-zinc-500 uppercase font-black">Email:</span>
                            <div class="font-bold">{{ $selectedRequest->user->email }}</div>
                        </div>
                        <div class="text-xs">
                            <span class="text-zinc-500 uppercase font-black">Applied:</span>
                            <div class="font-bold">{{ $selectedRequest->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:button wire:click="approve({{ $selectedRequest->id }})" variant="primary" color="emerald" class="flex-1">Approve Application</flux:button>
                    <flux:button wire:click="reject({{ $selectedRequest->id }})" variant="ghost" color="red">Reject</flux:button>
                    <flux:spacer />
                    <flux:button variant="ghost" x-on:click="Flux.modal('review-application').close()">Close</flux:button>
                </div>
            </div>
        @else
            <div class="py-12 flex justify-center">
                <flux:icon.loading class="w-8 h-8" />
            </div>
        @endif
    </flux:modal>
</div>

@script
    $wire.on('modal-opened', (event) => {
        Flux.modal(event.name).show();
    });

    $wire.on('modal-closed', (event) => {
        const modal = Flux.modal(event.name);
        if (typeof modal.close === 'function') modal.close();
        else if (typeof modal.hide === 'function') modal.hide();
    });
@endscript
