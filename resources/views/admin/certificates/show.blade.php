@extends('layouts.admin')

@section('title', 'Certificate #' . $certificate->certificate_number . ' | ZebLearn LMS')
@section('page-title', 'Certificate Details')

@section('content')
<div class="space-y-6 max-w-5xl">
    {{-- BREADCRUMBS & TOP BAR --}}
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.certificates.index') }}" class="hover:text-[#FB6221] transition-colors">Certificates</a>
            <span>/</span>
            <span class="text-slate-900 font-bold truncate max-w-xs">{{ $certificate->certificate_number }}</span>
        </nav>
        <x-ui.button
            href="{{ route('admin.certificates.index') }}"
            variant="secondary"
            size="sm"
            icon="arrow-left"
        >
            Back to Registry
        </x-ui.button>
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

    {{-- HEADER CARD --}}
    <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                        {{ $certificate->course?->category_badge ?? 'Enterprise' }}
                    </span>
                    @if ($certificate->isValid())
                        <x-ui.badge variant="success" size="xs" dot>
                            Valid &amp; Active
                        </x-ui.badge>
                    @else
                        <x-ui.badge variant="error" size="xs" dot>
                            Revoked
                        </x-ui.badge>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                    {{ $certificate->course?->name }}
                </h1>
                <p class="text-xs font-mono text-slate-500">
                    Certificate ID: <strong class="text-[#FB6221]">{{ $certificate->certificate_number }}</strong>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <x-ui.button
                    href="{{ route('admin.certificates.download', $certificate) }}"
                    variant="primary"
                    size="sm"
                    icon="download"
                >
                    Download PDF
                </x-ui.button>

                <x-ui.button
                    href="{{ route('certificates.verify', $certificate->certificate_number) }}"
                    variant="secondary"
                    size="sm"
                    target="_blank"
                    rel="noopener"
                    icon="arrow-top-right"
                >
                    Verify Page
                </x-ui.button>
            </div>
        </div>

        {{-- REVOKED NOTICE --}}
        @if ($certificate->isRevoked())
            <div class="mt-6 p-5 bg-red-50 rounded-2xl border border-red-200 text-red-900 space-y-1 text-xs sm:text-sm">
                <div class="flex items-center gap-2 font-bold uppercase font-mono text-red-700">
                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                    <span>Certificate Revoked</span>
                </div>
                <p>This certificate was revoked on <strong>{{ $certificate->revoked_at?->format('F d, Y H:i') }}</strong>.</p>
                <p class="text-red-800"><strong>Reason for Revocation:</strong> {{ $certificate->revocation_reason }}</p>
            </div>
        @endif

        {{-- METADATA GRIDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- Student Information -->
            <div class="space-y-4 p-5 bg-slate-50 rounded-2xl border border-slate-100">
                <h3 class="font-heading font-bold text-sm text-slate-900 uppercase tracking-wide">
                    Recipient Student
                </h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Name:</span>
                        <strong class="text-slate-900">{{ $certificate->user?->name }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Email:</span>
                        <span class="font-mono text-slate-800">{{ $certificate->user?->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">User ID:</span>
                        <span class="font-mono text-slate-800">#{{ $certificate->user_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Account Status:</span>
                        <span class="capitalize font-semibold text-slate-800">{{ $certificate->user?->status }}</span>
                    </div>
                </div>
            </div>

            <!-- Enrollment & Issuance Information -->
            <div class="space-y-4 p-5 bg-slate-50 rounded-2xl border border-slate-100">
                <h3 class="font-heading font-bold text-sm text-slate-900 uppercase tracking-wide">
                    Enrollment &amp; Issuance
                </h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Issued Timestamp:</span>
                        <strong class="text-slate-900">{{ $certificate->issued_at->format('M d, Y H:i:s') }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Enrollment ID:</span>
                        <a href="{{ route('admin.enrollments.show', $certificate->enrollment_id) }}" class="font-mono text-[#FB6221] hover:underline font-bold">
                            #{{ $certificate->enrollment_id }}
                        </a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Enrollment Approved:</span>
                        <span class="text-slate-800">{{ $certificate->enrollment?->approved_at?->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Verification URL:</span>
                        <a href="{{ $certificate->verificationUrl() }}" target="_blank" class="text-blue-600 hover:underline truncate max-w-xs">
                            /verify-certificate/{{ $certificate->certificate_number }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- REVOCATION CONTROLS (IF VALID) --}}
    @if ($certificate->isValid())
        <x-ui.card padding="lg" class="border-red-200 shadow-2xs">
            <div class="space-y-4">
                <div>
                    <h2 class="font-heading text-lg font-bold text-red-700 uppercase tracking-tight">
                        Revoke Certificate
                    </h2>
                    <p class="text-xs text-slate-600 mt-1">
                        Revoking this credential immediately flags it as invalid across all public verification checks and watermark-stamps the PDF. A mandatory reason is required for administrative auditing.
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.certificates.revoke', $certificate) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="revocation_reason" class="block text-xs font-mono font-bold uppercase text-slate-700 mb-1">
                            Revocation Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="revocation_reason"
                            name="revocation_reason"
                            rows="3"
                            required
                            placeholder="State the formal reason for revoking this certificate (e.g. Academic dishonesty, enrollment cancellation, administrative re-issuance)..."
                            class="w-full rounded-xl border border-slate-200 p-3 text-xs focus:outline-hidden focus:border-red-500 focus:ring-1 focus:ring-red-500"
                        >{{ old('revocation_reason') }}</textarea>
                        @error('revocation_reason')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            onclick="return confirm('Are you sure you want to officially revoke this certificate? This action will immediately invalidate the credential.')"
                            class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-sans text-xs font-bold uppercase tracking-wider shadow-sm transition-colors"
                        >
                            Confirm Revocation
                        </button>
                    </div>
                </form>
            </div>
        </x-ui.card>
    @endif
</div>
@endsection

