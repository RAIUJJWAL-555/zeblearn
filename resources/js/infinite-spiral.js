/**
 * High-Performance Smooth 60-120FPS Vanilla JS Infinite Spiral 3D SAP Module Helix
 * Zero external dependencies (No React, No React-DOM).
 * Optimized for silky-smooth hardware-accelerated transforms without frame drops.
 */

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);
const modulo = (value, divisor) => ((value % divisor) + divisor) % divisor;
const smoothstep = (min, max, value) => {
    const x = clamp((value - min) / (max - min || 1), 0, 1);
    return x * x * (3 - 2 * x);
};

const getCategoryIcon = (type) => {
    switch (type) {
        case 'finance':
            return '<svg class="w-3 h-3 text-sky-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23" /><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" /></svg>';
        case 'logistics':
            return '<svg class="w-3 h-3 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" /><polyline points="3.27 6.96 12 12.01 20.73 6.96" /><line x1="12" y1="22.08" x2="12" y2="12" /></svg>';
        case 'tech':
            return '<svg class="w-3 h-3 text-purple-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6" /><polyline points="8 6 2 12 8 18" /></svg>';
        case 'hr':
            return '<svg class="w-3 h-3 text-rose-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>';
        case 'cloud':
            return '<svg class="w-3 h-3 text-amber-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z" /></svg>';
        default:
            return '<svg class="w-3 h-3 text-[#FB6221] shrink-0" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" /></svg>';
    }
};

const MODULES = [
    { id: 'fico', code: 'FICO', title: 'SAP FICO', subtitle: 'Finance & Controlling', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
    { id: 'mm', code: 'MM', title: 'SAP MM', subtitle: 'Materials Management', tag: 'LOGISTICS', type: 'logistics', accent: '#34D399', href: '#course-deck' },
    { id: 'sd', code: 'SD', title: 'SAP SD', subtitle: 'Sales & Distribution', tag: 'SALES', type: 'logistics', accent: '#34D399', href: '#course-deck' },
    { id: 'pp', code: 'PP', title: 'SAP PP', subtitle: 'Production Planning', tag: 'MFG', type: 'logistics', accent: '#34D399', href: '#course-deck' },
    { id: 's4hana', code: 'S/4HANA', title: 'S/4HANA', subtitle: 'Next-Gen Cloud ERP', tag: 'CORE ERP', type: 'core', accent: '#FB6221', href: '#course-deck' },
    { id: 'abap', code: 'ABAP', title: 'SAP ABAP', subtitle: 'Advanced Business App', tag: 'DEV', type: 'tech', accent: '#A78BFA', href: '#course-deck' },
    { id: 'basis', code: 'BASIS', title: 'SAP BASIS', subtitle: 'System Administration', tag: 'INFRA', type: 'tech', accent: '#A78BFA', href: '#course-deck' },
    { id: 'hcm', code: 'HCM', title: 'SAP HCM', subtitle: 'Human Capital Mgmt', tag: 'HR', type: 'hr', accent: '#F472B6', href: '#course-deck' },
    { id: 'ewm', code: 'EWM', title: 'SAP EWM', subtitle: 'Extended Warehouse', tag: 'LOGISTICS', type: 'logistics', accent: '#34D399', href: '#course-deck' },
    { id: 'scm', code: 'SCM', title: 'SAP SCM', subtitle: 'Supply Chain Mgmt', tag: 'SUPPLY CHAIN', type: 'logistics', accent: '#34D399', href: '#course-deck' },
    { id: 'ariba', code: 'Ariba', title: 'SAP Ariba', subtitle: 'Strategic Sourcing', tag: 'SOURCING', type: 'cloud', accent: '#FBBF24', href: '#course-deck' },
    { id: 'sf', code: 'SF', title: 'SuccessFactors', subtitle: 'Cloud HCM Suite', tag: 'CLOUD HR', type: 'hr', accent: '#F472B6', href: '#course-deck' },
    { id: 'fscm', code: 'FSCM', title: 'SAP FSCM', subtitle: 'Fin. Supply Chain', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
    { id: 'tr', code: 'TR', title: 'SAP TR', subtitle: 'Treasury & Risk Mgmt', tag: 'TREASURY', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
    { id: 'co', code: 'CO', title: 'SAP CO', subtitle: 'Cost Controlling', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
    { id: 'fi', code: 'FI', title: 'SAP FI', subtitle: 'Financial Accounting', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
    { id: 'aa', code: 'AA', title: 'SAP AA', subtitle: 'Asset Accounting', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
    { id: 'refx', code: 'RE-FX', title: 'SAP RE-FX', subtitle: 'Real Estate Mgmt', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
    { id: 'eccs', code: 'EC-CS', title: 'SAP EC-CS', subtitle: 'Consolidation Mgmt', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
    { id: 'tm', code: 'TM', title: 'SAP TM', subtitle: 'Transportation Mgmt', tag: 'LOGISTICS', type: 'logistics', accent: '#34D399', href: '#course-deck' },
    { id: 'ibp', code: 'IBP', title: 'SAP IBP', subtitle: 'Integrated Business Plan', tag: 'SUPPLY CHAIN', type: 'logistics', accent: '#34D399', href: '#course-deck' },
    { id: 'apo', code: 'APO', title: 'SAP APO', subtitle: 'Adv. Planning & Opt.', tag: 'SUPPLY CHAIN', type: 'logistics', accent: '#34D399', href: '#course-deck' },
    { id: 'bpc', code: 'BPC', title: 'SAP BPC', subtitle: 'Budget & Planning', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
    { id: 'le', code: 'LE', title: 'SAP LE', subtitle: 'Logistics Execution', tag: 'LOGISTICS', type: 'logistics', accent: '#34D399', href: '#course-deck' },
    { id: 'srm', code: 'SRM', title: 'SAP SRM', subtitle: 'Supplier Relations', tag: 'SOURCING', type: 'cloud', accent: '#FBBF24', href: '#course-deck' },
    { id: 'wm', code: 'WM', title: 'SAP WM', subtitle: 'Warehouse Management', tag: 'LOGISTICS', type: 'logistics', accent: '#34D399', href: '#course-deck' },
    { id: 'lso', code: 'LSO', title: 'SAP LSO', subtitle: 'Learning Solution', tag: 'HR', type: 'hr', accent: '#F472B6', href: '#course-deck' },
    { id: 'om', code: 'OM', title: 'SAP OM', subtitle: 'Organizational Mgmt', tag: 'HR', type: 'hr', accent: '#F472B6', href: '#course-deck' },
    { id: 'py', code: 'PY', title: 'SAP PY', subtitle: 'Payroll Management', tag: 'HR', type: 'hr', accent: '#F472B6', href: '#course-deck' },
    { id: 'pt', code: 'PT', title: 'SAP PT', subtitle: 'Personnel Time Mgmt', tag: 'HR', type: 'hr', accent: '#F472B6', href: '#course-deck' }
];

export function initInfiniteSpiral() {
    const rootEl = document.getElementById('infinite-spiral-root');
    if (!rootEl || rootEl._spiralInitialized) return;
    rootEl._spiralInitialized = true;

    const speed = 0.48;
    const radius = 176;
    const cardWidth = 124;
    const cardHeight = 100;
    const verticalSpacing = 58;
    const perspective = 1000;
    const cardRadius = 14;
    const centerScale = 1.20;
    const cardsPerTurn = 7;
    const edgeFade = 0.32;
    const rotation = 0;
    const cardTilt = 0;

    // Clean container setup
    rootEl.innerHTML = '';
    const container = document.createElement('div');
    container.className = 'relative isolate h-full min-h-80 w-full overflow-hidden select-none';
    container.style.perspective = `${perspective}px`;
    container.style.cursor = 'grab';
    container.style.touchAction = 'pan-x';

    const list = document.createElement('div');
    list.className = 'absolute inset-0 [transform-style:preserve-3d]';
    list.setAttribute('role', 'list');
    list.setAttribute('aria-label', 'Infinite spiral gallery');

    const cardElements = [];

    MODULES.forEach((item) => {
        const a = document.createElement('a');
        a.href = item.href || '#course-deck';
        // Note: No CSS transitions on this outer element so requestAnimationFrame is 100% instantaneous without stutter
        a.className = 'absolute left-1/2 top-1/2 block overflow-hidden [backface-visibility:hidden] [transform-style:preserve-3d] [will-change:transform,opacity] cursor-pointer';
        a.style.width = `${cardWidth}px`;
        a.style.height = `${cardHeight}px`;
        a.style.borderRadius = `${cardRadius}px`;
        a.setAttribute('role', 'listitem');
        a.setAttribute('aria-label', `${item.title} module`);

        a.innerHTML = `
            <div class="absolute inset-0 flex flex-col justify-between p-2.5 bg-gradient-to-br from-[#162032] via-[#0E1626] to-[#070B13] text-white select-none overflow-hidden group/item rounded-[inherit] border border-white/20 hover:border-[#FB6221] shadow-[0_10px_24px_rgba(0,0,0,0.5)] transition-colors duration-200">
                <div class="absolute inset-x-0 top-0 h-[38%] bg-gradient-to-b from-white/[0.12] to-transparent pointer-events-none rounded-t-[inherit]"></div>
                <div class="absolute -top-3 -right-3 w-12 h-12 rounded-full opacity-25 pointer-events-none" style="background: ${item.accent || '#FB6221'};"></div>
                <div class="relative z-10 flex items-center justify-between gap-1">
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-white/[0.08] border border-white/10 text-[7px] font-mono font-bold uppercase tracking-wider" style="color: ${item.accent || '#38BDF8'};">
                        <span class="w-1.5 h-1.5 rounded-full" style="background: ${item.accent || '#FB6221'};"></span>
                        <span class="truncate max-w-[58px]">${item.tag || 'SAP'}</span>
                    </span>
                    ${getCategoryIcon(item.type)}
                </div>
                <div class="relative z-10 my-auto py-0.5">
                    <div class="font-heading text-[13px] font-black tracking-tight text-white leading-tight flex items-baseline gap-1">
                        <span class="text-[8px] font-mono font-extrabold text-[#FB6221] uppercase tracking-wider">SAP</span>
                        <span class="truncate">${item.code || item.title.replace(/^SAP\s+/, '')}</span>
                    </div>
                    ${item.subtitle ? `<div class="font-sans text-[8px] text-slate-300 font-medium leading-tight mt-0.5 truncate max-w-full">${item.subtitle}</div>` : ''}
                </div>
                <div class="relative z-10 flex items-center justify-between text-[7px] font-mono text-slate-400 pt-1 border-t border-white/10">
                    <span class="inline-flex items-center gap-1 text-slate-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_6px_#34D399]"></span>
                        <span>Live Access</span>
                    </span>
                    <span class="text-[#FB6221] font-bold group-hover/item:translate-x-0.5 transition-transform duration-150">&rarr;</span>
                </div>
            </div>
        `;

        a._lastZ = -1;
        a._isVisible = true;
        list.appendChild(a);
        cardElements.push(a);
    });

    container.appendChild(list);
    rootEl.appendChild(container);

    // State variables
    let progress = 0;
    let targetProgress = 0;
    let autoSpeed = 0;
    let isHovered = false;
    let isVisible = true;
    let isDragging = false;
    let dragMoved = false;
    let lastPointerY = 0;
    let previousTime = performance.now();
    let rafId = null;

    let boundsWidth = container.clientWidth || rootEl.clientWidth || 440;
    let boundsHeight = container.clientHeight || rootEl.clientHeight || 580;

    const updateBounds = () => {
        const w = container.clientWidth || rootEl.clientWidth || 440;
        const h = container.clientHeight || rootEl.clientHeight || 580;
        if (w > 0) boundsWidth = w;
        if (h > 0) boundsHeight = h;
    };

    if ('ResizeObserver' in window) {
        const ro = new ResizeObserver(() => updateBounds());
        ro.observe(container);
    }
    window.addEventListener('resize', updateBounds, { passive: true });

    // Smooth scroll reaction
    let lastScrollY = window.scrollY;
    const handleScroll = () => {
        const nextScrollY = window.scrollY;
        const scrollDelta = nextScrollY - lastScrollY;
        lastScrollY = nextScrollY;
        if (!isVisible || scrollDelta === 0) return;
        targetProgress += clamp((scrollDelta * 0.8) / Math.max(verticalSpacing * 2, 1), -1.2, 1.2);
    };
    window.addEventListener('scroll', handleScroll, { passive: true });

    // Pointer Drag interactions
    container.addEventListener('mouseenter', () => { isHovered = true; }, { passive: true });
    container.addEventListener('mouseleave', () => { isHovered = false; }, { passive: true });

    container.addEventListener('pointerdown', (e) => {
        if (e.button !== 0) return;
        isDragging = true;
        dragMoved = false;
        lastPointerY = e.clientY;
        targetProgress = progress;
        container.setPointerCapture(e.pointerId);
        container.style.cursor = 'grabbing';
    });

    container.addEventListener('pointermove', (e) => {
        if (!isDragging) return;
        const pointerDelta = e.clientY - lastPointerY;
        lastPointerY = e.clientY;
        if (Math.abs(pointerDelta) > 1.0) dragMoved = true;
        targetProgress -= pointerDelta / Math.max(verticalSpacing, 1);
    });

    const stopDragging = (e) => {
        if (!isDragging) return;
        isDragging = false;
        if (e.currentTarget && e.currentTarget.hasPointerCapture && e.currentTarget.hasPointerCapture(e.pointerId)) {
            e.currentTarget.releasePointerCapture(e.pointerId);
        }
        container.style.cursor = 'grab';
    };

    container.addEventListener('pointerup', stopDragging);
    container.addEventListener('pointercancel', stopDragging);

    container.addEventListener('click', (e) => {
        if (dragMoved) {
            e.preventDefault();
            e.stopPropagation();
            dragMoved = false;
        }
    }, true);

    // Lifecycle: Pause requestAnimationFrame when element is outside viewport
    const startLoop = () => {
        if (!rafId && isVisible) {
            previousTime = performance.now();
            rafId = requestAnimationFrame(render);
        }
    };

    const stopLoop = () => {
        if (rafId) {
            cancelAnimationFrame(rafId);
            rafId = null;
        }
    };

    const intersectionObserver = new IntersectionObserver(([entry]) => {
        isVisible = entry.isIntersecting;
        if (isVisible) {
            startLoop();
        } else {
            stopLoop();
        }
    }, { threshold: 0.05 });
    intersectionObserver.observe(container);

    // Ultra-optimized 60-120FPS render loop
    const count = MODULES.length;
    const half = count / 2;
    const fadeStart = clamp(1 - edgeFade, 0, 0.98);
    const turnSize = Math.max(cardsPerTurn, 1);
    const radFactor = (360 / turnSize) * (Math.PI / 180);

    function render(time) {
        if (!isVisible) {
            rafId = null;
            return;
        }

        const delta = Math.min((time - previousTime) * 0.001, 0.04);
        previousTime = time;

        const desiredAutoSpeed = !isDragging && !isHovered ? speed : 0;
        const speedBlend = 1 - Math.exp(-delta * 8);
        autoSpeed += (desiredAutoSpeed - autoSpeed) * speedBlend;
        targetProgress += autoSpeed * delta;

        const followBlend = 1 - Math.exp(-delta * (isDragging ? 24 : 12));
        progress += (targetProgress - progress) * followBlend;

        const fit = Math.min(1, boundsWidth / (cardWidth * 2.8), boundsHeight / (cardHeight * 2.35));
        const responsiveRadius = Math.min(radius, Math.max(72, boundsWidth * 0.36)) * fit;
        const currentProgress = progress;

        for (let i = 0; i < count; i++) {
            const card = cardElements[i];
            const offset = modulo(i - currentProgress + half, count) - half;
            const edge = Math.abs(offset) / half;
            const opacity = 1 - smoothstep(fadeStart, 1, edge);

            // Cull offscreen cards
            if (opacity < 0.02) {
                if (card._isVisible) {
                    card.style.opacity = '0';
                    card.style.pointerEvents = 'none';
                    card.style.visibility = 'hidden';
                    card._isVisible = false;
                }
                continue;
            }

            if (!card._isVisible) {
                card.style.visibility = 'visible';
                card._isVisible = true;
            }

            const focus = 1 - Math.min(Math.abs(offset) / (turnSize * 0.65), 1);
            const scale = (1 + (centerScale - 1) * focus) * fit;
            const angleRadians = offset * radFactor;
            const x = Math.sin(angleRadians) * responsiveRadius;
            const z = Math.cos(angleRadians) * responsiveRadius;
            const depthScale = clamp(perspective / (perspective - z), 0.72, 1.45);
            const visualScale = scale * depthScale;
            const depth = (z / responsiveRadius + 1) * 0.5;
            const y = offset * verticalSpacing * fit;

            // Direct hardware-accelerated transform
            card.style.transform = `translate(-50%, -50%) translate3d(${x.toFixed(1)}px, ${y.toFixed(1)}px, 0) scale(${visualScale.toFixed(3)})`;
            card.style.opacity = opacity.toFixed(2);
            card.style.pointerEvents = opacity > 0.25 ? 'auto' : 'none';

            const newZ = (depth * 1000 | 0) + i;
            if (card._lastZ !== newZ) {
                card.style.zIndex = newZ;
                card._lastZ = newZ;
            }
        }

        rafId = requestAnimationFrame(render);
    }

    startLoop();
}
