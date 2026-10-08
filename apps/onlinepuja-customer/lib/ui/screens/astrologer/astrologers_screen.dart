import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../theme/customer_theme.dart';
import '../../widgets/onlinepuja_ai_dialog.dart';

import '../../../app.dart';
import '../../../state/app_session.dart';
import '../cosmic_ai_screen.dart';
import '../darshan/live_darshan_screen.dart';
import '../history/history_screen.dart';
import '../horoscope/daily_horoscope_screen.dart';
import '../kundli/kundli_list_screen.dart';
import '../kundli/kundli_matching_screen.dart';
import '../mall/mall_screen.dart';
import '../notifications_screen.dart';
import '../panchang/panchang_screen.dart';
import '../profile/wallet_screen.dart';
import '../puja/puja_list_screen.dart';

/// Full-featured Spiritual Home Portal matching legacy Astroway richness:
/// - Header with Drawer, Title, Notifications badge & Green Wallet pill
/// - Search bar for astrologers, skills, pujas
/// - Horizontal quick service shortcuts (Horoscope, Kundli, Matching, Panchang, Puja, Mall, Cosmic AI, Blogs)
/// - Promotional Banner Carousel from /getCustomerHome
/// - Sticky Floating Consultation Pills: [ 💬 Chat with Astrologer ] [ 📞 Talk to Astrologer ]
/// - Astrotalk/OnlinePuja in News (Media coverage: IndiaTV, NDTV)
/// - Today's Panchang interactive strip
/// - Watch Astrology & Bhakti Videos
/// - Talk to Astrologers listing with live Chat/Call triggers
class AstrologersScreen extends StatefulWidget {
  const AstrologersScreen({super.key});

  @override
  State<AstrologersScreen> createState() => _AstrologersScreenState();
}

class _AstrologersScreenState extends State<AstrologersScreen> {
  late Future<List<Astrologer>> _future;
  Map<String, dynamic>? _homeData;
  String _query = '';
  String _selectedSkill = 'All';
  int _bannerIndex = 0;
  final ScrollController _scrollController = ScrollController();
  final GlobalKey _astrologersSectionKey = GlobalKey();
  bool _isHindi = false;

  static const List<String> _skillFilters = [
    'All',
    'Vedic',
    'Tarot',
    'Kundli',
    'Love & Relationship',
    'Career',
    'Numerology',
  ];

  @override
  void initState() {
    super.initState();
    _future = _load();
    _loadHomeData();
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  Future<void> _loadHomeData() async {
    try {
      final data = await MiscApi.instance.customerHome();
      if (mounted) setState(() => _homeData = data);
    } catch (_) {}
  }

  Future<List<Astrologer>> _load() async {
    final session = context.read<AppSession>();
    final list = await AstrologerApi.instance
        .list(userId: session.user?.id, sortBy: 'rating');
    return list.where((a) => !a.isBlock).toList();
  }

  void _reload() {
    setState(() => _future = _load());
    _loadHomeData();
  }

  void _scrollToAstrologers() {
    final context = _astrologersSectionKey.currentContext;
    if (context != null) {
      Scrollable.ensureVisible(
        context,
        duration: const Duration(milliseconds: 500),
        curve: Curves.easeInOut,
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final session = context.watch<AppSession>();
    final scheme = Theme.of(context).colorScheme;
    final currency = session.flags.currency;
    final wallet = session.user?.walletAmount ?? 0.0;

    return Scaffold(
      drawer: _drawer(context, session, scheme),
      appBar: AppBar(
        title: Text(session.flags.appName),
        actions: [
          // Language Switcher Chip (EN | हिं)
          GestureDetector(
            onTap: () {
              setState(() => _isHindi = !_isHindi);
              showSnack(context, _isHindi ? 'भाषा बदलकर हिंदी कर दी गई है।' : 'Language switched to English.');
            },
            child: Container(
              margin: const EdgeInsets.symmetric(vertical: 10, horizontal: 4),
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: _isHindi ? AppTheme.brandSaffron : scheme.surfaceContainerHighest,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: _isHindi ? AppTheme.brandSaffron : scheme.outline.withValues(alpha: 0.3)),
              ),
              child: Text(
                _isHindi ? 'हिं' : 'EN',
                style: TextStyle(
                  color: _isHindi ? Colors.white : scheme.onSurface,
                  fontWeight: FontWeight.bold,
                  fontSize: 12,
                ),
              ),
            ),
          ),
          // Wallet pill badge (green styled pill like old app)
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
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    '$currency${wallet.toStringAsFixed(1)}',
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 13,
                    ),
                  ),
                ],
              ),
            ),
          ),
          // Notification bell with badge
          IconButton(
            tooltip: 'Notifications',
            icon: const Icon(Icons.notifications_outlined),
            onPressed: () => Navigator.of(context).push(MaterialPageRoute(
                builder: (_) => const NotificationsScreen())),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async => _reload(),
        child: CustomScrollView(
          controller: _scrollController,
          physics: const AlwaysScrollableScrollPhysics(),
          slivers: [
            SliverToBoxAdapter(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _searchBar(context, scheme),
                  _heroBannerCarousel(context, scheme),
                  _quickShortcuts(context, scheme),
                  _liveAstrologerStories(context, scheme),
                  _astrologerHeaderAndFilters(context, scheme),
                ],
              ),
            ),
            FutureBuilder<List<Astrologer>>(
              future: _future,
              builder: (context, snap) => _list(context, snap, session),
            ),
            SliverToBoxAdapter(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _trustBadgesStrip(context, scheme),
                  _videosSection(context, scheme),
                  const SizedBox(height: 32),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  
  Widget _freeConsultationBanner(BuildContext context, ColorScheme scheme) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF8E2B12), Color(0xFFD97706), Color(0xFFF59E0B)],
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFFD97706).withValues(alpha: 0.35),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.2),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.card_giftcard_rounded, color: Colors.white, size: 26),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  _isHindi ? '🎁 प्रथम परामर्श 100% निःशुल्क' : '🎁 1ST CONSULTATION 100% FREE',
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w900,
                    fontSize: 13,
                    letterSpacing: 0.5,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  _isHindi ? 'शीर्ष वैदिक आचार्यों से ₹0 में बात करें' : 'Talk to India’s Top Vedic Astrologers at ₹0',
                  style: const TextStyle(color: Colors.white70, fontSize: 11),
                ),
              ],
            ),
          ),
          FilledButton(
            style: FilledButton.styleFrom(
              backgroundColor: Colors.white,
              foregroundColor: const Color(0xFF8E2B12),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              visualDensity: VisualDensity.compact,
            ),
            onPressed: () {
              setState(() => _selectedSkill = 'All');
              _scrollToAstrologers();
            },
            child: Text(
              _isHindi ? 'अभी लें ₹0' : 'Claim ₹0',
              style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 11),
            ),
          ),
        ],
      ),
    );
  }

  Widget _searchBar(BuildContext context, ColorScheme scheme) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 10, 16, 6),
      child: TextField(
        onChanged: (v) => setState(() => _query = v),
        decoration: InputDecoration(
          hintText: 'Search astrologers, skills, pujas…',
          prefixIcon: const Icon(Icons.search_rounded),
          filled: true,
          fillColor: scheme.surfaceContainerHighest.withValues(alpha: 0.4),
          contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(24),
            borderSide: BorderSide(color: scheme.outline.withValues(alpha: 0.3)),
          ),
        ),
      ),
    );
  }

  Widget _quickShortcuts(BuildContext context, ColorScheme scheme) {
    final shortcuts = [
      ('OnlinePuja\nAI', Icons.auto_awesome_rounded, const Color(0xFFFEF3C7), const Color(0xFFD97706), null, () {
        OnlinePujaAiDialog.show(context);
      }),
      ('Chat with\nAstrologer', Icons.chat_bubble_outline_rounded, const Color(0xFFE8F5E9), const Color(0xFF2E7D32), null, () {
        setState(() => _selectedSkill = 'All');
        _scrollToAstrologers();
      }),
      ('Talk to\nAstrologer', Icons.phone_in_talk_rounded, const Color(0xFFFFF3E0), const Color(0xFFE65100), null, () {
        setState(() => _selectedSkill = 'All');
        _scrollToAstrologers();
      }),
      ('Free\nKundli', Icons.auto_graph_rounded, const Color(0xFFFFEBEE), const Color(0xFFC62828), const KundliListScreen(), null),
      ('Kundli\nMatching', Icons.favorite_rounded, const Color(0xFFFCE4EC), const Color(0xFFAD1457), const KundliMatchingScreen(), null),
      ('Daily\nHoroscope', Icons.nightlight_round, const Color(0xFFEDE7F6), const Color(0xFF512DA8), const DailyHoroscopeScreen(), null),
      ("Today's\nPanchang", Icons.wb_twilight_rounded, const Color(0xFFFFF8E1), const Color(0xFFF57F17), const PanchangScreen(), null),
      ('Book a\nPuja', Icons.local_fire_department_rounded, const Color(0xFFFFE0B2), const Color(0xFFD84315), const PujaListScreen(), null),
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


  Widget _heroBannerCarousel(BuildContext context, ColorScheme scheme) {
    final curatedBanners = [
      (
        title: 'Accurate Predictions by Vedic Astrologers',
        subtitle: 'First session FREE · Guidance on Love, Marriage, Career & Wealth',
        cta: 'Consult Now',
        icon: Icons.self_improvement_rounded,
        gradient: const [Color(0xFF8E2B12), Color(0xFFD97706)],
        onTap: () => _scrollToAstrologers(),
      ),
      (
        title: '24/7 Live Sanctum Darshan & Aarti',
        subtitle: 'Kashi Vishwanath, Mahakaleshwar & Somnath Temple Feeds',
        cta: 'Watch Live 🙏',
        icon: Icons.temple_hindu_rounded,
        gradient: const [Color(0xFF4A148C), Color(0xFF7B1FA2)],
        onTap: () => Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => const LiveDarshanScreen()),
        ),
      ),
      (
        title: 'Authentic Online Puja & Hawan',
        subtitle: 'Special Navgrah & Temple Rituals with Prasad Delivery',
        cta: 'Book Puja',
        icon: Icons.local_fire_department_rounded,
        gradient: const [Color(0xFFB71C1C), Color(0xFFE53935)],
        onTap: () => Navigator.of(context).pushNamed(PujaListScreen.route),
      ),
      (
        title: 'AstroMall & Energized Rudraksha',
        subtitle: '100% Certified Vedic Samagri, Gemstones & Sphatik Malas',
        cta: 'Explore Mall',
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
                      ? AppTheme.brandSaffron
                      : scheme.outline.withValues(alpha: 0.3),
                  borderRadius: BorderRadius.circular(3),
                ),
              ),
          ],
        ),
      ],
    );
  }

  Widget _liveAstrologerStories(BuildContext context, ColorScheme scheme) {
    return FutureBuilder<List<Astrologer>>(
      future: _future,
      builder: (context, snap) {
        final all = snap.data ?? [];
        if (all.isEmpty) return const SizedBox.shrink();

        // Sort by online/live status first
        final onlineList = all.where((a) => a.isChatOnline || a.isCallOnline).toList();
        final displayList = (onlineList.isNotEmpty ? onlineList : all).take(10).toList();

        return Container(
          height: 128,
          margin: const EdgeInsets.only(top: 4, bottom: 8),
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
                    Text(
                      'Instant Connect',
                      style: TextStyle(fontSize: 11, color: scheme.outline),
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
                      onTap: () => context.openAstrologer(a.id ?? 0).then((_) => _reload()),
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
                          const SizedBox(height: 5),
                          SizedBox(
                            width: 62,
                            child: Text(
                              name,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              textAlign: TextAlign.center,
                              style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.w700),
                            ),
                          ),
                          Text(
                            rate,
                            style: TextStyle(fontSize: 9.5, color: scheme.primary, fontWeight: FontWeight.w600),
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

  Widget _astrologerHeaderAndFilters(BuildContext context, ColorScheme scheme) {
    return Column(
      key: _astrologersSectionKey,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
          child: Row(
            children: [
              Flexible(
                child: Text(
                  'Top Vedic Astrologers',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                        fontWeight: FontWeight.w800,
                        letterSpacing: -0.2,
                      ),
                ),
              ),
              const SizedBox(width: 8),
              Text(
                'Instant Chat & Call',
                style: TextStyle(
                  fontSize: 11.5,
                  fontWeight: FontWeight.w600,
                  color: CustomerTheme.brandSaffron,
                ),
              ),
            ],
          ),
        ),
        SizedBox(
          height: 38,
          child: ListView.separated(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 16),
            itemCount: _skillFilters.length,
            separatorBuilder: (_, _) => const SizedBox(width: 8),
            itemBuilder: (context, i) {
              final skill = _skillFilters[i];
              final isSelected = _selectedSkill == skill;
              return ChoiceChip(
                label: Text(skill),
                selected: isSelected,
                labelStyle: TextStyle(
                  fontSize: 12,
                  fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
                  color: isSelected ? Colors.white : scheme.onSurface,
                ),
                selectedColor: AppTheme.brandSaffron,
                backgroundColor: scheme.surfaceContainerHighest.withValues(alpha: 0.35),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                side: BorderSide(
                  color: isSelected
                      ? AppTheme.brandSaffron
                      : scheme.outline.withValues(alpha: 0.25),
                ),
                onSelected: (_) => setState(() => _selectedSkill = skill),
              );
            },
          ),
        ),
        const SizedBox(height: 6),
      ],
    );
  }

  Widget _videosSection(BuildContext context, ColorScheme scheme) {
    final videos = _homeData?['astrologyVideo'] as List? ?? [];
    if (videos.isEmpty) return const SizedBox.shrink();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 14, 16, 8),
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


  Widget _drawer(BuildContext context, AppSession session, ColorScheme scheme) {
    final u = session.user;
    final currency = session.flags.currency;
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
                  colors: [AppTheme.brandSaffron, AppTheme.brandDeep],
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
              leading: const Icon(Icons.local_fire_department_rounded),
              title: const Text('Book a Puja'),
              onTap: () {
                Navigator.pop(context);
                Navigator.of(context).push(MaterialPageRoute(builder: (_) => const PujaListScreen()));
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


  Widget _trustBadgesStrip(BuildContext context, ColorScheme scheme) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
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
          _trustItem(Icons.workspace_premium_rounded, 'Verified', 'Vedic Astrologers', scheme),
          Container(height: 28, width: 1, color: scheme.outline.withValues(alpha: 0.2)),
          _trustItem(Icons.security_rounded, 'Secure', 'Instant Payments', scheme),
        ],
      ),
    );
  }

  Widget _trustItem(IconData icon, String line1, String line2, ColorScheme scheme) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 22, color: AppTheme.brandSaffron),
        const SizedBox(height: 4),
        Text(line1, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 10.5)),
        Text(line2, style: TextStyle(fontSize: 9.5, color: scheme.outline)),
      ],
    );
  }

  void _joinWaitlist(BuildContext context, Astrologer a, AppSession session) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) {
        return Padding(
          padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade300,
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                children: [
                  CircleAvatar(
                    backgroundImage: NetworkImage(a.imageUrl),
                    radius: 22,
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(a.name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                        Text('${a.primarySkill} · In consultation / offline', style: TextStyle(fontSize: 12, color: Colors.grey.shade600)),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              const Text(
                'Join the Priority Waitlist to receive an instant push notification the moment this astrologer becomes available.',
                style: TextStyle(fontSize: 13, height: 1.4),
              ),
              const SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Navigator.of(ctx).pop(),
                      child: const Text('Cancel'),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: FilledButton(
                      style: FilledButton.styleFrom(backgroundColor: AppTheme.brandSaffron),
                      onPressed: () async {
                        Navigator.of(ctx).pop();
                        final ok = await AstrologerApi.instance.addToWaitList(
                          astrologerId: a.id ?? 0,
                          requestType: 'Chat',
                          userName: session.user?.name ?? 'Devotee',
                          userId: session.user?.id,
                        );
                        if (context.mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text(ok
                                  ? '🙏 Added to ${a.name} priority queue! You will be alerted.'
                                  : 'Could not join waitlist. Please try again.'),
                            ),
                          );
                        }
                      },
                      child: const Text('Join Waitlist', style: TextStyle(fontWeight: FontWeight.bold)),
                    ),
                  ),
                ],
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _list(BuildContext context,
      AsyncSnapshot<List<Astrologer>> snap, AppSession session) {
    if (snap.connectionState == ConnectionState.waiting) {
      return SliverToBoxAdapter(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
          child: StatusViews.skeletonList(context, items: 3),
        ),
      );
    }
    if (snap.hasError) {
      return SliverToBoxAdapter(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
          child: StatusViews.error(context, snap.error!, onRetry: _reload),
        ),
      );
    }
    final all = snap.data ?? const <Astrologer>[];
    final q = _query.trim().toLowerCase();
    var items = all;
    if (q.isNotEmpty) {
      items = items
          .where((a) =>
              a.name.toLowerCase().contains(q) ||
              a.primarySkill.toLowerCase().contains(q) ||
              a.allSkill.toLowerCase().contains(q))
          .toList();
    }
    if (_selectedSkill != 'All') {
      final s = _selectedSkill.toLowerCase();
      items = items
          .where((a) =>
              a.primarySkill.toLowerCase().contains(s) ||
              a.allSkill.toLowerCase().contains(s))
          .toList();
    }
    if (items.isEmpty) {
      return SliverToBoxAdapter(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 32),
          child: StatusViews.empty(
            context,
            message: _selectedSkill == 'All'
                ? 'No astrologers found.'
                : 'No $_selectedSkill astrologers available right now.',
          ),
        ),
      );
    }
    return SliverPadding(
      padding: const EdgeInsets.fromLTRB(16, 6, 16, 24),
      sliver: SliverList.separated(
        itemCount: items.length,
        separatorBuilder: (_, _) => const SizedBox(height: 12),
        itemBuilder: (context, i) {
          final a = items[i];
          final isFree =
              a.isFreeAvailable && !(session.user?.isFreeChat ?? false);
          return AstrologerCard(
            astrologer: a,
            onTap: () =>
                context.openAstrologer(a.id ?? 0).then((_) => _reload()),
            onChat: a.isChatOnline
                ? () => context.openChat(
                      astrologerId: a.id ?? 0,
                      astrologerName: a.name,
                      isFree: isFree,
                    )
                : null,
            onCall: a.isCallOnline
                ? () => context.openCall(
                      astrologerId: a.id ?? 0,
                      astrologerName: a.name,
                      ratePerMinute: a.charge > 0 ? a.charge.toDouble() : 15.0,
                    )
                : null,
            onWaitlist: () => _joinWaitlist(context, a, session),
          );
        },
      ),
    );
  }
}
