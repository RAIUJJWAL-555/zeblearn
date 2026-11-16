<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="branding">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">Branding & Visual Palette</h3>
            <p class="text-xs text-slate-500">Brand color hex codes and official social engagement channels.</p>
        </div>

        {{-- Color Palette Preview --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Primary Accent Color <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-2">
                    <input
                        type="color"
                        value="{{ old('brand_primary_color', $settings['brand_primary_color'] ?? '#FB6221') }}"
                        oninput="this.nextElementSibling.value = this.value"
                        class="w-10 h-10 rounded-xl border border-slate-200 cursor-pointer bg-white p-1"
                    />
                    <input
                        type="text"
                        name="brand_primary_color"
                        value="{{ old('brand_primary_color', $settings['brand_primary_color'] ?? '#FB6221') }}"
                        required
                        class="w-full text-xs font-mono font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                    />
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Default: #FB6221 (ZebLearn Orange)</p>
                @error('brand_primary_color') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Dark Neutral Background <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-2">
                    <input
                        type="color"
                        value="{{ old('brand_secondary_color', $settings['brand_secondary_color'] ?? '#0A0A0A') }}"
                        oninput="this.nextElementSibling.value = this.value"
                        class="w-10 h-10 rounded-xl border border-slate-200 cursor-pointer bg-white p-1"
                    />
                    <input
                        type="text"
                        name="brand_secondary_color"
                        value="{{ old('brand_secondary_color', $settings['brand_secondary_color'] ?? '#0A0A0A') }}"
                        required
                        class="w-full text-xs font-mono font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                    />
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Default: #0A0A0A (Dark Charcoal)</p>
                @error('brand_secondary_color') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Light Canvas Background <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-2">
                    <input
                        type="color"
                        value="{{ old('brand_accent_color', $settings['brand_accent_color'] ?? '#F8F9FA') }}"
                        oninput="this.nextElementSibling.value = this.value"
                        class="w-10 h-10 rounded-xl border border-slate-200 cursor-pointer bg-white p-1"
                    />
                    <input
                        type="text"
                        name="brand_accent_color"
                        value="{{ old('brand_accent_color', $settings['brand_accent_color'] ?? '#F8F9FA') }}"
                        required
                        class="w-full text-xs font-mono font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                    />
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Default: #F8F9FA (Off White Canvas)</p>
                @error('brand_accent_color') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Social Media Links --}}
        <div class="pt-4 border-t border-slate-100 space-y-4">
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider font-heading">Official Social Media Profiles</h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">LinkedIn Profile</label>
                    <input
                        type="url"
                        name="social_linkedin"
                        value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}"
                        class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                    />
                    @error('social_linkedin') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Twitter / X Profile</label>
                    <input
                        type="url"
                        name="social_twitter"
                        value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}"
                        class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                    />
                    @error('social_twitter') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Instagram Profile</label>
                    <input
                        type="url"
                        name="social_instagram"
                        value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}"
                        class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                    />
                    @error('social_instagram') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">YouTube Channel</label>
                    <input
                        type="url"
                        name="social_youtube"
                        value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}"
                        class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                    />
                    @error('social_youtube') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Facebook Page</label>
                    <input
                        type="url"
                        name="social_facebook"
                        value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
                        class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                    />
                    @error('social_facebook') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">WhatsApp Direct Desk</label>
                    <input
                        type="url"
                        name="social_whatsapp"
                        value="{{ old('social_whatsapp', $settings['social_whatsapp'] ?? '') }}"
                        class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                    />
                    @error('social_whatsapp') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#FB6221] hover:bg-[#e05417] shadow-sm shadow-orange-500/20 transition-all focus:ring-2 focus:ring-[#FB6221]"
            >
                Save Branding Settings
            </button>
        </div>
    </div>
</form>

