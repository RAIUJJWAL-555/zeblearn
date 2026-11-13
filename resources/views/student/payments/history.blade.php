@extends('layouts.app')

@section('title', 'My Payment History | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- BREADCRUMBS & TOP BAR --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Payment History</span>
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

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                    Payment History &amp; Receipts
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Verified online tuition transactions and access records.
                </p>
            </div>
            <x-ui.button
                href="{{ route('courses.index') }}"
                variant="secondary"
                size="sm"
                icon="book"
            >
                Browse More Courses
            </x-ui.button>
        </div>

        {{-- TRANSACTIONS TABLE --}}
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-6">Date</th>
                            <th class="py-3.5 px-6">Course</th>
                            <th class="py-3.5 px-6">Amount</th>
                            <th class="py-3.5 px-6">Payment ID</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap text-slate-600 font-mono">
                                    {{ $payment->paid_at?->format('d M Y, h:i A') ?? $payment->created_at->format('d M Y') }}
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 font-heading">
                                    {{ $payment->course->name }}
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 font-heading text-sm">
                                    {{ $payment->formatted_amount }}
                                </td>
                                <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
                                    {{ $payment->gateway_payment_id ?? $payment->gateway_order_id }}
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    @if ($payment->isPaid())
                                        <x-ui.badge variant="success" size="sm" dot>
                                            Paid &amp; Verified
                                        </x-ui.badge>
                                    @elseif ($payment->isFailed())
                                        <x-ui.badge variant="danger" size="sm" dot>
                                            Failed
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral" size="sm">
                                            {{ ucfirst($payment->status) }}
                                        </x-ui.badge>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    @if ($payment->isPaid())
                                        <a
                                            href="{{ route('student.payments.receipt', $payment) }}"
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-[#FB6221] hover:underline"
                                        >
                                            <span>View Receipt</span>
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-slate-300 font-mono">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    <p class="text-sm font-semibold text-slate-800">No payment records found</p>
                                    <p class="text-xs text-slate-400 mt-1">You have not completed any online course purchases yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

