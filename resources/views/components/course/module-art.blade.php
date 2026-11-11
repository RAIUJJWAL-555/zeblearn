@props([
    'module' => 'fico',
    'status' => 'available',
    'class' => 'w-14 h-14',
])

@php
    $key = strtolower(trim($module));
    // Clean potential prefix like 'sap-'
    $key = str_replace('sap-', '', $key);
@endphp

<div class="{{ $class }} shrink-0 relative flex items-center justify-center select-none pointer-events-none zeb-illustration-art" aria-hidden="true">
@if($key === 'fico' || str_contains($key, 'financial'))
    <!-- SAP FICO: Ledger balance sheet, financial cashflow in unified brand palette -->
    <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="fico-mod-grad" x1="8" y1="8" x2="56" y2="56" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFF7ED" />
                <stop offset="1" stop-color="#FFEDD5" />
            </linearGradient>
        </defs>
        <rect x="4" y="4" width="56" height="56" rx="14" fill="url(#fico-mod-grad)" stroke="#FED7AA" stroke-width="1.2" />
        <!-- Isometric Ledger Card -->
        <path d="M16 20 L40 12 L50 20 L26 28 Z" fill="#FFFFFF" stroke="#FB6221" stroke-width="1.2" />
        <path d="M16 20 L16 34 L26 42 L26 28 Z" fill="#FB6221" />
        <path d="M26 28 L26 42 L50 34 L50 20 Z" fill="#EA580C" />
        <!-- Ledger Balance Grid Lines -->
        <line x1="28" y1="25" x2="46" y2="19" stroke="#FFEDD5" stroke-width="1" />
        <line x1="28" y1="30" x2="42" y2="25" stroke="#FFEDD5" stroke-width="1" />
        <line x1="28" y1="35" x2="48" y2="29" stroke="#FFEDD5" stroke-width="1" />
        <!-- Mini Cashflow Bar Matrix in Foreground -->
        <rect x="34" y="38" width="5" height="14" rx="1.5" fill="#0A0A0A" />
        <rect x="42" y="32" width="5" height="20" rx="1.5" fill="#FB6221" />
        <rect x="50" y="28" width="5" height="24" rx="1.5" fill="#FDBA74" />
        <!-- Currency Accent Badge -->
        <circle cx="16" cy="46" r="6" fill="#0A0A0A" />
        <text x="13.5" y="49.5" font-family="'IBM Plex Mono', monospace" font-size="8" font-weight="700" fill="#FB6221">₹</text>
    </svg>

@elseif($key === 'mm' || str_contains($key, 'material'))
    <!-- SAP MM: Inventory crate, procurement warehouse pallet in unified brand palette -->
    <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="mm-mod-grad" x1="8" y1="8" x2="56" y2="56" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFF7ED" />
                <stop offset="1" stop-color="#FFEDD5" />
            </linearGradient>
        </defs>
        <rect x="4" y="4" width="56" height="56" rx="14" fill="url(#mm-mod-grad)" stroke="#FED7AA" stroke-width="1.2" />
        <!-- Isometric Cargo Crate Base -->
        <path d="M32 14 L50 23 L32 32 L14 23 Z" fill="#FB6221" stroke="#EA580C" stroke-width="1.2" />
        <path d="M14 23 L14 41 L32 50 L32 32 Z" fill="#1E293B" stroke="#0F172A" stroke-width="1.2" />
        <path d="M32 32 L32 50 L50 41 L50 23 Z" fill="#334155" stroke="#1E293B" stroke-width="1.2" />
        <!-- Crate Diagonal Stiffeners -->
        <line x1="14" y1="23" x2="32" y2="50" stroke="#0F172A" stroke-width="1.2" />
        <line x1="32" y1="32" x2="50" y2="41" stroke="#1E293B" stroke-width="1.2" />
        <!-- Logistics Barcode Label Badge -->
        <rect x="36" y="27" width="10" height="7" rx="1" fill="#FFFFFF" />
        <line x1="38" y1="29" x2="38" y2="32" stroke="#0A0A0A" stroke-width="1" />
        <line x1="40" y1="29" x2="40" y2="32" stroke="#0A0A0A" stroke-width="1" />
        <line x1="43" y1="29" x2="43" y2="32" stroke="#FB6221" stroke-width="1.5" />
    </svg>

@elseif($key === 'sd' || str_contains($key, 'sales'))
    <!-- SAP SD: Sales distribution dispatch in unified brand palette -->
    <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="sd-mod-grad" x1="8" y1="8" x2="56" y2="56" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFF7ED" />
                <stop offset="1" stop-color="#FFEDD5" />
            </linearGradient>
        </defs>
        <rect x="4" y="4" width="56" height="56" rx="14" fill="url(#sd-mod-grad)" stroke="#FED7AA" stroke-width="1.2" />
        <!-- Isometric Dispatch Truck / Delivery Parcel -->
        <path d="M28 16 L48 24 L34 32 L14 24 Z" fill="#FB6221" />
        <path d="M14 24 L14 38 L34 46 L34 32 Z" fill="#1E293B" />
        <path d="M34 32 L34 46 L48 38 L48 24 Z" fill="#334155" />
        <!-- Sales Route Arc -->
        <path d="M18 42 Q32 54 48 42" stroke="#FB6221" stroke-width="2" stroke-linecap="round" stroke-dasharray="2 2" fill="none" />
        <circle cx="48" cy="42" r="3" fill="#FB6221" />
        <circle cx="18" cy="42" r="2" fill="#0A0A0A" />
        <!-- Checkmark Badge -->
        <circle cx="44" cy="18" r="6" fill="#0A0A0A" stroke="#FB6221" stroke-width="1" />
        <path d="M41.5 18 L43.5 20 L47 16" stroke="#FB6221" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
    </svg>

@elseif($key === 'pp' || str_contains($key, 'production'))
    <!-- SAP PP: Manufacturing gear, production assembly line in unified brand palette -->
    <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="pp-mod-grad" x1="8" y1="8" x2="56" y2="56" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFF7ED" />
                <stop offset="1" stop-color="#FFEDD5" />
            </linearGradient>
        </defs>
        <rect x="4" y="4" width="56" height="56" rx="14" fill="url(#pp-mod-grad)" stroke="#FED7AA" stroke-width="1.2" />
        <!-- Precision Production Gear (Isometric 2.5D) -->
        <g transform="translate(32, 28)">
            <!-- Outer Gear Teeth -->
            <path d="M-5 -17 L5 -17 L4 -13 L-4 -13 Z
                     M12 -12 L19 -5 L16 -3 L10 -9 Z
                     M17 -5 L17 5 L13 4 L13 -4 Z
                     M12 12 L5 19 L3 16 L9 10 Z
                     M-5 17 L5 17 L4 13 L-4 13 Z
                     M-12 12 L-19 5 L-16 3 L-10 9 Z
                     M-17 -5 L-17 5 L-13 4 L-13 -4 Z
                     M-12 -12 L-5 -19 L-3 -16 L-9 -10 Z" fill="#475569" />
            <circle cx="0" cy="0" r="13" fill="#334155" />
            <circle cx="0" cy="0" r="6" fill="#0F172A" />
            <circle cx="0" cy="0" r="2.5" fill="#FB6221" />
        </g>
        <!-- Conveyor / Assembly Line -->
        <rect x="12" y="46" width="40" height="7" rx="3.5" fill="#1E293B" />
        <circle cx="18" cy="49.5" r="2" fill="#FB6221" />
        <circle cx="26" cy="49.5" r="2" fill="#94A3B8" />
        <circle cx="34" cy="49.5" r="2" fill="#94A3B8" />
        <circle cx="42" cy="49.5" r="2" fill="#FB6221" />
    </svg>

@elseif($key === 'abap')
    <!-- SAP ABAP: Code IDE terminal, HANA DB query in unified brand palette -->
    <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="abap-mod-grad" x1="8" y1="8" x2="56" y2="56" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFF7ED" />
                <stop offset="1" stop-color="#FFEDD5" />
            </linearGradient>
        </defs>
        <rect x="4" y="4" width="56" height="56" rx="14" fill="url(#abap-mod-grad)" stroke="#FED7AA" stroke-width="1.2" />
        <!-- Terminal Window Frame -->
        <rect x="10" y="12" width="44" height="34" rx="4" fill="#0A0A0A" stroke="#334155" stroke-width="1" />
        <circle cx="15" cy="17" r="1.5" fill="#FB6221" />
        <circle cx="19" cy="17" r="1.5" fill="#94A3B8" />
        <circle cx="23" cy="17" r="1.5" fill="#475569" />
        <!-- Code syntax lines & <ABAP/> Tag -->
        <text x="14" y="30" font-family="'IBM Plex Mono', monospace" font-size="7.5" font-weight="700" fill="#FB6221">&lt;ABAP&gt;</text>
        <line x1="14" y1="36" x2="34" y2="36" stroke="#FB6221" stroke-width="1.8" stroke-linecap="round" />
        <line x1="14" y1="40" x2="26" y2="40" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round" />
        <!-- HANA In-Memory Cylinder Badge -->
        <ellipse cx="45" cy="46" rx="9" ry="3.5" fill="#FB6221" />
        <path d="M36 46 L36 51 Q45 55 54 51 L54 46 Z" fill="#C2410C" />
    </svg>

@elseif($key === 'hcm' || str_contains($key, 'successfactors') || str_contains($key, 'human'))
    <!-- SAP HCM / SuccessFactors: People team nodes in unified brand palette -->
    <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="hcm-mod-grad" x1="8" y1="8" x2="56" y2="56" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFF7ED" />
                <stop offset="1" stop-color="#FFEDD5" />
            </linearGradient>
        </defs>
        <rect x="4" y="4" width="56" height="56" rx="14" fill="url(#hcm-mod-grad)" stroke="#FED7AA" stroke-width="1.2" />
        <!-- Executive Apex Node -->
        <circle cx="32" cy="18" r="6" fill="#0A0A0A" />
        <path d="M26 27 C26 24 38 24 38 27 Z" fill="#0A0A0A" />
        <circle cx="32" cy="17" r="2" fill="#FB6221" />
        <!-- Hierarchy Branch Lines -->
        <path d="M32 27 L32 34 M18 34 L46 34 M18 34 L18 39 M46 34 L46 39" stroke="#FB6221" stroke-width="1.5" stroke-linecap="round" />
        <!-- Left Team Node -->
        <circle cx="18" cy="43" r="5" fill="#1E293B" />
        <path d="M14 51 C14 48 22 48 22 51 Z" fill="#0F172A" />
        <!-- Right Team Node -->
        <circle cx="46" cy="43" r="5" fill="#FB6221" />
        <path d="M42 51 C42 48 50 48 50 51 Z" fill="#EA580C" />
    </svg>

@elseif($key === 'basis')
    <!-- SAP Basis: Enterprise server administration in unified brand palette -->
    <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="basis-mod-grad" x1="8" y1="8" x2="56" y2="56" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFF7ED" />
                <stop offset="1" stop-color="#FFEDD5" />
            </linearGradient>
        </defs>
        <rect x="4" y="4" width="56" height="56" rx="14" fill="url(#basis-mod-grad)" stroke="#FED7AA" stroke-width="1.2" />
        <!-- Server Tray 1 -->
        <rect x="12" y="14" width="40" height="9" rx="2.5" fill="#1E293B" />
        <circle cx="18" cy="18.5" r="1.5" fill="#FB6221" />
        <circle cx="23" cy="18.5" r="1.5" fill="#94A3B8" />
        <line x1="32" y1="18.5" x2="46" y2="18.5" stroke="#64748B" stroke-width="1.5" stroke-linecap="round" />
        <!-- Server Tray 2 -->
        <rect x="12" y="27" width="40" height="9" rx="2.5" fill="#1E293B" />
        <circle cx="18" cy="31.5" r="1.5" fill="#FB6221" />
        <circle cx="23" cy="31.5" r="1.5" fill="#CBD5E1" />
        <line x1="32" y1="31.5" x2="46" y2="31.5" stroke="#64748B" stroke-width="1.5" stroke-linecap="round" />
        <!-- Server Tray 3 (Database Core) -->
        <rect x="12" y="40" width="40" height="9" rx="2.5" fill="#0A0A0A" stroke="#FB6221" stroke-width="0.8" />
        <circle cx="18" cy="44.5" r="1.5" fill="#FB6221" />
        <circle cx="23" cy="44.5" r="1.5" fill="#FB6221" />
        <text x="32" y="47" font-family="'IBM Plex Mono', monospace" font-size="6" font-weight="700" fill="#FB6221">HANA</text>
    </svg>

@elseif($key === 'ewm')
    <!-- SAP EWM: Extended warehouse management in unified brand palette -->
    <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="ewm-mod-grad" x1="8" y1="8" x2="56" y2="56" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFF7ED" />
                <stop offset="1" stop-color="#FFEDD5" />
            </linearGradient>
        </defs>
        <rect x="4" y="4" width="56" height="56" rx="14" fill="url(#ewm-mod-grad)" stroke="#FED7AA" stroke-width="1.2" />
        <!-- Warehouse Shelving Uprights -->
        <line x1="16" y1="12" x2="16" y2="52" stroke="#64748B" stroke-width="2" />
        <line x1="48" y1="12" x2="48" y2="52" stroke="#64748B" stroke-width="2" />
        <line x1="16" y1="26" x2="48" y2="26" stroke="#94A3B8" stroke-width="1.5" />
        <line x1="16" y1="40" x2="48" y2="40" stroke="#94A3B8" stroke-width="1.5" />
        <!-- Stored Bin Cargo Boxes -->
        <rect x="20" y="16" width="10" height="9" rx="1" fill="#FB6221" />
        <rect x="34" y="16" width="10" height="9" rx="1" fill="#0A0A0A" />
        <rect x="20" y="30" width="12" height="9" rx="1" fill="#1E293B" />
        <rect x="36" y="30" width="8" height="9" rx="1" fill="#FDBA74" />
        <!-- AGV Scanner Laser Beacon -->
        <line x1="16" y1="46" x2="48" y2="46" stroke="#FB6221" stroke-width="1.2" stroke-dasharray="2 1" />
    </svg>

@else
    <!-- Universal Enterprise SAP Academy Module Art in unified brand palette -->
    <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="def-mod-grad" x1="8" y1="8" x2="56" y2="56" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFF7ED" />
                <stop offset="1" stop-color="#FFEDD5" />
            </linearGradient>
        </defs>
        <rect x="4" y="4" width="56" height="56" rx="14" fill="url(#def-mod-grad)" stroke="#FED7AA" stroke-width="1.2" />
        <!-- Enterprise Hexagon Connector Core -->
        <path d="M32 14 L48 23 L48 41 L32 50 L16 41 L16 23 Z" fill="#0A0A0A" stroke="#FB6221" stroke-width="1.5" />
        <circle cx="32" cy="32" r="6" fill="#FB6221" />
        <circle cx="32" cy="32" r="2.5" fill="#FFFFFF" />
        <!-- Orbit Nodes -->
        <circle cx="32" cy="14" r="2" fill="#FB6221" />
        <circle cx="48" cy="23" r="2" fill="#94A3B8" />
        <circle cx="48" cy="41" r="2" fill="#FB6221" />
        <circle cx="32" cy="50" r="2" fill="#CBD5E1" />
        <circle cx="16" cy="41" r="2" fill="#94A3B8" />
        <circle cx="16" cy="23" r="2" fill="#FB6221" />
    </svg>
@endif
</div>
