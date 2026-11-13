@extends('layouts.admin')

@section('title', 'Enrollment #' . $enrollment->id . ' - ' . $enrollment->user->name . ' | ZebLearn LMS')
@section('page-title', 'Enrollment Request')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- BREADCRUMBS --}}
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.enrollments.index') }}" class="hover:text-[#FB6221] transition-colors">Enrollments</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Request #{{ $enrollment->id }}</span>
        </nav>
        <x-ui.button
            href="{{ route('admin.enrollments.index') }}"
            variant="secondary"
            size="sm"
            icon="arrow-left"
        >
            Back to Enrollments
        </x-ui.button>
    </div>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <x-ui.alert variant="success" dismissible>
            {{ session('success') }}
        </x-ui.alert>
    @endif

    {{-- TOP SUMMARY CARD --}}
    <x-ui.card padding="md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="font-mono text-xs font-bold uppercase tracking-widest text-[#FB6221]">
                        ENROLLMENT APPLICATION #{{ $enrollment->id }}
                    </span>
                    @if ($enrollment->isApproved())
                        <x-ui.badge variant="success" size="xs" dot>
                            Approved &amp; Active
                        </x-ui.badge>
                    @elseif ($enrollment->isPending())
                        <x-ui.badge variant="warning" size="xs" dot dotPulse>
                            Pending Admin Review
                        </x-ui.badge>
                    @elseif ($enrollment->isRejected())
                        <x-ui.badge variant="danger" size="xs">
                            Declined
                        </x-ui.badge>
                    @else
                        <x-ui.badge variant="neutral" size="xs">
                            {{ ucfirst($enrollment->status) }}
                        </x-ui.badge>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight mt-2">
                    {{ $enrollment->user->name }} &rarr; {{ $enrollment->course->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Requested on {{ $enrollment->requested_at?->format('d M Y, h:i A') }} ({{ $enrollment->requested_at?->diffForHumans() }})
                </p>
            </div>

            {{-- Direct Actions --}}
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                @if ($enrollment->isPending())
                    <form action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            size="sm"
                            onclick="return confirm('Approve this enrollment and grant course access?')"
                        >
                            Approve Enrollment
                        </x-ui.button>
                    </form>

                    <form action="{{ route('admin.enrollments.reject', $enrollment) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <x-ui.button
                            type="submit"
                            variant="danger-outline"
                            size="sm"
                            onclick="return confirm('Decline this enrollment request?')"
                        >
                            Decline
                        </x-ui.button>
                    </form>
                @elseif ($enrollment->isApproved())
                    <form action="{{ route('admin.enrollments.cancel', $enrollment) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <x-ui.button
                            type="submit"
                            variant="secondary"
                            size="sm"
                            onclick="return confirm('Cancel this approved student enrollment?')"
                        >
                            Cancel Enrollment
                        </x-ui.button>
                    </form>
                @elseif ($enrollment->isRejected() || $enrollment->isCancelled())
                    <form action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            size="sm"
                            onclick="return confirm('Reconsider and approve this enrollment?')"
                        >
                            Re-Approve Enrollment
                        </x-ui.button>
                    </form>
                @endif
            </div>
        </div>
    </x-ui.card>

    {{-- 2-COLUMN DETAILS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- STUDENT PROFILE CARD --}}
        <x-ui.card padding="md" class="space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h3 class="font-heading font-bold text-base text-slate-900">Student Profile</h3>
                <x-ui.badge variant="neutral" size="xs" class="font-mono font-bold uppercase">
                    {{ $enrollment->user->role }}
                </x-ui.badge>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Full Name</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $enrollment->user->name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Email Address</span>
                    <span class="font-medium text-slate-800 font-mono">{{ $enrollment->user->email }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Phone Number</span>
                    <span class="font-medium text-slate-800 font-mono">{{ $enrollment->user->phone ?? 'Not provided' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Account Status</span>
                    @if ($enrollment->user->isActive())
                        <x-ui.badge variant="success" size="xs" dot class="font-bold uppercase">
                            {{ $enrollment->user->status }}
                        </x-ui.badge>
                    @else
                        <x-ui.badge variant="danger" size="xs" class="font-bold uppercase">
                            {{ $enrollment->user->status }}
                        </x-ui.badge>
                    @endif
                </div>
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Member Since</span>
                    <span class="text-slate-600">{{ $enrollment->user->created_at?->format('d M Y') }}</span>
                </div>
                @if ($enrollment->user->bio)
                    <div>
                        <span class="text-slate-400 font-mono uppercase text-[10px] block">Bio / Notes</span>
                        <p class="text-slate-600 leading-relaxed mt-0.5">{{ $enrollment->user->bio }}</p>
                    </div>
                @endif
            </div>
        </x-ui.card>

        {{-- COURSE INFORMATION CARD --}}
        <x-ui.card padding="md" class="space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h3 class="font-heading font-bold text-base text-slate-900">Course Information</h3>
                <a href="{{ route('admin.courses.curriculum', $enrollment->course) }}" class="text-[11px] font-semibold text-[#FB6221] hover:underline inline-flex items-center gap-1">
                    <span>View Curriculum</span>
                    <x-ui.icon name="arrow-right" size="xs" />
                </a>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Course Name</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $enrollment->course->name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Track Specialization</span>
                    <span class="font-medium text-slate-800">{{ $enrollment->course->category_label }} ({{ $enrollment->course->category_badge }})</span>
                </div>
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Tuition Fee</span>
                    <span class="font-bold text-slate-900 text-sm font-heading">{{ $enrollment->course->display_price }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Curriculum Depth</span>
                    <span class="text-slate-700">{{ $enrollment->course->modules->count() }} Modules • {{ $enrollment->course->display_duration }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Course Availability</span>
                    <x-ui.badge variant="neutral" size="xs" class="font-bold uppercase">
                        {{ $enrollment->course->status_label }}
                    </x-ui.badge>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- ADMISSIONS REVIEW & NOTES CARD --}}
    <x-ui.card padding="md" class="space-y-4">
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-slate-900">Admissions Review &amp; Admin Notes</h3>
            @if ($enrollment->reviewer)
                <span class="text-xs text-slate-500 font-mono">
                    Reviewed by <strong class="text-slate-800">{{ $enrollment->reviewer->name }}</strong>
                </span>
            @endif
        </div>

        <form action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="admin_note" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Internal Admin Note / Admission Remarks
                </label>
                <textarea
                    id="admin_note"
                    name="admin_note"
                    rows="3"
                    placeholder="e.g. Student confirmed enrollment interest via phone. Batch access scheduled for Saturday cohort."
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                >{{ old('admin_note', $enrollment->admin_note) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <x-ui.button
                    type="submit"
                    variant="dark"
                    size="sm"
                >
                    Update Note &amp; Confirm Approval
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
