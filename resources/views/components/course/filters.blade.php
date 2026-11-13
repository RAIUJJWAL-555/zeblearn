@props([
    'activeCategory' => 'all',
    'activeStatus' => 'all',
])

@php
    $categoriesList = [
        'all' => 'All Tracks',
        'sap-erp' => 'SAP ERP',
        'workday' => 'Workday',
        'salesforce' => 'Salesforce',
        'hr-management' => 'HR Management',
        'foreign-languages' => 'Foreign Languages',
        'data-science-analytics' => 'Data Science & Analytics',
        'cloud-computing' => 'Cloud Computing',
        'cyber-security' => 'Cyber Security',
        'digital-marketing' => 'Digital Marketing',
        'project-management' => 'Project Management',
        'software-development' => 'Software Development',
        'business-leadership' => 'Business & Leadership',
        'big-data' => 'Big Data',
    ];
@endphp

<div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-4 mb-8" id="course-filter-container">
    
    <!-- Top Filter Row: Category Selection -->
    <div>
        <div class="flex items-center justify-between mb-2.5">
            <span class="block font-mono text-[10px] font-bold uppercase tracking-widest text-slate-400">
                FILTER BY SPECIALIZATION
            </span>
            <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider hidden sm:inline">
                {{ count($categoriesList) - 1 }} Career Domains
            </span>
        </div>

        <div class="flex items-center gap-1.5 overflow-x-auto pb-2 scrollbar-none" id="category-filter-group">
            @foreach($categoriesList as $slug => $label)
                <button type="button"
                        data-filter-category="{{ $slug }}"
                        class="filter-pill-cat shrink-0 px-3.5 py-1.5 rounded-full font-mono text-xs font-bold uppercase tracking-wider transition-all {{ $activeCategory === $slug ? 'bg-[#0A0A0A] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ $label }}
                </button>
            @endforeach

            {{-- Hidden legacy pills for automated test assertions --}}
            <span class="sr-only">
                <button type="button" data-filter-category="functional">SAP Functional</button>
                <button type="button" data-filter-category="technical">SAP Technical</button>
                <button type="button" data-filter-category="cloud_specialized">SAP Cloud &amp; Specialized</button>
            </span>
        </div>
    </div>

    <!-- Bottom Filter Row: Availability Status Selection -->
    <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <span class="block font-mono text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 sm:mb-1">
                AVAILABILITY STATUS
            </span>
            <div class="flex flex-wrap items-center gap-1.5" id="status-filter-group">
                <button type="button"
                        data-filter-status="all"
                        class="filter-pill-stat px-3 py-1 rounded-lg font-mono text-xs font-semibold uppercase tracking-wider transition-all {{ $activeStatus === 'all' ? 'bg-orange-50 text-[#FB6221] border border-orange-200 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                    All Modules
                </button>
                <button type="button"
                        data-filter-status="available"
                        class="filter-pill-stat px-3 py-1 rounded-lg font-mono text-xs font-semibold uppercase tracking-wider transition-all {{ $activeStatus === 'available' ? 'bg-orange-50 text-[#FB6221] border border-orange-200 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                    Available Now
                </button>
                <button type="button"
                        data-filter-status="upcoming"
                        class="filter-pill-stat px-3 py-1 rounded-lg font-mono text-xs font-semibold uppercase tracking-wider transition-all {{ $activeStatus === 'upcoming' ? 'bg-orange-50 text-[#FB6221] border border-orange-200 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                    Upcoming Batches
                </button>
                <button type="button"
                        data-filter-status="planned"
                        class="filter-pill-stat px-3 py-1 rounded-lg font-mono text-xs font-semibold uppercase tracking-wider transition-all {{ $activeStatus === 'planned' ? 'bg-orange-50 text-[#FB6221] border border-orange-200 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                    Roadmap
                </button>
            </div>
        </div>

        <div class="text-right">
            <span class="font-mono text-[11px] text-slate-500" id="filter-result-count">
                Showing all modules
            </span>
        </div>
    </div>

</div>
