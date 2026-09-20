@extends('pages.growth-os.layout')

@section('growth_content')
<div class="grid grid-cols-12 gap-6">

    {{-- Left: Hindu Festival Calendar --}}
    <div class="col-span-12 lg:col-span-7">
        <div class="box p-6 bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-5 h-5 text-amber-500"></i> Hindu Festival Calendar (2026–27)
                    </h3>
                    <div class="text-xs text-slate-500 mt-0.5">Seeded Hindu festival dates for automated puja promotions & devotional wishes.</div>
                </div>
                <button id="btn-seed-festivals" class="btn btn-sm btn-outline-secondary text-xs py-1.5 px-3">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1"></i> Refresh Festivals
                </button>
            </div>

            {{-- Festivals List --}}
            <div class="space-y-3 mt-4 max-h-[620px] overflow-y-auto pr-1">
                @forelse($festivals as $fest)
                    <div class="p-3.5 rounded-xl border {{ $fest->scheduled ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 text-xs">
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
                        <div class="text-right sm:self-center">
                            <span class="px-2.5 py-1 rounded bg-white text-slate-700 font-medium text-[11px] border border-slate-200 shadow-2xs">
                                {{ \Carbon\Carbon::parse($fest->date)->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400">
                        No Hindu festivals seeded. Click "Refresh Festivals" to populate 2026-2027 calendar.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Queued Social Posts & Wish Generator --}}
    <div class="col-span-12 lg:col-span-5 space-y-6">

        {{-- Auto Scheduler Card --}}
        <div class="box p-5 bg-white rounded-xl shadow-sm border border-slate-100">
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Social Wish Autopilot</h4>
            <p class="text-xs text-slate-500 mb-4">Master Brain drafts tailored devotional social posts + hashtags for upcoming festivals within the next 60-75 days.</p>
            <button id="btn-schedule-festivals" class="btn btn-sm btn-primary w-full bg-gradient-to-r from-amber-500 to-rose-600 border-0 text-white font-medium py-2 text-xs">
                ✨ Auto-Schedule Upcoming Wishes
            </button>
        </div>

        {{-- Queued Social Posts --}}
        <div class="box p-5 bg-white rounded-xl shadow-sm border border-slate-100">
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center justify-between">
                <span>Queued Social Posts</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700">{{ $socialPosts->count() }}</span>
            </h4>
            <div class="space-y-3 max-h-[460px] overflow-y-auto pr-1 text-xs">
                @forelse($socialPosts as $post)
                    @php
                        $p = json_decode((string) $post->payload, true);
                    @endphp
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="font-bold text-slate-800 flex justify-between items-center">
                            <span>{{ $post->title }}</span>
                            <span class="text-[10px] text-purple-600 font-semibold">{{ $post->status }}</span>
                        </div>
                        @if($p && !empty($p['text']))
                            <div class="text-slate-700 mt-2 bg-white p-2.5 rounded-lg border border-slate-200 text-[11px] leading-relaxed">
                                {{ $p['text'] }}
                            </div>
                        @endif
                        @if($p && !empty($p['hashtags']))
                            <div class="text-primary font-medium text-[11px] mt-1.5">
                                {{ $p['hashtags'] }}
                            </div>
                        @endif
                        <div class="text-[10px] text-slate-400 mt-2 flex justify-between items-center pt-1.5 border-t border-slate-200">
                            <span>Due: <strong class="text-slate-600">{{ $post->due_at ?: 'Soon' }}</strong></span>
                            <span>Channel: {{ $post->channel }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs italic">
                        No festival posts queued. Click "Auto-Schedule" above to queue posts.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
                b.innerHTML = '✨ Auto-Schedule Upcoming Wishes';
                if (typeof toastr !== 'undefined') toastr.success(d.reply || 'Wishes queued!');
                setTimeout(function() { window.location.reload(); }, 1200);
            })
            .catch(function(err) {
                b.disabled = false;
                b.innerHTML = '✨ Auto-Schedule Upcoming Wishes';
                if (typeof toastr !== 'undefined') toastr.error(err.message);
            });
        });
    }
});
</script>
@endsection
