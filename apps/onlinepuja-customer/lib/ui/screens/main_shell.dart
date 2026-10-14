import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import '../theme/customer_theme.dart';

import 'astrologer/astrologers_screen.dart';
import 'home/consult_home_screen.dart';
import 'mall/mall_screen.dart';
import 'profile/profile_screen.dart';
import 'puja/puja_list_screen.dart';

/// Scope to allow children to switch tabs programmatically:
/// 0: Home | 1: Consult | 2: Pujas | 3: Vedic Store | 4: Account
class MainShellScope extends InheritedWidget {
  final void Function(int) selectTab;
  const MainShellScope({
    super.key,
    required this.selectTab,
    required super.child,
  });

  static MainShellScope? of(BuildContext context) =>
      context.dependOnInheritedWidgetOfExactType<MainShellScope>();

  @override
  bool updateShouldNotify(MainShellScope oldWidget) => false;
}

/// Clean, Modern 5-Tab Sacred Navigation Shell
/// Tab 0: Home (Live discovery, holy banners, sacred daily strip)
/// Tab 1: Consult (Dedicated Astrologers directory with fast filters & live calling)
/// Tab 2: Pujas (Sacred Temple Pujas, Darshan & Prasad)
/// Tab 3: Vedic Store (Sacred Store: energized Rudraksha, gems, yantras & items)
/// Tab 4: Account (Wallet, Booking History, Saved Kundlis, Settings)
class MainShell extends StatefulWidget {
  const MainShell({super.key});

  static const route = '/shell';

  @override
  State<MainShell> createState() => _MainShellState();
}

class _MainShellState extends State<MainShell> {
  int _tab = 0;

  void _selectTab(int index) {
    if (!mounted || _tab == index) return;
    setState(() => _tab = index);
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return MainShellScope(
      selectTab: _selectTab,
      child: Scaffold(
        body: ValueListenableBuilder<AppLanguage>(
          valueListenable: LocaleManager.instance.currentLanguage,
          builder: (context, lang, _) => KeyedSubtree(
            key: ValueKey('shell_lang_${lang.code}'),
            child: IndexedStack(
              index: _tab,
              children: [
                const ConsultHomeScreen(),
                const AstrologersScreen(),
                const PujaListScreen(),
                const MallScreen(),
                const ProfileScreen(),
              ],
            ),
          ),
        ),
        bottomNavigationBar: Container(
          decoration: BoxDecoration(
            color: isDark ? CustomerTheme.cosmicCardDark : Colors.white,
            border: Border(
              top: BorderSide(
                color: isDark
                    ? CustomerTheme.brandGold.withValues(alpha: 0.15)
                    : CustomerTheme.lightBorder.withValues(alpha: 0.7),
                width: 1,
              ),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: isDark ? 0.35 : 0.06),
                blurRadius: 16,
                offset: const Offset(0, -4),
              ),
            ],
          ),
          child: SafeArea(
            top: false,
            child: ValueListenableBuilder<AppLanguage>(
              valueListenable: LocaleManager.instance.currentLanguage,
              builder: (context, lang, _) {
                final isHi = lang != AppLanguage.en;
                return SizedBox(
                  height: 64,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceAround,
                    children: [
                      _navItem(
                        index: 0,
                        icon: Icons.home_outlined,
                        activeIcon: Icons.home_rounded,
                        label: isHi ? 'होम' : 'Home',
                        isDark: isDark,
                      ),
                      _navItem(
                        index: 1,
                        icon: Icons.psychology_outlined,
                        activeIcon: Icons.psychology_rounded,
                        label: AppStrings.astrologers,
                        isDark: isDark,
                        badge: 'LIVE',
                      ),
                      _navItem(
                        index: 2,
                        icon: Icons.local_fire_department_outlined,
                        activeIcon: Icons.local_fire_department_rounded,
                        label: AppStrings.puja,
                        isDark: isDark,
                      ),
                      _navItem(
                        index: 3,
                        icon: Icons.storefront_outlined,
                        activeIcon: Icons.storefront_rounded,
                        label: isHi ? 'वैदिक भण्डार' : 'Sacred Store',
                        isDark: isDark,
                      ),
                      _navItem(
                        index: 4,
                        icon: Icons.person_outline_rounded,
                        activeIcon: Icons.person_rounded,
                        label: AppStrings.profile,
                        isDark: isDark,
                      ),
                    ],
                  ),
                );
              },
            ),
          ),
        ),
      ),
    );
  }

  Widget _navItem({
    required int index,
    required IconData icon,
    required IconData activeIcon,
    required String label,
    required bool isDark,
    String? badge,
  }) {
    final isSelected = _tab == index;

    return Expanded(
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () => _selectTab(index),
          borderRadius: BorderRadius.circular(16),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Stack(
                clipBehavior: Clip.none,
                children: [
                  AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                    decoration: BoxDecoration(
                      color: isSelected
                          ? CustomerTheme.brandSaffron.withValues(alpha: isDark ? 0.22 : 0.12)
                          : Colors.transparent,
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Icon(
                      isSelected ? activeIcon : icon,
                      size: 21,
                      color: isSelected
                          ? CustomerTheme.brandSaffron
                          : (isDark ? Colors.white60 : const Color(0xFF64748B)),
                    ),
                  ),
                  if (badge != null && !isSelected)
                    Positioned(
                      top: -2,
                      right: -3,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 1),
                        decoration: BoxDecoration(
                          gradient: CustomerTheme.saffronGradient,
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          badge,
                          style: const TextStyle(
                            fontSize: 7.5,
                            fontWeight: FontWeight.w900,
                            color: Colors.white,
                          ),
                        ),
                      ),
                    ),
                ],
              ),
              const SizedBox(height: 2),
              Text(
                label,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                  fontSize: 10.5,
                  fontWeight: isSelected ? FontWeight.w800 : FontWeight.w500,
                  color: isSelected
                      ? CustomerTheme.brandSaffron
                      : (isDark ? Colors.white70 : const Color(0xFF64748B)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
