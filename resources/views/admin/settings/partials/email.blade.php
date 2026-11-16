<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="email">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-6">
        <div>
            <h3 class="text-base font-bold text-slate-900 font-heading">Email Sender & Notification Preferences</h3>
            <p class="text-xs text-slate-500">Configure outbound email identity and automated role-based trigger alerts.</p>
        </div>

        {{-- Sender Identity --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Sender Name <span class="text-rose-500">*</span></label>
                <input
                    type="text"
                    name="mail_from_name"
                    value="{{ old('mail_from_name', $settings['mail_from_name'] ?? 'ZebLearn Academy') }}"
                    required
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                @error('mail_from_name') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Default Sender Address <span class="text-rose-500">*</span></label>
                <input
                    type="email"
                    name="mail_from_address"
                    value="{{ old('mail_from_address', $settings['mail_from_address'] ?? 'enquiry@zeblearn.com') }}"
                    required
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
                @error('mail_from_address') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Notification Trigger Toggles --}}
        <div class="pt-4 border-t border-slate-100 space-y-4">
            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-heading">Automated Notification Triggers</h4>

            <div class="space-y-3">
                {{-- Student Triggers --}}
                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-900 block">Student: Enrollment Approved Alert</span>
                        <span class="text-[11px] text-slate-500">Dispatch notification when administrator approves course admission.</span>
                    </div>
                    <input type="checkbox" name="notify_student_enrollment_approved" value="1" @checked(old('notify_student_enrollment_approved', $settings['notify_student_enrollment_approved'] ?? true)) class="rounded text-[#FB6221] focus:ring-[#FB6221] h-4 w-4">
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-900 block">Student: Enrollment Declined Alert</span>
                        <span class="text-[11px] text-slate-500">Dispatch notification when enrollment request is rejected with reason.</span>
                    </div>
                    <input type="checkbox" name="notify_student_enrollment_rejected" value="1" @checked(old('notify_student_enrollment_rejected', $settings['notify_student_enrollment_rejected'] ?? true)) class="rounded text-[#FB6221] focus:ring-[#FB6221] h-4 w-4">
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-900 block">Student: Assignment Evaluated Alert</span>
                        <span class="text-[11px] text-slate-500">Dispatch notification when instructor evaluates assignment submission.</span>
                    </div>
                    <input type="checkbox" name="notify_student_assignment_graded" value="1" @checked(old('notify_student_assignment_graded', $settings['notify_student_assignment_graded'] ?? true)) class="rounded text-[#FB6221] focus:ring-[#FB6221] h-4 w-4">
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-900 block">Student: Certificate Issued Alert</span>
                        <span class="text-[11px] text-slate-500">Dispatch notification with download link upon track completion.</span>
                    </div>
                    <input type="checkbox" name="notify_student_certificate_issued" value="1" @checked(old('notify_student_certificate_issued', $settings['notify_student_certificate_issued'] ?? true)) class="rounded text-[#FB6221] focus:ring-[#FB6221] h-4 w-4">
                </div>

                {{-- Admin & Faculty Triggers --}}
                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-900 block">Admin: New Enrollment Request Alert</span>
                        <span class="text-[11px] text-slate-500">Alert administrative staff when student requests course admission.</span>
                    </div>
                    <input type="checkbox" name="notify_admin_new_enrollment" value="1" @checked(old('notify_admin_new_enrollment', $settings['notify_admin_new_enrollment'] ?? true)) class="rounded text-[#FB6221] focus:ring-[#FB6221] h-4 w-4">
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-900 block">Admin: Payment Reconciliation Alert</span>
                        <span class="text-[11px] text-slate-500">Alert administrators when an online payment is verified.</span>
                    </div>
                    <input type="checkbox" name="notify_admin_payment_received" value="1" @checked(old('notify_admin_payment_received', $settings['notify_admin_payment_received'] ?? true)) class="rounded text-[#FB6221] focus:ring-[#FB6221] h-4 w-4">
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-900 block">Faculty: Course Teaching Allocation Alert</span>
                        <span class="text-[11px] text-slate-500">Alert faculty member when administrator assigns them to a course.</span>
                    </div>
                    <input type="checkbox" name="notify_instructor_course_assigned" value="1" @checked(old('notify_instructor_course_assigned', $settings['notify_instructor_course_assigned'] ?? true)) class="rounded text-[#FB6221] focus:ring-[#FB6221] h-4 w-4">
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-900 block">Faculty: New Assignment Submission Alert</span>
                        <span class="text-[11px] text-slate-500">Alert assigned instructors when a student submits work requiring evaluation.</span>
                    </div>
                    <input type="checkbox" name="notify_instructor_assignment_submitted" value="1" @checked(old('notify_instructor_assignment_submitted', $settings['notify_instructor_assignment_submitted'] ?? true)) class="rounded text-[#FB6221] focus:ring-[#FB6221] h-4 w-4">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#FB6221] hover:bg-[#e05417] shadow-sm shadow-orange-500/20 transition-all focus:ring-2 focus:ring-[#FB6221]"
            >
                Save Notification Settings
            </button>
        </div>
    </div>
</form>

