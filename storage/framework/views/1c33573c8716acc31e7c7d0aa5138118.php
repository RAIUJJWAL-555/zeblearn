<div class="relative w-full max-w-[560px] mx-auto select-none pointer-events-none perspective-1000" aria-hidden="true">
    <!-- Ambient Backdrop Glow -->
    <div class="absolute -top-10 -right-10 w-72 h-72 bg-[#FB6221]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-10 -left-10 w-72 h-72 bg-slate-300/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main 3D Laptop / Enterprise S/4HANA Cockpit Container -->
    <div class="relative z-10 w-full preserve-3d transition-transform duration-500">
        
        <!-- Satellite Floating Panel 1: Top-Right (F110 Payment Run) -->
        <div class="absolute -top-6 -right-2 sm:-right-6 z-20 animate-ambient-1 pointer-events-auto">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-xl shadow-slate-900/10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-[#FB6221] shrink-0">
                    <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5 font-mono text-[9px] uppercase tracking-wider text-slate-500 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>F110 Payment Run</span>
                    </div>
                    <div class="font-heading text-sm sm:text-base font-bold text-[#0A0A0A] leading-tight">
                        ₹1,24,500 <span class="text-[10px] font-mono font-semibold text-emerald-600 uppercase">Posted</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Satellite Floating Panel 2: Bottom-Left (ACDOCA Ledger Sync) -->
        <div class="absolute -bottom-6 -left-2 sm:-left-6 z-20 animate-ambient-2 pointer-events-auto">
            <div class="bg-[#0A0A0A]/95 text-white backdrop-blur-md rounded-2xl p-3.5 sm:p-4 border border-slate-800 shadow-2xl flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#FB6221]/20 border border-[#FB6221]/40 flex items-center justify-center text-[#FB6221] shrink-0">
                    <span class="material-symbols-outlined text-[20px]">sync</span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5 font-mono text-[9px] uppercase tracking-wider text-slate-400 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
                        <span>ACDOCA Ledger</span>
                    </div>
                    <div class="font-heading text-xs sm:text-sm font-bold text-white leading-tight">
                        Universal Journal <span class="text-[10px] font-mono text-emerald-400 font-normal">· Real-Time</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3D Geometric Floating Accent Cube (Top-Left) -->
        <div class="absolute -top-8 left-6 z-15 animate-ambient-3 hidden sm:block">
            <svg class="w-10 h-10" viewBox="0 0 40 40" fill="none">
                <path d="M20 4 L36 13 L20 22 L4 13 Z" fill="#FB6221" fill-opacity="0.85" stroke="#FFFFFF" stroke-width="0.8" />
                <path d="M4 13 L20 22 L20 38 L4 29 Z" fill="#C2410C" fill-opacity="0.9" />
                <path d="M20 22 L36 13 L36 29 L20 38 Z" fill="#EA580C" fill-opacity="0.9" />
            </svg>
        </div>

        <!-- 3D Enterprise Laptop Device Frame -->
        <div class="w-full bg-[#0A0A0A] rounded-2xl p-2.5 sm:p-3 shadow-2xl border border-slate-800">
            <!-- Screen Top Bezel with Camera & Status Pill -->
            <div class="flex items-center justify-between px-3 py-1.5 border-b border-slate-800/80 mb-2 font-mono text-[10px] text-slate-400">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500/80"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                    <span class="text-slate-300 font-semibold ml-2 hidden sm:inline">SAP S/4HANA FIORI · GUI 800</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[9px] px-2 py-0.5 rounded-full bg-emerald-950/80 text-emerald-400 border border-emerald-800/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        LIVE SANDBOX
                    </span>
                    <span class="text-slate-500 text-[9px] hidden sm:inline">ecc-srv-04</span>
                </div>
            </div>

            <!-- Screen Workstation Body (S/4HANA Universal Journal ACDOCA Simulation) -->
            <div class="bg-[#111318] rounded-xl p-3.5 sm:p-4 text-slate-200 border border-slate-800/60">
                <!-- Navigation & Filter Bar -->
                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-3 border-b border-slate-800/80 font-mono text-[10px]">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 rounded bg-[#FB6221] text-white font-bold tracking-wider">T-CODE: FB03</span>
                        <span class="text-slate-400">Doc #10004921</span>
                    </div>
                    <div class="text-slate-400 hidden sm:block">
                        Company Code: <span class="text-white font-bold">1000 [ZEB CORP]</span>
                    </div>
                </div>

                <!-- Ledger Telemetry Columns (Mock ACDOCA Stream) -->
                <div class="space-y-2 font-mono text-[10px] sm:text-[11px]">
                    <!-- Row 1: Cash / Bank -->
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/90 border border-slate-800/80">
                        <div class="flex items-center gap-2">
                            <span class="w-6 text-center py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold text-[9px]">40</span>
                            <div>
                                <span class="text-white font-semibold block">100000 · Cash / Bank Clearing</span>
                                <span class="text-[9px] text-slate-500">Universal Journal Ledger 0L</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-emerald-400 font-bold block">+₹24,50,000</span>
                            <span class="text-[9px] text-slate-500">Debit Post</span>
                        </div>
                    </div>

                    <!-- Row 2: Sales Revenue Domestic -->
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/90 border border-slate-800/80">
                        <div class="flex items-center gap-2">
                            <span class="w-6 text-center py-0.5 rounded bg-[#FB6221]/20 text-[#FB6221] font-bold text-[9px]">50</span>
                            <div>
                                <span class="text-white font-semibold block">400000 · Sales Revenue Domestic</span>
                                <span class="text-[9px] text-slate-500">Cost Center CC-1002</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[#FB6221] font-bold block">-₹81,20,000</span>
                            <span class="text-[9px] text-slate-500">Credit Post</span>
                        </div>
                    </div>

                    <!-- Row 3: Trade Receivables -->
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/90 border border-slate-800/80">
                        <div class="flex items-center gap-2">
                            <span class="w-6 text-center py-0.5 rounded bg-sky-500/20 text-sky-400 font-bold text-[9px]">01</span>
                            <div>
                                <span class="text-white font-semibold block">160000 · Trade Receivables AR</span>
                                <span class="text-[9px] text-slate-500">Customer #CUST-901</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sky-400 font-bold block">+₹19,80,000</span>
                            <span class="text-[9px] text-slate-500">Customer Open Item</span>
                        </div>
                    </div>
                </div>

                <!-- Live Balance Equilibrium Bar -->
                <div class="mt-3 pt-3 border-t border-slate-800 flex items-center justify-between font-mono text-[9px] text-slate-400">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Zero Balance Variance · Postings Reconciled</span>
                    </div>
                    <span class="text-slate-300 font-bold">FY2026 / PER 03</span>
                </div>
            </div>

            <!-- Laptop Base / Hinge Bar -->
            <div class="h-2.5 w-full bg-gradient-to-b from-slate-800 to-slate-900 rounded-b-xl mt-1 flex items-center justify-center">
                <div class="w-16 h-1 rounded-full bg-slate-700"></div>
            </div>
        </div>

        <!-- 3D Shadow Projection Underneath Laptop Base -->
        <div class="w-4/5 h-4 mx-auto bg-[#0A0A0A]/20 rounded-full blur-md -mt-1"></div>
    </div>
</div>

<?php /**PATH /Users/ujjwalrai/Desktop/Office/Zeblearn/resources/views/components/home/hero-cockpit.blade.php ENDPATH**/ ?>