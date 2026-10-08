import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import '../theme/customer_theme.dart';

import '../widgets/onlinepuja_ai_dialog.dart';
import 'astrologer/astrologers_screen.dart';
import 'explore/explore_screen.dart';
import 'home/consult_home_screen.dart';
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

/// Luxury sacred navigation shell with 5 footer menus:
/// 0: Consult (Home) | 1: Puja | 2: Explore | 3: Astrologer | 4: Profile
class MainShell extends StatefulWidget {
  const MainShell({super.key});

  static const route = '/shell';

  @override
  State<MainShell> createState() => _MainShellState();
}

class _MainShellState extends State<MainShell> {
  int _tab = 0;

  void _selectTab(int index) {
    if (mounted) setState(() => _tab = index);
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return MainShellScope(
      selectTab: _selectTab,
      child: Scaffold(
        body: IndexedStack(
          index: _tab,
          children: const [
            ConsultHomeScreen(),
            PujaListScreen(),
            ExploreScreen(),
            AstrologersScreen(),
            ProfileScreen(),
          ],
        ),
        floatingActionButtonLocation: FloatingActionButtonLocation.miniEndFloat,
        floatingActionButton: Padding(
          padding: const EdgeInsets.only(bottom: 8),
          child: FloatingActionButton.extended(
            heroTag: 'onlinepuja_ai_fab',
            elevation: 4,
            onPressed: () => OnlinePujaAiDialog.show(context),
            backgroundColor: const Color(0xFFD97706),
            icon: Container(
              padding: const EdgeInsets.all(4),
              decoration: const BoxDecoration(
                shape: BoxShape.circle,
                color: Color(0xFFFEF3C7),
              ),
              child: const Text('ॐ',
                  style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w900,
                      color: Color(0xFF78350F))),
            ),
            label: const Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  'OnlinePuja AI',
                  style: TextStyle(
                    fontWeight: FontWeight.w800,
                    fontSize: 12.5,
                    color: Colors.white,
                    letterSpacing: 0.3,
                  ),
                ),
                SizedBox(width: 4),
                Text('✨', style: TextStyle(fontSize: 12)),
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
                    : CustomerTheme.lightBorder.withValues(alpha: 0.8),
                width: 1,
              ),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: isDark ? 0.25 : 0.06),
                blurRadius: 16,
                offset: const Offset(0, -4),
              ),
            ],
          ),
          child: SafeArea(
            top: false,
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 4),
              child: ValueListenableBuilder<AppLanguage>(
                valueListenable: LocaleManager.instance.currentLanguage,
                builder: (context, _, _) {
                  return Row(
                    mainAxisAlignment: MainAxisAlignment.spaceAround,
                    children: [
                      _navItem(
                        index: 0,
                        icon: Icons.auto_awesome_outlined,
                        activeIcon: Icons.auto_awesome_rounded,
                        label: AppStrings.consult,
                        isDark: isDark,
                      ),
                      _navItem(
                        index: 1,
                        icon: Icons.local_fire_department_outlined,
                        activeIcon: Icons.local_fire_department_rounded,
                        label: AppStrings.puja,
                        isDark: isDark,
                      ),
                      _navItem(
                        index: 2,
                        icon: Icons.explore_outlined,
                        activeIcon: Icons.explore_rounded,
                        label: AppStrings.explore,
                        isDark: isDark,
                      ),
                      _navItem(
                        index: 3,
                        icon: Icons.psychology_outlined,
                        activeIcon: Icons.psychology_rounded,
                        label: AppStrings.astrologers,
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
                  );
                },
              ),
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
  }) {
    final isSelected = _tab == index;

    return Expanded(
      child: GestureDetector(
        behavior: HitTestBehavior.opaque,
        onTap: () => setState(() => _tab = index),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 220),
          padding: const EdgeInsets.symmetric(vertical: 4),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              AnimatedContainer(
                duration: const Duration(milliseconds: 220),
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                decoration: BoxDecoration(
                  gradient: isSelected ? CustomerTheme.saffronGradient : null,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: isSelected
                      ? [
                          BoxShadow(
                            color: CustomerTheme.brandSaffron
                                .withValues(alpha: 0.35),
                            blurRadius: 8,
                            offset: const Offset(0, 2),
                          ),
                        ]
                      : null,
                ),
                child: Icon(
                  isSelected ? activeIcon : icon,
                  size: 20,
                  color: isSelected
                      ? Colors.white
                      : (isDark ? Colors.white60 : const Color(0xFF6B7280)),
                ),
              ),
              const SizedBox(height: 3),
              Text(
                label,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                  fontSize: 10.5,
                  fontWeight: isSelected ? FontWeight.w800 : FontWeight.w500,
                  color: isSelected
                      ? CustomerTheme.brandSaffron
                      : (isDark ? Colors.white54 : const Color(0xFF6B7280)),
                  letterSpacing: -0.1,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
