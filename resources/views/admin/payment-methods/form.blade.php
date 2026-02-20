@extends('layouts.admin')

@section('title', isset($paymentMethod) ? 'Edit Payment Method' : 'Add Payment Method')

@section('content')
<div class="max-w-3xl space-y-8">
    <div>
        <flux:heading size="2xl">{{ isset($paymentMethod) ? 'Edit Payment Method' : 'Add Payment Method' }}</flux:heading>
        <p class="text-zinc-500 mt-1">Configure payment options for customers</p>
    </div>

    <flux:card class="p-8">
        <form action="{{ isset($paymentMethod) ? route('admin.payment-methods.update', $paymentMethod) : route('admin.payment-methods.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ type: '{{ old('type', $paymentMethod->type ?? 'bank_transfer') }}' }">
            @csrf
            @if(isset($paymentMethod))
                @method('PUT')
            @endif

            {{-- Type --}}
            <flux:field>
                <flux:label>Payment Type</flux:label>
                <flux:select name="type" x-model="type" required>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="qris">QRIS</option>
                    <option value="ewallet">E-Wallet</option>
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            {{-- Name --}}
            <flux:field>
                <flux:label>Payment Method Name</flux:label>
                <flux:input type="text" name="name" value="{{ old('name', $paymentMethod->name ?? '') }}" placeholder="e.g., BCA, Mandiri, QRIS" required />
                <flux:error name="name" />
                <flux:description>Display name shown to customers</flux:description>
            </flux:field>

            {{-- Bank Transfer Fields --}}
            <div x-show="type === 'bank_transfer'" class="space-y-6">
                <flux:field>
                    <flux:label>Account Number</flux:label>
                    <flux:input type="text" name="account_number" value="{{ old('account_number', $paymentMethod->account_number ?? '') }}" placeholder="1234567890" />
                    <flux:error name="account_number" />
                </flux:field>

                <flux:field>
                    <flux:label>Account Name</flux:label>
                    <flux:input type="text" name="account_name" value="{{ old('account_name', $paymentMethod->account_name ?? '') }}" placeholder="NEXACODE" />
                    <flux:error name="account_name" />
                </flux:field>
            </div>

            {{-- QRIS Fields --}}
            <div x-show="type === 'qris'" class="space-y-6">
                <flux:field>
                    <flux:label>QRIS Static Code</flux:label>
                    <flux:textarea name="qris_static" rows="4" placeholder="Paste your static QRIS code here">{{ old('qris_static', $paymentMethod->qris_static ?? '') }}</flux:textarea>
                    <flux:error name="qris_static" />
                    <flux:description>The system will automatically convert this to dynamic QRIS with order amount</flux:description>
                </flux:field>
            </div>

            {{-- Logo --}}
            <flux:field>
                <flux:label>Logo (Optional)</flux:label>
                <flux:input type="file" name="logo" accept="image/*" />
                <flux:error name="logo" />
                <flux:description>Recommended size: 256x256px (Max 1MB)</flux:description>
                @if(isset($paymentMethod) && $paymentMethod->logo)
                    <div class="mt-3">
                        <img src="{{ Storage::url($paymentMethod->logo) }}" class="w-24 h-24 object-contain rounded-xl border border-zinc-200 dark:border-zinc-800" alt="Current logo">
                    </div>
                @endif
            </flux:field>

            {{-- Instructions --}}
            <flux:field>
                <flux:label>Payment Instructions (Optional)</flux:label>
                <div class="space-y-3" x-data="{ 
                    instructions: {{ json_encode(old('instructions', isset($paymentMethod) ? $paymentMethod->instructions : [])) }},
                    addInstruction() {
                        this.instructions.push('');
                    },
                    removeInstruction(index) {
                        this.instructions.splice(index, 1);
                    }
                }">
                    <template x-for="(instruction, index) in instructions" x-bind:key="index">
                        <div class="flex gap-2">
                            <flux:input type="text" x-bind:name="'instructions[' + index + ']'" x-model="instructions[index]" placeholder="Step-by-step instruction" class="flex-1" />
                            <flux:button type="button" variant="ghost" @click="removeInstruction(index)">
                                <flux:icon.trash class="w-4 h-4" />
                            </flux:button>
                        </div>
                    </template>
                    <flux:button type="button" variant="outline" size="sm" @click="addInstruction()">
                        <flux:icon.plus class="w-4 h-4" />
                        Add Instruction
                    </flux:button>
                </div>
                <flux:description>Step-by-step instructions shown to customers</flux:description>
            </flux:field>

            {{-- Active Status --}}
            <flux:field>
                <div class="flex items-center gap-3">
                    <flux:checkbox name="is_active" value="1" {{ old('is_active', $paymentMethod->is_active ?? true) ? 'checked' : '' }} />
                    <flux:label>Active (visible to customers)</flux:label>
                </div>
                <flux:error name="is_active" />
            </flux:field>

            {{-- Sort Order --}}
            <flux:field>
                <flux:label>Sort Order</flux:label>
                <flux:input type="number" name="sort_order" value="{{ old('sort_order', $paymentMethod->sort_order ?? 0) }}" min="0" />
                <flux:error name="sort_order" />
                <flux:description>Lower numbers appear first</flux:description>
            </flux:field>

            {{-- Actions --}}
            <div class="flex gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button type="submit" variant="primary">
                    {{ isset($paymentMethod) ? 'Update Payment Method' : 'Create Payment Method' }}
                </flux:button>
                <flux:button variant="ghost" href="{{ route('admin.payment-methods.index') }}">
                    Cancel
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
@endsection
