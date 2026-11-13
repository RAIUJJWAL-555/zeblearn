@php
    $isEdit = isset($course->id);
    $selectedCategory = old('category', $course->category ?? '');
    $selectedStatus = old('status', $course->status ?? 'available');
    $highlightsText = old('highlights', is_array($course->highlights) ? implode("\n", $course->highlights) : ($course->highlights ?? ''));
    $isPublished = old('is_published', $course->published_at ? true : false);
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
