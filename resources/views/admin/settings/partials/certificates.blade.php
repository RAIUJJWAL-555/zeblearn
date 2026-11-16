<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="certificates">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">Certificate Issuance Configuration</h3>
            <p class="text-xs text-slate-500">Configure certificate authority metadata, authorized signature attribution, and credential numbering formats.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Accrediting Organization Name <span class="text-rose-500">*</span></label>
                <input
                    type="text"
                    name="certificate_organization_name"
                    value="{{ old('certificate_organization_name', $settings['certificate_organization_name'] ?? 'ZebLearn Academy of Digital Excellence') }}"
                    required
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                <p class="text-[11px] text-slate-500 mt-1">Appears as the primary issuing institute on all generated student certificates.</p>
                @error('certificate_organization_name') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Certificate Serial Code Prefix <span class="text-rose-500">*</span></label>
                <input
                    type="text"
                    name="certificate_prefix"
                    value="{{ old('certificate_prefix', $settings['certificate_prefix'] ?? 'ZEB') }}"
                    required
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] uppercase tracking-wider font-mono"
                />
                <p class="text-[11px] text-slate-500 mt-1">Alphanumeric prefix prepended to unique credential verification identifiers (e.g. ZEB-2026-XXXX).</p>
                @error('certificate_prefix') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Authorized Signatory Name <span class="text-rose-500">*</span></label>
                <input
                    type="text"
                    name="certificate_signatory_name"
                    value="{{ old('certificate_signatory_name', $settings['certificate_signatory_name'] ?? 'Dr. Aris Thorne') }}"
                    required
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                <p class="text-[11px] text-slate-500 mt-1">Formal name of the certifying academic officer rendered on certificate templates.</p>
                @error('certificate_signatory_name') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Authorized Signatory Title <span class="text-rose-500">*</span></label>
                <input
                    type="text"
                    name="certificate_signatory_title"
                    value="{{ old('certificate_signatory_title', $settings['certificate_signatory_title'] ?? 'Academic Director & Head of Curriculum') }}"
                    required
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                <p class="text-[11px] text-slate-500 mt-1">Official institutional designation displayed beneath the signatory signature.</p>
                @error('certificate_signatory_title') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200/80 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-[11px] text-amber-800 space-y-1">
                <p class="font-bold">Credential Integrity Note</p>
                <p>Updating certificate signatory metadata applies to newly generated or re-rendered certificates. Previously issued credential serial numbers remain cryptographically anchored in the certificates database.</p>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
        <button
            type="submit"
            class="px-5 py-2.5 text-xs font-semibold text-white bg-[#FB6221] hover:bg-[#e05216] rounded-xl shadow-xs transition"
        >
            Save Certificate Settings
        </button>
    </div>
</form>

