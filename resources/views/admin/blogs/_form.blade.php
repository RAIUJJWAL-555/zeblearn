<form
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    x-data="{
        title: @js(old('title', $blog->title ?? '')),
        slug: @js(old('slug', $blog->slug ?? '')),
        seoTitle: @js(old('seo_title', $blog->seo_title ?? '')),
        metaDesc: @js(old('meta_description', $blog->meta_description ?? '')),
        status: @js(old('status', $blog->status ?? 'draft')),
        tab: 'write',
        body: @js(old('content', $blog->content ?? '')),
        isDirty: false,
        isSubmitting: false,

        init() {
            this.$nextTick(() => {
                if (this.$refs.previewArea) {
                    this.$refs.previewArea.innerHTML = (this.body && this.body.trim())
                        ? this.body
                        : '<p>Start writing article content...</p>';
                }
            });

            window.addEventListener('beforeunload', (e) => {
                if (this.isDirty && !this.isSubmitting) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        },

        autoGenerateSlug() {
            if (!this.slug || this.slug.trim() === '') {
                this.slug = this.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            }
            if (!this.seoTitle || this.seoTitle.trim() === '') {
                this.seoTitle = this.title + ' | ZebLearn';
            }
        },

        switchToPreview() {
            this.tab = 'preview';
            this.$nextTick(() => {
                if (this.$refs.previewArea && this.$refs.previewArea.innerHTML !== this.body) {
                    this.$refs.previewArea.innerHTML = (this.body && this.body.trim())
                        ? this.body
                        : '<p>Start writing article content...</p>';
                }
            });
        },

        switchToWrite() {
            this.tab = 'write';
            this.$nextTick(() => {
                if (this.$refs.contentArea && this.$refs.contentArea.value !== this.body) {
                    this.$refs.contentArea.value = this.body;
                }
            });
        },

        onPreviewInput() {
            if (!this.$refs.previewArea) return;
            this.body = this.$refs.previewArea.innerHTML;
            this.isDirty = true;
            if (this.$refs.contentArea) {
                this.$refs.contentArea.value = this.body;
            }
        },

        onWriteInput() {
            if (!this.$refs.contentArea) return;
            this.body = this.$refs.contentArea.value;
            this.isDirty = true;
        },

        handlePreviewClick(e) {
            const link = e.target.closest('a, button');
            if (link) {
                e.preventDefault();
            }
        },

        insertTag(openTag, closeTag = '', defaultText = '') {
            const textarea = this.$refs.contentArea;
            if (!textarea) return;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selected = textarea.value.substring(start, end) || defaultText;
            const replacement = openTag + selected + closeTag;
            textarea.setRangeText(replacement, start, end, 'select');
            this.body = textarea.value;
            this.isDirty = true;
            textarea.focus();
        },

        insertTable() {
            const tableHtml = '\n<table class=\'w-full border-collapse border border-slate-300 my-4 text-sm\'>\n  <thead>\n    <tr class=\'bg-slate-100\'>\n      <th class=\'border border-slate-300 p-2 text-left\'>Parameter</th>\n      <th class=\'border border-slate-300 p-2 text-left\'>Description</th>\n    </tr>\n  </thead>\n  <tbody>\n    <tr>\n      <td class=\'border border-slate-300 p-2\'>Value 1</td>\n      <td class=\'border border-slate-300 p-2\'>Description 1</td>\n    </tr>\n  </tbody>\n</table>\n';
            this.insertTag(tableHtml, '', '');
        },

        prepareSubmit() {
            this.isSubmitting = true;
            if (this.tab === 'preview' && this.$refs.previewArea) {
                this.body = this.$refs.previewArea.innerHTML;
                if (this.$refs.contentArea) {
                    this.$refs.contentArea.value = this.body;
                }
            }
        }
    }"
    @submit="prepareSubmit()"
    @input="isDirty = true"
    @change="isDirty = true"
    class="space-y-8"
>
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- LEFT COLUMN: MAIN CONTENT (8 cols) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- 1. BASIC INFORMATION --}}
            <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/90 shadow-xs space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="font-heading text-base font-bold text-slate-900">Article Content</h2>
                    <p class="text-xs text-slate-500">Title, slug, excerpt, and full rich-text body</p>
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                        Blog Title <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        x-model="title"
                        @blur="autoGenerateSlug()"
                        required
                        placeholder="e.g. Top 50 SAP EWM Interview Questions and Answers"
                        class="w-full rounded-xl border @error('title') border-rose-400 bg-rose-50/20 @else border-slate-300 bg-white @enderror px-4 py-3 text-base font-heading font-bold text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                    />
                    @error('title')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                        URL Slug <span class="text-slate-400 font-normal">(permanent public path: /{slug})</span>
                    </label>
                    <div class="flex items-center">
                        <span class="px-3.5 py-3 rounded-l-xl bg-slate-100 border border-r-0 border-slate-300 font-mono text-xs text-slate-500 select-none">
                            https://sap.zeblearn.com/
                        </span>
                        <input
                            type="text"
                            name="slug"
                            id="slug"
                            x-model="slug"
                            placeholder="sap-ewm-interview-questions"
                            class="flex-1 rounded-r-xl border border-slate-300 bg-white px-4 py-3 text-xs font-mono text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                        />
                    </div>
                    @error('slug')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Excerpt / Short Summary --}}
                <div>
                    <label for="excerpt" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                        Short Excerpt / Lead Paragraph <span class="text-slate-400 font-normal">(Displayed in previews and cards)</span>
                    </label>
                    <textarea
                        name="excerpt"
                        id="excerpt"
                        rows="3"
                        placeholder="Concise 2-sentence summary outlining what readers will learn..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-sans leading-relaxed"
                    >{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>
                    @error('excerpt')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Full Blog Content with Rich Editor & Live Preview --}}
                <div class="space-y-2 pt-2">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <div class="flex items-center gap-2.5">
                            <label for="content" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono">
                                Full Article Content
                            </label>
                            <template x-if="isDirty">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Unsaved Edits
                                </span>
                            </template>
                            <template x-if="!isDirty">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono text-slate-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                    Saved
                                </span>
                            </template>
                        </div>
                        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
                            <button
                                type="button"
                                @click="switchToWrite()"
                                :class="tab === 'write' ? 'bg-white text-[#FB6221] shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                                class="px-3 py-1 rounded-lg text-xs font-mono transition-all flex items-center gap-1.5"
                            >
                                <span class="material-symbols-outlined text-[15px]">code</span>
                                <span>Editor / Code</span>
                            </button>
                            <button
                                type="button"
                                @click="switchToPreview()"
                                :class="tab === 'preview' ? 'bg-white text-[#FB6221] shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                                class="px-3 py-1 rounded-lg text-xs font-mono transition-all flex items-center gap-1.5"
                            >
                                <span class="material-symbols-outlined text-[15px]">edit_note</span>
                                <span>Live Preview</span>
                            </button>
                        </div>
                    </div>

                    {{-- Rich Formatting Toolbar (Code Mode) --}}
                    <div x-show="tab === 'write'" class="flex flex-wrap items-center gap-1 p-2 bg-slate-50 border border-slate-200 rounded-t-xl text-xs font-mono">
                        <button type="button" @click="insertTag('<h2>', '</h2>', 'Section Heading')" class="px-2.5 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 font-bold shadow-2xs" title="Heading 2">H2</button>
                        <button type="button" @click="insertTag('<h3>', '</h3>', 'Subtopic Heading')" class="px-2.5 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 font-bold shadow-2xs" title="Heading 3">H3</button>
                        <button type="button" @click="insertTag('<h4>', '</h4>', 'Minor Heading')" class="px-2 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 font-bold shadow-2xs" title="Heading 4">H4</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="insertTag('<strong>', '</strong>', 'bold text')" class="px-2.5 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 font-bold shadow-2xs" title="Bold"><strong>B</strong></button>
                        <button type="button" @click="insertTag('<em>', '</em>', 'italic text')" class="px-2.5 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 italic shadow-2xs" title="Italic"><em>I</em></button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="insertTag('<ul>\n  <li>', '</li>\n  <li>Item 2</li>\n</ul>', 'Item 1')" class="px-2 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 shadow-2xs" title="Bullet List">• Bullet List</button>
                        <button type="button" @click="insertTag('<ol>\n  <li>', '</li>\n  <li>Step 2</li>\n</ol>', 'Step 1')" class="px-2 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 shadow-2xs" title="Numbered List">1. Number List</button>
                        <button type="button" @click="insertTag('<blockquote class=\'border-l-4 border-[#FB6221] pl-4 italic my-4 text-slate-600\'>', '</blockquote>', 'Quote text')" class="px-2 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 shadow-2xs" title="Blockquote">“ Quote</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" @click="insertTag('<pre><code class=\'language-sql\'>', '</code></pre>', 'SELECT * FROM KNA1;')" class="px-2 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 shadow-2xs" title="Code Block">&lt;/&gt; Code</button>
                        <button type="button" @click="insertTable()" class="px-2 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 shadow-2xs" title="Insert Table">⊞ Table</button>
                        <button type="button" @click="insertTag('<a href=\'https://example.com\' target=\'_blank\' rel=\'noopener noreferrer\' class=\'text-[#FB6221] underline\'>', '</a>', 'Link Text')" class="px-2 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 shadow-2xs" title="Insert Link">🔗 Link</button>
                        <button type="button" @click="insertTag('<img src=\'https://example.com/image.webp\' alt=\'Descriptive alt text\' class=\'w-full rounded-2xl my-4 object-cover\' />', '', '')" class="px-2 py-1 rounded bg-white hover:bg-slate-200 text-slate-800 shadow-2xs" title="Insert Image">🖼 Image</button>
                    </div>

                    {{-- Textarea Container (Code Mode) --}}
                    <div x-show="tab === 'write'">
                        <textarea
                            x-ref="contentArea"
                            x-model="body"
                            @input="onWriteInput()"
                            name="content"
                            id="content"
                            rows="18"
                            placeholder="Write comprehensive article content with semantic HTML or Markdown tags..."
                            class="w-full rounded-b-xl border border-slate-300 bg-white px-4 py-3 text-xs text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono leading-relaxed"
                        ></textarea>
                    </div>

                    {{-- Live Preview Top Helper Bar --}}
                    <div x-show="tab === 'preview'" class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 bg-emerald-50/80 border border-emerald-200/80 rounded-t-xl text-[11px] font-mono text-emerald-900">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="font-bold">Visual In-Place Editing Active</span>
                            <span class="text-emerald-700 hidden sm:inline">— Click any heading, paragraph, list, or table to edit directly</span>
                        </div>
                        <div class="flex items-center gap-3 text-[10px] text-emerald-800 font-sans">
                            <span class="hidden md:inline font-mono">Enter = new paragraph</span>
                            <span class="hidden md:inline font-mono">Shift+Enter = line break</span>
                        </div>
                    </div>

                    {{-- Live Preview Editable Container --}}
                    <div
                        x-show="tab === 'preview'"
                        x-ref="previewArea"
                        contenteditable="true"
                        spellcheck="true"
                        @input="onPreviewInput()"
                        @click="handlePreviewClick($event)"
                        class="min-h-[420px] max-h-[720px] overflow-y-auto p-6 bg-white border border-slate-300 rounded-b-xl focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 outline-hidden transition-all prose prose-slate max-w-none text-sm font-sans"
                    ></div>

                    <p class="text-[11px] font-sans text-slate-500">
                        Sanitized automatically before saving. Scripts, malicious iframes, and dangerous event handlers are safely removed.
                    </p>
                </div>
            </div>

            {{-- 2. SEO & METADATA SETTINGS --}}
            <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/90 shadow-xs space-y-5">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="font-heading text-base font-bold text-slate-900">SEO &amp; OpenGraph Settings</h2>
                        <p class="text-xs text-slate-500">Custom titles, snippets, robots directives, and structured data</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-mono font-bold uppercase border border-blue-200">
                        Schema Ready
                    </span>
                </div>

                {{-- SEO Title & Focus Keyword --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="seo_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                            SEO Title Tag
                        </label>
                        <input
                            type="text"
                            name="seo_title"
                            id="seo_title"
                            x-model="seoTitle"
                            placeholder="Primary Search Result Title..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-sans"
                        />
                    </div>

                    <div>
                        <label for="focus_keyword" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                            Focus Keyword
                        </label>
                        <input
                            type="text"
                            name="focus_keyword"
                            id="focus_keyword"
                            value="{{ old('focus_keyword', $blog->focus_keyword ?? '') }}"
                            placeholder="e.g. sap ewm interview questions"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-sans"
                        />
                    </div>
                </div>

                {{-- Meta Description --}}
                <div>
                    <label for="meta_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                        SEO Meta Description
                    </label>
                    <textarea
                        name="meta_description"
                        id="meta_description"
                        rows="2"
                        x-model="metaDesc"
                        placeholder="Snippet that appears in Google SERP results (recommended ~155-160 characters)..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-sans leading-relaxed"
                    ></textarea>
                </div>

                {{-- Meta Keywords --}}
                <div>
                    <label for="meta_keywords" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                        Meta Keywords <span class="text-slate-400 font-normal">(Comma-separated)</span>
                    </label>
                    <input
                        type="text"
                        name="meta_keywords"
                        id="meta_keywords"
                        value="{{ old('meta_keywords', $blog->meta_keywords ?? '') }}"
                        placeholder="e.g. sap hana interview questions, in-memory database, sap erp"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-sans"
                    />
                    <p class="text-[11px] text-slate-400 font-mono mt-1">Leave blank to automatically use article tags or focus keyword.</p>
                </div>

                {{-- Canonical URL --}}
                <div>
                    <label for="canonical_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                        Canonical URL <span class="text-slate-400 font-normal">(Defaults to permanent URL)</span>
                    </label>
                    <input
                        type="url"
                        name="canonical_url"
                        id="canonical_url"
                        value="{{ old('canonical_url', $blog->canonical_url ?? '') }}"
                        placeholder="https://sap.zeblearn.com/sap-ewm-interview-questions/"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-mono text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                    />
                </div>

                {{-- OpenGraph Title & Description --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="og_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                            Open Graph Title
                        </label>
                        <input
                            type="text"
                            name="og_title"
                            id="og_title"
                            value="{{ old('og_title', $blog->og_title ?? '') }}"
                            placeholder="Social share title..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-sans"
                        />
                    </div>
                    <div>
                        <label for="schema_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                            Schema Type
                        </label>
                        <select name="schema_type" id="schema_type" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-sans">
                            <option value="BlogPosting" {{ old('schema_type', $blog->schema_type ?? 'BlogPosting') === 'BlogPosting' ? 'selected' : '' }}>BlogPosting (Recommended)</option>
                            <option value="Article" {{ old('schema_type', $blog->schema_type ?? '') === 'Article' ? 'selected' : '' }}>Article</option>
                        </select>
                    </div>
                </div>

                {{-- Robots Index & Follow --}}
                <div class="flex items-center gap-6 pt-2">
                    <label class="flex items-center gap-2 text-xs font-mono text-slate-700 cursor-pointer">
                        <input
                            type="checkbox"
                            name="robots_index"
                            value="1"
                            {{ old('robots_index', $blog->robots_index ?? true) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-[#FB6221] focus:ring-[#FB6221]"
                        />
                        <span>Robots Index (Allow Google search indexing)</span>
                    </label>

                    <label class="flex items-center gap-2 text-xs font-mono text-slate-700 cursor-pointer">
                        <input
                            type="checkbox"
                            name="robots_follow"
                            value="1"
                            {{ old('robots_follow', $blog->robots_follow ?? true) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-[#FB6221] focus:ring-[#FB6221]"
                        />
                        <span>Robots Follow (Allow crawling links)</span>
                    </label>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: PUBLISHING & TAXONOMY (4 cols) --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- 1. PUBLISHING CONTROLS CARD --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h3 class="font-heading text-sm font-bold text-slate-900 uppercase tracking-wider">Publishing Status</h3>
                    @if(!empty($blog->id))
                        <a href="{{ route('admin.blogs.preview', $blog) }}" target="_blank" class="text-xs font-mono font-bold text-[#FB6221] hover:underline">
                            Live Preview
                        </a>
                    @endif
                </div>

                {{-- Status Selector --}}
                <div>
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="status"
                        id="status"
                        x-model="status"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-mono font-bold text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                    >
                        @foreach(\App\Models\Blog::STATUSES as $val => $lbl)
                            <option value="{{ $val }}" {{ old('status', $blog->status ?? 'draft') === $val ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Published Date / Time --}}
                <div>
                    <label for="published_at" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                        Published Date &amp; Time
                    </label>
                    <input
                        type="datetime-local"
                        name="published_at"
                        id="published_at"
                        value="{{ old('published_at', $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-mono text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                    />
                    <p class="text-[10px] text-slate-400 font-mono mt-1">Leave empty to auto-set on publish.</p>
                </div>

                {{-- Primary Action Buttons --}}
                <div class="pt-2 space-y-2">
                    <div class="flex items-center justify-between text-[11px] font-mono px-1 pb-1">
                        <span class="text-slate-500">Document State</span>
                        <template x-if="isDirty">
                            <span class="inline-flex items-center gap-1 font-semibold text-amber-600">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                Unsaved Changes
                            </span>
                        </template>
                        <template x-if="!isDirty">
                            <span class="inline-flex items-center gap-1 text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Up to Date
                            </span>
                        </template>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3 bg-[#FB6221] hover:bg-[#e05214] text-white text-xs font-mono font-bold uppercase tracking-wider rounded-xl transition-all shadow-md hover:shadow-lg cursor-pointer flex items-center justify-center gap-2"
                    >
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>{{ $submitLabel }}</span>
                    </button>

                    <a
                        href="{{ route('admin.blogs.index') }}"
                        class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-mono font-semibold uppercase tracking-wider rounded-xl transition-colors flex items-center justify-center"
                    >
                        Cancel &amp; Back
                    </a>
                </div>
            </div>

            {{-- 2. FEATURED IMAGE --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-heading text-sm font-bold text-slate-900 uppercase tracking-wider">Featured Image</h3>
                    <p class="text-xs text-slate-500">Card poster and social sharing cover</p>
                </div>

                @if(!empty($blog->featured_image))
                    <div class="relative rounded-xl overflow-hidden border border-slate-200 aspect-video bg-slate-100">
                        <img
                            src="{{ Str::startsWith($blog->featured_image, ['http://', 'https://']) ? $blog->featured_image : asset($blog->featured_image) }}"
                            alt="{{ $blog->featured_image_alt ?: $blog->title }}"
                            class="w-full h-full object-cover"
                        />
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                        Upload New Image File
                    </label>
                    <input
                        type="file"
                        name="featured_image_file"
                        accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-mono file:font-bold file:bg-orange-50 file:text-[#FB6221] hover:file:bg-orange-100 cursor-pointer"
                    />
                </div>

                <div>
                    <label for="featured_image" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1">
                        Or External Image URL
                    </label>
                    <input
                        type="text"
                        name="featured_image"
                        id="featured_image"
                        value="{{ old('featured_image', $blog->featured_image ?? '') }}"
                        placeholder="https://.../cover.webp"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-mono text-slate-900 focus:border-[#FB6221] outline-hidden"
                    />
                </div>

                <div>
                    <label for="featured_image_alt" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1">
                        Image Alt Text
                    </label>
                    <input
                        type="text"
                        name="featured_image_alt"
                        id="featured_image_alt"
                        value="{{ old('featured_image_alt', $blog->featured_image_alt ?? '') }}"
                        placeholder="Descriptive caption for accessibility..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#FB6221] outline-hidden"
                    />
                </div>
            </div>

            {{-- 3. CATEGORIES & TAXONOMY --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h3 class="font-heading text-sm font-bold text-slate-900 uppercase tracking-wider">Categories</h3>
                    <a href="{{ route('admin.blogs.categories.index') }}" target="_blank" class="text-[11px] font-mono text-[#FB6221] hover:underline">
                        + New
                    </a>
                </div>

                <div class="max-h-48 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                    @php
                        $selectedCats = old('categories', $blog->categories ? $blog->categories->pluck('id')->toArray() : []);
                    @endphp
                    @forelse($categories as $cat)
                        <label class="flex items-center gap-2.5 text-xs text-slate-700 hover:text-slate-900 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                name="categories[]"
                                value="{{ $cat->id }}"
                                {{ in_array($cat->id, $selectedCats) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-[#FB6221] focus:ring-[#FB6221]"
                            />
                            <span>{{ $cat->name }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-slate-400 font-sans">No categories created yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- 4. AUTHOR --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-heading text-sm font-bold text-slate-900 uppercase tracking-wider">Author Assignment</h3>
                </div>

                <div>
                    <label for="author_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                        System User
                    </label>
                    <select name="author_id" id="author_id" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#FB6221] outline-hidden">
                        <option value="">— None / Custom Name —</option>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}" {{ old('author_id', $blog->author_id ?? '') == $author->id ? 'selected' : '' }}>
                                {{ $author->name }} ({{ $author->role }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="author_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1">
                        Custom Byline / Author Name
                    </label>
                    <input
                        type="text"
                        name="author_name"
                        id="author_name"
                        value="{{ old('author_name', $blog->author_name ?? '') }}"
                        placeholder="e.g. ZebLearn Editorial Team"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#FB6221] outline-hidden"
                    />
                </div>
            </div>

            {{-- 5. AUDIT / MIGRATION METADATA (If Recovered) --}}
            @if(!empty($blog->legacy_source_url))
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-2 text-xs font-mono">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Legacy WordPress Telemetry</span>
                    <div class="text-[11px] text-slate-600 truncate">
                        <strong>Source:</strong> <a href="{{ $blog->legacy_source_url }}" target="_blank" class="text-[#FB6221] underline">{{ $blog->legacy_source_url }}</a>
                    </div>
                    <div class="text-[11px] text-slate-600">
                        <strong>WP Post ID:</strong> {{ $blog->legacy_id ?: 'N/A' }}
                    </div>
                    <div class="text-[11px] text-slate-600">
                        <strong>Recovery Status:</strong> <span class="uppercase font-bold text-emerald-600">{{ $blog->recovery_status }}</span>
                    </div>
                </div>
            @endif

        </div>
    </div>
</form>
