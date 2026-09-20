import 'package:flutter/material.dart';

import '../checkout/checkout_screen.dart';
import '../cosmic_ai_screen.dart';
import '../horoscope/daily_horoscope_screen.dart';
import '../kundli/kundli_list_screen.dart';
import '../kundli/kundli_matching_screen.dart';
import '../mall/mall_screen.dart';
import '../panchang/panchang_screen.dart';
import '../puja/puja_list_screen.dart';

/// Explore hub: spiritual tools & shop entries (legacy home feature grid).
class ExploreScreen extends StatelessWidget {
  const ExploreScreen({super.key});

  static final List<(String, String, IconData, Widget)> _features = [
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
      'Cosmic AI',
      'Free AI astrologer chat',
      Icons.auto_awesome,
      const CosmicAiScreen(),
    ),
  ];

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(title: const Text('Explore')),
      body: GridView.builder(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          mainAxisSpacing: 12,
          crossAxisSpacing: 12,
          childAspectRatio: 1.55,
        ),
        itemCount: _features.length,
        itemBuilder: (context, i) {
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
                  Text(title,
                      style: Theme.of(context)
                          .textTheme
                          .titleMedium
                          ?.copyWith(fontWeight: FontWeight.w800)),
                  Text(subtitle,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: Theme.of(context)
                          .textTheme
                          .bodySmall
                          ?.copyWith(color: scheme.outline)),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
