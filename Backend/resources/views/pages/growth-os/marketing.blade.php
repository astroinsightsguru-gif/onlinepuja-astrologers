@extends('pages.growth-os.layout')

@section('growth_content')
<div class="space-y-6">

    {{-- Top Action Header & AI Vault Navigation Ribbon --}}
    <div class="bg-white border-2 border-slate-200 rounded-2xl p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200 mb-1.5">
                <span>🕉️</span> Autonomous Marketing & Festival Campaign Nerve Center
            </div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">
                Festival Multi-Campaigns, Content Studio & B2B Distribution Hub
            </h1>
            <p class="text-xs text-slate-600 mt-0.5">
                Orchestrate Dev Deepawali & parallel Hindu festivals with 1080×1920 MP4 Video Reels, SEO longform guides, and national B2B partner distribution.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Gateway Status Badges --}}
            @php
                $waStateVal = $waState['state'] ?? ($waState['instance']['state'] ?? 'connecting');
                $isWaConnected = ($waStateVal === 'open');
            @endphp
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold {{ $isWaConnected ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-amber-100 text-amber-900 border border-amber-300' }}">
                <span class="w-2 h-2 rounded-full {{ $isWaConnected ? 'bg-emerald-600 animate-pulse' : 'bg-amber-600' }}"></span>
                WhatsApp: {{ ucfirst($waStateVal) }}
            </div>

            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold {{ $brevoConfigured ? 'bg-blue-100 text-blue-900 border border-blue-300' : 'bg-rose-100 text-rose-900 border border-rose-300' }}">
                <span class="w-2 h-2 rounded-full {{ $brevoConfigured ? 'bg-blue-600' : 'bg-rose-600' }}"></span>
                Brevo: {{ $brevoConfigured ? 'Connected' : 'Missing Key' }}
            </div>

            <a href="{{ route('growth-os.ai') }}#vault" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-amber-300 border border-amber-400/40 shadow-sm flex items-center gap-1.5 transition-all">
                <span>⚙️</span> Manage Gateways in AI Vault ↗
            </a>
        </div>
    </div>

    {{-- Audience Metrics Strip --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border-2 border-slate-200/90 rounded-2xl p-4 flex items-center gap-3.5 shadow-xs">
            <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-slate-800">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Devotees</p>
                <h4 class="text-xl font-black text-slate-900">{{ number_format($totalDevotees) }}</h4>
            </div>
        </div>

        <div class="bg-white border-2 border-slate-200/90 rounded-2xl p-4 flex items-center gap-3.5 shadow-xs">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700">
                <i data-lucide="phone" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">WhatsApp Reachable</p>
                <h4 class="text-xl font-black text-slate-900">{{ number_format($devoteesWithPhone) }}</h4>
            </div>
        </div>

        <div class="bg-white border-2 border-slate-200/90 rounded-2xl p-4 flex items-center gap-3.5 shadow-xs">
            <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-700">
                <i data-lucide="mail-check" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-blue-700 uppercase tracking-wider">Email Reachable</p>
                <h4 class="text-xl font-black text-slate-900">{{ number_format($devoteesWithEmail) }}</h4>
            </div>
        </div>

        <div class="bg-white border-2 border-slate-200/90 rounded-2xl p-4 flex items-center gap-3.5 shadow-xs">
            <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700">
                <i data-lucide="flame" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Bookable Pujas</p>
                <h4 class="text-xl font-black text-slate-900">{{ count($pujas) }} Active</h4>
            </div>
        </div>
    </div>

    {{-- 🪔 Indian Festivals Radar & Autonomous Multi-Channel Cockpit --}}
    <div class="bg-gradient-to-br from-amber-500/10 via-rose-500/5 to-orange-500/10 border-2 border-amber-300 rounded-3xl p-6 shadow-xs relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl select-none pointer-events-none">
            🪔
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-amber-200/70">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black bg-amber-500/20 text-amber-950 mb-2 border border-amber-400/50">
                    <span>🕉️</span> Autonomous Festival Campaign Radar
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Upcoming Indian Festivals & Multi-Channel Campaigns</h2>
                <p class="text-xs text-slate-700 mt-1 max-w-3xl leading-relaxed">
                    Detects approaching Hindu festivals, synthesizes 1080×1920 MP4 Video Reels, generates branded promotional banners, publishes in-depth SEO guides, and broadcasts simultaneously across WhatsApp, Brevo Email, and Web/App Push.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="loadFestivalCampaign('Dev Deepawali Varanasi 2026')" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-600 to-rose-600 hover:from-amber-700 hover:to-rose-700 text-white shadow-md flex items-center gap-2 transition-all">
                    <span>🪔</span> Dev Deepawali Spotlight
                </button>
            </div>
        </div>

        {{-- Dev Deepawali Spotlight Banner --}}
        <div class="mt-5 bg-slate-950 text-white rounded-3xl p-6 lg:p-7 shadow-xl border-2 border-amber-500/50 relative overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <div class="lg:col-span-7 space-y-3.5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black bg-amber-400 text-slate-950 uppercase tracking-wider">
                        ✨ Next Major Varanasi Pilgrimage Event • Tuesday, 24 Nov 2026
                    </div>
                    <h2 class="text-2xl lg:text-3xl font-black text-amber-300 tracking-tight leading-snug">
                        Dev Deepawali & Maha Deepdaan 2026 (Kartik Purnima)
                    </h2>
                    <p class="text-xs lg:text-sm text-slate-100 leading-relaxed">
                        On <strong>Tuesday, 24 November 2026</strong>, all 84 Ghats of Varanasi illuminate with 10+ Lakh earthen lamps as the gods descend to bathe in the holy Ganga. Devotees worldwide can offer 108 Deepdaan at Dashashwamedh & Manikarnika Ghats with certified Purohits chanting their family Gotra Sankalp.
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px]">
                        <span class="px-3 py-1.5 rounded-xl bg-emerald-950 text-emerald-300 border border-emerald-500/60 font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> WhatsApp: 1080x1920 Reel + Copy Ready
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-blue-950 text-blue-300 border border-blue-500/60 font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-400"></span> Brevo Email: Responsive HTML Ready
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-purple-950 text-purple-300 border border-purple-500/60 font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-purple-400"></span> Push: Broadcast Ready
                        </span>
                    </div>
                </div>

                <div class="lg:col-span-5 flex flex-col gap-2.5 bg-slate-900/90 p-4 rounded-2xl border border-amber-500/40">
                    <div class="text-center pb-2 border-b border-amber-500/20">
                        <span class="text-[11px] text-amber-300 uppercase tracking-wider font-extrabold">Autonomous Brain Studio</span>
                    </div>
                    <button type="button" id="btn-master-orchestrate" onclick="orchestrateMasterCampaign('Dev Deepawali Varanasi 2026')" class="w-full py-3 px-3 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-rose-500 hover:from-amber-300 hover:to-rose-400 text-slate-950 font-black text-xs flex items-center justify-center gap-2 transition-all shadow-lg animate-pulse">
                        <i data-lucide="sparkles" class="w-4 h-4"></i> 🚀 1-Tap Full Autonomous Campaign
                    </button>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" id="btn-gen-banners" onclick="generateFestivalBanners('Dev Deepawali & Deepdaan Varanasi 2026')" class="py-2.5 px-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-amber-300 border border-amber-500/40 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                            <i data-lucide="image" class="w-3.5 h-3.5"></i> Banners (1200 & 1080)
                        </button>
                        <button type="button" id="btn-publish-blog" onclick="publishFestivalBlog('Dev Deepawali Varanasi 2026')" class="py-2.5 px-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-emerald-300 border border-emerald-500/40 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Publish SEO Blog
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" id="btn-gen-dev-reel" onclick="generateFestivalReel('Dev Deepawali Varanasi 2026')" class="py-2.5 px-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-purple-300 border border-purple-500/40 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                            <i data-lucide="film" class="w-3.5 h-3.5"></i> 1080x1920 Reel
                        </button>
                        <button type="button" onclick="loadFestivalCampaign('Dev Deepawali Varanasi 2026')" class="py-2.5 px-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-white border border-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Load Composer
                        </button>
                    </div>
                    <button type="button" id="btn-quick-launch-dev" onclick="quickLaunchFestivalCampaign('Dev Deepawali Varanasi 2026')" class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-black text-xs flex items-center justify-center gap-2 transition-all shadow-md">
                        <i data-lucide="zap" class="w-4 h-4"></i> Broadcast (WhatsApp + Email + Push)
                    </button>

                    {{-- Dynamic Asset Dock --}}
                    <div id="master-asset-dock" class="mt-2 pt-2 border-t border-amber-500/20 text-xs space-y-2 hidden">
                        <p class="font-extrabold text-amber-300 uppercase text-[11px]">✨ Fresh Campaign Assets Live:</p>
                        <div class="flex items-center justify-between bg-black/60 p-2.5 rounded-xl border border-slate-800">
                            <span class="text-slate-200">Live SEO Blog:</span>
                            <a id="dock-blog-link" href="#" target="_blank" class="text-emerald-400 hover:underline font-bold">View Article ↗</a>
                        </div>
                        <div class="flex items-center justify-between bg-black/60 p-2.5 rounded-xl border border-slate-800">
                            <span class="text-slate-200">1200x630 Banner:</span>
                            <a id="dock-banner-link" href="#" target="_blank" class="text-amber-400 hover:underline font-bold">View Image ↗</a>
                        </div>
                        <div class="flex items-center justify-between bg-black/60 p-2.5 rounded-xl border border-slate-800">
                            <span class="text-slate-200">1080x1920 Reel:</span>
                            <a id="dock-reel-link" href="#" target="_blank" class="text-purple-400 hover:underline font-bold">Watch Video ↗</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upcoming Festivals Parallel Grid --}}
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span>🗓️</span> Upcoming Sacred Festival Calendar (Parallel Multi-Campaign Engine)
                </h3>
                <button type="button" onclick="openScheduleModal()" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-300 border border-amber-400/40 text-xs font-bold flex items-center gap-1.5 transition-all shadow-sm">
                    <i data-lucide="calendar-plus" class="w-4 h-4"></i> ➕ Schedule New Event / Festival
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @if(isset($festivalsRadar) && count($festivalsRadar) > 0)
                    @foreach($festivalsRadar as $fest)
                        @php
                            $badgeColor = match($fest['milestone']) {
                                't_minus_1', 'day_0' => 'bg-rose-100 text-rose-900 border-rose-300 animate-pulse',
                                't_minus_3' => 'bg-amber-100 text-amber-900 border-amber-300 font-bold',
                                't_minus_7' => 'bg-blue-100 text-blue-900 border-blue-300 font-bold',
                                't_minus_14' => 'bg-emerald-100 text-emerald-900 border-emerald-300 font-bold',
                                default => 'bg-slate-100 text-slate-800 border-slate-300 font-semibold',
                            };
                        @endphp
                        <div class="bg-white border-2 border-slate-200 hover:border-amber-400 rounded-2xl p-4 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                                    <span class="text-xs font-bold text-slate-700">{{ $fest['formatted_date'] }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] border {{ $badgeColor }}">
                                        {{ $fest['days_left'] }}d left
                                    </span>
                                </div>
                                <h4 class="text-base font-black text-slate-900 mt-2.5">{{ $fest['name'] }}</h4>
                                <p class="text-xs text-slate-600 mt-1">Deity: <span class="font-bold text-slate-900">{{ $fest['deity'] ?? 'Vedic Devata' }}</span></p>

                                @if(!empty($fest['puja']))
                                    <div class="mt-2.5 text-xs bg-amber-50 border border-amber-200 rounded-xl p-2.5 text-amber-950 font-bold truncate" title="{{ $fest['puja']['title'] }}">
                                        🪔 {{ $fest['puja']['title'] }}
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2">
                                <button type="button" onclick="loadFestivalCampaign('{{ addslashes($fest['name']) }}')" class="py-2 px-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold text-center transition-all">
                                    Composer
                                </button>
                                <button type="button" onclick="orchestrateMasterCampaign('{{ addslashes($fest['name']) }}')" class="py-2 px-2 bg-gradient-to-r from-amber-500 to-rose-600 hover:from-amber-400 hover:to-rose-400 text-slate-950 rounded-xl text-xs font-black text-center transition-all shadow-sm">
                                    ⚡ 1-Tap Brain
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full py-6 text-center text-xs text-slate-500 bg-white rounded-2xl border border-slate-200">
                        No upcoming festivals found in the next 60 days. Run <code>php artisan brain:seed-festivals</code> to refresh.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 🤝 B2B Partner Network & Google Maps Lead Automation Hub --}}
    <div class="bg-slate-900 text-white rounded-3xl p-6 lg:p-8 shadow-xl border border-slate-700 relative overflow-hidden">
        <div class="absolute -right-12 -top-12 opacity-10 text-9xl select-none pointer-events-none">
            🤝
        </div>

        {{-- B2B Header & Metrics --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-700">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-400/40 mb-2">
                    <span>💼</span> B2B Partner Distribution Engine
                </div>
                <h2 class="text-2xl font-black text-white tracking-tight">
                    Promote Dev Deepawali 2026 & Varanasi Pujas via National B2B Partners
                </h2>
                <p class="text-xs text-slate-200 mt-1 max-w-3xl leading-relaxed">
                    Partner with Travel Agencies, Pilgrimage Tour Operators, Astrologers, and Samagri Stores across Mumbai, Surat, Ahmedabad, Delhi, Kolkata, and Varanasi. They sell your Kashi Vedic Pujas to their clients with zero logistics, and earn a lucrative <strong class="text-emerald-300 font-bold">25% Revenue Share</strong> per booking.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="seedB2bDirectory()" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white border border-indigo-400/40 shadow-md flex items-center gap-1.5 transition-all">
                    <span>⚡</span> Seed Premier Directory
                </button>
            </div>
        </div>

        {{-- B2B Stat Counters --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 my-6">
            <div class="bg-slate-800/90 border border-slate-700 rounded-2xl p-4">
                <p class="text-xs font-bold text-indigo-300 uppercase tracking-wider">B2B Partners in Network</p>
                <h4 class="text-2xl font-black text-white mt-1">{{ number_format($totalB2bPartners ?? 0) }}</h4>
                <p class="text-xs text-slate-300 mt-0.5">Travel agents & astrologers</p>
            </div>
            <div class="bg-slate-800/90 border border-slate-700 rounded-2xl p-4">
                <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Partner Margin / Revenue Share</p>
                <h4 class="text-2xl font-black text-emerald-300 mt-1">25% Net</h4>
                <p class="text-xs text-slate-300 mt-0.5">₹300 - ₹600 profit / booking</p>
            </div>
            <div class="bg-slate-800/90 border border-slate-700 rounded-2xl p-4">
                <p class="text-xs font-bold text-amber-300 uppercase tracking-wider">Top Indian Hubs</p>
                <h4 class="text-xl font-bold text-amber-200 mt-1 truncate">
                    {{ isset($b2bCities) && count($b2bCities) > 0 ? $b2bCities->take(3)->pluck('city')->implode(', ') : 'Mumbai, Surat, Delhi' }}
                </h4>
                <p class="text-xs text-slate-300 mt-0.5">{{ count($b2bCities ?? []) }} cities active</p>
            </div>
            <div class="bg-slate-800/90 border border-slate-700 rounded-2xl p-4">
                <p class="text-xs font-bold text-purple-300 uppercase tracking-wider">Dev Deepawali Target</p>
                <h4 class="text-2xl font-black text-purple-300 mt-1">₹5,00,000+</h4>
                <p class="text-xs text-slate-300 mt-0.5">1,000 partner bookings goal</p>
            </div>
        </div>

        {{-- B2B Actions Grid: Google Maps Importer & Live Radar --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">
            
            {{-- Google Maps Lead Ingestion (Left 7 cols) --}}
            <div class="lg:col-span-7 bg-slate-950/80 border border-slate-700 rounded-2xl p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📍</span>
                        <div>
                            <h3 class="text-sm font-bold text-white">Google Maps Business Lead Ingestion</h3>
                            <p class="text-xs text-slate-300">Import CSV from Google Maps scraper tools or search via Live Radar</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/40">Auto +91 Cleaner</span>
                </div>

                {{-- Sub-tabs --}}
                <div class="flex items-center gap-2 border-b border-slate-700 pb-2 text-xs">
                    <button type="button" onclick="switchB2bTab('csv')" id="tab-btn-csv" class="px-3.5 py-1.5 rounded-lg bg-indigo-600 text-white font-bold transition-all">
                        📁 CSV / Excel Upload
                    </button>
                    <button type="button" onclick="switchB2bTab('radar')" id="tab-btn-radar" class="px-3.5 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-200 font-semibold transition-all">
                        🔍 Live City Radar
                    </button>
                </div>

                {{-- Sub-tab 1: CSV Upload --}}
                <div id="b2b-tab-csv" class="space-y-3">
                    <p class="text-xs text-slate-200">
                        Upload leads scraped from Google Maps (compatible with <strong>Outscraper, Apify, Instant Data Scraper, PhantomBuster</strong>). The engine automatically detects business name, phone, address, and Google rating.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block text-xs text-slate-200 font-bold mb-1">Default City</label>
                            <input type="text" id="b2b-csv-city" placeholder="e.g. Mumbai, Surat, Delhi" value="Mumbai" class="w-full text-xs rounded-xl bg-slate-900 border border-slate-600 text-white p-2.5 focus:border-amber-400 focus:ring-0">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-200 font-bold mb-1">Category</label>
                            <select id="b2b-csv-category" class="w-full text-xs rounded-xl bg-slate-900 border border-slate-600 text-white p-2.5 focus:border-amber-400 focus:ring-0">
                                <option value="travel_agency">✈️ Travel Agency / Tour Operator</option>
                                <option value="astrologer">🔮 Astrologer / Jyotish Kendra</option>
                                <option value="samagri_store">🪔 Puja Samagri / Spiritual Store</option>
                                <option value="hotel">🏨 Hotels & Dharamshalas</option>
                                <option value="corporate">🏢 Business & Trade Association</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-200 font-bold mb-1">Upload CSV File or Paste Raw CSV Text</label>
                        <input type="file" id="b2b-csv-file" accept=".csv,.txt" class="block w-full text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 mb-2">
                        <textarea id="b2b-csv-text" rows="3" placeholder="Or paste CSV with headers: name, phone, address, city, rating..." class="w-full text-xs rounded-xl bg-slate-900 border border-slate-600 text-white p-2.5 font-mono focus:border-amber-400 focus:ring-0"></textarea>
                    </div>

                    <button type="button" id="btn-import-csv" onclick="importB2bCsv()" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition-all">
                        <i data-lucide="upload-cloud" class="w-4 h-4"></i> Ingest & Validate Indian Numbers (+91)
                    </button>
                </div>

                {{-- Sub-tab 2: Live City Radar --}}
                <div id="b2b-tab-radar" class="space-y-3 hidden">
                    <p class="text-xs text-slate-200">
                        Query Google Places API to search verified businesses in any target Indian commercial hub.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block text-xs text-slate-200 font-bold mb-1">Target City</label>
                            <input type="text" id="b2b-radar-city" placeholder="e.g. Surat, Ahmedabad, Jaipur" value="Surat" class="w-full text-xs rounded-xl bg-slate-900 border border-slate-600 text-white p-2.5 focus:border-amber-400 focus:ring-0">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-200 font-bold mb-1">Category Query</label>
                            <select id="b2b-radar-category" class="w-full text-xs rounded-xl bg-slate-900 border border-slate-600 text-white p-2.5 focus:border-amber-400 focus:ring-0">
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
            <div class="lg:col-span-5 bg-gradient-to-br from-amber-950/70 via-slate-900 to-slate-950 border border-amber-500/40 rounded-2xl p-5 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-amber-500/20">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🪔</span>
                            <h3 class="text-sm font-bold text-amber-200">B2B Dev Deepawali Campaign</h3>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40">24 Nov 2026</span>
                    </div>

                    <div class="bg-black/50 rounded-xl p-3.5 border border-amber-500/30 text-xs space-y-1.5 text-amber-100">
                        <p class="font-extrabold text-amber-300">Commercial Partner Pitch Outline:</p>
                        <p>• <strong>The Problem:</strong> Hotels & boats in Kashi are 100% full. Devotees cannot travel.</p>
                        <p>• <strong>The Solution:</strong> Travel agency sells 108 Deepdaan on Manikarnika Ghat with client gotra.</p>
                        <p>• <strong>Fulfillment:</strong> Kashi Purohit performs puja, sends HD video + couriers Mahaprasad to client.</p>
                        <p>• <strong>Profit:</strong> Partner earns <strong class="text-emerald-300 font-bold">25% margin</strong> (₹300 - ₹600/booking).</p>
                    </div>

                    <div class="text-xs space-y-2">
                        <label class="block text-xs text-slate-200 font-bold">Filter Partner Target City</label>
                        <select id="b2b-campaign-city" class="w-full text-xs rounded-xl bg-slate-900 border border-slate-600 text-white p-2.5 focus:border-amber-400 focus:ring-0">
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
                    <button type="button" onclick="previewB2bPitch()" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-200 border border-amber-500/40 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                        <i data-lucide="eye" class="w-4 h-4"></i> Preview Sample Partner Pitch
                    </button>
                    <button type="button" id="btn-dispatch-b2b" onclick="dispatchB2bCampaign()" class="w-full py-3 rounded-xl bg-gradient-to-r from-amber-500 via-rose-600 to-red-600 hover:from-amber-600 hover:to-red-700 text-white font-black text-xs flex items-center justify-center gap-2 shadow-lg transition-all">
                        <i data-lucide="send" class="w-4 h-4"></i> 🚀 Launch B2B Dev Deepawali Campaign
                    </button>
                </div>
            </div>

        </div>

        {{-- B2B Partners Directory Table --}}
        <div class="mt-8 bg-slate-950 border border-slate-700 rounded-2xl p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-700">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">B2B Verified Leads Directory</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/40">{{ count($b2bLeads ?? []) }} Displayed</span>
                </div>
                <div class="text-xs text-slate-300">
                    Each partner has an auto-generated unique booking code for 25% revenue share attribution.
                </div>
            </div>

            <div class="overflow-x-auto mt-3">
                <table class="w-full text-left text-xs text-slate-200">
                    <thead>
                        <tr class="border-b border-slate-700 text-slate-300 text-xs font-bold bg-slate-900/60">
                            <th class="py-3 px-3">Business Name</th>
                            <th class="py-3 px-3">Category</th>
                            <th class="py-3 px-3">City</th>
                            <th class="py-3 px-3">Phone / WhatsApp</th>
                            <th class="py-3 px-3">Google Rating</th>
                            <th class="py-3 px-3">Partner Code</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Quick Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
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
                                        default => 'bg-slate-700/60 text-slate-200 border-slate-600',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-800/50 transition-all">
                                    <td class="py-3 px-3 font-bold text-white flex items-center gap-1.5">
                                        {{ $lead->business_name }}
                                        @if($lead->contact_person)
                                            <span class="text-xs text-slate-400 font-normal">({{ $lead->contact_person }})</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">{{ $catLabel }}</td>
                                    <td class="py-3 px-3 font-semibold text-amber-300">{{ $lead->city ?? 'India' }}</td>
                                    <td class="py-3 px-3 font-mono text-xs">{{ $lead->phone }}</td>
                                    <td class="py-3 px-3">
                                        @if($lead->google_rating)
                                            <span class="text-amber-300 font-bold">★ {{ $lead->google_rating }}</span>
                                            <span class="text-xs text-slate-400">({{ $lead->google_reviews_count }})</span>
                                        @else
                                            <span class="text-slate-500">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 font-mono text-xs text-indigo-300 font-bold">{{ $lead->partner_code }}</td>
                                    <td class="py-3 px-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $statusBadge }}">
                                            {{ ucfirst($lead->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <button type="button" onclick="previewB2bPitch({{ $lead->id }})" class="px-3 py-1 rounded-lg bg-indigo-600/30 hover:bg-indigo-600 text-indigo-200 hover:text-white font-bold text-xs transition-all border border-indigo-500/40">
                                            Pitch
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-300">
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
        <div class="lg:col-span-7 bg-white border-2 border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Broadcast Campaign Studio</h3>
                    <p class="text-xs text-slate-600 mt-0.5">Compose and blast devotional campaigns across WhatsApp & Brevo Email</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">Omnichannel</span>
            </div>

            <form id="form-broadcast" class="mt-5 space-y-4 text-xs" onsubmit="event.preventDefault();">
                @csrf
                {{-- Target Audience & Channel Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-800 mb-1.5">Target Devotee Audience</label>
                        <select id="camp-segment" class="w-full text-xs font-semibold rounded-xl border-2 border-slate-200 focus:border-amber-500 focus:ring-0 p-2.5 bg-white text-slate-900">
                            <option value="test">📱 Test to My Own WhatsApp & Email (Safe)</option>
                            <option value="all">👥 All Registered Devotees ({{ $totalDevotees }} contacts)</option>
                            <option value="bookers">🪔 Past Puja Bookers ({{ $totalOrders }} orders)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-800 mb-1.5">Dispatch Channels</label>
                        <select id="camp-channel" class="w-full text-xs font-semibold rounded-xl border-2 border-slate-200 focus:border-amber-500 focus:ring-0 p-2.5 bg-white text-slate-900">
                            <option value="both">⚡ Omnichannel (WhatsApp + Brevo Email)</option>
                            <option value="whatsapp">📱 WhatsApp Only (Evolution API)</option>
                            <option value="email">✉️ Email Only (Brevo REST API)</option>
                        </select>
                    </div>
                </div>

                {{-- Puja Quick Filler --}}
                <div>
                    <label class="block font-bold text-slate-800 mb-1.5">Select Bookable Puja (Auto-Fills Details)</label>
                    <select id="camp-puja-preset" class="w-full text-xs font-bold rounded-xl border-2 border-amber-200 bg-amber-50/50 focus:border-amber-500 focus:ring-0 p-2.5 text-amber-950" onchange="applyPujaPreset(this)">
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
                        <label class="block font-bold text-slate-800 mb-1.5">WhatsApp Headline / Greeting</label>
                        <input type="text" id="camp-headline" class="w-full text-xs rounded-xl border-2 border-slate-200 focus:border-amber-500 focus:ring-0 p-2.5 text-slate-900 font-semibold" 
                               value="🕉️ Special Rudrabhishek Puja Blessings from Varanasi" oninput="updateLivePreview()">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-800 mb-1.5">Email Subject Line</label>
                        <input type="text" id="camp-subject" class="w-full text-xs rounded-xl border-2 border-slate-200 focus:border-amber-500 focus:ring-0 p-2.5 text-slate-900 font-semibold" 
                               value="Divine Blessings for You and Your Family — OnlinePuja.live">
                    </div>
                </div>

                {{-- Message Body --}}
                <div>
                    <label class="block font-bold text-slate-800 mb-1.5">Message Body & Vedic Sankalp Details</label>
                    <textarea id="camp-body" rows="4" class="w-full text-xs rounded-xl border-2 border-slate-200 focus:border-amber-500 focus:ring-0 p-3 text-slate-900 leading-relaxed font-normal" oninput="updateLivePreview()">Pranam Devotee!

Participate in the auspicious Vedic ritual performed by certified Varanasi pandits with your personal sankalp and gotra. Sanctified Ganga Jal, Bhasma & holy prasad will be blessed in your name and dispatched directly to your doorstep.

May Lord Shiva bestow divine health, peace, and abundance upon your household. 🙏✨</textarea>
                </div>

                {{-- Media Attachment (Reel / Poster) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-2">
                        <label class="block font-bold text-slate-800 mb-1.5">Attach Media URL (Video Reel MP4 / Poster Image)</label>
                        <input type="url" id="camp-media-url" class="w-full text-xs rounded-xl border-2 border-slate-200 focus:border-amber-500 focus:ring-0 p-2.5 text-slate-900 font-mono" 
                               placeholder="https://.../video.mp4 or poster.jpg" oninput="updateLivePreview()">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-800 mb-1.5">Media Type</label>
                        <select id="camp-media-type" class="w-full text-xs rounded-xl border-2 border-slate-200 focus:border-amber-500 focus:ring-0 p-2.5 text-slate-900 font-bold" onchange="updateLivePreview()">
                            <option value="image">🖼️ Poster Image</option>
                            <option value="video">🎬 9:16 Video Reel</option>
                        </select>
                    </div>
                </div>

                {{-- Call To Action Button & Link --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-800 mb-1.5">Button Label</label>
                        <input type="text" id="camp-cta-text" class="w-full text-xs rounded-xl border-2 border-slate-200 focus:border-amber-500 focus:ring-0 p-2.5 text-slate-900 font-bold" 
                               value="Book Sacred Puja with Sankalp 🙏" oninput="updateLivePreview()">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-800 mb-1.5">Destination URL</label>
                        <input type="url" id="camp-cta-url" class="w-full text-xs rounded-xl border-2 border-slate-200 focus:border-amber-500 focus:ring-0 p-2.5 text-slate-900 font-mono" 
                               value="https://onlinepuja.live" oninput="updateLivePreview()">
                    </div>
                </div>

                {{-- Dispatch Actions --}}
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between gap-3">
                    <button type="button" onclick="sendCampaign('test')" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 py-2.5 px-4 rounded-xl text-xs font-bold flex items-center gap-1.5 border border-slate-300">
                        <i data-lucide="smartphone" class="w-4 h-4 text-slate-700"></i> Send Test to Admin
                    </button>
                    <button type="button" onclick="sendCampaign('live')" class="btn btn-sm bg-slate-900 hover:bg-slate-800 text-white py-2.5 px-6 rounded-xl text-xs font-black flex items-center gap-2 shadow-md">
                        <i data-lucide="send" class="w-4 h-4 text-amber-400"></i> Launch Broadcast Campaign
                    </button>
                </div>
            </form>
        </div>

        {{-- Live Smartphone Preview Mockup (Right 5 Cols) --}}
        <div class="lg:col-span-5 bg-white border-2 border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col items-center">
            <div class="w-full flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Live WhatsApp Preview</h4>
                <span class="text-xs text-slate-500 font-semibold">Devotee Phone View</span>
            </div>

            {{-- Phone Frame --}}
            <div class="w-full max-w-[320px] bg-slate-900 rounded-[36px] p-2.5 shadow-2xl border-4 border-slate-800">
                {{-- Screen --}}
                <div class="bg-[#efeae2] rounded-[28px] overflow-hidden text-slate-900 flex flex-col min-h-[460px]">
                    {{-- WhatsApp Header --}}
                    <div class="bg-[#075e54] text-white px-3.5 py-3 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-amber-400 text-slate-900 flex items-center justify-center font-black text-xs">
                            🕉️
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1">
                                <span class="font-bold text-xs truncate">Online Puja</span>
                                <span class="text-emerald-300 text-[10px]">✓</span>
                            </div>
                            <p class="text-[10px] text-emerald-100 truncate">Official Verified Business</p>
                        </div>
                    </div>

                    {{-- Chat Bubble Area --}}
                    <div class="p-3 flex-1 flex flex-col justify-end space-y-2">
                        <div class="bg-white rounded-xl rounded-tl-none p-3.5 shadow-sm text-xs space-y-2 border border-slate-200">
                            {{-- Media Preview Thumbnail --}}
                            <div id="preview-media-container" class="rounded-lg overflow-hidden bg-slate-100 aspect-video flex items-center justify-center relative hidden">
                                <img id="preview-media-img" src="" class="w-full h-full object-cover hidden" alt="Campaign Preview">
                                <div id="preview-video-badge" class="absolute inset-0 bg-black/40 flex items-center justify-center text-white hidden">
                                    <div class="w-9 h-9 rounded-full bg-white/90 text-slate-900 flex items-center justify-center font-bold text-xs shadow-md">▶</div>
                                </div>
                            </div>

                            <p class="font-extrabold text-slate-900 leading-snug text-xs" id="preview-headline">🕉️ Special Rudrabhishek Puja Blessings from Varanasi</p>
                            
                            <p class="text-slate-700 whitespace-pre-line leading-relaxed text-[11px]" id="preview-body">
                                Pranam Devotee!

                                Participate in the auspicious Vedic ritual performed by certified Varanasi pandits with your personal sankalp and gotra.
                            </p>

                            <div class="pt-2 border-t border-slate-100 flex flex-col gap-1.5">
                                <div class="bg-emerald-50 text-emerald-900 text-center py-2 px-3 rounded-lg font-bold text-xs flex items-center justify-center gap-1 border border-emerald-300 shadow-2xs">
                                    <span id="preview-cta-text">Book Sacred Puja with Sankalp 🙏</span>
                                </div>
                            </div>

                            <div class="text-[10px] text-right text-slate-500 mt-1 font-semibold">11:45 AM • ✓✓</div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-xs text-slate-500 text-center mt-4">
                Rendered with high-contrast formatting, bold Unicode emojis & inline media.
            </p>
        </div>

    </div>

    {{-- Lifecycle Automated Triggers --}}
    <div class="bg-white border-2 border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-black text-slate-900">Automated Devotee Lifecycle Journeys</h3>
                <p class="text-xs text-slate-600 mt-0.5">Always-on triggers that send personalized WhatsApp & Brevo email notifications</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-900 border border-blue-300">4 Active Flows</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-5">
            {{-- Flow 1 --}}
            <div class="border-2 border-slate-200 rounded-xl p-4 bg-slate-50/70 hover:bg-white hover:shadow-xs transition-all">
                <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-900 flex items-center justify-center font-black text-xs">1</span>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-300">Auto-Active</span>
                </div>
                <h4 class="font-black text-slate-900 text-xs mt-3">Welcome & ₹101 Sankalp</h4>
                <p class="text-xs text-slate-600 mt-1">Triggers when a devotee registers. Sends welcome email + WhatsApp ₹101 coupon code.</p>
                <div class="mt-3 text-xs text-slate-600 font-mono font-bold">Coupon: SANKALP101</div>
            </div>

            {{-- Flow 2 --}}
            <div class="border-2 border-slate-200 rounded-xl p-4 bg-slate-50/70 hover:bg-white hover:shadow-xs transition-all">
                <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-900 flex items-center justify-center font-black text-xs">2</span>
                    <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-300">Auto-Active</span>
                </div>
                <h4 class="font-black text-slate-900 text-xs mt-3">Puja Booking Confirmation</h4>
                <p class="text-xs text-slate-600 mt-1">Triggers immediately after checkout. Sends Order ID, Pandit details, and ritual timing guide.</p>
                <div class="mt-3 text-xs text-slate-600 font-mono font-bold">WhatsApp + Brevo HTML</div>
            </div>

            {{-- Flow 3 --}}
            <div class="border-2 border-slate-200 rounded-xl p-4 bg-slate-50/70 hover:bg-white hover:shadow-xs transition-all">
                <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-900 flex items-center justify-center font-black text-xs">3</span>
                    <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-300">Auto-Active</span>
                </div>
                <h4 class="font-black text-slate-900 text-xs mt-3">Prasad Dispatch & Tracking</h4>
                <p class="text-xs text-slate-600 mt-1">Triggers when prasad is packed in Varanasi. Sends India Post / courier tracking link to WhatsApp.</p>
                <div class="mt-3 text-xs text-slate-600 font-mono font-bold">WhatsApp Tracking</div>
            </div>

            {{-- Flow 4 --}}
            <div class="border-2 border-slate-200 rounded-xl p-4 bg-slate-50/70 hover:bg-white hover:shadow-xs transition-all">
                <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-900 flex items-center justify-center font-black text-xs">4</span>
                    <span class="text-xs font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-300">Auto-Active</span>
                </div>
                <h4 class="font-black text-slate-900 text-xs mt-3">Tithi & Muhurat Nudges</h4>
                <p class="text-xs text-slate-600 mt-1">Panchang-driven broadcast before Ekadashi, Pradosh, and Shivratri with recommended pujas.</p>
                <div class="mt-3 text-xs text-slate-600 font-mono font-bold">Hindu Calendar Sync</div>
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
        <p class="text-xs text-slate-500 mt-1">Open WhatsApp on your phone &gt; Linked Devices &gt; Link a Device</p>

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

{{-- Schedule Custom Festival / Event Modal --}}
<div id="modal-schedule-event" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🕉️</span>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Schedule Sacred Event / Festival</h3>
                    <p class="text-xs text-slate-500">Add any upcoming Hindu tithi or puja to parallel campaign engine</p>
                </div>
            </div>
            <button type="button" onclick="closeScheduleModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
        </div>

        <form id="form-schedule-event" onsubmit="saveCustomFestival(event)" class="mt-4 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Festival / Sacred Occasion Name *</label>
                <input type="text" id="sched-fest-name" required placeholder="e.g. Somvati Amavasya Pitru Shanti, Vaikuntha Ekadashi, Gita Jayanti" class="w-full text-xs rounded-xl bg-slate-50 border border-slate-200 p-2.5 text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-0">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Event Date *</label>
                    <input type="date" id="sched-fest-date" required class="w-full text-xs rounded-xl bg-slate-50 border border-slate-200 p-2.5 text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-0">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deity / Spiritual Theme</label>
                    <input type="text" id="sched-fest-deity" placeholder="e.g. Lord Shiva, Maa Lakshmi" value="Lord Shiva & Pitrus" class="w-full text-xs rounded-xl bg-slate-50 border border-slate-200 p-2.5 text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-0">
                </div>
            </div>

            <div class="p-3 bg-amber-50/80 border border-amber-200/70 rounded-xl text-xs text-amber-900 leading-relaxed font-medium">
                <span class="font-bold">⚡ Parallel Multi-Campaign Engine:</span> Once scheduled, you can immediately generate Banners, SEO Blogs, and Video Reels, and the daily autopilot radar will sweep milestones (T-14, T-7, T-3, T-1, Day-0) in parallel with all other festivals!
            </div>

            <div class="mt-4 flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeScheduleModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 rounded-xl">
                    Cancel
                </button>
                <button type="submit" id="btn-save-custom-fest" class="px-5 py-2 text-xs font-black bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl shadow-md flex items-center gap-1.5 transition-all">
                    <span>➕</span> Add to Parallel Radar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- B2B Pitch Preview Modal --}}
<div id="modal-b2b-pitch" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2">
                <span class="text-2xl">💼</span>
                <div>
                    <h3 class="text-base font-black text-slate-900" id="pitch-modal-partner">Dev Deepawali 2026 B2B Partnership Proposal</h3>
                    <p class="text-xs text-slate-500">25% Commission • Zero Logistics Vedic Fulfillment</p>
                </div>
            </div>
            <button type="button" onclick="closeB2bPitchModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">✕</button>
        </div>

        <div class="mt-4 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">WhatsApp Proposal Message</label>
                <div id="pitch-modal-whatsapp" class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 font-mono text-xs text-slate-800 whitespace-pre-wrap leading-relaxed max-h-60 overflow-y-auto"></div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Partner Attribution URL (25% Commission Tracking)</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="pitch-modal-url" readonly class="w-full text-xs font-mono rounded-xl bg-slate-100 border border-slate-300 p-2.5 text-slate-900 font-semibold select-all">
                    <button type="button" onclick="copyPitchUrl()" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold whitespace-nowrap">
                        Copy
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-3 border-t border-slate-200 flex items-center justify-end">
            <button type="button" onclick="closeB2bPitchModal()" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs">
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
    document.getElementById('camp-cta-url').value = 'https://onlinepuja.live/pujas/' + (slug || '');

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

function orchestrateMasterCampaign(festivalName) {
    var btn = document.getElementById('btn-master-orchestrate');
    var origText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin">⏳</span> Orchestrating Brain (Blog + Banners + Reel + Copy)...';
    }

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ action: 'orchestrate_master_campaign', festival: festivalName })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = origText;
        }
        if (res && res.ok) {
            var pkg = res.campaign_package;
            if (pkg) {
                document.getElementById('camp-headline').value = '🪔 ' + (pkg.branding ? pkg.branding.title : res.festival);
                document.getElementById('camp-subject').value = pkg.email_subject || ('Sacred Invitation: ' + res.festival);
                document.getElementById('camp-body').value = pkg.whatsapp_copy || '';
                document.getElementById('camp-cta-text').value = 'Book Sacred Sankalp 🙏';
                document.getElementById('camp-cta-url').value = pkg.booking_url || 'https://onlinepuja.live/pujas';
                document.getElementById('camp-media-url').value = pkg.video_reel_url || pkg.banner_landscape;
                document.getElementById('camp-media-type').value = pkg.video_reel_url ? 'video' : 'image';
                document.getElementById('camp-channel').value = 'both';
                updateLivePreview();
            }

            var dock = document.getElementById('master-asset-dock');
            if (dock) {
                dock.classList.remove('hidden');
                if (res.blog && res.blog.blog_url) {
                    document.getElementById('dock-blog-link').href = res.blog.blog_url;
                }
                if (res.banners && res.banners.landscape) {
                    document.getElementById('dock-banner-link').href = res.banners.landscape.banner_url;
                }
                if (res.reel && res.reel.video_url) {
                    document.getElementById('dock-reel-link').href = res.reel.video_url;
                }
            }

            alert('🎉 Autonomous Master Campaign Generated Successfully!\n\n• SEO Devotional Blog: Live on website\n• 1200x630 & 1080x1080 Banners: Ready\n• 1080x1920 MP4 Video Reel: Ready\n• Omnichannel WhatsApp & Email: Loaded in composer below');
        } else {
            alert('Campaign orchestration note: ' + (res.message || 'Check logs for details'));
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

function generateFestivalBanners(festivalName) {
    var btn = document.getElementById('btn-gen-banners');
    var origText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin">🎨</span> Rendering...';
    }

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ action: 'generate_festival_banners', festival: festivalName })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = origText;
        }
        if (res && res.ok && res.landscape) {
            document.getElementById('camp-media-url').value = res.landscape;
            document.getElementById('camp-media-type').value = 'image';
            updateLivePreview();
            alert('🎉 Landscape & Square Banners Generated!\n\nLandscape: ' + res.landscape + '\nSquare: ' + res.square);
        } else {
            alert('Banner generation note: ' + (res.message || 'Error'));
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

function publishFestivalBlog(festivalName) {
    var btn = document.getElementById('btn-publish-blog');
    var origText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin">✍️</span> Publishing...';
    }

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ action: 'publish_festival_blog', festival: festivalName })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = origText;
        }
        if (res && res.ok && res.blog_url) {
            alert('🎉 Devotional SEO Blog Published Live!\n\nURL: ' + res.blog_url);
            window.open(res.blog_url, '_blank');
        } else {
            alert('Blog note: ' + (res.message || 'Error'));
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
    if (!confirm('Broadcast campaign for ' + festivalName + ' to all reachable devotees across WhatsApp, Brevo Email, and Web/App Push?')) {
        return;
    }

    var btn = document.getElementById('btn-quick-launch-dev');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin">🚀</span> Broadcasting...';
    }

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            action: 'dispatch_festival_campaign',
            festival: festivalName,
            channels: ['whatsapp', 'email', 'push']
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="zap" class="w-4 h-4"></i> Broadcast (WhatsApp + Email + Push)';
        }
        if (res && res.ok) {
            alert('🎉 Multi-Channel Broadcast Complete!\n\nWhatsApp: ' + res.results.whatsapp.sent + '\nEmail: ' + res.results.email.sent + '\nPush: ' + res.results.push.sent);
        } else {
            alert('Broadcast note: ' + (res.message || 'Check logs for details'));
        }
    })
    .catch(function(err) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="zap" class="w-4 h-4"></i> Broadcast (WhatsApp + Email + Push)';
        }
        alert('Error: ' + err.message);
    });
}

function sendCampaign(mode) {
    var segment = document.getElementById('camp-segment').value;
    var channel = document.getElementById('camp-channel').value;
    var headline = document.getElementById('camp-headline').value;
    var subject = document.getElementById('camp-subject').value;
    var body = document.getElementById('camp-body').value;
    var mediaUrl = document.getElementById('camp-media-url').value;
    var mediaType = document.getElementById('camp-media-type').value;
    var ctaText = document.getElementById('camp-cta-text').value;
    var ctaUrl = document.getElementById('camp-cta-url').value;

    if (!headline || !body) {
        alert('Please fill out the Headline and Message Body.');
        return;
    }

    if (mode === 'live' && !confirm('Are you sure you want to blast this campaign to ' + segment + ' via ' + channel + '?')) {
        return;
    }

    var btn = event ? event.target : null;
    var origText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin">⏳</span> Dispatching...';
    }

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            action: 'dispatch_custom_broadcast',
            mode: mode,
            segment: segment,
            channel: channel,
            headline: headline,
            subject: subject,
            body: body,
            media_url: mediaUrl,
            media_type: mediaType,
            cta_text: ctaText,
            cta_url: ctaUrl
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = origText;
        }
        if (res && res.ok) {
            alert('🎉 ' + res.message);
        } else {
            alert('Dispatch failed: ' + (res.message || res.error || 'Error'));
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

function openScheduleModal() {
    var modal = document.getElementById('modal-schedule-event');
    if (modal) {
        modal.classList.remove('hidden');
        var dateInput = document.getElementById('sched-fest-date');
        if (dateInput && !dateInput.value) {
            var d = new Date();
            d.setDate(d.getDate() + 7);
            dateInput.value = d.toISOString().split('T')[0];
        }
    }
}

function closeScheduleModal() {
    var modal = document.getElementById('modal-schedule-event');
    if (modal) modal.classList.add('hidden');
}

function saveCustomFestival(e) {
    if (e && e.preventDefault) e.preventDefault();

    var name = document.getElementById('sched-fest-name').value.trim();
    var date = document.getElementById('sched-fest-date').value;
    var deity = document.getElementById('sched-fest-deity').value.trim() || 'Vedic Devata';

    if (!name || !date) {
        alert('Please provide Festival Name and Date.');
        return;
    }

    var btn = document.getElementById('btn-save-custom-fest');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin">⏳</span> Scheduling...';
    }

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            action: 'store_custom_festival',
            name: name,
            date: date,
            deity: deity
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span>➕</span> Add to Parallel Radar';
        }
        if (res && res.ok) {
            alert('🎉 ' + res.message);
            closeScheduleModal();
            location.reload();
        } else {
            alert('Error: ' + (res.error || res.message || 'Failed to schedule festival'));
        }
    })
    .catch(function(err) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span>➕</span> Add to Parallel Radar';
        }
        alert('Network Error: ' + err.message);
    });
}

function switchB2bTab(tab) {
    var csvTab = document.getElementById('b2b-tab-csv');
    var radarTab = document.getElementById('b2b-tab-radar');
    var btnCsv = document.getElementById('tab-btn-csv');
    var btnRadar = document.getElementById('tab-btn-radar');

    if (tab === 'csv') {
        csvTab.classList.remove('hidden');
        radarTab.classList.add('hidden');
        btnCsv.className = 'px-3.5 py-1.5 rounded-lg bg-indigo-600 text-white font-bold transition-all';
        btnRadar.className = 'px-3.5 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-200 font-semibold transition-all';
    } else {
        csvTab.classList.add('hidden');
        radarTab.classList.remove('hidden');
        btnCsv.className = 'px-3.5 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-200 font-semibold transition-all';
        btnRadar.className = 'px-3.5 py-1.5 rounded-lg bg-indigo-600 text-white font-bold transition-all';
    }
}

function seedB2bDirectory() {
    if (!confirm('Seed premier curated directory of national travel agents, tour operators, and spiritual Kendra leads across Mumbai, Surat, Ahmedabad, and Delhi?')) {
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
        alert(res.message || 'B2B Directory Seeded Successfully');
        location.reload();
    })
    .catch(function(err) {
        alert('Network error: ' + err.message);
    });
}

function importB2bCsv() {
    var city = document.getElementById('b2b-csv-city').value.trim();
    var category = document.getElementById('b2b-csv-category').value;
    var fileInput = document.getElementById('b2b-csv-file');
    var rawText = document.getElementById('b2b-csv-text').value.trim();

    if (!rawText && (!fileInput.files || fileInput.files.length === 0)) {
        alert('Please choose a CSV file or paste raw CSV text.');
        return;
    }

    var btn = document.getElementById('btn-import-csv');
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin">⏳</span> Ingesting and Validating Numbers...';

    if (fileInput.files && fileInput.files.length > 0) {
        var reader = new FileReader();
        reader.onload = function(e) {
            submitCsvPayload(e.target.result, city, category, btn);
        };
        reader.readAsText(fileInput.files[0]);
    } else {
        submitCsvPayload(rawText, city, category, btn);
    }
}

function submitCsvPayload(csvContent, city, category, btn) {
    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            action: 'import_b2b_csv',
            csv_content: csvContent,
            default_city: city,
            category: category
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="upload-cloud" class="w-4 h-4"></i> Ingest & Validate Indian Numbers (+91)';
        if (res && res.ok) {
            alert('🎉 ' + res.message);
            location.reload();
        } else {
            alert('Import error: ' + (res.message || res.error || 'Failed'));
        }
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="upload-cloud" class="w-4 h-4"></i> Ingest & Validate Indian Numbers (+91)';
        alert('Network error: ' + err.message);
    });
}

function runGoogleMapsRadar() {
    var city = document.getElementById('b2b-radar-city').value.trim();
    var category = document.getElementById('b2b-radar-category').value;

    if (!city) {
        alert('Please enter a target city.');
        return;
    }

    var btn = document.getElementById('btn-radar-search');
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin">📡</span> Scanning Google Places Radar in ' + city + '...';

    fetch('{{ route("growth-os.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            action: 'radar_b2b_search',
            city: city,
            category: category
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="radar" class="w-4 h-4"></i> Scan Google Maps Places & Ingest';
        if (res && res.ok) {
            alert('🎉 ' + res.message);
            location.reload();
        } else {
            alert('Radar search note: ' + (res.message || res.error || 'Done'));
        }
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="radar" class="w-4 h-4"></i> Scan Google Maps Places & Ingest';
        alert('Network error: ' + err.message);
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
        body: JSON.stringify({
            action: 'preview_b2b_pitch',
            lead_id: leadId || null
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res && res.ok && res.pitch) {
            document.getElementById('pitch-modal-partner').innerText = 'Proposal for ' + (res.pitch.partner_name || 'B2B Partner');
            document.getElementById('pitch-modal-whatsapp').innerText = res.pitch.whatsapp_copy;
            document.getElementById('pitch-modal-url').value = res.pitch.booking_url;
            document.getElementById('modal-b2b-pitch').classList.remove('hidden');
        } else {
            alert('Could not generate pitch: ' + (res.error || 'Unknown'));
        }
    })
    .catch(function(err) {
        alert('Network error: ' + err.message);
    });
}

function closeB2bPitchModal() {
    document.getElementById('modal-b2b-pitch').classList.add('hidden');
}

function copyPitchUrl() {
    var inp = document.getElementById('pitch-modal-url');
    inp.select();
    document.execCommand('copy');
    alert('Copied partner attribution link to clipboard!');
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
            var html = '<img src="' + res.base64 + '" class="w-48 h-48 rounded-lg shadow-sm mx-auto" alt="WhatsApp QR Code">';
            if (res.pairingCode) {
                html += '<div class="mt-3 p-2.5 bg-amber-500/10 border border-amber-500/30 rounded-xl text-center"><span class="text-[10px] text-slate-400 block uppercase font-semibold">Or use Pairing Code in WhatsApp:</span><span class="text-base font-mono font-bold text-amber-300 tracking-wider">' + res.pairingCode + '</span></div>';
            }
            box.innerHTML = html;
        } else if (res && res.pairingCode) {
            box.innerHTML = '<div class="p-4 bg-amber-500/10 border border-amber-500/30 rounded-xl text-center"><span class="text-xs text-slate-700 block mb-1 font-bold">WhatsApp Pairing Code:</span><span class="text-2xl font-mono font-black text-amber-600 tracking-widest">' + res.pairingCode + '</span><p class="text-xs text-slate-500 mt-2">Enter this in WhatsApp &gt; Linked Devices &gt; Link with phone number</p></div>';
        } else if (res && (res.code || res.state === 'open')) {
            box.innerHTML = '<div class="p-3 bg-emerald-500/20 text-emerald-800 border border-emerald-500/30 text-xs rounded-lg font-bold text-center">Instance is already paired and active!</div>';
        } else {
            box.innerHTML = '<div class="text-xs text-rose-500 font-bold">' + (res.error || 'Failed to fetch QR') + '</div>';
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
