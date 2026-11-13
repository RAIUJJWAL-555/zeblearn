@php
    $placementProofs = [
        [
            'id' => 'proof-fico-accenture',
            'title' => 'Accenture',
            'course' => 'SAP FICO',
            'role' => 'Associate Consultant - SAP Financials',
            'doc_type' => 'Corporate Offer Letter',
            'candidate' => 'Sumit Singh',
            'poster' => '/images/banner/orange/AYUSH.webp',
            'youtube_url' => 'https://www.youtube.com/@ZEBLEARN',
            'youtube_title' => "Watch Sumit's Video Review",
            'note' => 'Accenture alumni placement record. Candidate contact details and compensation are kept confidential under privacy guidelines.',
        ],
        [
            'id' => 'proof-mm-supply',
            'title' => 'Supply Chain ERP',
            'course' => 'SAP MM',
            'role' => 'SAP MM Functional Consultant',
            'doc_type' => 'Placement Offer Confirmation',
            'candidate' => 'Sneha Nair',
            'poster' => '/images/banner/orange/DIVYA%20SHREE.webp',
            'youtube_url' => 'https://www.youtube.com/@ZEBLEARN',
            'youtube_title' => "Watch Sneha's Video Review",
            'note' => 'Materials Management candidate placement record via PlacementBaba corporate pipeline. Personal contact information redacted.',
        ],
        [
            'id' => 'proof-sd-order',
            'title' => 'Global IT Consulting',
            'course' => 'SAP SD',
            'role' => 'SAP SD Solution Analyst',
            'doc_type' => 'Employment Contract',
            'candidate' => 'Ganesh Agrawal',
            'poster' => '/images/banner/orange/PREETI.webp',
            'youtube_url' => 'https://www.youtube.com/@ZEBLEARN',
            'youtube_title' => "Watch Ganesh's Video Review",
            'note' => 'Sales & Distribution alumni placement record. Candidate identifiers redacted for privacy.',
        ],
        [
            'id' => 'proof-pp-mfg',
            'title' => 'Manufacturing Tech',
            'course' => 'SAP PP',
            'role' => 'Junior SAP PP Consultant',
            'doc_type' => 'Offer Confirmation',
            'candidate' => 'Damini Sharma',
            'poster' => '/images/banner/orange/SHRUTI.webp',
            'youtube_url' => 'https://www.youtube.com/@ZEBLEARN',
            'youtube_title' => "Watch Damini's Video Review",
            'note' => 'Production Planning placement record. Direct corporate selection via interview referral rounds.',
        ],
    ];
@endphp

<!-- =========================================================================
     SECTION 2: PLACEMENT PROOF & VERIFICATION (FLEXCAROUSEL 3D EXPERIENCE)
     ========================================================================= -->
<section id="placement-proof" class="w-full py-16 lg:py-24 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto border-t border-slate-200 overflow-hidden" aria-labelledby="placement-proof-heading">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10" data-reveal>
        <div class="max-w-2xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 border border-orange-200 text-[11px] font-mono font-bold uppercase tracking-wider text-[#FB6221]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
                <span>DON'T BELIEVE US? VERIFY EVERYTHING.</span>
            </div>
            <h2 id="placement-proof-heading" class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black uppercase text-[#0A0A0A] tracking-tight leading-tight">
               Feedback from Our Students
            </h2>
            <p class="font-sans text-sm sm:text-base text-slate-600 font-normal leading-relaxed">
                 Watch how ZebLearn's programs helped students achieve their professional goals.
            </p>
        </div>

        <div class="flex items-center gap-3">
         
        </div>
    </div>

    <!-- 3D Liquid FlexCarousel Experience (from resources/reactItems/FlexCarousel.jsx) -->
    <div class="w-full relative overflow-hidden rounded-3xl bg-[#0A0A0A] border border-slate-800 shadow-2xl mb-8" style="height: 560px;">
        <div id="flex-carousel-root" class="w-full h-full relative" style="width: 100%; height: 560px; position: relative;"></div>
    </div>

    <!-- Verification Help Note -->
    <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4" data-reveal>
        <div>
            <span class="font-heading text-sm font-bold uppercase text-[#0A0A0A] block mb-1">
                Candidate Privacy &amp; Verification
            </span>
            <p class="font-sans text-xs text-slate-600 leading-relaxed font-normal max-w-2xl">
                Personal phone numbers, addresses, and compensation details are kept confidential under student privacy guidelines. Prospective learners can verify authentic placement documentation directly with our training counselors.
            </p>
        </div>

        <a
            href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20verify%20placement%20records%20and%20speak%20with%20counselor."
            target="_blank"
            rel="noopener noreferrer"
            class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#0A0A0A] hover:bg-slate-800 text-white font-sans text-xs font-semibold uppercase tracking-wider transition-colors shadow-xs"
        >
            <span>Ask Admissions Desk</span>
            <span class="material-symbols-outlined text-[16px] text-[#FB6221]">chat</span>
        </a>
    </div>
</section>

<!-- Accessible Proof Lightbox Modal -->
<div
    id="proof-modal"
    class="fixed inset-0 z-50 bg-[#0A0A0A]/80 backdrop-blur-sm hidden items-center justify-center p-4 sm:p-6"
    role="dialog"
    aria-modal="true"
    aria-labelledby="proof-modal-title"
>
    <div class="relative w-full max-w-2xl bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50">
            <span class="font-mono text-xs font-bold text-slate-700 uppercase tracking-wider">
                Document Details
            </span>
            <button
                type="button"
                id="proof-modal-close"
                class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-600 hover:text-black hover:border-black flex items-center justify-center transition-colors"
                aria-label="Close document lightbox"
            >
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 sm:p-8 overflow-y-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span id="proof-modal-type" class="inline-block px-3 py-1 rounded-full bg-orange-50 border border-orange-200 font-mono text-xs font-bold text-[#FB6221] uppercase tracking-wider mb-2">
                        Corporate Offer Letter
                    </span>
                    <h3 id="proof-modal-title" class="font-heading text-2xl font-black uppercase text-[#0A0A0A] tracking-tight">
                        Accenture
                    </h3>
                    <p id="proof-modal-role" class="font-mono text-xs text-slate-500 mt-1">
                        Associate Consultant - SAP Financials
                    </p>
                </div>
                <a
                    id="proof-modal-youtube"
                    href="https://www.youtube.com/@zeblearn"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-sans text-xs font-bold transition-all shadow-md hover:shadow-lg"
                >
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                    <span>Watch Video Review</span>
                </a>
            </div>

            <!-- Student Placement Poster Display -->
            <div class="w-full rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 shadow-lg flex items-center justify-center p-2">
                <img
                    id="proof-modal-poster"
                    src="/images/posters/poster-sumit-accenture.svg"
                    alt="Verified Student Placement Poster"
                    class="max-h-80 w-auto rounded-xl object-contain"
                >
            </div>

            <!-- Simulated Document Card with Redaction Lines -->
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 text-white space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <span class="font-mono text-xs text-[#FB6221] font-bold">VERIFIED RECRUITMENT RECORD</span>
                    <span class="font-mono text-xs text-slate-500">REF: ZEB-PLC-CONFIDENTIAL</span>
                </div>

                <div class="space-y-2 text-xs font-mono text-slate-300">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Candidate:</span>
                        <span id="proof-modal-candidate" class="font-bold text-white">Sumit Singh</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Course Track:</span>
                        <span id="proof-modal-course" class="text-slate-300">SAP FICO</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Designation:</span>
                        <span id="proof-modal-designation" class="text-slate-300">Associate Consultant</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">CTC / Compensation:</span>
                        <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 text-[10px]">[REDACTED PER PRIVACY]</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Contact Number:</span>
                        <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 text-[10px]">+91-XXXXX-XXXXX [PROTECTED]</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                    <span>Verified via PlacementBaba Records</span>
                    <span class="text-emerald-400 font-bold">✓ Official Record</span>
                </div>
            </div>

            <p id="proof-modal-note" class="font-sans text-xs text-slate-600 leading-relaxed">
                Accenture alumni placement record. Candidate contact details and compensation are kept confidential under privacy guidelines.
            </p>

            <div class="p-4 rounded-xl bg-orange-50 border border-orange-200/80 flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px] text-[#FB6221] shrink-0">verified_user</span>
                <p class="font-sans text-xs text-slate-700 leading-normal">
                    This document record is archived on our secure verification ledger. Personal identifiers are protected in compliance with candidate NDA agreements.
                </p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
            <button
                type="button"
                onclick="closeProofModal()"
                class="px-5 py-2 rounded-full bg-[#0A0A0A] text-white font-sans text-xs font-semibold uppercase tracking-wider hover:bg-slate-800 transition-colors"
            >
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
const placementProofData = @json(collect($placementProofs)->keyBy('id'));

function openProofModal(proofId) {
    const data = placementProofData[proofId] || placementProofData['proof-fico-accenture'];
    const modal = document.getElementById('proof-modal');
    if (!modal || !data) return;

    document.getElementById('proof-modal-title').textContent = data.title;
    document.getElementById('proof-modal-role').textContent = data.role;
    document.getElementById('proof-modal-type').textContent = data.doc_type;
    document.getElementById('proof-modal-candidate').textContent = data.candidate;
    document.getElementById('proof-modal-course').textContent = data.course;
    document.getElementById('proof-modal-designation').textContent = data.role;
    document.getElementById('proof-modal-note').textContent = data.note;

    const posterEl = document.getElementById('proof-modal-poster');
    if (posterEl && data.poster) posterEl.src = data.poster;

    const youtubeEl = document.getElementById('proof-modal-youtube');
    if (youtubeEl && data.youtube_url) youtubeEl.href = data.youtube_url;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeProofModal() {
    const modal = document.getElementById('proof-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function() {
    const closeBtn = document.getElementById('proof-modal-close');
    const modal = document.getElementById('proof-modal');
    const toggleAllBtn = document.getElementById('toggle-all-proofs-btn');

    if (closeBtn) closeBtn.addEventListener('click', closeProofModal);
    if (toggleAllBtn) {
        toggleAllBtn.addEventListener('click', function() {
            openProofModal('proof-fico-accenture');
        });
    }

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeProofModal();
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeProofModal();
    });
});
</script>
@endpush
