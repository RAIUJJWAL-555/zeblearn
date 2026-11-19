
import Alpine from 'alpinejs';
import { initMarquees } from './marquee';

window.Alpine = Alpine;
Alpine.start();

/**
 * High-performance viewport observer for lazy loading heavy 3D/WebGL modules.
 * Uses requestIdleCallback to guarantee zero main-thread blocking and preserve 60-120fps.
 */
function onVisible(elementId, callback, rootMargin = '0px') {
    const el = document.getElementById(elementId);
    if (!el) return;

    const execute = () => {
        if ('requestIdleCallback' in window) {
            requestIdleCallback(() => callback(el), { timeout: 2500 });
        } else {
            setTimeout(() => callback(el), 120);
        }
    };

    if (!('IntersectionObserver' in window)) {
        execute();
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
        for (const entry of entries) {
            if (entry.isIntersecting) {
                obs.disconnect();
                execute();
                break;
            }
        }
    }, { rootMargin });

    observer.observe(el);
}

function initApp() {
    // Mobile navigation
    const menuButton = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuButton && mobileMenu) {
        const closeMenu = () => {
            mobileMenu.classList.add('hidden');
            menuButton.setAttribute('aria-expanded', 'false');
            menuButton.setAttribute('aria-label', 'Open navigation menu');
        };

        menuButton.addEventListener('click', () => {
            const isOpen = !mobileMenu.classList.contains('hidden');

            mobileMenu.classList.toggle('hidden');
            menuButton.setAttribute('aria-expanded', String(!isOpen));
            menuButton.setAttribute(
                'aria-label',
                isOpen ? 'Open navigation menu' : 'Close navigation menu'
            );
        });

        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeMenu);
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeMenu();
        });

        document.addEventListener('click', event => {
            if (
                !mobileMenu.contains(event.target) &&
                !menuButton.contains(event.target)
            ) {
                closeMenu();
            }
        });
    }

    // Contact form: open WhatsApp with enquiry details
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', event => {
            event.preventDefault();

            const form = contactForm;

            const name = form.elements.name.value.trim();
            const phone = form.elements.phone.value.trim();
            const email = form.elements.email.value.trim();
            const course = form.elements.course.value;
            const message = form.elements.message.value.trim();

            const enquiry = [
                'Hello ZebLearn, I have an enquiry.',
                `Name: ${name}`,
                `Phone: +91 ${phone}`,
                email ? `Email: ${email}` : '',
                `Course: ${course}`,
                message ? `Message: ${message}` : ''
            ].filter(Boolean).join('\n');

            const whatsappURL =
                `https://wa.me/917277877778?text=${encodeURIComponent(enquiry)}`;

            window.open(whatsappURL, '_blank', 'noopener,noreferrer');
        });
    }

    // Hero Student Demo Registration Form
    const heroStudentForm = document.getElementById('hero-student-form');
    if (heroStudentForm) {
        heroStudentForm.addEventListener('submit', event => {
            event.preventDefault();
            const name = document.getElementById('hero-name')?.value.trim() || '';
            const phone = document.getElementById('hero-phone')?.value.trim() || '';
            const course = document.getElementById('hero-course')?.value || 'SAP Training';

            // Record inbound lead attribution asynchronously
            if (window.fetch) {
                fetch('/api/leads', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        name: name,
                        phone: phone,
                        course_name: course,
                        source_url: window.location.href,
                        cta_type: 'hero_demo',
                        referrer: document.referrer
                    })
                }).catch(() => {});
            }

            const enquiry = [
                'Hello ZebLearn, I want to book a free live demo class.',
                `Name: ${name}`,
                `Phone: +91 ${phone}`,
                `Course: ${course}`
            ].join('\n');

            const whatsappURL = `https://wa.me/917277877778?text=${encodeURIComponent(enquiry)}`;
            window.open(whatsappURL, '_blank', 'noopener,noreferrer');

            setTimeout(() => {
                window.location.href = `/book-demo/thank-you?module=${encodeURIComponent(course)}`;
            }, 600);
        });
    }

    // Global Course Search Modal Trigger Delegation
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-search-trigger], button[aria-label="Search courses"]');
        if (trigger) {
            event.preventDefault();
            window.dispatchEvent(new CustomEvent('open-course-search'));
        }
    });

    // 3. Progressive Scroll Reveal (Shared Single IntersectionObserver)
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const revealTargets = document.querySelectorAll('[data-reveal]');

    if (!prefersReducedMotion && revealTargets.length > 0 && 'IntersectionObserver' in window) {
        document.documentElement.classList.add('js-reveal-ready');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.01,
            rootMargin: '50px 0px 50px 0px'
        });

        revealTargets.forEach(el => observer.observe(el));
    }

    // 4. Interactive Learning Journey Progress Tracker
    const progressLine = document.getElementById('journey-progress-line');
    const journeyNodes = document.querySelectorAll('.journey-node');
    const journeyCards = document.querySelectorAll('.journey-card');

    if (progressLine && journeyNodes.length > 0) {
        const updateJourneyProgress = (stepNumber) => {
            const step = Math.max(1, Math.min(4, parseInt(stepNumber, 10) || 1));
            progressLine.style.width = `${(step / 4) * 100}%`;

            journeyNodes.forEach(node => {
                const nStep = parseInt(node.getAttribute('data-step'), 10);
                const circle = node.querySelector('div');
                const label = node.querySelector('span:first-of-type');
                if (nStep <= step) {
                    node.classList.add('active');
                    if (circle) {
                        circle.classList.add('bg-[#FB6221]', 'text-white', 'ring-4', 'ring-orange-100');
                        circle.classList.remove('bg-white', 'border-2', 'border-slate-300', 'text-slate-700');
                    }
                    if (label) {
                        label.classList.add('text-[#0A0A0A]');
                        label.classList.remove('text-slate-600');
                    }
                } else {
                    node.classList.remove('active');
                    if (circle) {
                        circle.classList.remove('bg-[#FB6221]', 'text-white', 'ring-4', 'ring-orange-100');
                        circle.classList.add('bg-white', 'border-2', 'border-slate-300', 'text-slate-700');
                    }
                    if (label) {
                        label.classList.remove('text-[#0A0A0A]');
                        label.classList.add('text-slate-600');
                    }
                }
            });
        };

        journeyNodes.forEach(node => {
            node.addEventListener('click', () => {
                const step = node.getAttribute('data-step');
                updateJourneyProgress(step);
            });
        });

        journeyCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                const step = card.getAttribute('data-step');
                updateJourneyProgress(step);
            });
        });

        if (!prefersReducedMotion && 'IntersectionObserver' in window) {
            const journeyObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const step = entry.target.getAttribute('data-step');
                        updateJourneyProgress(step);
                    }
                });
            }, { threshold: 0.5 });

            journeyCards.forEach(card => journeyObserver.observe(card));
        }
    }

    // 5. Desktop Mouse-Follow Tilt for 3D Hero Sculpture
    const sculptureContainer = document.getElementById('hero-3d-container');
    const sculptureTarget = document.getElementById('hero-3d-artwork');

    if (sculptureContainer && sculptureTarget && !prefersReducedMotion && window.innerWidth >= 1024) {
        let isHovered = false;
        let mouseX = 0;
        let mouseY = 0;
        let currentRotateX = 0;
        let currentRotateY = 0;
        let rafId = null;

        const updateTilt = () => {
            if (!isHovered) {
                // Smoothly decay back to rest
                currentRotateX += (0 - currentRotateX) * 0.08;
                currentRotateY += (0 - currentRotateY) * 0.08;
            } else {
                // Target rotation: restrained maximum ±4 degrees
                const targetRotateX = -mouseY * 4.0;
                const targetRotateY = mouseX * 4.0;
                currentRotateX += (targetRotateX - currentRotateX) * 0.08;
                currentRotateY += (targetRotateY - currentRotateY) * 0.08;
            }

            sculptureTarget.style.transform = `perspective(1200px) rotateX(${currentRotateX.toFixed(2)}deg) rotateY(${currentRotateY.toFixed(2)}deg)`;

            if (isHovered || Math.abs(currentRotateX) > 0.04 || Math.abs(currentRotateY) > 0.04) {
                rafId = requestAnimationFrame(updateTilt);
            } else {
                sculptureTarget.style.transform = '';
                rafId = null;
            }
        };

        const heroSection = sculptureContainer.closest('section') || sculptureContainer;

        heroSection.addEventListener('mouseenter', () => {
            isHovered = true;
            if (!rafId) rafId = requestAnimationFrame(updateTilt);
        });

        heroSection.addEventListener('mousemove', (e) => {
            const rect = heroSection.getBoundingClientRect();
            mouseX = Math.max(-1, Math.min(1, ((e.clientX - rect.left) / rect.width) * 2 - 1));
            mouseY = Math.max(-1, Math.min(1, ((e.clientY - rect.top) / rect.height) * 2 - 1));
            if (!rafId) rafId = requestAnimationFrame(updateTilt);
        });

        heroSection.addEventListener('mouseleave', () => {
            isHovered = false;
        });
    }

    // 6. LaserFlow WebGL2 Footer Simulation (Lazy loaded on viewport scroll)
    onVisible('laserflow-container', (container) => {
        import('./laserflow').then(({ initLaserFlow }) => {
            const laserflowCard = document.getElementById('laserflow-card');
            if (container && laserflowCard) {
                initLaserFlow(container, laserflowCard, {
                    beamPosition: 0.6,
                    color: '#f97316',
                    intensity: 1.8,
                    flare: 1.5,
                    spread: 1.5,
                    spill: 1,
                    fog: 0.2,
                    dust: 1.6,
                    revealImage: '/images/hero-3d-sculpture.webp',
                    revealRadius: 220,
                    revealOpacity: 0.6,
                    beamWidth: 1.5,
                    streaks: 1.5,
                    dots: 1,
                    pulse: 1.5,
                    speed: 1.5,
                    seed: 3,
                    mouseInteraction: true,
                    intro: true,
                });
            }
        }).catch(err => console.error('[LaserFlow] Lazy load failed:', err));
    });

    // 7. Awards & Recognition Certificate Showcase (Lazy loaded on viewport scroll)
    onVisible('certificate-showcase', () => {
        import('./certificate-showcase').then(({ initCertificateShowcase }) => {
            initCertificateShowcase();
        }).catch(err => console.error('[CertificateShowcase] Lazy load failed:', err));
    });

    // 7B. FlexCarousel 3D WebGL Liquid Experience (Lazy loaded on viewport scroll)
    onVisible('flex-carousel-root', () => {
        import('./flex-carousel').then(({ initFlexCarousel }) => {
            initFlexCarousel();
        }).catch(err => console.error('[FlexCarousel] Lazy load failed:', err));
    });

    // 7C. InfiniteSpiral 3D Interactive SAP Module Helix (Lazy loaded smoothly with 150px buffer)
    onVisible('infinite-spiral-root', () => {
        import('./infinite-spiral').then(({ initInfiniteSpiral }) => {
            initInfiniteSpiral();
        }).catch(err => console.error('[InfiniteSpiral] Lazy load failed:', err));
    }, '150px');

    // 7D. Continuous Infinite Marquee Engine
    try {
        initMarquees();
    } catch (err) {
        console.error('[Marquees] Init failed:', err);
    }
}

// Ensure execution even if DOMContentLoaded has already fired
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}
