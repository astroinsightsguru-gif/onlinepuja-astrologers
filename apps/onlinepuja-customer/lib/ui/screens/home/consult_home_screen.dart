import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../app.dart';
import '../../../state/app_session.dart';
import '../../theme/customer_theme.dart';
import '../../widgets/onlinepuja_ai_dialog.dart';

import '../cosmic_ai_screen.dart';
import '../darshan/live_darshan_screen.dart';
import '../history/history_screen.dart';
import '../horoscope/daily_horoscope_screen.dart';
import '../kundli/kundli_list_screen.dart';
import '../kundli/kundli_matching_screen.dart';
import '../main_shell.dart';
import '../mall/mall_screen.dart';
import '../notifications_screen.dart';
import '../panchang/panchang_screen.dart';
import '../profile/wallet_screen.dart';
import '../puja/puja_detail_screen.dart';
import '../kp/kp_calendar_screen.dart';

/// Divine Vedic Home Portal (Consult Tab):
/// - Clean header with drawer, wallet, notification bell & language toggle
/// - Search bar for pujas and astrologers
/// - Promotional Hero Carousel with quick CTA triggers
/// - Quick Vedic shortcuts (AI, Kundli, Matching, Horoscope, Panchang, Darshan, Mall)
/// - Live Astrologers Online horizontal strip (no overflow, instant profile link)
/// - Popular Pujas & Havans card carousel (with "View All" link to Puja tab)
/// - Top Astrologers card carousel (with "View All" link to Astrologer tab)
/// - Astrology & Bhakti Videos
/// - Trust Badges
class ConsultHomeScreen extends StatefulWidget {
  const ConsultHomeScreen({super.key});

  @override
  State<ConsultHomeScreen> createState() => _ConsultHomeScreenState();
}

class _ConsultHomeScreenState extends State<ConsultHomeScreen> {
  late Future<List<Astrologer>> _astroFuture;
  late Future<List<Puja>> _pujaFuture;
  Map<String, dynamic>? _homeData;
  int _bannerIndex = 0;
  bool _isHindi = false;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  void _loadData() {
    final session = context.read<AppSession>();
    _astroFuture = AstrologerApi.instance
        .list(userId: session.user?.id, sortBy: 'rating')
        .then((l) => l.where((a) => !a.isBlock).toList());
    _pujaFuture = PujaApi.instance
        .recommended(userId: session.user?.id)
        .catchError((_) => PujaApi.instance.list());
    _loadHomeData();
  }

  Future<void> _loadHomeData() async {
    try {
      final data = await MiscApi.instance.customerHome();
      if (mounted) setState(() => _homeData = data);
    } catch (_) {}
  }

  void _goToTab(int tabIndex) {
    MainShellScope.of(context)?.selectTab(tabIndex);
  }

  @override
  Widget build(BuildContext context) {
    final session = context.watch<AppSession>();
    final scheme = Theme.of(context).colorScheme;
    final currency = session.flags.currency.isNotEmpty ? session.flags.currency : '₹';
    final wallet = session.user?.walletAmount ?? 0.0;

    return Scaffold(
      drawer: _drawer(context, session, scheme),
      appBar: AppBar(
        titleSpacing: 0,
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: const BoxDecoration(
                shape: BoxShape.circle,
                gradient: CustomerTheme.goldGradient,
              ),
              child: const Text('ॐ',
                  style: TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w900,
                      color: Color(0xFF78350F))),
            ),
            const SizedBox(width: 8),
            Text(
              'Online Puja',
              style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.w900,
                    letterSpacing: -0.3,
                  ),
            ),
          ],
        ),
        actions: [
          // Language selector
          ValueListenableBuilder<AppLanguage>(
            valueListenable: LocaleManager.instance.currentLanguage,
            builder: (context, currentLang, _) {
              return TextButton(
                style: TextButton.styleFrom(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  minimumSize: Size.zero,
                  tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                ),
                onPressed: () => _showLanguageSelector(context),
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(
                    color: CustomerTheme.brandSaffron.withOpacity(0.12),
                    border: Border.all(color: CustomerTheme.brandSaffron.withOpacity(0.5)),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Icon(Icons.language, size: 12, color: CustomerTheme.brandSaffron),
                      const SizedBox(width: 4),
                      Text(
                        currentLang.code.toUpperCase(),
                        style: const TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w800,
                          color: CustomerTheme.brandSaffron,
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
          const SizedBox(width: 4),
          // Wallet button
          GestureDetector(
            onTap: () => Navigator.of(context)
                .pushNamed(WalletScreen.route)
                .then((_) => session.refreshUser()),
            child: Container(
              margin: const EdgeInsets.symmetric(vertical: 10, horizontal: 4),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
              decoration: BoxDecoration(
                color: const Color(0xFF1B8A5A),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                '$currency${wallet.toStringAsFixed(1)}',
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 13,
                ),
              ),
            ),
          ),
          // Notifications
          IconButton(
            tooltip: 'Notifications',
            icon: const Icon(Icons.notifications_outlined),
            onPressed: () => Navigator.of(context).push(MaterialPageRoute(
                builder: (_) => const NotificationsScreen())),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          setState(() => _loadData());
        },
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _searchBar(context, scheme),
              _heroBannerCarousel(context, scheme),
              _quickShortcuts(context, scheme),
              _liveAstrologerStories(context, scheme),
              _trendingPujasSection(context, scheme, currency),
              _topAstrologersSection(context, scheme, currency),
              _videosSection(context, scheme),
              _trustBadgesStrip(context, scheme),
              const SizedBox(height: 36),
            ],
          ),
        ),
      ),
    );
  }

  void _showLanguageSelector(BuildContext context) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (ctx) {
        return SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.language, color: CustomerTheme.brandSaffron, size: 24),
                        const SizedBox(width: 8),
                        Text(
                          AppStrings.selectLanguage,
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: Color(0xFF1A132F),
                          ),
                        ),
                      ],
                    ),
                    IconButton(
                      icon: const Icon(Icons.close),
                      onPressed: () => Navigator.pop(ctx),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                const Text(
                  'Choose your preferred language for consultations, pujas & horoscopes:',
                  style: TextStyle(fontSize: 13, color: Colors.black54),
                ),
                const SizedBox(height: 16),
                Flexible(
                  child: GridView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      childAspectRatio: 2.7,
                      crossAxisSpacing: 10,
                      mainAxisSpacing: 10,
                    ),
                    itemCount: AppLanguage.values.length,
                    itemBuilder: (context, idx) {
                      final lang = AppLanguage.values[idx];
                      final isSelected =
                          LocaleManager.instance.currentLanguage.value == lang;
                      return InkWell(
                        onTap: () {
                          LocaleManager.instance.setLanguage(lang);
                          Navigator.pop(ctx);
                        },
                        borderRadius: BorderRadius.circular(12),
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: isSelected
                                ? CustomerTheme.brandSaffron.withOpacity(0.12)
                                : const Color(0xFFF9F7F2),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: isSelected
                                  ? CustomerTheme.brandSaffron
                                  : Colors.black12,
                              width: isSelected ? 1.5 : 1,
                            ),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Text(
                                    lang.label,
                                    style: TextStyle(
                                      fontWeight: FontWeight.bold,
                                      fontSize: 14,
                                      color: isSelected
                                          ? CustomerTheme.brandSaffron
                                          : const Color(0xFF1A132F),
                                    ),
                                  ),
                                  Text(
                                    lang.englishName,
                                    style: TextStyle(
                                      fontSize: 11,
                                      color: isSelected
                                          ? CustomerTheme.brandSaffron.withOpacity(0.8)
                                          : Colors.black54,
                                    ),
                                  ),
                                ],
                              ),
                              if (isSelected)
                                const Icon(
                                  Icons.check_circle,
                                  color: CustomerTheme.brandSaffron,
                                  size: 18,
                                ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
                const SizedBox(height: 12),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _searchBar(BuildContext context, ColorScheme scheme) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 6),
      child: GestureDetector(
        onTap: () => _goToTab(3), // Jump to Astrologer tab to search
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          decoration: BoxDecoration(
            color: scheme.surfaceContainerHighest.withValues(alpha: 0.4),
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: scheme.outline.withValues(alpha: 0.3)),
          ),
          child: Row(
            children: [
              Icon(Icons.search_rounded, color: scheme.onSurfaceVariant),
              const SizedBox(width: 10),
              Text(
                'Search pujas, astrologers, kundli…',
                style: TextStyle(
                  color: scheme.onSurfaceVariant.withValues(alpha: 0.8),
                  fontSize: 13.5,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _heroBannerCarousel(BuildContext context, ColorScheme scheme) {
    final curatedBanners = [
      (
        title: 'Authentic Online Puja & Hawan',
        subtitle: 'Special Navgrah & Temple Rituals with Live Sankalp & Prasad',
        cta: 'Book Puja 🙏',
        icon: Icons.local_fire_department_rounded,
        gradient: const [Color(0xFFB71C1C), Color(0xFFE53935)],
        onTap: () => _goToTab(1), // Puja tab
      ),
      (
        title: 'Accurate Predictions by Vedic Astrologers',
        subtitle: 'First session FREE · Guidance on Love, Marriage, Career & Wealth',
        cta: 'Consult Astrologer',
        icon: Icons.self_improvement_rounded,
        gradient: const [Color(0xFF8E2B12), Color(0xFFD97706)],
        onTap: () => _goToTab(3), // Astrologer tab
      ),
      (
        title: '24/7 Live Sanctum Darshan & Aarti',
        subtitle: 'Kashi Vishwanath, Mahakaleshwar & Somnath Temple Feeds',
        cta: 'Watch Live 🪔',
        icon: Icons.temple_hindu_rounded,
        gradient: const [Color(0xFF4A148C), Color(0xFF7B1FA2)],
        onTap: () => Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => const LiveDarshanScreen()),
        ),
      ),
      (
        title: 'AstroMall & Energized Rudraksha',
        subtitle: '100% Certified Vedic Samagri, Gemstones & Sphatik Malas',
        cta: 'Explore Mall 🛍️',
        icon: Icons.shopping_bag_outlined,
        gradient: const [Color(0xFF1B5E20), Color(0xFF2E7D32)],
        onTap: () => Navigator.of(context).pushNamed(MallScreen.route),
      ),
    ];

    final apiBanners = (_homeData?['banner'] as List?) ?? [];

    return Column(
      children: [
        SizedBox(
          height: 156,
          child: PageView.builder(
            itemCount: curatedBanners.length,
            onPageChanged: (i) => setState(() => _bannerIndex = i),
            itemBuilder: (context, i) {
              final b = curatedBanners[i];
              final apiImg = i < apiBanners.length
                  ? (apiBanners[i]['bannerImage'] ?? '').toString()
                  : '';

              return Container(
                margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(18),
                  gradient: LinearGradient(
                    colors: b.gradient,
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: b.gradient.first.withValues(alpha: 0.25),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(18),
                  child: Stack(
                    children: [
                      if (apiImg.isNotEmpty)
                        Positioned.fill(
                          child: Image.network(
                            apiImg,
                            fit: BoxFit.cover,
                            errorBuilder: (_, e, s) => const SizedBox.shrink(),
                          ),
                        ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 16),
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [
                              Colors.black.withValues(alpha: 0.65),
                              Colors.transparent,
                            ],
                            begin: Alignment.centerLeft,
                            end: Alignment.centerRight,
                          ),
                        ),
                        child: Row(
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Text(
                                    b.title,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontSize: 15,
                                      fontWeight: FontWeight.w800,
                                      height: 1.2,
                                    ),
                                  ),
                                  const SizedBox(height: 3),
                                  Text(
                                    b.subtitle,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(
                                      color: Colors.white70,
                                      fontSize: 11,
                                      height: 1.2,
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  ElevatedButton(
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: Colors.white,
                                      foregroundColor: b.gradient.first,
                                      elevation: 2,
                                      shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(18),
                                      ),
                                      padding: const EdgeInsets.symmetric(
                                        horizontal: 12,
                                        vertical: 4,
                                      ),
                                      visualDensity: VisualDensity.compact,
                                    ),
                                    onPressed: b.onTap,
                                    child: Text(
                                      b.cta,
                                      style: const TextStyle(
                                        fontWeight: FontWeight.bold,
                                        fontSize: 11.5,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(width: 10),
                            CircleAvatar(
                              radius: 34,
                              backgroundColor: Colors.white.withValues(alpha: 0.22),
                              child: Icon(b.icon, size: 38, color: Colors.white),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
        ),
        const SizedBox(height: 6),
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            for (int i = 0; i < curatedBanners.length; i++)
              AnimatedContainer(
                duration: const Duration(milliseconds: 300),
                margin: const EdgeInsets.symmetric(horizontal: 3),
                width: _bannerIndex == i ? 18 : 6,
                height: 6,
                decoration: BoxDecoration(
                  color: _bannerIndex == i
                      ? CustomerTheme.brandSaffron
                      : scheme.outline.withValues(alpha: 0.3),
                  borderRadius: BorderRadius.circular(3),
                ),
              ),
          ],
        ),
      ],
    );
  }

  Widget _quickShortcuts(BuildContext context, ColorScheme scheme) {
    final shortcuts = [
      ('OnlinePuja\nAI', Icons.auto_awesome_rounded, const Color(0xFFFEF3C7), const Color(0xFFD97706), null, () {
        OnlinePujaAiDialog.show(context);
      }),
      ('Book a\nPuja', Icons.local_fire_department_rounded, const Color(0xFFFFE0B2), const Color(0xFFD84315), null, () {
        _goToTab(1); // Jump to Puja tab
      }),
      ('Consult\nAstrologer', Icons.psychology_rounded, const Color(0xFFFFF3E0), const Color(0xFFE65100), null, () {
        _goToTab(3); // Jump to Astrologer tab
      }),
      ('Free\nKundli', Icons.auto_graph_rounded, const Color(0xFFFFEBEE), const Color(0xFFC62828), const KundliListScreen(), null),
      ('Kundli\nMatching', Icons.favorite_rounded, const Color(0xFFFCE4EC), const Color(0xFFAD1457), const KundliMatchingScreen(), null),
      ('Daily\nHoroscope', Icons.nightlight_round, const Color(0xFFEDE7F6), const Color(0xFF512DA8), const DailyHoroscopeScreen(), null),
      ("Today's\nPanchang", Icons.wb_twilight_rounded, const Color(0xFFFFF8E1), const Color(0xFFF57F17), const PanchangScreen(), null),
      ('KP\nCalendar', Icons.shield_moon_rounded, const Color(0xFFE8EAF6), const Color(0xFF283593), const KpCalendarScreen(), null),
      ('Live\nDarshan', Icons.temple_hindu_rounded, const Color(0xFFF3E5F5), const Color(0xFF6A1B9A), const LiveDarshanScreen(), null),
      ('Astro\nMall', Icons.storefront_rounded, const Color(0xFFE0F2F1), const Color(0xFF00695C), const MallScreen(), null),
    ];

    return Container(
      height: 108,
      margin: const EdgeInsets.only(top: 8, bottom: 4),
      child: ListView.separated(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        scrollDirection: Axis.horizontal,
        itemCount: shortcuts.length,
        separatorBuilder: (_, _) => const SizedBox(width: 12),
        itemBuilder: (context, i) {
          final item = shortcuts[i];
          return GestureDetector(
            onTap: () {
              if (item.$6 != null) {
                item.$6!();
              } else if (item.$5 != null) {
                Navigator.of(context).push(MaterialPageRoute(builder: (_) => item.$5!));
              }
            },
            child: SizedBox(
              width: 80,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: BoxDecoration(
                      color: item.$3,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: item.$4.withValues(alpha: 0.3), width: 1.2),
                    ),
                    child: Icon(item.$2, color: item.$4, size: 24),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    item.$1,
                    maxLines: 2,
                    textAlign: TextAlign.center,
                    overflow: TextOverflow.ellipsis,
                    style: Theme.of(context).textTheme.labelSmall?.copyWith(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          height: 1.15,
                        ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  /// Live Astrologers Online horizontal strip with fixed 128px height (Zero overflow)
  Widget _liveAstrologerStories(BuildContext context, ColorScheme scheme) {
    return FutureBuilder<List<Astrologer>>(
      future: _astroFuture,
      builder: (context, snap) {
        final all = snap.data ?? [];
        if (all.isEmpty) return const SizedBox.shrink();

        final onlineList = all.where((a) => a.isChatOnline || a.isCallOnline).toList();
        final displayList = (onlineList.isNotEmpty ? onlineList : all).take(12).toList();

        return Container(
          height: 128, // Ample height for avatar + live tag + name + rate (fixes overflow)
          margin: const EdgeInsets.only(top: 6, bottom: 6),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 2),
                child: Row(
                  children: [
                    const Icon(Icons.fiber_manual_record, color: Colors.green, size: 10),
                    const SizedBox(width: 5),
                    Text(
                      'Live Astrologers Online',
                      style: Theme.of(context).textTheme.labelMedium?.copyWith(
                            fontWeight: FontWeight.w800,
                            letterSpacing: 0.2,
                          ),
                    ),
                    const Spacer(),
                    GestureDetector(
                      onTap: () => _goToTab(3), // Jump to Astrologer tab
                      child: Text(
                        'View All →',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                          color: CustomerTheme.brandSaffron,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 6),
              Expanded(
                child: ListView.separated(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  scrollDirection: Axis.horizontal,
                  itemCount: displayList.length,
                  separatorBuilder: (_, _) => const SizedBox(width: 14),
                  itemBuilder: (context, i) {
                    final a = displayList[i];
                    final name = a.name.split(' ').first;
                    final rate = a.charge > 0 ? '₹${a.charge.toInt()}/m' : 'FREE';

                    return GestureDetector(
                      onTap: () => context.openAstrologer(a.id ?? 0).then((_) => setState(() => _loadData())),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Stack(
                            clipBehavior: Clip.none,
                            alignment: Alignment.center,
                            children: [
                              Container(
                                width: 56,
                                height: 56,
                                decoration: const BoxDecoration(
                                  shape: BoxShape.circle,
                                  gradient: LinearGradient(
                                    colors: [Color(0xFFE65100), Color(0xFFFFB300), Color(0xFFD84315)],
                                    begin: Alignment.topLeft,
                                    end: Alignment.bottomRight,
                                  ),
                                ),
                                padding: const EdgeInsets.all(2.2),
                                child: Container(
                                  decoration: const BoxDecoration(
                                    shape: BoxShape.circle,
                                    color: Colors.white,
                                  ),
                                  padding: const EdgeInsets.all(1.5),
                                  child: ClipOval(
                                    child: a.profileImage.isNotEmpty
                                        ? Image.network(
                                            a.profileImage,
                                            fit: BoxFit.cover,
                                            errorBuilder: (_, e, s) => _avatarFallback(a.name),
                                          )
                                        : _avatarFallback(a.name),
                                  ),
                                ),
                              ),
                              Positioned(
                                bottom: -2,
                                child: Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1.5),
                                  decoration: BoxDecoration(
                                    color: Colors.green.shade700,
                                    borderRadius: BorderRadius.circular(8),
                                    border: Border.all(color: Colors.white, width: 1.2),
                                  ),
                                  child: const Text(
                                    'LIVE',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 8.5,
                                      fontWeight: FontWeight.w900,
                                      letterSpacing: 0.3,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 6),
                          SizedBox(
                            width: 64,
                            child: Text(
                              name,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              textAlign: TextAlign.center,
                              style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700),
                            ),
                          ),
                          Text(
                            rate,
                            style: TextStyle(
                              fontSize: 10,
                              color: scheme.primary,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ],
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _avatarFallback(String name) {
    final initial = name.isNotEmpty ? name[0].toUpperCase() : 'A';
    return Container(
      color: const Color(0xFFFFF3E0),
      child: Center(
        child: Text(
          initial,
          style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFFE65100), fontSize: 18),
        ),
      ),
    );
  }

  /// Popular Pujas & Havans horizontal preview card section
  Widget _trendingPujasSection(BuildContext context, ColorScheme scheme, String currency) {
    return FutureBuilder<List<Puja>>(
      future: _pujaFuture,
      builder: (context, snap) {
        final list = snap.data ?? [];
        if (list.isEmpty) return const SizedBox.shrink();

        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 10),
              child: Row(
                children: [
                  const Icon(Icons.local_fire_department_rounded, color: Color(0xFFB71C1C), size: 20),
                  const SizedBox(width: 6),
                  Text(
                    'Popular Pujas & Havans',
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                          fontWeight: FontWeight.w900,
                          letterSpacing: -0.2,
                        ),
                  ),
                  const Spacer(),
                  GestureDetector(
                    onTap: () => _goToTab(1), // Puja tab
                    child: Text(
                      'View All →',
                      style: TextStyle(
                        fontSize: 12.5,
                        fontWeight: FontWeight.w800,
                        color: CustomerTheme.brandSaffron,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            SizedBox(
              height: 200,
              child: ListView.separated(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                scrollDirection: Axis.horizontal,
                itemCount: list.take(8).length,
                separatorBuilder: (_, _) => const SizedBox(width: 14),
                itemBuilder: (context, i) {
                  final p = list[i];
                  final priceText = p.startingPrice != null
                      ? '$currency${p.startingPrice!.toInt()}'
                      : '${currency}501';

                  return GestureDetector(
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(builder: (_) => PujaDetailScreen(puja: p)),
                    ),
                    child: Container(
                      width: 220,
                      decoration: BoxDecoration(
                        color: scheme.surface,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: scheme.outline.withValues(alpha: 0.18)),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.05),
                            blurRadius: 8,
                            offset: const Offset(0, 3),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          ClipRRect(
                            borderRadius: const BorderRadius.vertical(top: Radius.circular(15)),
                            child: Stack(
                              children: [
                                p.coverImage.isNotEmpty
                                    ? Image.network(
                                        p.coverImage,
                                        height: 104,
                                        width: 220,
                                        fit: BoxFit.cover,
                                        errorBuilder: (_, e, s) => Container(
                                          height: 104,
                                          color: const Color(0xFFFDE68A),
                                          child: const Icon(Icons.temple_hindu_rounded, size: 36),
                                        ),
                                      )
                                    : Container(
                                        height: 104,
                                        color: const Color(0xFFFDE68A),
                                        child: const Center(
                                          child: Icon(Icons.temple_hindu_rounded, size: 36),
                                        ),
                                      ),
                                Positioned(
                                  top: 8,
                                  left: 8,
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFB71C1C),
                                      borderRadius: BorderRadius.circular(8),
                                    ),
                                    child: const Text(
                                      'VERIFIED PANDIT',
                                      style: TextStyle(
                                        color: Colors.white,
                                        fontSize: 8.5,
                                        fontWeight: FontWeight.w900,
                                        letterSpacing: 0.4,
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          Padding(
                            padding: const EdgeInsets.all(10),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  p.title.isNotEmpty ? p.title : 'Vedic Puja',
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(
                                    fontWeight: FontWeight.w800,
                                    fontSize: 13,
                                  ),
                                ),
                                const SizedBox(height: 3),
                                Text(
                                  p.place.isNotEmpty ? p.place : 'Haridwar / Varanasi / Kashi',
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: TextStyle(
                                    fontSize: 11,
                                    color: scheme.onSurfaceVariant,
                                  ),
                                ),
                                const SizedBox(height: 6),
                                Row(
                                  children: [
                                    Text(
                                      priceText,
                                      style: const TextStyle(
                                        fontWeight: FontWeight.w900,
                                        fontSize: 14,
                                        color: Color(0xFF1B8A5A),
                                      ),
                                    ),
                                    const Spacer(),
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                      decoration: BoxDecoration(
                                        color: CustomerTheme.brandSaffron.withValues(alpha: 0.12),
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Text(
                                        'Book Now',
                                        style: TextStyle(
                                          color: CustomerTheme.brandSaffron,
                                          fontWeight: FontWeight.w800,
                                          fontSize: 11,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
          ],
        );
      },
    );
  }

  /// Top Certified Astrologers preview card carousel
  Widget _topAstrologersSection(BuildContext context, ColorScheme scheme, String currency) {
    return FutureBuilder<List<Astrologer>>(
      future: _astroFuture,
      builder: (context, snap) {
        final list = snap.data ?? [];
        if (list.isEmpty) return const SizedBox.shrink();

        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 20, 16, 10),
              child: Row(
                children: [
                  const Icon(Icons.psychology_rounded, color: Color(0xFFD97706), size: 20),
                  const SizedBox(width: 6),
                  Text(
                    'Top Vedic Astrologers',
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                          fontWeight: FontWeight.w900,
                          letterSpacing: -0.2,
                        ),
                  ),
                  const Spacer(),
                  GestureDetector(
                    onTap: () => _goToTab(3), // Astrologer tab
                    child: Text(
                      'View All (${list.length}) →',
                      style: TextStyle(
                        fontSize: 12.5,
                        fontWeight: FontWeight.w800,
                        color: CustomerTheme.brandSaffron,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            SizedBox(
              height: 180,
              child: ListView.separated(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                scrollDirection: Axis.horizontal,
                itemCount: list.take(6).length,
                separatorBuilder: (_, _) => const SizedBox(width: 14),
                itemBuilder: (context, i) {
                  final a = list[i];
                  final rate = a.charge > 0 ? '$currency${a.charge.toInt()}/min' : 'FREE';

                  return GestureDetector(
                    onTap: () => context.openAstrologer(a.id ?? 0).then((_) => setState(() => _loadData())),
                    child: Container(
                      width: 200,
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: scheme.surface,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: scheme.outline.withValues(alpha: 0.18)),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.04),
                            blurRadius: 8,
                            offset: const Offset(0, 3),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              CircleAvatar(
                                radius: 26,
                                backgroundImage: a.imageUrl.isNotEmpty ? NetworkImage(a.imageUrl) : null,
                                child: a.imageUrl.isEmpty ? _avatarFallback(a.name) : null,
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                          Text(
                            a.name,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            a.primarySkill.isNotEmpty ? a.primarySkill : 'Vedic Astrology',
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(fontSize: 11, color: scheme.onSurfaceVariant),
                          ),
                          const Spacer(),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                rate,
                                style: const TextStyle(
                                  fontWeight: FontWeight.w900,
                                  fontSize: 12.5,
                                  color: Color(0xFFD97706),
                                ),
                              ),
                              FilledButton(
                                style: FilledButton.styleFrom(
                                  backgroundColor: const Color(0xFF1B8A5A),
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                  visualDensity: VisualDensity.compact,
                                ),
                                onPressed: () => context.openAstrologer(a.id ?? 0),
                                child: const Text(
                                  'Consult',
                                  style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
          ],
        );
      },
    );
  }

  Widget _videosSection(BuildContext context, ColorScheme scheme) {
    final videos = _homeData?['astrologyVideo'] as List? ?? [];
    if (videos.isEmpty) return const SizedBox.shrink();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 20, 16, 10),
          child: Text(
            'Watch Astrology & Aarti Videos',
            style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.w800),
          ),
        ),
        SizedBox(
          height: 140,
          child: ListView.separated(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            scrollDirection: Axis.horizontal,
            itemCount: videos.length,
            separatorBuilder: (_, _) => const SizedBox(width: 12),
            itemBuilder: (context, i) {
              final v = videos[i] as Map<String, dynamic>;
              final title = (v['videoTitle'] ?? 'Aarti & Mantra').toString();
              final img = (v['coverImage'] ?? '').toString();
              final videoUrl = (v['youtubeLink'] ?? v['video_link'] ?? v['link'] ?? '').toString();

              return Material(
                color: Colors.transparent,
                child: InkWell(
                  borderRadius: BorderRadius.circular(14),
                  onTap: () async {
                    if (videoUrl.isNotEmpty) {
                      final uri = Uri.parse(videoUrl);
                      try {
                        final launched = await launchUrl(uri, mode: LaunchMode.externalApplication);
                        if (!launched && context.mounted) {
                          await launchUrl(uri, mode: LaunchMode.inAppBrowserView);
                        }
                      } catch (_) {
                        if (context.mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(content: Text('Opening $title…')),
                          );
                        }
                      }
                    }
                  },
                  child: Container(
                    width: 180,
                    decoration: BoxDecoration(
                      color: scheme.surface,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: scheme.outline.withValues(alpha: 0.25)),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Stack(
                          alignment: Alignment.center,
                          children: [
                            ClipRRect(
                              borderRadius: const BorderRadius.vertical(top: Radius.circular(13)),
                              child: img.isNotEmpty
                                  ? Image.network(img, height: 80, width: 180, fit: BoxFit.cover)
                                  : Container(height: 80, color: Colors.black26),
                            ),
                            const CircleAvatar(
                              radius: 16,
                              backgroundColor: Colors.red,
                              child: Icon(Icons.play_arrow, color: Colors.white, size: 20),
                            ),
                          ],
                        ),
                        Padding(
                          padding: const EdgeInsets.all(8),
                          child: Text(
                            title,
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        ),
      ],
    );
  }

  Widget _trustBadgesStrip(BuildContext context, ColorScheme scheme) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: scheme.surfaceContainerHighest.withValues(alpha: 0.35),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: scheme.outline.withValues(alpha: 0.15)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _trustItem(Icons.verified_user_rounded, '100% Private', '& Confidential', scheme),
          Container(height: 28, width: 1, color: scheme.outline.withValues(alpha: 0.2)),
          _trustItem(Icons.workspace_premium_rounded, 'Verified', 'Vedic Pandits', scheme),
          Container(height: 28, width: 1, color: scheme.outline.withValues(alpha: 0.2)),
          _trustItem(Icons.security_rounded, 'Secure', 'Instant Booking', scheme),
        ],
      ),
    );
  }

  Widget _trustItem(IconData icon, String line1, String line2, ColorScheme scheme) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 22, color: CustomerTheme.brandSaffron),
        const SizedBox(height: 4),
        Text(line1, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 10.5)),
        Text(line2, style: TextStyle(fontSize: 9.5, color: scheme.outline)),
      ],
    );
  }

  Widget _drawer(BuildContext context, AppSession session, ColorScheme scheme) {
    final u = session.user;
    final currency = session.flags.currency.isNotEmpty ? session.flags.currency : '₹';
    return Drawer(
      child: SafeArea(
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            UserAccountsDrawerHeader(
              currentAccountPicture: CircleAvatar(
                backgroundColor: scheme.primaryContainer,
                child: Icon(Icons.person_rounded, color: scheme.primary, size: 36),
              ),
              accountName: Text(u?.displayName ?? 'Devotee Guest', style: const TextStyle(fontWeight: FontWeight.bold)),
              accountEmail: Text(
                u?.contactNo?.isNotEmpty == true
                    ? '${u?.countryCode ?? '+91'} ${u?.contactNo}'
                    : 'Tap to sign in',
                style: const TextStyle(color: Colors.white70),
              ),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [CustomerTheme.brandSaffron, CustomerTheme.brandDeepSaffron],
                ),
              ),
            ),
            ListTile(
              leading: const Icon(Icons.account_balance_wallet_outlined),
              title: const Text('Wallet Balance'),
              trailing: Text(
                '$currency${(u?.walletAmount ?? 0).toStringAsFixed(0)}',
                style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF1B8A5A)),
              ),
              onTap: () {
                Navigator.pop(context);
                Navigator.of(context).pushNamed(WalletScreen.route).then((_) => session.refreshUser());
              },
            ),
            const Divider(),
            ListTile(
              leading: const Icon(Icons.local_fire_department_rounded),
              title: const Text('Book a Puja'),
              onTap: () {
                Navigator.pop(context);
                _goToTab(1);
              },
            ),
            ListTile(
              leading: const Icon(Icons.psychology_rounded),
              title: const Text('Consult Astrologers'),
              onTap: () {
                Navigator.pop(context);
                _goToTab(3);
              },
            ),
            ListTile(
              leading: const Icon(Icons.nightlight_round),
              title: const Text('Daily Horoscope'),
              onTap: () {
                Navigator.pop(context);
                Navigator.of(context).push(MaterialPageRoute(builder: (_) => const DailyHoroscopeScreen()));
              },
            ),
            ListTile(
              leading: const Icon(Icons.auto_graph_rounded),
              title: const Text('Free Kundli'),
              onTap: () {
                Navigator.pop(context);
                Navigator.of(context).push(MaterialPageRoute(builder: (_) => const KundliListScreen()));
              },
            ),
            ListTile(
              leading: const Icon(Icons.favorite_rounded),
              title: const Text('Kundli Matching'),
              onTap: () {
                Navigator.pop(context);
                Navigator.of(context).push(MaterialPageRoute(builder: (_) => const KundliMatchingScreen()));
              },
            ),
            ListTile(
              leading: const Icon(Icons.wb_twilight_rounded),
              title: const Text("Today's Panchang"),
              onTap: () {
                Navigator.pop(context);
                Navigator.of(context).push(MaterialPageRoute(builder: (_) => const PanchangScreen()));
              },
            ),
            ListTile(
              leading: const Icon(Icons.storefront_rounded),
              title: const Text('AstroMall'),
              onTap: () {
                Navigator.pop(context);
                Navigator.of(context).push(MaterialPageRoute(builder: (_) => const MallScreen()));
              },
            ),
            ListTile(
              leading: const Icon(Icons.auto_awesome),
              title: const Text('Cosmic AI Astrologer'),
              onTap: () {
                Navigator.pop(context);
                Navigator.of(context).push(MaterialPageRoute(builder: (_) => const CosmicAiScreen()));
              },
            ),
            ListTile(
              leading: const Icon(Icons.history_rounded),
              title: const Text('Consultation History'),
              onTap: () {
                Navigator.pop(context);
                Navigator.of(context).push(MaterialPageRoute(builder: (_) => const HistoryScreen()));
              },
            ),
            const Divider(),
            ListTile(
              leading: const Icon(Icons.palette_outlined),
              title: const Text('Theme / Appearance'),
              subtitle: Text(session.themeMode.name.toUpperCase()),
              trailing: const Icon(Icons.brightness_medium_rounded),
              onTap: () {
                final next = session.themeMode == ThemeMode.dark ? ThemeMode.light : ThemeMode.dark;
                session.setThemeMode(next);
              },
            ),
          ],
        ),
      ),
    );
  }
}
