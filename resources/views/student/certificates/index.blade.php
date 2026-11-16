@extends('layouts.app')

@section('title', 'My Certificates | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- BREADCRUMBS & TOP BAR --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">My Certificates</span>
            </nav>
            <x-ui.button
                href="{{ route('student.dashboard') }}"
                variant="secondary"
                size="sm"
                icon="arrow-left"
            >
                Back to Dashboard
            </x-ui.button>
        </div>

        {{-- FLASH MESSAGES --}}
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>
                {{ session('success') }}
            </x-ui.alert>
        @endif

        {{-- HEADER CARD --}}
        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                            Verified Credentials
                        </span>
                        <x-ui.badge variant="primary" size="xs">
                            {{ $totalCertificates }} {{ Str::plural('Certificate', $totalCertificates) }}
                        </x-ui.badge>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                        Course Completion Certificates
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-sans">
                        Official, verifiable certificates earned by successfully completing all course curriculum lessons.
                    </p>
                </div>
            </div>
        </x-ui.card>

        {{-- CERTIFICATES LISTING --}}
        @if ($certificates->isEmpty())
            <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs">
                <x-ui.empty-state
                    title="No Certificates Earned Yet"
                    description="Complete all published lessons in your enrolled courses. Once 100% of lessons are completed, your official certificate of completion will be generated and made available here automatically."
                    icon="school"
                >
                    <x-ui.button
                        href="{{ route('student.dashboard') }}"
                        variant="primary"
                        size="sm"
                        icon="arrow-right"
                    >
                        Go to My Courses
                    </x-ui.button>
                </x-ui.empty-state>
            </x-ui.card>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($certificates as $certificate)
                    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs flex flex-col justify-between space-y-5 hover:border-slate-300 transition-all">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $certificate->course?->category_badge ?? 'Enterprise' }}
                                </span>

                                @if ($certificate->isValid())
                                    <x-ui.badge variant="success" size="xs" dot>
                                        Verified Active
                                    </x-ui.badge>
                                @else
                                    <x-ui.badge variant="error" size="xs" dot>
                                        Revoked
                                    </x-ui.badge>
                                @endif
                            </div>

                            <div>
                                <h3 class="font-heading font-bold text-lg text-slate-900 leading-snug">
                                    {{ $certificate->course?->name ?? 'Course' }}
                                </h3>
                                <p class="text-xs text-slate-500 font-sans mt-1">
                                    Issued on {{ $certificate->issued_at->format('F d, Y') }}
                                </p>
                            </div>

                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                                <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400 block">
                                    Credential ID
                                </span>
                                <span class="font-mono text-sm font-bold text-[#FB6221]">
                                    {{ $certificate->certificate_number }}
                                </span>
                            </div>

                            @if ($certificate->isRevoked())
                                <div class="p-2.5 bg-red-50 rounded-lg border border-red-200 text-xs text-red-700">
                                    <strong>Revoked:</strong> {{ $certificate->revocation_reason ?: 'Administrative decision.' }}
                                </div>
                            @endif
                        </div>

                        {{-- ACTIONS --}}
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                            <x-ui.button
                                href="{{ route('student.certificates.download', $certificate) }}"
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
                                Verify Online
                            </x-ui.button>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>

            {{-- PAGINATION --}}
            <div class="pt-4">
                {{ $certificates->links() }}
            </div>
        @endif

    </div>
</div>
@endsection

