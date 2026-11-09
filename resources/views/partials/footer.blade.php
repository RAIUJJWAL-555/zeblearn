<footer id="contact" class="bg-[#0A0A0A] text-white">
    <div class="mx-auto max-w-6xl px-5 pb-7 pt-14 sm:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="{{ route('home') }}" class="font-['Anton'] text-3xl uppercase tracking-wide">ZebLearn</a>
                <p class="mt-4 max-w-xs text-sm leading-6 text-white/65">
                    Online SAP training with live classes, practical system exposure and career support.
                </p>
            </div>
            <div>
                <h2 class="mb-4 text-xs font-bold uppercase tracking-[0.18em] text-white/45">SAP Courses</h2>
                <ul class="space-y-3 text-sm text-white/75">
                    <li><a href="{{ route('courses.fico') }}" class="transition hover:text-[#FB6221]">SAP FICO Course</a></li>
                    <li><a href="{{ url('/#course-deck') }}" class="transition hover:text-[#FB6221]">SAP MM Course</a></li>
                    <li><a href="{{ url('/#course-deck') }}" class="transition hover:text-[#FB6221]">SAP SD Course</a></li>
                    <li><a href="{{ url('/#course-deck') }}" class="transition hover:text-[#FB6221]">SAP PP Course</a></li>
                    <li><a href="{{ url('/#course-deck') }}" class="transition hover:text-[#FB6221]">SAP Power User</a></li>
                </ul>
            </div>
            <div>
                <h2 class="mb-4 text-xs font-bold uppercase tracking-[0.18em] text-white/45">Explore</h2>
                <ul class="space-y-3 text-sm text-white/75">
                    <li><a href="{{ route('about') }}" class="transition hover:text-[#FB6221]">About ZebLearn</a></li>
                    <li><a href="{{ route('blogs') }}" class="transition hover:text-[#FB6221]">Blog &amp; Interview Prep</a></li>
                    <li><a href="https://sap.zeblearn.com/category/sap-faq/" target="_blank" rel="noopener noreferrer" class="transition hover:text-[#FB6221]">SAP FAQs</a></li>
                    <li><a href="{{ route('contact') }}" class="transition hover:text-[#FB6221]">Contact</a></li>
                    <li><a href="{{ route('book-demo') }}" class="transition hover:text-[#FB6221]">Book a Free Demo</a></li>
                </ul>
            </div>
            <div>
                <h2 class="mb-4 text-xs font-bold uppercase tracking-[0.18em] text-white/45">Get in touch</h2>
                <ul class="space-y-3 text-sm text-white/75">
                    <li><a href="tel:+918506888288" class="transition hover:text-[#FB6221]">+91 8506 888 288</a></li>
                    <li><a href="https://wa.me/917277877778" target="_blank" rel="noopener noreferrer" class="transition hover:text-[#FB6221]">WhatsApp: +91 72778 77778</a></li>
                    <li><a href="mailto:enquiry@zeblearn.com" class="transition hover:text-[#FB6221]">enquiry@zeblearn.com</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-14 border-t border-white/10 pt-6">
            <div class="flex flex-col gap-4 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} ZebLearn. All rights reserved.</p>
                <div class="flex flex-wrap gap-x-5 gap-y-2">
                    <a href="https://sap.zeblearn.com/disclaimer-policy-trademark/" class="transition hover:text-white">Privacy Policy &amp; Terms</a>
                    <a href="https://sap.zeblearn.com/refund-policy/" class="transition hover:text-white">Refund Policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>
