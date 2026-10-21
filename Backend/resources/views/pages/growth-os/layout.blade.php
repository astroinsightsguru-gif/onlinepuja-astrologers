@extends('../layout/' . ($layout ?? 'side-menu'))

@section('subhead')
    <title>Growth & AI OS — Online Puja</title>
@endsection

@section('subcontent')
{{-- Sleek, uncluttered Command Bar --}}
<div class="intro-y flex flex-col md:flex-row md:items-center justify-between mt-6 pb-4 border-b border-slate-200/80 gap-3">
    <div>
        <div class="flex items-center gap-2.5">
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Growth & AI OS</h2>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/80 uppercase tracking-wider">Vedic Engine</span>
        </div>
        <p class="text-slate-500 text-xs mt-0.5">Autonomous SEO healing, keyword silos, content pipeline & festival social engine.</p>
    </div>
    <div class="flex items-center gap-2">
        <button id="btn-quick-scan" class="btn btn-sm btn-outline-secondary text-xs py-1.5 px-3 bg-white hover:bg-slate-50 shadow-2xs rounded-lg border-slate-200">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1.5 text-slate-600"></i> Run SEO Scan
        </button>
        <button id="btn-quick-generate" class="btn btn-sm text-xs py-1.5 px-3.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg shadow-2xs border-0">
            <i data-lucide="sparkles" class="w-3.5 h-3.5 mr-1.5 text-amber-400"></i> AI Generate
        </button>
    </div>
</div>

{{-- Sub-Navigation Tabs: Compact, Single-Row Responsive Strip (Never wraps or squishes) --}}
<div class="intro-y mt-3.5">
    <div class="flex items-center gap-1 bg-white p-1.5 rounded-xl border border-slate-200/70 shadow-2xs overflow-x-auto no-scrollbar">
        @php
            $currentRoute = Route::currentRouteName();
        @endphp
        <a href="{{ route('growth-os.dashboard') }}" class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap transition-all {{ $currentRoute === 'growth-os.dashboard' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
            <i data-lucide="activity" class="w-3.5 h-3.5 {{ $currentRoute === 'growth-os.dashboard' ? 'text-amber-400' : 'text-slate-400' }}"></i> Growth & Goals
        </a>
        <a href="{{ route('growth-os.keywords') }}" class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap transition-all {{ $currentRoute === 'growth-os.keywords' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
            <i data-lucide="key" class="w-3.5 h-3.5 {{ $currentRoute === 'growth-os.keywords' ? 'text-amber-400' : 'text-slate-400' }}"></i> Keywords & Silos
        </a>
        <a href="{{ route('growth-os.content') }}" class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap transition-all {{ $currentRoute === 'growth-os.content' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
            <i data-lucide="file-text" class="w-3.5 h-3.5 {{ $currentRoute === 'growth-os.content' ? 'text-amber-400' : 'text-slate-400' }}"></i> Content Pipeline
        </a>
        <a href="{{ route('growth-os.seo') }}" class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap transition-all {{ $currentRoute === 'growth-os.seo' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
            <i data-lucide="shield-check" class="w-3.5 h-3.5 {{ $currentRoute === 'growth-os.seo' ? 'text-amber-400' : 'text-slate-400' }}"></i> SEO Auditor & Fixer
        </a>
        <a href="{{ route('growth-os.social') }}" class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap transition-all {{ $currentRoute === 'growth-os.social' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
            <i data-lucide="share-2" class="w-3.5 h-3.5 {{ $currentRoute === 'growth-os.social' ? 'text-amber-400' : 'text-slate-400' }}"></i> Social & Festivals
        </a>
        <a href="{{ route('growth-os.ai') }}" class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center gap-2 whitespace-nowrap transition-all {{ $currentRoute === 'growth-os.ai' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' }}">
            <i data-lucide="cpu" class="w-3.5 h-3.5 {{ $currentRoute === 'growth-os.ai' ? 'text-amber-400' : 'text-slate-400' }}"></i> AI Engine & Vault
        </a>
    </div>
</div>

{{-- Feedback Messages --}}
@if(session('success'))
    <div class="intro-y alert alert-success show mb-4 mt-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-lg flex items-center justify-between shadow-2xs">
        <div class="flex items-center gap-2 text-xs font-medium">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close text-xs" data-tw-dismiss="alert">✕</button>
    </div>
@endif
@if(session('error'))
    <div class="intro-y alert alert-danger show mb-4 mt-4 bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-lg flex items-center justify-between shadow-2xs">
        <div class="flex items-center gap-2 text-xs font-medium">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="btn-close text-xs" data-tw-dismiss="alert">✕</button>
    </div>
@endif

{{-- Module Content --}}
<div class="intro-y mt-4">
    @yield('growth_content')
</div>

{{-- Action Execution Modal / Toast --}}
<div id="growth-os-notification-area" class="fixed top-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none max-w-sm"></div>

<script>
window.showGrowthNotification = function(type, message) {
    if (typeof toastr !== 'undefined' && toastr[type]) {
        toastr[type](message);
        return;
    }
    // High-visibility fallback banner
    var container = document.getElementById('growth-os-notification-area');
    if (!container) return;
    var el = document.createElement('div');
    el.className = 'pointer-events-auto p-3.5 rounded-xl text-xs font-semibold shadow-lg transition-all transform duration-300 translate-y-2 border ' + 
        (type === 'success' ? 'bg-emerald-950 text-emerald-100 border-emerald-500/50' : 'bg-rose-950 text-rose-100 border-rose-500/50');
    el.innerHTML = '<div class="flex items-center gap-2"><span>' + (type === 'success' ? '✓' : '⚠️') + '</span><span>' + (message || '') + '</span></div>';
    container.appendChild(el);
    setTimeout(function() {
        el.style.opacity = '0';
        setTimeout(function() { el.remove(); }, 300);
    }, 4000);
};

window.executeAction = function(actionName, btn, extraPayload) {
    var originalHtml = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block mr-1">⏳</span> Running…';
    }

    var payload = Object.assign({ action: actionName }, extraPayload || {});

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
        if (data.ok) {
            window.showGrowthNotification('success', data.message || data.output || 'Action executed successfully!');
            setTimeout(function() { window.location.reload(); }, 1200);
        } else {
            window.showGrowthNotification('error', data.error || 'Action failed');
        }
    })
    .catch(function(err) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
        window.showGrowthNotification('error', 'Request failed: ' + err.message);
    });
};

document.addEventListener('DOMContentLoaded', function() {
    var scanBtn = document.getElementById('btn-quick-scan');
    if (scanBtn) {
        scanBtn.addEventListener('click', function() { window.executeAction('seo_scan', this); });
    }

    var genBtn = document.getElementById('btn-quick-generate');
    if (genBtn) {
        genBtn.addEventListener('click', function() { window.executeAction('generate_content', this); });
    }
});
</script>
@endsection
