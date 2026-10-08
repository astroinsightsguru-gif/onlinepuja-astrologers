import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../app.dart';
import '../../../state/app_session.dart';
import '../../theme/customer_theme.dart';
import '../../widgets/onlinepuja_ai_dialog.dart';

import '../cosmic_ai_screen.dart';
import '../history/history_screen.dart';
import '../horoscope/daily_horoscope_screen.dart';
import '../japa/japa_mala_screen.dart';
import '../kundli/kundli_list_screen.dart';
import '../kundli/kundli_matching_screen.dart';
import '../main_shell.dart';
import '../notifications_screen.dart';
import '../panchang/panchang_screen.dart';
import '../profile/wallet_screen.dart';
import '../puja/puja_detail_screen.dart';
import '../puja/sankalp_vault_screen.dart';
import '../kp/kp_calendar_screen.dart';

/// Clean, Human-Centered Vedic Home Portal (Consult Tab):
/// - Clean header with OnlinePuja.live branding, drawer, smart AI button, wallet & language toggle
/// - Non-cluttered search bar with daily Muhurat status
/// - Curated promotional hero banner with seamless tab navigation
/// - 4x2 Curated Vedic Sanctum Grid (Kundli, Matching, Panchang, Horoscope, KP Calendar, AI, Japa, Sankalp)
/// - Unified Live Consultation section with ₹1 offer hook, online indicators, and direct Chat/Call buttons
/// - Popular Holy Teerth Pujas preview
class ConsultHomeScreen extends StatefulWidget {
  const ConsultHomeScreen({super.key});

  @override
  State<ConsultHomeScreen> createState() => _ConsultHomeScreenState();
}

class _ConsultHomeScreenState extends State<ConsultHomeScreen> {
  late Future<List<Astrologer>> _astroFuture;
  late Future<List<Puja>> _pujaFuture;
  Map<String, dynamic>? _homeData;
  Map<String, dynamic>? _firstConsultOffer;
  int _bannerIndex = 0;

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
      final session = context.read<AppSession>();
      final data = await MiscApi.instance.customerHome();
      final promo = await MiscApi.instance.checkFirstConsultOffer(userId: session.user?.id);
      if (mounted) {
        setState(() {
          _homeData = data;
          _firstConsultOffer = promo;
        });
      }
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
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      drawer: _drawer(context, session, scheme),
      appBar: AppBar(
        titleSpacing: 0,
        title: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(5),
              decoration: const BoxDecoration(
                shape: BoxShape.circle,
                gradient: CustomerTheme.goldGradient,
              ),
              child: const Text('ॐ',
                  style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w900,
                      color: Color(0xFF78350F))),
            ),
            const SizedBox(width: 6),
            Flexible(
              child: RichText(
                overflow: TextOverflow.ellipsis,
                text: TextSpan(
                  children: [
                    TextSpan(
                      text: 'OnlinePuja',
                      style: Theme.of(context).textTheme.titleMedium?.copyWith(
                            fontWeight: FontWeight.w900,
                            letterSpacing: -0.3,
                          ),
                    ),
                    const TextSpan(
                      text: '.live',
                      style: TextStyle(
                        fontWeight: FontWeight.w900,
                        fontSize: 16,
                        color: Color(0xFFD97706),
                        letterSpacing: -0.3,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
        actions: [
          // Smart OnlinePuja AI header button
          InkWell(
            onTap: () => OnlinePujaAiDialog.show(context),
            borderRadius: BorderRadius.circular(16),
            child: Container(
              margin: const EdgeInsets.symmetric(vertical: 10, horizontal: 4),
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFFFEF3C7), Color(0xFFFDE68A)],
                ),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: const Color(0xFFF59E0B)),
              ),
              child: const Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.auto_awesome, size: 12, color: Color(0xFFD97706)),
                  SizedBox(width: 3),
                  Text('AI', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: Color(0xFF92400E))),
                ],
              ),
            ),
          ),
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
                    color: CustomerTheme.brandSaffron.withValues(alpha: 0.12),
                    border: Border.all(color: CustomerTheme.brandSaffron.withValues(alpha: 0.5)),
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
                '$currency${wallet.toStringAsFixed(0)}',
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
        onRefresh: () async => setState(() => _loadData()),
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _searchBar(context, scheme),
              _heroBannerCarousel(context, scheme),
              _curatedVedicGrid(context, scheme, isDark),
              _liveAstrologerConsultationSection(context, scheme, currency, isDark),
              _trendingPujasSection(context, scheme, currency),
              const SizedBox(height: 48),
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
                      children: const [
                        Icon(Icons.language, color: CustomerTheme.brandSaffron, size: 24),
                        SizedBox(width: 8),
                        Text(
                          'Select Language',
                          style: TextStyle(
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
                                ? CustomerTheme.brandSaffron.withValues(alpha: 0.12)
                                : Colors.grey.shade100,
                            border: Border.all(
                              color: isSelected
                                  ? CustomerTheme.brandSaffron
                                  : Colors.grey.shade300,
                              width: 1.5,
                            ),
                            borderRadius: BorderRadius.circular(12),
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
                                          ? CustomerTheme.brandSaffron.withValues(alpha: 0.8)
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
      child: Column(
        children: [
          GestureDetector(
            onTap: () => _goToTab(2), // Jump to Astrologer tab
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(22),
                border: Border.all(color: const Color(0xFFE2E8F0)),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.03),
                    blurRadius: 6,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Row(
                children: [
                  const Icon(Icons.search_rounded, color: Color(0xFFD97706), size: 20),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      'Search verified astrologers, pujas, kundli…',
                      style: TextStyle(
                        color: scheme.onSurfaceVariant.withValues(alpha: 0.8),
                        fontSize: 13,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 6),
          // Auspicious Muhurat status pill
          Align(
            alignment: Alignment.centerLeft,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
              decoration: BoxDecoration(
                color: const Color(0xFFFEF3C7),
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: const Color(0xFFFDE68A)),
              ),
              child: const Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text("🕉️ ", style: TextStyle(fontSize: 10)),
                  Flexible(
                    child: Text(
                      "Today's Shubh Muhurat: Amrit Kaal Active",
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF92400E),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
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
        onTap: () => _goToTab(2), // Astrologer tab
      ),
      (
        title: '24/7 Live Sanctum Darshan & Aarti',
        subtitle: 'Kashi Vishwanath, Mahakaleshwar & Somnath Temple Feeds',
        cta: 'Watch Live 🪔',
        icon: Icons.temple_hindu_rounded,
        gradient: const [Color(0xFF4A148C), Color(0xFF7B1FA2)],
        onTap: () => _goToTab(3), // Live Darshan tab
      ),
      (
        title: 'AstroMall & Energized Rudraksha',
        subtitle: '100% Certified Vedic Samagri, Gemstones & Sphatik Malas',
        cta: 'Explore Mall 🛍️',
        icon: Icons.shopping_bag_outlined,
        gradient: const [Color(0xFF1B5E20), Color(0xFF2E7D32)],
        onTap: () => _goToTab(4), // AstroMall tab
      ),
    ];

    return Column(
      children: [
        SizedBox(
          height: 154,
          child: PageView.builder(
            itemCount: curatedBanners.length,
            onPageChanged: (i) => setState(() => _bannerIndex = i),
            itemBuilder: (context, i) {
              final b = curatedBanners[i];

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
                      color: b.gradient.first.withValues(alpha: 0.3),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Padding(
                  padding: const EdgeInsets.all(14),
                  child: Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Text(
                              b.title,
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 15,
                                fontWeight: FontWeight.w800,
                                height: 1.2,
                              ),
                            ),
                            const SizedBox(height: 4),
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
                      const SizedBox(width: 8),
                      CircleAvatar(
                        radius: 32,
                        backgroundColor: Colors.white.withValues(alpha: 0.22),
                        child: Icon(b.icon, size: 34, color: Colors.white),
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

  /// Clean, 4x2 Curated Vedic Sanctum Grid (Zero horizontal clutter)
  Widget _curatedVedicGrid(BuildContext context, ColorScheme scheme, bool isDark) {
    final tools = [
      ('Free Kundli', Icons.auto_graph_rounded, const Color(0xFFFFEBEE), const Color(0xFFC62828), const KundliListScreen()),
      ('Kundli Match', Icons.favorite_rounded, const Color(0xFFFCE4EC), const Color(0xFFAD1457), const KundliMatchingScreen()),
      ('Panchang', Icons.wb_twilight_rounded, const Color(0xFFFFF8E1), const Color(0xFFF57F17), const PanchangScreen()),
      ('Horoscope', Icons.nightlight_round, const Color(0xFFEDE7F6), const Color(0xFF512DA8), const DailyHoroscopeScreen()),
      ('KP Calendar', Icons.shield_moon_rounded, const Color(0xFFE8EAF6), const Color(0xFF283593), const KpCalendarScreen()),
      ('Cosmic AI', Icons.auto_awesome_rounded, const Color(0xFFFEF3C7), const Color(0xFFD97706), const CosmicAiScreen()),
      ('Japa Mala', Icons.circle_outlined, const Color(0xFFF3E5F5), const Color(0xFF6A1B9A), const JapaMalaScreen()),
      ('My Sankalp', Icons.savings_rounded, const Color(0xFFE0F2F1), const Color(0xFF00695C), const SankalpVaultScreen()),
    ];

    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 6),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 3.5,
                height: 15,
                decoration: BoxDecoration(
                  color: CustomerTheme.brandSaffron,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              Expanded(
                child: Text(
                  'Vedic Sanctum & Daily Rituals',
                  style: Theme.of(context).textTheme.titleSmall?.copyWith(
                        fontWeight: FontWeight.w800,
                        letterSpacing: -0.2,
                      ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          GridView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: tools.length,
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 4,
              childAspectRatio: 0.88,
              crossAxisSpacing: 8,
              mainAxisSpacing: 8,
            ),
            itemBuilder: (context, i) {
              final t = tools[i];
              return InkWell(
                onTap: () => Navigator.of(context).push(
                  MaterialPageRoute(builder: (_) => t.$5),
                ),
                borderRadius: BorderRadius.circular(16),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Container(
                      width: 48,
                      height: 48,
                      decoration: BoxDecoration(
                        color: t.$3,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: t.$4.withValues(alpha: 0.25), width: 1.2),
                        boxShadow: [
                          BoxShadow(
                            color: t.$4.withValues(alpha: 0.08),
                            blurRadius: 6,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: Icon(t.$2, color: t.$4, size: 24),
                    ),
                    const SizedBox(height: 5),
                    Text(
                      t.$1,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 10.5,
                        fontWeight: FontWeight.w700,
                        color: isDark ? Colors.white70 : const Color(0xFF334155),
                        letterSpacing: -0.2,
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
      ),
    );
  }

  /// Unified Live Consultation section with ₹1 promo hook, online indicators, and direct Chat/Call buttons
  Widget _liveAstrologerConsultationSection(BuildContext context, ColorScheme scheme, String currency, bool isDark) {
    return FutureBuilder<List<Astrologer>>(
      future: _astroFuture,
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
                  Container(
                    width: 3.5,
                    height: 15,
                    decoration: BoxDecoration(
                      color: const Color(0xFFEA580C),
                      borderRadius: BorderRadius.circular(2),
                    ),
                  ),
                  const SizedBox(width: 7),
                  Flexible(
                    child: Text(
                      'Talk & Chat with Astrologers',
                      style: Theme.of(context).textTheme.titleSmall?.copyWith(
                            fontWeight: FontWeight.w800,
                            letterSpacing: -0.2,
                          ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const SizedBox(width: 6),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(
                      color: const Color(0xFFDCFCE7),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.fiber_manual_record, color: Color(0xFF16A34A), size: 7),
                        SizedBox(width: 3),
                        Text(
                          'Online',
                          style: TextStyle(
                            color: Color(0xFF15803D),
                            fontSize: 9.5,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 8),
                  GestureDetector(
                    onTap: () => _goToTab(2), // Astrologers tab
                    child: Text(
                      'View All (${list.length}) →',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w800,
                        color: CustomerTheme.brandSaffron,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            if (_firstConsultOffer?['eligible'] == true)
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 0, 16, 10),
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFFFEF3C7), Color(0xFFFDE68A)],
                    ),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: const Color(0xFFF59E0B)),
                  ),
                  child: Row(
                    children: [
                      const Text("🎉", style: TextStyle(fontSize: 18)),
                      const SizedBox(width: 8),
                      const Expanded(
                        child: Text(
                          "First 5 Mins Consultation at ₹1/min only with Verified Gurus!",
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w800,
                            color: Color(0xFF92400E),
                          ),
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: const Color(0xFFEA580C),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: const Text(
                          "₹1/min",
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 10.5,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            SizedBox(
              height: 180,
              child: ListView.separated(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                scrollDirection: Axis.horizontal,
                itemCount: list.take(8).length,
                separatorBuilder: (_, _) => const SizedBox(width: 12),
                itemBuilder: (context, i) {
                  final a = list[i];
                  final rate = a.charge > 0 ? '$currency${a.charge.toInt()}/m' : 'FREE';

                  return Container(
                    width: 154,
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: isDark ? const Color(0xFF1E1B2E) : Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: isDark ? const Color(0xFF332B4A) : const Color(0xFFE2E8F0)),
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
                        Stack(
                          children: [
                            CircleAvatar(
                              radius: 28,
                              backgroundImage: a.imageUrl.isNotEmpty ? NetworkImage(a.imageUrl) : null,
                              child: a.imageUrl.isEmpty ? _avatarFallback(a.name) : null,
                            ),
                            Positioned(
                              bottom: 0,
                              right: 0,
                              child: Container(
                                width: 12,
                                height: 12,
                                decoration: BoxDecoration(
                                  color: Colors.green,
                                  shape: BoxShape.circle,
                                  border: Border.all(color: Colors.white, width: 2),
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 6),
                        Text(
                          a.name,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          textAlign: TextAlign.center,
                          style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 12.5),
                        ),
                        Text(
                          a.primarySkill.isNotEmpty ? a.primarySkill : 'Vedic Astrology',
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: TextStyle(fontSize: 10, color: isDark ? Colors.white60 : const Color(0xFF64748B)),
                        ),
                        const Spacer(),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              rate,
                              style: const TextStyle(
                                fontWeight: FontWeight.w900,
                                fontSize: 11.5,
                                color: Color(0xFFD97706),
                              ),
                            ),
                            ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF047857),
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                visualDensity: VisualDensity.compact,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                              ),
                              onPressed: () => context.openAstrologer(a.id ?? 0),
                              child: const Text('Consult', style: TextStyle(fontSize: 10.5, fontWeight: FontWeight.bold, color: Colors.white)),
                            ),
                          ],
                        ),
                      ],
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
              padding: const EdgeInsets.fromLTRB(16, 18, 16, 10),
              child: Row(
                children: [
                  Container(
                    width: 3.5,
                    height: 15,
                    decoration: BoxDecoration(
                      color: const Color(0xFFB71C1C),
                      borderRadius: BorderRadius.circular(2),
                    ),
                  ),
                  const SizedBox(width: 7),
                  Expanded(
                    child: Text(
                      'Sacred Pujas & Chadhava',
                      style: Theme.of(context).textTheme.titleSmall?.copyWith(
                            fontWeight: FontWeight.w800,
                            letterSpacing: -0.2,
                          ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  GestureDetector(
                    onTap: () => _goToTab(1), // Puja tab
                    child: Text(
                      'View All →',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w800,
                        color: CustomerTheme.brandSaffron,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            SizedBox(
              height: 196,
              child: ListView.separated(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                scrollDirection: Axis.horizontal,
                itemCount: list.take(6).length,
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
                      width: 210,
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
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          ClipRRect(
                            borderRadius: const BorderRadius.vertical(top: Radius.circular(15)),
                            child: Stack(
                              children: [
                                p.coverImage.isNotEmpty
                                    ? Image.network(
                                        p.coverImage,
                                        height: 100,
                                        width: 210,
                                        fit: BoxFit.cover,
                                        errorBuilder: (_, e, s) => Container(
                                          height: 100,
                                          color: const Color(0xFFFDE68A),
                                          child: const Icon(Icons.temple_hindu_rounded, size: 36),
                                        ),
                                      )
                                    : Container(
                                        height: 100,
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
                                        fontSize: 8,
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
                                    fontSize: 12.5,
                                  ),
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  p.place.isNotEmpty ? p.place : 'Holy Teerth Pilgrimage',
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: TextStyle(
                                    fontSize: 10.5,
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
                                        fontSize: 13.5,
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
                                          fontSize: 10.5,
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
                _goToTab(2);
              },
            ),
            ListTile(
              leading: const Icon(Icons.temple_hindu_rounded),
              title: const Text('Live Temple Darshan'),
              onTap: () {
                Navigator.pop(context);
                _goToTab(3);
              },
            ),
            ListTile(
              leading: const Icon(Icons.storefront_rounded),
              title: const Text('AstroMall'),
              onTap: () {
                Navigator.pop(context);
                _goToTab(4);
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
              leading: const Icon(Icons.auto_awesome),
              title: const Text('OnlinePuja AI Astrologer'),
              onTap: () {
                Navigator.pop(context);
                OnlinePujaAiDialog.show(context);
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
