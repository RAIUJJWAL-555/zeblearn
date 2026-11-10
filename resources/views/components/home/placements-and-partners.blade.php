@php
    $placementCards = [
        [
            'id' => 'sumit-singh',
            'name' => 'Sumit Singh',
            'course' => 'SAP FICO Training',
            'employer_status' => 'Placed at Accenture',
            'employer_note' => 'Accenture · Documented Alumni Placement',
            'quote' => 'It is due to the quality training provided here that I got placed in Accenture. The atmosphere is brilliant and everyone is studying hard.',
            'initials' => 'SS',
            'status' => 'Verified Alumni Placement',
            'status_type' => 'verified_placement',
            'bg_accent' => 'border-orange-200/90',
        ],
        [
            'id' => 'deeksha',
            'name' => 'Deeksha',
            'course' => 'Fast Track SAP FICO',
            'employer_status' => 'Alumni Career Feedback',
            'employer_note' => 'PlacementBaba Network Support',
            'quote' => 'I found ZebLearn the best place if you are searching for jobs. The trainers are very supportive and you will get ample opportunities here.',
            'initials' => 'D',
            'status' => 'Verified Course Alum',
            'status_type' => 'verified_course',
            'bg_accent' => 'border-slate-200/90',
        ],
        [
            'id' => 'shyam-singh',
            'name' => 'Shyam Singh',
            'course' => 'ERP & Enterprise Servers',
            'employer_status' => 'Technical Training Alum',
            'employer_note' => 'Server & Systems Course',
            'quote' => 'I joined ZebLearn to pursue ERP & Servers. The technical sessions were given in a very simplified manner. Thanks ZebLearn.',
            'initials' => 'SS',
            'status' => 'Verified Course Alum',
            'status_type' => 'verified_course',
            'bg_accent' => 'border-slate-200/90',
        ],
        [
            'id' => 'placement-slot',
            'name' => 'Placement Record Slot',
            'course' => 'SAP Functional Batch',
            'employer_status' => 'Upcoming Placement Slot',
            'employer_note' => 'Pending Offer Letter Documentation',
            'quote' => '[Placement Record Placeholder] Additional verified student placement records will be documented here upon verification of candidate offer letters.',
            'initials' => 'PR',
            'status' => 'Data-Driven Slot',
            'status_type' => 'placeholder',
            'bg_accent' => 'border-dashed border-slate-300',
        ],
    ];

    // Category C: Enterprise SAP Ecosystem
    $ecosystemCompanies = [
        [
            'name' => 'Deloitte',
            'role' => 'Global SAP Enterprise Practice',
            'url' => 'https://www.deloitte.com/',
        ],
        [
            'name' => 'IBM',
            'role' => 'SAP Global Alliance Partner',
            'url' => 'https://www.ibm.com/',
        ],
        [
            'name' => 'TCS',
            'role' => 'Tata Consultancy Services',
            'url' => 'https://www.tcs.com/',
        ],
        [
            'name' => 'Wipro',
            'role' => 'Enterprise SAP Consulting',
            'url' => 'https://www.wipro.com/',
        ],
        [
            'name' => 'Capgemini',
            'role' => 'Global SAP Systems Integration',
            'url' => 'https://www.capgemini.com/',
        ],
        [
            'name' => 'Infosys',
            'role' => 'Enterprise Cloud & ERP Practice',
            'url' => 'https://www.infosys.com/',
        ],
    ];
@endphp

<section id="placements" class="w-full bg-[#FAFAF8] py-16 lg:py-24 border-t border-slate-200" aria-labelledby="placements-heading">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12">

        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12" data-reveal>
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 text-[#0A0A0A] font-mono text-[10px] font-bold uppercase tracking-widest shadow-xs mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
                    <span>CAREER TRAJECTORY &amp; OUTCOMES</span>
                </div>
                <h2 id="placements-heading" class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black uppercase text-[#0A0A0A] tracking-tight leading-tight">
                    REAL LEARNERS. REAL CAREERS.
                </h2>
                <p class="text-sm sm:text-base text-slate-600 max-w-xl font-normal leading-relaxed mt-2">
                    Explore verified learner journeys and the organizations where our students have progressed.
                </p>
            </div>

            <!-- Placement Partner Direct Portal Banner -->
            <div class="shrink-0">
                <a
                    href="https://www.placementbaba.com/"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-[#0A0A0A] hover:bg-[#FB6221] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider transition-all duration-200 shadow-sm hover:scale-[1.02] zeb-btn-cta">
                    <span>Browse Openings on PlacementBaba</span>
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                </a>
            </div>
        </div>

        <!-- Student Placement Cards Grid (2x2 on Desktop) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16">
            @foreach($placementCards as $card)
                <article class="bg-white rounded-3xl p-6 sm:p-8 border {{ $card['bg_accent'] }} shadow-xs hover:shadow-md transition-all duration-300 flex flex-col justify-between zeb-card-interactive" data-reveal>
                    <div>
                        <!-- Header with Status Badge -->
                        <div class="flex items-center justify-between gap-2 mb-4">
                            @if($card['status_type'] === 'verified_placement')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 border border-orange-200 font-mono text-[11px] font-bold text-[#FB6221] uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">business_center</span>
                                    <span>{{ $card['employer_status'] }}</span>
                                </span>
                            @elseif($card['status_type'] === 'verified_course')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 font-mono text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[14px]">school</span>
                                    <span>{{ $card['employer_status'] }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 border border-dashed border-slate-300 font-mono text-[11px] font-medium text-slate-500 uppercase tracking-wider">
                                    <span>{{ $card['employer_status'] }}</span>
                                </span>
                            @endif

                            <span class="font-mono text-[10px] text-slate-400 uppercase">
                                {{ $card['status'] }}
                            </span>
                        </div>

                        <!-- Quote / Experience -->
                        <blockquote class="font-sans text-sm sm:text-base text-slate-800 font-medium leading-relaxed mb-6">
                            &ldquo;{{ $card['quote'] }}&rdquo;
                        </blockquote>
                    </div>

                    <!-- Student Meta Box -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <!-- Student Avatar / Initials -->
                            <div class="w-11 h-11 rounded-full bg-[#0A0A0A] text-white flex items-center justify-center font-heading text-sm font-bold shrink-0 shadow-xs">
                                {{ $card['initials'] }}
                            </div>
                            <div>
                                <h3 class="font-['Inter'] text-sm font-bold text-[#0A0A0A] leading-tight">
                                    {{ $card['name'] }}
                                </h3>
                                <p class="font-mono text-[11px] text-slate-500 mt-0.5">
                                    {{ $card['course'] }} · <span class="text-[#0A0A0A] font-semibold">{{ $card['employer_note'] }}</span>
                                </p>
                            </div>
                        </div>

                        <span class="hidden sm:inline-flex px-3 py-1 rounded-full bg-slate-50 border border-slate-200 font-mono text-[10px] font-bold uppercase text-slate-600">
                            {{ $card['course'] }}
                        </span>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- =========================================================================
             EMPLOYERS AND INDUSTRY ECOSYSTEM (CATEGORIZED & ORIGINAL COLORFUL LOGOS)
             ========================================================================= -->
        <div class="pt-12 border-t border-slate-200">
            <div class="mb-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-[#0A0A0A] font-mono text-[10px] font-bold uppercase tracking-widest mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
                    <span>CAREER OUTCOMES &amp; INDUSTRY LANDSCAPE</span>
                </div>
                <h3 class="font-heading text-2xl sm:text-3xl font-black uppercase text-[#0A0A0A] tracking-tight">
                    EMPLOYERS &amp; INDUSTRY PARTNERS
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 max-w-2xl mt-1 leading-relaxed">
                    Clear categorization of our verified dedicated placement partner, documented alumni employers, and the wider enterprise SAP industry landscape.
                </p>
            </div>

            <!-- Row 1: Category A & Category B (Featured Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- CATEGORY A: Official Dedicated Placement Partner -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-orange-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-100 text-[#FB6221] font-mono text-[10px] font-bold uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
                                <span>CATEGORY A · OFFICIAL PLACEMENT PARTNER</span>
                            </span>
                            <span class="font-mono text-[10px] text-emerald-600 font-bold uppercase">Active Partnership</span>
                        </div>

                        <!-- Original Colorful PlacementBaba Logo -->
                        <div class="my-4">
                            <a
                                href="https://www.placementbaba.com/"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="PlacementBaba official placement partner website"
                                aria-label="PlacementBaba official website"
                                class="inline-block transition-transform hover:scale-[1.02]">
                                <svg class="h-8 w-auto max-w-[170px]" viewBox="0 0 160 32" xmlns="http://www.w3.org/2000/svg" aria-label="PlacementBaba logo">
                                    <rect x="0" y="3" width="26" height="26" rx="6" fill="#FB6221"/>
                                    <text x="6" y="21" font-family="'Manrope', sans-serif" font-weight="800" font-size="14" fill="#FFFFFF">PB</text>
                                    <text x="34" y="23" font-family="'Inter', sans-serif" font-size="16" font-weight="800" fill="#0A0A0A" letter-spacing="-0.02em">Placement<tspan fill="#FB6221">Baba</tspan></text>
                                </svg>
                            </a>
                        </div>

                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-3">
                            <strong>PlacementBaba</strong> is ZebLearn's dedicated placement platform partner, providing direct access to active SAP openings, resume screening, and client interview scheduling across India.
                        </p>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="font-mono text-xs text-slate-500">Placement Support &amp; Hiring Portal</span>
                        <a
                            href="https://www.placementbaba.com/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 text-xs font-bold text-[#FB6221] hover:underline uppercase">
                            <span>Visit Portal</span>
                            <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                        </a>
                    </div>
                </div>

                <!-- CATEGORY B: Verified Alumni Employer -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-[#A100FF] font-mono text-[10px] font-bold uppercase tracking-wider border border-purple-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#A100FF]"></span>
                                <span>CATEGORY B · VERIFIED ALUMNI EMPLOYER</span>
                            </span>
                            <span class="font-mono text-[10px] text-slate-500 uppercase">Documented Alumni Placement</span>
                        </div>

                        <!-- Original Colorful Accenture Logo -->
                        <div class="my-4">
                            <a
                                href="https://www.accenture.com/"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="Accenture official website"
                                aria-label="Accenture official website"
                                class="inline-block transition-transform hover:scale-[1.02]">
                                <svg class="h-8 w-auto max-w-[150px]" viewBox="0 0 140 32" xmlns="http://www.w3.org/2000/svg" aria-label="Accenture logo">
                                    <text x="0" y="24" font-family="'Inter', Arial, sans-serif" font-size="22" font-weight="700" fill="#000000" letter-spacing="-0.04em">accenture</text>
                                    <polygon points="68,5 80,13 68,21 73,13" fill="#A100FF"/>
                                </svg>
                            </a>
                        </div>

                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-3">
                            Documented enterprise destination where ZebLearn graduates (e.g. <strong>Sumit Singh</strong>, SAP FICO) have been successfully hired following intensive system training and mock interview rounds.
                        </p>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="font-mono text-xs text-slate-500">Global SAP Implementation Leader</span>
                        <a
                            href="https://www.accenture.com/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 text-xs font-bold text-[#0A0A0A] hover:text-[#FB6221] hover:underline uppercase">
                            <span>accenture.com</span>
                            <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Row 2: Category C: Enterprise SAP Ecosystem -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs zeb-card-interactive" data-reveal>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                            CATEGORY C // INDUSTRY ECOSYSTEM
                        </span>
                        <h4 class="font-heading text-lg sm:text-xl font-bold uppercase text-[#0A0A0A] tracking-tight mt-0.5">
                            WHERE SAP PROFESSIONALS OPERATE
                        </h4>
                    </div>
                    <p class="text-xs text-slate-500 max-w-md">
                        Prominent global systems integrators with major SAP enterprise practices. Displayed for industry employment context; does not imply direct institutional partnership.
                    </p>
                </div>

                <!-- 6 Original Colorful Logos in Responsive Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <!-- 1. Deloitte -->
                    <a
                        href="https://www.deloitte.com/"
                        target="_blank"
                        rel="noopener noreferrer"
                        title="Deloitte official website"
                        aria-label="Deloitte official website"
                        class="h-20 px-4 py-3 rounded-2xl bg-slate-50/60 hover:bg-white border border-slate-200/80 hover:border-slate-300 shadow-2xs hover:shadow-sm flex flex-col items-center justify-center transition-all duration-200 group zeb-partner-card">
                        <svg class="h-6 w-auto max-w-[110px]" viewBox="0 0 130 30" xmlns="http://www.w3.org/2000/svg" aria-label="Deloitte logo">
                            <text x="0" y="23" font-family="'Inter', Arial, sans-serif" font-size="24" font-weight="800" fill="#000000" letter-spacing="-0.03em">Deloitte</text>
                            <circle cx="95" cy="21" r="3.5" fill="#86BC25"/>
                        </svg>
                        <span class="font-mono text-[9px] uppercase tracking-wider text-slate-400 group-hover:text-slate-600 mt-1">Enterprise Practice</span>
                    </a>

                    <!-- 2. IBM -->
                    <a
                        href="https://www.ibm.com/"
                        target="_blank"
                        rel="noopener noreferrer"
                        title="IBM official website"
                        aria-label="IBM official website"
                        class="h-20 px-4 py-3 rounded-2xl bg-slate-50/60 hover:bg-white border border-slate-200/80 hover:border-slate-300 shadow-2xs hover:shadow-sm flex flex-col items-center justify-center transition-all duration-200 group zeb-partner-card">
                        <svg class="h-6 w-auto max-w-[70px]" viewBox="0 0 100 40" xmlns="http://www.w3.org/2000/svg" aria-label="IBM 8-bar blue logo">
                            <g fill="#0F62FE">
                                <rect x="0" y="2" width="24" height="2"/>
                                <rect x="30" y="2" width="36" height="2"/>
                                <rect x="72" y="2" width="28" height="2"/>
                                <rect x="0" y="7" width="24" height="2"/>
                                <rect x="30" y="7" width="36" height="2"/>
                                <rect x="72" y="7" width="28" height="2"/>
                                <rect x="8" y="12" width="8" height="2"/>
                                <rect x="30" y="12" width="36" height="2"/>
                                <rect x="72" y="12" width="8" height="2"/>
                                <rect x="82" y="12" width="8" height="2"/>
                                <rect x="92" y="12" width="8" height="2"/>
                                <rect x="8" y="17" width="8" height="2"/>
                                <rect x="30" y="17" width="36" height="2"/>
                                <rect x="72" y="17" width="8" height="2"/>
                                <rect x="82" y="17" width="8" height="2"/>
                                <rect x="92" y="17" width="8" height="2"/>
                                <rect x="8" y="22" width="8" height="2"/>
                                <rect x="30" y="22" width="36" height="2"/>
                                <rect x="72" y="22" width="8" height="2"/>
                                <rect x="82" y="22" width="8" height="2"/>
                                <rect x="92" y="22" width="8" height="2"/>
                                <rect x="8" y="27" width="8" height="2"/>
                                <rect x="30" y="27" width="36" height="2"/>
                                <rect x="72" y="27" width="8" height="2"/>
                                <rect x="82" y="27" width="8" height="2"/>
                                <rect x="92" y="27" width="8" height="2"/>
                                <rect x="0" y="32" width="24" height="2"/>
                                <rect x="30" y="32" width="36" height="2"/>
                                <rect x="72" y="32" width="28" height="2"/>
                                <rect x="0" y="37" width="24" height="2"/>
                                <rect x="30" y="37" width="36" height="2"/>
                                <rect x="72" y="37" width="28" height="2"/>
                            </g>
                        </svg>
                        <span class="font-mono text-[9px] uppercase tracking-wider text-slate-400 group-hover:text-slate-600 mt-1">Enterprise Practice</span>
                    </a>

                    <!-- 3. TCS -->
                    <a
                        href="https://www.tcs.com/"
                        target="_blank"
                        rel="noopener noreferrer"
                        title="Tata Consultancy Services official website"
                        aria-label="Tata Consultancy Services official website"
                        class="h-20 px-4 py-3 rounded-2xl bg-slate-50/60 hover:bg-white border border-slate-200/80 hover:border-slate-300 shadow-2xs hover:shadow-sm flex flex-col items-center justify-center transition-all duration-200 group zeb-partner-card">
                        <svg class="h-6 w-auto max-w-[120px]" viewBox="0 0 150 32" xmlns="http://www.w3.org/2000/svg" aria-label="TCS logo">
                            <text x="0" y="24" font-family="'Inter', sans-serif" font-size="26" font-weight="900" fill="#002B49" letter-spacing="-0.03em">tcs</text>
                            <text x="44" y="16" font-family="'Inter', sans-serif" font-size="8" font-weight="700" fill="#002B49" letter-spacing="0.05em">TATA</text>
                            <text x="44" y="25" font-family="'Inter', sans-serif" font-size="7" font-weight="600" fill="#6B7280" letter-spacing="0.02em">CONSULTANCY SERVICES</text>
                        </svg>
                        <span class="font-mono text-[9px] uppercase tracking-wider text-slate-400 group-hover:text-slate-600 mt-1">Enterprise Practice</span>
                    </a>

                    <!-- 4. Wipro -->
                    <a
                        href="https://www.wipro.com/"
                        target="_blank"
                        rel="noopener noreferrer"
                        title="Wipro official website"
                        aria-label="Wipro official website"
                        class="h-20 px-4 py-3 rounded-2xl bg-slate-50/60 hover:bg-white border border-slate-200/80 hover:border-slate-300 shadow-2xs hover:shadow-sm flex flex-col items-center justify-center transition-all duration-200 group zeb-partner-card">
                        <svg class="h-6 w-auto max-w-[100px]" viewBox="0 0 130 32" xmlns="http://www.w3.org/2000/svg" aria-label="Wipro logo">
                            <g transform="translate(14, 16)">
                                <circle cx="0" cy="-9" r="2.5" fill="#E03C31"/>
                                <circle cx="6.5" cy="-6.5" r="2.5" fill="#FFB81C"/>
                                <circle cx="9" cy="0" r="2.5" fill="#78BE20"/>
                                <circle cx="6.5" cy="6.5" r="2.5" fill="#00A3E0"/>
                                <circle cx="0" cy="9" r="2.5" fill="#0070AD"/>
                                <circle cx="-6.5" cy="6.5" r="2.5" fill="#7B2282"/>
                                <circle cx="-9" cy="0" r="2.5" fill="#9B26B6"/>
                                <circle cx="-6.5" cy="-6.5" r="2.5" fill="#C8102E"/>
                                <circle cx="0" cy="0" r="1.8" fill="#5C768D"/>
                            </g>
                            <text x="34" y="23" font-family="'Inter', sans-serif" font-size="20" font-weight="700" fill="#1F2937" letter-spacing="-0.02em">wipro</text>
                        </svg>
                        <span class="font-mono text-[9px] uppercase tracking-wider text-slate-400 group-hover:text-slate-600 mt-1">Enterprise Practice</span>
                    </a>

                    <!-- 5. Capgemini -->
                    <a
                        href="https://www.capgemini.com/"
                        target="_blank"
                        rel="noopener noreferrer"
                        title="Capgemini official website"
                        aria-label="Capgemini official website"
                        class="h-20 px-4 py-3 rounded-2xl bg-slate-50/60 hover:bg-white border border-slate-200/80 hover:border-slate-300 shadow-2xs hover:shadow-sm flex flex-col items-center justify-center transition-all duration-200 group zeb-partner-card">
                        <svg class="h-6 w-auto max-w-[120px]" viewBox="0 0 150 32" xmlns="http://www.w3.org/2000/svg" aria-label="Capgemini logo">
                            <path d="M12,4 C14.5,7 18,10.5 19,13.5 C19.8,16 18,18.5 15.5,18.5 C13.5,18.5 12.5,17 12,15.5 C11.5,17 10.5,18.5 8.5,18.5 C6,18.5 4.2,16 5,13.5 C6,10.5 9.5,7 12,4 Z M11,18 L13,18 L14,24 L10,24 Z" fill="#0070AD"/>
                            <text x="26" y="22" font-family="'Inter', sans-serif" font-size="19" font-weight="700" fill="#111827" letter-spacing="-0.03em">Capgemini</text>
                        </svg>
                        <span class="font-mono text-[9px] uppercase tracking-wider text-slate-400 group-hover:text-slate-600 mt-1">Enterprise Practice</span>
                    </a>

                    <!-- 6. Infosys -->
                    <a
                        href="https://www.infosys.com/"
                        target="_blank"
                        rel="noopener noreferrer"
                        title="Infosys official website"
                        aria-label="Infosys official website"
                        class="h-20 px-4 py-3 rounded-2xl bg-slate-50/60 hover:bg-white border border-slate-200/80 hover:border-slate-300 shadow-2xs hover:shadow-sm flex flex-col items-center justify-center transition-all duration-200 group zeb-partner-card">
                        <svg class="h-6 w-auto max-w-[95px]" viewBox="0 0 120 32" xmlns="http://www.w3.org/2000/svg" aria-label="Infosys logo">
                            <text x="2" y="24" font-family="'Inter', sans-serif" font-size="24" font-weight="800" fill="#007CC3" letter-spacing="-0.03em">Infosys</text>
                        </svg>
                        <span class="font-mono text-[9px] uppercase tracking-wider text-slate-400 group-hover:text-slate-600 mt-1">Enterprise Practice</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>
