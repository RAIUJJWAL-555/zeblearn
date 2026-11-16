<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="courses">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">Course & Enrollment Policies</h3>
            <p class="text-xs text-slate-500">Configure global enrollment accessibility, approval gates, and defaults for curriculum catalog.</p>
        </div>

        <div class="space-y-4">
            <!-- Enrollment Enabled Toggle -->
            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200/80 hover:border-slate-300 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer">
                <input
                    type="checkbox"
                    name="enrollment_enabled"
                    value="1"
                    {{ old('enrollment_enabled', $settings['enrollment_enabled'] ?? true) ? 'checked' : '' }}
                    class="mt-0.5 rounded text-[#FB6221] focus:ring-[#FB6221]/30 focus:ring-offset-0 border-slate-300 h-4 w-4"
                />
                <div class="flex-1">
                    <span class="block text-xs font-bold text-slate-800">Global Course Enrollment Available</span>
                    <span class="block text-[11px] text-slate-500 mt-0.5">When disabled, all new course enrollment buttons and checkout flows are suspended platform-wide.</span>
                </div>
            </label>

            <!-- Admin Approval Requirement Toggle -->
            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200/80 hover:border-slate-300 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer">
                <input
                    type="checkbox"
                    name="enrollment_require_admin_approval"
                    value="1"
                    {{ old('enrollment_require_admin_approval', $settings['enrollment_require_admin_approval'] ?? true) ? 'checked' : '' }}
                    class="mt-0.5 rounded text-[#FB6221] focus:ring-[#FB6221]/30 focus:ring-offset-0 border-slate-300 h-4 w-4"
                />
                <div class="flex-1">
                    <span class="block text-xs font-bold text-slate-800">Require Manual Administrator Approval for Enrollments</span>
                    <span class="block text-[11px] text-slate-500 mt-0.5">When active, student enrollment requests require approval in the Admin Enrollment Desk before curriculum access is granted.</span>
                </div>
            </label>

            <!-- Student Registration Enabled Toggle -->
            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200/80 hover:border-slate-300 bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer">
                <input
                    type="checkbox"
                    name="student_registration_enabled"
                    value="1"
                    {{ old('student_registration_enabled', $settings['student_registration_enabled'] ?? true) ? 'checked' : '' }}
                    class="mt-0.5 rounded text-[#FB6221] focus:ring-[#FB6221]/30 focus:ring-offset-0 border-slate-300 h-4 w-4"
                />
                <div class="flex-1">
                    <span class="block text-xs font-bold text-slate-800">Open Public Student Registration</span>
                    <span class="block text-[11px] text-slate-500 mt-0.5">Allow prospective learners to register self-service student accounts from the public authentication portal.</span>
                </div>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <div class="max-w-md">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Course Visibility State</label>
                <select
                    name="default_course_visibility"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                >
                    <option value="available" {{ old('default_course_visibility', $settings['default_course_visibility'] ?? 'available') === 'available' ? 'selected' : '' }}>Available (Open for enrollment)</option>
                    <option value="upcoming" {{ old('default_course_visibility', $settings['default_course_visibility'] ?? 'available') === 'upcoming' ? 'selected' : '' }}>Upcoming (Announced with demo booking)</option>
                    <option value="planned" {{ old('default_course_visibility', $settings['default_course_visibility'] ?? 'available') === 'planned' ? 'selected' : '' }}>Planned (Curriculum drafting)</option>
                </select>
                <p class="text-[11px] text-slate-500 mt-1">Default status assigned to newly created courses when not explicitly configured.</p>
                @error('default_course_visibility') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
        <button
            type="submit"
            class="px-5 py-2.5 text-xs font-semibold text-white bg-[#FB6221] hover:bg-[#e05216] rounded-xl shadow-xs transition"
        >
            Save Course Policies
        </button>
    </div>
</form>
