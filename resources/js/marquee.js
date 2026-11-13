/**
 * ZebLearn Bulletproof Infinite Marquee Engine
 * Drives .zeb-marquee-track elements with hardware-accelerated requestAnimationFrame.
 * Immune to CSS caching, media query overrides, and browser rendering quirks.
 */
export function initMarquees() {
    const tracks = document.querySelectorAll('.zeb-marquee-track');
    if (!tracks.length) return;

    tracks.forEach((track) => {
        // Take over from CSS animation to guarantee smooth execution
        track.style.animation = 'none';
        track.style.webkitAnimation = 'none';
        track.style.willChange = 'transform';

        let isPaused = false;
        let offset = 0;
        let lastTime = performance.now();

        // Target duration from inline CSS variable or fallback
        const durationAttr = track.style.getPropertyValue('--zeb-marquee-duration');
        const durationSec = Math.max(10, parseFloat(durationAttr) || 38);

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

        function step(now) {
            const dt = (now - lastTime) / 1000;
            lastTime = now;

            // Only advance if not paused and tab wasn't backgrounded (dt < 0.2s)
            if (!isPaused && dt > 0 && dt < 0.2) {
                const halfWidth = track.scrollWidth / 2;
                if (halfWidth > 0) {
                    const speed = halfWidth / durationSec;
                    offset += speed * dt;
                    if (offset >= halfWidth) {
                        offset = offset % halfWidth;
                    }
                    track.style.transform = `translate3d(${-offset.toFixed(2)}px, 0, 0)`;
                }
            }

            requestAnimationFrame(step);
        }

        requestAnimationFrame(step);
    });
}

