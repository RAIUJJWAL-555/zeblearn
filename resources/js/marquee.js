/**
 * ZebLearn High-Performance Infinite Marquee Engine
 * Drives .zeb-marquee-track elements with hardware-accelerated requestAnimationFrame.
 * - Zero forced reflows: scrollWidth is cached and observed via ResizeObserver.
 * - Zero offscreen CPU work: animation pauses completely when scrolled out of viewport.
 */
export function initMarquees() {
    const tracks = document.querySelectorAll('.zeb-marquee-track');
    if (!tracks.length) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    tracks.forEach((track) => {
        // Take over from CSS animation to guarantee smooth execution
        track.style.animation = 'none';
        track.style.webkitAnimation = 'none';
        track.style.willChange = 'transform';

        let isPaused = false;
        let isVisible = false;
        let rafId = null;
        let offset = 0;
        let lastTime = performance.now();

        // Target duration from inline CSS variable or fallback
        const durationAttr = track.style.getPropertyValue('--zeb-marquee-duration');
        const durationSec = Math.max(10, parseFloat(durationAttr) || 38);

        // Cache halfWidth outside the animation loop (Eliminates forced reflow)
        let halfWidth = track.scrollWidth / 2;

        const updateWidth = () => {
            const newHalfWidth = track.scrollWidth / 2;
            if (newHalfWidth > 0) {
                halfWidth = newHalfWidth;
            }
        };

        // ResizeObserver updates cached dimensions without running in the rAF loop
        if ('ResizeObserver' in window) {
            const ro = new ResizeObserver(updateWidth);
            ro.observe(track);
        } else {
            window.addEventListener('resize', updateWidth, { passive: true });
        }

        function step(now) {
            if (!isVisible) {
                rafId = null;
                return;
            }

            const dt = (now - lastTime) / 1000;
            lastTime = now;

            // Only advance if not paused, valid delta, and track has measured width
            if (!isPaused && dt > 0 && dt < 0.2 && halfWidth > 0) {
                const speed = halfWidth / durationSec;
                offset += speed * dt;
                if (offset >= halfWidth) {
                    offset = offset % halfWidth;
                }
                track.style.transform = `translate3d(${-offset.toFixed(2)}px, 0, 0)`;
            }

            rafId = requestAnimationFrame(step);
        }

        const startAnimation = () => {
            if (!rafId && isVisible) {
                lastTime = performance.now();
                rafId = requestAnimationFrame(step);
            }
        };

        const stopAnimation = () => {
            if (rafId) {
                cancelAnimationFrame(rafId);
                rafId = null;
            }
        };

        // Pause on direct hover over track
        track.addEventListener('mouseenter', () => { isPaused = true; });
        track.addEventListener('mouseleave', () => {
            isPaused = false;
            lastTime = performance.now();
        });

        // Touch support on mobile
        track.addEventListener('touchstart', () => { isPaused = true; }, { passive: true });
        track.addEventListener('touchend', () => {
            isPaused = false;
            lastTime = performance.now();
        }, { passive: true });

        // Viewport IntersectionObserver: Only animate when visible in screen
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        isVisible = true;
                        updateWidth();
                        startAnimation();
                    } else {
                        isVisible = false;
                        stopAnimation();
                    }
                }
            }, { rootMargin: '100px 0px' });

            observer.observe(track);
        } else {
            isVisible = true;
            startAnimation();
        }
    });
}
