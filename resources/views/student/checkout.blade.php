@extends('layouts.app')

@section('title', 'Secure Checkout - ' . $course->name . ' | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- BREADCRUMBS --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('courses.index') }}" class="hover:text-[#FB6221] transition-colors">Courses</a>
                <span>/</span>
                <span class="text-slate-900 font-bold truncate max-w-sm">{{ $course->name }}</span>
                <span>/</span>
                <span class="text-[#FB6221]">Checkout</span>
            </nav>
            <a
                href="{{ route('courses.index') }}"
                class="text-xs font-semibold text-slate-600 hover:text-[#FB6221] flex items-center gap-1 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Courses</span>
            </a>
        </div>

        {{-- CHECKOUT CARD (2-COLUMN ORDER SUMMARY & GATEWAY) --}}
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-slate-100">

                {{-- LEFT COLUMN: COURSE SUMMARY (7 COLS) --}}
                <div class="md:col-span-7 p-6 sm:p-10 space-y-6">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200 mb-3">
                            {{ $course->category_badge }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                            {{ $course->name }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                            {{ $course->short_description }}
                        </p>
                    </div>

                    {{-- Course Features --}}
                    <div class="space-y-3 pt-4 border-t border-slate-100 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Delivery Mode</span>
                            <strong class="text-slate-800 font-medium">{{ $course->delivery_mode ?? 'Live Online + Sandbox Practice' }}</strong>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Curriculum Depth</span>
                            <strong class="text-slate-800 font-medium">{{ $course->display_modules }}</strong>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Batch Duration</span>
                            <strong class="text-slate-800 font-medium">{{ $course->display_duration }}</strong>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Admissions Status</span>
                            <strong class="text-emerald-600 font-medium flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Instant Auto-Enrollment on Payment
                            </strong>
                        </div>
                    </div>

                    {{-- Manual Approval Alternative --}}
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 text-xs text-slate-600 space-y-2">
                        <div class="font-bold text-slate-800">Prefer manual admissions review?</div>
                        <p class="leading-relaxed">
                            If your employer is sponsoring this tuition or you want to attend a demo session first, you can request manual admin approval instead.
                        </p>
                        <form action="{{ route('courses.enroll', $course) }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="text-[#FB6221] font-semibold hover:underline cursor-pointer"
                            >
                                Request Free Demo / Manual Enrollment &rarr;
                            </button>
                        </form>
                    </div>
                </div>

                {{-- RIGHT COLUMN: PAYMENT SUMMARY & RAZORPAY CTA (5 COLS) --}}
                <div class="md:col-span-5 p-6 sm:p-10 bg-slate-50/50 flex flex-col justify-between space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 font-heading uppercase tracking-wider text-xs font-mono text-slate-400">
                            Payment Summary
                        </h2>

                        <div class="mt-4 space-y-3 text-xs">
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Original Tuition</span>
                                <span class="line-through text-slate-400">₹{{ number_format($course->original_fee ?? 75000) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-emerald-600 font-medium">
                                <span>Academy Scholarship</span>
                                <span>{{ $course->display_discount }}</span>
                            </div>
                            <div class="pt-3 border-t border-slate-200 flex items-baseline justify-between">
                                <span class="font-bold text-slate-900 text-sm">Total Payable</span>
                                <div class="text-right">
                                    <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                                        ₹{{ number_format($orderData['amount'] / 100, 2) }}
                                    </span>
                                    <span class="block text-[10px] text-slate-400 font-mono">INR (net payable tuition)</span>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden verification form submitted upon Razorpay success --}}
                        <form id="razorpay-verification-form" action="{{ route('student.checkout.verify', $course) }}" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" />
                            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" />
                            <input type="hidden" name="razorpay_signature" id="razorpay_signature" />
                        </form>

                        {{-- Razorpay Secure Pay Button --}}
                        <div class="mt-8 space-y-3">
                            <button
                                type="button"
                                id="rzp-button"
                                class="w-full py-3.5 px-6 rounded-xl bg-amber-400 hover:bg-amber-500 text-black font-sans text-xs font-bold uppercase tracking-wider shadow-md hover:shadow-lg transition-all duration-200 hover:scale-[1.01] flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Pay ₹{{ number_format($orderData['amount'] / 100) }} via Razorpay</span>
                            </button>

                            <div class="flex items-center justify-center gap-2 text-[11px] text-slate-400 font-mono text-center">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>256-bit Encrypted SSL • UPI, Cards, NetBanking</span>
                            </div>
                        </div>
                    </div>

                    {{-- Student Verification Reminder --}}
                    <div class="pt-4 border-t border-slate-100 text-[11px] text-slate-500">
                        Logged in as <strong class="text-slate-800">{{ $orderData['user_name'] }}</strong> ({{ $orderData['user_email'] }}). Course access is unlocked immediately upon transaction confirmation.
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const payBtn = document.getElementById('rzp-button');
    if (!payBtn) return;

    const options = {
        key: "{{ $orderData['key_id'] }}",
        amount: "{{ $orderData['amount'] }}",
        currency: "{{ $orderData['currency'] }}",
        name: "ZebLearn Academy",
        description: "{{ $orderData['course_name'] }} Tuition",
        image: "{{ asset('logo/favicon.webp') }}",
        order_id: "{{ $orderData['order_id'] }}",
        handler: function (response) {
            // Populate form and submit for server-side verification
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;

            payBtn.disabled = true;
            payBtn.innerText = 'Verifying Payment...';

            document.getElementById('razorpay-verification-form').submit();
        },
        prefill: {
            name: "{{ $orderData['user_name'] }}",
            email: "{{ $orderData['user_email'] }}",
            contact: "{{ $orderData['user_phone'] }}"
        },
        theme: {
            color: "#FB6221"
        }
    };

    const rzp = new Razorpay(options);

    payBtn.addEventListener('click', (e) => {
        e.preventDefault();
        rzp.open();
    });
});
</script>
@endpush

