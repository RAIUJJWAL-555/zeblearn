@extends('layouts.admin')

@section('title', 'Certificate Registry | ZebLearn LMS')
@section('page-title', 'Certificates')

@section('content')
<div class="space-y-6">
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Certificates</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                Certificate Registry &amp; Credentials
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Monitor all issued course completion credentials, download official certificates, and manage revocations.
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

    {{-- STATS & QUICK FILTER CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a
            href="{{ route('admin.certificates.index') }}"
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $activeStatus === 'all' ? 'border-[#FB6221] ring-2 ring-[#FB6221]/15' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Issued</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $totalCount }}</span>
        </a>

        <a
            href="{{ route('admin.certificates.index', ['status' => 'valid']) }}"
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $activeStatus === 'valid' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-600">Active &amp; Valid</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $validCount }}</span>
        </a>

        <a
            href="{{ route('admin.certificates.index', ['status' => 'revoked']) }}"
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $activeStatus === 'revoked' ? 'border-red-500 ring-2 ring-red-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-red-600">Revoked</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $revokedCount }}</span>
        </a>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.certificates.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <input type="hidden" name="status" value="{{ $activeStatus }}">

            <div class="w-full sm:max-w-md relative">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[20px]">search</span>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by student, course, or certificate number..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:outline-hidden focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221]"
                >
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                @if ($search || $activeStatus !== 'all')
                    <a
                        href="{{ route('admin.certificates.index') }}"
                        class="px-3 py-2 text-xs font-mono uppercase text-slate-500 hover:text-slate-800 transition-colors"
                    >
                        Clear Filters
                    </a>
                @endif
                <x-ui.button type="submit" variant="primary" size="sm">
                    Search
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- CERTIFICATES TABLE --}}
    <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
        @if ($certificates->isEmpty())
            <div class="p-8">
                <x-ui.empty-state
                    title="No Certificates Found"
                    description="No issued certificates matched your current search and status filter criteria."
                    icon="workspace_premium"
                />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-mono uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Certificate ID</th>
                            <th class="py-3 px-4">Student</th>
                            <th class="py-3 px-4">Course</th>
                            <th class="py-3 px-4">Issued On</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @foreach ($certificates as $certificate)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <a
                                        href="{{ route('admin.certificates.show', $certificate) }}"
                                        class="font-mono font-bold text-[#FB6221] hover:underline"
                                    >
                                        {{ $certificate->certificate_number }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $certificate->user?->name ?? 'Student' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $certificate->user?->email }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-slate-800">{{ $certificate->course?->name ?? 'Course' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono uppercase">{{ $certificate->course?->category_badge }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">
                                    {{ $certificate->issued_at->format('M d, Y') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($certificate->isValid())
                                        <x-ui.badge variant="success" size="xs" dot>
                                            Active
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="error" size="xs" dot>
                                            Revoked
                                        </x-ui.badge>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a
                                            href="{{ route('admin.certificates.download', $certificate) }}"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                            title="Download PDF"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">download</span>
                                        </a>

                                        <a
                                            href="{{ route('certificates.verify', $certificate->certificate_number) }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-[#FB6221] hover:bg-orange-50 transition-colors"
                                            title="Public Verification"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                        </a>

                                        <a
                                            href="{{ route('admin.certificates.show', $certificate) }}"
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800 transition-colors"
                                        >
                                            Inspect
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $certificates->links() }}
            </div>
        @endif
    </x-ui.card>
</div>
@endsection

