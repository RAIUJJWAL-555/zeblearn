@props([
    'step' => '1',
])

@php
    $s = (string) $step;
@endphp

<div class="w-full flex items-center justify-center py-2 select-none pointer-events-none zeb-illustration-art" aria-hidden="true">
@if($s === '1' || $s === 'learn')
    <!-- STEP 01: LEARN (Live Classes on a Real SAP System) -->
    <svg class="w-full max-w-[180px] h-[90px]" viewBox="0 0 180 90" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="j1-screen" x1="50" y1="15" x2="130" y2="65" gradientUnits="userSpaceOnUse">
                <stop stop-color="#0A0A0A" />
                <stop offset="1" stop-color="#1E293B" />
            </linearGradient>
        </defs>
        <!-- Device Base Shadow -->
        <ellipse cx="90" cy="74" rx="55" ry="10" fill="#0A0A0A" fill-opacity="0.06" />
        <!-- Dual Display Monitor (Presenter + Classroom) -->
        <rect x="42" y="16" width="96" height="52" rx="6" fill="url(#j1-screen)" stroke="#334155" stroke-width="1.2" />
        <rect x="46" y="20" width="9" height="9" rx="2" fill="#FB6221" />
        <!-- Presenter Video Avatar -->
        <circle cx="50.5" cy="23.5" r="2" fill="#FFFFFF" />
        <path d="M47.5 27 C47.5 25.5 53.5 25.5 53.5 27 Z" fill="#FFFFFF" />
        <!-- Code / System Lines -->
        <rect x="60" y="22" width="45" height="2" rx="1" fill="#94A3B8" />
        <rect x="60" y="27" width="30" height="2" rx="1" fill="#64748B" />
        <!-- Interactive Chat Stream Panel on Right of Screen -->
        <rect x="110" y="20" width="24" height="44" rx="2" fill="#0F172A" />
        <rect x="113" y="24" width="18" height="2" rx="1" fill="#FB6221" />
        <rect x="113" y="29" width="14" height="2" rx="1" fill="#FDBA74" />
        <rect x="113" y="34" width="16" height="2" rx="1" fill="#FB6221" />
        <rect x="113" y="39" width="12" height="2" rx="1" fill="#94A3B8" />
        <!-- Live Badge -->
        <rect x="46" y="34" width="28" height="8" rx="2" fill="#FB6221" fill-opacity="0.15" stroke="#FB6221" stroke-width="0.8" />
        <circle cx="50" cy="38" r="1.5" fill="#FB6221" />
        <text x="54" y="40.5" font-family="'IBM Plex Mono', monospace" font-size="5" font-weight="700" fill="#FB6221">LIVE</text>
        <!-- Stand Base -->
        <path d="M84 68 L96 68 L98 74 L82 74 Z" fill="#475569" />
    </svg>

@elseif($s === '2' || $s === 'practice')
    <!-- STEP 02: PRACTICE (Assignments & SAP Sandbox Labs) -->
    <svg class="w-full max-w-[180px] h-[90px]" viewBox="0 0 180 90" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="j2-rack" x1="45" y1="20" x2="135" y2="70" gradientUnits="userSpaceOnUse">
                <stop stop-color="#1E293B" />
                <stop offset="1" stop-color="#0F172A" />
            </linearGradient>
        </defs>
        <ellipse cx="90" cy="74" rx="55" ry="10" fill="#0A0A0A" fill-opacity="0.06" />
        <!-- Server Enclosure -->
        <rect x="50" y="16" width="80" height="52" rx="6" fill="url(#j2-rack)" stroke="#334155" stroke-width="1.2" />
        <!-- Bay 1 (Active Sandbox Terminal) -->
        <rect x="56" y="22" width="68" height="11" rx="2" fill="#0A0A0A" stroke="#475569" stroke-width="0.8" />
        <circle cx="62" cy="27.5" r="1.5" fill="#FB6221" />
        <text x="68" y="29.5" font-family="'IBM Plex Mono', monospace" font-size="5.5" font-weight="700" fill="#FFFFFF">GUI: /nFB01 POST</text>
        <!-- Bay 2 (Configuration Sandbox) -->
        <rect x="56" y="36" width="68" height="11" rx="2" fill="#0A0A0A" stroke="#475569" stroke-width="0.8" />
        <circle cx="62" cy="41.5" r="1.5" fill="#FB6221" />
        <text x="68" y="43.5" font-family="'IBM Plex Mono', monospace" font-size="5.5" font-weight="700" fill="#FDBA74">SPRO: S/4HANA ORG</text>
        <!-- Bay 3 (Testing & Status) -->
        <rect x="56" y="50" width="68" height="11" rx="2" fill="#0A0A0A" stroke="#475569" stroke-width="0.8" />
        <circle cx="62" cy="55.5" r="1.5" fill="#FB6221" />
        <text x="68" y="57.5" font-family="'IBM Plex Mono', monospace" font-size="5.5" font-weight="700" fill="#FB6221">100% RECONCILED</text>
    </svg>

@elseif($s === '3' || $s === 'skills')
    <!-- STEP 03: BUILD SKILLS (Mock Interviews & Resume Support) -->
    <svg class="w-full max-w-[180px] h-[90px]" viewBox="0 0 180 90" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="90" cy="74" rx="55" ry="10" fill="#0A0A0A" fill-opacity="0.06" />
        <!-- Consultant Dossier / Resume Binder -->
        <rect x="48" y="16" width="46" height="52" rx="4" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="1.2" />
        <rect x="54" y="22" width="20" height="3" rx="1.5" fill="#FB6221" />
        <rect x="54" y="28" width="34" height="2" rx="1" fill="#94A3B8" />
        <rect x="54" y="33" width="28" height="2" rx="1" fill="#CBD5E1" />
        <rect x="54" y="38" width="32" height="2" rx="1" fill="#CBD5E1" />
        <circle cx="58" cy="48" r="3" fill="#FB6221" />
        <rect x="64" y="47" width="20" height="2" rx="1" fill="#94A3B8" />
        <circle cx="58" cy="56" r="3" fill="#FB6221" />
        <rect x="64" y="55" width="22" height="2" rx="1" fill="#FB6221" />

        <!-- Simulation Studio Microphone & Audio Waves -->
        <g transform="translate(112, 22)">
            <rect x="6" y="6" width="14" height="22" rx="7" fill="#0A0A0A" stroke="#334155" stroke-width="1.2" />
            <line x1="8" y1="12" x2="18" y2="12" stroke="#475569" stroke-width="1" />
            <line x1="8" y1="17" x2="18" y2="17" stroke="#475569" stroke-width="1" />
            <path d="M3 16 Q3 30 13 30 Q23 30 23 16" stroke="#FB6221" stroke-width="1.8" fill="none" />
            <line x1="13" y1="30" x2="13" y2="38" stroke="#334155" stroke-width="2" />
            <line x1="8" y1="38" x2="18" y2="38" stroke="#334155" stroke-width="2" stroke-linecap="round" />
            <!-- Feedback Waves -->
            <path d="M26 12 Q29 17 26 22" stroke="#FB6221" stroke-width="1.5" stroke-linecap="round" fill="none" />
            <path d="M29 9 Q34 17 29 25" stroke="#FB6221" stroke-width="1.2" stroke-linecap="round" fill="none" />
        </g>
    </svg>

@else
    <!-- STEP 04: CAREER PREPARATION (Placement Partner Support) -->
    <svg class="w-full max-w-[180px] h-[90px]" viewBox="0 0 180 90" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="90" cy="74" rx="55" ry="10" fill="#0A0A0A" fill-opacity="0.06" />
        <!-- Offer Letter / Certificate Podium -->
        <path d="M50 48 L90 62 L130 48 L90 34 Z" fill="#F8FAFC" stroke="#CBD5E1" stroke-width="1.2" />
        <path d="M50 48 L50 56 L90 70 L90 62 Z" fill="#E2E8F0" />
        <path d="M90 62 L90 70 L130 56 L130 48 Z" fill="#CBD5E1" />

        <!-- Verified Medal Insignia with Ribbon -->
        <g transform="translate(68, 12)">
            <path d="M16 26 L8 44 L18 40 L24 44 L16 26 Z" fill="#EA580C" />
            <path d="M28 26 L20 44 L28 40 L36 44 L28 26 Z" fill="#C2410C" />
            <circle cx="22" cy="18" r="14" fill="#0A0A0A" stroke="#FB6221" stroke-width="2" />
            <circle cx="22" cy="18" r="11" fill="#1E293B" />
            <path d="M17 18 L20.5 21.5 L27 15" stroke="#FB6221" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
        </g>

        <!-- Hiring Partner Verification Pill -->
        <g transform="translate(98, 48)">
            <rect x="0" y="0" width="38" height="15" rx="4" fill="#0A0A0A" stroke="#FB6221" stroke-width="0.8" />
            <circle cx="6" cy="7.5" r="2" fill="#FB6221" />
            <text x="11" y="10.5" font-family="'IBM Plex Mono', monospace" font-size="5" font-weight="700" fill="#FFFFFF">HIRED</text>
        </g>
    </svg>
@endif
</div>

