@extends('pages.growth-os.layout')

@section('growth_content')
<div class="grid grid-cols-12 gap-6">

    {{-- NORTH STAR GOAL CARD --}}
    <div class="col-span-12 lg:col-span-8">
        <div class="box p-6 bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                <div>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200">🎯 Active North-Star Goal</span>
                    <h3 class="text-xl font-bold text-slate-800 mt-2">{{ $goal ? $goal->title : '100,000 visitors in 50 days' }}</h3>
                    <div class="text-xs text-slate-500 mt-1">
                        Timeline: <span class="font-medium text-slate-700">{{ $goal ? $goal->start_date : '2026-08-26' }}</span> to <span class="font-medium text-slate-700">{{ $goal ? $goal->deadline : '2026-10-15' }}</span> · Day <span class="font-bold text-rose-600">{{ $elapsed }}</span> of <span class="font-bold">{{ $horizon }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-black text-slate-800">{{ number_format($totalTraffic) }} <span class="text-xs font-normal text-slate-400">/ {{ number_format($goal ? $goal->target : 100000) }}</span></div>
                    <div class="text-xs font-semibold {{ $pace >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">
                        Current Pace: {{ $pace }}% (Expected: {{ number_format($expected) }})
                    </div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="mt-5">
                <div class="flex justify-between text-xs text-slate-500 mb-1">
                    <span>Progress to 100K</span>
                    <span>{{ round(($totalTraffic / max(1, $goal ? $goal->target : 100000)) * 100, 2) }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-rose-600 h-3.5 rounded-full transition-all duration-500" style="width: {{ min(100, max(2, ($totalTraffic / max(1, $goal ? $goal->target : 100000)) * 100)) }}%"></div>
                </div>
            </div>

            {{-- Honest Arithmetic Levers --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-5 border-t border-slate-100 text-center">
                <div class="p-3 bg-slate-50 rounded-lg">
                    <div class="text-xs text-slate-500 font-medium">Daily Required Traffic</div>
                    <div class="text-lg font-bold text-slate-800 mt-1">~{{ number_format(round(($goal ? $goal->target : 100000) / max(1, $horizon))) }} / day</div>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg">
                    <div class="text-xs text-slate-500 font-medium">Implied Content Output</div>
                    <div class="text-lg font-bold text-slate-800 mt-1">2-4 Articles / day</div>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg">
                    <div class="text-xs text-slate-500 font-medium">Engine Mode</div>
                    <div class="text-lg font-bold text-emerald-600 mt-1">Autonomous Compound</div>
                </div>
            </div>
        </div>

        {{-- Traffic Snapshots Table --}}
        <div class="box p-6 bg-white rounded-xl shadow-sm border border-slate-100 mt-6">
            <h4 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-4 h-4 text-primary"></i> Nightly Traffic Snapshots
            </h4>
            <div class="overflow-x-auto">
                <table class="table table-report w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600">
                            <th class="py-2.5">Date</th>
                            <th class="py-2.5">Actual Traffic</th>
                            <th class="py-2.5">Target Traffic</th>
                            <th class="py-2.5">Pace Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($snapshots as $snap)
                            <tr class="border-b border-slate-100">
                                <td class="py-2.5 font-medium">{{ $snap->date }}</td>
                                <td class="py-2.5">{{ number_format($snap->value) }}</td>
                                <td class="py-2.5 text-slate-500">{{ number_format($expected) }}</td>
                                <td class="py-2.5">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $snap->value >= $expected ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $expected > 0 ? round(($snap->value / $expected) * 100) : 0 }}% pace
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-slate-400">
                                    Nightly snapshots trigger automatically at 23:40 via cron (`artisan goal:snapshot`).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- QUICK STATS & RECENT MASTER BRAIN CHATS --}}
    <div class="col-span-12 lg:col-span-4 space-y-6">

        {{-- Metrics Summary --}}
        <div class="box p-5 bg-white rounded-xl shadow-sm border border-slate-100">
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Autonomous Pipeline Stats</h4>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                    <span class="text-slate-500 flex items-center gap-1.5"><i data-lucide="key" class="w-3.5 h-3.5 text-amber-500"></i> Researched Keywords</span>
                    <span class="font-bold text-slate-800">{{ $stats['total_keywords'] }} ({{ $stats['easy_keywords'] }} easy win)</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                    <span class="text-slate-500 flex items-center gap-1.5"><i data-lucide="file-text" class="w-3.5 h-3.5 text-blue-500"></i> Content Drafts</span>
                    <span class="font-bold text-slate-800">{{ $stats['drafted_content'] }} drafted · {{ $stats['approved_content'] }} approved</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                    <span class="text-slate-500 flex items-center gap-1.5"><i data-lucide="shield-alert" class="w-3.5 h-3.5 text-rose-500"></i> Open SEO Issues</span>
                    <span class="font-bold text-rose-600">{{ $stats['open_seo_issues'] }} open · {{ $stats['fixed_seo_issues'] }} fixed</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                    <span class="text-slate-500 flex items-center gap-1.5"><i data-lucide="calendar" class="w-3.5 h-3.5 text-purple-500"></i> Hindu Festivals</span>
                    <span class="font-bold text-slate-800">{{ $stats['festivals_seeded'] }} total · {{ $stats['festivals_scheduled'] }} scheduled</span>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-slate-500 flex items-center gap-1.5"><i data-lucide="cpu" class="w-3.5 h-3.5 text-emerald-500"></i> Live AI Models</span>
                    <span class="font-bold text-emerald-700">{{ $stats['ai_models_count'] }} synced</span>
                </div>
            </div>
        </div>

        {{-- Recent Brain Command Log --}}
        <div class="box p-5 bg-white rounded-xl shadow-sm border border-slate-100">
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center justify-between">
                <span>Master Brain Log</span>
                <span class="text-xs font-normal text-slate-400">Recent Chats</span>
            </h4>
            <div class="space-y-3 max-h-80 overflow-y-auto pr-1 text-xs">
                @forelse($recentChats as $chat)
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                        <div class="font-semibold text-slate-800 flex items-center gap-1">
                            <span class="text-primary">👤</span> {{ Str::limit($chat->message, 45) }}
                        </div>
                        <div class="text-slate-600 mt-1 pl-4 border-l-2 border-amber-400">
                            {{ Str::limit($chat->response, 90) }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-1 text-right">
                            Provider: <span class="font-medium text-slate-600">{{ $chat->provider ?: 'auto' }}</span> · {{ \Carbon\Carbon::parse($chat->created_at)->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400">
                        No chatbot commands logged yet. Click the 🧠 floating icon to give commands!
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
