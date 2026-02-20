<div wire:init="load" class="space-y-6">
    <div class="pt-4 pb-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}" separator="slash">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">User Management</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <flux:heading size="xl" level="1">User Management</flux:heading>
            <flux:subheading italic>Manage all platform participants, roles, and access controls.</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="createUser">Add New User</flux:button>
    </div>

    <flux:card class="space-y-6">
        <div class="flex flex-col md:flex-row gap-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search users by name, email, or username..." icon="magnifying-glass" class="flex-1" />
            
            <div class="flex gap-4">
                <flux:select wire:model.live="roleFilter" placeholder="All Roles" class="w-40">
                    <flux:select.option value="">All Roles</flux:select.option>
                    @foreach($roles as $role)
                        <flux:select.option value="{{ $role->slug }}">{{ $role->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="statusFilter" placeholder="All Status" class="w-40">
                    <flux:select.option value="">All Status</flux:select.option>
                    <flux:select.option value="1">Active</flux:select.option>
                    <flux:select.option value="0">Inactive</flux:select.option>
                </flux:select>
            </div>
        </div>

        <flux:table :paginate="$this->readyToLoad ? $users : null">
            <flux:table.columns>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Role</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Joined</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @if(!$this->readyToLoad)
                    @foreach(range(1, 10) as $i)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:skeleton class="size-10 rounded-full" />
                                    <div class="space-y-2">
                                        <flux:skeleton class="w-32 h-4" />
                                        <flux:skeleton class="w-48 h-3" />
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-1">
                                    <flux:skeleton class="w-16 h-6 rounded-full" />
                                </div>
                            </flux:table.cell>
                            <flux:table.cell><flux:skeleton class="w-12 h-6 rounded-full" /></flux:table.cell>
                            <flux:table.cell><flux:skeleton class="w-24 h-4" /></flux:table.cell>
                            <flux:table.cell align="right"><flux:skeleton class="size-8 rounded-md" /></flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @else
                    @foreach ($users as $user)
                        <flux:table.row :key="$user->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$user->name" :initials="$user->initials" />
                                    <div>
                                        <div class="font-bold text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-zinc-500">{{ $user->email }} / @ {{ $user->username }}</div>
                                    </div>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <flux:badge size="sm" color="{{ $role->slug === 'admin' ? 'amber' : ($role->slug === 'author' ? 'sky' : 'zinc') }}" inset="left">
                                            {{ $role->name }}
                                        </flux:badge>
                                    @endforeach
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:switch 
                                    wire:click="toggleUserStatus({{ $user->id }})" 
                                    :checked="$user->is_active" 
                                    color="emerald"
                                    aria-label="Toggle user activity"
                                />
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="text-xs text-zinc-500">{{ $user->created_at->format('M d, Y') }}</div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:dropdown align="end" variant="subtle">
                                    <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil-square" wire:click="editUser({{ $user->id }})">Edit User</flux:menu.item>
                                        <flux:menu.item icon="key" wire:click="initiatePasswordReset({{ $user->id }})">Reset Password</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger">Delete User</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @endif
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <!-- Create User Modal -->
    <flux:modal wire:model="showCreateModal" class="md:w-[600px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add New User</flux:heading>
                <flux:subheading>Create a new account and assign roles manually.</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field class="col-span-2">
                    <flux:label>Full Name</flux:label>
                    <flux:input wire:model="name" placeholder="John Doe" />
                </flux:field>

                <flux:field>
                    <flux:label>Username</flux:label>
                    <flux:input wire:model="username" placeholder="johndoe" />
                </flux:field>

                <flux:field>
                    <flux:label>Email Address</flux:label>
                    <flux:input type="email" wire:model="email" placeholder="john@example.com" />
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>Password</flux:label>
                    <div class="flex gap-2">
                        <flux:input :type="$showPassword ? 'text' : 'password'" wire:model="password" class="flex-1" />
                        <flux:button variant="subtle" square icon="{{ $showPassword ? 'eye-slash' : 'eye' }}" wire:click="$toggle('showPassword')" />
                    </div>
                    <flux:description>Admin visibility enabled. Copy and share with the user securely.</flux:description>
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>Assign Roles</flux:label>
                    <div class="flex flex-wrap gap-4 mt-2">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <flux:checkbox wire:model="selectedRoles" value="{{ $role->id }}" />
                                <span class="text-sm font-medium">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </flux:field>

                <flux:field class="col-span-2">
                    <div class="flex items-center gap-3">
                        <flux:switch wire:model="is_active" color="emerald" id="create-active-switch" />
                        <flux:label for="create-active-switch">Account Active</flux:label>
                    </div>
                </flux:field>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button wire:click="$set('showCreateModal', false)">Cancel</flux:button>
                <flux:button variant="primary" wire:click="saveUser">Create User</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Edit User Modal -->
    <flux:modal wire:model="showEditModal" class="md:w-[600px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Edit User Details</flux:heading>
                <flux:subheading>Updating user information and access levels.</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field class="col-span-2">
                    <flux:label>Full Name</flux:label>
                    <flux:input wire:model="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Username</flux:label>
                    <flux:input wire:model="username" />
                </flux:field>

                <flux:field>
                    <flux:label>Email Address</flux:label>
                    <flux:input type="email" wire:model="email" />
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>Assign Roles</flux:label>
                    <div class="flex flex-wrap gap-4 mt-2">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <flux:checkbox wire:model="selectedRoles" value="{{ $role->id }}" />
                                <span class="text-sm font-medium">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </flux:field>

                <flux:field class="col-span-2">
                    <div class="flex items-center gap-3">
                        <flux:switch wire:model="is_active" color="emerald" id="edit-active-switch" />
                        <flux:label for="edit-active-switch">Account Active</flux:label>
                    </div>
                </flux:field>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button wire:click="$set('showEditModal', false)">Cancel</flux:button>
                <flux:button variant="primary" wire:click="updateUser">Save Changes</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Reset Password Modal -->
    <flux:modal wire:model="showResetPasswordModal" class="md:w-[450px]">
        <div class="space-y-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <flux:icon.key class="w-8 h-8 text-amber-600" />
                </div>
                <flux:heading size="lg">Reset User Password</flux:heading>
                <flux:subheading>This will immediately change the user's password.</flux:subheading>
            </div>

            <flux:card class="bg-zinc-50 dark:bg-zinc-800/50 border-dashed border-2">
                <div class="text-center space-y-2">
                    <flux:label class="uppercase tracking-widest text-[10px] font-black opacity-50">New Random Password</flux:label>
                    <div class="text-2xl font-black font-mono tracking-tighter text-emerald-600 dark:text-emerald-400 select-all">
                        {{ $autoGeneratedPassword }}
                    </div>
                    <flux:description>Copy this password and share it with the user.</flux:description>
                </div>
            </flux:card>

            <div class="flex flex-col gap-2">
                <flux:button variant="primary" class="w-full" wire:click="resetPassword">Confirm New Password</flux:button>
                <flux:button variant="ghost" class="w-full" wire:click="$set('showResetPasswordModal', false)">Nevermind, keep old one</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
