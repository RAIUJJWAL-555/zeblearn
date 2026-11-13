@extends('layouts.auth')

@section('title', 'Forgot Password | ZebLearn LMS')
@section('description', 'Recover your ZebLearn LMS account credentials securely via email verification link.')

@section('content')
<div class="space-y-6">
    {{-- Back Link --}}
    <div>
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#FB6221] transition-colors focus:outline-hidden focus:underline mb-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Back to Sign In</span>
        </a>

        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-mono font-semibold tracking-wider uppercase bg-orange-100 text-[#FB6221] mb-3 block w-fit">
            ACCOUNT RECOVERY
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
            Forgot your password?
        </h2>
        <p class="mt-2 text-sm text-slate-600">
            No worries! Enter your registered account email, and we'll send you a secure link to reset your password.
        </p>
    </div>

    {{-- Success Notification --}}
    @if (session('status'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-start gap-3 text-emerald-800 text-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-medium">{{ session('status') }}</p>
                <p class="text-xs text-emerald-700 mt-1">Please check your inbox (and spam folder) for the verification link.</p>
            </div>
        </div>
    @endif

    {{-- Error Banner --}}
    @if ($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 flex items-start gap-3 text-rose-800 text-sm">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                @foreach ($errors->all() as $error)
                    <p class="font-medium">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Request Form --}}
    <form
        action="{{ route('password.email') }}"
        method="POST"
        class="space-y-5"
        x-data="{ submitting: false }"
        @submit="submitting = true"
    >
        @csrf

        {{-- Email Address Field --}}
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                Registered Email Address
            </label>
            <input
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                required
                autofocus
                value="{{ old('email') }}"
                placeholder="e.g. yourname@domain.com"
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

        {{-- Submit Button --}}
        <div>
            <button
                type="submit"
                :disabled="submitting"
                class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl font-semibold text-sm text-white bg-[#FB6221] hover:bg-[#e05419] active:scale-[0.99] shadow-md shadow-orange-500/20 transition-all focus:outline-hidden focus:ring-4 focus:ring-[#FB6221]/30 cursor-pointer disabled:opacity-75 disabled:cursor-wait"
            >
                <svg x-show="submitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="submitting ? 'Sending reset link...' : 'Send Password Reset Link'">Send Password Reset Link</span>
            </button>
        </div>
    </form>

    {{-- Bottom Switcher Link --}}
    <div class="pt-6 border-t border-slate-200 text-center">
        <p class="text-xs text-slate-600">
            Remembered your credentials?
            <a href="{{ route('login') }}" class="font-semibold text-[#FB6221] hover:text-[#e05419] transition-colors ml-1 focus:outline-hidden focus:underline">
                Return to Sign In
            </a>
        </p>
    </div>
</div>
@endsection
