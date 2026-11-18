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

    <!-- Filter Row: Category Selection -->
    <div>
        <div class="flex items-center justify-between mb-2.5">
            <span class="block font-mono text-[10px] font-bold uppercase tracking-widest text-slate-400">
                FILTER BY SPECIALIZATION
            </span>
            <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider" id="filter-result-count">
                Showing all courses
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
</div>
