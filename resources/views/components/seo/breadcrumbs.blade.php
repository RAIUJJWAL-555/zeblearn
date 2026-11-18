@props([
    'items' => []
])

@if(!empty($items))
<nav aria-label="Breadcrumbs" class="py-3">
    <ol class="flex flex-wrap items-center gap-1.5 text-xs font-mono text-slate-500" itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach($items as $index => $item)
            <li class="flex items-center gap-1.5" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                @if(!$loop->last && !empty($item['url']))
                    <a href="{{ $item['url'] }}" itemprop="item" class="hover:text-[#FB6221] transition-colors">
                        <span itemprop="name">{{ $item['name'] }}</span>
                    </a>
                    <span class="text-slate-300" aria-hidden="true">/</span>
                @else
                    <span itemprop="name" class="text-slate-900 font-semibold truncate max-w-xs sm:max-w-md">
                        {{ $item['name'] }}
                    </span>
                @endif
                <meta itemprop="position" content="{{ $index + 1 }}" />
            </li>
        @endforeach
    </ol>
</nav>
@endif
