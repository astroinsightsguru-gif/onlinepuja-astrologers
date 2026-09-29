import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import '../blog/blog_screen.dart';
import '../charity/annadaan_screen.dart';
import '../cosmic_ai_screen.dart';
import '../darshan/live_darshan_screen.dart';
import '../horoscope/daily_horoscope_screen.dart';
import '../japa/japa_mala_screen.dart';
import '../kundli/kundli_list_screen.dart';
import '../kundli/kundli_matching_screen.dart';
import '../mall/mall_screen.dart';
import '../panchang/panchang_screen.dart';
import '../puja/puja_list_screen.dart';
import 'prashna_oracle_screen.dart';
import 'swapna_shastra_screen.dart';

/// Explore hub: spiritual tools & shop entries (legacy home feature grid).
class ExploreScreen extends StatefulWidget {
  const ExploreScreen({super.key});

  @override
  State<ExploreScreen> createState() => _ExploreScreenState();
}

class _ExploreScreenState extends State<ExploreScreen> {

  static final List<(String, String, IconData, Widget)> _features = [
    (
      'Live Darshan',
      'Kashi, Ganga Aarti & holy shrines',
      Icons.temple_hindu_rounded,
      const LiveDarshanScreen(),
    ),
    (
      'Kundli',
      'Birth chart, planets & dasha',
      Icons.auto_graph_rounded,
      const KundliListScreen(),
    ),
    (
      'Matching',
      'Guna milan for marriage',
      Icons.favorite_rounded,
      const KundliMatchingScreen(),
    ),
    (
      'Rudraksha Japa',
      '108 beads digital mala counter',
      Icons.fingerprint_rounded,
      const JapaMalaScreen(),
    ),
    (
      'Panchang',
      "Today's almanac",
      Icons.wb_twilight_rounded,
      const PanchangScreen(),
    ),
    (
      'Horoscope',
      'Daily predictions by sign',
      Icons.nightlight_round,
      const DailyHoroscopeScreen(),
    ),
    (
      'Prashna Oracle',
      'Instant Horary answers & remedies',
      Icons.psychology_alt_rounded,
      const PrashnaOracleScreen(),
    ),
    (
      'Swapna Shastra',
      'Decode spiritual dream omens',
      Icons.bedtime_rounded,
      const SwapnaShastraScreen(),
    ),
    (
      'Puja',
      'Book sacred pujas',
      Icons.local_fire_department_rounded,
      const PujaListScreen(),
    ),
    (
      'AstroMall',
      'Gemstones & spiritual items',
      Icons.storefront_rounded,
      const MallScreen(),
    ),
    (
      'Annadaan & Seva',
      'Gau seva & holy charity',
      Icons.volunteer_activism_rounded,
      const AnnadaanScreen(),
    ),
    (
      'Cosmic AI',
      'Free AI astrologer chat',
      Icons.auto_awesome,
      const CosmicAiScreen(),
    ),
    (
      'Astro Blogs',
      'Spiritual articles & wisdom',
      Icons.menu_book_rounded,
      const BlogScreen(),
    ),
  ];

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(title: const Text('Explore')),
      body: CustomScrollView(
        slivers: [
          // 1. Live Rahu Kaal & Shubh Muhurat Radar
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
              child: _muhuratRadar(context),
            ),
          ),

          // 2. Daily Cosmic Voice Briefing Player
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 4, 16, 14),
              child: _cosmicVoiceCard(context),
            ),
          ),

          // 3. Main Grid of Features
          SliverPadding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
            sliver: SliverGrid(
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                mainAxisSpacing: 12,
                crossAxisSpacing: 12,
                childAspectRatio: 1.18,
              ),
              delegate: SliverChildBuilderDelegate(
                (context, i) {
                  final (title, subtitle, icon, screen) = _features[i];
                  return InkWell(
                    borderRadius: BorderRadius.circular(16),
                    onTap: () => Navigator.of(context)
                        .push(MaterialPageRoute(builder: (_) => screen)),
                    child: Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: scheme.surface,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(
                            color: scheme.outline.withValues(alpha: 0.35)),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: scheme.primaryContainer,
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Icon(icon, color: scheme.primary, size: 22),
                          ),
                          const SizedBox(height: 10),
                          Text(
                            title,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: Theme.of(context)
                                .textTheme
                                .titleMedium
                                ?.copyWith(fontWeight: FontWeight.w800),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            subtitle,
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: Theme.of(context)
                                .textTheme
                                .bodySmall
                                ?.copyWith(color: scheme.outline),
                          ),
                        ],
                      ),
                    ),
                  );
                },
                childCount: _features.length,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _muhuratRadar(BuildContext context) {
    final now = DateTime.now();
    final hour = now.hour;
    final isAuspicious = hour >= 11 && hour <= 13; // Abhijit Muhurat window

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
      decoration: BoxDecoration(
        color: isAuspicious
            ? const Color(0xFF059669).withValues(alpha: 0.12)
            : Colors.amber.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: isAuspicious
              ? Colors.greenAccent.withValues(alpha: 0.4)
              : Colors.amber.withValues(alpha: 0.4),
        ),
      ),
      child: Row(
        children: [
          Icon(
            isAuspicious ? Icons.verified_rounded : Icons.schedule_rounded,
            color: isAuspicious ? Colors.green : Colors.amber.shade700,
            size: 22,
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  isAuspicious
                      ? 'Abhijit Muhurat Active · Highly Auspicious'
                      : 'Rahu Kaal Radar · Favorable Window Ahead',
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 12,
                    color: isAuspicious ? Colors.green.shade800 : Colors.amber.shade900,
                  ),
                ),
                Text(
                  isAuspicious
                      ? 'Ideal time for new investments, prayers, and calls'
                      : 'Next auspicious window: 11:45 AM – 12:35 PM',
                  style: const TextStyle(fontSize: 11, color: Colors.black87),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _cosmicVoiceCard(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [AppTheme.brandDeep, AppTheme.brandDeep.withValues(alpha: 0.88)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.brandSaffron.withValues(alpha: 0.4)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.08),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: AppTheme.brandSaffron.withValues(alpha: 0.2),
              shape: BoxShape.circle,
              border: Border.all(color: AppTheme.brandSaffron.withValues(alpha: 0.4)),
            ),
            child: const Text('🕉️', style: TextStyle(fontSize: 20)),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text(
                  'Daily Cosmic Vibrations ✨',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13),
                ),
                const SizedBox(height: 2),
                const Text(
                  'Mantra: ॐ नमः शिवाय · Chant 11x for peace',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(color: Colors.white70, fontSize: 11),
                ),
              ],
            ),
          ),
          const SizedBox(width: 8),
          FilledButton.tonal(
            style: FilledButton.styleFrom(
              backgroundColor: AppTheme.brandSaffron,
              foregroundColor: Colors.white,
              visualDensity: VisualDensity.compact,
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
            ),
            onPressed: () {
              Navigator.of(context).push(
                MaterialPageRoute(builder: (_) => const JapaMalaScreen()),
              );
            },
            child: const Text('Chant', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }
}
