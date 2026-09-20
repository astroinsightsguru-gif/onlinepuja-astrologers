<?php

namespace App\Http\Controllers\Admin;

use App\AI\ProviderManager;
use App\Brain\ContentPipeline;
use App\Brain\GoalPlanner;
use App\Brain\KeywordResearch;
use App\Brain\SiteAuditor;
use App\Brain\Vault;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrowthOsController extends Controller
{
    public function __construct(
        protected ProviderManager $ai,
        protected KeywordResearch $research,
        protected ContentPipeline $pipeline,
        protected SiteAuditor $auditor
    ) {
        $this->middleware('auth');
    }

    /** 1. Growth & Goals Dashboard */
    public function dashboard()
    {
        $goal = DB::table('goals')->where('status', 'active')->first();
        $snapshots = [];
        $pace = 0;
        $expected = 0;
        $totalTraffic = 0;
        $elapsed = 0;
        $horizon = 50;

        if ($goal) {
            $snapshots = DB::table('goal_snapshots')
                ->where('goal_id', $goal->id)
                ->orderBy('date', 'ASC')
                ->get();

            $totalTraffic = (int) $snapshots->sum('value');
            $startDate = \Carbon\Carbon::parse($goal->start_date);
            $deadline = \Carbon\Carbon::parse($goal->deadline);
            $elapsed = max(1, $startDate->diffInDays(now()));
            $horizon = max(1, $startDate->diffInDays($deadline) + 1);
            $expected = (int) round(($elapsed / $horizon) * $goal->target);
            $pace = $expected > 0 ? min(999, round(($totalTraffic / $expected) * 100)) : 100;
        }

        $stats = [
            'total_keywords' => DB::table('keywords')->count(),
            'easy_keywords' => DB::table('keywords')->where('difficulty', '<=', 45)->count(),
            'drafted_content' => DB::table('content_plans')->where('status', 'drafted')->count(),
            'approved_content' => DB::table('content_plans')->where('status', 'approved')->count(),
            'published_content' => DB::table('content_plans')->where('status', 'published')->count(),
            'open_seo_issues' => DB::table('seo_issues')->where('status', 'open')->count(),
            'fixed_seo_issues' => DB::table('seo_issues')->where('status', 'fixed')->count(),
            'festivals_seeded' => DB::table('hindu_festivals')->count(),
            'festivals_scheduled' => DB::table('hindu_festivals')->where('scheduled', true)->count(),
            'ai_models_count' => DB::table('ai_models')->count(),
        ];

        $recentChats = DB::table('brain_chats')->orderByDesc('id')->limit(8)->get();

        return view('pages.growth-os.dashboard', compact('goal', 'snapshots', 'pace', 'expected', 'totalTraffic', 'elapsed', 'horizon', 'stats', 'recentChats'));
    }

    /** 2. SEO Keywords & Silo Matrix */
    public function keywords(Request $request)
    {
        $query = DB::table('keywords');

        if ($request->filled('cluster')) {
            $query->where('cluster', $request->cluster);
        }
        if ($request->filled('intent')) {
            $query->where('intent', $request->intent);
        }
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where('phrase', 'like', "%{$s}%");
        }

        $keywords = $query->orderByDesc('id')->paginate(25);
        $clusters = DB::table('keywords')->select('cluster', DB::raw('count(*) as count'))->groupBy('cluster')->orderByDesc('count')->get();

        return view('pages.growth-os.keywords', compact('keywords', 'clusters'));
    }

    /** Store new manual keyword or trigger research */
    public function storeKeyword(Request $request)
    {
        $request->validate([
            'phrase' => 'required|string|max:255',
            'cluster' => 'nullable|string|max:100',
            'intent' => 'nullable|in:commercial,informational,transactional',
        ]);

        $phrase = trim(mb_strtolower($request->phrase));
        $cluster = trim($request->cluster ?: 'general');
        $intent = $request->intent ?: (str_contains($phrase, 'book') || str_contains($phrase, 'online') || str_contains($phrase, 'price') ? 'commercial' : 'informational');

        if (! DB::table('keywords')->where('phrase', $phrase)->exists()) {
            DB::table('keywords')->insert([
                'phrase' => $phrase,
                'source' => 'manual-admin',
                'cluster' => $cluster,
                'intent' => $intent,
                'difficulty' => random_int(25, 50),
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return back()->with('success', "Keyword \"{$phrase}\" added successfully!");
        }

        return back()->with('error', 'Keyword already exists in database.');
    }

    /** 3. Content Pipeline Kanban */
    public function contentPipeline()
    {
        $ideas = DB::table('content_plans')->where('status', 'idea')->orderByDesc('id')->limit(20)->get();
        $drafted = DB::table('content_plans')->where('status', 'drafted')->orderByDesc('id')->limit(20)->get();
        $criticFailed = DB::table('content_plans')->where('status', 'critic_failed')->orderByDesc('id')->limit(20)->get();
        $approved = DB::table('content_plans')->where('status', 'approved')->orderByDesc('id')->limit(20)->get();
        $published = DB::table('content_plans')->where('status', 'published')->orderByDesc('id')->limit(20)->get();

        return view('pages.growth-os.content', compact('ideas', 'drafted', 'criticFailed', 'approved', 'published'));
    }

    /** Update content plan status */
    public function updateContentStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:idea,drafted,critic_failed,approved,published,deleted']);

        if ($request->status === 'deleted') {
            DB::table('content_plans')->where('id', $id)->delete();
            return response()->json(['ok' => true, 'message' => 'Content plan deleted.']);
        }

        $updates = ['status' => $request->status, 'updated_at' => now()];

        if ($request->status === 'published') {
            // Auto publish to blog if payload has HTML
            Artisan::call('content:publish', ['id' => $id]);
            return response()->json(['ok' => true, 'message' => 'Content published successfully to live blog!']);
        }

        DB::table('content_plans')->where('id', $id)->update($updates);

        return response()->json(['ok' => true, 'message' => "Status updated to {$request->status}."]);
    }

    /** 4. SEO Health Auditor & God-Mode Fixer */
    public function seoAuditor()
    {
        $issues = DB::table('seo_issues')->orderByDesc('id')->paginate(30);
        $issueStats = DB::table('seo_issues')
            ->select('type', 'status', DB::raw('count(*) as count'))
            ->groupBy('type', 'status')
            ->get();

        $openCount = DB::table('seo_issues')->where('status', 'open')->count();
        $fixedCount = DB::table('seo_issues')->where('status', 'fixed')->count();

        return view('pages.growth-os.seo', compact('issues', 'issueStats', 'openCount', 'fixedCount'));
    }

    /** Revert a fixed SEO issue */
    public function revertSeoIssue($id)
    {
        $issue = DB::table('seo_issues')->where('id', $id)->first();

        if (! $issue || ! $issue->revert_payload) {
            return back()->with('error', 'No revert payload available for this issue.');
        }

        try {
            $payload = json_decode((string) $issue->revert_payload, true);
            if (! empty($payload['table']) && ! empty($payload['id']) && ! empty($payload['field'])) {
                DB::table($payload['table'])
                    ->where('id', $payload['id'])
                    ->update([$payload['field'] => $payload['old_value'] ?? '']);
            }

            DB::table('seo_issues')->where('id', $id)->update([
                'status' => 'open',
                'reverted_at' => now(),
                'updated_at' => now(),
            ]);

            return back()->with('success', 'Issue reverted successfully to original state.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Revert failed: ' . $e->getMessage());
        }
    }

    /** 5. Social Media & Hindu Festival Scheduler */
    public function socialScheduler()
    {
        $festivals = DB::table('hindu_festivals')->orderBy('date', 'ASC')->get();
        $socialPosts = DB::table('content_plans')
            ->where('channel', 'social')
            ->orderByDesc('id')
            ->get();

        return view('pages.growth-os.social', compact('festivals', 'socialPosts'));
    }

    /** 6. AI Engine & Model Vault */
    public function aiEngine()
    {
        $creds = Vault::aiCredentials();
        $models = DB::table('ai_models')->orderBy('provider')->orderBy('model_id')->get();
        $providers = [
            'omniroute' => [
                'name' => 'OmniRoute Gateway',
                'url' => $creds['omniroute_url'] ?? 'https://ai.vmstudio.digital',
                'configured' => ! empty($creds['omniroute_key']),
                'type' => 'Self-Hosted AI Router',
            ],
            'openai' => [
                'name' => 'OpenAI',
                'url' => 'https://api.openai.com/v1',
                'configured' => ! empty($creds['openai_key']),
                'type' => 'Direct API (GPT-4o, etc.)',
            ],
            'gemini' => [
                'name' => 'Google Gemini',
                'url' => 'https://generativelanguage.googleapis.com/v1beta/openai',
                'configured' => ! empty($creds['gemini_key']),
                'type' => 'Google AI Studio',
            ],
            'openrouter' => [
                'name' => 'OpenRouter',
                'url' => 'https://openrouter.ai/api/v1',
                'configured' => ! empty($creds['openrouter_key']),
                'type' => 'Multi-Model Proxy',
            ],
            'pollinations' => [
                'name' => 'Pollinations AI',
                'url' => 'https://text.pollinations.ai',
                'configured' => true,
                'type' => 'Free / Keyless Fallback Tier',
            ],
        ];

        return view('pages.growth-os.ai', compact('creds', 'models', 'providers'));
    }

    /** Save Vault settings (API keys) */
    public function saveVault(Request $request)
    {
        $keys = [
            'omniroute_url', 'omniroute_key', 'openai_key',
            'openrouter_key', 'gemini_key', 'ai_text_chain', 'ai_image_chain',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                $val = trim((string) $request->input($key));
                Vault::set($key, $val === '' ? null : $val, true);
            }
        }

        return back()->with('success', 'Vault credentials updated securely!');
    }

    /** AJAX Action Runner for Background Tools */
    public function runAction(Request $request)
    {
        $action = $request->input('action');
        $output = '';

        try {
            switch ($action) {
                case 'sync_models':
                    Artisan::call('ai:sync-models');
                    $output = Artisan::output() ?: 'AI models synced successfully.';
                    break;
                case 'seo_scan':
                    Artisan::call('seo:scan', ['--max' => 10]);
                    $output = Artisan::output() ?: 'SEO scan completed.';
                    break;
                case 'seo_fix':
                    Artisan::call('seo:fix', ['--god' => true, '--limit' => 5]);
                    $output = Artisan::output() ?: 'SEO God-mode fix applied.';
                    break;
                case 'seo_research':
                    Artisan::call('seo:research');
                    $output = Artisan::output() ?: 'Keyword research completed.';
                    break;
                case 'generate_content':
                    Artisan::call('content:generate', ['--limit' => 1]);
                    $output = Artisan::output() ?: 'New content draft generated with Critic review.';
                    break;
                case 'seed_festivals':
                    Artisan::call('brain:seed-festivals');
                    $output = Artisan::output() ?: 'Hindu festivals seeded.';
                    break;
                default:
                    return response()->json(['ok' => false, 'message' => "Unknown action: {$action}"], 400);
            }

            return response()->json(['ok' => true, 'output' => trim($output)]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
