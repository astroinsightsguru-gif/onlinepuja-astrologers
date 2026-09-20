import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

/// Brand identity widgets (logo lockup, zodiac wheel strip).
class Brand {
  Brand._();

  static Widget logo({double size = 96, bool showName = true}) => Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: size,
            height: size,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [AppTheme.brandSaffron, AppTheme.gold],
              ),
              borderRadius: BorderRadius.circular(size * 0.28),
              boxShadow: [
                BoxShadow(
                  color: AppTheme.brandSaffron.withValues(alpha: 0.35),
                  blurRadius: 24,
                  offset: const Offset(0, 10),
                ),
              ],
            ),
            child: Icon(
              Icons.self_improvement,
              size: size * 0.6,
              color: Colors.white,
            ),
          ),
          if (showName) ...[
            const SizedBox(height: 18),
            RichText(
              text: const TextSpan(
                style: TextStyle(
                  fontSize: 26,
                  fontWeight: FontWeight.w800,
                  letterSpacing: -0.5,
                  color: AppTheme.brandDeep,
                ),
                children: [
                  TextSpan(text: 'Online'),
                  TextSpan(
                    text: 'Puja',
                    style: TextStyle(color: AppTheme.gold),
                  ),
                ],
              ),
            ),
          ],
        ],
      );

  /// Twelve zodiac signs strip (light ↕ modern wheel).
  static Widget zodiacStrip({
    required ValueChanged<String> onSignTap,
    String selected = '',
  }) {
    const signs = [
      'Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo',
      'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces',
    ];
    const glyphs = [
      '♈', '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓',
    ];
    return SizedBox(
      height: 88,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
        itemCount: signs.length,
        separatorBuilder: (_, _) => const SizedBox(width: 10),
        itemBuilder: (context, i) {
          final isSel = selected == signs[i];
          final scheme = Theme.of(context).colorScheme;
          return InkWell(
            borderRadius: BorderRadius.circular(14),
            onTap: () => onSignTap(signs[i]),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 180),
              width: 74,
              decoration: BoxDecoration(
                color: isSel ? scheme.primaryContainer : scheme.surface,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(
                  color: isSel ? scheme.primary : scheme.outline.withValues(alpha: 0.5),
                ),
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(glyphs[i],
                      style: TextStyle(
                          fontSize: 24,
                          color: isSel ? scheme.primary : scheme.onSurface)),
                  const SizedBox(height: 4),
                  Text(signs[i],
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: Theme.of(context).textTheme.labelSmall),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
