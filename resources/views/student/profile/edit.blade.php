@extends('layouts.app')

@section('title', 'Edit Profile | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- BREADCRUMBS & TOP BAR --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('student.profile.show') }}" class="hover:text-[#FB6221] transition-colors">My Profile</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Edit</span>
            </nav>
            <x-ui.button
                href="{{ route('student.profile.show') }}"
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

        {{-- 1. MAIN PROFILE UPDATE FORM --}}
        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading tracking-tight">
                        Edit Personal Information
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Update your public profile details and communication preferences.
                    </p>
                </div>

                {{-- PROFILE PHOTO UPLOAD / REMOVE SECTION --}}
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
                            Profile Photo
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

                    @if ($user->avatar_path)
                        <div class="sm:self-center">
                            <button
                                type="submit"
                                form="delete-avatar-form"
                                class="text-xs font-semibold text-rose-600 hover:text-rose-700 hover:underline cursor-pointer"
                            >
                                Remove Photo
                            </button>
                        </div>
                    @endif
                </div>

                {{-- TEXT FIELDS GRID --}}
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
                            Email Address (Fixed)
                        </label>
                        <input
                            type="email"
                            id="email"
                            value="{{ $user->email }}"
                            disabled
                            class="w-full px-4 py-2.5 text-xs sm:text-sm bg-slate-100 border border-slate-200 rounded-xl text-slate-500 cursor-not-allowed"
                            title="Email address changes must be handled through administrative support."
                        />
                    </div>

                    <div class="sm:col-span-2">
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

                    <div class="sm:col-span-2">
                        <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                            Personal Biography &amp; Professional Career Goals
                        </label>
                        <textarea
                            name="bio"
                            id="bio"
                            rows="4"
                            placeholder="Share your academic background, ERP domain interests, and career goals..."
                            class="w-full px-4 py-3 text-xs sm:text-sm bg-white border border-slate-200 rounded-xl text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all resize-y"
                        >{{ old('bio', $user->bio) }}</textarea>
                    </div>
                </div>

                {{-- NOTIFICATION & PROFILE PREFERENCES --}}
                <div class="pt-5 border-t border-slate-100 space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 font-heading">
                        Communication Preferences
                    </h3>

                    <div class="space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                name="preferences[email_notifications]"
                                value="1"
                                {{ old('preferences.email_notifications', $user->getPreference('email_notifications', true)) ? 'checked' : '' }}
                                class="mt-0.5 rounded border-slate-300 text-[#FB6221] focus:ring-[#FB6221]"
                            />
                            <div>
                                <span class="text-xs sm:text-sm font-semibold text-slate-800">Email Notifications</span>
                                <p class="text-[11px] text-slate-500">Receive email alerts for enrollment updates, graded coursework, and certificate announcements.</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                name="preferences[course_reminders]"
                                value="1"
                                {{ old('preferences.course_reminders', $user->getPreference('course_reminders', true)) ? 'checked' : '' }}
                                class="mt-0.5 rounded border-slate-300 text-[#FB6221] focus:ring-[#FB6221]"
                            />
                            <div>
                                <span class="text-xs sm:text-sm font-semibold text-slate-800">Course Study Reminders</span>
                                <p class="text-[11px] text-slate-500">Weekly study reminders and alerts about pending module assignments.</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                name="preferences[marketing_emails]"
                                value="1"
                                {{ old('preferences.marketing_emails', $user->getPreference('marketing_emails', false)) ? 'checked' : '' }}
                                class="mt-0.5 rounded border-slate-300 text-[#FB6221] focus:ring-[#FB6221]"
                            />
                            <div>
                                <span class="text-xs sm:text-sm font-semibold text-slate-800">Placement &amp; Industry Career News</span>
                                <p class="text-[11px] text-slate-500">Updates regarding campus hiring drives and industry webinar sessions.</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- FORM ACTIONS --}}
                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <x-ui.button
                        href="{{ route('student.profile.show') }}"
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
                        Save Profile Changes
                    </x-ui.button>
                </div>
            </x-ui.card>
        </form>

        {{-- SEPARATE HIDDEN FORM FOR AVATAR DELETION --}}
        @if ($user->avatar_path)
            <form id="delete-avatar-form" method="POST" action="{{ route('student.profile.avatar.destroy') }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif

        {{-- 2. PASSWORD SECURITY UPDATE CARD --}}
        <div id="password-section">
            <form method="POST" action="{{ route('student.profile.password.update') }}">
                @csrf
                @method('PUT')

                <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
                    <div>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900 font-heading tracking-tight">
                            Change Password
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Ensure your student account is using a long, random password to stay secure.
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
</div>
@endsection

