@extends('layouts.admin')

@section('title', 'Payment Audit #' . $payment->id . ' | ZebLearn LMS')
@section('page-title', 'Payment Audit')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- BREADCRUMBS --}}
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.payments.index') }}" class="hover:text-[#FB6221] transition-colors">Payments</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Transaction #{{ $payment->id }}</span>
        </nav>
        <x-ui.button
            href="{{ route('admin.payments.index') }}"
            variant="secondary"
            size="sm"
            icon="arrow-left"
        >
            Back to Payments
        </x-ui.button>
    </div>

    {{-- TOP SUMMARY CARD --}}
    <x-ui.card padding="md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span class="font-mono text-xs font-bold uppercase tracking-widest text-[#FB6221]">
                        TRANSACTION ID #{{ $payment->id }}
                    </span>
                    @if ($payment->isPaid())
                        <x-ui.badge variant="success" size="xs" dot>
                            Paid &amp; Verified
                        </x-ui.badge>
                    @elseif ($payment->isFailed())
                        <x-ui.badge variant="danger" size="xs">
                            Failed
                        </x-ui.badge>
                    @else
                        <x-ui.badge variant="neutral" size="xs">
                            {{ ucfirst($payment->status) }}
                        </x-ui.badge>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                    {{ $payment->formatted_amount }} <span class="text-xs font-mono text-slate-400 font-normal">INR</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Initiated {{ $payment->created_at->format('d M Y, h:i:s A') }} ({{ $payment->created_at->diffForHumans() }})
                </p>
            </div>

            @if ($payment->enrollment)
                <x-ui.button
                    href="{{ route('admin.enrollments.show', $payment->enrollment) }}"
                    variant="secondary"
                    size="sm"
                >
                    View Linked Enrollment &rarr;
                </x-ui.button>
            @endif
        </div>
    </x-ui.card>

    {{-- 2-COLUMN TRANSACTION DETAILS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- GATEWAY AUDIT DATA --}}
        <x-ui.card padding="md" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-heading font-bold text-base text-slate-900">
                    Gateway Audit Log (Razorpay)
                </h3>
                <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-400 uppercase">
                    <x-ui.icon name="shield-check" size="xs" class="text-slate-400" />
                    TLS Secured
                </span>
            </div>

            <div class="space-y-3.5 text-xs font-mono">
                <div>
                    <span class="text-slate-400 uppercase text-[10px] tracking-wider block mb-0.5">Gateway Order ID</span>
                    <span class="text-slate-900 font-bold select-all bg-slate-50 border border-slate-200/60 rounded px-2 py-1 block">{{ $payment->gateway_order_id }}</span>
                </div>
                <div>
                    <span class="text-slate-400 uppercase text-[10px] tracking-wider block mb-0.5">Gateway Payment ID</span>
                    <span class="text-slate-900 font-bold select-all bg-slate-50 border border-slate-200/60 rounded px-2 py-1 block">{{ $payment->gateway_payment_id ?? 'None (Uncaptured / Incomplete)' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 uppercase text-[10px] tracking-wider block mb-0.5">Payment Method</span>
                    <span class="text-slate-700 font-sans uppercase font-medium">{{ $payment->payment_method ?? 'Not reported' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 uppercase text-[10px] tracking-wider block mb-0.5">Cryptographic Verification</span>
                    @if ($payment->isPaid() && $payment->verified_at)
                        <span class="inline-flex items-center gap-1.5 text-emerald-600 font-sans font-semibold text-xs">
                            <x-ui.icon name="check-circle" size="xs" class="text-emerald-500" />
                            Verified (HMAC-SHA256 Match)
                        </span>
                    @elseif ($payment->isFailed())
                        <span class="inline-flex items-center gap-1.5 text-rose-600 font-sans font-semibold text-xs">
                            <x-ui.icon name="x-circle" size="xs" class="text-rose-500" />
                            Verification Failed / Rejected
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-slate-500 font-sans text-xs">
                            <x-ui.icon name="clock" size="xs" class="text-slate-400" />
                            Pending Verification
                        </span>
                    @endif
                </div>
                <div>
                    <span class="text-slate-400 uppercase text-[10px] tracking-wider block mb-0.5">Verified Timestamp</span>
                    <span class="text-slate-700 font-sans">{{ $payment->verified_at?->format('d M Y, h:i:s A') ?? 'Not verified' }}</span>
                </div>
                @if ($payment->failure_reason)
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200">
                        <span class="text-rose-500 uppercase text-[10px] font-bold block mb-1">Failure Reason</span>
                        <span class="text-rose-700 font-sans font-medium">{{ $payment->failure_reason }}</span>
                    </div>
                @endif
            </div>
        </x-ui.card>

        {{-- STUDENT & COURSE ENTITY --}}
        <x-ui.card padding="md" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-heading font-bold text-base text-slate-900">
                    Student &amp; Course Entity
                </h3>
                <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-400 uppercase">
                    <x-ui.icon name="academic-cap" size="xs" class="text-slate-400" />
                    Enrolled Unit
                </span>
            </div>

            <div class="space-y-4 text-xs">
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] tracking-wider block mb-1">Student Details</span>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-xs text-slate-700 shrink-0">
                            {{ strtoupper(substr($payment->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <strong class="text-slate-900 text-sm font-heading block">{{ $payment->user->name }}</strong>
                            <span class="text-slate-500 font-mono text-[11px]">{{ $payment->user->email }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] tracking-wider block mb-1">Course Name</span>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                        <strong class="text-slate-900 text-sm font-heading block">{{ $payment->course->name }}</strong>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-slate-500">{{ $payment->course->category_badge }}</span>
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-600 font-semibold font-mono">{{ $payment->course->display_price }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] tracking-wider block mb-1">Linked Enrollment Status</span>
                    @if ($payment->enrollment)
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-200/60">
                            <span class="font-mono text-xs text-slate-600">ID #{{ $payment->enrollment->id }}</span>
                            @if ($payment->enrollment->isApproved())
                                <x-ui.badge variant="success" size="xs" dot>
                                    {{ $payment->enrollment->status_label }}
                                </x-ui.badge>
                            @elseif ($payment->enrollment->isPending())
                                <x-ui.badge variant="warning" size="xs" dot dotPulse>
                                    {{ $payment->enrollment->status_label }}
                                </x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral" size="xs">
                                    {{ $payment->enrollment->status_label }}
                                </x-ui.badge>
                            @endif
                        </div>
                    @else
                        <span class="text-slate-400 font-mono italic">No linked enrollment record</span>
                    @endif
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
@endsection
