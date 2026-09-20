@extends('pages.growth-os.layout')

@section('growth_content')
<div class="box p-6 bg-white rounded-xl shadow-sm border border-slate-100">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
        <div>
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5 text-blue-500"></i> Autonomous Content Pipeline & Critic
            </h3>
            <div class="text-xs text-slate-500 mt-0.5">
                AI keyword $\rightarrow$ draft generation $\rightarrow$ Critic linting (blacklist, H2 structure, keyword density) $\rightarrow$ 1-click auto-publishing.
            </div>
        </div>
        <button id="btn-pipeline-generate" class="btn btn-sm btn-primary bg-gradient-to-r from-amber-500 to-rose-600 border-0 text-white font-medium text-xs py-1.5 px-3">
            <i data-lucide="sparkles" class="w-3.5 h-3.5 mr-1"></i> Generate AI Content Draft
        </button>
    </div>

    {{-- KANBAN LANES --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-6">

        {{-- Column 1: Ideas --}}
        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
            <div class="flex justify-between items-center pb-2 border-b border-slate-200 mb-3">
                <span class="font-bold text-xs text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span> 💡 Ideas
                </span>
                <span class="px-2 py-0.5 bg-white text-slate-600 font-bold rounded-full text-[11px] border border-slate-200">{{ $ideas->count() }}</span>
            </div>
            <div class="space-y-2.5 max-h-[600px] overflow-y-auto pr-1">
                @forelse($ideas as $item)
                    <div class="bg-white p-3 rounded-lg shadow-xs border border-slate-200 hover:shadow-sm transition-all text-xs">
                        <div class="font-semibold text-slate-800">{{ $item->title }}</div>
                        <div class="flex justify-between items-center text-[10px] text-slate-400 mt-2">
                            <span>Pillar: <strong class="text-slate-600">{{ $item->pillar ?: 'web' }}</strong></span>
                            <button class="text-rose-600 hover:underline btn-change-status" data-id="{{ $item->id }}" data-status="deleted">✕</button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs italic">No ideas in queue.</div>
                @endforelse
            </div>
        </div>

        {{-- Column 2: Drafted --}}
        <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100">
            <div class="flex justify-between items-center pb-2 border-b border-blue-200 mb-3">
                <span class="font-bold text-xs text-blue-900 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> 📝 Drafted
                </span>
                <span class="px-2 py-0.5 bg-white text-blue-800 font-bold rounded-full text-[11px] border border-blue-200">{{ $drafted->count() }}</span>
            </div>
            <div class="space-y-2.5 max-h-[600px] overflow-y-auto pr-1">
                @forelse($drafted as $item)
                    <div class="bg-white p-3 rounded-lg shadow-xs border border-blue-100 hover:shadow-sm transition-all text-xs">
                        <div class="font-semibold text-slate-800">{{ $item->title }}</div>
                        <div class="text-[10px] text-slate-400 mt-1">Pillar: {{ $item->pillar }} · {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</div>
                        <div class="flex gap-1.5 mt-2.5 pt-2 border-t border-slate-100">
                            <button class="btn btn-xs btn-outline-secondary py-0.5 px-2 text-[10px] btn-preview-article" data-item="{{ json_encode($item) }}">👁️ View</button>
                            <button class="btn btn-xs btn-outline-success py-0.5 px-2 text-[10px] btn-change-status" data-id="{{ $item->id }}" data-status="approved">✓ Approve</button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs italic">No drafts awaiting review.</div>
                @endforelse
            </div>
        </div>

        {{-- Column 3: Critic Failed --}}
        <div class="bg-amber-50/60 p-3 rounded-xl border border-amber-200">
            <div class="flex justify-between items-center pb-2 border-b border-amber-200 mb-3">
                <span class="font-bold text-xs text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> ⚠️ Critic Review
                </span>
                <span class="px-2 py-0.5 bg-white text-amber-800 font-bold rounded-full text-[11px] border border-amber-200">{{ $criticFailed->count() }}</span>
            </div>
            <div class="space-y-2.5 max-h-[600px] overflow-y-auto pr-1">
                @forelse($criticFailed as $item)
                    <div class="bg-white p-3 rounded-lg shadow-xs border border-amber-200 hover:shadow-sm transition-all text-xs">
                        <div class="font-semibold text-slate-800">{{ $item->title }}</div>
                        @php
                            $itemPayload = json_decode((string) ($item->payload ?? ''), true);
                            $criticFeedback = is_array($itemPayload) ? ($itemPayload['critic_feedback'] ?? null) : null;
                        @endphp
                        @if($criticFeedback)
                            <div class="text-[10px] text-amber-700 mt-1 bg-amber-50 p-1.5 rounded border border-amber-100">
                                <strong>Critic:</strong> {{ Str::limit($criticFeedback, 60) }}
                            </div>
                        @endif
                        <div class="flex gap-1.5 mt-2.5 pt-2 border-t border-slate-100">
                            <button class="btn btn-xs btn-outline-secondary py-0.5 px-2 text-[10px] btn-preview-article" data-item="{{ json_encode($item) }}">👁️ View</button>
                            <button class="btn btn-xs btn-outline-warning py-0.5 px-2 text-[10px] btn-change-status" data-id="{{ $item->id }}" data-status="approved">Override</button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs italic">All drafts pass Critic lint!</div>
                @endforelse
            </div>
        </div>

        {{-- Column 4: Approved --}}
        <div class="bg-emerald-50/50 p-3 rounded-xl border border-emerald-200">
            <div class="flex justify-between items-center pb-2 border-b border-emerald-200 mb-3">
                <span class="font-bold text-xs text-emerald-900 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> ✅ Approved
                </span>
                <span class="px-2 py-0.5 bg-white text-emerald-800 font-bold rounded-full text-[11px] border border-emerald-200">{{ $approved->count() }}</span>
            </div>
            <div class="space-y-2.5 max-h-[600px] overflow-y-auto pr-1">
                @forelse($approved as $item)
                    <div class="bg-white p-3 rounded-lg shadow-xs border border-emerald-200 hover:shadow-sm transition-all text-xs">
                        <div class="font-semibold text-slate-800">{{ $item->title }}</div>
                        <div class="text-[10px] text-emerald-700 mt-1">Ready for live publication</div>
                        <div class="flex gap-1.5 mt-2.5 pt-2 border-t border-slate-100">
                            <button class="btn btn-xs btn-outline-secondary py-0.5 px-2 text-[10px] btn-preview-article" data-item="{{ json_encode($item) }}">👁️ View</button>
                            <button class="btn btn-xs btn-success text-white py-0.5 px-2 text-[10px] btn-change-status" data-id="{{ $item->id }}" data-status="published">🚀 Publish</button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs italic">No approved drafts waiting.</div>
                @endforelse
            </div>
        </div>

        {{-- Column 5: Published --}}
        <div class="bg-purple-50/50 p-3 rounded-xl border border-purple-200">
            <div class="flex justify-between items-center pb-2 border-b border-purple-200 mb-3">
                <span class="font-bold text-xs text-purple-900 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span> 🚀 Published
                </span>
                <span class="px-2 py-0.5 bg-white text-purple-800 font-bold rounded-full text-[11px] border border-purple-200">{{ $published->count() }}</span>
            </div>
            <div class="space-y-2.5 max-h-[600px] overflow-y-auto pr-1">
                @forelse($published as $item)
                    <div class="bg-white p-3 rounded-lg shadow-xs border border-purple-200 hover:shadow-sm transition-all text-xs">
                        <div class="font-semibold text-slate-800">{{ $item->title }}</div>
                        <div class="text-[10px] text-purple-700 mt-1 font-medium">Live on Online Puja Blog</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Published {{ \Carbon\Carbon::parse($item->published_at ?: $item->updated_at)->format('d M Y') }}</div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs italic">No published articles yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ARTICLE PREVIEW MODAL --}}
<div id="article-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4" style="display:none;">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden shadow-2xl">
        <div class="p-4 bg-gradient-to-r from-amber-500 to-rose-600 text-white flex justify-between items-center">
            <h4 id="modal-title" class="font-bold text-sm">Article Preview</h4>
            <button id="modal-close" class="text-white hover:opacity-80 text-lg cursor-pointer">✕</button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 space-y-4 text-xs">
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                <div class="text-slate-500 font-medium">Slug & Meta Description:</div>
                <div id="modal-slug" class="font-mono text-primary mt-0.5"></div>
                <div id="modal-meta" class="text-slate-700 italic mt-1"></div>
            </div>
            <div>
                <div class="font-bold text-slate-800 text-sm mb-2">Content HTML Preview:</div>
                <div id="modal-content" class="prose prose-sm max-w-none text-slate-700 border p-4 rounded-lg bg-slate-50/50"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var genBtn = document.getElementById('btn-pipeline-generate');
    if (genBtn) {
        genBtn.addEventListener('click', function() {
            var b = this;
            b.disabled = true;
            b.innerHTML = '<span class="animate-spin inline-block mr-1">⏳</span> Drafting & Critic Linting…';
            fetch('{{ route("growth-os.action") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'generate_content' })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                b.disabled = false;
                b.innerHTML = '<i data-lucide="sparkles" class="w-3.5 h-3.5 mr-1"></i> Generate AI Content Draft';
                if (d.ok) {
                    if (typeof toastr !== 'undefined') toastr.success('New content drafted successfully!');
                    setTimeout(function() { window.location.reload(); }, 1200);
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(d.error || 'Drafting failed');
                }
            });
        });
    }

    Array.prototype.forEach.call(document.querySelectorAll('.btn-change-status'), function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var status = this.getAttribute('data-status');
            var b = this;
            b.disabled = true;

            fetch('/admin/growth-os/content/' + id + '/status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ status: status })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.ok) {
                    if (typeof toastr !== 'undefined') toastr.success(d.message);
                    setTimeout(function() { window.location.reload(); }, 800);
                } else {
                    b.disabled = false;
                    if (typeof toastr !== 'undefined') toastr.error('Failed to update status');
                }
            });
        });
    });

    var modal = document.getElementById('article-modal');
    var modalTitle = document.getElementById('modal-title');
    var modalSlug = document.getElementById('modal-slug');
    var modalMeta = document.getElementById('modal-meta');
    var modalContent = document.getElementById('modal-content');
    var modalClose = document.getElementById('modal-close');

    Array.prototype.forEach.call(document.querySelectorAll('.btn-preview-article'), function(btn) {
        btn.addEventListener('click', function() {
            var raw = this.getAttribute('data-item');
            try {
                var item = JSON.parse(raw);
                var payload = item.payload ? (typeof item.payload === 'string' ? JSON.parse(item.payload) : item.payload) : {};
                modalTitle.textContent = item.title;
                modalSlug.textContent = 'Slug: /blog/' + (payload.slug || item.slug || 'untitled');
                modalMeta.textContent = 'Meta: ' + (payload.meta_description || item.meta_description || 'No description');
                modalContent.innerHTML = payload.html || payload.content || '<p class="text-slate-400">No HTML payload available.</p>';
                modal.style.display = 'flex';
            } catch(e) {
                alert('Could not parse payload');
            }
        });
    });

    if (modalClose) {
        modalClose.addEventListener('click', function() { modal.style.display = 'none'; });
    }
    modal.addEventListener('click', function(e) {
        if (e.target === modal) modal.style.display = 'none';
    });
});
</script>
@endsection
