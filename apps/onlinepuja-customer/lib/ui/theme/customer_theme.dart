import 'package:flutter/material.dart';

/// Online Puja Customer Luxury Sacred Vedic Design System
class CustomerTheme {
  CustomerTheme._();

  // Sacred Vedic Brand Palette
  static const Color brandSaffron = Color(0xFFF47B20);
  static const Color brandDeepSaffron = Color(0xFFEA580C);
  static const Color brandGold = Color(0xFFF59E0B);
  static const Color brandDarkGold = Color(0xFFD97706);
  static const Color brandAmber = Color(0xFFFBBF24);
  static const Color brandCrimson = Color(0xFF991B1B);
  static const Color brandKumkum = Color(0xFFB91C1C);
  static const Color sacredEmerald = Color(0xFF059669);
  static const Color sacredGreen = Color(0xFF10B981);
  static const Color sacredRose = Color(0xFFE11D48);

  // Backgrounds & Surfaces
  static const Color cosmicDark = Color(0xFF0A0F1D);
  static const Color cosmicCardDark = Color(0xFF121829);
  static const Color cosmicCardElevated = Color(0xFF1A2238);
  static const Color cosmicBorderDark = Color(0x2EF59E0B);

  static const Color lightBg = Color(0xFFFAF7F2);
  static const Color lightCard = Color(0xFFFFFFFF);
  static const Color lightBorder = Color(0xFFE8DFD3);

  // Gradients
  static const LinearGradient saffronGradient = LinearGradient(
    colors: [Color(0xFFFB923C), Color(0xFFEA580C)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient goldGradient = LinearGradient(
    colors: [Color(0xFFFDE68A), Color(0xFFF59E0B), Color(0xFFD97706)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient cosmicDarkGradient = LinearGradient(
    colors: [Color(0xFF0B1120), Color(0xFF020617)],
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
  );

  static const LinearGradient heroCosmicGradient = LinearGradient(
    colors: [Color(0xFF1E1B4B), Color(0xFF0F172A), Color(0xFF020617)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient dakshinaGradient = LinearGradient(
    colors: [Color(0xFF10B981), Color(0xFF047857)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  static const LinearGradient crimsonGradient = LinearGradient(
    colors: [Color(0xFFEF4444), Color(0xFF991B1B)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  // Color Schemes
  static const ColorScheme lightScheme = ColorScheme.light(
    primary: brandSaffron,
    onPrimary: Colors.white,
    primaryContainer: Color(0xFFFFEDD5),
    onPrimaryContainer: Color(0xFF7C2D12),
    secondary: brandGold,
    onSecondary: Colors.white,
    secondaryContainer: Color(0xFFFEF3C7),
    onSecondaryContainer: Color(0xFF78350F),
    tertiary: brandCrimson,
    onTertiary: Colors.white,
    surface: lightBg,
    onSurface: Color(0xFF1E1A16),
    surfaceContainerHighest: Color(0xFFF3EDE4),
    error: Color(0xFFDC2626),
    outline: lightBorder,
  );

  static const ColorScheme darkScheme = ColorScheme.dark(
    primary: brandSaffron,
    onPrimary: Colors.white,
    primaryContainer: Color(0xFF7C2D12),
    onPrimaryContainer: Color(0xFFFFEDD5),
    secondary: brandGold,
    onSecondary: Color(0xFF1E1A16),
    secondaryContainer: Color(0xFF78350F),
    onSecondaryContainer: Color(0xFFFEF3C7),
    tertiary: brandCrimson,
    onTertiary: Colors.white,
    surface: cosmicDark,
    onSurface: Color(0xFFF1EDE6),
    surfaceContainerHighest: cosmicCardDark,
    error: Color(0xFFF87171),
    outline: cosmicBorderDark,
  );

  static ThemeData light() => _theme(lightScheme, isDark: false);
  static ThemeData dark() => _theme(darkScheme, isDark: true);

  static ThemeData _theme(ColorScheme scheme, {required bool isDark}) {
    final base = ThemeData(
      useMaterial3: true,
      colorScheme: scheme,
      scaffoldBackgroundColor: scheme.surface,
      fontFamily: 'Roboto',
    );

    return base.copyWith(
      appBarTheme: AppBarTheme(
        backgroundColor: scheme.surface,
        foregroundColor: scheme.onSurface,
        elevation: 0,
        centerTitle: false,
        titleTextStyle: TextStyle(
          color: scheme.onSurface,
          fontSize: 18,
          fontWeight: FontWeight.w700,
          letterSpacing: -0.2,
        ),
      ),
      cardTheme: CardThemeData(
        color: isDark ? cosmicCardDark : lightCard,
        elevation: isDark ? 0 : 1,
        shadowColor: Colors.black.withValues(alpha: isDark ? 0 : 0.04),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: BorderSide(
            color: isDark
                ? brandGold.withValues(alpha: 0.15)
                : lightBorder.withValues(alpha: 0.8),
          ),
        ),
        margin: EdgeInsets.zero,
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          backgroundColor: brandSaffron,
          foregroundColor: Colors.white,
          elevation: 2,
          shadowColor: brandSaffron.withValues(alpha: 0.35),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
          textStyle: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15),
        ),
      ),
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: brandSaffron,
          side: BorderSide(color: brandSaffron.withValues(alpha: 0.6)),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 13),
          textStyle: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: isDark ? cosmicCardElevated : Colors.white,
        contentPadding:
            const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(
            color: isDark ? cosmicBorderDark : lightBorder,
          ),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(
            color: isDark ? cosmicBorderDark : lightBorder,
          ),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: brandSaffron, width: 1.8),
        ),
        labelStyle: TextStyle(
          color: isDark ? Colors.white60 : Colors.black54,
          fontSize: 14,
        ),
        hintStyle: TextStyle(
          color: isDark ? Colors.white38 : Colors.black38,
          fontSize: 14,
        ),
      ),
      bottomNavigationBarTheme: BottomNavigationBarThemeData(
        backgroundColor: isDark ? cosmicCardDark : Colors.white,
        selectedItemColor: brandSaffron,
        unselectedItemColor: isDark ? Colors.white54 : Colors.black45,
        elevation: 10,
        type: BottomNavigationBarType.fixed,
      ),
      navigationBarTheme: NavigationBarThemeData(
        backgroundColor: isDark ? cosmicCardDark : Colors.white,
        indicatorColor: brandSaffron.withValues(alpha: isDark ? 0.22 : 0.15),
        iconTheme: WidgetStateProperty.resolveWith((states) {
          if (states.contains(WidgetState.selected)) {
            return const IconThemeData(color: brandSaffron);
          }
          return IconThemeData(
            color: isDark ? Colors.white54 : const Color(0xFF6B7280),
          );
        }),
        labelTextStyle: WidgetStateProperty.resolveWith((states) {
          if (states.contains(WidgetState.selected)) {
            return const TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w700,
              color: brandSaffron,
            );
          }
          return TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w500,
            color: isDark ? Colors.white54 : const Color(0xFF6B7280),
          );
        }),
      ),
    );
  }
}
