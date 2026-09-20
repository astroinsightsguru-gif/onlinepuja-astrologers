@extends('../layout/' . ($layout ?? 'side-menu'))

@section('subhead')
    <title>Growth & AI OS — Online Puja</title>
@endsection

@section('subcontent')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8 pb-4 border-b border-slate-200">
    <div class="mr-auto">
        <h2 class="text-2xl font-bold flex items-center gap-2 text-slate-800">
            <span class="p-2 rounded-xl bg-gradient-to-r from-amber-500 to-rose-600 text-white text-xl shadow-md">🧠</span>
            Growth & AI OS Command Center
        </h2>
        <div class="text-slate-500 text-xs mt-0.5">
            Autonomous growth, keyword tri-source research, content pipeline, technical SEO healer & multi-channel social engine.
        </div>
    </div>
    <div class="w-full sm:w-auto flex flex-wrap gap-2 mt-4 sm:mt-0">
        <button id="btn-quick-scan" class="btn btn-outline-secondary shadow-sm text-xs py-1.5 px-3 bg-white hover:bg-slate-50">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1.5 text-primary"></i> Run SEO Scan
        </button>
        <button id="btn-quick-sync" class="btn btn-outline-secondary shadow-sm text-xs py-1.5 px-3 bg-white hover:bg-slate-50">
            <i data-lucide="cpu" class="w-3.5 h-3.5 mr-1.5 text-success"></i> Sync AI Models
        </button>
        <button id="btn-quick-generate" class="btn btn-primary shadow-sm text-xs py-1.5 px-3 bg-gradient-to-r from-amber-500 to-rose-600 border-0 text-white font-medium">
            <i data-lucide="sparkles" class="w-3.5 h-3.5 mr-1.5"></i> Generate AI Content
        </button>
    </div>
</div>

{{-- Sub-Navigation Tabs --}}
<div class="intro-y mt-5">
    <div class="flex flex-wrap border-b border-slate-200 gap-1 bg-white p-2 rounded-t-xl shadow-sm">
        @php
            $currentRoute = Route::currentRouteName();
        @endphp
        <a href="{{ route('growth-os.dashboard') }}" class="py-2.5 px-4 rounded-lg font-medium text-sm flex items-center gap-2 transition-all {{ $currentRoute === 'growth-os.dashboard' ? 'bg-gradient-to-r from-amber-500 to-rose-600 text-white shadow' : 'text-slate-600 hover:bg-slate-100' }}">
            <i data-lucide="activity" class="w-4 h-4"></i> 🎯 Growth & Goals
        </a>
        <a href="{{ route('growth-os.keywords') }}" class="py-2.5 px-4 rounded-lg font-medium text-sm flex items-center gap-2 transition-all {{ $currentRoute === 'growth-os.keywords' ? 'bg-gradient-to-r from-amber-500 to-rose-600 text-white shadow' : 'text-slate-600 hover:bg-slate-100' }}">
            <i data-lucide="key" class="w-4 h-4"></i> 🔑 Keywords & Silos
        </a>
        <a href="{{ route('growth-os.content') }}" class="py-2.5 px-4 rounded-lg font-medium text-sm flex items-center gap-2 transition-all {{ $currentRoute === 'growth-os.content' ? 'bg-gradient-to-r from-amber-500 to-rose-600 text-white shadow' : 'text-slate-600 hover:bg-slate-100' }}">
            <i data-lucide="file-text" class="w-4 h-4"></i> 📝 Content Pipeline
        </a>
        <a href="{{ route('growth-os.seo') }}" class="py-2.5 px-4 rounded-lg font-medium text-sm flex items-center gap-2 transition-all {{ $currentRoute === 'growth-os.seo' ? 'bg-gradient-to-r from-amber-500 to-rose-600 text-white shadow' : 'text-slate-600 hover:bg-slate-100' }}">
            <i data-lucide="shield-check" class="w-4 h-4"></i> 🔍 SEO Auditor & Fixer
        </a>
        <a href="{{ route('growth-os.social') }}" class="py-2.5 px-4 rounded-lg font-medium text-sm flex items-center gap-2 transition-all {{ $currentRoute === 'growth-os.social' ? 'bg-gradient-to-r from-amber-500 to-rose-600 text-white shadow' : 'text-slate-600 hover:bg-slate-100' }}">
            <i data-lucide="share-2" class="w-4 h-4"></i> 📅 Social & Festivals
        </a>
        <a href="{{ route('growth-os.ai') }}" class="py-2.5 px-4 rounded-lg font-medium text-sm flex items-center gap-2 transition-all {{ $currentRoute === 'growth-os.ai' ? 'bg-gradient-to-r from-amber-500 to-rose-600 text-white shadow' : 'text-slate-600 hover:bg-slate-100' }}">
            <i data-lucide="bot" class="w-4 h-4"></i> 🤖 AI Engine & Vault
        </a>
    </div>
</div>

{{-- Feedback Messages --}}
@if(session('success'))
    <div class="intro-y alert alert-success show mb-4 mt-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-lg flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-tw-dismiss="alert">✕</button>
    </div>
@endif
@if(session('error'))
    <div class="intro-y alert alert-danger show mb-4 mt-4 bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-lg flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="btn-close" data-tw-dismiss="alert">✕</button>
    </div>
@endif

{{-- Module Content --}}
<div class="intro-y mt-4">
    @yield('growth_content')
</div>

{{-- Action Execution Modal / Toast --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    function executeAction(actionName, btn) {
        var originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block mr-1">⏳</span> Running…';

        fetch('{{ route("growth-os.action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ action: actionName })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            if (data.ok) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.output || 'Action executed successfully!');
                } else {
                    alert('Success: ' + (data.output || 'Completed'));
                }
                setTimeout(function() { window.location.reload(); }, 1200);
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(data.error || 'Action failed');
                } else {
                    alert('Error: ' + (data.error || 'Failed'));
                }
            }
        })
        .catch(function(err) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            if (typeof toastr !== 'undefined') {
                toastr.error(err.message);
            } else {
                alert('Request failed: ' + err.message);
            }
        });
    }

    var scanBtn = document.getElementById('btn-quick-scan');
    if (scanBtn) {
        scanBtn.addEventListener('click', function() { executeAction('seo_scan', this); });
    }

    var syncBtn = document.getElementById('btn-quick-sync');
    if (syncBtn) {
        syncBtn.addEventListener('click', function() { executeAction('sync_models', this); });
    }

    var genBtn = document.getElementById('btn-quick-generate');
    if (genBtn) {
        genBtn.addEventListener('click', function() { executeAction('generate_content', this); });
    }
});
</script>
@endsection
