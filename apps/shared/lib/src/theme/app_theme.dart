import 'package:flutter/material.dart';

/// Online Puja v2 design system — Material 3, saffron/gold brand seed,
/// light + dark themes (website offers Auto/Day/Night).
class AppTheme {
  AppTheme._();

  static const Color brandSaffron = Color(0xFFF4A338);
  static const Color brandDeep = Color(0xFF8E2B12);
  static const Color brandMaroon = Color(0xFF6D1B36);
  static const Color gold = Color(0xFFC9A227);
  static const Color brandGold = gold;

  static const ColorScheme lightScheme = ColorScheme.light(
    primary: brandSaffron,
    onPrimary: Colors.white,
    primaryContainer: Color(0xFFFFE9C7),
    onPrimaryContainer: Color(0xFF4A2E00),
    secondary: brandMaroon,
    onSecondary: Colors.white,
    secondaryContainer: Color(0xFFF6D9E3),
    onSecondaryContainer: Color(0xFF4A0F26),
    tertiary: brandDeep,
    onTertiary: Colors.white,
    surface: Colors.white,
    onSurface: Color(0xFF221B14),
    error: Color(0xFFB3261E),
    outline: Color(0xFFD9C6B0),
  );

  static const ColorScheme darkScheme = ColorScheme.dark(
    primary: brandSaffron,
    onPrimary: Color(0xFF2A1800),
    primaryContainer: Color(0xFF5A3D00),
    onPrimaryContainer: Color(0xFFFFE0A6),
    secondary: Color(0xFFE8A3B8),
    onSecondary: Color(0xFF4A0F26),
    secondaryContainer: Color(0xFF6D1B36),
    onSecondaryContainer: Color(0xFFF6D9E3),
    tertiary: Color(0xFFF2B27E),
    onTertiary: Color(0xFF3F1D00),
    surface: Color(0xFF1C1712),
    onSurface: Color(0xFFEDE4D8),
    error: Color(0xFFFFB4AB),
    outline: Color(0xFF5C4F41),
  );

  static ThemeData light() => _theme(lightScheme);
  static ThemeData dark() => _theme(darkScheme);

  static ThemeData _theme(ColorScheme scheme) {
    final base = ThemeData(
      useMaterial3: true,
      colorScheme: scheme,
      scaffoldBackgroundColor: scheme.surface,
    );
    return base.copyWith(
      appBarTheme: AppBarTheme(
        backgroundColor: scheme.surface,
        foregroundColor: scheme.onSurface,
        elevation: 0,
        centerTitle: true,
        titleTextStyle: TextStyle(
          color: scheme.onSurface,
          fontSize: 18,
          fontWeight: FontWeight.w700,
          letterSpacing: -0.2,
        ),
      ),
      cardTheme: CardThemeData(
        color: scheme.surface,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: BorderSide(color: scheme.outline.withValues(alpha: 0.4)),
        ),
        margin: EdgeInsets.zero,
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 14),
          textStyle: const TextStyle(fontWeight: FontWeight.w700),
        ),
      ),
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: scheme.surface,
        contentPadding:
            const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: scheme.outline),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: scheme.outline.withValues(alpha: 0.6)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: scheme.primary, width: 1.6),
        ),
      ),
      snackBarTheme: SnackBarThemeData(
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      ),
      navigationBarTheme: NavigationBarThemeData(
        height: 68,
        backgroundColor: scheme.surface,
        indicatorColor: scheme.primaryContainer,
        labelBehavior: NavigationDestinationLabelBehavior.alwaysShow,
      ),
      chipTheme: base.chipTheme.copyWith(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(10),
          side: BorderSide(color: scheme.outline.withValues(alpha: 0.5)),
        ),
      ),
    );
  }
}
