@props([
    'activeCategory' => 'all',
    'activeStatus' => 'all',
])

<div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-4 mb-10" id="course-filter-container">
    
    <!-- Top Filter Row: Category Selection -->
    <div>
        <span class="block font-mono text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2.5">
            FILTER BY SPECIALIZATION
        </span>
        <div class="flex flex-wrap items-center gap-1.5" id="category-filter-group">
            <button type="button"
                    data-filter-category="all"
                    class="filter-pill-cat px-3.5 py-1.5 rounded-full font-mono text-xs font-bold uppercase tracking-wider transition-all {{ $activeCategory === 'all' ? 'bg-[#0A0A0A] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                All Tracks
            </button>
            <button type="button"
                    data-filter-category="functional"
                    class="filter-pill-cat px-3.5 py-1.5 rounded-full font-mono text-xs font-bold uppercase tracking-wider transition-all {{ $activeCategory === 'functional' ? 'bg-[#0A0A0A] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                SAP Functional
            </button>
            <button type="button"
                    data-filter-category="technical"
                    class="filter-pill-cat px-3.5 py-1.5 rounded-full font-mono text-xs font-bold uppercase tracking-wider transition-all {{ $activeCategory === 'technical' ? 'bg-[#0A0A0A] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                SAP Technical
            </button>
            <button type="button"
                    data-filter-category="cloud_specialized"
                    class="filter-pill-cat px-3.5 py-1.5 rounded-full font-mono text-xs font-bold uppercase tracking-wider transition-all {{ $activeCategory === 'cloud_specialized' ? 'bg-[#0A0A0A] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                SAP Cloud &amp; Specialized
            </button>
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
                    Available Now (1)
                </button>
                <button type="button"
                        data-filter-status="upcoming"
                        class="filter-pill-stat px-3 py-1 rounded-lg font-mono text-xs font-semibold uppercase tracking-wider transition-all {{ $activeStatus === 'upcoming' ? 'bg-orange-50 text-[#FB6221] border border-orange-200 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                    Upcoming Batches (5)
                </button>
                <button type="button"
                        data-filter-status="planned"
                        class="filter-pill-stat px-3 py-1 rounded-lg font-mono text-xs font-semibold uppercase tracking-wider transition-all {{ $activeStatus === 'planned' ? 'bg-orange-50 text-[#FB6221] border border-orange-200 font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                    Roadmap (5)
                </button>
            </div>
        </div>

        <div class="text-right">
            <span class="font-mono text-[11px] text-slate-500" id="filter-result-count">
                Showing all 11 modules
            </span>
        </div>
    </div>

</div>

