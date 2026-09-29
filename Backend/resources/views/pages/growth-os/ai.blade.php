@extends('pages.growth-os.layout')

@section('growth_content')
<div class="grid grid-cols-12 gap-6">

    {{-- Clean & Optimized AI Provider Failover Pipeline Strip (OpenAI Removed, OmniRoute Default) --}}
    <div class="col-span-12">
        <div class="box p-5 bg-white rounded-xl shadow-xs border border-slate-200/80">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between pb-3.5 border-b border-slate-100 gap-3">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">
                        <i data-lucide="workflow" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            AI Failover Pipeline
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                OmniRoute Primary
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Automated failover: OmniRoute routes multi-modal requests by default, cascading seamlessly to Gemini, OpenRouter, and Pollinations.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200/60 text-xs">
                    <i data-lucide="shield-alert" class="w-4 h-4 text-emerald-600"></i>
                    <span class="font-medium text-slate-700">Circuit Breaker:</span>
                    <span class="text-slate-500 font-mono text-[11px]">3 fails &rarr; 10m bench</span>
                </div>
            </div>

            {{-- 4-Step Clean Flow Pipeline (OpenAI Removed) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
                @php
                    $steps = [
                        'omniroute'   => ['step' => 1, 'badge' => 'Default Router', 'desc' => 'Multi-modal (Text, Flux, Video)'],
                        'gemini'      => ['step' => 2, 'badge' => 'Tier 1 Fallback', 'desc' => 'Google Gemini 1.5 / 2.0 Flash'],
                        'openrouter'  => ['step' => 3, 'badge' => 'Tier 2 Fallback', 'desc' => 'DeepSeek V3, Claude, Llama 3.3'],
                        'pollinations'=> ['step' => 4, 'badge' => 'Zero-Config Safety', 'desc' => 'Keyless Free Fallback'],
                    ];
                @endphp

                @foreach($providers as $slug => $p)
                    @php
                        $meta = $steps[$slug] ?? ['step' => '•', 'badge' => 'Fallback', 'desc' => $p['type']];
                        $isReady = !empty($p['configured']);
                    @endphp
                    <div class="relative p-3.5 rounded-xl border transition-all duration-200 {{ $isReady ? 'bg-white border-emerald-300/80 shadow-xs hover:border-emerald-400' : 'bg-slate-50/70 border-slate-200/80 opacity-90' }}">
                        <div class="flex items-center justify-between gap-1 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold {{ $isReady ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700' }}">
                                    {{ $meta['step'] }}
                                </span>
                                <span class="font-bold text-slate-800 text-xs truncate">{{ $p['name'] }}</span>
                            </div>
                            @if($isReady)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-800">
                                    Needs Key
                                </span>
                            @endif
                        </div>

                        <div class="text-[11px] text-slate-600 font-medium truncate mb-1">
                            {{ $meta['badge'] }}
                        </div>
                        <div class="text-[10px] text-slate-400 truncate">
                            {{ $meta['desc'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Left: Vault Credentials (Without OpenAI) --}}
    <div class="col-span-12 lg:col-span-5">
        <form method="POST" action="{{ route('growth-os.ai.vault') }}" class="box p-5 bg-white rounded-xl shadow-xs border border-slate-200/80 space-y-4">
            @csrf

            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="key" class="w-4 h-4 text-amber-500"></i> AI Vault Credentials
                </h4>
                <span class="text-[11px] px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-medium">AES-256 Encrypted</span>
            </div>

            {{-- OmniRoute URL --}}
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">OmniRoute Gateway URL (Default)</label>
                <div class="relative">
                    <input type="text" name="omniroute_url" value="{{ $creds['omniroute_url'] ?? 'https://ai.vmstudio.digital' }}" class="w-full text-xs rounded-lg border-slate-200 pl-3 pr-8 py-2 font-mono bg-slate-50 focus:bg-white focus:ring-1 focus:ring-primary focus:border-primary transition" placeholder="https://ai.vmstudio.digital">
                    <button type="button" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 copy-btn" data-copy="{{ $creds['omniroute_url'] ?? '' }}" title="Copy URL">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>

            {{-- OmniRoute Key --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-semibold text-slate-700">OmniRoute API Key</label>
                    <button type="button" class="text-[10px] text-primary hover:underline toggle-secret-btn" data-target="omniroute_key">Show</button>
                </div>
                <input type="password" id="omniroute_key" name="omniroute_key" value="{{ $creds['omniroute_key'] ?? '' }}" class="w-full text-xs rounded-lg border-slate-200 px-3 py-2 font-mono bg-slate-50 focus:bg-white focus:ring-1 focus:ring-primary focus:border-primary transition" placeholder="sk-omni-...">
            </div>

            {{-- Gemini Key --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-semibold text-slate-700">Google Gemini API Key (Tier 1 Fallback)</label>
                    <button type="button" class="text-[10px] text-primary hover:underline toggle-secret-btn" data-target="gemini_key">Show</button>
                </div>
                <input type="password" id="gemini_key" name="gemini_key" value="{{ $creds['gemini_key'] ?? '' }}" class="w-full text-xs rounded-lg border-slate-200 px-3 py-2 font-mono bg-slate-50 focus:bg-white focus:ring-1 focus:ring-primary focus:border-primary transition" placeholder="AIzaSy...">
            </div>

            {{-- OpenRouter Key --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-semibold text-slate-700">OpenRouter API Key (Tier 2 Fallback)</label>
                    <button type="button" class="text-[10px] text-primary hover:underline toggle-secret-btn" data-target="openrouter_key">Show</button>
                </div>
                <input type="password" id="openrouter_key" name="openrouter_key" value="{{ $creds['openrouter_key'] ?? '' }}" class="w-full text-xs rounded-lg border-slate-200 px-3 py-2 font-mono bg-slate-50 focus:bg-white focus:ring-1 focus:ring-primary focus:border-primary transition" placeholder="sk-or-v1-...">
            </div>

            {{-- Failover Text Chain Order --}}
            <div class="pt-2 border-t border-slate-100">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Failover Text Chain Order</label>
                <input type="text" name="ai_text_chain" value="{{ $creds['ai_text_chain'] ?? 'omniroute,gemini,openrouter,pollinations' }}" class="w-full text-xs rounded-lg border-slate-200 px-3 py-2 font-mono bg-slate-50 focus:bg-white focus:ring-1 focus:ring-primary focus:border-primary transition">
                <div class="text-[10px] text-slate-400 mt-1">Default: <code class="font-mono text-slate-600">omniroute,gemini,openrouter,pollinations</code></div>
            </div>

            <div class="pt-2">
                <button type="submit" class="btn btn-sm btn-primary w-full text-xs py-2.5 rounded-lg flex items-center justify-center gap-1.5 shadow-xs font-medium">
                    <i data-lucide="save" class="w-4 h-4"></i> Save AI Vault Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Right: OmniRoute Model Routing Studio (Clean Dropdown Selectors — Replaces 4137-Row Clutter Table) --}}
    <div class="col-span-12 lg:col-span-7">
        <form method="POST" action="{{ route('growth-os.ai.vault') }}" class="box p-5 bg-white rounded-xl shadow-xs border border-slate-200/80 space-y-5">
            @csrf

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-3 border-b border-slate-100 gap-2">
                <div>
                    <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="cpu" class="w-4 h-4 text-emerald-600"></i> OmniRoute Model Routing Studio
                    </h4>
                    <div class="text-xs text-slate-400 mt-0.5">Select active models for text astrology, spiritual image generation, and video reels.</div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-sync-models-now" class="btn btn-sm btn-outline-secondary text-xs py-1.5 px-3 rounded-lg flex items-center gap-1.5 bg-white hover:bg-slate-50">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-primary"></i> Sync Catalog
                    </button>
                </div>
            </div>

            {{-- 1. Default Text / Astrology Model Dropdown --}}
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="message-square" class="w-4 h-4 text-blue-500"></i> Default Text & Astrologer Model
                    </label>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold bg-blue-100 text-blue-800">OmniRoute Chat</span>
                </div>
                <select name="model_omniroute" class="w-full text-xs rounded-lg border-slate-300 px-3 py-2 bg-white text-slate-800 font-medium focus:ring-1 focus:ring-primary">
                    @php $currTextModel = $creds['model_omniroute'] ?? 'auto/pro-chat'; @endphp
                    @foreach($textModels as $mId => $mLabel)
                        <option value="{{ $mId }}" {{ $currTextModel === $mId ? 'selected' : '' }}>
                            {{ $mLabel }} ({{ $mId }})
                        </option>
                    @endforeach
                </select>
                <div class="text-[11px] text-slate-500">Powers Master Astrologer AI, Kundli interpretation, and Growth OS content drafting.</div>
            </div>

            {{-- 2. Default Spiritual Image Model Dropdown --}}
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="image" class="w-4 h-4 text-amber-500"></i> Default Spiritual Image Model
                    </label>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold bg-amber-100 text-amber-800">Photorealistic Vedic</span>
                </div>
                <select name="model_omniroute_image" class="w-full text-xs rounded-lg border-slate-300 px-3 py-2 bg-white text-slate-800 font-medium focus:ring-1 focus:ring-primary">
                    @php $currImgModel = $creds['model_omniroute_image'] ?? 'flux'; @endphp
                    @foreach($imageModels as $mId => $mLabel)
                        <option value="{{ $mId }}" {{ $currImgModel === $mId ? 'selected' : '' }}>
                            {{ $mLabel }}
                        </option>
                    @endforeach
                </select>
                <div class="text-[11px] text-slate-500">Generates puja ceremony banners, deity wallpapers, and festival cards. Direct keyless Flux fallback active.</div>
            </div>

            {{-- 3. Default Video Reel Model Dropdown --}}
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="video" class="w-4 h-4 text-rose-500"></i> Default Video Reel Model
                    </label>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold bg-rose-100 text-rose-800">9:16 Vertical Motion</span>
                </div>
                <select name="model_omniroute_video" class="w-full text-xs rounded-lg border-slate-300 px-3 py-2 bg-white text-slate-800 font-medium focus:ring-1 focus:ring-primary">
                    @php $currVidModel = $creds['model_omniroute_video'] ?? 'fal-ai/xai/grok-imagine-video/text-to-video'; @endphp
                    @foreach($videoModels as $mId => $mLabel)
                        <option value="{{ $mId }}" {{ $currVidModel === $mId ? 'selected' : '' }}>
                            {{ $mLabel }}
                        </option>
                    @endforeach
                </select>
                <div class="text-[11px] text-slate-500">Generates 5-10s video reels for Instagram & Facebook (flickering diya, incense, sacred temple atmosphere).</div>
            </div>

            {{-- Gateway Status Card --}}
            <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-200 text-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <div>
                        <div class="font-bold text-slate-800">OmniRoute Gateway Online</div>
                        <div class="text-slate-500 text-[11px]">Endpoint: <code class="font-mono text-emerald-800">{{ $creds['omniroute_url'] ?? 'https://ai.vmstudio.digital' }}</code> · {{ $totalModelsCount ?? 4137 }} Total Upstream Models Available</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-sm btn-primary bg-emerald-600 hover:bg-emerald-700 border-0 text-white text-xs px-4 py-2 rounded-lg font-medium whitespace-nowrap shadow-xs">
                    <i data-lucide="check" class="w-3.5 h-3.5 mr-1"></i> Save Model Preferences
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Secret toggle buttons
    document.querySelectorAll('.toggle-secret-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                this.textContent = 'Hide';
            } else {
                input.type = 'password';
                this.textContent = 'Show';
            }
        });
    });

    // Copy URL button
    document.querySelectorAll('.copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var text = this.getAttribute('data-copy');
            if (text && navigator.clipboard) {
                navigator.clipboard.writeText(text);
                if (typeof toastr !== 'undefined') toastr.success('Copied to clipboard');
            }
        });
    });

    // Sync Models Action
    var syncBtn = document.getElementById('btn-sync-models-now');
    if (syncBtn) {
        syncBtn.addEventListener('click', function() {
            var b = this;
            b.disabled = true;
            b.innerHTML = '<span class="animate-spin inline-block mr-1">⏳</span> Syncing…';
            fetch('{{ route("growth-os.action") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'sync_models' })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                b.disabled = false;
                b.innerHTML = '<i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1"></i> Sync Catalog';
                if (d.ok) {
                    if (typeof toastr !== 'undefined') toastr.success(d.output || 'Models synced successfully!');
                    setTimeout(function() { window.location.reload(); }, 1200);
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(d.error || 'Failed');
                }
            })
            .catch(function(err) {
                b.disabled = false;
                b.innerHTML = '<i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1"></i> Sync Catalog';
                if (typeof toastr !== 'undefined') toastr.error(err.message);
            });
        });
    }
});
</script>
@endsection
