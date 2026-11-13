@extends('layouts.app')

@section('title', 'Payment Receipt #' . $payment->id . ' | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- BREADCRUMBS & PRINT ACTION --}}
        <div class="flex items-center justify-between no-print">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('student.payments.history') }}" class="hover:text-[#FB6221] transition-colors">Payments</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Receipt #{{ $payment->id }}</span>
            </nav>
            <div class="flex items-center gap-2">
                <x-ui.button
                    type="button"
                    onclick="window.print()"
                    variant="secondary"
                    size="sm"
                    icon="printer"
                >
                    Print Receipt
                </x-ui.button>
            </div>
        </div>

        {{-- PRINTABLE RECEIPT CARD --}}
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-sm space-y-8" id="receipt-card">
            {{-- RECEIPT TOP HEADER --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                <div>
                    <img src="{{ asset('logo/logo2-400.webp') }}" alt="ZebLearn" class="h-10 w-auto object-contain" />
                    <p class="text-[11px] font-mono text-slate-400 mt-2">Enterprise Learning Management System</p>
                </div>
                <div class="sm:text-right">
                    <x-ui.badge variant="success" size="sm" dot class="mb-2">
                        Verified Payment
                    </x-ui.badge>
                    <h2 class="font-heading font-black text-slate-900 text-lg tracking-tight">
                        RECEIPT #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                    </h2>
                    <p class="text-xs text-slate-400 font-mono">
                        {{ $payment->paid_at?->format('d M Y, h:i A') ?? $payment->created_at->format('d M Y') }}
                    </p>
                </div>
            </div>

            {{-- 2-COLUMN PARTIES SUMMARY --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] block mb-1">Student Details</span>
                    <strong class="text-slate-900 font-heading text-sm block">{{ $user->name }}</strong>
                    <span class="text-slate-600 font-mono block">{{ $user->email }}</span>
                    @if ($user->phone)
                        <span class="text-slate-500 font-mono block">{{ $user->phone }}</span>
                    @endif
                </div>

                <div class="sm:text-right">
                    <span class="text-slate-400 font-mono uppercase text-[10px] block mb-1">Payment Method &amp; Gateway</span>
                    <strong class="text-slate-900 font-heading text-sm block">Razorpay Payments (India)</strong>
                    <span class="text-slate-600 font-mono text-[11px] block">Payment ID: {{ $payment->gateway_payment_id ?? '—' }}</span>
                    <span class="text-slate-500 font-mono text-[11px] block">Order ID: {{ $payment->gateway_order_id }}</span>
                </div>
            </div>

            {{-- LINE ITEMS TABLE --}}
            <div class="border border-slate-100 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-mono uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-5">Description</th>
                            <th class="py-3 px-5">Duration</th>
                            <th class="py-3 px-5 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-4 px-5">
                                <strong class="text-slate-900 font-heading text-sm block">{{ $course->name }}</strong>
                                <span class="text-slate-500 text-[11px]">{{ $course->category_label }} • Enterprise LMS Access</span>
                            </td>
                            <td class="py-4 px-5 text-slate-600 font-mono">
                                {{ $course->display_duration }}
                            </td>
                            <td class="py-4 px-5 text-right font-heading font-black text-slate-900 text-sm">
                                {{ $payment->formatted_amount }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50/70 border-t border-slate-200/80">
                        <tr>
                            <td colspan="2" class="py-3.5 px-5 font-bold text-slate-800 text-xs">Total Amount Paid (INR)</td>
                            <td class="py-3.5 px-5 text-right font-heading font-black text-slate-900 text-base">
                                {{ $payment->formatted_amount }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- FOOTER ADVISORY --}}
            <div class="border-t border-slate-100 pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-slate-500">
                <p class="leading-relaxed">
                    This is an electronically generated tuition fee receipt issued by ZebLearn Academy for online LMS course access.
                </p>
                <a
                    href="{{ route('student.courses.show', $course) }}"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-[#FB6221] hover:underline shrink-0 no-print"
                >
                    <span>Go to Course Learning Area</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

