@extends('pages.growth-os.layout')

@section('growth_content')
<div class="space-y-6">

    {{-- TOP: OmniRoute AI Creative Studio (Images & Reels) --}}
    <div class="box p-6 bg-white rounded-2xl shadow-sm border border-slate-100">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between pb-5 border-b border-slate-100 gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-rose-600 flex items-center justify-center text-white shadow-sm">
                        <i data-lucide="sparkles" class="w-5 h-5"></i>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                            OmniRoute AI Creative Studio
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">Vedic Engine v2</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Generate high-fidelity photorealistic Vedic ceremony photography, temple darshan art, and cinematic video reels via OmniRoute (Flux & Wan 2.1).</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Free Tier Flux Active
                </span>
            </div>
        </div>

        {{-- Creative Studio Controls: 2-Row Roomy Grid --}}
        <div class="grid grid-cols-12 gap-5 mt-5">
            {{-- Topic / Ceremony Selection --}}
            <div class="col-span-12 lg:col-span-6">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Puja / Festival / Ceremony</label>
                <input type="text" id="creative-topic" value="Griha Pravesh" 
                       class="w-full text-xs rounded-xl border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium text-slate-800" 
                       placeholder="e.g. Griha Pravesh, Satyanarayan, Maha Shivratri...">
                <div class="flex flex-wrap gap-1.5 mt-2.5">
                    <button type="button" class="quick-topic-btn px-2.5 py-1 rounded-lg text-[11px] bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 font-medium transition" data-topic="Griha Pravesh">🏡 Griha Pravesh</button>
                    <button type="button" class="quick-topic-btn px-2.5 py-1 rounded-lg text-[11px] bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 font-medium transition" data-topic="Satyanarayan Puja">🍌 Satyanarayan</button>
                    <button type="button" class="quick-topic-btn px-2.5 py-1 rounded-lg text-[11px] bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 font-medium transition" data-topic="Diwali Lakshmi Puja">🪔 Lakshmi Puja</button>
                    <button type="button" class="quick-topic-btn px-2.5 py-1 rounded-lg text-[11px] bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 font-medium transition" data-topic="Lord Shiva Rudrabhishek">🔱 Rudrabhishek</button>
                    <button type="button" class="quick-topic-btn px-2.5 py-1 rounded-lg text-[11px] bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 font-medium transition" data-topic="Lord Ganesha Aarti">🐘 Ganesha Aarti</button>
                </div>
            </div>

            {{-- Visual Style Preset --}}
            <div class="col-span-12 lg:col-span-6">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Aesthetic Style (Anti-Cartoon Filter Active)</label>
                <select id="creative-style" class="w-full text-xs rounded-xl border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-amber-500/20 font-medium text-slate-800">
                    <option value="photorealistic" selected>📸 Photorealistic Vedic Photography (Hasselblad 85mm f/1.8)</option>
                    <option value="temple_sanctum">🪔 Temple Sanctum Sanctorum (Dhoop & Glowing Brass Diyas)</option>
                    <option value="heritage_art">🎨 Raja Ravi Varma Heritage Fine Art (Oil on Canvas)</option>
                    <option value="video_reel">🎥 9:16 Cinematic Video Motion Reel (OmniRoute)</option>
                </select>
                <div class="text-[11px] text-slate-500 mt-2 flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>Strict anti-CGI negative prompt filter eliminates glowing plastic toys and uncanny cartoon figurines.</span>
                </div>
            </div>

            {{-- Row 2: Aspect Ratio & Actions --}}
            <div class="col-span-12 sm:col-span-4 lg:col-span-3">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Aspect Ratio</label>
                <select id="creative-ratio" class="w-full text-xs rounded-xl border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-amber-500/20 font-medium text-slate-800">
                    <option value="1:1" selected>1:1 Square (Instagram / Facebook Post)</option>
                    <option value="4:5">4:5 Portrait (Social Feed Optimal)</option>
                    <option value="9:16">9:16 Vertical (Reels / Stories)</option>
                    <option value="16:9">16:9 Landscape (Blog / Banner)</option>
                </select>
            </div>

            <div class="col-span-12 sm:col-span-8 lg:col-span-9 flex flex-col sm:flex-row items-end gap-3">
                <button id="btn-generate-creative" class="btn btn-sm flex-1 w-full bg-gradient-to-r from-amber-500 to-rose-600 text-white font-bold py-2.5 rounded-xl shadow-md hover:from-amber-600 hover:to-rose-700 border-0 flex items-center justify-center gap-2 text-xs transition">
                    <i data-lucide="sparkles" class="w-4 h-4"></i> <span id="generate-btn-text">Generate Photorealistic Vedic Image (Flux)</span>
                </button>
                <button id="btn-preview-prompt" class="btn btn-sm btn-outline-secondary w-full sm:w-auto text-xs py-2.5 px-4 rounded-xl text-slate-700 hover:bg-slate-50 flex items-center justify-center gap-1.5 whitespace-nowrap bg-white border-slate-200">
                    <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i> Inspect Vedic Prompt Tokens
                </button>
            </div>
        </div>

        {{-- Prompt Inspector Drawer --}}
        <div id="prompt-inspector" class="hidden mt-5 pt-4 border-t border-slate-100 bg-slate-50/70 p-4 rounded-xl text-xs space-y-2.5">
            <div>
                <span class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">✨ Injected Vedic Realism Tokens:</span>
                <div id="inspector-positive" class="text-slate-600 mt-1 font-mono text-[11px] leading-relaxed bg-white p-3 rounded-lg border border-slate-200"></div>
            </div>
            <div>
                <span class="font-bold text-rose-700 uppercase tracking-wider text-[10px]">🛡️ Active Anti-CGI Negative Filter:</span>
                <div id="inspector-negative" class="text-rose-600 mt-1 font-mono text-[11px] leading-relaxed bg-rose-50/50 p-3 rounded-lg border border-rose-200"></div>
            </div>
        </div>

        {{-- Live Creative Preview Box --}}
        <div id="creative-preview-card" class="hidden mt-5 pt-5 border-t border-slate-100">
            <div class="flex flex-col md:flex-row gap-5 items-start bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div class="w-full md:w-64 h-64 rounded-xl overflow-hidden bg-slate-900 flex items-center justify-center shadow-inner relative group flex-shrink-0">
                    <img id="preview-image" src="" alt="AI Generated Asset" class="w-full h-full object-cover rounded-xl hidden">
                    <video id="preview-video" src="" controls class="w-full h-full object-cover rounded-xl hidden"></video>
                    <div id="preview-loading" class="text-center text-white p-4">
                        <div class="animate-spin inline-block w-8 h-8 border-3 border-amber-500 border-t-transparent rounded-full mb-2"></div>
                        <div class="text-xs font-medium">Generating photorealistic Vedic creative…</div>
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800" id="preview-badge">Photorealistic Creative Ready</span>
                        <div class="flex items-center gap-2">
                            <a id="btn-download-creative" href="#" target="_blank" download="vedic-creative.png" class="btn btn-sm btn-outline-secondary text-xs py-1.5 px-3 rounded-lg bg-white flex items-center gap-1">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i> Download HD
                            </a>
                        </div>
                    </div>
                    <div class="text-xs text-slate-700 font-medium leading-relaxed bg-white p-3 rounded-xl border border-slate-200" id="preview-caption"></div>
                    <div class="flex items-center gap-2 pt-1">
                        <select id="select-attach-post" class="text-xs rounded-xl border-slate-200 px-3 py-2 bg-white text-slate-700 flex-1">
                            <option value="">Select Queued Post to Attach Creative...</option>
                            @foreach($socialPosts as $post)
                                <option value="{{ $post->id }}">{{ $post->title }} ({{ $post->status }})</option>
                            @endforeach
                        </select>
                        <button id="btn-attach-to-post" class="btn btn-sm btn-primary bg-amber-500 hover:bg-amber-600 border-0 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="paperclip" class="w-3.5 h-3.5"></i> Attach to Post
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN GRID: Calendar (Left) & Queued Posts (Right) --}}
    <div class="grid grid-cols-12 gap-6">

        {{-- Left: Hindu Festival Calendar --}}
        <div class="col-span-12 lg:col-span-7">
            <div class="box p-6 bg-white rounded-2xl shadow-sm border border-slate-100">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i data-lucide="calendar" class="w-5 h-5 text-amber-500"></i> Hindu Festival Calendar (2026–27)
                        </h3>
                        <div class="text-xs text-slate-500 mt-0.5">Seeded Hindu festival dates for automated puja promotions & devotional wishes.</div>
                    </div>
                    <button id="btn-seed-festivals" class="btn btn-sm btn-outline-secondary text-xs py-1.5 px-3 rounded-lg bg-white hover:bg-slate-50">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1"></i> Refresh Festivals
                    </button>
                </div>

                {{-- Festivals List --}}
                <div class="space-y-3 mt-4 max-h-[580px] overflow-y-auto pr-1">
                    @forelse($festivals as $fest)
                        <div class="p-3.5 rounded-xl border {{ $fest->scheduled ? 'bg-emerald-50/30 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs transition hover:border-amber-300">
                            <div>
                                <div class="font-bold text-sm text-slate-800 flex items-center gap-2">
                                    <span>🙏</span> {{ $fest->name }}
                                    @if($fest->scheduled)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Scheduled ✓</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Pending</span>
                                    @endif
                                </div>
                                <div class="text-slate-500 mt-1">
                                    Deity: <span class="font-medium text-slate-700">{{ $fest->deity ?: 'General' }}</span> · Date: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($fest->date)->format('D, d M Y') }}</strong>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    Channels: <span class="font-mono text-slate-600">{{ $fest->channels ?: '["facebook","instagram","x"]' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 sm:self-center">
                                <button type="button" class="btn-create-for-fest px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-amber-400 text-slate-700 hover:text-amber-700 font-semibold text-[11px] shadow-2xs flex items-center gap-1 transition"
                                        data-name="{{ $fest->name }}" data-deity="{{ $fest->deity }}">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i> Create Visual
                                </button>
                                <span class="px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-600 font-medium text-[11px]">
                                    {{ \Carbon\Carbon::parse($fest->date)->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            No Hindu festivals seeded. Click "Refresh Festivals" to populate 2026-2027 calendar.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Queued Social Posts & Wish Autopilot --}}
        <div class="col-span-12 lg:col-span-5 space-y-6">

            {{-- Auto Scheduler Card --}}
            <div class="box p-5 bg-white rounded-2xl shadow-sm border border-slate-100">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="bot" class="w-4 h-4 text-rose-500"></i> Social Wish Autopilot
                </h4>
                <p class="text-xs text-slate-500 mb-3.5 leading-relaxed">Master Brain drafts tailored devotional social posts + hashtags for upcoming festivals within the next 60-75 days.</p>
                <button id="btn-schedule-festivals" class="btn btn-sm btn-primary w-full bg-gradient-to-r from-amber-500 to-rose-600 border-0 text-white font-bold py-2.5 text-xs rounded-xl shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="wand" class="w-4 h-4"></i> Auto-Schedule Upcoming Wishes
                </button>
            </div>

            {{-- Queued Social Posts --}}
            <div class="box p-5 bg-white rounded-2xl shadow-sm border border-slate-100">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center justify-between">
                    <span>Queued Social Posts</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">{{ $socialPosts->count() }}</span>
                </h4>
                <div class="space-y-3.5 max-h-[460px] overflow-y-auto pr-1 text-xs">
                    @forelse($socialPosts as $post)
                        @php
                            $p = json_decode((string) $post->payload, true);
                        @endphp
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                            <div class="font-bold text-slate-900 flex justify-between items-center">
                                <span>{{ $post->title }}</span>
                                <span class="text-[10px] text-purple-600 font-bold uppercase tracking-wider bg-purple-50 px-2 py-0.5 rounded-full">{{ $post->status }}</span>
                            </div>

                            @if(!empty($p['image_url']))
                                <div class="w-full h-32 rounded-lg overflow-hidden border border-slate-200 relative group">
                                    <img src="{{ $p['image_url'] }}" alt="Attached Creative" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 right-1 bg-black/60 text-white text-[9px] px-1.5 py-0.5 rounded">Vedic Visual Attached</span>
                                </div>
                            @endif

                            @if($p && !empty($p['text']))
                                <div class="text-slate-700 bg-white p-3 rounded-xl border border-slate-200 text-[11px] leading-relaxed">
                                    {{ $p['text'] }}
                                </div>
                            @endif

                            @if($p && !empty($p['hashtags']))
                                <div class="text-primary font-medium text-[11px]">
                                    {{ $p['hashtags'] }}
                                </div>
                            @endif

                            <div class="text-[11px] text-slate-400 flex justify-between items-center pt-2 border-t border-slate-200">
                                <span>Due: <strong class="text-slate-700">{{ $post->due_at ?: 'Soon' }}</strong></span>
                                <span class="font-mono text-slate-600 uppercase text-[10px]">{{ $post->channel }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs italic">
                            No festival posts queued. Click "Auto-Schedule" above to queue posts.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var topicInput = document.getElementById('creative-topic');
    var styleSelect = document.getElementById('creative-style');
    var ratioSelect = document.getElementById('creative-ratio');
    var btnText = document.getElementById('generate-btn-text');

    function syncBtnLabel() {
        if (styleSelect.value === 'video_reel') {
            btnText.textContent = 'Generate Cinematic Video Reel (9:16)';
            ratioSelect.value = '9:16';
        } else {
            btnText.textContent = 'Generate Photorealistic Vedic Image (Flux)';
        }
    }
    styleSelect.addEventListener('change', function() {
        syncBtnLabel();
        updatePromptPreview();
    });
    syncBtnLabel();

    // Quick topic pills
    document.querySelectorAll('.quick-topic-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            topicInput.value = this.getAttribute('data-topic');
            updatePromptPreview();
        });
    });

    // Festival row quick button
    document.querySelectorAll('.btn-create-for-fest').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var name = this.getAttribute('data-name');
            var deity = this.getAttribute('data-deity');
            topicInput.value = deity ? (name + ' (' + deity + ')') : name;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            updatePromptPreview();
        });
    });

    // Inspect prompt
    var promptInspector = document.getElementById('prompt-inspector');
    var btnInspect = document.getElementById('btn-preview-prompt');
    if (btnInspect) {
        btnInspect.addEventListener('click', function() {
            if (promptInspector.classList.contains('hidden')) {
                promptInspector.classList.remove('hidden');
                updatePromptPreview();
            } else {
                promptInspector.classList.add('hidden');
            }
        });
    }

    function updatePromptPreview() {
        var topic = topicInput.value.trim() || 'Griha Pravesh';
        var style = styleSelect.value;
        var ratio = ratioSelect.value;
        fetch('{{ route("growth-os.action") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ action: 'preview_social_prompt', topic: topic, style: style, aspect_ratio: ratio })
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.ok && d.data) {
                document.getElementById('inspector-positive').textContent = d.data.prompt;
                document.getElementById('inspector-negative').textContent = d.data.negative_prompt;
            }
        });
    }

    // Generate Visual via OmniRoute
    var btnGenerate = document.getElementById('btn-generate-creative');
    var previewCard = document.getElementById('creative-preview-card');
    var previewImg = document.getElementById('preview-image');
    var previewVid = document.getElementById('preview-video');
    var previewLoading = document.getElementById('preview-loading');
    var previewCaption = document.getElementById('preview-caption');
    var btnDownload = document.getElementById('btn-download-creative');
    var lastGeneratedUrl = null;

    if (btnGenerate) {
        btnGenerate.addEventListener('click', function() {
            var topic = topicInput.value.trim() || 'Griha Pravesh';
            var style = styleSelect.value;
            var ratio = ratioSelect.value;
            var isVideo = (style === 'video_reel');

            previewCard.classList.remove('hidden');
            previewLoading.classList.remove('hidden');
            previewImg.classList.add('hidden');
            previewVid.classList.add('hidden');
            btnGenerate.disabled = true;
            btnGenerate.innerHTML = '<span class="animate-spin inline-block mr-1">⏳</span> Generating ' + (isVideo ? 'Video Reel' : 'Photorealistic Image') + '…';

            fetch('{{ route("growth-os.action") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    action: 'generate_social_creative',
                    topic: topic,
                    style: style,
                    aspect_ratio: ratio,
                    type: isVideo ? 'video' : 'image'
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                btnGenerate.disabled = false;
                syncBtnLabel();
                btnGenerate.innerHTML = '<i data-lucide="sparkles" class="w-4 h-4 mr-1"></i> ' + (isVideo ? 'Generate Cinematic Video Reel' : 'Generate Photorealistic Vedic Image (Flux)');
                previewLoading.classList.add('hidden');

                if (d.ok && d.data) {
                    var data = d.data;
                    previewCaption.textContent = data.prompt || topic;
                    if (data.url) {
                        lastGeneratedUrl = data.url;
                        previewImg.src = data.url;
                        previewImg.classList.remove('hidden');
                        btnDownload.href = data.url;
                        if (typeof toastr !== 'undefined') toastr.success('Photorealistic Vedic creative generated successfully!');
                    } else if (data.video_url) {
                        lastGeneratedUrl = data.video_url;
                        previewVid.src = data.video_url;
                        previewVid.classList.remove('hidden');
                        btnDownload.href = data.video_url;
                        if (typeof toastr !== 'undefined') toastr.success('Devotional reel motion generated!');
                    }
                } else {
                    var err = (d.data && d.data.message) || d.error || 'Generation failed';
                    if (typeof toastr !== 'undefined') toastr.error(err);
                }
            })
            .catch(function(err) {
                btnGenerate.disabled = false;
                syncBtnLabel();
                btnGenerate.innerHTML = '<i data-lucide="sparkles" class="w-4 h-4 mr-1"></i> ' + (isVideo ? 'Generate Cinematic Video Reel' : 'Generate Photorealistic Vedic Image (Flux)');
                previewLoading.classList.add('hidden');
                if (typeof toastr !== 'undefined') toastr.error(err.message);
            });
        });
    }

    // Attach to Post
    var btnAttach = document.getElementById('btn-attach-to-post');
    if (btnAttach) {
        btnAttach.addEventListener('click', function() {
            var postId = document.getElementById('select-attach-post').value;
            if (!postId) {
                alert('Please select a queued post from the dropdown');
                return;
            }
            if (!lastGeneratedUrl) {
                alert('Please generate a visual asset first!');
                return;
            }

            btnAttach.disabled = true;
            btnAttach.textContent = 'Attaching…';
            fetch('{{ route("growth-os.action") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'attach_social_creative', post_id: postId, image_url: lastGeneratedUrl })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                btnAttach.disabled = false;
                btnAttach.innerHTML = '<i data-lucide="paperclip" class="w-3.5 h-3.5 mr-1"></i> Attach to Post';
                if (d.ok) {
                    if (typeof toastr !== 'undefined') toastr.success(d.message || 'Attached!');
                    setTimeout(function() { window.location.reload(); }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(d.error || 'Failed');
                }
            });
        });
    }

    // Seed Festivals
    var seedBtn = document.getElementById('btn-seed-festivals');
    if (seedBtn) {
        seedBtn.addEventListener('click', function() {
            var b = this;
            b.disabled = true;
            b.innerHTML = 'Seeding…';
            fetch('{{ route("growth-os.action") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'seed_festivals' })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                b.disabled = false;
                b.innerHTML = '<i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1"></i> Refresh Festivals';
                if (d.ok) {
                    if (typeof toastr !== 'undefined') toastr.success(d.output || 'Festivals refreshed');
                    setTimeout(function() { window.location.reload(); }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(d.error || 'Failed');
                }
            });
        });
    }

    // Auto Schedule Festivals
    var schedBtn = document.getElementById('btn-schedule-festivals');
    if (schedBtn) {
        schedBtn.addEventListener('click', function() {
            var b = this;
            b.disabled = true;
            b.innerHTML = '<span class="animate-spin inline-block mr-1">⏳</span> Writing Devotional Posts…';
            fetch('{{ route("brain.chat") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ message: 'schedule upcoming hindu festival wishes this month' })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                b.disabled = false;
                b.innerHTML = '<i data-lucide="wand" class="w-4 h-4 mr-1"></i> Auto-Schedule Upcoming Wishes';
                if (typeof toastr !== 'undefined') toastr.success(d.reply || 'Wishes queued!');
                setTimeout(function() { window.location.reload(); }, 1200);
            })
            .catch(function(err) {
                b.disabled = false;
                b.innerHTML = '<i data-lucide="wand" class="w-4 h-4 mr-1"></i> Auto-Schedule Upcoming Wishes';
                if (typeof toastr !== 'undefined') toastr.error(err.message);
            });
        });
    }
});
</script>
@endsection
