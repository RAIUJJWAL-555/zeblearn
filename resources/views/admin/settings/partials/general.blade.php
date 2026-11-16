<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="general">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">General Platform Identity</h3>
            <p class="text-xs text-slate-500">Core organizational information and public operational coordinates.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Platform Name <span class="text-rose-500">*</span></label>
                <input
                    type="text"
                    name="site_name"
                    value="{{ old('site_name', $settings['site_name'] ?? 'ZebLearn LMS') }}"
                    required
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                @error('site_name') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Platform Tagline</label>
                <input
                    type="text"
                    name="site_tagline"
                    value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                @error('site_tagline') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Primary Contact Email <span class="text-rose-500">*</span></label>
                <input
                    type="email"
                    name="contact_email"
                    value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                    required
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                @error('contact_email') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Primary Contact Phone</label>
                <input
                    type="text"
                    name="contact_phone"
                    value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                @error('contact_phone') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Student Support Desk URL</label>
                <input
                    type="url"
                    name="support_url"
                    value="{{ old('support_url', $settings['support_url'] ?? '') }}"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                @error('support_url') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Official Website URL</label>
                <input
                    type="url"
                    name="official_website_url"
                    value="{{ old('official_website_url', $settings['official_website_url'] ?? '') }}"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                @error('official_website_url') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Timezone <span class="text-rose-500">*</span></label>
                <select
                    name="default_timezone"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                >
                    @foreach (['Asia/Kolkata', 'UTC', 'America/New_York', 'Europe/London', 'Asia/Dubai', 'Asia/Singapore'] as $tz)
                        <option value="{{ $tz }}" @selected(old('default_timezone', $settings['default_timezone'] ?? 'Asia/Kolkata') === $tz)>
                            {{ $tz }}
                        </option>
                    @endforeach
                </select>
                @error('default_timezone') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Currency Code <span class="text-rose-500">*</span></label>
                <select
                    name="default_currency"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                >
                    <option value="INR" @selected(old('default_currency', $settings['default_currency'] ?? 'INR') === 'INR')>INR (₹ Indian Rupee)</option>
                    <option value="USD" @selected(old('default_currency', $settings['default_currency'] ?? 'INR') === 'USD')>USD ($ United States Dollar)</option>
                    <option value="EUR" @selected(old('default_currency', $settings['default_currency'] ?? 'INR') === 'EUR')>EUR (€ Euro)</option>
                    <option value="GBP" @selected(old('default_currency', $settings['default_currency'] ?? 'INR') === 'GBP')>GBP (£ British Pound)</option>
                </select>
                @error('default_currency') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Footer Copyright Text</label>
                <input
                    type="text"
                    name="footer_copyright"
                    value="{{ old('footer_copyright', $settings['footer_copyright'] ?? '') }}"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                @error('footer_copyright') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#FB6221] hover:bg-[#e05417] shadow-sm shadow-orange-500/20 transition-all focus:ring-2 focus:ring-[#FB6221]"
            >
                Save General Settings
            </button>
        </div>
    </div>
</form>
