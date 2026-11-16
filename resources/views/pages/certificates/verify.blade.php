@extends('layouts.app')

@section('title', 'Verify Certificate ' . $certificateNumber . ' | ZebLearn LMS')
@section('description', 'Official public credential verification for certificate ' . $certificateNumber . ' issued by ZebLearn.')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-32 sm:pt-40 pb-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-6">
            <a href="{{ route('home') }}" class="hover:text-[#FB6221] transition-colors">Home</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Credential Verification</span>
            <span>/</span>
            <span class="text-[#FB6221] font-bold">{{ $certificateNumber }}</span>
        </nav>

        <!-- Main Verification Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-lg overflow-hidden relative">

            <!-- Top Header Banner -->
            <div class="{{ $isValid ? 'bg-gradient-to-r from-emerald-600 to-teal-700' : 'bg-gradient-to-r from-red-600 to-rose-700' }} text-white p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-white/20 text-white font-mono text-[10px] font-bold uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full {{ $isValid ? 'bg-emerald-300 animate-pulse' : 'bg-red-200' }}"></span>
                        ZebLearn Credential Registry
                    </div>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
                        {{ $isValid ? 'Verified Official Certificate' : 'Revoked Certificate' }}
                    </h1>
                </div>

                <div class="shrink-0">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl {{ $isValid ? 'bg-emerald-500/30 border border-emerald-300/40 text-emerald-100' : 'bg-red-500/30 border border-red-300/40 text-red-100' }} text-xs font-mono font-bold uppercase">
                        <span class="material-symbols-outlined text-[18px]">
                            {{ $isValid ? 'verified' : 'cancel' }}
                        </span>
                        <span>{{ $isValid ? 'Valid Credential' : 'Revoked' }}</span>
                    </span>
                </div>
            </div>

            <!-- Revocation Notice if applicable -->
            @if ($isRevoked)
                <div class="bg-red-50 border-b border-red-200 p-5 sm:p-6 text-red-900">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-red-600 text-[24px] shrink-0 mt-0.5">warning</span>
                        <div class="space-y-1 text-xs sm:text-sm">
                            <strong class="font-bold block uppercase tracking-wide text-red-800">
                                This certificate was officially revoked on {{ $revokedAt?->format('F d, Y') }}
                            </strong>
                            <p class="text-red-700 font-sans">
                                <strong>Reason:</strong> {{ $revocationReason ?: 'Administrative decision by examination board.' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Certificate Details Table -->
            <div class="p-6 sm:p-8 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    <!-- Student Name -->
                    <div class="space-y-1">
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400 block">
                            Awarded To
                        </span>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 font-heading">
                            {{ $studentName }}
                        </h2>
                    </div>

                    <!-- Certificate Number -->
                    <div class="space-y-1">
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400 block">
                            Certificate Identifier
                        </span>
                        <span class="font-mono text-base sm:text-lg font-bold text-[#FB6221] bg-orange-50 px-3 py-1 rounded-lg border border-orange-200 inline-block">
                            {{ $certificateNumber }}
                        </span>
                    </div>

                    <!-- Course Name -->
                    <div class="sm:col-span-2 space-y-1 pt-2 border-t border-slate-100">
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400 block">
                            Program / Course Completed
                        </span>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-lg sm:text-xl font-bold text-slate-900 font-heading">
                                {{ $courseName }}
                            </h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $courseCategory }}
                            </span>
                        </div>
                    </div>

                    <!-- Issue Date -->
                    <div class="space-y-1 pt-2 border-t border-slate-100">
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400 block">
                            Date of Issuance
                        </span>
                        <strong class="text-sm font-sans text-slate-800">
                            {{ $issuedAt->format('F d, Y') }}
                        </strong>
                    </div>

                    <!-- Issuer -->
                    <div class="space-y-1 pt-2 border-t border-slate-100">
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400 block">
                            Accrediting Body
                        </span>
                        <strong class="text-sm font-sans text-slate-800">
                            ZebLearn LMS Education Board
                        </strong>
                    </div>
                </div>

                <!-- Security Assurance Footer -->
                <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-slate-500 font-sans">
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">security</span>
                        <span>Digitally verified via ZebLearn LMS Registry.</span>
                    </div>

                    <a
                        href="{{ route('courses.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#0A0A0A] hover:bg-[#FB6221] text-white font-sans text-xs font-semibold uppercase tracking-wider transition-colors shadow-xs"
                    >
                        <span>Explore ZebLearn Courses</span>
                        <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

