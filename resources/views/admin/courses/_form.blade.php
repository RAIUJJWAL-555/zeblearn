@php
    $isEdit = isset($course->id);
    $selectedCategory = old('category', $course->category ?? '');
    $selectedStatus = old('status', $course->status ?? 'available');
    $highlightsText = old('highlights', is_array($course->highlights) ? implode("\n", $course->highlights) : ($course->highlights ?? ''));
    $isPublished = old('is_published', $course->published_at ? true : false);
    $trustConfig = $course->resolved_trust_config;
    $previewRecords = $isEdit ? app(\App\Services\TrustProofService::class)->getRecordsForCourse($course) : [];

    $initialBatches = old('batches', $isEdit && isset($course->batches) && $course->batches->isNotEmpty()
        ? $course->batches->map(fn($b) => [
            'id' => $b->id,
            'name' => $b->name,
            'start_date' => $b->start_date ? $b->start_date->format('Y-m-d') : '',
            'end_date' => $b->end_date ? $b->end_date->format('Y-m-d') : '',
            'timings' => $b->timings,
            'days' => $b->days,
            'delivery_mode' => $b->delivery_mode,
            'total_seats' => $b->total_seats,
            'available_seats' => $b->available_seats,
            'status' => $b->status,
            'is_active' => (bool) $b->is_active,
        ])->values()->all()
        : []);

    $initialFaqs = old('faqs', $isEdit && isset($course->faqs) && $course->faqs->isNotEmpty()
        ? $course->faqs->map(fn($f) => [
            'id' => $f->id,
            'question' => $f->question,
            'answer' => $f->answer,
            'is_active' => (bool) $f->is_active,
        ])->values()->all()
        : []);
@endphp

<div class="space-y-8">
    {{-- Error Banner --}}
    @if ($errors->any())
        <x-ui.alert variant="danger">
            <div class="space-y-1">
                <p class="font-bold">Please correct the following errors:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </x-ui.alert>
    @endif

    {{-- SECTION 1: BASIC INFORMATION --}}
    <x-ui.card
        title="Course Identity"
        subtitle="Core title, category track, and public slug"
        padding="md"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Course Name --}}
            <div class="md:col-span-2">
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Course Name / Title <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    required
                    value="{{ old('name', $course->name) }}"
                    placeholder="e.g. SAP FICO Training, Workday HCM Masterclass"
                    class="w-full rounded-xl border @error('name') border-rose-400 bg-rose-50/20 @else border-slate-300 bg-white @enderror px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                />
                @error('name')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug --}}
            <div>
                <label for="slug" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    URL Slug <span class="text-slate-400 font-normal lowercase">(auto-generated if empty)</span>
                </label>
                <input
                    type="text"
                    id="slug"
                    name="slug"
                    value="{{ old('slug', $course->slug) }}"
                    placeholder="e.g. sap-fico-training"
                    class="w-full rounded-xl border @error('slug') border-rose-400 bg-rose-50/20 @else border-slate-300 bg-white @enderror px-4 py-3 text-sm font-mono text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                />
                @error('slug')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Category --}}
            <div>
                <label for="category" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Subject Category <span class="text-rose-500">*</span>
                </label>
                <select
                    id="category"
                    name="category"
                    required
                    class="w-full rounded-xl border @error('category') border-rose-400 bg-rose-50/20 @else border-slate-300 bg-white @enderror px-4 py-3 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs cursor-pointer"
                >
                    <option value="" disabled {{ empty($selectedCategory) ? 'selected' : '' }}>Select enterprise category...</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ $selectedCategory === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Lifecycle Status <span class="text-rose-500">*</span>
                </label>
                <select
                    id="status"
                    name="status"
                    required
                    class="w-full rounded-xl border @error('status') border-rose-400 bg-rose-50/20 @else border-slate-300 bg-white @enderror px-4 py-3 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs cursor-pointer"
                >
                    <option value="available" {{ $selectedStatus === 'available' ? 'selected' : '' }}>● Available (Open for admission)</option>
                    <option value="upcoming" {{ $selectedStatus === 'upcoming' ? 'selected' : '' }}>● Upcoming Batch (Advance booking)</option>
                    <option value="planned" {{ $selectedStatus === 'planned' ? 'selected' : '' }}>● Roadmap Planned (In development)</option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort Order --}}
            <div>
                <label for="sort_order" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Display Sort Order
                </label>
                <input
                    type="number"
                    id="sort_order"
                    name="sort_order"
                    min="0"
                    value="{{ old('sort_order', $course->sort_order ?? 0) }}"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-mono text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                />
            </div>
        </div>
    </x-ui.card>

    {{-- SECTION 2: PRICING & LOGISTICS --}}
    <x-ui.card
        title="Tuition & Delivery Logistics"
        subtitle="Pricing tiers, discount rates, duration, and class delivery"
        padding="md"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {{-- Course Fee --}}
            <div>
                <label for="fee" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Effective Fee (₹)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-mono text-sm pointer-events-none">₹</span>
                    <input
                        type="number"
                        id="fee"
                        name="fee"
                        min="0"
                        placeholder="25000"
                        value="{{ old('fee', $course->fee) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white pl-8 pr-4 py-3 text-sm font-mono text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                    />
                </div>
            </div>

            {{-- Original Fee --}}
            <div>
                <label for="original_fee" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Original Fee (₹)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-mono text-sm pointer-events-none">₹</span>
                    <input
                        type="number"
                        id="original_fee"
                        name="original_fee"
                        min="0"
                        placeholder="65000"
                        value="{{ old('original_fee', $course->original_fee) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white pl-8 pr-4 py-3 text-sm font-mono text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                    />
                </div>
            </div>

            {{-- Discount Percent --}}
            <div>
                <label for="discount_percent" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Discount Rate (%)
                </label>
                <div class="relative">
                    <input
                        type="number"
                        id="discount_percent"
                        name="discount_percent"
                        min="0"
                        max="100"
                        placeholder="60"
                        value="{{ old('discount_percent', $course->discount_percent ?? 60) }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-8 text-sm font-mono text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                    />
                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 font-mono text-xs pointer-events-none">%</span>
                </div>
            </div>

            {{-- Modules Count --}}
            <div>
                <label for="modules_count" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Total Modules
                </label>
                <input
                    type="number"
                    id="modules_count"
                    name="modules_count"
                    min="1"
                    placeholder="12"
                    value="{{ old('modules_count', $course->modules_count ?? 12) }}"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-mono text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                />
            </div>

            {{-- Duration --}}
            <div class="sm:col-span-2">
                <label for="duration" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Estimated Duration
                </label>
                <input
                    type="text"
                    id="duration"
                    name="duration"
                    placeholder="e.g. 3 Months, 6 to 9 Months"
                    value="{{ old('duration', $course->duration ?? '3 Months') }}"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                />
            </div>

            {{-- Delivery Mode --}}
            <div class="sm:col-span-2">
                <label for="delivery_mode" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Delivery Methodology
                </label>
                <input
                    type="text"
                    id="delivery_mode"
                    name="delivery_mode"
                    placeholder="e.g. Live Online + Real System Practice"
                    value="{{ old('delivery_mode', $course->delivery_mode ?? 'Live Online + Real System Practice') }}"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                />
            </div>
        </div>

        {{-- Badges & Visibility Row --}}
        <div class="pt-5 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
                <label for="tag_badge" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Tag Badge <span class="text-slate-400 font-normal">(e.g. HOT, BESTSELLER)</span>
                </label>
                <input
                    type="text"
                    id="tag_badge"
                    name="tag_badge"
                    placeholder="HOT"
                    value="{{ old('tag_badge', $course->tag_badge ?? 'HOT') }}"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-mono text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                />
            </div>

            <div>
                <label for="badge_label" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Banner Label <span class="text-slate-400 font-normal">(e.g. AVAILABLE NOW)</span>
                </label>
                <input
                    type="text"
                    id="badge_label"
                    name="badge_label"
                    placeholder="AVAILABLE NOW"
                    value="{{ old('badge_label', $course->badge_label ?? 'AVAILABLE NOW') }}"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-mono text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                />
            </div>

            {{-- Checkboxes --}}
            <div class="flex items-center gap-3 pt-4 sm:pt-6">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        id="featured"
                        name="featured"
                        value="1"
                        {{ old('featured', $course->featured) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-slate-300 text-[#FB6221] focus:ring-[#FB6221] cursor-pointer"
                    />
                    <span class="text-xs font-semibold text-slate-800">Featured Course</span>
                </label>
            </div>

            <div class="flex items-center gap-3 pt-4 sm:pt-6">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        id="is_published"
                        name="is_published"
                        value="1"
                        {{ $isPublished ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                    />
                    <span class="text-xs font-semibold text-emerald-700">Published (Live Online)</span>
                </label>
            </div>
        </div>
    </x-ui.card>

    {{-- SECTION 3: DESCRIPTIONS & HIGHLIGHTS --}}
    <x-ui.card
        title="Syllabus & Curriculum Content"
        subtitle="Marketing descriptions, key highlights, and learner overview"
        padding="md"
    >
        <div class="space-y-5">
            {{-- Short Description --}}
            <div>
                <label for="short_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Card Short Description <span class="text-rose-500">*</span>
                </label>
                <textarea
                    id="short_description"
                    name="short_description"
                    rows="3"
                    required
                    placeholder="Concise 2-sentence summary displayed on course catalog cards..."
                    class="w-full rounded-xl border @error('short_description') border-rose-400 bg-rose-50/20 @else border-slate-300 bg-white @enderror px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                >{{ old('short_description', $course->short_description) }}</textarea>
                @error('short_description')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Full Description --}}
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Detailed Course Overview
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="Comprehensive course outline, methodology, and learning expectations..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                >{{ old('description', $course->description) }}</textarea>
            </div>

            {{-- Key Highlights --}}
            <div>
                <label for="highlights" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Curriculum Highlights <span class="text-slate-400 font-normal">(one bullet per line)</span>
                </label>
                <textarea
                    id="highlights"
                    name="highlights"
                    rows="4"
                    placeholder="General Ledger (GL), AP & AR Configuration&#10;Asset Accounting & Year-End Closing&#10;24/7 Dedicated Server Sandbox Access&#10;Placement Support via PlacementBaba"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono"
                >{{ $highlightsText }}</textarea>
            </div>
        </div>
    </x-ui.card>

    {{-- SECTION: UPCOMING LIVE COHORTS & BATCHES --}}
    <x-ui.card
        title="Upcoming Live Cohorts &amp; Batches"
        subtitle="Manage upcoming cohort schedules, seats, delivery timings, and live status displayed on the public course page"
        padding="md"
    >
        <div
            x-data="{
                batches: {{ Js::from($initialBatches) }},
                addBatch() {
                    this.batches.push({
                        id: null,
                        name: 'Cohort ' + (this.batches.length + 1) + ' (Live Online)',
                        start_date: '',
                        end_date: '',
                        timings: '08:00 AM – 10:00 AM IST',
                        days: 'Sat & Sun (Weekend)',
                        delivery_mode: 'Live Online',
                        total_seats: 25,
                        available_seats: 8,
                        status: 'filling_fast',
                        is_active: true,
                    });
                },
                removeBatch(index) {
                    this.batches.splice(index, 1);
                }
            }"
            class="space-y-4"
        >
            <input type="hidden" name="batches" value="">

            {{-- Empty State --}}
            <template x-if="batches.length === 0">
                <div class="p-8 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-400 mb-2 block">event_upcoming</span>
                    <h4 class="font-heading text-sm font-bold text-slate-800 uppercase tracking-tight">No Live Cohorts Scheduled</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                        Add upcoming weekend or weekday batches so learners can view schedules and reserve their seat for the next cohort on the public landing page.
                    </p>
                    <button
                        type="button"
                        @click="addBatch()"
                        class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0A0A0A] hover:bg-[#FB6221] text-white text-xs font-mono font-bold uppercase tracking-wider transition-colors shadow-xs cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        <span>Schedule First Cohort</span>
                    </button>
                </div>
            </template>

            {{-- Cohort Cards List --}}
            <div class="space-y-4">
                <template x-for="(batch, index) in batches" :key="index">
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/70 hover:bg-slate-50 transition-all space-y-4 relative">
                        <input type="hidden" :name="'batches[' + index + '][id]'" :value="batch.id">

                        {{-- Header Bar --}}
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-[#FB6221]/10 text-[#FB6221] font-mono font-bold text-xs flex items-center justify-center" x-text="index + 1"></span>
                                <span class="font-mono text-xs font-bold uppercase tracking-wider text-slate-800">
                                    Cohort Batch Details
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-mono text-slate-600 select-none">
                                    <input
                                        type="checkbox"
                                        :name="'batches[' + index + '][is_active]'"
                                        value="1"
                                        x-model="batch.is_active"
                                        class="w-4 h-4 rounded text-[#FB6221] focus:ring-[#FB6221] border-slate-300 cursor-pointer"
                                    />
                                    <span>Active</span>
                                </label>

                                <button
                                    type="button"
                                    @click="removeBatch(index)"
                                    class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                    title="Remove this cohort"
                                >
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </div>

                        {{-- Form Fields Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- Batch Name --}}
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Cohort Name <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    :name="'batches[' + index + '][name]'"
                                    x-model="batch.name"
                                    required
                                    placeholder="e.g. Weekend Executive Cohort #12"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent"
                                />
                            </div>

                            {{-- Start Date --}}
                            <div>
                                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Start Date <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    :name="'batches[' + index + '][start_date]'"
                                    x-model="batch.start_date"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent font-mono"
                                />
                            </div>

                            {{-- End Date --}}
                            <div>
                                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    End Date <span class="text-slate-400 font-normal">(optional)</span>
                                </label>
                                <input
                                    type="date"
                                    :name="'batches[' + index + '][end_date]'"
                                    x-model="batch.end_date"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent font-mono"
                                />
                            </div>

                            {{-- Days --}}
                            <div>
                                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Days Schedule
                                </label>
                                <input
                                    type="text"
                                    :name="'batches[' + index + '][days]'"
                                    x-model="batch.days"
                                    placeholder="e.g. Sat & Sun (Weekend)"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent"
                                />
                            </div>

                            {{-- Timings --}}
                            <div>
                                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Batch Timings
                                </label>
                                <input
                                    type="text"
                                    :name="'batches[' + index + '][timings]'"
                                    x-model="batch.timings"
                                    placeholder="e.g. 08:00 AM – 10:00 AM IST"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent"
                                />
                            </div>

                            {{-- Delivery Mode --}}
                            <div>
                                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Delivery Mode
                                </label>
                                <select
                                    :name="'batches[' + index + '][delivery_mode]'"
                                    x-model="batch.delivery_mode"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent cursor-pointer"
                                >
                                    <option value="Live Online">Live Online</option>
                                    <option value="Weekend Classroom">Weekend Classroom</option>
                                    <option value="Hybrid (Live + Lab)">Hybrid (Live + Lab)</option>
                                    <option value="Fast-Track Boot Camp">Fast-Track Boot Camp</option>
                                </select>
                            </div>

                            {{-- Status Badge --}}
                            <div>
                                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Status Badge
                                </label>
                                <select
                                    :name="'batches[' + index + '][status]'"
                                    x-model="batch.status"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent cursor-pointer"
                                >
                                    <option value="filling_fast">Filling Fast (Amber)</option>
                                    <option value="open">Open / Seats Available (Green)</option>
                                    <option value="almost_full">Almost Full (Red)</option>
                                    <option value="closed">Closed / Waitlist (Gray)</option>
                                </select>
                            </div>

                            {{-- Total Seats --}}
                            <div class="sm:col-span-1">
                                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Total Seats
                                </label>
                                <input
                                    type="number"
                                    min="1"
                                    max="500"
                                    :name="'batches[' + index + '][total_seats]'"
                                    x-model.number="batch.total_seats"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent font-mono"
                                />
                            </div>

                            {{-- Available Seats --}}
                            <div class="sm:col-span-1">
                                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Available Seats
                                </label>
                                <input
                                    type="number"
                                    min="0"
                                    max="500"
                                    :name="'batches[' + index + '][available_seats]'"
                                    x-model.number="batch.available_seats"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent font-mono"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Add Button Bar --}}
            <div x-show="batches.length > 0" class="pt-2 flex justify-start">
                <button
                    type="button"
                    @click="addBatch()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 hover:text-[#FB6221] text-xs font-mono font-bold uppercase tracking-wider transition-colors shadow-2xs cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>Add Another Cohort</span>
                </button>
            </div>
        </div>
    </x-ui.card>

    {{-- SECTION: COURSE FAQS --}}
    <x-ui.card
        title="Frequently Asked Questions (FAQs)"
        subtitle="Common questions & answers displayed in the FAQ accordion on the public course page"
        padding="md"
    >
        <div
            x-data="{
                faqs: {{ Js::from($initialFaqs) }},
                addFaq() {
                    this.faqs.push({
                        id: null,
                        question: '',
                        answer: '',
                        is_active: true,
                    });
                },
                removeFaq(index) {
                    this.faqs.splice(index, 1);
                }
            }"
            class="space-y-4"
        >
            <input type="hidden" name="faqs" value="">

            {{-- Empty State --}}
            <template x-if="faqs.length === 0">
                <div class="p-8 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-400 mb-2 block">quiz</span>
                    <h4 class="font-heading text-sm font-bold text-slate-800 uppercase tracking-tight">No Course FAQs Added</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                        Provide answers to common queries regarding lab credentials, prerequisites, curriculum coverage, and placement assistance.
                    </p>
                    <button
                        type="button"
                        @click="addFaq()"
                        class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0A0A0A] hover:bg-[#FB6221] text-white text-xs font-mono font-bold uppercase tracking-wider transition-colors shadow-xs cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        <span>Add First FAQ</span>
                    </button>
                </div>
            </template>

            {{-- FAQ Cards List --}}
            <div class="space-y-4">
                <template x-for="(faq, index) in faqs" :key="index">
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/70 hover:bg-slate-50 transition-all space-y-3 relative">
                        <input type="hidden" :name="'faqs[' + index + '][id]'" :value="faq.id">

                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-[#FB6221]/10 text-[#FB6221] font-mono font-bold text-xs flex items-center justify-center" x-text="index + 1"></span>
                                <span class="font-mono text-xs font-bold uppercase tracking-wider text-slate-800">
                                    Question &amp; Answer
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-mono text-slate-600 select-none">
                                    <input
                                        type="checkbox"
                                        :name="'faqs[' + index + '][is_active]'"
                                        value="1"
                                        x-model="faq.is_active"
                                        class="w-4 h-4 rounded text-[#FB6221] focus:ring-[#FB6221] border-slate-300 cursor-pointer"
                                    />
                                    <span>Active</span>
                                </label>

                                <button
                                    type="button"
                                    @click="removeFaq(index)"
                                    class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                    title="Remove this FAQ"
                                >
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Question <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                :name="'faqs[' + index + '][question]'"
                                x-model="faq.question"
                                required
                                placeholder="e.g. Is 24/7 dedicated server sandbox access provided?"
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent"
                            />
                        </div>

                        <div>
                            <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Answer <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                :name="'faqs[' + index + '][answer]'"
                                x-model="faq.answer"
                                rows="3"
                                required
                                placeholder="Explain the details clearly..."
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent"
                            ></textarea>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Add Button Bar --}}
            <div x-show="faqs.length > 0" class="pt-2 flex justify-start">
                <button
                    type="button"
                    @click="addFaq()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 hover:text-[#FB6221] text-xs font-mono font-bold uppercase tracking-wider transition-colors shadow-2xs cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    <span>Add Another FAQ</span>
                </button>
            </div>
        </div>
    </x-ui.card>

    {{-- SECTION 4: SEO METADATA --}}
    <x-ui.card
        title="SEO Metadata"
        subtitle="Search engine indexing tags and OpenGraph meta descriptions"
        padding="md"
    >
        <div class="grid grid-cols-1 gap-5">
            <div>
                <label for="seo_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    SEO Meta Title
                </label>
                <input
                    type="text"
                    id="seo_title"
                    name="seo_title"
                    value="{{ old('seo_title', $course->seo_title) }}"
                    placeholder="e.g. SAP FICO Course Online with Live Training & Placement Support"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                />
            </div>

            <div>
                <label for="seo_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    SEO Meta Description
                </label>
                <textarea
                    id="seo_description"
                    name="seo_description"
                    rows="2"
                    placeholder="Search engine snippet describing learning outcomes and certification..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                >{{ old('seo_description', $course->seo_description) }}</textarea>
            </div>
        </div>
    </x-ui.card>

    {{-- SECTION 5: TRUST & PROOF CMS CONTROLS --}}
    <x-ui.card
        title="Trust &amp; Proof of Work CMS Controls"
        subtitle="Dynamic proof inheritance, section display limits, and zero duplicate data entry"
        padding="md"
    >
        <div class="space-y-6">
            {{-- Global Inheritance Banner --}}
            <div class="p-4 rounded-2xl bg-orange-50/60 border border-orange-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#FB6221] text-[24px] mt-0.5">verified_user</span>
                    <div>
                        <p class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">Institution-Wide Proof Inheritance</p>
                        <p class="text-xs text-slate-600 mt-0.5">
                            When enabled, global partner logos, general placement records, and institutional awards are automatically inherited by this course without manual duplication.
                        </p>
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 cursor-pointer select-none shrink-0">
                    <input
                        type="hidden"
                        name="trust_config[include_global]"
                        value="0"
                    />
                    <input
                        type="checkbox"
                        name="trust_config[include_global]"
                        value="1"
                        @checked(old('trust_config.include_global', $trustConfig['include_global'] ?? true))
                        class="w-4 h-4 rounded text-[#FB6221] focus:ring-[#FB6221] border-slate-300"
                    />
                    <span class="text-xs font-mono font-semibold uppercase text-slate-800">Inherit Global</span>
                </label>
            </div>

            {{-- Quick Link to Central Library --}}
            <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 bg-slate-50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-500 text-[18px]">hub</span>
                    <span class="text-xs text-slate-700 font-medium">Manage proof items centrally in the Trust Library:</span>
                </div>
                <a
                    href="{{ route('admin.trust.index') }}"
                    target="_blank"
                    class="text-xs font-mono font-bold text-[#FB6221] hover:underline flex items-center gap-1"
                >
                    <span>Open Central Library</span>
                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                </a>
            </div>

            {{-- Per-discipline Section Cards --}}
            <div>
                <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-3">
                    Discipline Display Quotas &amp; Landing Page Visibility
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $disciplines = [
                            'placements' => ['name' => 'Student Placements', 'icon' => 'work', 'key' => 'placements'],
                            'testimonials' => ['name' => 'Student Testimonials', 'icon' => 'format_quote', 'key' => 'testimonials'],
                            'partners' => ['name' => 'Hiring Partners', 'icon' => 'handshake', 'key' => 'partners'],
                            'trainers' => ['name' => 'Expert Faculty', 'icon' => 'school', 'key' => 'trainers'],
                            'certifications' => ['name' => 'Certification Badges', 'icon' => 'verified', 'key' => 'certifications'],
                            'projects' => ['name' => 'Capstone Projects', 'icon' => 'terminal', 'key' => 'projects'],
                            'video_stories' => ['name' => 'Video Stories', 'icon' => 'videocam', 'key' => 'video_stories'],
                            'awards' => ['name' => 'Awards & Recognitions', 'icon' => 'trophy', 'key' => 'awards'],
                        ];
                    @endphp

                    @foreach ($disciplines as $dKey => $disc)
                        @php
                            $dCfg = $trustConfig['sections'][$dKey] ?? ['enabled' => true, 'limit' => 6];
                            $itemCount = isset($previewRecords[$dKey]) ? count($previewRecords[$dKey]) : 0;
                        @endphp
                        <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-2xs space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-slate-600 text-[18px]">{{ $disc['icon'] }}</span>
                                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">{{ $disc['name'] }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $itemCount > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $itemCount }} mapped
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                    <input
                                        type="hidden"
                                        name="trust_config[sections][{{ $dKey }}][enabled]"
                                        value="0"
                                    />
                                    <input
                                        type="checkbox"
                                        name="trust_config[sections][{{ $dKey }}][enabled]"
                                        value="1"
                                        @checked(old("trust_config.sections.{$dKey}.enabled", $dCfg['enabled']))
                                        class="w-4 h-4 rounded text-[#FB6221] focus:ring-[#FB6221] border-slate-300"
                                    />
                                    <span class="text-xs text-slate-700 font-medium">Show Section</span>
                                </label>

                                <div class="flex items-center gap-2 justify-end">
                                    <label for="trust_limit_{{ $dKey }}" class="text-[11px] font-mono text-slate-500 uppercase">Limit:</label>
                                    <input
                                        type="number"
                                        id="trust_limit_{{ $dKey }}"
                                        name="trust_config[sections][{{ $dKey }}][limit]"
                                        value="{{ old("trust_config.sections.{$dKey}.limit", $dCfg['limit']) }}"
                                        min="1"
                                        max="50"
                                        class="w-16 px-2 py-1 rounded-lg border border-slate-300 text-xs text-center font-mono focus:ring-[#FB6221] focus:border-[#FB6221]"
                                    />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Currently Inherited & Mapped Items Preview --}}
            @if ($isEdit && !empty($previewRecords))
                <div class="pt-4 border-t border-slate-200">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                            Active Proofs Linked to this Course
                        </h4>
                        <span class="text-[11px] font-mono text-slate-500">Live Render Preview</span>
                    </div>
                    <div class="space-y-2">
                        @foreach ($previewRecords as $groupKey => $recs)
                            @if (count($recs) > 0)
                                <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/70">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-mono font-semibold uppercase text-slate-800">{{ ucfirst($groupKey) }} ({{ count($recs) }})</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($recs as $rec)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs bg-white border border-slate-200 text-slate-700 shadow-2xs">
                                                @if ($rec->image_path)
                                                    <img src="{{ $rec->image_path }}" class="w-3.5 h-3.5 rounded object-cover" alt="" onerror="this.style.display='none'" />
                                                @endif
                                                <span class="font-medium">{{ $rec->title }}</span>
                                                @if ($rec->subtitle)
                                                    <span class="text-slate-400 text-[10px]">({{ $rec->subtitle }})</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-ui.card>

    {{-- FORM ACTIONS BOTTOM BAR --}}
    <div class="flex items-center justify-end gap-3 pt-2">
        <x-ui.button
            href="{{ route('admin.courses.index') }}"
            variant="secondary"
            size="md"
        >
            Cancel
        </x-ui.button>

        <x-ui.button
            type="submit"
            variant="primary"
            size="md"
        >
            {{ $isEdit ? 'Save Changes' : 'Create Course' }}
        </x-ui.button>
    </div>
</div>
