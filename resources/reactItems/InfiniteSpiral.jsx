'use client';

import { useEffect, useMemo, useRef } from 'react';

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);
const modulo = (value, divisor) => ((value % divisor) + divisor) % divisor;
const smoothstep = (min, max, value) => {
  const x = clamp((value - min) / (max - min || 1), 0, 1);
  return x * x * (3 - 2 * x);
};

const renderCategoryIcon = type => {
  switch (type) {
    case 'finance':
      return (
        <svg className="w-3 h-3 text-sky-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
          <line x1="12" y1="1" x2="12" y2="23" />
          <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
        </svg>
      );
    case 'logistics':
      return (
        <svg className="w-3 h-3 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
          <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
          <line x1="12" y1="22.08" x2="12" y2="12" />
        </svg>
      );
    case 'tech':
      return (
        <svg className="w-3 h-3 text-purple-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
          <polyline points="16 18 22 12 16 6" />
          <polyline points="8 6 2 12 8 18" />
        </svg>
      );
    case 'hr':
      return (
        <svg className="w-3 h-3 text-rose-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
          <circle cx="9" cy="7" r="4" />
          <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
          <path d="M16 3.13a4 4 0 0 1 0 7.75" />
        </svg>
      );
    case 'cloud':
      return (
        <svg className="w-3 h-3 text-amber-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
          <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z" />
        </svg>
      );
    default:
      return (
        <svg className="w-3 h-3 text-[#FB6221] shrink-0" viewBox="0 0 24 24" fill="currentColor">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
        </svg>
      );
  }
};

const InfiniteSpiral = ({
  items = [],
  speed = 0.55,
  direction = 'up',
  animationMode = 'auto',
  radius = 170,
  cardWidth = 100,
  cardHeight = 100,
  verticalSpacing = 60,
  perspective = 1000,
  cardsPerTurn = 7,
  rotation = 0,
  cardTilt = 0,
  cardRadius = 10,
  centerScale = 1.2,
  edgeFade = 0.3,
  edgeBlur = 6,
  pauseOnHover = true,
  imageFit = 'cover',
  grayscale = 0,
  className = ''
}) => {
  const rootRef = useRef(null);
  const cardRefs = useRef([]);
  const progressRef = useRef(0);
  const targetProgressRef = useRef(0);
  const autoSpeedRef = useRef(0);
  const hoveredRef = useRef(false);
  const visibleRef = useRef(true);
  const draggingRef = useRef(false);
  const lastPointerYRef = useRef(0);
  const dragMovedRef = useRef(false);

  const normalizedItems = useMemo(
    () =>
      items.map((item, index) =>
        typeof item === 'string'
          ? { src: item, alt: `Spiral image ${index + 1}` }
          : { alt: item.title ? `${item.title} module` : `Spiral image ${index + 1}`, ...item }
      ),
    [items]
  );

  useEffect(() => {
    const root = rootRef.current;
    if (!root || normalizedItems.length === 0) return;

    let frameId;
    let previousTime = performance.now();
    let bounds = root.getBoundingClientRect();
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const scrollEnabled = animationMode === 'scroll' || animationMode === 'all';
    const scrollSpeedMultiplier = Math.max(speed, 0) / 0.55;
    let lastScrollY = window.scrollY;
    const resizeObserver = new ResizeObserver(() => {
      bounds = root.getBoundingClientRect();
    });
    resizeObserver.observe(root);
    const intersectionObserver = new IntersectionObserver(([entry]) => {
      visibleRef.current = entry.isIntersecting;
    });
    intersectionObserver.observe(root);

    const handleScroll = () => {
      const nextScrollY = window.scrollY;
      const scrollDelta = nextScrollY - lastScrollY;
      lastScrollY = nextScrollY;
      if (!scrollEnabled || !visibleRef.current || scrollDelta === 0) return;
      targetProgressRef.current += clamp(
        (scrollDelta * scrollSpeedMultiplier) / Math.max(verticalSpacing * 2, 1),
        -1.5,
        1.5
      );
    };
    window.addEventListener('scroll', handleScroll, { passive: true });

    const render = time => {
      const delta = Math.min((time - previousTime) / 1000, 0.05);
      previousTime = time;
      const autoEnabled = animationMode === 'auto' || animationMode === 'all';
      const motionPaused = draggingRef.current || (pauseOnHover && hoveredRef.current);
      const directionMultiplier = direction === 'down' ? -1 : 1;
      const desiredAutoSpeed =
        autoEnabled && visibleRef.current && !reducedMotion.matches && !motionPaused
          ? speed * directionMultiplier
          : 0;
      const speedBlend = 1 - Math.exp(-delta * 7);
      autoSpeedRef.current += (desiredAutoSpeed - autoSpeedRef.current) * speedBlend;
      targetProgressRef.current += autoSpeedRef.current * delta;

      const followBlend = 1 - Math.exp(-delta * (draggingRef.current ? 22 : 11));
      progressRef.current += (targetProgressRef.current - progressRef.current) * followBlend;

      const count = normalizedItems.length;
      const half = count / 2;
      const width = Math.max(bounds.width, 1);
      const height = Math.max(bounds.height, 1);
      const fit = Math.min(1, width / (cardWidth * 2.8), height / (cardHeight * 2.35));
      const responsiveRadius = Math.min(radius, Math.max(72, width * 0.36)) * fit;
      const fadeStart = clamp(1 - edgeFade, 0, 0.98);
      const turnSize = Math.max(cardsPerTurn, 1);

      cardRefs.current.forEach((card, index) => {
        if (!card) return;
        const offset = modulo(index - progressRef.current + half, count) - half;
        const edge = Math.min(Math.abs(offset) / Math.max(half, 1), 1);
        const opacity = 1 - smoothstep(fadeStart, 1, edge);
        const focus = 1 - Math.min(Math.abs(offset) / Math.max(turnSize * 0.65, 1), 1);
        const scale = (1 + (centerScale - 1) * focus) * fit;
        const angle = offset * (360 / turnSize) + rotation;
        const angleRadians = (angle * Math.PI) / 180;
        const x = Math.sin(angleRadians) * responsiveRadius;
        const z = Math.cos(angleRadians) * responsiveRadius;
        const depthScale = clamp(perspective / Math.max(perspective - z, 1), 0.72, 1.45);
        const visualScale = scale * depthScale;
        const depth = (z / Math.max(responsiveRadius, 1) + 1) / 2;
        const blur = edgeBlur * smoothstep(0.35, 1, edge);
        card.style.transform = `translate(-50%, -50%) translate3d(${x}px, ${offset * verticalSpacing * fit}px, 0) rotateZ(${cardTilt}deg) scale(${visualScale})`;
        card.style.opacity = opacity.toFixed(3);
        card.style.filter = blur > 0.01 ? `blur(${blur.toFixed(2)}px)` : 'none';
        card.style.zIndex = String(Math.round(depth * 100000) + index);
        card.style.pointerEvents = opacity > 0.25 ? 'auto' : 'none';
      });
      frameId = requestAnimationFrame(render);
    };

    frameId = requestAnimationFrame(render);
    return () => {
      cancelAnimationFrame(frameId);
      resizeObserver.disconnect();
      intersectionObserver.disconnect();
      window.removeEventListener('scroll', handleScroll);
    };
  }, [
    normalizedItems,
    speed,
    direction,
    animationMode,
    radius,
    perspective,
    cardWidth,
    cardHeight,
    verticalSpacing,
    cardsPerTurn,
    rotation,
    cardTilt,
    centerScale,
    edgeFade,
    edgeBlur,
    pauseOnHover
  ]);

  const dragEnabled = animationMode === 'drag' || animationMode === 'all';

  const stopDragging = event => {
    if (!draggingRef.current) return;
    draggingRef.current = false;
    if (event.currentTarget.hasPointerCapture(event.pointerId)) {
      event.currentTarget.releasePointerCapture(event.pointerId);
    }
    event.currentTarget.style.cursor = dragEnabled ? 'grab' : 'default';
  };

  return (
    <div
      ref={rootRef}
      className={`relative isolate h-full min-h-80 w-full overflow-hidden ${className}`}
      style={{
        perspective: `${perspective}px`,
        '--spiral-width': `${cardWidth}px`,
        '--spiral-height': `${cardHeight}px`,
        '--spiral-radius': `${cardRadius}px`,
        cursor: dragEnabled ? 'grab' : 'default',
        touchAction: dragEnabled ? 'pan-x' : 'auto',
        userSelect: dragEnabled ? 'none' : 'auto'
      }}
      onMouseEnter={() => {
        hoveredRef.current = true;
      }}
      onMouseLeave={() => {
        hoveredRef.current = false;
      }}
      onPointerDown={event => {
        if (!dragEnabled || event.button !== 0) return;
        draggingRef.current = true;
        dragMovedRef.current = false;
        lastPointerYRef.current = event.clientY;
        targetProgressRef.current = progressRef.current;
        event.currentTarget.setPointerCapture(event.pointerId);
        event.currentTarget.style.cursor = 'grabbing';
      }}
      onPointerMove={event => {
        if (!draggingRef.current) return;
        const pointerDelta = event.clientY - lastPointerYRef.current;
        lastPointerYRef.current = event.clientY;
        if (Math.abs(pointerDelta) > 0.5) dragMovedRef.current = true;
        targetProgressRef.current -= pointerDelta / Math.max(verticalSpacing, 1);
      }}
      onPointerUp={stopDragging}
      onPointerCancel={stopDragging}
      onClickCapture={event => {
        if (!dragMovedRef.current) return;
        event.preventDefault();
        event.stopPropagation();
        dragMovedRef.current = false;
      }}
    >
      <div className="absolute inset-0 [transform-style:preserve-3d]" role="list" aria-label="Infinite spiral gallery">
        {normalizedItems.map((item, index) => {
          const Card = item.href ? 'a' : 'div';
          return (
            <Card
              key={item.id ?? `${item.title ?? item.src}-${index}`}
              ref={node => {
                cardRefs.current[index] = node;
              }}
              className="absolute left-1/2 top-1/2 block h-[var(--spiral-height)] w-[var(--spiral-width)] overflow-hidden rounded-[var(--spiral-radius)] [backface-visibility:hidden] [transform-style:preserve-3d] [will-change:transform,opacity,filter] motion-reduce:transition-none"
              style={{ width: cardWidth, height: cardHeight, borderRadius: cardRadius }}
              href={item.href}
              target={item.target}
              rel={item.target === '_blank' ? 'noreferrer' : undefined}
              role="listitem"
              aria-label={item.label ?? item.title ?? item.alt}
            >
              {item.title ? (
                <div className="absolute inset-0 flex flex-col justify-between p-2.5 bg-gradient-to-br from-[#162032] via-[#0E1626] to-[#070B13] text-white select-none overflow-hidden group/item rounded-[inherit] border border-white/20 hover:border-[#FB6221] shadow-[0_12px_28px_rgba(0,0,0,0.6)] transition-all duration-300">
                  {/* Top Specular Glass Shine */}
                  <div className="absolute inset-x-0 top-0 h-[38%] bg-gradient-to-b from-white/[0.12] to-transparent pointer-events-none rounded-t-[inherit]"></div>

                  {/* Colored Ambient Corner Glow */}
                  <div
                    className="absolute -top-3 -right-3 w-12 h-12 rounded-full blur-md opacity-30 pointer-events-none"
                    style={{ background: item.accent || '#FB6221' }}
                  ></div>

                  {/* Subtle Grid Watermark Accent */}
                  <div className="absolute inset-0 bg-[radial-gradient(circle_at_bottom_right,rgba(251,98,33,0.08),transparent_65%)] pointer-events-none"></div>

                  {/* Top Bar: Category Pill + Micro Category Icon */}
                  <div className="relative z-10 flex items-center justify-between gap-1">
                    <span
                      className="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-white/[0.08] border border-white/10 text-[7px] font-mono font-bold uppercase tracking-wider"
                      style={{ color: item.accent || '#38BDF8' }}
                    >
                      <span className="w-1 h-1 rounded-full animate-pulse" style={{ background: item.accent || '#FB6221' }}></span>
                      <span className="truncate max-w-[58px]">{item.tag || 'SAP'}</span>
                    </span>
                    {renderCategoryIcon(item.type)}
                  </div>

                  {/* Center: Bold Module Acronym + Subtitle */}
                  <div className="relative z-10 my-auto py-0.5">
                    <div className="font-heading text-[13px] font-black tracking-tight text-white leading-tight flex items-baseline gap-1">
                      <span className="text-[8px] font-mono font-extrabold text-[#FB6221] uppercase tracking-wider">SAP</span>
                      <span className="truncate">{item.code || item.title.replace(/^SAP\s+/, '')}</span>
                    </div>
                    {item.subtitle && (
                      <div className="font-sans text-[8px] text-slate-300 font-medium leading-tight mt-0.5 truncate max-w-full">
                        {item.subtitle}
                      </div>
                    )}
                  </div>

                  {/* Bottom Status Bar */}
                  <div className="relative z-10 flex items-center justify-between text-[7px] font-mono text-slate-400 pt-1 border-t border-white/10">
                    <span className="inline-flex items-center gap-1 text-slate-300">
                      <span className="w-1 h-1 rounded-full bg-emerald-400 shadow-[0_0_5px_#34D399]"></span>
                      <span>Live Access</span>
                    </span>
                    <span className="text-[#FB6221] font-bold group-hover/item:translate-x-0.5 transition-transform">&rarr;</span>
                  </div>
                </div>
              ) : (
                <img
                  className="absolute inset-0 block h-full w-full select-none object-center"
                  src={item.src}
                  alt={item.alt}
                  loading={index < 6 ? 'eager' : 'lazy'}
                  draggable={false}
                  style={{
                    width: cardWidth,
                    height: cardHeight,
                    maxWidth: 'none',
                    maxHeight: 'none',
                    objectFit: imageFit,
                    filter: `grayscale(${Math.min(1, Math.max(0, grayscale))})`
                  }}
                />
              )}
            </Card>
          );
        })}
      </div>
    </div>
  );
};

export default InfiniteSpiral;
