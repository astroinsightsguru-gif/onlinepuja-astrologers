import 'package:flutter/material.dart';

/// Design tokens and styling for the Astrologer Partner App.
/// Combines Vedic sacred aesthetics (saffron, turmeric gold, vermilion)
/// with a sleek, executive dark & light UI.
class PartnerTheme {
  PartnerTheme._();

  // Core Brand Colors
  static const Color saffron = Color(0xFFF57C00);
  static const Color saffronLight = Color(0xFFFF9800);
  static const Color saffronDark = Color(0xFFE65100);
  static const Color gold = Color(0xFFD4AF37);
  static const Color goldLight = Color(0xFFF3C644);
  static const Color goldDark = Color(0xFFB38914);

  // Status & Utility Colors
  static const Color emerald = Color(0xFF10B981);
  static const Color emeraldLight = Color(0xFF34D399);
  static const Color emeraldDark = Color(0xFF059669);
  static const Color crimson = Color(0xFFE11D48);
  static const Color crimsonLight = Color(0xFFFB7185);
  static const Color sky = Color(0xFF0EA5E9);
  static const Color purple = Color(0xFF8B5CF6);
  static const Color amber = Color(0xFFF59E0B);

  // Dark Theme Surfaces
  static const Color darkBg = Color(0xFF0F0D15);
  static const Color darkSurface = Color(0xFF171421);
  static const Color darkCard = Color(0xFF1F1B2C);
  static const Color darkCardHover = Color(0xFF282338);
  static const Color darkBorder = Color(0xFF302B42);

  // Gradients
  static const LinearGradient saffronGradient = LinearGradient(
    colors: [Color(0xFFFF851B), Color(0xFFE65100)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient goldGradient = LinearGradient(
    colors: [Color(0xFFF6D365), Color(0xFFFDA085)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient luxuryGold = LinearGradient(
    colors: [Color(0xFFFFDF7A), Color(0xFFD4AF37), Color(0xFFA67C1E)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient darkCardGradient = LinearGradient(
    colors: [Color(0xFF231E31), Color(0xFF181523)],
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
  );

  static const LinearGradient emeraldGradient = LinearGradient(
    colors: [Color(0xFF10B981), Color(0xFF047857)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient crimsonGradient = LinearGradient(
    colors: [Color(0xFFF43F5E), Color(0xFFBE123C)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient cosmicDarkGradient = LinearGradient(
    colors: [Color(0xFF2A1B44), Color(0xFF120E1C)],
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
  );

  // Box Shadows
  static List<BoxShadow> glow(Color color, {double blur = 16, double spread = 0}) => [
        BoxShadow(
          color: color.withValues(alpha: 0.35),
          blurRadius: blur,
          spreadRadius: spread,
          offset: const Offset(0, 4),
        ),
      ];

  static List<BoxShadow> cardShadow(bool dark) => [
        BoxShadow(
          color: dark
              ? Colors.black.withValues(alpha: 0.35)
              : Colors.black.withValues(alpha: 0.06),
          blurRadius: 18,
          offset: const Offset(0, 6),
        ),
      ];
}
