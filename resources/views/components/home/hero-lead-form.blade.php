<div class="relative w-full max-w-[440px] mx-auto lg:ml-auto select-none" id="hero-lead-form-wrapper">
    <!-- Ambient backlight glows for glassy depth (No grid) -->
    <div class="absolute -top-6 -right-6 w-48 h-48 bg-[#FB6221]/20 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="absolute -bottom-6 -left-6 w-48 h-48 bg-blue-500/20 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- Glassmorphic Hero Registration Card -->
    <div class="relative bg-slate-900/65 backdrop-blur-xl backdrop-saturate-150 rounded-2xl sm:rounded-3xl p-6 sm:p-7 border border-white/20 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.6),inset_0_1px_1px_rgba(255,255,255,0.15)]">
        <!-- Minimal Clean Header -->
        <div class="mb-5">
            <h2 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-white leading-tight">
                BOOK YOUR FREE LIVE DEMO
            </h2>
            <p class="font-sans text-xs text-slate-300 mt-1">
                Live interactive batch with real SAP system practice.
            </p>
        </div>

        <!-- Glassy Form -->
        <form id="hero-student-form" class="space-y-3.5">
            <!-- Full Name -->
            <div class="flex flex-col gap-1">
                <label for="hero-name" class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-300">
                    Full Name <span class="text-[#FB6221]">*</span>
                </label>
                <input
                    type="text"
                    id="hero-name"
                    name="name"
                    required
                    placeholder="Your Name"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-white/15 bg-white/10 hover:bg-white/15 focus:bg-white/20 focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/30 font-sans text-xs sm:text-sm text-white placeholder-slate-400 outline-none transition-all shadow-inner"
                />
            </div>

            <!-- WhatsApp Number -->
            <div class="flex flex-col gap-1">
                <label for="hero-phone" class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-300">
                    WhatsApp Number <span class="text-[#FB6221]">*</span>
                </label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 py-2.5 bg-white/15 border border-r-0 border-white/15 rounded-l-xl text-xs font-mono font-bold text-slate-200 select-none">
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
                        class="w-full px-3.5 py-2.5 rounded-r-xl border border-white/15 bg-white/10 hover:bg-white/15 focus:bg-white/20 focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/30 font-sans text-xs sm:text-sm text-white placeholder-slate-400 outline-none transition-all shadow-inner"
                    />
                </div>
            </div>

            <!-- Target SAP Module -->
            <div class="flex flex-col gap-1">
                <label for="hero-course" class="font-mono text-[11px] uppercase tracking-wider font-semibold text-slate-300">
                    Select SAP Module <span class="text-[#FB6221]">*</span>
                </label>
                <div class="relative">
                    <select
                        id="hero-course"
                        name="course"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-white/15 bg-slate-900/90 focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/30 font-sans text-xs sm:text-sm text-white outline-none transition-all cursor-pointer appearance-none"
                    >
                        <option value="SAP FICO" class="bg-slate-900 text-white">SAP FICO (Financials &amp; Controlling)</option>
                        <option value="SAP MM" class="bg-slate-900 text-white">SAP MM (Materials Management &amp; SCM)</option>
                        <option value="SAP SD" class="bg-slate-900 text-white">SAP SD (Sales &amp; Distribution)</option>
                        <option value="SAP PP" class="bg-slate-900 text-white">SAP PP (Production Planning)</option>
                        <option value="SAP ABAP" class="bg-slate-900 text-white">SAP ABAP (Programming)</option>
                        <option value="Career Counselling" class="bg-slate-900 text-white">Not Sure Yet (Guide Me)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-300">
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
                class="w-full mt-2 py-3.5 px-4 rounded-xl bg-gradient-to-r from-[#FB6221] to-[#e85315] hover:from-[#ff7336] hover:to-[#f05c1e] text-white font-heading font-bold text-xs sm:text-sm uppercase tracking-wider shadow-[0_8px_25px_rgba(251,98,33,0.45)] hover:shadow-[0_10px_30px_rgba(251,98,33,0.6)] transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99] group"
            >
                <span>CLAIM FREE DEMO SEAT</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </button>

            <p class="text-[10px] font-mono text-slate-400 text-center tracking-tight mt-2.5">
                Limited Slots Available &bull; Start Learning Next Batch
            </p>

        </form>
    </div>
</div>
