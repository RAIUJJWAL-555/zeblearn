@extends('layouts.instructor')

@section('title', 'Edit Faculty Profile | ZebLearn LMS')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    {{-- BREADCRUMBS & TOP BAR --}}
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
            <a href="{{ route('instructor.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Instructor</a>
            <span>/</span>
            <a href="{{ route('instructor.profile.show') }}" class="hover:text-[#FB6221] transition-colors">Faculty Profile</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Edit</span>
        </nav>
        <x-ui.button
            href="{{ route('instructor.profile.show') }}"
            variant="secondary"
            size="sm"
            icon="arrow-left"
        >
            Back to Profile
        </x-ui.button>
    </div>

    {{-- FLASH MESSAGES & VALIDATION ERRORS --}}
    @if (session('success'))
        <x-ui.alert variant="success" dismissible>
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if ($errors->any())
        <x-ui.alert variant="danger" dismissible>
            <p class="font-bold mb-1">Please correct the following errors:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    {{-- 1. MAIN FACULTY PROFILE UPDATE FORM --}}
    <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading tracking-tight">
                    Edit Faculty Profile
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Update your professional profile details, teaching qualifications, and contact information.
                </p>
            </div>

            {{-- PROFILE PHOTO UPLOAD / REMOVE SECTION --}}
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center gap-6">
                <div class="relative shrink-0">
                    @if ($instructor->avatarUrl())
                        <img
                            src="{{ $instructor->avatarUrl() }}"
                            alt="{{ $instructor->name }}"
                            class="w-20 h-20 rounded-2xl object-cover border border-slate-200 shadow-sm"
                        />
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-[#0A0A0A] to-slate-800 text-white flex items-center justify-center font-heading font-black text-2xl shadow-sm border border-slate-700">
                            {{ strtoupper(substr($instructor->name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 space-y-2">
                    <label for="avatar" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono">
                        Faculty Photo
                    </label>
                    <input
                        type="file"
                        name="avatar"
                        id="avatar"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-[#FB6221] hover:file:bg-orange-100 cursor-pointer"
                    />
                    <p class="text-[11px] text-slate-400 font-sans">
                        Accepted formats: JPEG, PNG, WEBP. Maximum file size: 2 MB.
                    </p>
                </div>

                @if ($instructor->avatar_path)
                    <div class="sm:self-center">
                        <button
                            type="submit"
                            form="delete-instructor-avatar-form"
                            class="text-xs font-semibold text-rose-600 hover:text-rose-700 hover:underline cursor-pointer"
                        >
                            Remove Photo
                        </button>
                    </div>
                @endif
            </div>

            {{-- FORM FIELDS GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-4 border-t border-slate-100">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $instructor->name) }}"
                        required
                        class="w-full px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    />
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                        Email Address (Fixed)
                    </label>
                    <input
                        type="email"
                        id="email"
                        value="{{ $instructor->email }}"
                        disabled
                        class="w-full px-4 py-2.5 text-xs sm:text-sm bg-slate-100 border border-slate-200 rounded-xl text-slate-500 cursor-not-allowed"
                    />
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                        Phone Number
                    </label>
                    <input
                        type="tel"
                        name="phone"
                        id="phone"
                        value="{{ old('phone', $instructor->phone) }}"
                        placeholder="+91 98765 43210"
                        class="w-full px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    />
                </div>

                <div>
                    <label for="designation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                        Academic Designation
                    </label>
                    <input
                        type="text"
                        name="designation"
                        id="designation"
                        value="{{ old('designation', $instructor->designation) }}"
                        placeholder="e.g. Lead SAP Architect"
                        class="w-full px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    />
                </div>

                <div>
                    <label for="specialization" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                        Area of Specialization
                    </label>
                    <input
                        type="text"
                        name="specialization"
                        id="specialization"
                        value="{{ old('specialization', $instructor->specialization) }}"
                        placeholder="e.g. S/4HANA Finance, Workday HCM"
                        class="w-full px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    />
                </div>

                <div>
                    <label for="experience_years" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                        Years of Industry Experience
                    </label>
                    <input
                        type="number"
                        name="experience_years"
                        id="experience_years"
                        min="0"
                        max="60"
                        value="{{ old('experience_years', $instructor->experience_years) }}"
                        class="w-full px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    />
                </div>

                <div class="sm:col-span-2">
                    <label for="qualification" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                        Academic Qualifications &amp; Industry Certifications
                    </label>
                    <input
                        type="text"
                        name="qualification"
                        id="qualification"
                        value="{{ old('qualification', $instructor->qualification) }}"
                        placeholder="e.g. M.Tech in CS, SAP Certified Associate, PMP"
                        class="w-full px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    />
                </div>

                <div class="sm:col-span-2">
                    <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                        Faculty Biography
                    </label>
                    <textarea
                        name="bio"
                        id="bio"
                        rows="4"
                        placeholder="Describe your enterprise consulting background, teaching methodologies, and industry experience..."
                        class="w-full px-4 py-3 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all resize-y"
                    >{{ old('bio', $instructor->bio) }}</textarea>
                </div>
            </div>

            {{-- FORM ACTIONS --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <x-ui.button
                    href="{{ route('instructor.profile.show') }}"
                    variant="secondary"
                    size="md"
                >
                    Cancel
                </x-ui.button>
                <x-ui.button
                    type="submit"
                    variant="primary"
                    size="md"
                    icon="check"
                >
                    Save Faculty Changes
                </x-ui.button>
            </div>
        </x-ui.card>
    </form>

    {{-- SEPARATE HIDDEN FORM FOR AVATAR DELETION --}}
    @if ($instructor->avatar_path)
        <form id="delete-instructor-avatar-form" method="POST" action="{{ route('instructor.profile.avatar.destroy') }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif

    {{-- 2. PASSWORD SECURITY UPDATE CARD --}}
    <div id="password-section">
        <form method="POST" action="{{ route('instructor.profile.password.update') }}">
            @csrf
            @method('PUT')

            <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-slate-900 font-heading tracking-tight">
                        Change Faculty Account Password
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Use a strong, unique password to secure grading access and student records.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-4 border-t border-slate-100">
                    <div class="sm:col-span-2">
                        <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                            Current Password <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="current_password"
                            id="current_password"
                            required
                            autocomplete="current-password"
                            class="w-full sm:max-w-md px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                        />
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                            New Password <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                        />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                            Confirm New Password <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                        />
                    </div>
                </div>

                <div class="pt-5 border-t border-slate-100 flex items-center justify-end">
                    <x-ui.button
                        type="submit"
                        variant="secondary"
                        size="md"
                        icon="lock-closed"
                    >
                        Update Password
                    </x-ui.button>
                </div>
            </x-ui.card>
        </form>
    </div>
</div>
@endsection

