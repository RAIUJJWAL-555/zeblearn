@extends('layouts.auth')

@section('title', 'Student Registration | ZebLearn LMS')
@section('description', 'Create your student account to enroll in ZebLearn enterprise course tracks.')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-mono font-semibold tracking-wider uppercase bg-orange-100 text-[#FB6221] mb-3">
            STUDENT ONBOARDING
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
            Create your Student Account
        </h2>
        <p class="mt-2 text-sm text-slate-600">
            Join ZebLearn Academy to access your coursework, live classes, and hands-on cloud sandboxes.
        </p>
    </div>

    {{-- Error Alert Banner --}}
    @if ($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 flex items-start gap-3 text-rose-800 text-sm">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="font-medium">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Registration Form --}}
    <form
        action="{{ route('register') }}"
        method="POST"
        class="space-y-4"
        x-data="{ showPassword: false, showConfirmPassword: false, submitting: false }"
        @submit="submitting = true"
    >
        @csrf

        {{-- Full Name Field --}}
        <div>
            <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                Full Name
            </label>
            <input
                id="name"
                name="name"
                type="text"
                autocomplete="name"
                required
                autofocus
                value="{{ old('name') }}"
                placeholder="e.g. Rahul Sharma"
                aria-describedby="name-error"
                class="w-full rounded-xl border @error('name') border-rose-400 bg-rose-50/20 @else border-slate-300 bg-white @enderror px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
            />
            @error('name')
                <p id="name-error" class="mt-1.5 text-xs font-medium text-rose-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Email Address Field --}}
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                Email Address
            </label>
            <input
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                required
                value="{{ old('email') }}"
                placeholder="e.g. rahul@domain.com"
                aria-describedby="email-error"
                class="w-full rounded-xl border @error('email') border-rose-400 bg-rose-50/20 @else border-slate-300 bg-white @enderror px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
            />
            @error('email')
                <p id="email-error" class="mt-1.5 text-xs font-medium text-rose-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        {{-- Password Grid (2 Columns on Tablet / Desktop for clean visual) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        autocomplete="new-password"
                        required
                        placeholder="••••••••••••"
                        aria-describedby="password-error"
                        class="w-full rounded-xl border @error('password') border-rose-400 bg-rose-50/20 @else border-slate-300 bg-white @enderror px-4 py-3 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-hidden"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                    >
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                    Confirm Password
                </label>
                <div class="relative">
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        autocomplete="new-password"
                        required
                        placeholder="••••••••••••"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                    />
                    <button
                        type="button"
                        @click="showConfirmPassword = !showConfirmPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-hidden"
                        :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
                    >
                        <svg x-show="!showConfirmPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showConfirmPassword" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Inline Password Guidance --}}
        <div class="rounded-lg bg-slate-100/80 p-3 text-xs text-slate-600 flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Password must be at least 8 characters with a mix of letters, numbers, and symbols.</span>
        </div>

        @error('password')
            <p id="password-error" class="text-xs font-medium text-rose-600 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>{{ $message }}</span>
            </p>
        @enderror

        {{-- Submit Button --}}
        <div class="pt-2">
            <button
                type="submit"
                :disabled="submitting"
                class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl font-semibold text-sm text-white bg-[#FB6221] hover:bg-[#e05419] active:scale-[0.99] shadow-md shadow-orange-500/20 transition-all focus:outline-hidden focus:ring-4 focus:ring-[#FB6221]/30 cursor-pointer disabled:opacity-75 disabled:cursor-wait"
            >
                <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="submitting ? 'Creating account...' : 'Register as Student'">Register as Student</span>
            </button>
        </div>
    </form>

    {{-- Bottom Switcher Link --}}
    <div class="pt-6 border-t border-slate-200 text-center">
        <p class="text-xs text-slate-600">
            Already registered with ZebLearn?
            <a href="{{ route('login') }}" class="font-semibold text-[#FB6221] hover:text-[#e05419] transition-colors ml-1 focus:outline-hidden focus:underline">
                Sign in to Dashboard
            </a>
        </p>
    </div>
</div>
@endsection
