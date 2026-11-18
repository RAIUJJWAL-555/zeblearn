<div
    x-data="{
        open: false,
        query: '',
        activeTab: 'all',
        courses: [],
        blogs: [],
        loading: false,
        hasSearched: false,
        fetchResults() {
            this.loading = true;
            fetch('{{ route('courses.search.api') }}?q=' + encodeURIComponent(this.query))
                .then(res => res.json())
                .then(data => {
                    this.courses = data.courses || [];
                    this.blogs = data.blogs || [];
                    this.loading = false;
                    this.hasSearched = true;
                })
                .catch(() => {
                    this.loading = false;
                });
        },
        init() {
            this.$watch('query', () => {
                this.fetchResults();
            });
        }
    }"
    @open-course-search.window="open = true; $nextTick(() => { $refs.searchInput?.focus(); if (courses.length === 0 && blogs.length === 0) fetchResults(); })"
    @keydown.window.prevent.cmd.k="open = true; $nextTick(() => { $refs.searchInput?.focus(); if (courses.length === 0 && blogs.length === 0) fetchResults(); })"
    @keydown.window.prevent.ctrl.k="open = true; $nextTick(() => { $refs.searchInput?.focus(); if (courses.length === 0 && blogs.length === 0) fetchResults(); })"
    @keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-start justify-center p-4 sm:p-6 sm:pt-20"
>
    <!-- Modal Card -->
    <div
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[85vh]"
    >
        <!-- Search Header -->
        <div class="p-4 border-b border-slate-100 flex items-center gap-3">
            <span class="material-symbols-outlined text-[24px] text-[#FB6221] shrink-0">search</span>

            <form action="{{ route('courses.index') }}" method="GET" class="flex-1 flex items-center" @submit="open = false">
                <input
                    type="text"
                    name="q"
                    x-ref="searchInput"
                    x-model="query"
                    placeholder="Search SAP courses, interview questions, blogs & guides..."
                    class="w-full text-sm sm:text-base text-slate-900 placeholder:text-slate-400 bg-transparent outline-hidden font-sans"
                    autocomplete="off"
                >
            </form>

            <div class="flex items-center gap-2">
                <div x-show="loading" class="w-4 h-4 border-2 border-[#FB6221] border-t-transparent rounded-full animate-spin"></div>
                <button
                    type="button"
                    @click="open = false"
                    class="px-2 py-1 text-[11px] font-mono text-slate-400 hover:text-slate-700 bg-slate-100 rounded-lg transition-colors cursor-pointer"
                >
                    ESC
                </button>
            </div>
        </div>

        <!-- Filter Sub-nav (All / Courses / Blogs) -->
        <div class="px-4 py-2 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs">
            <div class="flex items-center gap-1">
                <button
                    type="button"
                    @click="activeTab = 'all'"
                    class="px-2.5 py-1 rounded-md text-xs font-semibold transition-colors cursor-pointer"
                    :class="activeTab === 'all' ? 'bg-white text-[#FB6221] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                >
                    All Results (<span x-text="courses.length + blogs.length"></span>)
                </button>
                <button
                    type="button"
                    @click="activeTab = 'courses'"
                    class="px-2.5 py-1 rounded-md text-xs font-semibold transition-colors cursor-pointer"
                    :class="activeTab === 'courses' ? 'bg-white text-[#FB6221] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                >
                    Courses (<span x-text="courses.length"></span>)
                </button>
                <button
                    type="button"
                    @click="activeTab = 'blogs'"
                    class="px-2.5 py-1 rounded-md text-xs font-semibold transition-colors cursor-pointer"
                    :class="activeTab === 'blogs' ? 'bg-white text-[#FB6221] shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                >
                    Blogs & Guides (<span x-text="blogs.length"></span>)
                </button>
            </div>

            <span x-show="query.trim() !== ''" class="hidden sm:inline">
                <a :href="'{{ route('courses.index') }}?q=' + encodeURIComponent(query)" class="text-[11px] font-mono text-[#FB6221] hover:underline font-semibold">
                    View in Catalog →
                </a>
            </span>
        </div>

        <!-- Search Results List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <!-- Empty State -->
            <div x-show="!loading && courses.length === 0 && blogs.length === 0 && hasSearched" class="py-12 text-center space-y-2">
                <span class="material-symbols-outlined text-[36px] text-slate-300">search_off</span>
                <p class="text-sm font-semibold text-slate-700">No matching courses or blogs found</p>
                <p class="text-xs text-slate-500">Try searching for FICO, MM, ABAP, S/4HANA, or EWM.</p>
            </div>

            <!-- COURSES SECTION -->
            <div x-show="(activeTab === 'all' || activeTab === 'courses') && courses.length > 0" class="space-y-2">
                <div class="flex items-center justify-between text-[11px] font-mono uppercase tracking-wider text-slate-400 pb-1 border-b border-slate-100">
                    <span class="flex items-center gap-1.5 font-bold text-slate-600">
                        <span class="material-symbols-outlined text-[14px] text-[#FB6221]">school</span>
                        <span>Training Courses (<span x-text="courses.length"></span>)</span>
                    </span>
                    <a href="{{ route('courses.index') }}" class="text-[#FB6221] hover:underline normal-case font-sans text-xs">
                        All Courses →
                    </a>
                </div>

                <div class="space-y-1.5">
                    <template x-for="item in courses" :key="'c-' + item.id">
                        <a
                            :href="item.url"
                            class="block group rounded-xl p-3 hover:bg-orange-50/50 transition-colors border border-transparent hover:border-orange-100"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#FB6221] transition-colors" x-text="item.name"></h4>
                                        <template x-if="item.is_recently_added">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800">
                                                NEW
                                            </span>
                                        </template>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 font-mono">
                                        <span class="text-blue-600 font-semibold" x-text="item.category_label"></span>
                                        <span>•</span>
                                        <span x-text="item.duration"></span>
                                        <span>•</span>
                                        <span x-text="item.delivery_mode"></span>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="text-xs font-bold text-slate-900 font-mono" x-text="item.display_price"></span>
                                    <span class="block text-[11px] text-[#FB6221] font-semibold group-hover:translate-x-0.5 transition-transform">
                                        View Course →
                                    </span>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>

            <!-- BLOGS & ARTICLES SECTION -->
            <div x-show="(activeTab === 'all' || activeTab === 'blogs') && blogs.length > 0" class="space-y-2 pt-2">
                <div class="flex items-center justify-between text-[11px] font-mono uppercase tracking-wider text-slate-400 pb-1 border-b border-slate-100">
                    <span class="flex items-center gap-1.5 font-bold text-slate-600">
                        <span class="material-symbols-outlined text-[14px] text-blue-500">article</span>
                        <span>Articles, Guides & Interview Prep (<span x-text="blogs.length"></span>)</span>
                    </span>
                    <a href="{{ route('blogs') }}" class="text-[#FB6221] hover:underline normal-case font-sans text-xs">
                        All Blogs →
                    </a>
                </div>

                <div class="space-y-1.5">
                    <template x-for="item in blogs" :key="'b-' + item.id">
                        <a
                            :href="item.url"
                            class="block group rounded-xl p-3 hover:bg-blue-50/50 transition-colors border border-transparent hover:border-blue-100"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="space-y-1">
                                    <h4 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-1" x-text="item.title"></h4>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 font-mono">
                                        <span class="text-[#FB6221] font-semibold" x-text="item.category_label"></span>
                                        <span>•</span>
                                        <span x-text="item.reading_time"></span>
                                        <template x-if="item.published_date">
                                            <span class="hidden sm:inline">
                                                <span>•</span>
                                                <span x-text="item.published_date"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">
                                        Article
                                    </span>
                                    <span class="block text-[11px] text-blue-600 font-semibold group-hover:translate-x-0.5 transition-transform mt-0.5">
                                        Read Guide →
                                    </span>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        <!-- Footer Bar -->
        <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1">
                    <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded text-[10px] font-mono shadow-2xs">↵</kbd>
                    <span>Search</span>
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded text-[10px] font-mono shadow-2xs">esc</kbd>
                    <span>Close</span>
                </span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('courses.index') }}" class="text-xs font-semibold text-slate-600 hover:text-[#FB6221] transition-colors">
                    Courses Catalog
                </a>
                <span>•</span>
                <a href="{{ route('blogs') }}" class="text-xs font-semibold text-slate-600 hover:text-[#FB6221] transition-colors">
                    Blog Library
                </a>
            </div>
        </div>
    </div>
</div>
