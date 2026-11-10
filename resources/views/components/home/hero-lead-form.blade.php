<div class="w-full max-w-[420px] mx-auto lg:ml-auto select-none" id="hero-lead-form-wrapper">
    <!-- Clean, Simple Hero Registration Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-[0_16px_36px_-8px_rgba(0,0,0,0.08)]">
        <!-- Minimal Clean Header -->
        <div class="mb-5">
            <h2 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-[#0A0A0A] leading-tight">
                BOOK YOUR FREE LIVE DEMO
            </h2>
            <p class="font-sans text-xs text-slate-500 mt-1">
                Live interactive batch with real SAP system practice.
            </p>
        </div>

        <!-- Simple Form -->
        <form id="hero-student-form" class="space-y-3.5">
            <!-- Full Name -->
            <div class="flex flex-col gap-1">
                <label for="hero-name" class="font-mono text-[11px] uppercase tracking-wider font-bold text-slate-700">
                    Full Name <span class="text-[#FB6221]">*</span>
                </label>
                <input
                    type="text"
                    id="hero-name"
                    name="name"
                    required
                    placeholder="Your Name"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] font-sans text-xs sm:text-sm text-[#0A0A0A] placeholder-slate-400 outline-none transition-all bg-slate-50/50 focus:bg-white"
                />
            </div>

            <!-- WhatsApp Number -->
            <div class="flex flex-col gap-1">
                <label for="hero-phone" class="font-mono text-[11px] uppercase tracking-wider font-bold text-slate-700">
                    WhatsApp Number <span class="text-[#FB6221]">*</span>
                </label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 py-2.5 bg-slate-100 border border-r-0 border-slate-200 rounded-l-xl text-xs font-mono font-bold text-slate-700 select-none">
                        +91
                    </span>
                    <input
                        type="tel"
                        id="hero-phone"
                        name="phone"
                        required
                        pattern="[0-9]{10}"
                        maxlength="10"
                        placeholder="98765 43210"
                        class="w-full px-3.5 py-2.5 rounded-r-xl border border-slate-200 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] font-sans text-xs sm:text-sm text-[#0A0A0A] placeholder-slate-400 outline-none transition-all bg-slate-50/50 focus:bg-white"
                    />
                </div>
            </div>

            <!-- Target SAP Module -->
            <div class="flex flex-col gap-1">
                <label for="hero-course" class="font-mono text-[11px] uppercase tracking-wider font-bold text-slate-700">
                    Select SAP Module <span class="text-[#FB6221]">*</span>
                </label>
                <div class="relative">
                    <select
                        id="hero-course"
                        name="course"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] font-sans text-xs sm:text-sm text-[#0A0A0A] outline-none transition-all bg-slate-50/50 focus:bg-white appearance-none cursor-pointer"
                    >
                        <option value="SAP FICO">SAP FICO (Financials &amp; Controlling)</option>
                        <option value="SAP MM">SAP MM (Materials Management &amp; SCM)</option>
                        <option value="SAP SD">SAP SD (Sales &amp; Distribution)</option>
                        <option value="SAP PP">SAP PP (Production Planning)</option>
                        <option value="SAP ABAP">SAP ABAP (Programming)</option>
                        <option value="Career Counselling">Not Sure Yet (Guide Me)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                id="hero-submit-btn"
                class="w-full mt-2 py-3.5 px-4 rounded-xl bg-[#FB6221] hover:bg-[#e05316] text-white font-heading font-bold text-xs sm:text-sm uppercase tracking-wider shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99]"
            >
                <span>CLAIM FREE DEMO SEAT</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>

        </form>
    </div>
</div>
