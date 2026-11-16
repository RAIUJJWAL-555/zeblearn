@extends('layouts.admin')

@section('title', 'Add New Instructor | ZebLearn LMS')
@section('page-title', 'Add Instructor')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- BREADCRUMBS & HEADER --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.instructors.index') }}" class="hover:text-[#FB6221] transition-colors">Instructors</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">New</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                Register New Instructor
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Create a faculty member profile with academic credentials and teaching specialization.
            </p>
        </div>

        <x-ui.button
            href="{{ route('admin.instructors.index') }}"
            variant="secondary"
            size="sm"
            icon="arrow-left"
        >
            Back to Directory
        </x-ui.button>
    </div>

    {{-- ERROR SUMMARY ALERT --}}
    @if ($errors->any())
        <x-ui.alert variant="danger" dismissible>
            <div class="space-y-1">
                <strong class="font-bold block">Please resolve the following input issues:</strong>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </x-ui.alert>
    @endif

    {{-- CREATE FORM --}}
    <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs">
        <form method="POST" action="{{ route('admin.instructors.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- SECTION 1: ACCOUNT & CONTACT INFO --}}
            <div class="space-y-4">
                <h3 class="font-heading font-bold text-base text-slate-900 border-b border-slate-100 pb-2">
                    1. Account &amp; Identity
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="e.g. Dr. Rajesh Sharma"
                            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border @error('name') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                        >
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="rajesh.sharma@zeblearn.com"
                            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border @error('email') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                        >
                        @error('email')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Initial Password <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            minlength="8"
                            placeholder="Minimum 8 characters"
                            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border @error('password') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                        >
                        @error('password')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Phone / WhatsApp
                        </label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="+91 98765 43210"
                            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                        >
                        @error('phone')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 2: PROFESSIONAL & ACADEMIC CREDENTIALS --}}
            <div class="space-y-4 pt-2">
                <h3 class="font-heading font-bold text-base text-slate-900 border-b border-slate-100 pb-2">
                    2. Professional Designation &amp; Academic Expertise
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="designation" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Professional Designation
                        </label>
                        <input
                            type="text"
                            id="designation"
                            name="designation"
                            value="{{ old('designation') }}"
                            placeholder="e.g. Principal SAP S/4HANA Architect"
                            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                        >
                    </div>

                    <div>
                        <label for="specialization" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Specialization Area
                        </label>
                        <input
                            type="text"
                            id="specialization"
                            name="specialization"
                            value="{{ old('specialization') }}"
                            placeholder="e.g. SAP FICO, S/4HANA Finance, Controlling"
                            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                        >
                    </div>

                    <div>
                        <label for="experience_years" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Industry Experience (Years)
                        </label>
                        <input
                            type="number"
                            id="experience_years"
                            name="experience_years"
                            value="{{ old('experience_years') }}"
                            min="0"
                            max="60"
                            placeholder="e.g. 12"
                            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                        >
                    </div>

                    <div>
                        <label for="qualification" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Academic &amp; Professional Qualifications
                        </label>
                        <input
                            type="text"
                            id="qualification"
                            name="qualification"
                            value="{{ old('qualification') }}"
                            placeholder="e.g. M.Tech (CS), SAP Certified Application Associate"
                            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                        >
                    </div>
                </div>

                <div>
                    <label for="bio" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Professional Biography
                    </label>
                    <textarea
                        id="bio"
                        name="bio"
                        rows="4"
                        placeholder="Detailed background, corporate implementation experience, and teaching philosophy..."
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                    >{{ old('bio') }}</textarea>
                </div>
            </div>

            {{-- SECTION 3: STATUS & AVATAR --}}
            <div class="space-y-4 pt-2">
                <h3 class="font-heading font-bold text-base text-slate-900 border-b border-slate-100 pb-2">
                    3. Account Status &amp; Profile Photo
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label for="status" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Account Access Status <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="status"
                            name="status"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                        >
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Authorized to login and teach)</option>
                            <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended (Access blocked)</option>
                        </select>
                    </div>

                    <div>
                        <label for="avatar" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Profile Photo (Optional)
                        </label>
                        <input
                            type="file"
                            id="avatar"
                            name="avatar"
                            accept="image/png,image/jpeg,image/webp"
                            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-[#FB6221] hover:file:bg-orange-100"
                        >
                        <p class="text-[11px] text-slate-400 mt-1">JPEG, PNG or WebP up to 2MB.</p>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <x-ui.button
                    href="{{ route('admin.instructors.index') }}"
                    variant="ghost"
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
                    Create Instructor Account
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection

