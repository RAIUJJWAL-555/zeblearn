@extends('layouts.admin')

@section('title', 'Payment Transactions & Reconciliation | ZebLearn LMS')
@section('page-title', 'Payments')

@section('content')
<div class="space-y-6">
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Payments</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                Payment Transactions &amp; Audit
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Real-time transaction logs, Razorpay order IDs, and student payment reconciliation.
            </p>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Transactions</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $stats['total'] }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-600">Total Paid (INR)</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-heading mt-1 block">
                ₹{{ number_format($stats['paid_sum_rupees']) }}
            </span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-600">Successful Payments</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $stats['paid_count'] }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-rose-600">Failed Attempts</span>
            <span class="text-2xl sm:text-3xl font-black text-rose-600 font-heading mt-1 block">{{ $stats['failed_count'] }}</span>
        </div>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <x-ui.card padding="sm">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <x-ui.icon name="search" size="sm" />
                </div>
                <input
                    type="text"
                    name="search"
                    aria-label="Search payments by student, order ID, payment ID, or course"
                    value="{{ $search }}"
                    placeholder="Search by student, order ID, payment ID, or course..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/80 pl-10 pr-4 py-2.5 text-xs text-slate-900 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 transition-all outline-hidden font-sans"
                />
            </div>

            <div class="sm:col-span-4">
                <select
                    name="course_id"
                    aria-label="Filter payments by course"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/80 px-3.5 py-2.5 text-xs text-slate-700 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 transition-all outline-hidden font-medium cursor-pointer"
                >
                    <option value="all">All Courses ({{ $courses->count() }})</option>
                    @foreach ($courses as $c)
                        <option value="{{ $c->id }}" {{ (string)$activeCourse === (string)$c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <select
                    name="status"
                    aria-label="Filter payments by status"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/80 px-3.5 py-2.5 text-xs text-slate-700 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 transition-all outline-hidden font-medium cursor-pointer"
                >
                    <option value="all" {{ $activeStatus === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="paid" {{ $activeStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="created" {{ $activeStatus === 'created' ? 'selected' : '' }}>Created</option>
                    <option value="pending" {{ $activeStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ $activeStatus === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ $activeStatus === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <div class="sm:col-span-1 flex items-center">
                <x-ui.button
                    type="submit"
                    variant="dark"
                    size="sm"
                    fullWidth
                    icon="filter"
                    title="Apply Filters"
                >
                    Filter
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- PAYMENTS TABLE --}}
    <x-ui.card padding="none">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6">Student</th>
                        <th class="py-3.5 px-6">Course</th>
                        <th class="py-3.5 px-6">Amount</th>
                        <th class="py-3.5 px-6">Gateway Identifiers</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6 whitespace-nowrap text-slate-600 font-mono">
                                {{ $payment->paid_at?->format('d M Y, h:i A') ?? $payment->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900 font-heading">{{ $payment->user->name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $payment->user->email }}</div>
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-800">
                                {{ $payment->course->name }}
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-900 font-heading text-sm font-mono">
                                {{ $payment->formatted_amount }}
                            </td>
                            <td class="py-4 px-6 font-mono text-[11px] text-slate-500">
                                <div><span class="text-slate-400">Order:</span> {{ $payment->gateway_order_id }}</div>
                                @if ($payment->gateway_payment_id)
                                    <div><span class="text-slate-400">Payment:</span> {{ $payment->gateway_payment_id }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
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
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <a
                                    href="{{ route('admin.payments.show', $payment) }}"
                                    class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors inline-block"
                                    title="View Audit Details"
                                >
                                    <x-ui.icon name="eye" size="xs" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-6 text-center text-slate-500">
                                <x-ui.empty-state
                                    title="No payment transactions found"
                                    description="Transactions will appear automatically upon Razorpay checkout order creation."
                                    icon="credit-card"
                                    :bordered="false"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $payments->links() }}
            </div>
        @endif
    </x-ui.card>
</div>
@endsection
