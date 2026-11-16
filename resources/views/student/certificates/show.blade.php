@extends('layouts.app')

@section('title', 'Certificate Details - ' . $certificate->certificate_number . ' | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- BREADCRUMBS --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('student.certificates.index') }}" class="hover:text-[#FB6221] transition-colors">Certificates</a>
                <span>/</span>
                <span class="text-slate-900 font-bold truncate max-w-xs">{{ $certificate->certificate_number }}</span>
            </nav>
            <x-ui.button
                href="{{ route('student.certificates.index') }}"
                variant="secondary"
                size="sm"
                icon="arrow-left"
            >
                Back to Certificates
            </x-ui.button>
        </div>

        {{-- CERTIFICATE DETAIL CARD --}}
        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div class="space-y-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                        Official Credential
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                        {{ $course->name }}
                    </h1>
                </div>

                <div>
                    @if ($certificate->isValid())
                        <x-ui.badge variant="success" size="sm" dot>
                            Verified Active
                        </x-ui.badge>
                    @else
                        <x-ui.badge variant="error" size="sm" dot>
                            Revoked
                        </x-ui.badge>
                    @endif
                </div>
            </div>

            @if ($certificate->isRevoked())
                <div class="p-4 bg-red-50 rounded-2xl border border-red-200 text-sm text-red-800 space-y-1">
                    <strong class="font-bold block uppercase text-xs font-mono text-red-700">Certificate Revocation Notice</strong>
                    <p>This certificate was revoked on {{ $certificate->revoked_at?->format('F d, Y') }}.</p>
                    <p><strong>Reason:</strong> {{ $certificate->revocation_reason }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                    <span class="text-xs font-mono uppercase text-slate-400 block">Certificate Identifier</span>
                    <span class="font-mono text-base font-bold text-[#FB6221]">{{ $certificate->certificate_number }}</span>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                    <span class="text-xs font-mono uppercase text-slate-400 block">Issued On</span>
                    <strong class="text-slate-800 text-base font-sans">{{ $certificate->issued_at->format('F d, Y') }}</strong>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                    <span class="text-xs font-mono uppercase text-slate-400 block">Recipient</span>
                    <strong class="text-slate-800 text-base font-sans">{{ $student->name }}</strong>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                    <span class="text-xs font-mono uppercase text-slate-400 block">Accredited Body</span>
                    <strong class="text-slate-800 text-base font-sans">ZebLearn LMS Education Board</strong>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center gap-4">
                <x-ui.button
                    href="{{ route('student.certificates.download', $certificate) }}"
                    variant="primary"
                    size="md"
                    icon="download"
                >
                    Download Certificate PDF
                </x-ui.button>

                <x-ui.button
                    href="{{ route('certificates.verify', $certificate->certificate_number) }}"
                    variant="secondary"
                    size="md"
                    target="_blank"
                    rel="noopener"
                    icon="arrow-top-right"
                >
                    Public Verification Page
                </x-ui.button>
            </div>
        </x-ui.card>

    </div>
</div>
@endsection

