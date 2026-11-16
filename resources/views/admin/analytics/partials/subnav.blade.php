@php
    $links = [
        [
            'label' => 'Platform Overview',
            'route' => 'admin.analytics.index',
            'url' => route('admin.analytics.index'),
            'active' => request()->routeIs('admin.analytics.index'),
            'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>',
        ],
        [
            'label' => 'Revenue & Financials',
            'route' => 'admin.analytics.revenue',
            'url' => route('admin.analytics.revenue'),
            'active' => request()->routeIs('admin.analytics.revenue'),
            'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
        [
            'label' => 'Enrollments',
            'route' => 'admin.analytics.enrollments',
            'url' => route('admin.analytics.enrollments'),
            'active' => request()->routeIs('admin.analytics.enrollments'),
            'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ],
        [
            'label' => 'Course Performance',
            'route' => 'admin.analytics.courses',
            'url' => route('admin.analytics.courses'),
            'active' => request()->routeIs('admin.analytics.courses'),
            'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>',
        ],
        [
            'label' => 'Student Engagement',
            'route' => 'admin.analytics.students',
            'url' => route('admin.analytics.students'),
            'active' => request()->routeIs('admin.analytics.students'),
            'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
        ],
        [
            'label' => 'Instructor Reports',
            'route' => 'admin.analytics.instructors',
            'url' => route('admin.analytics.instructors'),
            'active' => request()->routeIs('admin.analytics.instructors'),
            'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>',
        ],
    ];
@endphp

<div class="border-b border-slate-200 bg-white mb-6 -mt-6 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 shadow-xs">
    <div class="flex items-center overflow-x-auto no-scrollbar gap-1 sm:gap-2 py-2.5">
        @foreach ($links as $item)
            <a
                href="{{ $item['url'] }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ $item['active'] ? 'bg-[#0A0A0A] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}"
            >
                <span class="{{ $item['active'] ? 'text-[#FB6221]' : 'text-slate-400' }}">
                    {!! $item['icon'] !!}
                </span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>

