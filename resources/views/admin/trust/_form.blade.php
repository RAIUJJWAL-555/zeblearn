@php
    $isEdit = isset($trustItem->id);
    $selectedType = old('type', $trustItem->type ?? \App\Models\TrustItem::TYPE_PLACEMENT);
    $selectedScope = old('scope_type', $trustItem->scope_type ?? \App\Models\TrustItem::SCOPE_GLOBAL);
    $selectedScopeCategory = old('scope_category', $trustItem->scope_category ?? '');
    $selectedCourseIds = old('course_ids', $isEdit ? $trustItem->courses->pluck('id')->toArray() : []);
    $isVerified = old('is_verified', $trustItem->is_verified ?? true);
    $isPublished = old('is_published', $trustItem->is_published ?? true);
    $meta = old('metadata', $trustItem->metadata ?? []);
    $metaVal = function ($key, $default = '') use ($meta) {
        $val = $meta[$key] ?? $default;
        return is_array($val) ? implode(', ', $val) : (string) $val;
    };
@endphp

<div
    x-data="{
        selectedType: '{{ $selectedType }}',
        selectedScope: '{{ $selectedScope }}',
        courseSearch: '',
        selectedCourseIds: {{ json_encode(array_map('intval', $selectedCourseIds)) }},
        toggleCourse(id) {
            id = parseInt(id);
            if (this.selectedCourseIds.includes(id)) {
                this.selectedCourseIds = this.selectedCourseIds.filter(item => item !== id);
            } else {
                this.selectedCourseIds.push(id);
            }
        },
        selectAllCourses() {
            @php
                $allIds = $allCourses->pluck('id')->toArray();
            @endphp
            this.selectedCourseIds = {{ json_encode($allIds) }};
        },
        deselectAllCourses() {
            this.selectedCourseIds = [];
        }
    }"
    class="space-y-6"
>
    {{-- Error Banner --}}
    @if ($errors->any())
        <x-ui.alert variant="danger">
            <div class="space-y-1">
                <p class="font-bold">Please correct the following errors:</p>
                <ul class="list-disc pl-5 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </x-ui.alert>
    @endif

    {{-- 1. RECORD TYPE & PUBLISHING STATUS --}}
    <x-ui.card title="Discipline & Verification Status" subtitle="Select proof discipline and set live visibility criteria">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="type" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Proof Discipline / Content Type <span class="text-rose-500">*</span>
                </label>
                <select
                    id="type"
                    name="type"
                    x-model="selectedType"
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#FB6221] focus:border-transparent font-medium"
                >
                    @foreach ($types as $key => $label)
                        <option value="{{ $key }}" @selected($selectedType === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-slate-500">The type dictates presentation format and landing page section placement.</p>
            </div>

            <div>
                <label for="display_priority" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Display Priority (Order Weight)
                </label>
                <input
                    type="number"
                    id="display_priority"
                    name="display_priority"
                    value="{{ old('display_priority', $trustItem->display_priority ?? 10) }}"
                    min="0"
                    max="999"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#FB6221] focus:border-transparent"
                />
                <p class="mt-1.5 text-xs text-slate-500">Higher numbers appear earlier in course landing page grids.</p>
            </div>

            <div class="md:col-span-2 pt-2 border-t border-slate-100 flex flex-wrap items-center gap-6">
                <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="is_verified"
                        value="1"
                        @checked($isVerified)
                        class="w-4 h-4 rounded text-[#FB6221] focus:ring-[#FB6221] border-slate-300"
                    />
                    <div>
                        <span class="text-sm font-semibold text-slate-900 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">verified</span>
                            Verified Authentic Proof
                        </span>
                        <p class="text-xs text-slate-500">Only verified records are eligible for public landing page showcase.</p>
                    </div>
                </label>

                <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="is_published"
                        value="1"
                        @checked($isPublished)
                        class="w-4 h-4 rounded text-[#FB6221] focus:ring-[#FB6221] border-slate-300"
                    />
                    <div>
                        <span class="text-sm font-semibold text-slate-900 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-blue-600">visibility</span>
                            Publish to Live Site
                        </span>
                        <p class="text-xs text-slate-500">Must be published to appear on dynamic course landing pages.</p>
                    </div>
                </label>
            </div>
        </div>
    </x-ui.card>

    {{-- 2. DYNAMIC COURSE MAPPING & SCOPE --}}
    <x-ui.card title="Dynamic Course Mapping & Scope" subtitle="Assign this proof globally, by academic track, or to specific courses">
        <div class="space-y-5">
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2.5">
                    Distribution Scope <span class="text-rose-500">*</span>
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <label
                        :class="selectedScope === '{{ \App\Models\TrustItem::SCOPE_GLOBAL }}' ? 'border-[#FB6221] bg-orange-50/40 ring-1 ring-[#FB6221]' : 'border-slate-200 bg-white hover:bg-slate-50'"
                        class="p-4 rounded-xl border flex flex-col justify-between cursor-pointer transition-all"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">Institution Global</span>
                            <input
                                type="radio"
                                name="scope_type"
                                value="{{ \App\Models\TrustItem::SCOPE_GLOBAL }}"
                                x-model="selectedScope"
                                class="text-[#FB6221] focus:ring-[#FB6221]"
                            />
                        </div>
                        <p class="text-xs text-slate-600">Available across all courses (honoring course global toggle).</p>
                    </label>

                    <label
                        :class="selectedScope === '{{ \App\Models\TrustItem::SCOPE_ALL }}' ? 'border-[#FB6221] bg-orange-50/40 ring-1 ring-[#FB6221]' : 'border-slate-200 bg-white hover:bg-slate-50'"
                        class="p-4 rounded-xl border flex flex-col justify-between cursor-pointer transition-all"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">All Courses</span>
                            <input
                                type="radio"
                                name="scope_type"
                                value="{{ \App\Models\TrustItem::SCOPE_ALL }}"
                                x-model="selectedScope"
                                class="text-[#FB6221] focus:ring-[#FB6221]"
                            />
                        </div>
                        <p class="text-xs text-slate-600">Directly associated with every course in the catalog.</p>
                    </label>

                    <label
                        :class="selectedScope === '{{ \App\Models\TrustItem::SCOPE_CATEGORY }}' ? 'border-[#FB6221] bg-orange-50/40 ring-1 ring-[#FB6221]' : 'border-slate-200 bg-white hover:bg-slate-50'"
                        class="p-4 rounded-xl border flex flex-col justify-between cursor-pointer transition-all"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">Course Category</span>
                            <input
                                type="radio"
                                name="scope_type"
                                value="{{ \App\Models\TrustItem::SCOPE_CATEGORY }}"
                                x-model="selectedScope"
                                class="text-[#FB6221] focus:ring-[#FB6221]"
                            />
                        </div>
                        <p class="text-xs text-slate-600">Automatically mapped to all courses matching a specific category.</p>
                    </label>

                    <label
                        :class="selectedScope === '{{ \App\Models\TrustItem::SCOPE_SELECTED_COURSES }}' ? 'border-[#FB6221] bg-orange-50/40 ring-1 ring-[#FB6221]' : 'border-slate-200 bg-white hover:bg-slate-50'"
                        class="p-4 rounded-xl border flex flex-col justify-between cursor-pointer transition-all"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">Selected Courses</span>
                            <input
                                type="radio"
                                name="scope_type"
                                value="{{ \App\Models\TrustItem::SCOPE_SELECTED_COURSES }}"
                                x-model="selectedScope"
                                class="text-[#FB6221] focus:ring-[#FB6221]"
                            />
                        </div>
                        <p class="text-xs text-slate-600">Manually select specific courses from the directory.</p>
                    </label>
                </div>
            </div>

            {{-- Category Selector (Visible when scope is category) --}}
            <div x-show="selectedScope === 'category'" x-cloak class="pt-3 border-t border-slate-100">
                <label for="scope_category" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Target Course Category <span class="text-rose-500">*</span>
                </label>
                <select
                    id="scope_category"
                    name="scope_category"
                    class="w-full sm:w-1/2 px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#FB6221] focus:border-transparent font-medium"
                >
                    <option value="">Select target category...</option>
                    @foreach ($categories as $catKey => $catData)
                        @php
                            $catSlug = is_string($catKey) ? $catKey : (is_array($catData) ? ($catData['slug'] ?? $catKey) : $catData);
                            $catName = is_array($catData) ? ($catData['name'] ?? $catSlug) : $catData;
                        @endphp
                        <option value="{{ $catSlug }}" @selected($selectedScopeCategory === $catSlug)>{{ $catName }}</option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-slate-500">Every course whose category matches will automatically display this record.</p>
            </div>

            {{-- Multi-Course Selector (Visible when scope is selected_courses) --}}
            <div x-show="selectedScope === 'selected_courses'" x-cloak class="pt-3 border-t border-slate-100 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono">
                        Select Linked Courses (<span x-text="selectedCourseIds.length"></span> selected) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="selectAllCourses()"
                            class="text-xs font-mono font-medium text-slate-600 hover:text-slate-900 underline"
                        >
                            Select All
                        </button>
                        <span class="text-slate-300">•</span>
                        <button
                            type="button"
                            @click="deselectAllCourses()"
                            class="text-xs font-mono font-medium text-slate-600 hover:text-slate-900 underline"
                        >
                            Deselect All
                        </button>
                    </div>
                </div>

                {{-- Quick filter input --}}
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input
                        type="text"
                        x-model="courseSearch"
                        placeholder="Filter courses by name or category..."
                        class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50/70 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#FB6221] focus:bg-white"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 max-h-64 overflow-y-auto p-1.5 border border-slate-200 rounded-xl bg-slate-50/50">
                    @foreach ($allCourses as $c)
                        <div
                            x-show="'{{ strtolower(addslashes($c->name . ' ' . $c->category)) }}'.includes(courseSearch.toLowerCase())"
                            @click="toggleCourse({{ $c->id }})"
                            :class="selectedCourseIds.includes({{ $c->id }}) ? 'bg-orange-50 border-orange-300 ring-1 ring-[#FB6221]' : 'bg-white border-slate-200 hover:border-slate-300'"
                            class="p-2.5 rounded-lg border flex items-center justify-between cursor-pointer transition-all select-none"
                        >
                            <div class="min-w-0 pr-2">
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ $c->name }}</p>
                                <p class="text-[10px] font-mono text-slate-500 uppercase">{{ $c->category }}</p>
                            </div>
                            <input
                                type="checkbox"
                                name="course_ids[]"
                                value="{{ $c->id }}"
                                :checked="selectedCourseIds.includes({{ $c->id }})"
                                class="w-4 h-4 rounded text-[#FB6221] focus:ring-[#FB6221] border-slate-300 shrink-0 pointer-events-none"
                            />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- 3. CORE PRESENTATION FIELDS --}}
    <x-ui.card title="Core Presentation Details" subtitle="Primary textual and graphic elements displayed on the landing page">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Primary Title / Entity Name <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $trustItem->title ?? '') }}"
                    required
                    placeholder="e.g. Rahul Verma / Deloitte India / AWS Certified Solutions Architect"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#FB6221] focus:border-transparent font-medium"
                />
            </div>

            <div>
                <label for="subtitle" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Subtitle / Designation / Secondary Line
                </label>
                <input
                    type="text"
                    id="subtitle"
                    name="subtitle"
                    value="{{ old('subtitle', $trustItem->subtitle ?? '') }}"
                    placeholder="e.g. Associate SAP Consultant / Global Hiring Partner / Cohort #12"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#FB6221] focus:border-transparent"
                />
            </div>

            <div>
                <label for="content_category" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Content Category / Specialization Tag
                </label>
                <input
                    type="text"
                    id="content_category"
                    name="content_category"
                    value="{{ old('content_category', $trustItem->content_category ?? '') }}"
                    placeholder="e.g. Enterprise ERP / Cloud Computing / Data Analytics"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#FB6221] focus:border-transparent"
                />
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Full Description / Quote / Citation / Case Study Summary
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Enter detailed testimonial quote, capstone project description, trainer profile, or award citation..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#FB6221] focus:border-transparent"
                >{{ old('description', $trustItem->description ?? '') }}</textarea>
            </div>

            <div>
                <label for="image_path" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    Image / Logo / Badge / Photo URL or Path
                </label>
                <input
                    type="text"
                    id="image_path"
                    name="image_path"
                    value="{{ old('image_path', $trustItem->image_path ?? '') }}"
                    placeholder="e.g. /images/partners/deloitte.svg or https://..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#FB6221] focus:border-transparent"
                />
                <p class="mt-1.5 text-xs text-slate-500">Local asset path or external HTTPS URL to photo or vector logo.</p>
            </div>

            <div>
                <label for="media_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                    External Verification / Media / Video URL
                </label>
                <input
                    type="url"
                    id="media_url"
                    name="media_url"
                    value="{{ old('media_url', $trustItem->media_url ?? '') }}"
                    placeholder="https://..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#FB6221] focus:border-transparent"
                />
                <p class="mt-1.5 text-xs text-slate-500">YouTube video, Credly badge link, LinkedIn profile, or certificate validation URL.</p>
            </div>
        </div>
    </x-ui.card>

    {{-- 4. DISCIPLINE-SPECIFIC STRUCTURED METADATA --}}
    <x-ui.card title="Structured Metadata & Proof Fields" subtitle="Specific metrics and metadata tailored to the selected discipline">
        {{-- PLACEMENT FIELDS --}}
        <div x-show="selectedType === 'placement'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Student Full Name</label>
                <input
                    type="text"
                    name="metadata[student_name]"
                    value="{{ $metaVal('student_name') }}"
                    placeholder="e.g. Priya Sharma"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Hiring Company</label>
                <input
                    type="text"
                    name="metadata[company]"
                    value="{{ $metaVal('company') }}"
                    placeholder="e.g. PwC India"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Designation / Role</label>
                <input
                    type="text"
                    name="metadata[role]"
                    value="{{ $metaVal('role') }}"
                    placeholder="e.g. S/4HANA Finance Consultant"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">CTC / Package</label>
                <input
                    type="text"
                    name="metadata[package]"
                    value="{{ $metaVal('package') }}"
                    placeholder="e.g. 14.5 LPA"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Placement Year / Cohort</label>
                <input
                    type="text"
                    name="metadata[hired_year]"
                    value="{{ $metaVal('hired_year') }}"
                    placeholder="e.g. 2026 Batch"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">LinkedIn Profile</label>
                <input
                    type="url"
                    name="metadata[linkedin_url]"
                    value="{{ $metaVal('linkedin_url') }}"
                    placeholder="https://linkedin.com/in/..."
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
        </div>

        {{-- TESTIMONIAL FIELDS --}}
        <div x-show="selectedType === 'testimonial'" x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Reviewer Name</label>
                <input
                    type="text"
                    name="metadata[author_name]"
                    value="{{ $metaVal('author_name') }}"
                    placeholder="e.g. Ankit Gupta"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Reviewer Role & Company</label>
                <input
                    type="text"
                    name="metadata[author_role]"
                    value="{{ $metaVal('author_role') }}"
                    placeholder="e.g. Senior SAP MM Consultant @ KPMG"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Star Rating (1 - 5)</label>
                <input
                    type="number"
                    name="metadata[rating]"
                    value="{{ $metaVal('rating', 5) }}"
                    min="1"
                    max="5"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
        </div>

        {{-- CERTIFICATION RECORD FIELDS --}}
        <div x-show="selectedType === 'certification'" x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Issuing Global Body</label>
                <input
                    type="text"
                    name="metadata[issuing_body]"
                    value="{{ $metaVal('issuing_body') }}"
                    placeholder="e.g. SAP SE / AWS / Microsoft"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Official Exam Code</label>
                <input
                    type="text"
                    name="metadata[exam_code]"
                    value="{{ $metaVal('exam_code') }}"
                    placeholder="e.g. C_TS4FI_2023"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Passing Rate / First-Attempt Rate</label>
                <input
                    type="text"
                    name="metadata[pass_rate]"
                    value="{{ $metaVal('pass_rate', '98%') }}"
                    placeholder="e.g. 98.4%"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
        </div>

        {{-- TRAINER FIELDS --}}
        <div x-show="selectedType === 'trainer'" x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Current Industry Role & Employer</label>
                <input
                    type="text"
                    name="metadata[designation]"
                    value="{{ $metaVal('designation') }}"
                    placeholder="e.g. Principal Architect @ IBM"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Industry Experience</label>
                <input
                    type="text"
                    name="metadata[experience_years]"
                    value="{{ $metaVal('experience_years') }}"
                    placeholder="e.g. 15+ Years"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Total Learners Mentored</label>
                <input
                    type="text"
                    name="metadata[students_trained]"
                    value="{{ $metaVal('students_trained') }}"
                    placeholder="e.g. 5,000+ Professionals"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
        </div>

        {{-- PARTNER FIELDS --}}
        <div x-show="selectedType === 'partner'" x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Relationship / Tier</label>
                <input
                    type="text"
                    name="metadata[partner_type]"
                    value="{{ $metaVal('partner_type', 'Official Hiring Partner') }}"
                    placeholder="e.g. Strategic Hiring Partner"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Corporate Website</label>
                <input
                    type="url"
                    name="metadata[website_url]"
                    value="{{ $metaVal('website_url') }}"
                    placeholder="https://..."
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Tier / Category</label>
                <input
                    type="text"
                    name="metadata[tier]"
                    value="{{ $metaVal('tier', 'Tier-1 MNC') }}"
                    placeholder="e.g. Tier-1 MNC / Big 4"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
        </div>

        {{-- CAPSTONE PROJECT FIELDS --}}
        <div x-show="selectedType === 'project'" x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Project Domain / Industry</label>
                <input
                    type="text"
                    name="metadata[domain]"
                    value="{{ $metaVal('domain') }}"
                    placeholder="e.g. Manufacturing & Supply Chain"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Tech Stack / Tools Used</label>
                <input
                    type="text"
                    name="metadata[tech_stack]"
                    value="{{ $metaVal('tech_stack') }}"
                    placeholder="e.g. SAP S/4HANA, Fiori, ABAP Cloud"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Duration</label>
                <input
                    type="text"
                    name="metadata[duration]"
                    value="{{ $metaVal('duration', '4 Weeks') }}"
                    placeholder="e.g. 4 Weeks Real-time Simulation"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
        </div>

        {{-- VIDEO STORY FIELDS --}}
        <div x-show="selectedType === 'video_story'" x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Student / Speaker Name</label>
                <input
                    type="text"
                    name="metadata[student_name]"
                    value="{{ $metaVal('student_name') }}"
                    placeholder="e.g. Sneha Roy"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Video Duration</label>
                <input
                    type="text"
                    name="metadata[duration]"
                    value="{{ $metaVal('duration', '2:45') }}"
                    placeholder="e.g. 3:15 min"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Embed Platform</label>
                <input
                    type="text"
                    name="metadata[platform]"
                    value="{{ $metaVal('platform', 'YouTube') }}"
                    placeholder="YouTube / Vimeo"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
        </div>

        {{-- AWARD RECOGNITION FIELDS --}}
        <div x-show="selectedType === 'award'" x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Conferring Organization</label>
                <input
                    type="text"
                    name="metadata[issuing_organization]"
                    value="{{ $metaVal('issuing_organization') }}"
                    placeholder="e.g. National EdTech Summit"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Year Conferred</label>
                <input
                    type="text"
                    name="metadata[year]"
                    value="{{ $metaVal('year', date('Y')) }}"
                    placeholder="e.g. 2026"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-xs font-mono uppercase text-slate-600 mb-1.5">Category of Recognition</label>
                <input
                    type="text"
                    name="metadata[recognition_title]"
                    value="{{ $metaVal('recognition_title') }}"
                    placeholder="e.g. Best Enterprise Tech Upskilling Platform"
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-[#FB6221]"
                />
            </div>
        </div>
    </x-ui.card>

    {{-- ACTION BUTTONS --}}
    <div class="flex items-center justify-between pt-4 border-t border-slate-200">
        <a
            href="{{ route('admin.trust.index', ['type' => $selectedType]) }}"
            class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-mono font-bold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition-colors"
        >
            Cancel &amp; Return
        </a>

        <button
            type="submit"
            class="px-6 py-2.5 rounded-xl bg-[#FB6221] hover:bg-[#e05216] text-white text-xs font-mono font-bold uppercase tracking-wider shadow-sm transition-all flex items-center gap-2"
        >
            <span class="material-symbols-outlined text-[18px]">save</span>
            <span>{{ $isEdit ? 'Update Trust Record' : 'Save To Trust Library' }}</span>
        </button>
    </div>
</div>
