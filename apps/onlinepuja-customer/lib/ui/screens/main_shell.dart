import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import '../theme/customer_theme.dart';

import 'astrologer/astrologers_screen.dart';
import 'darshan/live_darshan_screen.dart';
import 'explore/explore_screen.dart';
import 'home/consult_home_screen.dart';
import 'mall/mall_screen.dart';
import 'profile/profile_screen.dart';
import 'puja/puja_list_screen.dart';

/// Scope to allow children to switch tabs programmatically
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

/// Luxury sacred navigation shell with 7 scrollable slider footer menus:
/// 0: Consult | 1: Puja | 2: Astrologers | 3: Live Darshan | 4: Astromall | 5: Explore | 6: Profile
class MainShell extends StatefulWidget {
  const MainShell({super.key});

  static const route = '/shell';

  @override
  State<MainShell> createState() => _MainShellState();
}

class _MainShellState extends State<MainShell> {
  int _tab = 0;
  final ScrollController _navScrollController = ScrollController();

  final List<GlobalKey> _itemKeys = List.generate(7, (_) => GlobalKey());

  void _selectTab(int index) {
    if (!mounted) return;
    setState(() => _tab = index);

    // Auto-scroll the footer slider so the selected tab is comfortably in view
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_navScrollController.hasClients && index < _itemKeys.length) {
        final currentContext = _itemKeys[index].currentContext;
        if (currentContext != null) {
          Scrollable.ensureVisible(
            currentContext,
            duration: const Duration(milliseconds: 280),
            curve: Curves.easeInOut,
            alignment: 0.5,
          );
        }
      }
    });
  }

  @override
  void dispose() {
    _navScrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return MainShellScope(
      selectTab: _selectTab,
      child: Scaffold(
        body: ValueListenableBuilder<AppLanguage>(
          valueListenable: LocaleManager.instance.currentLanguage,
          builder: (context, _, __) => IndexedStack(
            index: _tab,
            children: const [
              ConsultHomeScreen(),
              ExploreScreen(),
              PujaListScreen(),
              AstrologersScreen(),
              LiveDarshanScreen(),
              MallScreen(),
              ProfileScreen(),
            ],
          ),
        ),
        // Large covering FAB removed - moved to smart top header actions across screens
        bottomNavigationBar: Container(
          decoration: BoxDecoration(
            color: isDark ? CustomerTheme.cosmicCardDark : Colors.white,
            border: Border(
              top: BorderSide(
                color: isDark
                    ? CustomerTheme.brandGold.withValues(alpha: 0.15)
                    : CustomerTheme.lightBorder.withValues(alpha: 0.8),
                width: 1,
              ),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: isDark ? 0.35 : 0.08),
                blurRadius: 16,
                offset: const Offset(0, -4),
              ),
            ],
          ),
          child: SafeArea(
            top: false,
            child: ValueListenableBuilder<AppLanguage>(
              valueListenable: LocaleManager.instance.currentLanguage,
              builder: (context, _, _) {
                return SizedBox(
                  height: 68,
                  child: ListView(
                    controller: _navScrollController,
                    scrollDirection: Axis.horizontal,
                    physics: const BouncingScrollPhysics(),
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    children: [
                      _sliderItem(
                        key: _itemKeys[0],
                        index: 0,
                        icon: Icons.auto_awesome_outlined,
                        activeIcon: Icons.auto_awesome_rounded,
                        label: 'Consult / AI',
                        isDark: isDark,
                        badge: 'AI',
                      ),
                      _sliderItem(
                        key: _itemKeys[1],
                        index: 1,
                        icon: Icons.explore_outlined,
                        activeIcon: Icons.explore_rounded,
                        label: AppStrings.explore,
                        isDark: isDark,
                      ),
                      _sliderItem(
                        key: _itemKeys[2],
                        index: 2,
                        icon: Icons.local_fire_department_outlined,
                        activeIcon: Icons.local_fire_department_rounded,
                        label: AppStrings.puja,
                        isDark: isDark,
                      ),
                      _sliderItem(
                        key: _itemKeys[3],
                        index: 3,
                        icon: Icons.psychology_outlined,
                        activeIcon: Icons.psychology_rounded,
                        label: AppStrings.astrologers,
                        isDark: isDark,
                      ),
                      _sliderItem(
                        key: _itemKeys[4],
                        index: 4,
                        icon: Icons.temple_hindu_outlined,
                        activeIcon: Icons.temple_hindu_rounded,
                        label: AppStrings.liveDarshan,
                        isDark: isDark,
                        badge: "LIVE",
                      ),
                      _sliderItem(
                        key: _itemKeys[5],
                        index: 5,
                        icon: Icons.storefront_outlined,
                        activeIcon: Icons.storefront_rounded,
                        label: AppStrings.astroMall,
                        isDark: isDark,
                      ),
                      _sliderItem(
                        key: _itemKeys[6],
                        index: 6,
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

  Widget _sliderItem({
    required Key key,
    required int index,
    required IconData icon,
    required IconData activeIcon,
    required String label,
    required bool isDark,
    String? badge,
  }) {
    final isSelected = _tab == index;

    return Container(
      key: key,
      margin: const EdgeInsets.symmetric(horizontal: 3),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () => _selectTab(index),
          borderRadius: BorderRadius.circular(16),
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 220),
            padding: const EdgeInsets.symmetric(horizontal: 11, vertical: 3),
            decoration: BoxDecoration(
              color: isSelected
                  ? CustomerTheme.brandSaffron.withValues(alpha: isDark ? 0.22 : 0.12)
                  : Colors.transparent,
              borderRadius: BorderRadius.circular(16),
              border: isSelected
                  ? Border.all(color: CustomerTheme.brandSaffron.withValues(alpha: 0.45), width: 1.2)
                  : null,
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Stack(
                  clipBehavior: Clip.none,
                  children: [
                    AnimatedContainer(
                      duration: const Duration(milliseconds: 220),
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                      decoration: BoxDecoration(
                        gradient: isSelected ? CustomerTheme.saffronGradient : null,
                        borderRadius: BorderRadius.circular(14),
                        boxShadow: isSelected
                            ? [
                                BoxShadow(
                                  color: CustomerTheme.brandSaffron.withValues(alpha: 0.35),
                                  blurRadius: 6,
                                  offset: const Offset(0, 2),
                                ),
                              ]
                            : null,
                      ),
                      child: Icon(
                        isSelected ? activeIcon : icon,
                        size: 19,
                        color: isSelected
                            ? Colors.white
                            : (isDark ? Colors.white60 : const Color(0xFF64748B)),
                      ),
                    ),
                    if (badge != null)
                      Positioned(
                        top: -3,
                        right: -4,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 1),
                          decoration: BoxDecoration(
                            color: const Color(0xFFEF4444),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            badge,
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 7.5,
                              fontWeight: FontWeight.w900,
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
                  style: TextStyle(
                    fontSize: 10.5,
                    fontWeight: isSelected ? FontWeight.w800 : FontWeight.w600,
                    color: isSelected
                        ? CustomerTheme.brandSaffron
                        : (isDark ? Colors.white70 : const Color(0xFF64748B)),
                    letterSpacing: -0.2,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
