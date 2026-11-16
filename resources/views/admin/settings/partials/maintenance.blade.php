<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="maintenance">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">Platform Maintenance & System Status</h3>
            <p class="text-xs text-slate-500">Temporarily restrict public and learner access during infrastructure upgrades or database migrations.</p>
        </div>

        @if(!empty($settings['maintenance_mode_enabled']))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3">
                <span class="relative flex h-3 w-3 mt-1">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                </span>
                <div class="text-xs text-rose-800">
                    <p class="font-bold">Maintenance Mode is Currently Active</p>
                    <p class="mt-0.5">Learners and anonymous visitors are receiving an HTTP 503 Service Unavailable notice. Authorized administrators bypass this barrier automatically.</p>
                </div>
            </div>
        @else
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3">
                <span class="relative flex h-3 w-3 mt-1">
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <div class="text-xs text-emerald-800">
                    <p class="font-bold">Platform Status: Fully Operational</p>
                    <p class="mt-0.5">All student portals, public courses, and admin desks are serving live traffic normally.</p>
                </div>
            </div>
        @endif

        <div class="space-y-4">
            <label class="flex items-start gap-3 p-4 rounded-xl border border-amber-200/80 bg-amber-50/40 hover:bg-amber-50/60 transition cursor-pointer">
                <input
                    type="checkbox"
                    name="maintenance_mode_enabled"
                    value="1"
                    {{ old('maintenance_mode_enabled', $settings['maintenance_mode_enabled'] ?? false) ? 'checked' : '' }}
                    class="mt-0.5 rounded text-amber-600 focus:ring-amber-500/30 focus:ring-offset-0 border-amber-300 h-4 w-4"
                />
                <div class="flex-1">
                    <span class="block text-xs font-bold text-amber-950">Activate Soft Platform Maintenance Mode</span>
                    <span class="block text-[11px] text-amber-800/80 mt-0.5">
                        Intercepts all non-admin routes with an HTTP 503 Maintenance landing screen.
                        <strong>Important:</strong> Administrators remain authenticated and have unrestricted access to all <code class="text-xs bg-amber-100/80 px-1 py-0.5 rounded">/admin/*</code> routes and authentication endpoints to disable maintenance when ready.
                    </span>
                </div>
            </label>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Custom Public Maintenance Notice</label>
                <textarea
                    name="maintenance_message"
                    rows="3"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                    placeholder="We are currently performing scheduled platform enhancements. We will be back online shortly."
                >{{ old('maintenance_message', $settings['maintenance_message'] ?? 'We are currently performing scheduled platform enhancements. We will be back online shortly.') }}</textarea>
                <p class="text-[11px] text-slate-500 mt-1">This announcement is rendered prominently on the maintenance landing page for learners and visitors.</p>
                @error('maintenance_message') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-600 space-y-1">
            <p class="font-bold text-slate-800">Admin Bypass Assurance</p>
            <p>You will <strong>never</strong> be locked out. Even in maintenance mode, administrator sessions, admin routes, and the <code class="bg-white border border-slate-200 px-1 py-0.5 rounded text-slate-900 font-mono">/login</code> gateway remain fully operational for users with the <code class="text-slate-900 font-mono">admin</code> role.</p>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
        <button
            type="submit"
            class="px-5 py-2.5 text-xs font-semibold text-white bg-[#FB6221] hover:bg-[#e05216] rounded-xl shadow-xs transition"
        >
            Save Maintenance Configuration
        </button>
    </div>
</form>

