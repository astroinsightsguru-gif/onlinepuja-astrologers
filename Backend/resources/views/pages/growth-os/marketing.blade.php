@extends('pages.growth-os.layout')

@section('growth_content')
<div class="space-y-6">

    {{-- Channel Gateways Health Header --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        {{-- WhatsApp Evolution Node --}}
        <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 font-bold">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">WhatsApp Gateway</h3>
                        <p class="text-[11px] text-slate-500">Evolution API v2</p>
                    </div>
                </div>
                @php
                    $waStateVal = $waState['state'] ?? ($waState['instance']['state'] ?? 'connecting');
                    $isWaConnected = ($waStateVal === 'open');
                    $waStatusText = $waStateVal;
                @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $isWaConnected ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    <span class="w-2 h-2 rounded-full {{ $isWaConnected ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                    {{ ucfirst($waStatusText) }}
                </span>
            </div>

            <div class="mt-4 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Instance ID:</span>
                    <span class="font-mono font-semibold text-slate-800">onlinepuja</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Primary Number:</span>
                    <span class="font-semibold text-slate-800">+91 70071 58014</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Endpoint:</span>
                    <span class="font-mono text-[11px] text-slate-500 truncate max-w-[170px]" title="https://wa.vmstudio.digital">wa.vmstudio.digital</span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2">
                <button type="button" onclick="openWaQrModal()" class="flex-1 btn btn-sm bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 rounded-xl text-xs flex items-center justify-center gap-1.5 shadow-sm">
                    <i data-lucide="qr-code" class="w-3.5 h-3.5"></i> Scan QR
                </button>
                <button type="button" onclick="testWhatsAppPing()" class="btn btn-sm btn-outline-secondary py-2 px-3 text-xs rounded-xl hover:bg-slate-50">
                    <i data-lucide="send" class="w-3.5 h-3.5 text-slate-600"></i> Ping
                </button>
            </div>
        </div>

        {{-- Brevo Email Engine --}}
        <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 font-bold">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Brevo Email Engine</h3>
                        <p class="text-[11px] text-slate-500">Official REST API v3</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $brevoConfigured ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800' }}">
                    <span class="w-2 h-2 rounded-full {{ $brevoConfigured ? 'bg-blue-500' : 'bg-rose-500' }}"></span>
                    {{ $brevoConfigured ? 'Connected' : 'Missing Key' }}
                </span>
            </div>

            <div class="mt-4 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Sender:</span>
                    <span class="font-semibold text-slate-800">Online Puja Varanasi</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Authorized IP:</span>
                    <a href="https://app.brevo.com/security/authorised_ips" target="_blank" class="text-blue-600 font-semibold underline flex items-center gap-1">
                        200.234.41.231 <i data-lucide="external-link" class="w-3 h-3"></i>
                    </a>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Contact List:</span>
                    <span class="font-semibold text-slate-800">List #2 (Devotees)</span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2">
                <button type="button" onclick="syncDevoteesToBrevo()" class="flex-1 btn btn-sm bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-xl text-xs flex items-center justify-center gap-1.5 shadow-sm">
                    <i data-lucide="users" class="w-3.5 h-3.5"></i> Sync Contacts
                </button>
                <button type="button" onclick="testBrevoEmail()" class="btn btn-sm btn-outline-secondary py-2 px-3 text-xs rounded-xl hover:bg-slate-50">
                    <i data-lucide="send" class="w-3.5 h-3.5 text-slate-600"></i> Ping
                </button>
            </div>
        </div>

        {{-- Android z-Agents App Node --}}
        <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 font-bold">
                        <i data-lucide="smartphone" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">z-Agents Android</h3>
                        <p class="text-[11px] text-slate-500">vmai-plugins/z-agents</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    Standby Gateway
                </span>
            </div>

            <div class="mt-4 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Package:</span>
                    <span class="font-mono text-slate-800">com.vmai.zagents</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Role:</span>
                    <span class="font-semibold text-slate-800">Background SMS / WhatsApp</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Webhook Trigger:</span>
                    <span class="font-mono text-slate-500">/api/agent/trigger</span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2">
                <a href="https://github.com/vmai-plugins/z-agents" target="_blank" class="flex-1 btn btn-sm bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 rounded-xl text-xs flex items-center justify-center gap-1.5 shadow-sm">
                    <i data-lucide="github" class="w-3.5 h-3.5"></i> View Repo
                </a>
            </div>
        </div>

    </div>

    {{-- Audience Metrics Strip --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-800">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Total Devotees</p>
                <h4 class="text-lg font-extrabold text-slate-900">{{ number_format($totalDevotees) }}</h4>
            </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <i data-lucide="phone" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">WhatsApp Reachable</p>
                <h4 class="text-lg font-extrabold text-slate-900">{{ number_format($devoteesWithPhone) }}</h4>
            </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <i data-lucide="mail-check" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Email Reachable</p>
                <h4 class="text-lg font-extrabold text-slate-900">{{ number_format($devoteesWithEmail) }}</h4>
            </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <i data-lucide="flame" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Bookable Pujas</p>
                <h4 class="text-lg font-extrabold text-slate-900">{{ count($pujas) }} Active</h4>
            </div>
        </div>
    </div>

    {{-- 🪔 Indian Festivals Radar & Autonomous Multi-Channel Cockpit --}}
    <div class="bg-gradient-to-br from-amber-500/10 via-rose-500/5 to-orange-500/10 border-2 border-amber-300/80 rounded-3xl p-6 shadow-sm relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl select-none pointer-events-none">
            🪔
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-amber-200/60">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-900 mb-2">
                    <span>🕉️</span> Autonomous Festival Campaign Radar
                </div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Upcoming Indian Festivals & Multi-Channel Campaigns</h3>
                <p class="text-xs text-slate-600 mt-1">
                    Detects approaching Hindu festivals, synthesizes 1080×1920 MP4 Video Reels, crafts devotional Sanskrit copy, and broadcasts simultaneously across WhatsApp, Brevo Email, and Web/App Push.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="loadFestivalCampaign('Dev Deepawali Varanasi')" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-600 to-rose-600 hover:from-amber-700 hover:to-rose-700 text-white shadow-md flex items-center gap-2 transition-all">
                    <span>🪔</span> Dev Deepawali Spotlight
                </button>
            </div>
        </div>

        {{-- Dev Deepawali Spotlight Banner --}}
        <div class="mt-5 bg-gradient-to-r from-slate-900 via-amber-950 to-slate-900 text-white rounded-2xl p-6 shadow-lg border border-amber-500/40 relative">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <div class="lg:col-span-7 space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-400 text-slate-950 uppercase tracking-wider">
                        ✨ Next Major Varanasi Pilgrimage Event
                    </div>
                    <h2 class="text-2xl font-black text-amber-100 tracking-tight">
                        Dev Deepawali Varanasi 2026 (Kartik Purnima)
                    </h2>
                    <p class="text-xs text-amber-200/90 leading-relaxed">
                        On <strong>Tuesday, 24 November 2026</strong>, all 84 Ghats of Varanasi illuminate with 10+ Lakh earthen lamps as the gods descend to bathe in the holy Ganga. Offer 108 Deepdaan at Dashashwamedh & Manikarnika Ghats with family Gotra Sankalp.
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px]">
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-950 text-emerald-300 border border-emerald-700/60 font-semibold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> WhatsApp: 1080x1920 Reel + Copy Ready
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-blue-950 text-blue-300 border border-blue-700/60 font-semibold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-400"></span> Brevo Email: Responsive HTML Ready
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-purple-950 text-purple-300 border border-purple-700/60 font-semibold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-purple-400"></span> Push: OneSignal Broadcast Ready
                        </span>
                    </div>
                </div>

                <div class="lg:col-span-5 flex flex-col gap-2.5 bg-black/40 p-4 rounded-xl border border-amber-500/30">
                    <div class="text-center pb-2 border-b border-amber-500/20">
                        <span class="text-[11px] text-amber-300/80 uppercase tracking-wider font-semibold">Campaign Orchestration</span>
                    </div>
                    <button type="button" onclick="loadFestivalCampaign('Dev Deepawali Varanasi')" class="w-full py-2.5 px-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-md">
                        <i data-lucide="edit-3" class="w-4 h-4"></i> 1. Load in Campaign Composer
                    </button>
                    <button type="button" id="btn-gen-dev-reel" onclick="generateFestivalReel('Dev Deepawali Varanasi')" class="w-full py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-200 border border-amber-400/40 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                        <i data-lucide="film" class="w-4 h-4"></i> 2. Generate 1080x1920 MP4 Reel
                    </button>
                    <button type="button" id="btn-quick-launch-dev" onclick="quickLaunchFestivalCampaign('Dev Deepawali Varanasi')" class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-md">
                        <i data-lucide="zap" class="w-4 h-4"></i> 3. Launch Multi-Channel Campaign
                    </button>
                </div>
            </div>
        </div>

        {{-- Upcoming Festivals Grid --}}
        <div class="mt-6">
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span>🗓️</span> Upcoming Sacred Festival Calendar (Next 60 Days)
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5">
                @if(isset($festivalsRadar) && count($festivalsRadar) > 0)
                    @foreach($festivalsRadar as $fest)
                        @php
                            $badgeColor = match($fest['milestone']) {
                                't_minus_1', 'day_0' => 'bg-rose-100 text-rose-800 border-rose-300 animate-pulse',
                                't_minus_3' => 'bg-amber-100 text-amber-800 border-amber-300',
                                't_minus_7' => 'bg-blue-100 text-blue-800 border-blue-300',
                                't_minus_14' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <div class="bg-white/90 border border-amber-200/80 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                                    <span class="text-[11px] font-bold text-slate-500">{{ $fest['formatted_date'] }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeColor }}">
                                        {{ $fest['days_left'] }}d left
                                    </span>
                                </div>
                                <h5 class="text-sm font-extrabold text-slate-900 mt-2">{{ $fest['name'] }}</h5>
                                <p class="text-[11px] text-slate-500 mt-0.5">Deity: <span class="font-semibold text-slate-700">{{ $fest['deity'] ?? 'Vedic' }}</span></p>

                                @if(!empty($fest['puja']))
                                    <div class="mt-2 text-[10.5px] bg-amber-50/80 border border-amber-200/60 rounded-lg p-2 text-amber-900 truncate" title="{{ $fest['puja']['title'] }}">
                                        🪔 {{ $fest['puja']['title'] }}
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center gap-1.5">
                                <button type="button" onclick="loadFestivalCampaign('{{ addslashes($fest['name']) }}')" class="flex-1 py-1.5 px-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-[11px] font-bold text-center transition-all">
                                    Plan Campaign
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full py-4 text-center text-xs text-slate-500">
                        No upcoming festivals found in the next 60 days. Run <code>php artisan brain:seed-festivals</code> to refresh.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 🤝 B2B Partner Network & Google Maps Lead Automation Hub --}}
    <div class="bg-gradient-to-br from-indigo-900 via-slate-900 to-slate-950 text-white rounded-3xl p-6 lg:p-8 shadow-xl border border-indigo-500/30 relative overflow-hidden">
        <div class="absolute -right-12 -top-12 opacity-10 text-9xl select-none pointer-events-none">
            🤝
        </div>

        {{-- B2B Header & Metrics --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-indigo-500/20">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 mb-2">
                    <span>💼</span> B2B Partner Distribution Engine
                </div>
                <h2 class="text-2xl font-black text-white tracking-tight">
                    Promote Dev Deepawali 2026 & Varanasi Pujas via National B2B Partners
                </h2>
                <p class="text-xs text-slate-300 mt-1 max-w-3xl leading-relaxed">
                    Partner with Travel Agencies, Pilgrimage Tour Operators, Astrologers, and Samagri Stores across Mumbai, Surat, Ahmedabad, Delhi, Kolkata, and Varanasi. They sell your Kashi Vedic Pujas to their clients with zero logistics, and earn a lucrative <strong>25% Revenue Share</strong> per booking.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="seedB2bDirectory()" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white border border-indigo-400/40 shadow-md flex items-center gap-1.5 transition-all">
                    <span>⚡</span> Seed Premier Directory
                </button>
            </div>
        </div>

        {{-- B2B Stat Counters --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 my-6">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                <p class="text-[11px] font-semibold text-indigo-300 uppercase tracking-wider">B2B Partners in Network</p>
                <h4 class="text-2xl font-black text-white mt-1">{{ number_format($totalB2bPartners ?? 0) }}</h4>
                <p class="text-[10px] text-slate-400 mt-0.5">Travel agents & astrologers</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                <p class="text-[11px] font-semibold text-emerald-300 uppercase tracking-wider">Partner Margin / Revenue Share</p>
                <h4 class="text-2xl font-black text-emerald-400 mt-1">25% Net</h4>
                <p class="text-[10px] text-slate-400 mt-0.5">₹300 - ₹600 profit / booking</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                <p class="text-[11px] font-semibold text-amber-300 uppercase tracking-wider">Top Indian Hubs</p>
                <h4 class="text-xl font-bold text-amber-200 mt-1 truncate">
                    {{ isset($b2bCities) && count($b2bCities) > 0 ? $b2bCities->take(3)->pluck('city')->implode(', ') : 'Mumbai, Surat, Delhi' }}
                </h4>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ count($b2bCities ?? []) }} cities active</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                <p class="text-[11px] font-semibold text-purple-300 uppercase tracking-wider">Dev Deepawali 2026 Target</p>
                <h4 class="text-2xl font-black text-purple-300 mt-1">₹5,00,000+</h4>
                <p class="text-[10px] text-slate-400 mt-0.5">1,000 partner bookings goal</p>
            </div>
        </div>

        {{-- B2B Actions Grid: Google Maps Importer & Live Radar --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">
            
            {{-- Google Maps Lead Ingestion (Left 7 cols) --}}
            <div class="lg:col-span-7 bg-slate-900/80 border border-indigo-500/20 rounded-2xl p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-white/10">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📍</span>
                        <div>
                            <h3 class="text-sm font-bold text-white">Google Maps Business Lead Ingestion</h3>
                            <p class="text-[11px] text-slate-400">Import CSV from Google Maps scraper tools or search via Live Radar</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Auto +91 Cleaner</span>
                </div>

                {{-- Sub-tabs --}}
                <div class="flex items-center gap-2 border-b border-white/10 pb-2 text-xs">
                    <button type="button" onclick="switchB2bTab('csv')" id="tab-btn-csv" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-bold transition-all">
                        📁 CSV / Excel Upload
                    </button>
                    <button type="button" onclick="switchB2bTab('radar')" id="tab-btn-radar" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 font-semibold transition-all">
                        🔍 Live City Radar
                    </button>
                </div>

                {{-- Sub-tab 1: CSV Upload --}}
                <div id="b2b-tab-csv" class="space-y-3">
                    <p class="text-[11.5px] text-slate-300">
                        Upload leads scraped from Google Maps (compatible with <strong>Outscraper, Apify, Instant Data Scraper, PhantomBuster</strong>). The engine automatically detects business name, phone, address, and Google rating.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block text-[11px] text-slate-400 font-semibold mb-1">Default City</label>
                            <input type="text" id="b2b-csv-city" placeholder="e.g. Mumbai, Surat, Delhi" value="Mumbai" class="w-full text-xs rounded-xl bg-slate-950 border border-slate-700 text-white p-2.5 focus:border-indigo-400 focus:ring-0">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 font-semibold mb-1">Category</label>
                            <select id="b2b-csv-category" class="w-full text-xs rounded-xl bg-slate-950 border border-slate-700 text-white p-2.5 focus:border-indigo-400 focus:ring-0">
                                <option value="travel_agency">✈️ Travel Agency / Tour Operator</option>
                                <option value="astrologer">🔮 Astrologer / Jyotish Kendra</option>
                                <option value="samagri_store">🪔 Puja Samagri / Spiritual Store</option>
                                <option value="hotel">🏨 Hotels & Dharamshalas</option>
                                <option value="corporate">🏢 Business & Trade Association</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] text-slate-400 font-semibold mb-1">Upload CSV File or Paste Raw CSV Text</label>
                        <input type="file" id="b2b-csv-file" accept=".csv,.txt" class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 mb-2">
                        <textarea id="b2b-csv-text" rows="3" placeholder="Or paste CSV with headers: name, phone, address, city, rating..." class="w-full text-xs rounded-xl bg-slate-950 border border-slate-700 text-white p-2.5 font-mono focus:border-indigo-400 focus:ring-0"></textarea>
                    </div>

                    <button type="button" id="btn-import-csv" onclick="importB2bCsv()" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition-all">
                        <i data-lucide="upload-cloud" class="w-4 h-4"></i> Ingest & Validate Indian Numbers (+91)
                    </button>
                </div>

                {{-- Sub-tab 2: Live City Radar --}}
                <div id="b2b-tab-radar" class="space-y-3 hidden">
                    <p class="text-[11.5px] text-slate-300">
                        Query Google Places API to search verified businesses in any target Indian commercial hub.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block text-[11px] text-slate-400 font-semibold mb-1">Target City</label>
                            <input type="text" id="b2b-radar-city" placeholder="e.g. Surat, Ahmedabad, Jaipur" value="Surat" class="w-full text-xs rounded-xl bg-slate-950 border border-slate-700 text-white p-2.5 focus:border-indigo-400 focus:ring-0">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 font-semibold mb-1">Category Query</label>
                            <select id="b2b-radar-category" class="w-full text-xs rounded-xl bg-slate-950 border border-slate-700 text-white p-2.5 focus:border-indigo-400 focus:ring-0">
                                <option value="travel_agency">✈️ Tour and Travel Agencies</option>
                                <option value="astrologer">🔮 Astrologers & Vastu Consultants</option>
                                <option value="samagri_store">🪔 Puja Samagri Stores</option>
                                <option value="hotel">🏨 Dharamshalas and Hotels near Ghats</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" id="btn-radar-search" onclick="runGoogleMapsRadar()" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition-all">
                        <i data-lucide="radar" class="w-4 h-4"></i> Scan Google Maps Places & Ingest
                    </button>
                </div>
            </div>

            {{-- B2B Dev Deepawali Blast Center (Right 5 cols) --}}
            <div class="lg:col-span-5 bg-gradient-to-br from-amber-950/60 via-slate-900 to-slate-950 border border-amber-500/40 rounded-2xl p-5 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-amber-500/20">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🪔</span>
                            <h3 class="text-sm font-bold text-amber-200">B2B Dev Deepawali Campaign</h3>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">24 Nov 2026</span>
                    </div>

                    <div class="bg-black/30 rounded-xl p-3 border border-amber-500/20 text-[11px] space-y-1.5 text-amber-100/90">
                        <p class="font-bold text-amber-200">Commercial Partner Pitch Outline:</p>
                        <p>• <strong>The Problem:</strong> Hotels & boats in Kashi are 100% full. Their clients cannot travel.</p>
                        <p>• <strong>The Solution:</strong> Travel agency sells 108 Deepdaan on Manikarnika Ghat with client gotra.</p>
                        <p>• <strong>Fulfillment:</strong> Kashi Purohit performs puja, sends HD video + couriers Mahaprasad to client.</p>
                        <p>• <strong>Profit:</strong> Partner earns <strong>25% margin</strong> (₹300 - ₹600/booking).</p>
                    </div>

                    <div class="text-xs space-y-2">
                        <label class="block text-[11px] text-slate-300 font-semibold">Filter Partner Target City</label>
                        <select id="b2b-campaign-city" class="w-full text-xs rounded-xl bg-slate-950 border border-slate-700 text-white p-2 focus:border-amber-400 focus:ring-0">
                            <option value="all">🇮🇳 All Cities & Partners (National Blast)</option>
                            @if(isset($b2bCities))
                                @foreach($b2bCities as $bc)
                                    <option value="{{ $bc->city }}">{{ $bc->city }} ({{ $bc->total }} leads)</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="space-y-2 pt-4">
                    <button type="button" onclick="previewB2bPitch()" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-200 border border-amber-500/30 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                        <i data-lucide="eye" class="w-4 h-4"></i> Preview Sample Partner Pitch
                    </button>
                    <button type="button" id="btn-dispatch-b2b" onclick="dispatchB2bCampaign()" class="w-full py-3 rounded-xl bg-gradient-to-r from-amber-500 via-rose-600 to-red-600 hover:from-amber-600 hover:to-red-700 text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-lg transition-all">
                        <i data-lucide="send" class="w-4 h-4"></i> 🚀 Launch B2B Dev Deepawali Campaign
                    </button>
                </div>
            </div>

        </div>

        {{-- B2B Partners Directory Table --}}
        <div class="mt-8 bg-slate-950/70 border border-white/10 rounded-2xl p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-white/10">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">B2B Verified Leads Directory</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300">{{ count($b2bLeads ?? []) }} Displayed</span>
                </div>
                <div class="text-[11px] text-slate-400">
                    Each partner has an auto-generated unique booking code for 25% revenue share attribution.
                </div>
            </div>

            <div class="overflow-x-auto mt-3">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead>
                        <tr class="border-b border-white/10 text-slate-400 text-[11px] font-semibold">
                            <th class="py-2.5 px-3">Business Name</th>
                            <th class="py-2.5 px-3">Category</th>
                            <th class="py-2.5 px-3">City</th>
                            <th class="py-2.5 px-3">Phone / WhatsApp</th>
                            <th class="py-2.5 px-3">Google Rating</th>
                            <th class="py-2.5 px-3">Partner Code</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3 text-right">Quick Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @if(isset($b2bLeads) && count($b2bLeads) > 0)
                            @foreach($b2bLeads as $lead)
                                @php
                                    $catLabel = match($lead->category) {
                                        'travel_agency' => '✈️ Travel Agent',
                                        'astrologer' => '🔮 Astrologer',
                                        'samagri_store' => '🪔 Samagri Store',
                                        'hotel' => '🏨 Hotel/Ashram',
                                        default => '🏢 Corporate',
                                    };
                                    $statusBadge = match($lead->status) {
                                        'partner_onboarded' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
                                        'contacted' => 'bg-blue-500/20 text-blue-300 border-blue-500/40',
                                        default => 'bg-slate-500/20 text-slate-300 border-slate-500/40',
                                    };
                                @endphp
                                <tr class="hover:bg-white/5 transition-all">
                                    <td class="py-2.5 px-3 font-bold text-white flex items-center gap-1.5">
                                        {{ $lead->business_name }}
                                        @if($lead->contact_person)
                                            <span class="text-[10px] text-slate-400 font-normal">({{ $lead->contact_person }})</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3">{{ $catLabel }}</td>
                                    <td class="py-2.5 px-3 font-semibold text-amber-200">{{ $lead->city ?? 'India' }}</td>
                                    <td class="py-2.5 px-3 font-mono text-[11px]">{{ $lead->phone }}</td>
                                    <td class="py-2.5 px-3">
                                        @if($lead->google_rating)
                                            <span class="text-amber-300 font-bold">★ {{ $lead->google_rating }}</span>
                                            <span class="text-[10px] text-slate-500">({{ $lead->google_reviews_count }})</span>
                                        @else
                                            <span class="text-slate-600">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 font-mono text-[10.5px] text-indigo-300 font-bold">{{ $lead->partner_code }}</td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $statusBadge }}">
                                            {{ ucfirst($lead->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-right">
                                        <button type="button" onclick="previewB2bPitch({{ $lead->id }})" class="px-2.5 py-1 rounded bg-indigo-600/30 hover:bg-indigo-600 text-indigo-200 hover:text-white font-semibold text-[10.5px] transition-all">
                                            Pitch
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="py-6 text-center text-slate-400">
                                    No B2B leads in database yet. Click <strong>"Seed Premier Directory"</strong> or upload a Google Maps CSV above.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Interactive Omnichannel Campaign Studio & Live Device Preview --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Campaign Composer (Left 7 Cols) --}}
        <div class="lg:col-span-7 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Broadcast Campaign Studio</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Compose and blast devotional campaigns across WhatsApp & Brevo Email</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">Omnichannel</span>
            </div>

            <form id="form-broadcast" class="mt-5 space-y-4 text-xs" onsubmit="event.preventDefault();">
                @csrf
                {{-- Target Audience & Channel Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Target Devotee Audience</label>
                        <select id="camp-segment" class="w-full text-xs rounded-xl border-slate-300 focus:border-slate-800 focus:ring-0 p-2.5 bg-slate-50/50">
                            <option value="test">📱 Test to My Own WhatsApp & Email (Safe)</option>
                            <option value="all">👥 All Registered Devotees ({{ $totalDevotees }} contacts)</option>
                            <option value="bookers">🪔 Past Puja Bookers ({{ $totalOrders }} orders)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Dispatch Channels</label>
                        <select id="camp-channel" class="w-full text-xs rounded-xl border-slate-300 focus:border-slate-800 focus:ring-0 p-2.5 bg-slate-50/50">
                            <option value="both">⚡ Omnichannel (WhatsApp + Brevo Email)</option>
                            <option value="whatsapp">📱 WhatsApp Only (Evolution API)</option>
                            <option value="email">✉️ Email Only (Brevo REST API)</option>
                        </select>
                    </div>
                </div>

                {{-- Puja Quick Filler --}}
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Select Bookable Puja (Auto-Fills Details)</label>
                    <select id="camp-puja-preset" class="w-full text-xs rounded-xl border-slate-300 focus:border-slate-800 focus:ring-0 p-2.5 bg-slate-50/50" onchange="applyPujaPreset(this)">
                        <option value="">-- Choose a Puja to Promote --</option>
                        @foreach($pujas as $p)
                            <option value="{{ $p->id }}" 
                                    data-title="{{ $p->puja_title }}" 
                                    data-place="{{ $p->puja_place }}" 
                                    data-price="{{ $p->puja_price }}"
                                    data-slug="{{ $p->slug }}">
                                {{ $p->puja_title }} (₹{{ number_format($p->puja_price) }}{{ $p->puja_place ? ' • ' . $p->puja_place : '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Campaign Headline & Email Subject --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">WhatsApp Headline / Greeting</label>
                        <input type="text" id="camp-headline" class="w-full text-xs rounded-xl border-slate-300 focus:border-slate-800 focus:ring-0 p-2.5" 
                               value="🕉️ Special Rudrabhishek Puja Blessings from Varanasi" oninput="updateLivePreview()">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Email Subject Line</label>
                        <input type="text" id="camp-subject" class="w-full text-xs rounded-xl border-slate-300 focus:border-slate-800 focus:ring-0 p-2.5" 
                               value="Divine Blessings for You and Your Family — OnlinePuja.live">
                    </div>
                </div>

                {{-- Message Body --}}
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Message Body & Vedic Sankalp Details</label>
                    <textarea id="camp-body" rows="4" class="w-full text-xs rounded-xl border-slate-300 focus:border-slate-800 focus:ring-0 p-2.5" oninput="updateLivePreview()">Pranam Devotee!

Participate in the auspicious Vedic ritual performed by certified Varanasi pandits with your personal sankalp and gotra. Sanctified Ganga Jal, Bhasma & holy prasad will be blessed in your name and dispatched directly to your doorstep.

May Lord Shiva bestow divine health, peace, and abundance upon your household. 🙏✨</textarea>
                </div>

                {{-- Media Attachment (Reel / Poster) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1.5">Attach Media URL (Video Reel MP4 / Poster Image)</label>
                        <input type="url" id="camp-media-url" class="w-full text-xs rounded-xl border-slate-300 focus:border-slate-800 focus:ring-0 p-2.5" 
                               placeholder="https://.../video.mp4 or poster.jpg" oninput="updateLivePreview()">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Media Type</label>
                        <select id="camp-media-type" class="w-full text-xs rounded-xl border-slate-300 focus:border-slate-800 focus:ring-0 p-2.5" onchange="updateLivePreview()">
                            <option value="image">🖼️ Poster Image</option>
                            <option value="video">🎬 9:16 Video Reel</option>
                        </select>
                    </div>
                </div>

                {{-- Call To Action Button & Link --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Button Label</label>
                        <input type="text" id="camp-cta-text" class="w-full text-xs rounded-xl border-slate-300 focus:border-slate-800 focus:ring-0 p-2.5" 
                               value="Book Sacred Puja with Sankalp 🙏" oninput="updateLivePreview()">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Destination URL</label>
                        <input type="url" id="camp-cta-url" class="w-full text-xs rounded-xl border-slate-300 focus:border-slate-800 focus:ring-0 p-2.5" 
                               value="https://onlinepuja.live" oninput="updateLivePreview()">
                    </div>
                </div>

                {{-- Dispatch Actions --}}
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                    <button type="button" onclick="sendCampaign('test')" class="btn btn-sm btn-outline-secondary py-2.5 px-4 rounded-xl text-xs font-semibold flex items-center gap-1.5 hover:bg-slate-50">
                        <i data-lucide="smartphone" class="w-4 h-4 text-slate-600"></i> Send Test to Admin
                    </button>
                    <button type="button" onclick="sendCampaign('live')" class="btn btn-sm bg-slate-900 hover:bg-slate-800 text-white py-2.5 px-5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-md">
                        <i data-lucide="send" class="w-4 h-4 text-amber-400"></i> Launch Broadcast Campaign
                    </button>
                </div>
            </form>
        </div>

        {{-- Live Smartphone Preview Mockup (Right 5 Cols) --}}
        <div class="lg:col-span-5 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-sm flex flex-col items-center">
            <div class="w-full flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Live WhatsApp Preview</h4>
                <span class="text-[11px] text-slate-400">Recipient Device</span>
            </div>

            {{-- Phone Frame --}}
            <div class="w-full max-w-[320px] bg-slate-900 rounded-[36px] p-2.5 shadow-2xl border-4 border-slate-800">
                {{-- Screen --}}
                <div class="bg-[#efeae2] rounded-[28px] overflow-hidden text-slate-900 flex flex-col min-h-[460px]">
                    {{-- WhatsApp Header --}}
                    <div class="bg-[#075e54] text-white px-3 py-2.5 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-amber-400 text-slate-900 flex items-center justify-center font-bold text-xs">
                            🕉️
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1">
                                <span class="font-bold text-xs truncate">Online Puja</span>
                                <span class="text-emerald-300 text-[10px]">✓</span>
                            </div>
                            <p class="text-[9px] text-emerald-100 truncate">Official Verified Business</p>
                        </div>
                    </div>

                    {{-- Chat Bubble Area --}}
                    <div class="p-3 flex-1 flex flex-col justify-end space-y-2">
                        <div class="bg-white rounded-xl rounded-tl-none p-3 shadow-sm text-[11px] space-y-2 border border-slate-200/50">
                            {{-- Media Preview Thumbnail --}}
                            <div id="preview-media-container" class="rounded-lg overflow-hidden bg-slate-100 aspect-video flex items-center justify-center relative hidden">
                                <img id="preview-media-img" src="" class="w-full h-full object-cover hidden" alt="Campaign Preview">
                                <div id="preview-video-badge" class="absolute inset-0 bg-black/30 flex items-center justify-center text-white hidden">
                                    <div class="w-8 h-8 rounded-full bg-white/80 text-slate-900 flex items-center justify-center font-bold text-xs shadow-md">▶</div>
                                </div>
                            </div>

                            <p class="font-bold text-slate-900 leading-snug" id="preview-headline">🕉️ Special Rudrabhishek Puja Blessings from Varanasi</p>
                            
                            <p class="text-slate-700 whitespace-pre-line leading-relaxed text-[10.5px]" id="preview-body">
                                Pranam Devotee!

                                Participate in the auspicious Vedic ritual performed by certified Varanasi pandits with your personal sankalp and gotra.
                            </p>

                            <div class="pt-2 border-t border-slate-100 flex flex-col gap-1.5">
                                <div class="bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-center py-1.5 px-2 rounded-lg font-bold text-[10px] flex items-center justify-center gap-1 border border-emerald-200">
                                    <span id="preview-cta-text">Book Sacred Puja with Sankalp 🙏</span>
                                </div>
                            </div>

                            <div class="text-[9px] text-right text-slate-400 mt-1">11:45 AM • ✓✓</div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-slate-400 text-center mt-4">
                Rendered with high-contrast formatting, bold Unicode emojis & inline media.
            </p>
        </div>

    </div>

    {{-- Lifecycle Automated Triggers --}}
    <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Automated Devotee Lifecycle Journeys</h3>
                <p class="text-xs text-slate-500 mt-0.5">Always-on triggers that send personalized WhatsApp & Brevo email notifications</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200">4 Active Flows</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-5">
            {{-- Flow 1 --}}
            <div class="border border-slate-200/80 rounded-xl p-4 bg-slate-50/50 hover:bg-white hover:shadow-sm transition-all">
                <div class="flex items-center justify-between">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">1</span>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Auto-Active</span>
                </div>
                <h4 class="font-bold text-slate-900 text-xs mt-3">Welcome & ₹101 Sankalp</h4>
                <p class="text-[11px] text-slate-500 mt-1">Triggers when a devotee registers. Sends welcome email + WhatsApp ₹101 coupon code.</p>
                <div class="mt-3 text-[10px] text-slate-400 font-mono">Coupon: SANKALP101</div>
            </div>

            {{-- Flow 2 --}}
            <div class="border border-slate-200/80 rounded-xl p-4 bg-slate-50/50 hover:bg-white hover:shadow-sm transition-all">
                <div class="flex items-center justify-between">
                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-xs">2</span>
                    <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">Auto-Active</span>
                </div>
                <h4 class="font-bold text-slate-900 text-xs mt-3">Puja Booking Confirmation</h4>
                <p class="text-[11px] text-slate-500 mt-1">Triggers immediately after checkout. Sends Order ID, Pandit details, and ritual timing guide.</p>
                <div class="mt-3 text-[10px] text-slate-400 font-mono">WhatsApp + Brevo HTML</div>
            </div>

            {{-- Flow 3 --}}
            <div class="border border-slate-200/80 rounded-xl p-4 bg-slate-50/50 hover:bg-white hover:shadow-sm transition-all">
                <div class="flex items-center justify-between">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs">3</span>
                    <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">Auto-Active</span>
                </div>
                <h4 class="font-bold text-slate-900 text-xs mt-3">Prasad Dispatch & Tracking</h4>
                <p class="text-[11px] text-slate-500 mt-1">Triggers when prasad is packed in Varanasi. Sends India Post / courier tracking link to WhatsApp.</p>
                <div class="mt-3 text-[10px] text-slate-400 font-mono">WhatsApp Tracking</div>
            </div>

            {{-- Flow 4 --}}
            <div class="border border-slate-200/80 rounded-xl p-4 bg-slate-50/50 hover:bg-white hover:shadow-sm transition-all">
                <div class="flex items-center justify-between">
                    <span class="w-7 h-7 rounded-lg bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-xs">4</span>
                    <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200">Auto-Active</span>
                </div>
                <h4 class="font-bold text-slate-900 text-xs mt-3">Tithi & Muhurat Nudges</h4>
                <p class="text-[11px] text-slate-500 mt-1">Panchang-driven broadcast before Ekadashi, Pradosh, and Shivratri with recommended pujas.</p>
                <div class="mt-3 text-[10px] text-slate-400 font-mono">Hindu Calendar Sync</div>
            </div>
        </div>
    </div>

</div>

{{-- WhatsApp QR Modal --}}
<div id="modal-wa-qr" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-center">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center font-bold mb-3">
            <i data-lucide="qr-code" class="w-6 h-6"></i>
        </div>
        <h3 class="text-base font-bold text-slate-900">Scan WhatsApp QR Code</h3>
        <p class="text-xs text-slate-500 mt-1">Open WhatsApp on your phone > Linked Devices > Link a Device</p>

        <div id="wa-qr-container" class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-center min-h-[220px]">
            <div class="text-xs text-slate-400 animate-pulse">Fetching live QR code…</div>
        </div>

        <div class="mt-4 flex items-center gap-2">
            <button type="button" onclick="fetchWaQr()" class="flex-1 btn btn-sm btn-outline-secondary py-2 text-xs rounded-xl">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1"></i> Refresh QR
            </button>
            <button type="button" onclick="closeWaQrModal()" class="btn btn-sm bg-slate-900 text-white py-2 px-4 text-xs rounded-xl">
                Done
            </button>
        </div>
    </div>
</div>

<script>
function updateLivePreview() {
    var headline = document.getElementById('camp-headline').value;
    var body = document.getElementById('camp-body').value;
    var ctaText = document.getElementById('camp-cta-text').value;
    var mediaUrl = document.getElementById('camp-media-url').value;
    var mediaType = document.getElementById('camp-media-type').value;

    document.getElementById('preview-headline').innerText = headline || 'Devotional Headline';
    document.getElementById('preview-body').innerText = body || '';
    document.getElementById('preview-cta-text').innerText = ctaText || 'View Details';

    var mediaBox = document.getElementById('preview-media-container');
    var mediaImg = document.getElementById('preview-media-img');
    var videoBadge = document.getElementById('preview-video-badge');

    if (mediaUrl) {
        mediaBox.classList.remove('hidden');
        mediaImg.src = mediaUrl;
        mediaImg.classList.remove('hidden');
        if (mediaType === 'video') {
            videoBadge.classList.remove('hidden');
        } else {
            videoBadge.classList.add('hidden');
        }
    } else {
        mediaBox.classList.add('hidden');
    }
}

function applyPujaPreset(sel) {
    var opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;

    var title = opt.getAttribute('data-title');
    var place = opt.getAttribute('data-place');
    var price = opt.getAttribute('data-price');
    var slug = opt.getAttribute('data-slug');

    document.getElementById('camp-headline').value = '🕉️ Sacred ' + title + (place ? ' in ' + place : '');
    document.getElementById('camp-subject').value = 'Divine Blessings: Book ' + title + ' with Personal Sankalp';
    document.getElementById('camp-body').value = 'Pranam Devotee!\n\nPerform sacred ' + title + ' with authentic Vedic rituals and personal sankalp. Receive divine prasad from Varanasi at your doorstep!\n\nDakshina: ₹' + price + ' with full Vedic samagri included.';
    document.getElementById('camp-cta-text').value = 'Book ' + title + ' 🙏';
    document.getElementById('camp-cta-url').value = 'https://onlinepuja.live/puja/' + (slug || 'list');

    updateLivePreview();
}

function loadFestivalCampaign(festivalName) {
    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ action: 'preview_festival_campaign', festival: festivalName })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.ok && res.package) {
            var pkg = res.package;
            document.getElementById('camp-headline').value = '🪔 ' + (pkg.branding ? pkg.branding.title : pkg.festival);
            document.getElementById('camp-subject').value = pkg.email_subject || ('Sacred Invitation: ' + pkg.festival);
            document.getElementById('camp-body').value = pkg.whatsapp_copy || '';
            document.getElementById('camp-cta-text').value = 'Book Sacred Sankalp 🙏';
            document.getElementById('camp-cta-url').value = pkg.booking_url || 'https://onlinepuja.live/pujas';
            document.getElementById('camp-channel').value = 'both';

            updateLivePreview();
            var comp = document.getElementById('form-broadcast');
            if (comp) comp.scrollIntoView({ behavior: 'smooth' });
        } else {
            alert('Failed to load campaign: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(function(err) {
        alert('Network error: ' + err.message);
    });
}

function generateFestivalReel(festivalName) {
    var btn = document.getElementById('btn-gen-dev-reel');
    var origText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin">⏳</span> Synthesizing 1080x1920 MP4 Reel (FFmpeg)...';
    }

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ action: 'generate_festival_reel', festival: festivalName })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = origText;
        }
        if (res && res.ok && res.video_url) {
            document.getElementById('camp-media-url').value = res.video_url;
            document.getElementById('camp-media-type').value = 'video';
            updateLivePreview();
            alert('🎉 Video Reel Synthesized Successfully!\n\nURL: ' + res.video_url);
        } else {
            alert('Video generation note: ' + (res.message || 'Check logs for status'));
        }
    })
    .catch(function(err) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = origText;
        }
        alert('Error: ' + err.message);
    });
}

function quickLaunchFestivalCampaign(festivalName) {
    if (!confirm('Launch multi-channel broadcast for ' + festivalName + ' across WhatsApp, Brevo Email, and Push notifications?')) {
        return;
    }

    var btn = document.getElementById('btn-quick-launch-dev');
    var origText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin">🚀</span> Broadcasting Multi-Channel Campaign...';
    }

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            action: 'launch_festival_campaign',
            festival: festivalName,
            channels: ['whatsapp', 'email', 'push']
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = origText;
        }
        if (res && res.ok) {
            alert('✨ Multi-Channel Campaign Launched!\n\n' + res.message);
        } else {
            alert('Launch failed: ' + (res.message || 'Error'));
        }
    })
    .catch(function(err) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = origText;
        }
        alert('Network Error: ' + err.message);
    });
}

function sendCampaign(mode) {
    var segment = mode === 'test' ? 'test' : document.getElementById('camp-segment').value;
    var channel = document.getElementById('camp-channel').value;
    var headline = document.getElementById('camp-headline').value;
    var subject = document.getElementById('camp-subject').value;
    var body = document.getElementById('camp-body').value;
    var mediaUrl = document.getElementById('camp-media-url').value;
    var mediaType = document.getElementById('camp-media-type').value;
    var ctaText = document.getElementById('camp-cta-text').value;
    var ctaUrl = document.getElementById('camp-cta-url').value;

    window.executeAction('send_devotee_broadcast', null, {
        segment: segment,
        channel: channel,
        headline: headline,
        subject: subject,
        body: body,
        media_url: mediaUrl,
        media_type: mediaType,
        cta_text: ctaText,
        cta_url: ctaUrl
    });
}

function testWhatsAppPing() {
    window.executeAction('test_whatsapp', null);
}

function testBrevoEmail() {
    window.executeAction('test_brevo', null);
}

function syncDevoteesToBrevo() {
    window.executeAction('sync_brevo_contacts', null);
}

function switchB2bTab(tab) {
    var csvTab = document.getElementById('b2b-tab-csv');
    var radarTab = document.getElementById('b2b-tab-radar');
    var btnCsv = document.getElementById('tab-btn-csv');
    var btnRadar = document.getElementById('tab-btn-radar');

    if (tab === 'csv') {
        csvTab.classList.remove('hidden');
        radarTab.classList.add('hidden');
        btnCsv.className = 'px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-bold transition-all';
        btnRadar.className = 'px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 font-semibold transition-all';
    } else {
        radarTab.classList.remove('hidden');
        csvTab.classList.add('hidden');
        btnRadar.className = 'px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-bold transition-all';
        btnCsv.className = 'px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 font-semibold transition-all';
    }
}

function importB2bCsv() {
    var fileInput = document.getElementById('b2b-csv-file');
    var textInput = document.getElementById('b2b-csv-text').value;
    var defaultCity = document.getElementById('b2b-csv-city').value;
    var defaultCat = document.getElementById('b2b-csv-category').value;
    var btn = document.getElementById('btn-import-csv');

    if (!fileInput.files.length && !textInput.trim()) {
        alert('Please choose a CSV file or paste CSV text from your Google Maps scraper.');
        return;
    }

    var formData = new FormData();
    formData.append('action', 'import_b2b_csv');
    formData.append('default_city', defaultCity);
    formData.append('default_category', defaultCat);
    formData.append('_token', '{{ csrf_token() }}');

    if (fileInput.files.length) {
        formData.append('csv_file', fileInput.files[0]);
    } else {
        formData.append('csv_data', textInput);
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin">⏳</span> Ingesting Google Maps Leads...';

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="upload-cloud" class="w-4 h-4"></i> Ingest & Validate Indian Numbers (+91)';
        if (res && res.ok) {
            alert('🎉 ' + res.message);
            location.reload();
        } else {
            alert('Import note: ' + (res.error || res.message || 'Check file format'));
        }
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="upload-cloud" class="w-4 h-4"></i> Ingest & Validate Indian Numbers (+91)';
        alert('Error: ' + err.message);
    });
}

function runGoogleMapsRadar() {
    var city = document.getElementById('b2b-radar-city').value;
    var category = document.getElementById('b2b-radar-category').value;
    var btn = document.getElementById('btn-radar-search');

    if (!city) {
        alert('Please enter a target Indian city (e.g. Surat, Mumbai, Ahmedabad).');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin">🔍</span> Scanning Google Places Radar for ' + city + '...';

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            action: 'radar_google_places',
            city: city,
            category: category
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="radar" class="w-4 h-4"></i> Scan Google Maps Places & Ingest';
        if (res && res.ok) {
            alert('✨ ' + res.message);
            location.reload();
        } else {
            alert('Radar note: ' + (res.error || res.message));
        }
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="radar" class="w-4 h-4"></i> Scan Google Maps Places & Ingest';
        alert('Error: ' + err.message);
    });
}

function seedB2bDirectory() {
    if (!confirm('Seed premier national B2B directory across Mumbai, Surat, Ahmedabad, Delhi, Kolkata & Varanasi?')) {
        return;
    }

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ action: 'seed_b2b_directory' })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        alert('✨ ' + (res.message || 'Seeded successfully!'));
        location.reload();
    })
    .catch(function(err) {
        alert('Error: ' + err.message);
    });
}

function previewB2bPitch(leadId) {
    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ action: 'preview_b2b_pitch', lead_id: leadId })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.ok && res.pitch) {
            var p = res.pitch;
            document.getElementById('camp-headline').value = '🤝 ' + p.business_name + ' × OnlinePuja Dev Deepawali Partnership';
            document.getElementById('camp-subject').value = p.email_subject;
            document.getElementById('camp-body').value = p.whatsapp_copy;
            document.getElementById('camp-cta-text').value = 'View B2B Partner Portal 📊';
            document.getElementById('camp-cta-url').value = p.booking_url;
            document.getElementById('camp-media-url').value = p.video_url;
            document.getElementById('camp-media-type').value = 'video';
            document.getElementById('camp-channel').value = 'both';

            updateLivePreview();
            var comp = document.getElementById('form-broadcast');
            if (comp) comp.scrollIntoView({ behavior: 'smooth' });
        } else {
            alert('Could not preview pitch: ' + (res.error || 'Unknown error'));
        }
    })
    .catch(function(err) {
        alert('Network error: ' + err.message);
    });
}

function dispatchB2bCampaign() {
    var city = document.getElementById('b2b-campaign-city').value;
    var cityLabel = city === 'all' ? 'All National Partners' : city;

    if (!confirm('Broadcast B2B Dev Deepawali 2026 Campaign (WhatsApp Reel + Proposal Email) to: ' + cityLabel + '?')) {
        return;
    }

    var btn = document.getElementById('btn-dispatch-b2b');
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin">🚀</span> Broadcasting B2B Proposals...';

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            action: 'dispatch_b2b_campaign',
            city: city,
            channels: ['whatsapp', 'email']
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="send" class="w-4 h-4"></i> 🚀 Launch B2B Dev Deepawali Campaign';
        if (res && res.ok) {
            alert('🎉 ' + res.message + '\n\nWhatsApp Sent: ' + (res.results ? res.results.whatsapp.sent : 0) + '\nEmail Sent: ' + (res.results ? res.results.email.sent : 0));
            location.reload();
        } else {
            alert('Dispatch failed: ' + (res.message || res.error || 'Error'));
        }
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="send" class="w-4 h-4"></i> 🚀 Launch B2B Dev Deepawali Campaign';
        alert('Network Error: ' + err.message);
    });
}

function openWaQrModal() {
    document.getElementById('modal-wa-qr').classList.remove('hidden');
    fetchWaQr();
}

function closeWaQrModal() {
    document.getElementById('modal-wa-qr').classList.add('hidden');
}

function fetchWaQr() {
    var box = document.getElementById('wa-qr-container');
    box.innerHTML = '<div class="text-xs text-slate-400 animate-pulse">Requesting QR from Evolution API…</div>';

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ action: 'get_wa_qr' })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.base64) {
            box.innerHTML = '<img src="' + res.base64 + '" class="w-48 h-48 rounded-lg shadow-sm mx-auto" alt="WhatsApp QR Code">';
        } else if (res && res.code) {
            box.innerHTML = '<div class="p-3 bg-emerald-50 text-emerald-800 text-xs rounded-lg font-semibold">Instance is already paired and active!</div>';
        } else {
            box.innerHTML = '<div class="text-xs text-rose-500 font-semibold">' + (res.error || 'Failed to fetch QR') + '</div>';
        }
    })
    .catch(function(err) {
        box.innerHTML = '<div class="text-xs text-rose-500">Error: ' + err.message + '</div>';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    updateLivePreview();
});
</script>
@endsection
