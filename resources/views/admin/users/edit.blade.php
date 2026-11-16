@extends('layouts.admin')

@section('title', "Edit User: {$user->name} | ZebLearn LMS")
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- BREADCRUMBS & TOP BAR --}}
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.users.index') }}" class="hover:text-[#FB6221] transition-colors">Users</a>
            <span>/</span>
            <a href="{{ route('admin.users.show', $user) }}" class="hover:text-[#FB6221] transition-colors">{{ $user->name }}</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Edit</span>
        </nav>
        <x-ui.button
            href="{{ route('admin.users.show', $user) }}"
            variant="secondary"
            size="sm"
            icon="arrow-left"
        >
            Back to User
        </x-ui.button>
    </div>

    {{-- FLASH MESSAGES & VALIDATION ERRORS --}}
    @if (session('error'))
        <x-ui.alert variant="danger" dismissible>
            {{ session('error') }}
        </x-ui.alert>
    @endif

    @if ($errors->any())
        <x-ui.alert variant="danger" dismissible>
            <p class="font-bold mb-1">Please fix the following validation errors:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    {{-- USER EDIT FORM --}}
    <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading tracking-tight">
                    Edit User Account Information
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Manage credentials, role assignments, account status, and profile information for this user.
                </p>
            </div>

            {{-- PROFILE PHOTO UPLOAD SECTION --}}
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center gap-6">
                <div class="relative shrink-0">
                    @if ($user->avatarUrl())
                        <img
                            src="{{ $user->avatarUrl() }}"
                            alt="{{ $user->name }}"
                            class="w-20 h-20 rounded-2xl object-cover border border-slate-200 shadow-sm"
                        />
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-[#0A0A0A] to-slate-800 text-white flex items-center justify-center font-heading font-black text-2xl shadow-sm border border-slate-700">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 space-y-2">
                    <label for="avatar" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono">
                        User Avatar Photo
                    </label>
                    <input
                        type="file"
                        name="avatar"
                        id="avatar"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-[#FB6221] hover:file:bg-orange-100 cursor-pointer"
                    />
                    <p class="text-[11px] text-slate-400 font-sans">
                        Accepted formats: JPEG, PNG, WEBP. Max: 2 MB.
                    </p>
                </div>
            </div>

            {{-- CORE FIELDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-4 border-t border-slate-100">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    />
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
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
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="+91 98765 43210"
                        class="w-full px-4 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    />
                </div>

                {{-- ROLE & STATUS SETTINGS --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="role" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                            Role <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="role"
                            id="role"
                            required
                            class="w-full px-3 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                        >
                            <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="instructor" {{ old('role', $user->role) === 'instructor' ? 'selected' : '' }}>Instructor</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="status"
                            id="status"
                            required
                            class="w-full px-3 py-2.5 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                        >
                            <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                        User Biography / Profile Summary
                    </label>
                    <textarea
                        name="bio"
                        id="bio"
                        rows="3"
                        class="w-full px-4 py-3 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all resize-y"
                    >{{ old('bio', $user->bio) }}</textarea>
                </div>
            </div>

            {{-- FACULTY / INSTRUCTOR SPECIFIC CREDENTIALS --}}
            <div class="pt-5 border-t border-slate-100 space-y-4">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-slate-900 font-heading">
                        Faculty Credentials (Optional / for Instructors)
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="designation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                            Academic Designation
                        </label>
                        <input
                            type="text"
                            name="designation"
                            id="designation"
                            value="{{ old('designation', $user->designation) }}"
                            placeholder="e.g. Lead ERP Consultant"
                            class="w-full px-4 py-2 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                        />
                    </div>

                    <div>
                        <label for="specialization" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                            Specialization Area
                        </label>
                        <input
                            type="text"
                            name="specialization"
                            id="specialization"
                            value="{{ old('specialization', $user->specialization) }}"
                            placeholder="e.g. SAP FICO, S/4HANA"
                            class="w-full px-4 py-2 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                        />
                    </div>

                    <div>
                        <label for="experience_years" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                            Experience (Years)
                        </label>
                        <input
                            type="number"
                            name="experience_years"
                            id="experience_years"
                            min="0"
                            max="60"
                            value="{{ old('experience_years', $user->experience_years) }}"
                            class="w-full px-4 py-2 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                        />
                    </div>

                    <div>
                        <label for="qualification" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                            Qualifications &amp; Certifications
                        </label>
                        <input
                            type="text"
                            name="qualification"
                            id="qualification"
                            value="{{ old('qualification', $user->qualification) }}"
                            placeholder="e.g. MCA, SAP Certified"
                            class="w-full px-4 py-2 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                        />
                    </div>
                </div>
            </div>

            {{-- FORM ACTIONS --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <x-ui.button
                    href="{{ route('admin.users.show', $user) }}"
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
                    Update User Account
                </x-ui.button>
            </div>
        </x-ui.card>
    </form>
</div>
@endsection

