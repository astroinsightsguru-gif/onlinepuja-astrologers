<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('ai_agents')) {
            Schema::create('ai_agents', function (Blueprint $table) {
                $table->id();
                $table->string('agent_key')->unique();
                $table->string('name');
                $table->string('icon')->default('🤖');
                $table->string('role_description');
                $table->text('system_prompt');
                $table->string('language')->default('hi');
                $table->string('tone')->default('vedic_devotional');
                $table->string('model_preference')->default('omniroute');
                $table->string('cron_cadence')->default('0 5 * * *');
                $table->enum('mode', ['autonomous', 'supervised'])->default('supervised');
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_run_at')->nullable();
                $table->string('last_status')->nullable();
                $table->json('extra_config')->nullable();
                $table->timestamps();
            });

            // Seed initial 6 autonomous agents
            $defaultAgents = [
                [
                    'agent_key' => 'panchang',
                    'name' => 'Dainik Panchang Agent',
                    'icon' => '🪔',
                    'role_description' => 'Computes Tithi, Nakshatra, Muhurat, Sunrise/Sunset, Choghadiya & synthesizes 1080x1080 graphics and 1080x1920 video reels.',
                    'system_prompt' => 'You are the Vedic Dainik Panchang Agent for OnlinePuja.live. Calculate precise tithi, nakshatra, paksha, shubh muhurat (Abhijit, Amrit Kaal) and ashubh kaal (Rahu Kaal). Generate high-reverence devotional daily summaries in Hindi with Sanskrit shlokas and practical tips for devotees.',
                    'language' => 'hi',
                    'tone' => 'vedic_devotional',
                    'model_preference' => 'omniroute',
                    'cron_cadence' => '0 4 * * *',
                    'mode' => 'supervised',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'agent_key' => 'rashifal',
                    'name' => 'Rashifal & Jyotish Agent',
                    'icon' => '🔮',
                    'role_description' => 'Generates daily & weekly predictions for all 12 Rashis (Aries to Pisces), lucky colors/numbers, daily remedies (upay) and puja cross-sells.',
                    'system_prompt' => 'You are the Chief Jyotish and Horoscope Agent for OnlinePuja.live. Deliver accurate, uplifting Vedic moon-sign horoscope forecasts across all 12 Rashis. Provide personalized daily upay (mantra chanting, charity), lucky numbers and colors, and suggest relevant temple pujas for planetary dosha shanti.',
                    'language' => 'hi',
                    'tone' => 'vedic_devotional',
                    'model_preference' => 'omniroute',
                    'cron_cadence' => '30 4 * * *',
                    'mode' => 'supervised',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'agent_key' => 'seo_blog',
                    'name' => 'SEO Devotional Blog Agent',
                    'icon' => '✍️',
                    'role_description' => 'Researches high-intent keywords, writes 1,800+ word schema-optimized devotional guides, generates featured banners and auto-publishes to live blogs.',
                    'system_prompt' => 'You are the Senior Vedic Content Strategist for OnlinePuja.live. Craft in-depth, authentic, SEO-optimized guides explaining Hindu rituals, Varanasi ghat pujas, mantra vidhis, and temple histories. Include structured FAQ sections, schema markup, and clear calls to action to book sacred rituals with gotra sankalp.',
                    'language' => 'en',
                    'tone' => 'modern_inspirational',
                    'model_preference' => 'omniroute',
                    'cron_cadence' => '0 6 * * *',
                    'mode' => 'autonomous',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'agent_key' => 'festival',
                    'name' => 'Festival Campaign Orchestrator',
                    'icon' => '🎆',
                    'role_description' => 'Tracks approaching Hindu festivals and automates parallel drops at T-14, T-7, T-3, T-1, and Day-0 with multi-aspect banners, reels, and copy.',
                    'system_prompt' => 'You are the Hindu Festival Master Campaign Orchestrator for OnlinePuja.live. For every approaching festival (such as Dev Deepawali, Kartik Purnima, Mahashivratri), synthesize cohesive promotional packages: 1200x630 banners, 1080x1920 MP4 video reels, engaging WhatsApp copy, and Brevo email templates.',
                    'language' => 'hi',
                    'tone' => 'high_conversion',
                    'model_preference' => 'omniroute',
                    'cron_cadence' => '0 0 * * *',
                    'mode' => 'supervised',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'agent_key' => 'b2b',
                    'name' => 'B2B Partner Distribution Agent',
                    'icon' => '💼',
                    'role_description' => 'Scans Google Maps leads across travel agencies, tour operators and astrologers, dispatching 25% revenue share partnership pitches for Kashi pujas.',
                    'system_prompt' => 'You are the Commercial B2B Partnership Director for OnlinePuja.live. Craft professional, lucrative proposals for travel agencies, hotels, and spiritual stores across Mumbai, Surat, Ahmedabad, and Delhi. Emphasize zero-logistics 25% net profit margin on remote Varanasi puja bookings.',
                    'language' => 'en',
                    'tone' => 'high_conversion',
                    'model_preference' => 'omniroute',
                    'cron_cadence' => '0 10 * * 1,4',
                    'mode' => 'supervised',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'agent_key' => 'retention',
                    'name' => 'Puja Sankalp & Retention Agent',
                    'icon' => '📦',
                    'role_description' => 'Delivers sanctum darshan video links, Speed Post courier tracking, review collections, and personalized planetary muhurat reminders.',
                    'system_prompt' => 'You are the Devotee Care & Sacred Fulfillment Agent for OnlinePuja.live. Nurture devotees following their ritual bookings with heartfelt updates on their personal gotra sankalp, dispatch sanctified prasad tracking details, and offer muhurat reminders tailored to their family gotra vault.',
                    'language' => 'hi',
                    'tone' => 'vedic_devotional',
                    'model_preference' => 'omniroute',
                    'cron_cadence' => '0 */2 * * *',
                    'mode' => 'autonomous',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('ai_agents')->insert($defaultAgents);
        }

        if (!Schema::hasTable('ai_agent_outputs')) {
            Schema::create('ai_agent_outputs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agent_id')->constrained('ai_agents')->onDelete('cascade');
                $table->string('title');
                $table->longText('content_payload')->nullable();
                $table->string('media_type')->nullable(); // image, video_reel, blog, broadcast
                $table->string('media_url')->nullable();
                $table->enum('status', ['pending_review', 'approved', 'rejected', 'published'])->default('pending_review');
                $table->timestamp('published_at')->nullable();
                $table->json('distribution_channels')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_agent_outputs');
        Schema::dropIfExists('ai_agents');
    }
};
