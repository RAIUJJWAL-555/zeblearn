<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="seo">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">Search Engine Optimization & Social Sharing</h3>
            <p class="text-xs text-slate-500">Configure global metadata defaults, canonical base parameters, and crawler accessibility flags.</p>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Platform Meta Title <span class="text-rose-500">*</span></label>
                <input
                    type="text"
                    name="seo_default_title"
                    value="{{ old('seo_default_title', $settings['seo_default_title'] ?? 'ZebLearn LMS — Premier Corporate & Professional Upskilling Platform') }}"
                    required
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                <p class="text-[11px] text-slate-500 mt-1">Recommended length: 50–60 characters. Displayed in search engine results and browser tabs.</p>
                @error('seo_default_title') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Meta Description</label>
                <textarea
                    name="seo_default_description"
                    rows="3"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                >{{ old('seo_default_description', $settings['seo_default_description'] ?? 'Master industry-leading digital skills with ZebLearn. World-class cohorts, hands-on labs, certified instructors, and recognized credentials.') }}</textarea>
                <p class="text-[11px] text-slate-500 mt-1">Recommended length: 150–160 characters. Primary snippet rendered beneath page links in search engines.</p>
                @error('seo_default_description') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Canonical Base URL</label>
                <input
                    type="url"
                    name="seo_canonical_base_url"
                    value="{{ old('seo_canonical_base_url', $settings['seo_canonical_base_url'] ?? config('app.url')) }}"
                    placeholder="https://zeblearn.com"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                <p class="text-[11px] text-slate-500 mt-1">Root URL used for canonical link headers to consolidate page ranking signals.</p>
                @error('seo_canonical_base_url') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100">
                <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200/80 hover:border-slate-300 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer">
                    <input
                        type="checkbox"
                        name="seo_allow_indexing"
                        value="1"
                        {{ old('seo_allow_indexing', $settings['seo_allow_indexing'] ?? true) ? 'checked' : '' }}
                        class="mt-0.5 rounded text-[#FB6221] focus:ring-[#FB6221]/30 focus:ring-offset-0 border-slate-300 h-4 w-4"
                    />
                    <div class="flex-1">
                        <span class="block text-xs font-bold text-slate-800">Allow Search Engine Web Crawlers (Robots Indexing)</span>
                        <span class="block text-[11px] text-slate-500 mt-0.5">When checked, outputs <code class="text-xs bg-slate-100 px-1 py-0.5 rounded">robots: index, follow</code>. Unchecking sets <code class="text-xs bg-slate-100 px-1 py-0.5 rounded">noindex, nofollow</code> across public pages (useful during staging or test environments).</span>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
        <button
            type="submit"
            class="px-5 py-2.5 text-xs font-semibold text-white bg-[#FB6221] hover:bg-[#e05216] rounded-xl shadow-xs transition"
        >
            Save SEO Settings
        </button>
    </div>
</form>

