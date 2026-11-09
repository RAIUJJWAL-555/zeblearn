// premium.js – visual enhancements
(() => {
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (prefersReduced.matches) return;

  // Scroll reveal using IntersectionObserver
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('[data-reveal]').forEach(el => revealObserver.observe(el));

  // Auto‑add data‑reveal to headings, cards, tiles
  document.querySelectorAll('h2, h3, .card, .bento-tile').forEach(el => {
    el.setAttribute('data-reveal', '');
  });

  // Sticky filter bar already has .glass via CSS class .blog-filter-bar
  // Add glass utility to specific elements if missing
  const glassTargets = ['.badge-chip', '.book-demo-form', '.hero-stat-chip'];
  glassTargets.forEach(sel => {
    document.querySelectorAll(sel).forEach(el => el.classList.add('glass'));
  });

  // Insert a rotated badge per page (first matching container)
  const badge = document.createElement('div');
  badge.textContent = 'Live online';
  badge.style.position = 'absolute';
  badge.style.top = '1rem';
  badge.style.right = '1rem';
  badge.style.background = 'var(--color-brand-orange)';
  badge.style.color = '#fff';
  badge.style.padding = '0.25rem 0.5rem';
  badge.style.fontSize = '0.75rem';
  badge.style.transform = 'rotate(-3deg)';
  badge.style.zIndex = '10';
  const first = document.querySelector('body');
  if (first) first.appendChild(badge);

  // Hand‑drawn SVG squiggle under a key word in hero title
  const heroTitle = document.querySelector('.hero-section h1');
  if (heroTitle) {
    const words = heroTitle.textContent.split(' ');
    const idx = Math.floor(words.length / 2);
    const before = document.createTextNode(words.slice(0, idx).join(' ') + ' ');
    const targetWord = document.createElement('span');
    targetWord.textContent = words[idx];
    const after = document.createTextNode(' ' + words.slice(idx + 1).join(' '));
    heroTitle.textContent = '';
    heroTitle.appendChild(before);
    heroTitle.appendChild(targetWord);
    heroTitle.appendChild(after);
    const svg = document.createElementNS('http://www.w3.org/2000/svg','svg');
    svg.setAttribute('viewBox','0 0 100 10');
    svg.setAttribute('preserveAspectRatio','none');
    svg.style.width = '100%';
    svg.style.height = '4px';
    svg.style.display = 'block';
    svg.style.marginTop = '4px';
    const path = document.createElementNS('http://www.w3.org/2000/svg','path');
    path.setAttribute('d','M0 5 Q25 0 50 5 T100 5');
    path.setAttribute('stroke','var(--color-brand-orange)');
    path.setAttribute('stroke-width','2');
    path.setAttribute('fill','none');
    svg.appendChild(path);
    targetWord.appendChild(svg);
  }

  // Simple hero image parallax (max 12px)
  const heroImg = document.querySelector('.parallax-hero');
  if (heroImg) {
    window.addEventListener('scroll', () => {
      const offset = window.scrollY * 0.05; // 5% of scroll
      const y = Math.min(offset, 12);
      heroImg.style.transform = `translateY(${y}px)`;
    });
  }
})();

