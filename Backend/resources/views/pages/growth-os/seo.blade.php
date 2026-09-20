@extends('pages.growth-os.layout')

@section('growth_content')
<div class="grid grid-cols-12 gap-6">

    {{-- Scoreboard Cards --}}
    <div class="col-span-12 grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="box p-4 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="p-3 bg-rose-50 text-rose-600 rounded-xl text-xl">⚠️</div>
            <div>
                <div class="text-xs text-slate-500 font-medium">Open Technical Issues</div>
                <div class="text-xl font-bold text-rose-600">{{ $openCount }}</div>
            </div>
        </div>
        <div class="box p-4 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl text-xl">🛡️</div>
            <div>
                <div class="text-xs text-slate-500 font-medium">Healed via AI Fixer</div>
                <div class="text-xl font-bold text-emerald-600">{{ $fixedCount }}</div>
            </div>
        </div>
        <div class="box p-4 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl text-xl">⚡</div>
            <div>
                <div class="text-xs text-slate-500 font-medium">God Mode Healer</div>
                <div class="text-sm font-bold text-slate-800">Weekly Auto-Fix</div>
            </div>
        </div>
        <div class="box p-4 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl text-xl">🔄</div>
            <div>
                <div class="text-xs text-slate-500 font-medium">Safety Guarantee</div>
                <div class="text-sm font-bold text-slate-800">100% Revertable</div>
            </div>
        </div>
    </div>

    {{-- Main Issue Ledger --}}
    <div class="col-span-12">
        <div class="box p-6 bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-5 h-5 text-rose-500"></i> Technical SEO Audit Ledger
                    </h3>
                    <div class="text-xs text-slate-500 mt-0.5">Scans sitemaps and pages for titles, meta lengths, H1 tags, alt attributes and accidental noindex.</div>
                </div>
                <div class="flex gap-2">
                    <button id="btn-scan-seo" class="btn btn-sm btn-outline-secondary text-xs py-1.5 px-3">
                        <i data-lucide="scan" class="w-3.5 h-3.5 mr-1"></i> Scan Sitemap
                    </button>
                    <button id="btn-fix-godmode" class="btn btn-sm btn-primary bg-gradient-to-r from-amber-500 to-rose-600 border-0 text-white font-medium text-xs py-1.5 px-3">
                        <i data-lucide="wand" class="w-3.5 h-3.5 mr-1"></i> Run AI Auto-Fix (God Mode)
                    </button>
                </div>
            </div>

            {{-- Issues Table --}}
            <div class="overflow-x-auto mt-4">
                <table class="table table-report w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600">
                            <th class="py-2.5">Issue Type</th>
                            <th class="py-2.5">Target URL / Entity</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5">Audit Payload</th>
                            <th class="py-2.5 text-center">Safety Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issues as $issue)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="py-2.5 font-bold text-slate-700">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-mono bg-slate-100 text-slate-800">
                                        {{ str_replace('_', ' ', $issue->type) }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-slate-800 font-medium">
                                    <a href="{{ $issue->page_url }}" target="_blank" class="text-primary hover:underline flex items-center gap-1">
                                        {{ Str::limit($issue->page_url, 45) }} <i data-lucide="external-link" class="w-3 h-3"></i>
                                    </a>
                                </td>
                                <td class="py-2.5">
                                    @if($issue->status === 'fixed')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            ✓ Auto-Fixed
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            ● Open
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 text-slate-500">
                                    @php
                                        $details = json_decode((string) ($issue->detail ?? ''), true);
                                    @endphp
                                    @if($details)
                                        <span class="font-mono text-[11px] text-slate-600">{{ Str::limit(json_encode($details), 50) }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="py-2.5 text-center">
                                    @if($issue->status === 'fixed' && $issue->revert_payload)
                                        <form method="POST" action="{{ route('growth-os.seo.revert', $issue->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-outline-secondary text-rose-700 hover:bg-rose-50 py-1 px-2 text-[10px]" title="Revert back to original">
                                                ↩ Revert
                                            </button>
                                        </form>
                                    @elseif($issue->status === 'open')
                                        <span class="text-[11px] text-slate-400">Heals in God-Mode run</span>
                                    @else
                                        <span class="text-[11px] text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-400">
                                    No technical SEO issues found. Click "Scan Sitemap" to audit the live site.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $issues->links() }}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var scanBtn = document.getElementById('btn-scan-seo');
    if (scanBtn) {
        scanBtn.addEventListener('click', function() {
            var b = this;
            b.disabled = true;
            b.innerHTML = 'Scanning…';
            fetch('{{ route("growth-os.action") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'seo_scan' })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                b.disabled = false;
                b.innerHTML = '<i data-lucide="scan" class="w-3.5 h-3.5 mr-1"></i> Scan Sitemap';
                if (d.ok) {
                    if (typeof toastr !== 'undefined') toastr.success(d.output || 'Scan complete');
                    setTimeout(function() { window.location.reload(); }, 1200);
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(d.error || 'Scan failed');
                }
            });
        });
    }

    var fixBtn = document.getElementById('btn-fix-godmode');
    if (fixBtn) {
        fixBtn.addEventListener('click', function() {
            var b = this;
            b.disabled = true;
            b.innerHTML = 'Applying AI Fixes…';
            fetch('{{ route("growth-os.action") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'seo_fix' })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                b.disabled = false;
                b.innerHTML = '<i data-lucide="wand" class="w-3.5 h-3.5 mr-1"></i> Run AI Auto-Fix (God Mode)';
                if (d.ok) {
                    if (typeof toastr !== 'undefined') toastr.success(d.output || 'God Mode fixes applied!');
                    setTimeout(function() { window.location.reload(); }, 1200);
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(d.error || 'Fix failed');
                }
            });
        });
    }
});
</script>
@endsection
