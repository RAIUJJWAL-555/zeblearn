<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="payments">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">Razorpay Gateway Configuration</h3>
            <p class="text-xs text-slate-500">Manage real-time payment credentials and checkout webhook verification.</p>
        </div>

        <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/80 text-xs text-amber-900 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="space-y-1">
                <strong class="font-semibold text-amber-950">Credential Security & Protection Policy</strong>
                <p class="text-amber-800 text-[11px] leading-relaxed">
                    Existing API secrets are masked (<code>••••••••</code>) and securely hashed/protected. Submitting an empty password field or leaving the mask intact will safely preserve your current working credentials without overwriting them.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Gateway Toggle --}}
            <div class="sm:col-span-2 flex items-center justify-between p-4 rounded-xl border border-slate-200/80 bg-slate-50/50">
                <div>
                    <label class="block text-xs font-bold text-slate-900">Enable Razorpay Online Checkout</label>
                    <p class="text-[11px] text-slate-500">Allow students to process real-time debit card, UPI, netbanking, and credit card payments.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        name="razorpay_enabled"
                        value="1"
                        @checked(old('razorpay_enabled', $settings['razorpay_enabled'] ?? false))
                        class="sr-only peer"
                    >
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#FB6221]"></div>
                </label>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Environment Mode <span class="text-rose-500">*</span></label>
                <select
                    name="razorpay_mode"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                >
                    <option value="test" @selected(old('razorpay_mode', $settings['razorpay_mode'] ?? 'test') === 'test')>Test / Sandbox Mode (Simulated Payments)</option>
                    <option value="live" @selected(old('razorpay_mode', $settings['razorpay_mode'] ?? 'test') === 'live')>Live Mode (Real Bank Settlement)</option>
                </select>
                @error('razorpay_mode') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Razorpay Key ID</label>
                <input
                    type="text"
                    name="razorpay_key_id"
                    value="{{ old('razorpay_key_id', $settings['razorpay_key_id'] ?? '') }}"
                    placeholder="rzp_test_..."
                    class="w-full text-xs font-mono font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                @error('razorpay_key_id') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Razorpay Key Secret (Masked)</label>
                <input
                    type="password"
                    name="razorpay_key_secret"
                    value="{{ old('razorpay_key_secret', $settings['razorpay_key_secret'] ?? '') }}"
                    placeholder="Enter new secret or leave blank to preserve"
                    class="w-full text-xs font-mono font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                <p class="text-[11px] text-slate-400 mt-1">Leave unchanged to preserve active working secret.</p>
                @error('razorpay_key_secret') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Razorpay Webhook Secret (Masked)</label>
                <input
                    type="password"
                    name="razorpay_webhook_secret"
                    value="{{ old('razorpay_webhook_secret', $settings['razorpay_webhook_secret'] ?? '') }}"
                    placeholder="Enter new webhook secret or leave blank to preserve"
                    class="w-full text-xs font-mono font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                <p class="text-[11px] text-slate-400 mt-1">Used to verify server-to-server webhook signatures.</p>
                @error('razorpay_webhook_secret') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Webhook Guidance Box --}}
        <div class="pt-4 border-t border-slate-100 space-y-2">
            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-heading">Razorpay Dashboard Webhook Setup</h4>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-2 font-mono">
                <div>
                    <span class="text-slate-400">Webhook Endpoint URL:</span>
                    <strong class="text-slate-900 block select-all">{{ url('webhooks/razorpay') }}</strong>
                </div>
                <div>
                    <span class="text-slate-400">Active Events:</span>
                    <span class="text-emerald-700 block font-semibold">order.paid, payment.captured, payment.failed</span>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#FB6221] hover:bg-[#e05417] shadow-sm shadow-orange-500/20 transition-all focus:ring-2 focus:ring-[#FB6221]"
            >
                Save Payment Settings
            </button>
        </div>
    </div>
</form>

