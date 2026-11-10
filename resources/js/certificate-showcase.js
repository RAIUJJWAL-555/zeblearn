/**
 * ZebLearn Awards & Recognition Certificate Showcase Controller
 * Provides smooth transitions, thumbnail sync, keyboard & touch swipe navigation,
 * and optional accessible auto-rotation.
 */

export const certificates = [
    {
        id: 1,
        title: "India's Inspiring Woman Tech Leader of the Year",
        subtitle: 'Certificate of Excellence · Mrs. Bhupinder Kaur (CEO, ZebLearn)',
        presenter: 'Presented by Mishwa Entertainment',
        guest: 'Chief Guest: Bollywood Actor Arbaaz Khan',
        venue: 'Radisson Blu, New Delhi · Valid Till May 2027',
        image: '/images/certificates/certificate-1.webp',
        alt: "Certificate of Excellence - India's Inspiring Woman Tech Leader of the Year awarded to Mrs. Bhupinder Kaur, CEO of ZebLearn Technologies",
        shortLabel: '01 · Woman Tech Leader',
    },
    {
        id: 2,
        title: 'Global SAP Training Spotlight Award',
        subtitle: 'Certificate of Excellence · Most Promising & High Benchmark in Education 2026',
        presenter: 'Supported by MSME Convention #startupindia & Brandman India',
        guest: 'Organized by IEMS & NTT Global',
        venue: 'Crowne Plaza, Mayur Vihar, New Delhi · Mar 29th, 2026',
        image: '/images/certificates/certificate-2.webp',
        alt: 'Certificate of Excellence - Global SAP Training Spotlight Award presented to ZebLearn International ERP School',
        shortLabel: '02 · Global Spotlight',
    },
    {
        id: 3,
        title: 'Best SAP Training Company in India',
        subtitle: "Certificate of Achievement · India's Leadership Excellence Awards 2026",
        presenter: 'Presented by Insights Success & IEA',
        guest: 'Chief Guest: Bollywood Actress Amrita Rao',
        venue: 'Radisson Blu Hotel, Dwarka, New Delhi · May 3rd, 2026',
        image: '/images/certificates/certificate-3.webp',
        alt: 'Certificate of Achievement - Best SAP Training Company in India awarded to ZebLearn Technologies Pvt. Ltd.',
        shortLabel: '03 · Best SAP Company',
    },
];

export function initCertificateShowcase() {
    const root = document.getElementById('certificate-showcase');
    if (!root) return null;

    const featuredImg = document.getElementById('featured-cert-img');
    const infoBlock = document.getElementById('cert-info-block');
    const titleEl = document.getElementById('cert-title');
    const subtitleEl = document.getElementById('cert-subtitle');
    const presenterEl = document.getElementById('cert-presenter');
    const guestEl = document.getElementById('cert-guest');
    const venueEl = document.getElementById('cert-venue');
    const counterEl = document.getElementById('cert-counter');
    const prevBtn = document.getElementById('cert-prev-btn');
    const nextBtn = document.getElementById('cert-next-btn');
    const thumbBtns = root.querySelectorAll('.cert-thumb-btn');

    let currentIndex = 0;
    let isTransitioning = false;
    let autoRotateTimer = null;
    const AUTO_ROTATE_DELAY = 7000;

    const activeClasses = [
        'border-2',
        'border-[#D4AF37]',
        'ring-1',
        'ring-[#D4AF37]/30',
        'shadow-[0_0_12px_rgba(212,175,55,0.25)]',
        'opacity-100',
        'scale-[1.02]',
    ];
    const inactiveClasses = [
        'border',
        'border-white/15',
        'opacity-60',
        'hover:opacity-100',
        'hover:border-white/35',
    ];

    function setThumbnailState(idx) {
        if (!thumbBtns || !thumbBtns.length) return;
        thumbBtns.forEach((btn, i) => {
            const isSelected = i === idx;
            btn.setAttribute('aria-selected', isSelected ? 'true' : 'false');

            const textSpan = btn.querySelector('span');

            if (isSelected) {
                btn.classList.remove(...inactiveClasses);
                btn.classList.add(...activeClasses);
                if (textSpan) {
                    textSpan.classList.add('text-amber-200/90', 'font-semibold');
                    textSpan.classList.remove('text-slate-400', 'font-medium');
                }
            } else {
                btn.classList.remove(...activeClasses);
                btn.classList.add(...inactiveClasses);
                if (textSpan) {
                    textSpan.classList.remove('text-amber-200/90', 'font-semibold');
                    textSpan.classList.add('text-slate-400', 'font-medium');
                }
            }
        });
    }

    function showCertificate(newIndex) {
        if (isTransitioning) return;
        if (newIndex === currentIndex && featuredImg.getAttribute('src') === certificates[newIndex].image) {
            return;
        }

        isTransitioning = true;
        const cert = certificates[newIndex];

        // 1. Crossfade: fade out image and info block
        if (featuredImg) featuredImg.classList.add('opacity-0');
        if (infoBlock) infoBlock.classList.add('opacity-0');

        setTimeout(() => {
            // 2. Update contents
            if (featuredImg) {
                featuredImg.src = cert.image;
                featuredImg.alt = cert.alt;
            }
            if (titleEl) titleEl.textContent = cert.title;
            if (subtitleEl) subtitleEl.textContent = cert.subtitle;
            if (presenterEl) presenterEl.textContent = cert.presenter;
            if (guestEl) guestEl.textContent = cert.guest;
            if (venueEl) venueEl.textContent = cert.venue;
            if (counterEl) {
                counterEl.textContent = `0${cert.id} / 0${certificates.length}`;
            }

            currentIndex = newIndex;
            setThumbnailState(currentIndex);

            // 3. Fade in
            if (featuredImg) featuredImg.classList.remove('opacity-0');
            if (infoBlock) infoBlock.classList.remove('opacity-0');

            setTimeout(() => {
                isTransitioning = false;
            }, 300);
        }, 200);
    }

    function next() {
        const nextIdx = (currentIndex + 1) % certificates.length;
        showCertificate(nextIdx);
    }

    function prev() {
        const prevIdx = (currentIndex - 1 + certificates.length) % certificates.length;
        showCertificate(prevIdx);
    }

    // Click to cycle next certificate
    root.addEventListener('click', (e) => {
        if (!prevBtn?.contains(e.target) && !nextBtn?.contains(e.target)) {
            next();
            restartAutoRotate();
        }
    });

    // Controls
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            next();
            restartAutoRotate();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prev();
            restartAutoRotate();
        });
    }

    thumbBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            const idx = parseInt(btn.getAttribute('data-cert-index') || '0', 10);
            showCertificate(idx);
            restartAutoRotate();
        });
    });

    // Keyboard navigation (Left / Right Arrow)
    root.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            next();
            restartAutoRotate();
        } else if (e.key === 'ArrowLeft') {
            e.preventDefault();
            prev();
            restartAutoRotate();
        }
    });

    // Mobile touch swipe handling
    let touchStartX = 0;
    let touchStartY = 0;

    root.addEventListener('touchstart', (e) => {
        if (e.touches && e.touches.length === 1) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            stopAutoRotate();
        }
    }, { passive: true });

    root.addEventListener('touchend', (e) => {
        if (e.changedTouches && e.changedTouches.length === 1) {
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;

            // Horizontal swipe with minimal vertical scroll
            if (Math.abs(deltaX) > 40 && Math.abs(deltaX) > Math.abs(deltaY)) {
                if (deltaX < 0) {
                    next();
                } else {
                    prev();
                }
            }
            startAutoRotate();
        }
    }, { passive: true });

    // Auto-rotation handling
    function startAutoRotate() {
        if (autoRotateTimer) clearInterval(autoRotateTimer);
        autoRotateTimer = setInterval(() => {
            next();
        }, AUTO_ROTATE_DELAY);
    }

    function stopAutoRotate() {
        if (autoRotateTimer) {
            clearInterval(autoRotateTimer);
            autoRotateTimer = null;
        }
    }

    function restartAutoRotate() {
        stopAutoRotate();
        startAutoRotate();
    }

    root.addEventListener('mouseenter', stopAutoRotate);
    root.addEventListener('mouseleave', startAutoRotate);
    root.addEventListener('focusin', stopAutoRotate);
    root.addEventListener('focusout', startAutoRotate);

    startAutoRotate();

    return {
        next,
        prev,
        showCertificate,
    };
}

