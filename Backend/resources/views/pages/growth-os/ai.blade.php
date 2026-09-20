@extends('pages.growth-os.layout')

@section('growth_content')
<div class="grid grid-cols-12 gap-6">

    {{-- Provider Failover Chain Cards --}}
    <div class="col-span-12">
        <div class="box p-6 bg-white rounded-xl shadow-sm border border-slate-100">
            <h3 class="text-base font-bold text-slate-800 flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <span class="flex items-center gap-2">
                    <i data-lucide="cpu" class="w-5 h-5 text-emerald-600"></i> AI Provider Failover Chain & Circuit Breakers
                </span>
                <span class="text-xs font-normal text-slate-500">Auto-failover: 3 consecutive fails = 10 min bench</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                @foreach($providers as $slug => $p)
                    <div class="p-3.5 rounded-xl border {{ $p['configured'] ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200' }} text-xs">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-bold text-slate-800 text-sm">{{ $p['name'] }}</span>
                            @if($p['configured'])
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-xs" title="Ready & Configured"></span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400" title="Key Missing"></span>
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-500 font-medium">{{ $p['type'] }}</div>
                        <div class="mt-2 text-[10px] {{ $p['configured'] ? 'text-emerald-700 font-semibold' : 'text-slate-400' }}">
                            {{ $p['configured'] ? '✓ Connected' : '⚠ Requires Key' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Left: Vault Credentials Settings --}}
    <div class="col-span-12 lg:col-span-5">
        <div class="box p-6 bg-white rounded-xl shadow-sm border border-slate-100">
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                <i data-lucide="lock" class="w-4 h-4 text-amber-500"></i> AI Vault Credentials Store
            </h4>
            <p class="text-xs text-slate-500 mb-4">Values are encrypted via AES-256-GCM. Environment variables (<code class="bg-slate-100 px-1 py-0.5 rounded text-rose-600">BRAIN_*</code>) override vault values.</p>

            <form method="POST" action="{{ route('growth-os.ai.vault') }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">OmniRoute Gateway URL</label>
                    <input type="text" name="omniroute_url" value="{{ $creds['omniroute_url'] ?? 'https://ai.vmstudio.digital' }}" class="form-control form-control-sm rounded-lg font-mono text-xs">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">OmniRoute API Key</label>
                    <input type="password" name="omniroute_key" value="{{ $creds['omniroute_key'] ?? '' }}" placeholder="Paste OmniRoute Key" class="form-control form-control-sm rounded-lg font-mono text-xs">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">OpenAI API Key (Direct)</label>
                    <input type="password" name="openai_key" value="{{ $creds['openai_key'] ?? '' }}" placeholder="sk-..." class="form-control form-control-sm rounded-lg font-mono text-xs">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Google Gemini API Key</label>
                    <input type="password" name="gemini_key" value="{{ $creds['gemini_key'] ?? '' }}" placeholder="AIza..." class="form-control form-control-sm rounded-lg font-mono text-xs">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">OpenRouter API Key</label>
                    <input type="password" name="openrouter_key" value="{{ $creds['openrouter_key'] ?? '' }}" placeholder="sk-or-..." class="form-control form-control-sm rounded-lg font-mono text-xs">
                </div>
                <div>
                    <label class="block text-slate-600 font-semibold mb-1">Failover Text Chain Order</label>
                    <input type="text" name="ai_text_chain" value="{{ $creds['ai_text_chain'] ?? 'omniroute,openai,gemini,openrouter,pollinations' }}" class="form-control form-control-sm rounded-lg font-mono text-xs">
                </div>
                <button type="submit" class="btn btn-sm btn-primary w-full bg-gradient-to-r from-amber-500 to-rose-600 border-0 text-white font-medium py-2 mt-2">
                    💾 Save Vault Credentials
                </button>
            </form>
        </div>
    </div>

    {{-- Right: Synced AI Models Table --}}
    <div class="col-span-12 lg:col-span-7">
        <div class="box p-6 bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-3 border-b border-slate-100 gap-2 mb-3">
                <div>
                    <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-primary"></i> Live AI Model Catalogue ({{ $models->count() }})
                    </h4>
                    <div class="text-xs text-slate-400">Synced twice daily via <code class="text-slate-600">ai:sync-models</code>.</div>
                </div>
                <button id="btn-sync-models-now" class="btn btn-sm btn-outline-secondary text-xs py-1.5 px-3">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1"></i> Sync Now
                </button>
            </div>

            <div class="overflow-x-auto max-h-[520px] overflow-y-auto pr-1">
                <table class="table table-report w-full text-xs">
                    <thead class="sticky top-0 bg-white">
                        <tr class="bg-slate-50 text-slate-600">
                            <th class="py-2.5">Provider</th>
                            <th class="py-2.5">Model ID</th>
                            <th class="py-2.5">Kind</th>
                            <th class="py-2.5">Label</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($models as $m)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="py-2 font-bold text-slate-700">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono {{ $m->provider === 'openai' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                        {{ strtoupper($m->provider) }}
                                    </span>
                                </td>
                                <td class="py-2 font-mono font-medium text-slate-800">{{ $m->model_id }}</td>
                                <td class="py-2 text-slate-500">{{ $m->kind ?: 'chat' }}</td>
                                <td class="py-2 text-slate-600 text-[11px]">{{ $m->label ?: $m->model_id }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-slate-400">
                                    No AI models synced yet. Click "Sync Now" to query live providers.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var syncBtn = document.getElementById('btn-sync-models-now');
    if (syncBtn) {
        syncBtn.addEventListener('click', function() {
            var b = this;
            b.disabled = true;
            b.innerHTML = 'Syncing…';
            fetch('{{ route("growth-os.action") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'sync_models' })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                b.disabled = false;
                b.innerHTML = '<i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1"></i> Sync Now';
                if (d.ok) {
                    if (typeof toastr !== 'undefined') toastr.success(d.output || 'Models synced');
                    setTimeout(function() { window.location.reload(); }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(d.error || 'Sync failed');
                }
            });
        });
    }
});
</script>
@endsection
