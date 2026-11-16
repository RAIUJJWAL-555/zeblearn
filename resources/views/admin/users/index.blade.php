@extends('layouts.admin')

@section('title', 'User Directory | ZebLearn LMS')
@section('page-title', 'Users')

@section('content')
<div class="space-y-6">
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Users</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                User Directory &amp; Accounts
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Manage registered students, faculty instructors, and administrative user credentials.
            </p>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <x-ui.alert variant="success" dismissible>
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert variant="danger" dismissible>
            {{ session('error') }}
        </x-ui.alert>
    @endif

    {{-- STATS FILTER CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <a
            href="{{ route('admin.users.index') }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ $activeRole === 'all' && $activeStatus === 'all' ? 'border-[#FB6221] ring-2 ring-[#FB6221]/15' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Users</span>
            <span class="text-xl sm:text-2xl font-black text-slate-900 font-heading mt-0.5 block">{{ $stats['total'] }}</span>
        </a>

        <a
            href="{{ route('admin.users.index', ['role' => 'student']) }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ $activeRole === 'student' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[10px] font-mono font-bold uppercase tracking-wider text-blue-600">Students</span>
            <span class="text-xl sm:text-2xl font-black text-slate-900 font-heading mt-0.5 block">{{ $stats['students'] }}</span>
        </a>

        <a
            href="{{ route('admin.users.index', ['role' => 'instructor']) }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ $activeRole === 'instructor' ? 'border-[#FB6221] ring-2 ring-[#FB6221]/15' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[10px] font-mono font-bold uppercase tracking-wider text-[#FB6221]">Instructors</span>
            <span class="text-xl sm:text-2xl font-black text-slate-900 font-heading mt-0.5 block">{{ $stats['instructors'] }}</span>
        </a>

        <a
            href="{{ route('admin.users.index', ['role' => 'admin']) }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ $activeRole === 'admin' ? 'border-slate-900 ring-2 ring-slate-900/15' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-700">Admins</span>
            <span class="text-xl sm:text-2xl font-black text-slate-900 font-heading mt-0.5 block">{{ $stats['admins'] }}</span>
        </a>

        <a
            href="{{ route('admin.users.index', ['status' => 'active']) }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ $activeStatus === 'active' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[10px] font-mono font-bold uppercase tracking-wider text-emerald-600">Active</span>
            <span class="text-xl sm:text-2xl font-black text-slate-900 font-heading mt-0.5 block">{{ $stats['active'] }}</span>
        </a>

        <a
            href="{{ route('admin.users.index', ['status' => 'suspended']) }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ $activeStatus === 'suspended' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[10px] font-mono font-bold uppercase tracking-wider text-rose-600">Suspended</span>
            <span class="text-xl sm:text-2xl font-black text-slate-900 font-heading mt-0.5 block">{{ $stats['suspended'] }}</span>
        </a>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <x-ui.card padding="md" class="border-slate-200/80 shadow-2xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-center">
            <div class="sm:col-span-5 relative">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by name, email, phone..."
                    class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="sm:col-span-3">
                <select
                    name="role"
                    class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                >
                    <option value="all" {{ $activeRole === 'all' ? 'selected' : '' }}>All Roles</option>
                    <option value="student" {{ $activeRole === 'student' ? 'selected' : '' }}>Students</option>
                    <option value="instructor" {{ $activeRole === 'instructor' ? 'selected' : '' }}>Instructors</option>
                    <option value="admin" {{ $activeRole === 'admin' ? 'selected' : '' }}>Administrators</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <select
                    name="status"
                    class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                >
                    <option value="all" {{ $activeStatus === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ $activeStatus === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ $activeStatus === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <x-ui.button
                    type="submit"
                    variant="primary"
                    size="sm"
                    class="w-full justify-center"
                >
                    Filter
                </x-ui.button>
                @if ($search !== '' || $activeRole !== 'all' || $activeStatus !== 'all')
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="px-2.5 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors shrink-0"
                        title="Clear filters"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </x-ui.card>

    {{-- USERS DIRECTORY TABLE --}}
    <x-ui.card padding="none" class="border-slate-200/80 shadow-2xs overflow-hidden">
        @if ($users->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-orange-50 text-[#FB6221] mx-auto flex items-center justify-center mb-4">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 font-heading">No users found</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                    No registered user accounts match your search or filter parameters.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-mono uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-5 font-bold">User</th>
                            <th class="py-3 px-4 font-bold">Role</th>
                            <th class="py-3 px-4 font-bold">Status</th>
                            <th class="py-3 px-4 font-bold">Phone</th>
                            <th class="py-3 px-4 font-bold">Joined</th>
                            <th class="py-3 px-5 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $u)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        @if ($u->avatarUrl())
                                            <img
                                                src="{{ $u->avatarUrl() }}"
                                                alt="{{ $u->name }}"
                                                class="w-9 h-9 rounded-xl object-cover border border-slate-200 shrink-0"
                                            />
                                        @else
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#0A0A0A] to-slate-800 text-white flex items-center justify-center font-heading font-black text-xs shrink-0">
                                                {{ strtoupper(substr($u->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <a
                                                href="{{ route('admin.users.show', $u) }}"
                                                class="font-bold text-slate-900 hover:text-[#FB6221] transition-colors truncate block text-xs sm:text-sm font-heading"
                                            >
                                                {{ $u->name }}
                                            </a>
                                            <span class="text-[11px] text-slate-400 block truncate">{{ $u->email }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($u->isAdmin())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-slate-900 text-white">
                                            Admin
                                        </span>
                                    @elseif ($u->isInstructor())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-100 text-[#FB6221]">
                                            Instructor
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-blue-100 text-blue-700">
                                            Student
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($u->isActive())
                                        <span class="inline-flex items-center gap-1.5 text-emerald-600 font-semibold text-xs">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-rose-600 font-semibold text-xs">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                            Suspended
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 font-mono text-[11px]">
                                    {{ $u->phone ?: '—' }}
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                    {{ $u->created_at->format('d M Y') }}
                                </td>

                                <td class="py-3.5 px-5 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button
                                            href="{{ route('admin.users.show', $u) }}"
                                            variant="ghost"
                                            size="xs"
                                            icon="eye"
                                            title="View details"
                                        />
                                        <x-ui.button
                                            href="{{ route('admin.users.edit', $u) }}"
                                            variant="ghost"
                                            size="xs"
                                            icon="pencil"
                                            title="Edit user"
                                        />

                                        @if (auth()->id() !== $u->id)
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $u) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    onclick="return confirm('Are you sure you want to {{ $u->isActive() ? 'suspend' : 'activate' }} this user?')"
                                                    class="p-1 rounded-lg text-xs font-semibold {{ $u->isActive() ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} transition-colors cursor-pointer"
                                                    title="{{ $u->isActive() ? 'Suspend account' : 'Activate account' }}"
                                                >
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        @if ($u->isActive())
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        @endif
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </x-ui.card>
</div>
@endsection

