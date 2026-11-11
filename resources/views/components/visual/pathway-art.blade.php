@props([
    'pathway' => 'entry', // 'entry' | 'acceleration' | 'transition'
])

@php
    $type = strtolower($pathway);
@endphp

<div class="w-full flex items-center justify-center py-2 select-none pointer-events-none zeb-illustration-art" aria-hidden="true">
@if($type === 'entry' || $type === '01' || $type === 'freshers')
    <!-- PATHWAY 01: Freshers / Start Your SAP Career -->
    <svg class="w-full max-w-[240px] h-[130px]" viewBox="0 0 240 130" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="p1-base" x1="40" y1="90" x2="200" y2="120" gradientUnits="userSpaceOnUse">
                <stop stop-color="#F1F5F9" />
                <stop offset="1" stop-color="#E2E8F0" />
            </linearGradient>
            <linearGradient id="p1-screen" x1="80" y1="35" x2="160" y2="85" gradientUnits="userSpaceOnUse">
                <stop stop-color="#0A0A0A" />
                <stop offset="1" stop-color="#1E293B" />
            </linearGradient>
            <linearGradient id="p1-accent" x1="130" y1="20" x2="190" y2="80" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FB6221" />
                <stop offset="1" stop-color="#EA580C" />
            </linearGradient>
            <filter id="p1-shadow" x="20" y="80" width="200" height="45" filterUnits="userSpaceOnUse">
                <feDropShadow dx="0" dy="8" stdDeviation="6" flood-color="#0A0A0A" flood-opacity="0.08" />
            </filter>
        </defs>

        <!-- Base Isometric Shadow -->
        <ellipse cx="120" cy="102" rx="85" ry="20" fill="#0A0A0A" fill-opacity="0.06" filter="url(#p1-shadow)" />

        <!-- Isometric Stepped Pedestal Base -->
        <path d="M50 92 L120 114 L190 92 L120 70 Z" fill="url(#p1-base)" stroke="#CBD5E1" stroke-width="1.2" />
        <path d="M50 92 L50 98 L120 120 L120 114 Z" fill="#CBD5E1" />
        <path d="M120 114 L120 120 L190 98 L190 92 Z" fill="#94A3B8" />

        <!-- Isometric Laptop Base -->
        <path d="M75 80 L135 96 L165 84 L105 68 Z" fill="#334155" stroke="#475569" stroke-width="1" />
        <path d="M75 80 L75 84 L135 100 L135 96 Z" fill="#1E293B" />
        <path d="M135 96 L135 100 L165 88 L165 84 Z" fill="#0F172A" />

        <!-- Laptop Trackpad & Keyboard Highlights -->
        <path d="M88 77 L124 87 L144 79 L108 69 Z" fill="#1E293B" />
        <path d="M112 88 L124 91 L129 88 L117 85 Z" fill="#475569" />

        <!-- Laptop Screen (Tilted Upright 2.5D) -->
        <path d="M75 80 L105 68 L105 28 L75 40 Z" fill="url(#p1-screen)" stroke="#334155" stroke-width="1.2" />
        <!-- Screen Content (SAP GUI / Code Lines) -->
        <rect x="78" y="44" width="23" height="2" rx="1" fill="#FB6221" />
        <rect x="78" y="49" width="18" height="1.8" rx="0.9" fill="#94A3B8" />
        <rect x="78" y="53" width="21" height="1.8" rx="0.9" fill="#64748B" />
        <rect x="78" y="57" width="14" height="1.8" rx="0.9" fill="#FB6221" />
        <circle cx="98" cy="45" r="1.5" fill="#FB6221" />

        <!-- Floating Isometric Graduation Cap -->
        <g class="transform-gpu transition-transform duration-300 group-hover:-translate-y-1">
            <!-- Cap Top Diamond Plane -->
            <path d="M135 16 L175 28 L135 40 L95 28 Z" fill="#0A0A0A" stroke="#1E293B" stroke-width="1.2" />
            <path d="M135 22 L165 30 L135 38 L105 30 Z" fill="#1E293B" />
            <!-- Cap Skull Cap Underneath -->
            <path d="M118 35 Q135 44 152 35 L152 42 Q135 50 118 42 Z" fill="#0F172A" />
            <!-- Golden / Orange Tassel & Button -->
            <circle cx="135" cy="28" r="2.5" fill="#FB6221" />
            <path d="M135 28 Q155 32 165 42 L165 52" stroke="#FB6221" stroke-width="1.8" stroke-linecap="round" fill="none" />
            <circle cx="165" cy="53" r="2" fill="#FB6221" />
        </g>

        <!-- Stepped Learning Books / Modular Blocks -->
        <g>
            <path d="M150 78 L180 86 L200 76 L170 68 Z" fill="url(#p1-accent)" />
            <path d="M150 78 L150 82 L180 90 L180 86 Z" fill="#C2410C" />
            <path d="M180 86 L180 90 L200 80 L200 76 Z" fill="#9A3412" />

            <!-- Book 2 on top -->
            <path d="M154 72 L180 79 L196 71 L170 64 Z" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="1" />
            <path d="M154 72 L154 75 L180 82 L180 79 Z" fill="#E2E8F0" />
            <path d="M180 79 L180 82 L196 74 L196 71 Z" fill="#CBD5E1" />
        </g>

        <!-- Floating Verification / Stamp Badge -->
        <g class="transform-gpu transition-transform duration-300 group-hover:scale-105">
            <circle cx="185" cy="38" r="14" fill="#FFFFFF" stroke="#FB6221" stroke-width="1.5" />
            <circle cx="185" cy="38" r="11" fill="#FFF7ED" />
            <path d="M180 38 L183.5 41.5 L190.5 34.5" stroke="#FB6221" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </g>
    </svg>

@elseif($type === 'acceleration' || $type === '02' || $type === 'domain')
    <!-- PATHWAY 02: Domain & Working Professionals / Upgrade Your Career -->
    <svg class="w-full max-w-[240px] h-[130px]" viewBox="0 0 240 130" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="p2-base" x1="40" y1="90" x2="200" y2="120" gradientUnits="userSpaceOnUse">
                <stop stop-color="#F8FAFC" />
                <stop offset="1" stop-color="#E2E8F0" />
            </linearGradient>
            <linearGradient id="p2-bar1" x1="70" y1="80" x2="86" y2="80" gradientUnits="userSpaceOnUse">
                <stop stop-color="#94A3B8" />
                <stop offset="1" stop-color="#64748B" />
            </linearGradient>
            <linearGradient id="p2-bar2" x1="98" y1="65" x2="114" y2="65" gradientUnits="userSpaceOnUse">
                <stop stop-color="#475569" />
                <stop offset="1" stop-color="#334155" />
            </linearGradient>
            <linearGradient id="p2-bar3" x1="126" y1="45" x2="142" y2="45" gradientUnits="userSpaceOnUse">
                <stop stop-color="#1E293B" />
                <stop offset="1" stop-color="#0A0A0A" />
            </linearGradient>
            <linearGradient id="p2-bar4" x1="154" y1="25" x2="170" y2="25" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FB6221" />
                <stop offset="1" stop-color="#EA580C" />
            </linearGradient>
        </defs>

        <!-- Base Shadow -->
        <ellipse cx="120" cy="104" rx="85" ry="18" fill="#0A0A0A" fill-opacity="0.06" />

        <!-- Isometric Floor Platform -->
        <path d="M48 90 L120 114 L192 90 L120 66 Z" fill="url(#p2-base)" stroke="#CBD5E1" stroke-width="1.2" />
        <path d="M48 90 L48 96 L120 120 L120 114 Z" fill="#CBD5E1" />
        <path d="M120 114 L120 120 L192 96 L192 90 Z" fill="#94A3B8" />

        <!-- Isometric Grid Metric Floor Lines -->
        <path d="M72 82 L144 106 M96 74 L168 98" stroke="#E2E8F0" stroke-width="1" />
        <path d="M96 74 L60 86 M132 70 L96 82 M168 66 L132 78" stroke="#E2E8F0" stroke-width="1" />

        <!-- 3D Bar 1 (Baseline) -->
        <g>
            <path d="M68 84 L80 88 L80 72 L68 68 Z" fill="#94A3B8" />
            <path d="M80 88 L92 84 L92 68 L80 72 Z" fill="#64748B" />
            <path d="M68 68 L80 72 L92 68 L80 64 Z" fill="#CBD5E1" />
        </g>

        <!-- 3D Bar 2 (Mid-Growth) -->
        <g>
            <path d="M96 76 L108 80 L108 54 L96 50 Z" fill="#475569" />
            <path d="M108 80 L120 76 L120 50 L108 54 Z" fill="#334155" />
            <path d="M96 50 L108 54 L120 50 L108 46 Z" fill="#64748B" />
        </g>

        <!-- 3D Bar 3 (Senior Consultant) -->
        <g>
            <path d="M124 68 L136 72 L136 36 L124 32 Z" fill="#1E293B" />
            <path d="M136 72 L148 68 L148 32 L136 36 Z" fill="#0A0A0A" />
            <path d="M124 32 L136 36 L148 32 L136 28 Z" fill="#334155" />
        </g>

        <!-- 3D Bar 4 (Lead Solution Architect - Peak Orange) -->
        <g class="transform-gpu transition-transform duration-300 group-hover:-translate-y-1">
            <path d="M152 60 L164 64 L164 18 L152 14 Z" fill="#FB6221" />
            <path d="M164 64 L176 60 L176 14 L164 18 Z" fill="#C2410C" />
            <path d="M152 14 L164 18 L176 14 L164 10 Z" fill="#FDBA74" />
        </g>

        <!-- Dynamic 3D Upward Surge Curve & Arrow -->
        <path d="M74 62 Q110 50 136 26 T172 10" stroke="#FB6221" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="3 2" fill="none" />
        <polygon points="172,6 178,14 168,13" fill="#FB6221" />

        <!-- Isometric Executive Briefcase on Platform -->
        <g>
            <!-- Briefcase body -->
            <path d="M54 94 L80 102 L92 98 L66 90 Z" fill="#1E293B" stroke="#0A0A0A" stroke-width="1" />
            <path d="M54 94 L54 102 L80 110 L80 102 Z" fill="#0A0A0A" />
            <path d="M80 102 L80 110 L92 106 L92 98 Z" fill="#0F172A" />
            <!-- Handle & Silver Clasp -->
            <path d="M68 93 L76 95 L76 93 L68 91 Z" fill="#FB6221" />
            <circle cx="73" cy="104" r="1.5" fill="#CBD5E1" />
        </g>

        <!-- Floating Analytics Dial Badge (+65% COMP) -->
        <g class="transform-gpu transition-transform duration-300 group-hover:scale-105">
            <rect x="170" y="32" width="56" height="26" rx="6" fill="#0A0A0A" stroke="#334155" stroke-width="1" />
            <circle cx="182" cy="45" r="4.5" fill="#FB6221" />
            <path d="M180 46 L182 43.5 L184 46" stroke="#FFFFFF" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
            <text x="191" y="49" font-family="'IBM Plex Mono', monospace" font-size="8.5" font-weight="700" fill="#FFFFFF">+65%</text>
        </g>
    </svg>

@else
    <!-- PATHWAY 03: IT, Developers & Sysadmins / Transition Into SAP -->
    <svg class="w-full max-w-[240px] h-[130px]" viewBox="0 0 240 130" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="p3-base" x1="40" y1="90" x2="200" y2="120" gradientUnits="userSpaceOnUse">
                <stop stop-color="#0F172A" />
                <stop offset="1" stop-color="#020617" />
            </linearGradient>
            <linearGradient id="p3-server" x1="80" y1="30" x2="160" y2="90" gradientUnits="userSpaceOnUse">
                <stop stop-color="#1E293B" />
                <stop offset="1" stop-color="#0F172A" />
            </linearGradient>
            <linearGradient id="p3-cloud" x1="140" y1="15" x2="190" y2="55" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FB6221" />
                <stop offset="1" stop-color="#EA580C" />
            </linearGradient>
        </defs>

        <!-- Base Shadow -->
        <ellipse cx="120" cy="104" rx="85" ry="18" fill="#0A0A0A" fill-opacity="0.08" />

        <!-- Dark Enterprise Isometric Base -->
        <path d="M48 90 L120 114 L192 90 L120 66 Z" fill="url(#p3-base)" stroke="#334155" stroke-width="1.2" />
        <path d="M48 90 L48 96 L120 120 L120 114 Z" fill="#1E293B" />
        <path d="M120 114 L120 120 L192 96 L192 90 Z" fill="#0A0A0A" />

        <!-- Enterprise Circuit / Bus Connection Lines -->
        <path d="M70 82 L120 98 L170 82" stroke="#FB6221" stroke-width="1.2" stroke-linecap="round" stroke-dasharray="2 3" />
        <circle cx="70" cy="82" r="2.5" fill="#FB6221" />
        <circle cx="120" cy="98" r="2.5" fill="#FB6221" />
        <circle cx="170" cy="82" r="2.5" fill="#FB6221" />

        <!-- Tiered S/4HANA Database Server Stack (Isometric Rack) -->
        <g class="transform-gpu transition-transform duration-300 group-hover:-translate-y-1">
            <!-- Unit 1 (Bottom Tier) -->
            <path d="M92 78 L126 89 L148 81 L114 70 Z" fill="#1E293B" stroke="#334155" stroke-width="1" />
            <path d="M92 78 L92 84 L126 95 L126 89 Z" fill="#0F172A" />
            <path d="M126 89 L126 95 L148 87 L148 81 Z" fill="#020617" />
            <circle cx="100" cy="82" r="1.2" fill="#FB6221" />
            <circle cx="104" cy="83.5" r="1.2" fill="#94A3B8" />

            <!-- Unit 2 (Mid Tier - In-Memory Engine) -->
            <path d="M92 68 L126 79 L148 71 L114 60 Z" fill="#1E293B" stroke="#334155" stroke-width="1" />
            <path d="M92 68 L92 74 L126 85 L126 79 Z" fill="#0F172A" />
            <path d="M126 79 L126 85 L148 77 L148 71 Z" fill="#020617" />
            <circle cx="100" cy="72" r="1.2" fill="#FB6221" />
            <circle cx="104" cy="73.5" r="1.2" fill="#FB6221" />

            <!-- Unit 3 (Top Tier - HANA DB Core) -->
            <path d="M92 58 L126 69 L148 61 L114 50 Z" fill="#334155" stroke="#475569" stroke-width="1" />
            <path d="M92 58 L92 64 L126 75 L126 69 Z" fill="#1E293B" />
            <path d="M126 69 L126 75 L148 67 L148 61 Z" fill="#0F172A" />
            <circle cx="100" cy="62" r="1.5" fill="#FB6221" />
            <line x1="108" y1="65" x2="124" y2="70" stroke="#FB6221" stroke-width="1.2" stroke-linecap="round" />
        </g>

        <!-- Floating Code Console Badge (<ABAP/>) -->
        <g class="transform-gpu transition-transform duration-300 group-hover:scale-105">
            <rect x="42" y="32" width="62" height="26" rx="6" fill="#0A0A0A" stroke="#334155" stroke-width="1" />
            <circle cx="50" cy="39" r="1.5" fill="#FB6221" />
            <circle cx="55" cy="39" r="1.5" fill="#94A3B8" />
            <circle cx="60" cy="39" r="1.5" fill="#475569" />
            <text x="50" y="52" font-family="'IBM Plex Mono', monospace" font-size="8.5" font-weight="700" fill="#FB6221">&lt;ABAP/&gt;</text>
        </g>

        <!-- Floating Cloud Integration Hexagon / SAP BTP Node -->
        <g class="transform-gpu transition-transform duration-300 group-hover:translate-x-1">
            <path d="M175 22 L192 32 L192 52 L175 62 L158 52 L158 32 Z" fill="#0A0A0A" stroke="#FB6221" stroke-width="1.2" />
            <path d="M175 28 L187 35 L187 49 L175 56 L163 49 L163 35 Z" fill="#1E293B" />
            <!-- Cloud Icon inside -->
            <path d="M170 44 Q170 40 174 40 Q177 37 180 40 Q183 40 183 44 Z" fill="#FB6221" />
        </g>

        <!-- Connecting Data Packet Beam -->
        <path d="M104 42 Q130 25 158 38" stroke="#FB6221" stroke-width="1.5" stroke-dasharray="2 2" fill="none" />
    </svg>
@endif
</div>

