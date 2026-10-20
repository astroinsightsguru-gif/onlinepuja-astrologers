@extends('pages.growth-os.layout')

@section('growth_content')
<div class="space-y-5">

    {{-- TOP: Metric KPI Strip (Clean, Compact, High-Signal) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        {{-- Metric 1: Webhook Status --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dispatch Webhook</span>
                <div class="text-sm font-bold text-slate-800 mt-1 flex items-center gap-1.5">
                    @if(!empty($webhookUrl))
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Connected (Active)
                    @else
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Setup Required
                    @endif
                </div>
            </div>
            <button type="button" id="toggle-webhook-config" class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                Configure
            </button>
        </div>

        {{-- Metric 2: Hindu Festivals --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Seeded Festivals</span>
                <div class="text-sm font-bold text-slate-800 mt-1">
                    {{ $festivals->count() }} <span class="text-xs font-normal text-slate-400">Events (2026-27)</span>
                </div>
            </div>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="calendar" class="w-4 h-4"></i>
            </div>
        </div>

        {{-- Metric 3: Queued Social Posts --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Approved Post Queue</span>
                <div class="text-sm font-bold text-slate-800 mt-1">
                    {{ $socialPosts->count() }} <span class="text-xs font-normal text-slate-400">Posts Ready</span>
                </div>
            </div>
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="send" class="w-4 h-4"></i>
            </div>
        </div>

        {{-- Metric 4: Channels & Schedule --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Morning Schedule</span>
                <div class="text-sm font-bold text-slate-800 mt-1">
                    06:30 AM <span class="text-xs font-normal text-slate-400">IST Daily</span>
                </div>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-4 h-4"></i>
            </div>
        </div>
    </div>

    {{-- Collapsible Webhook Dispatch Connector --}}
    <div id="webhook-config-drawer" class="{{ empty($webhookUrl) ? '' : 'hidden' }} bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
            <div>
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="radio" class="w-3.5 h-3.5 text-rose-500"></i> Social Media Dispatch Webhook URL
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Dispatches daily festival posts to your Make.com, Zapier, Publer, or Meta Graph API webhook.</p>
            </div>
            <span class="text-[11px] text-slate-400 font-mono">Payload: id, title, text, hashtags, channels, image_url, link</span>
        </div>
        <div class="mt-3 flex flex-col sm:flex-row gap-2">
            <input type="url" id="social-webhook-input" value="{{ $webhookUrl }}" placeholder="https://hook.eu1.make.com/... or Zapier / Meta webhook URL" class="w-full text-xs rounded-xl border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-slate-900/10 font-mono text-slate-800">
            <button type="button" id="btn-save-social-webhook" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 whitespace-nowrap transition">Save Webhook</button>
            <button type="button" id="btn-test-social-webhook" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 font-semibold text-xs hover:bg-slate-200 whitespace-nowrap transition">Test Ping</button>
        </div>
    </div>

    {{-- SECTION SWITCHER TABS: Declutters the screen by organizing into focused workflows --}}
    <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
        <div class="flex items-center gap-2">
            <button type="button" id="tab-btn-schedule" class="section-tab-btn active px-3.5 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-white shadow-2xs">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-amber-400"></i> Festival Calendar & Queue
            </button>
            <button type="button" id="tab-btn-studio" class="section-tab-btn px-3.5 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/80">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-slate-400"></i> OmniRoute AI Creative Studio
            </button>
        </div>
        <div class="flex items-center gap-2">
            <button id="btn-schedule-festivals" class="btn btn-sm text-xs py-1.5 px-3 bg-gradient-to-r from-amber-500 to-rose-600 text-white font-semibold rounded-lg shadow-2xs border-0 flex items-center gap-1.5">
                <i data-lucide="wand" class="w-3.5 h-3.5"></i> Auto-Schedule Wishes
            </button>
        </div>
    </div>

    {{-- TAB PANE 1: Festival Calendar & Queued Posts (Default View) --}}
    <div id="pane-schedule" class="section-pane">
        <div class="grid grid-cols-12 gap-5">
            {{-- Left: Hindu Festival Calendar --}}
            <div class="col-span-12 lg:col-span-7">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-2xs">
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-amber-500"></i> Hindu Festival Calendar (2026–27)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Seeded Hindu festival dates for automated puja promotions & devotional wishes.</p>
                        </div>
                        <button id="btn-seed-festivals" class="btn btn-sm btn-outline-secondary text-xs py-1.5 px-2.5 bg-white hover:bg-slate-50 rounded-lg border-slate-200">
                            <i data-lucide="refresh-cw" class="w-3 h-3 mr-1"></i> Refresh
                        </button>
                    </div>

                    {{-- Festivals List --}}
                    <div class="space-y-2.5 mt-3.5 max-h-[580px] overflow-y-auto pr-1">
                        @forelse($festivals as $fest)
                            <div class="p-3 rounded-xl border {{ $fest->scheduled ? 'bg-emerald-50/40 border-emerald-200/80' : 'bg-slate-50/60 border-slate-200/80' }} flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs transition hover:border-amber-300">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-white border border-slate-200/80 flex flex-col items-center justify-center flex-shrink-0 text-center shadow-2xs">
                                        <span class="text-[9px] font-bold uppercase text-rose-600">{{ \Carbon\Carbon::parse($fest->date)->format('M') }}</span>
                                        <span class="text-sm font-extrabold text-slate-800 leading-none">{{ \Carbon\Carbon::parse($fest->date)->format('d') }}</span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-xs text-slate-800 flex items-center gap-2">
                                            <span>{{ $fest->name }}</span>
                                            @if($fest->scheduled)
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800">Scheduled ✓</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800">Pending</span>
                                            @endif
                                        </div>
                                        <div class="text-slate-500 text-[11px] mt-0.5">
                                            Deity: <strong class="text-slate-700">{{ $fest->deity ?: 'General' }}</strong> · {{ \Carbon\Carbon::parse($fest->date)->format('l, d M Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 self-end sm:self-center">
                                    <button type="button" class="btn-create-for-fest px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-amber-400 text-slate-700 hover:text-amber-700 font-semibold text-[11px] shadow-2xs flex items-center gap-1 transition"
                                            data-name="{{ $fest->name }}" data-deity="{{ $fest->deity }}">
                                        <i data-lucide="sparkles" class="w-3 h-3 text-amber-500"></i> Create Visual
                                    </button>
                                    <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-600 font-medium text-[10px]">
                                        {{ \Carbon\Carbon::parse($fest->date)->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-slate-400 text-xs">
                                No Hindu festivals seeded. Click "Refresh" to populate 2026-2027 calendar.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right: Queued Social Posts --}}
            <div class="col-span-12 lg:col-span-5">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-2xs">
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Queued Devotional Posts</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Scheduled for morning webhook dispatch.</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">{{ $socialPosts->count() }}</span>
                    </div>

                    <div class="space-y-3 mt-3.5 max-h-[580px] overflow-y-auto pr-1 text-xs">
                        @forelse($socialPosts as $post)
                            @php
                                $p = json_decode((string) $post->payload, true);
                            @endphp
                            <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/80 space-y-2.5">
                                <div class="font-bold text-slate-900 flex justify-between items-center text-xs">
                                    <span>{{ $post->title }}</span>
                                    <span class="text-[9px] text-purple-700 font-bold uppercase tracking-wider bg-purple-100 px-2 py-0.5 rounded-full">{{ $post->status }}</span>
                                </div>

                                @if(!empty($p['image_url']))
                                    <div class="w-full h-32 rounded-lg overflow-hidden border border-slate-200 relative group">
                                        <img src="{{ $p['image_url'] }}" alt="Attached Creative" class="w-full h-full object-cover">
                                        <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[9px] px-1.5 py-0.5 rounded">Vedic Visual Attached</span>
                                    </div>
                                @endif

                                @if($p && !empty($p['text']))
                                    <div class="text-slate-700 bg-white p-2.5 rounded-lg border border-slate-200/70 text-[11px] leading-relaxed">
                                        {{ $p['text'] }}
                                    </div>
                                @endif

                                @if($p && !empty($p['hashtags']))
                                    <div class="text-amber-700 font-medium text-[10px]">
                                        {{ $p['hashtags'] }}
                                    </div>
                                @endif

                                <div class="text-[10px] text-slate-400 flex justify-between items-center pt-2 border-t border-slate-200/70">
                                    <span>Due: <strong class="text-slate-700">{{ $post->due_at ?: 'Morning 06:30' }}</strong></span>
                                    <span class="font-mono text-slate-600 uppercase">{{ $post->channel }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-slate-400 text-xs italic">
                                No devotional posts currently queued. Click "Auto-Schedule Wishes" above to generate upcoming festival posts.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB PANE 2: OmniRoute AI Creative Studio (Focused, Decluttered Workspace) --}}
    <div id="pane-studio" class="section-pane hidden">
        <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-slate-100 gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white shadow-2xs">
                        <i data-lucide="sparkles" class="w-5 h-5 text-amber-400"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                            OmniRoute AI Creative Studio
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">Vedic Engine v2</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Generate photorealistic Vedic ceremony photography (Flux) and cinematic motion video reels (Wan 2.1).</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Free Tier Flux Active
                    </span>
                </div>
            </div>

            {{-- Clean, Roomy Studio Grid --}}
            <div class="grid grid-cols-12 gap-5 mt-5">
                {{-- Left Side: Parameters --}}
                <div class="col-span-12 lg:col-span-7 space-y-4">
                    {{-- Quick Mode Selector --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Preset Category</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" id="btn-mode-puja" class="preset-mode-btn active px-3 py-2 rounded-xl text-xs font-semibold border border-slate-900 bg-slate-900 text-white transition flex items-center justify-center gap-1.5">
                                <i data-lucide="flame" class="w-3.5 h-3.5 text-amber-400"></i> Sacred Puja / Ceremony
                            </button>
                            <button type="button" id="btn-mode-rashi" class="preset-mode-btn px-3 py-2 rounded-xl text-xs font-semibold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition flex items-center justify-center gap-1.5">
                                <i data-lucide="sun" class="w-3.5 h-3.5 text-amber-500"></i> Daily Rashifal Reel
                            </button>
                        </div>
                    </div>

                    {{-- Topic Input & Presets --}}
                    <div id="wrapper-puja-presets">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Puja / Festival / Ceremony Subject</label>
                        <input type="text" id="creative-topic" value="Griha Pravesh" 
                               class="w-full text-xs rounded-xl border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-slate-900/10 transition font-medium text-slate-800" 
                               placeholder="e.g. Griha Pravesh, Satyanarayan, Maha Shivratri...">
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <button type="button" class="quick-topic-btn px-2.5 py-1 rounded-lg text-[11px] bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 font-medium transition" data-topic="Griha Pravesh">Griha Pravesh</button>
                            <button type="button" class="quick-topic-btn px-2.5 py-1 rounded-lg text-[11px] bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 font-medium transition" data-topic="Satyanarayan Puja">Satyanarayan</button>
                            <button type="button" class="quick-topic-btn px-2.5 py-1 rounded-lg text-[11px] bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 font-medium transition" data-topic="Diwali Lakshmi Puja">Lakshmi Puja</button>
                            <button type="button" class="quick-topic-btn px-2.5 py-1 rounded-lg text-[11px] bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 font-medium transition" data-topic="Lord Shiva Rudrabhishek">Rudrabhishek</button>
                            <button type="button" class="quick-topic-btn px-2.5 py-1 rounded-lg text-[11px] bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-800 font-medium transition" data-topic="Lord Ganesha Aarti">Ganesha Aarti</button>
                        </div>
                    </div>

                    {{-- Rashifal Zodiac Selector (Hidden until clicked) --}}
                    <div id="wrapper-rashi-presets" class="hidden">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Select Zodiac Sign (Rashifal)</label>
                        <select id="select-rashi" class="w-full text-xs rounded-xl border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white text-slate-800 font-medium">
                            <option value="Aries (Mesh) Daily Rashifal">♈ Mesh (Aries)</option>
                            <option value="Taurus (Vrishabh) Daily Rashifal">♉ Vrishabh (Taurus)</option>
                            <option value="Gemini (Mithun) Daily Rashifal">♊ Mithun (Gemini)</option>
                            <option value="Cancer (Kark) Daily Rashifal">♋ Kark (Cancer)</option>
                            <option value="Leo (Singh) Daily Rashifal">♌ Singh (Leo)</option>
                            <option value="Virgo (Kanya) Daily Rashifal">♍ Kanya (Virgo)</option>
                            <option value="Libra (Tula) Daily Rashifal">♎ Tula (Libra)</option>
                            <option value="Scorpio (Vrishchik) Daily Rashifal">♏ Vrishchik (Scorpio)</option>
                            <option value="Sagittarius (Dhanu) Daily Rashifal">♐ Dhanu (Sagittarius)</option>
                            <option value="Capricorn (Makar) Daily Rashifal">♑ Makar (Capricorn)</option>
                            <option value="Aquarius (Kumbh) Daily Rashifal">♒ Kumbh (Aquarius)</option>
                            <option value="Pisces (Meen) Daily Rashifal">♓ Meen (Pisces)</option>
                        </select>
                    </div>

                    {{-- Style & Aspect Ratio Controls --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Visual Aesthetic Style</label>
                            <select id="creative-style" class="w-full text-xs rounded-xl border-slate-200 px-3 py-2 bg-slate-50 focus:bg-white text-slate-800 font-medium">
                                <option value="photorealistic" selected>Photorealistic Vedic Photography (85mm f/1.8)</option>
                                <option value="temple_sanctum">Temple Sanctum Sanctorum (Glowing Brass Diyas)</option>
                                <option value="heritage_art">Raja Ravi Varma Heritage Fine Art (Oil on Canvas)</option>
                                <option value="video_reel">9:16 Cinematic Video Motion Reel (OmniRoute)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Aspect Ratio</label>
                            <select id="creative-ratio" class="w-full text-xs rounded-xl border-slate-200 px-3 py-2 bg-slate-50 focus:bg-white text-slate-800 font-medium">
                                <option value="1:1" selected>1:1 Square (Instagram / Facebook Post)</option>
                                <option value="4:5">4:5 Portrait (Social Feed Optimal)</option>
                                <option value="9:16">9:16 Vertical (Reels / Stories)</option>
                                <option value="16:9">16:9 Landscape (Blog / Banner)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-3 pt-2">
                        <button id="btn-generate-creative" class="btn btn-sm flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 rounded-xl shadow-2xs border-0 flex items-center justify-center gap-2 text-xs transition">
                            <i data-lucide="sparkles" class="w-4 h-4 text-amber-400"></i> <span id="generate-btn-text">Generate Photorealistic Vedic Image (Flux)</span>
                        </button>
                        <button id="btn-preview-prompt" class="btn btn-sm btn-outline-secondary text-xs py-2.5 px-3.5 rounded-xl text-slate-700 hover:bg-slate-50 bg-white border-slate-200 whitespace-nowrap">
                            <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500 mr-1"></i> Inspect Tokens
                        </button>
                    </div>

                    {{-- Prompt Inspector Drawer --}}
                    <div id="prompt-inspector" class="hidden pt-3 border-t border-slate-100 bg-slate-50/70 p-3.5 rounded-xl text-xs space-y-2">
                        <div>
                            <span class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Injected Vedic Realism Tokens:</span>
                            <div id="inspector-positive" class="text-slate-600 mt-1 font-mono text-[11px] leading-relaxed bg-white p-2.5 rounded-lg border border-slate-200"></div>
                        </div>
                        <div>
                            <span class="font-bold text-rose-700 uppercase tracking-wider text-[10px]">Active Anti-CGI Negative Filter:</span>
                            <div id="inspector-negative" class="text-rose-600 mt-1 font-mono text-[11px] leading-relaxed bg-rose-50/50 p-2.5 rounded-lg border border-rose-200"></div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Live Creative Preview & Attachment --}}
                <div class="col-span-12 lg:col-span-5">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 flex flex-col items-center justify-center min-h-[340px] text-center">
                        <div id="creative-preview-empty" class="text-slate-400 text-xs py-12">
                            <i data-lucide="image" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                            Select a subject and click Generate to produce photorealistic visual assets.
                        </div>

                        <div id="creative-preview-card" class="hidden w-full space-y-3">
                            <div class="w-full h-64 rounded-xl overflow-hidden bg-slate-900 flex items-center justify-center shadow-inner relative group">
                                <img id="preview-image" src="" alt="AI Generated Asset" class="w-full h-full object-cover rounded-xl hidden">
                                <video id="preview-video" src="" controls class="w-full h-full object-cover rounded-xl hidden"></video>
                                <div id="preview-loading" class="text-center text-white p-4">
                                    <div class="animate-spin inline-block w-8 h-8 border-3 border-amber-500 border-t-transparent rounded-full mb-2"></div>
                                    <div class="text-xs font-medium">Generating photorealistic Vedic creative…</div>
                                </div>
                            </div>
                            <div class="text-left text-xs text-slate-700 font-medium leading-relaxed bg-white p-3 rounded-xl border border-slate-200" id="preview-caption"></div>
                            <div class="flex items-center gap-2 pt-1">
                                <select id="select-attach-post" class="text-xs rounded-xl border-slate-200 px-3 py-2 bg-white text-slate-700 flex-1">
                                    <option value="">Select Queued Post to Attach Visual...</option>
                                    @foreach($socialPosts as $post)
                                        <option value="{{ $post->id }}">{{ $post->title }} ({{ $post->status }})</option>
                                    @endforeach
                                </select>
                                <button id="btn-attach-to-post" class="btn btn-sm bg-amber-500 hover:bg-amber-600 border-0 text-white font-semibold px-3 py-2 rounded-xl text-xs flex items-center gap-1.5 whitespace-nowrap">
                                    <i data-lucide="paperclip" class="w-3.5 h-3.5"></i> Attach
                                </button>
                                <a id="btn-download-creative" href="#" target="_blank" download="vedic-creative.png" class="btn btn-sm btn-outline-secondary text-xs p-2 rounded-xl bg-white border-slate-200">
                                    <i data-lucide="download" class="w-3.5 h-3.5 text-slate-600"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Tab Switching between Schedule and Creative Studio
    var tabBtnSchedule = document.getElementById('tab-btn-schedule');
    var tabBtnStudio = document.getElementById('tab-btn-studio');
    var paneSchedule = document.getElementById('pane-schedule');
    var paneStudio = document.getElementById('pane-studio');

    function switchSectionTab(tab) {
        if (tab === 'studio') {
            paneStudio.classList.remove('hidden');
            paneSchedule.classList.add('hidden');
            tabBtnStudio.classList.add('bg-slate-900', 'text-white');
            tabBtnStudio.classList.remove('bg-white', 'text-slate-600');
            tabBtnSchedule.classList.remove('bg-slate-900', 'text-white');
            tabBtnSchedule.classList.add('bg-white', 'text-slate-600');
        } else {
            paneSchedule.classList.remove('hidden');
            paneStudio.classList.add('hidden');
            tabBtnSchedule.classList.add('bg-slate-900', 'text-white');
            tabBtnSchedule.classList.remove('bg-white', 'text-slate-600');
            tabBtnStudio.classList.remove('bg-slate-900', 'text-white');
            tabBtnStudio.classList.add('bg-white', 'text-slate-600');
        }
    }

    if (tabBtnSchedule && tabBtnStudio) {
        tabBtnSchedule.addEventListener('click', function() { switchSectionTab('schedule'); });
        tabBtnStudio.addEventListener('click', function() { switchSectionTab('studio'); });
    }

    // 2. Preset Mode Switcher inside Creative Studio
    var btnModePuja = document.getElementById('btn-mode-puja');
    var btnModeRashi = document.getElementById('btn-mode-rashi');
    var wrapPuja = document.getElementById('wrapper-puja-presets');
    var wrapRashi = document.getElementById('wrapper-rashi-presets');
    var topicInput = document.getElementById('creative-topic');
    var selectRashi = document.getElementById('select-rashi');

    if (btnModePuja && btnModeRashi) {
        btnModePuja.addEventListener('click', function() {
            wrapPuja.classList.remove('hidden');
            wrapRashi.classList.add('hidden');
            btnModePuja.classList.add('bg-slate-900', 'text-white', 'border-slate-900');
            btnModePuja.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
            btnModeRashi.classList.remove('bg-slate-900', 'text-white', 'border-slate-900');
            btnModeRashi.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
            updatePromptPreview();
        });

        btnModeRashi.addEventListener('click', function() {
            wrapRashi.classList.remove('hidden');
            wrapPuja.classList.add('hidden');
            btnModeRashi.classList.add('bg-slate-900', 'text-white', 'border-slate-900');
            btnModeRashi.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
            btnModePuja.classList.remove('bg-slate-900', 'text-white', 'border-slate-900');
            btnModePuja.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
            topicInput.value = selectRashi.value;
            updatePromptPreview();
        });

        if (selectRashi) {
            selectRashi.addEventListener('change', function() {
                topicInput.value = this.value;
                updatePromptPreview();
            });
        }
    }

    // 3. Webhook Drawer Toggle
    var toggleWebhookBtn = document.getElementById('toggle-webhook-config');
    var webhookDrawer = document.getElementById('webhook-config-drawer');
    if (toggleWebhookBtn && webhookDrawer) {
        toggleWebhookBtn.addEventListener('click', function() {
            webhookDrawer.classList.toggle('hidden');
        });
    }

    // 4. Creative Studio Logic
    var styleSelect = document.getElementById('creative-style');
    var ratioSelect = document.getElementById('creative-ratio');
    var btnText = document.getElementById('generate-btn-text');

    function syncBtnLabel() {
        if (!styleSelect || !btnText) return;
        if (styleSelect.value === 'video_reel') {
            btnText.textContent = 'Generate Cinematic Video Reel (9:16)';
            ratioSelect.value = '9:16';
        } else {
            btnText.textContent = 'Generate Photorealistic Vedic Image (Flux)';
        }
    }
    if (styleSelect) {
        styleSelect.addEventListener('change', function() {
            syncBtnLabel();
            updatePromptPreview();
        });
        syncBtnLabel();
    }

    // Quick topic pills
    document.querySelectorAll('.quick-topic-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            topicInput.value = this.getAttribute('data-topic');
            updatePromptPreview();
        });
    });

    // Festival row quick button -> Switch to Studio & Pre-fill
    document.querySelectorAll('.btn-create-for-fest').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var name = this.getAttribute('data-name');
            var deity = this.getAttribute('data-deity');
            topicInput.value = deity ? (name + ' (' + deity + ')') : name;
            switchSectionTab('studio');
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
        if (!topicInput || !styleSelect || !ratioSelect) return;
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
                var pEl = document.getElementById('inspector-positive');
                var nEl = document.getElementById('inspector-negative');
                if (pEl) pEl.textContent = d.data.prompt;
                if (nEl) nEl.textContent = d.data.negative_prompt;
            }
        });
    }

    // Generate Visual via OmniRoute
    var btnGenerate = document.getElementById('btn-generate-creative');
    var previewEmpty = document.getElementById('creative-preview-empty');
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

            if (previewEmpty) previewEmpty.classList.add('hidden');
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
                btnGenerate.innerHTML = '<i data-lucide="sparkles" class="w-4 h-4 mr-1 text-amber-400"></i> ' + (isVideo ? 'Generate Cinematic Video Reel' : 'Generate Photorealistic Vedic Image (Flux)');
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
                btnGenerate.innerHTML = '<i data-lucide="sparkles" class="w-4 h-4 mr-1 text-amber-400"></i> ' + (isVideo ? 'Generate Cinematic Video Reel' : 'Generate Photorealistic Vedic Image (Flux)');
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
                btnAttach.innerHTML = '<i data-lucide="paperclip" class="w-3.5 h-3.5 mr-1"></i> Attach';
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
            b.innerHTML = '<span class="animate-spin inline-block mr-1">⏳</span> Refreshing…';
            fetch('{{ route("growth-os.action") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'seed_festivals' })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                b.disabled = false;
                b.innerHTML = '<i data-lucide="refresh-cw" class="w-3 h-3 mr-1"></i> Refresh';
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
                b.innerHTML = '<i data-lucide="wand" class="w-3.5 h-3.5 mr-1"></i> Auto-Schedule Wishes';
                if (typeof toastr !== 'undefined') toastr.success(d.reply || 'Wishes queued!');
                setTimeout(function() { window.location.reload(); }, 1200);
            })
            .catch(function(err) {
                b.disabled = false;
                b.innerHTML = '<i data-lucide="wand" class="w-3.5 h-3.5 mr-1"></i> Auto-Schedule Wishes';
                if (typeof toastr !== 'undefined') toastr.error(err.message);
            });
        });
    }

    // Save & Test Social Webhook
    var btnSaveWebhook = document.getElementById('btn-save-social-webhook');
    var btnTestWebhook = document.getElementById('btn-test-social-webhook');
    var webhookInput = document.getElementById('social-webhook-input');

    if (btnSaveWebhook) {
        btnSaveWebhook.addEventListener('click', function() {
            var url = webhookInput.value.trim();
            btnSaveWebhook.disabled = true;
            btnSaveWebhook.textContent = 'Saving…';
            fetch('{{ route("growth-os.action") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'save_social_webhook', webhook_url: url })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                btnSaveWebhook.disabled = false;
                btnSaveWebhook.textContent = 'Save Webhook';
                if (d.ok) {
                    if (typeof toastr !== 'undefined') toastr.success(d.message || 'Saved!');
                    setTimeout(function() { window.location.reload(); }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(d.error || 'Failed');
                }
            });
        });
    }

    if (btnTestWebhook) {
        btnTestWebhook.addEventListener('click', function() {
            var url = webhookInput.value.trim();
            btnTestWebhook.disabled = true;
            btnTestWebhook.textContent = 'Pinging…';
            fetch('{{ route("growth-os.action") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'test_social_webhook', webhook_url: url })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                btnTestWebhook.disabled = false;
                btnTestWebhook.textContent = 'Test Ping';
                if (d.ok) {
                    if (typeof toastr !== 'undefined') toastr.success(d.message || 'Ping successful!');
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(d.error || 'Ping failed');
                }
            })
            .catch(function(err) {
                btnTestWebhook.disabled = false;
                btnTestWebhook.textContent = 'Test Ping';
                if (typeof toastr !== 'undefined') toastr.error(err.message);
            });
        });
    }
});
</script>
@endsection
