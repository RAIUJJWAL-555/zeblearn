@extends('layouts.admin')

@section('title', "User: {$user->name} | ZebLearn LMS")
@section('page-title', 'User Details')

@section('content')
<div class="space-y-6">
    {{-- BREADCRUMBS & TOP NAVIGATION --}}
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.users.index') }}" class="hover:text-[#FB6221] transition-colors">Users</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">{{ $user->name }}</span>
        </nav>
        <div class="flex items-center gap-2">
            <x-ui.button
                href="{{ route('admin.users.index') }}"
                variant="secondary"
                size="sm"
                icon="arrow-left"
            >
                Back to Directory
            </x-ui.button>
            <x-ui.button
                href="{{ route('admin.users.edit', $user) }}"
                variant="primary"
                size="sm"
                icon="pencil"
            >
                Edit User
            </x-ui.button>
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

    {{-- USER PROFILE HEADER CARD --}}
    <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                @if ($user->avatarUrl())
                    <img
                        src="{{ $user->avatarUrl() }}"
                        alt="{{ $user->name }}"
                        class="w-20 h-20 rounded-2xl object-cover border border-slate-200 shadow-sm shrink-0"
                    />
                @else
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-[#0A0A0A] to-slate-800 text-white flex items-center justify-center font-heading font-black text-3xl shadow-sm shrink-0 border border-slate-700">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif

                <div class="space-y-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        @if ($user->isAdmin())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-slate-900 text-white">
                                Super Administrator
                            </span>
                        @elseif ($user->isInstructor())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-100 text-[#FB6221]">
                                Faculty Instructor
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-blue-100 text-blue-700">
                                Student Account
                            </span>
                        @endif

                        @if ($user->isActive())
                            <x-ui.badge variant="success" size="xs" dot>
                                Active
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="danger" size="xs" dot>
                                Suspended
                            </x-ui.badge>
                        @endif

                        <span class="text-[11px] font-mono text-slate-400">
                            ID: #USR-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="text-[11px] font-mono text-slate-400">
                            Registered: {{ $user->created_at->format('d M Y, h:i A') }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                        {{ $user->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-sans">
                        {{ $user->email }} • {{ $user->phone ?: 'No phone provided' }}
                    </p>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                @if (auth()->id() !== $user->id)
                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <x-ui.button
                            type="submit"
                            variant="{{ $user->isActive() ? 'secondary' : 'primary' }}"
                            size="sm"
                            onclick="return confirm('Are you sure you want to {{ $user->isActive() ? 'suspend' : 'activate' }} this user?')"
                        >
                            {{ $user->isActive() ? 'Suspend Account' : 'Activate Account' }}
                        </x-ui.button>
                    </form>

                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <x-ui.button
                            type="submit"
                            variant="ghost"
                            size="sm"
                            class="text-rose-600 hover:bg-rose-50 hover:text-rose-700 border border-rose-200"
                            onclick="return confirm('Permanently delete this user? Academic or financial transaction references will safely block deletion.')"
                        >
                            Delete User
                        </x-ui.button>
                    </form>
                @endif
            </div>
        </div>
    </x-ui.card>

    {{-- USER BIOGRAPHY / INSTRUCTOR CREDENTIALS (IF PRESENT) --}}
    @if ($user->bio || $user->isInstructor())
        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900 font-heading uppercase tracking-wider">
                {{ $user->isInstructor() ? 'Faculty Credentials & Background' : 'User Biography' }}
            </h2>

            @if ($user->isInstructor())
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 py-3 border-y border-slate-100 text-xs">
                    <div>
                        <span class="block text-[10px] font-mono uppercase text-slate-400">Designation</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">{{ $user->designation ?: 'Lead Instructor' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-mono uppercase text-slate-400">Specialization</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">{{ $user->specialization ?: 'ERP Specialist' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-mono uppercase text-slate-400">Experience</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">{{ $user->experience_years ? $user->experience_years.' Years' : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-mono uppercase text-slate-400">Qualifications</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">{{ $user->qualification ?: 'N/A' }}</span>
                    </div>
                </div>
            @endif

            @if ($user->bio)
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    {{ $user->bio }}
                </p>
            @endif
        </x-ui.card>
    @endif

    {{-- SECTION A: STUDENT ENROLLMENTS --}}
    @if ($user->isStudent())
        <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 font-heading uppercase tracking-wider">
                        Course Enrollments ({{ $user->enrollments->count() }})
                    </h3>
                    <p class="text-[11px] text-slate-500">History of requested, approved, and cancelled course enrollments.</p>
                </div>
            </div>

            @if ($user->enrollments->isEmpty())
                <p class="text-xs text-slate-400 py-8 text-center">No enrollment records found for this student.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] font-mono uppercase tracking-wider text-slate-500 border-b border-slate-100">
                                <th class="py-2.5 px-5 font-bold">Course</th>
                                <th class="py-2.5 px-4 font-bold">Status</th>
                                <th class="py-2.5 px-4 font-bold">Requested</th>
                                <th class="py-2.5 px-4 font-bold">Approved</th>
                                <th class="py-2.5 px-4 font-bold">Admin Note</th>
                                <th class="py-2.5 px-5 font-bold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($user->enrollments as $enrollment)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-5 font-bold text-slate-900">
                                        {{ $enrollment->course?->name ?? 'Course' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @if ($enrollment->status === 'approved')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                                Approved
                                            </span>
                                        @elseif ($enrollment->status === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-rose-100 text-rose-800">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                        {{ $enrollment->requested_at?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                        {{ $enrollment->approved_at?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 max-w-xs truncate">
                                        {{ $enrollment->admin_note ?: '—' }}
                                    </td>
                                    <td class="py-3 px-5 text-right">
                                        <x-ui.button
                                            href="{{ route('admin.enrollments.show', $enrollment->id) }}"
                                            variant="ghost"
                                            size="xs"
                                        >
                                            Review
                                        </x-ui.button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.card>
    @endif

    {{-- SECTION B: FINANCIAL PAYMENTS HISTORY --}}
    @if ($user->payments->isNotEmpty())
        <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 font-heading uppercase tracking-wider">
                        Payment Transactions ({{ $user->payments->count() }})
                    </h3>
                    <p class="text-[11px] text-slate-500">Verified Razorpay fee transactions and audit records.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-mono uppercase tracking-wider text-slate-500 border-b border-slate-100">
                            <th class="py-2.5 px-5 font-bold">Transaction / Order</th>
                            <th class="py-2.5 px-4 font-bold">Course</th>
                            <th class="py-2.5 px-4 font-bold">Amount</th>
                            <th class="py-2.5 px-4 font-bold">Status</th>
                            <th class="py-2.5 px-4 font-bold">Paid Date</th>
                            <th class="py-2.5 px-5 font-bold text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($user->payments as $payment)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-5 font-mono text-[11px] text-slate-700">
                                    {{ $payment->gateway_payment_id ?: $payment->gateway_order_id }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    {{ $payment->course?->name ?? 'Course' }}
                                </td>
                                <td class="py-3 px-4 font-heading font-black text-slate-900">
                                    ₹{{ number_format($payment->amount / 100, 2) }}
                                </td>
                                <td class="py-3 px-4">
                                    <x-ui.badge variant="{{ $payment->isPaid() ? 'success' : 'default' }}" size="xs">
                                        {{ strtoupper($payment->status) }}
                                    </x-ui.badge>
                                </td>
                                <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                    {{ $payment->paid_at?->format('d M Y, h:i A') ?? '—' }}
                                </td>
                                <td class="py-3 px-5 text-right">
                                    <x-ui.button
                                        href="{{ route('admin.payments.show', $payment->id) }}"
                                        variant="ghost"
                                        size="xs"
                                    >
                                        View
                                    </x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @endif

    {{-- SECTION C: INSTRUCTOR ASSIGNED COURSES --}}
    @if ($user->isInstructor())
        <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 font-heading uppercase tracking-wider">
                        Assigned Courses ({{ $user->assignedCourses->count() }})
                    </h3>
                    <p class="text-[11px] text-slate-500">Courses where this instructor has grading &amp; student evaluation privileges.</p>
                </div>
                <x-ui.button
                    href="{{ route('admin.instructors.show', $user->id) }}"
                    variant="ghost"
                    size="xs"
                >
                    Manage Faculty Assignments &rarr;
                </x-ui.button>
            </div>

            @if ($user->assignedCourses->isEmpty())
                <p class="text-xs text-slate-400 py-8 text-center">No courses currently assigned to this instructor.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($user->assignedCourses as $c)
                        <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 font-heading">{{ $c->name }}</h4>
                                <p class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    Role: {{ $c->pivot->role_or_title ?: 'Lead Instructor' }} • Assigned {{ \Carbon\Carbon::parse($c->pivot->assigned_at)->format('d M Y') }}
                                </p>
                            </div>
                            <x-ui.button
                                href="{{ route('admin.courses.edit', $c->id) }}"
                                variant="secondary"
                                size="xs"
                            >
                                Course Settings
                            </x-ui.button>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    @endif

    {{-- SECTION D: CERTIFICATES ISSUED --}}
    @if ($user->certificates->isNotEmpty())
        <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 font-heading uppercase tracking-wider">
                    Issued Certificates ({{ $user->certificates->count() }})
                </h3>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($user->certificates as $cert)
                    <div class="p-4 flex items-center justify-between gap-4 text-xs">
                        <div>
                            <p class="font-bold text-slate-900">{{ $cert->course?->name ?? 'Course' }}</p>
                            <span class="font-mono text-[#FB6221] text-[11px]">{{ $cert->certificate_number }}</span>
                            <span class="text-slate-400 text-[10px] ml-2 font-mono">Issued {{ $cert->issued_at?->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-ui.button
                                href="{{ route('admin.certificates.show', $cert->id) }}"
                                variant="secondary"
                                size="xs"
                            >
                                Details
                            </x-ui.button>
                            <x-ui.button
                                href="{{ route('admin.certificates.download', $cert->id) }}"
                                variant="primary"
                                size="xs"
                            >
                                PDF
                            </x-ui.button>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    @endif
</div>
@endsection

