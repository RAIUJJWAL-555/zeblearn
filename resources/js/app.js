
document.addEventListener('DOMContentLoaded', () => {
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
});
