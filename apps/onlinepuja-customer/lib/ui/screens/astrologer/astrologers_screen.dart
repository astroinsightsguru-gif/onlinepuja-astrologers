import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../theme/customer_theme.dart';
import '../../../app.dart';
import '../../../state/app_session.dart';
import '../notifications_screen.dart';
import '../profile/wallet_screen.dart';

/// High-Quality, High-Conversion Astrologer Landing & Consultation Hub
/// Clutter-free design focused exclusively on discovering and consulting verified Vedic astrologers.
class AstrologersScreen extends StatefulWidget {
  const AstrologersScreen({super.key});

  @override
  State<AstrologersScreen> createState() => _AstrologersScreenState();
}

class _AstrologersScreenState extends State<AstrologersScreen> {
  late Future<List<Astrologer>> _future;
  Map<String, dynamic>? _firstConsultOffer;
  String _query = '';
  String _selectedSkill = 'All';
  String _availabilityFilter = 'all'; // all, online, chat, call, offer
  String _sortBy = 'rating_desc'; // rating_desc, exp_desc, price_asc, price_desc
  final TextEditingController _searchController = TextEditingController();

  static const List<String> _skillFilters = [
    'All',
    'Vedic',
    'Tarot',
    'Kundli',
    'Love & Relationship',
    'Career & Wealth',
    'Marriage',
    'Vastu',
    'Numerology',
  ];

  @override
  void initState() {
    super.initState();
    _future = _load();
    _loadOffer();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadOffer() async {
    try {
      final session = context.read<AppSession>();
      final offer = await MiscApi.instance.checkFirstConsultOffer(userId: session.user?.id);
      if (mounted) setState(() => _firstConsultOffer = offer);
    } catch (_) {}
  }

  Future<List<Astrologer>> _load() async {
    final session = context.read<AppSession>();
    final list = await AstrologerApi.instance.list(
      userId: session.user?.id,
      sortBy: 'rating',
    );
    return list.where((a) => !a.isBlock).toList();
  }

  void _reload() {
    setState(() => _future = _load());
    _loadOffer();
  }

  @override
  Widget build(BuildContext context) {
    final session = context.watch<AppSession>();
    final scheme = Theme.of(context).colorScheme;
    final currency = session.flags.currency.isNotEmpty ? session.flags.currency : '₹';
    final wallet = session.user?.walletAmount ?? 0.0;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F0C1B) : const Color(0xFFF9F7F2),
      appBar: AppBar(
        backgroundColor: isDark ? const Color(0xFF161224) : Colors.white,
        elevation: 0.5,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(children: [RichText(text: TextSpan(children: [TextSpan(text: 'OnlinePuja', style: TextStyle(fontSize: 16.5, fontWeight: FontWeight.w900, color: isDark ? Colors.white : const Color(0xFF1E293B), letterSpacing: -0.2)), const TextSpan(text: '.live', style: TextStyle(fontSize: 16.5, fontWeight: FontWeight.w900, color: Color(0xFFD97706), letterSpacing: -0.2))])), const SizedBox(width: 5), const Text('?', style: TextStyle(fontSize: 13))]), const SizedBox(height: 1),
            const Text(
              'Verified Vedic Acharyas & Tarot Readers',
              style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
            ),
          ],
        ),
        actions: [
          // Wallet pill with quick recharge action
          GestureDetector(
            onTap: () => Navigator.of(context)
                .pushNamed(WalletScreen.route)
                .then((_) => session.refreshUser()),
            child: Container(
              margin: const EdgeInsets.symmetric(vertical: 10, horizontal: 6),
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: const Color(0xFF047857),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF047857).withOpacity(0.25),
                    blurRadius: 4,
                  ),
                ],
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Icon(Icons.account_balance_wallet_rounded, size: 13, color: Colors.white),
                  const SizedBox(width: 4),
                  Text(
                    '$currency${wallet.toStringAsFixed(0)}',
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w800,
                      fontSize: 12.5,
                    ),
                  ),
                  const SizedBox(width: 4),
                  const Text('+', style: TextStyle(color: Colors.white70, fontSize: 13, fontWeight: FontWeight.bold)),
                ],
              ),
            ),
          ),
          IconButton(
            tooltip: 'Notifications',
            icon: const Icon(Icons.notifications_outlined),
            onPressed: () => Navigator.of(context).push(
              MaterialPageRoute(builder: (_) => const NotificationsScreen()),
            ),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async => _reload(),
        child: CustomScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          slivers: [
            // 1. First Consultation Promotional Banner
            if (_firstConsultOffer?['eligible'] == true)
              SliverToBoxAdapter(
                child: _firstConsultBanner(context),
              ),

            // 2. Live Online Stories Carousel
            SliverToBoxAdapter(
              child: _liveStoriesStrip(context, scheme),
            ),

            // 3. Search and Filter Hub
            SliverToBoxAdapter(
              child: _searchAndFilterHub(context, scheme),
            ),

            // 4. Astrologer Cards List
            FutureBuilder<List<Astrologer>>(
              future: _future,
              builder: (context, snap) => _buildAstrologerList(context, snap, session, currency),
            ),

            // 5. Minimalist Sanctum Trust Strip
            SliverToBoxAdapter(
              child: _trustGuaranteeStrip(context),
            ),
          ],
        ),
      ),
    );
  }

  /// Promotional banner for ₹1 introductory offer
  Widget _firstConsultBanner(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(14, 10, 14, 6),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF78350F), Color(0xFF92400E), Color(0xFFB45309)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF78350F).withOpacity(0.2),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.15),
              shape: BoxShape.circle,
            ),
            child: const Text('🎉', style: TextStyle(fontSize: 20)),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: const [
                Text(
                  'FIRST CONSULTATION AT ₹1 ONLY',
                  style: TextStyle(
                    color: Color(0xFFFDE68A),
                    fontSize: 12.5,
                    fontWeight: FontWeight.w900,
                    letterSpacing: 0.5,
                  ),
                ),
                SizedBox(height: 2),
                Text(
                  '5 minutes introductory consultation with any verified Astrologer',
                  style: TextStyle(color: Colors.white70, fontSize: 11),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
            decoration: BoxDecoration(
              color: const Color(0xFFFBBF24),
              borderRadius: BorderRadius.circular(10),
            ),
            child: const Text(
              '₹1 / min',
              style: TextStyle(
                color: Color(0xFF78350F),
                fontWeight: FontWeight.w900,
                fontSize: 12,
              ),
            ),
          ),
        ],
      ),
    );
  }

  /// Live Online Astrologers Quick Connect Stories Bar
  Widget _liveStoriesStrip(BuildContext context, ColorScheme scheme) {
    return FutureBuilder<List<Astrologer>>(
      future: _future,
      builder: (context, snap) {
        final all = snap.data ?? [];
        if (all.isEmpty) return const SizedBox.shrink();

        final online = all.where((a) => a.isChatOnline || a.isCallOnline).toList();
        final display = (online.isNotEmpty ? online : all).take(12).toList();

        return Container(
          height: 106,
          margin: const EdgeInsets.only(top: 8, bottom: 4),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 2),
                child: Row(
                  children: [
                    Container(
                      width: 8,
                      height: 8,
                      decoration: const BoxDecoration(
                        color: Color(0xFF10B981),
                        shape: BoxShape.circle,
                      ),
                    ),
                    const SizedBox(width: 6),
                    const Text(
                      'ONLINE FOR CONSULTATION',
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w900,
                        letterSpacing: 0.5,
                        color: Color(0xFF047857),
                      ),
                    ),
                    const Spacer(),
                    Text(
                      '${online.length} Active Now',
                      style: const TextStyle(fontSize: 11, color: Color(0xFF64748B), fontWeight: FontWeight.w600),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 6),
              Expanded(
                child: ListView.separated(
                  padding: const EdgeInsets.symmetric(horizontal: 14),
                  scrollDirection: Axis.horizontal,
                  itemCount: display.length,
                  separatorBuilder: (_, _) => const SizedBox(width: 12),
                  itemBuilder: (context, i) {
                    final a = display[i];
                    final isOnline = a.isChatOnline || a.isCallOnline;

                    return GestureDetector(
                      onTap: () => context.openAstrologer(a.id ?? 0).then((_) => _reload()),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Stack(
                            alignment: Alignment.center,
                            children: [
                              Container(
                                width: 52,
                                height: 52,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  gradient: isOnline
                                      ? const LinearGradient(
                                          colors: [Color(0xFF10B981), Color(0xFF059669)],
                                        )
                                      : const LinearGradient(
                                          colors: [Color(0xFFD97706), Color(0xFFB45309)],
                                        ),
                                ),
                                padding: const EdgeInsets.all(2),
                                child: ClipOval(
                                  child: a.imageUrl.isNotEmpty
                                      ? Image.network(
                                          a.imageUrl,
                                          fit: BoxFit.cover,
                                          errorBuilder: (_, _, _) => _avatarFallback(a.name),
                                        )
                                      : _avatarFallback(a.name),
                                ),
                              ),
                              if (isOnline)
                                Positioned(
                                  bottom: 0,
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 1),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFF10B981),
                                      borderRadius: BorderRadius.circular(6),
                                      border: Border.all(color: Colors.white, width: 1),
                                    ),
                                    child: const Text(
                                      'LIVE',
                                      style: TextStyle(
                                        color: Colors.white,
                                        fontSize: 7.5,
                                        fontWeight: FontWeight.w900,
                                      ),
                                    ),
                                  ),
                                ),
                            ],
                          ),
                          const SizedBox(height: 4),
                          SizedBox(
                            width: 60,
                            child: Text(
                              a.name.split(' ').first,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              textAlign: TextAlign.center,
                              style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.w700),
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

  /// Search bar + Availability filter pills + Vedic skill chips
  Widget _searchAndFilterHub(BuildContext context, ColorScheme scheme) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(14, 4, 14, 6),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // 1. Search Bar with Clear Icon
          Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: const Color(0xFFE2E8F0)),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.03),
                  blurRadius: 6,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: TextField(
              controller: _searchController,
              onChanged: (v) => setState(() => _query = v),
              decoration: InputDecoration(
                hintText: 'Search by Astrologer name, Vedic, Tarot, Love…',
                hintStyle: const TextStyle(fontSize: 12.5, color: Color(0xFF94A3B8)),
                prefixIcon: const Icon(Icons.search_rounded, size: 20, color: Color(0xFF64748B)),
                suffixIcon: _query.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear, size: 18),
                        onPressed: () {
                          _searchController.clear();
                          setState(() => _query = '');
                        },
                      )
                    : null,
                border: InputBorder.none,
                contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
              ),
            ),
          ),
          const SizedBox(height: 10),

          // 2. Consultation Mode Filter Chips (All, Online, Chat, Call, Offer)
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: [
                _filterChip('all', '🌟 All Astrologers'),
                const SizedBox(width: 6),
                _filterChip('online', '🟢 Online Now'),
                const SizedBox(width: 6),
                _filterChip('chat', '💬 Chat Ready'),
                const SizedBox(width: 6),
                _filterChip('call', '📞 Call Ready'),
                const SizedBox(width: 6),
                _filterChip('offer', '🎁 ₹1 Promo'),
              ],
            ),
          ),
          const SizedBox(height: 8),

          // 3. Vedic Specialization Skill Filter Chips
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: _skillFilters.map((s) {
                final isSelected = _selectedSkill == s;
                return Padding(
                  padding: const EdgeInsets.only(right: 6),
                  child: FilterChip(
                    label: Text(s, style: TextStyle(fontSize: 11, fontWeight: isSelected ? FontWeight.w800 : FontWeight.w500)),
                    selected: isSelected,
                    selectedColor: const Color(0xFFFEF3C7),
                    checkmarkColor: const Color(0xFFD97706),
                    backgroundColor: Colors.white,
                    side: BorderSide(
                      color: isSelected ? const Color(0xFFD97706) : const Color(0xFFE2E8F0),
                    ),
                    onSelected: (_) => setState(() => _selectedSkill = s),
                  ),
                );
              }).toList(),
            ),
          ),
          const SizedBox(height: 6),
        ],
      ),
    );
  }

  Widget _filterChip(String key, String label) {
    final isSelected = _availabilityFilter == key;
    return ChoiceChip(
      label: Text(
        label,
        style: TextStyle(
          fontSize: 11.5,
          fontWeight: isSelected ? FontWeight.w800 : FontWeight.w600,
          color: isSelected ? Colors.white : const Color(0xFF334155),
        ),
      ),
      selected: isSelected,
      selectedColor: const Color(0xFFD97706),
      backgroundColor: Colors.white,
      side: BorderSide(color: isSelected ? const Color(0xFFD97706) : const Color(0xFFE2E8F0)),
      onSelected: (_) => setState(() => _availabilityFilter = key),
    );
  }

  /// High-Quality Astrologer Cards List
  Widget _buildAstrologerList(
    BuildContext context,
    AsyncSnapshot<List<Astrologer>> snap,
    AppSession session,
    String currency,
  ) {
    if (snap.connectionState == ConnectionState.waiting) {
      return SliverToBoxAdapter(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
          child: StatusViews.skeletonList(context, items: 4),
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
    var filtered = all;

    // Search query filter
    final q = _query.trim().toLowerCase();
    if (q.isNotEmpty) {
      filtered = filtered.where((a) {
        return a.name.toLowerCase().contains(q) ||
            a.primarySkill.toLowerCase().contains(q) ||
            a.allSkill.toLowerCase().contains(q) ||
            a.languageKnown.toLowerCase().contains(q);
      }).toList();
    }

    // Skill filter
    if (_selectedSkill != 'All') {
      final s = _selectedSkill.toLowerCase();
      filtered = filtered.where((a) {
        return a.primarySkill.toLowerCase().contains(s) || a.allSkill.toLowerCase().contains(s);
      }).toList();
    }

    // Availability filter
    if (_availabilityFilter == 'online') {
      filtered = filtered.where((a) => a.isChatOnline || a.isCallOnline).toList();
    } else if (_availabilityFilter == 'chat') {
      filtered = filtered.where((a) => a.isChatOnline).toList();
    } else if (_availabilityFilter == 'call') {
      filtered = filtered.where((a) => a.isCallOnline).toList();
    } else if (_availabilityFilter == 'offer') {
      filtered = filtered.where((a) => a.isFreeAvailable || a.charge > 0).toList();
    }

    if (filtered.isEmpty) {
      return SliverToBoxAdapter(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 48),
          child: Center(
            child: Column(
              children: [
                const Icon(Icons.search_off_rounded, size: 48, color: Color(0xFF94A3B8)),
                const SizedBox(height: 12),
                const Text(
                  'No Astrologers Match Your Filter',
                  style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16),
                ),
                const SizedBox(height: 4),
                const Text(
                  'Try resetting filters or searching for another Vedic skill.',
                  style: TextStyle(color: Color(0xFF64748B), fontSize: 12),
                ),
                const SizedBox(height: 16),
                OutlinedButton(
                  onPressed: () {
                    setState(() {
                      _query = '';
                      _selectedSkill = 'All';
                      _availabilityFilter = 'all';
                      _searchController.clear();
                    });
                  },
                  child: const Text('Reset All Filters'),
                ),
              ],
            ),
          ),
        ),
      );
    }

    final isEligibleForOffer = _firstConsultOffer?['eligible'] == true;

    return SliverPadding(
      padding: const EdgeInsets.fromLTRB(14, 4, 14, 20),
      sliver: SliverList.separated(
        itemCount: filtered.length,
        separatorBuilder: (_, _) => const SizedBox(height: 12),
        itemBuilder: (context, i) {
          final a = filtered[i];
          return _astrologerLandingCard(context, a, session, currency, isEligibleForOffer);
        },
      ),
    );
  }

  /// State-of-the-Art Astrologer Landing Card
  Widget _astrologerLandingCard(
    BuildContext context,
    Astrologer a,
    AppSession session,
    String currency,
    bool isEligibleForOffer,
  ) {
    final isOnline = a.isChatOnline || a.isCallOnline;
    final rate = a.charge > 0 ? '$currency${a.charge.toInt()}/min' : 'FREE';

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(18),
          onTap: () => context.openAstrologer(a.id ?? 0).then((_) => _reload()),
          child: Padding(
            padding: const EdgeInsets.all(14),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Top Row: Avatar + Details + Price
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Avatar with Status Badge
                    Stack(
                      children: [
                        Container(
                          width: 68,
                          height: 68,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(
                              color: isOnline ? const Color(0xFF10B981) : const Color(0xFFE2E8F0),
                              width: 2,
                            ),
                          ),
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(14),
                            child: a.imageUrl.isNotEmpty
                                ? Image.network(
                                    a.imageUrl,
                                    fit: BoxFit.cover,
                                    errorBuilder: (_, _, _) => _avatarFallback(a.name),
                                  )
                                : _avatarFallback(a.name),
                          ),
                        ),
                        if (isOnline)
                          Positioned(
                            bottom: 2,
                            right: 2,
                            child: Container(
                              width: 14,
                              height: 14,
                              decoration: BoxDecoration(
                                color: const Color(0xFF10B981),
                                shape: BoxShape.circle,
                                border: Border.all(color: Colors.white, width: 2),
                              ),
                            ),
                          ),
                      ],
                    ),
                    const SizedBox(width: 12),

                    // Information Column
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Flexible(
                                child: Text(
                                  a.name,
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: const TextStyle(
                                    fontWeight: FontWeight.w800,
                                    fontSize: 15,
                                    color: Color(0xFF0F172A),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 4),
                              const Icon(Icons.verified_rounded, size: 16, color: Color(0xFFD97706)),
                            ],
                          ),
                          const SizedBox(height: 3),
                          Text(
                            a.primarySkill.isNotEmpty ? a.primarySkill : 'Vedic Astrologer',
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                              fontSize: 12,
                              color: Color(0xFF475569),
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                          const SizedBox(height: 3),
                          Text(
                            a.languageKnown.isNotEmpty ? a.languageKnown : 'Hindi, English',
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8)),
                          ),
                          const SizedBox(height: 5),

                          // Rating and Experience badges
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFFEF3C7),
                                  borderRadius: BorderRadius.circular(6),
                                ),
                                child: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    const Icon(Icons.star_rounded, size: 13, color: Color(0xFFD97706)),
                                    const SizedBox(width: 2),
                                    Text(
                                      a.rating.toStringAsFixed(1),
                                      style: const TextStyle(
                                        fontSize: 11,
                                        fontWeight: FontWeight.w800,
                                        color: Color(0xFF92400E),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              const SizedBox(width: 6),
                              Text(
                                '${a.experienceInYears}+ yrs exp',
                                style: const TextStyle(fontSize: 11, color: Color(0xFF64748B), fontWeight: FontWeight.w600),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),

                    // Price Block
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        if (isEligibleForOffer && a.charge > 0) ...[
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                            decoration: BoxDecoration(
                              color: const Color(0xFFFEE2E2),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: const Text(
                              '₹1 OFFER',
                              style: TextStyle(
                                color: Color(0xFFB91C1C),
                                fontWeight: FontWeight.w900,
                                fontSize: 9.5,
                              ),
                            ),
                          ),
                          const SizedBox(height: 2),
                          const Text(
                            '₹1/min',
                            style: TextStyle(
                              fontSize: 15,
                              fontWeight: FontWeight.w900,
                              color: Color(0xFFDC2626),
                            ),
                          ),
                          Text(
                            rate,
                            style: const TextStyle(
                              fontSize: 11,
                              decoration: TextDecoration.lineThrough,
                              color: Color(0xFF94A3B8),
                            ),
                          ),
                        ] else ...[
                          Text(
                            rate,
                            style: const TextStyle(
                              fontSize: 14.5,
                              fontWeight: FontWeight.w900,
                              color: Color(0xFF047857),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ],
                ),
                const SizedBox(height: 12),

                // Divider line
                const Divider(height: 1, color: Color(0xFFF1F5F9)),
                const SizedBox(height: 10),

                // Action Buttons Row: Chat, Call, Waitlist
                Row(
                  children: [
                    // Chat Action Button
                    Expanded(
                      child: OutlinedButton.icon(
                        style: OutlinedButton.styleFrom(
                          foregroundColor: a.isChatOnline ? const Color(0xFF047857) : const Color(0xFF94A3B8),
                          side: BorderSide(
                            color: a.isChatOnline ? const Color(0xFF10B981) : const Color(0xFFCBD5E1),
                            width: 1.2,
                          ),
                          padding: const EdgeInsets.symmetric(vertical: 9),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                        icon: Icon(
                          Icons.chat_bubble_outline_rounded,
                          size: 16,
                          color: a.isChatOnline ? const Color(0xFF047857) : const Color(0xFF94A3B8),
                        ),
                        label: Text(
                          a.isChatOnline ? 'Chat Now' : 'Chat Offline',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: a.isChatOnline ? const Color(0xFF047857) : const Color(0xFF94A3B8),
                          ),
                        ),
                        onPressed: a.isChatOnline
                            ? () => context.openChat(
                                  astrologerId: a.id ?? 0,
                                  astrologerName: a.name,
                                  isFree: a.isFreeAvailable,
                                )
                            : () => _joinWaitlist(context, a, session),
                      ),
                    ),
                    const SizedBox(width: 10),

                    // Call Action Button
                    Expanded(
                      child: FilledButton.icon(
                        style: FilledButton.styleFrom(
                          backgroundColor: a.isCallOnline ? const Color(0xFFD97706) : const Color(0xFFE2E8F0),
                          foregroundColor: a.isCallOnline ? Colors.white : const Color(0xFF64748B),
                          padding: const EdgeInsets.symmetric(vertical: 9),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                        icon: Icon(
                          Icons.phone_rounded,
                          size: 16,
                          color: a.isCallOnline ? Colors.white : const Color(0xFF94A3B8),
                        ),
                        label: Text(
                          a.isCallOnline ? 'Call Now' : 'Call Offline',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: a.isCallOnline ? Colors.white : const Color(0xFF64748B),
                          ),
                        ),
                        onPressed: a.isCallOnline
                            ? () => context.openCall(
                                  astrologerId: a.id ?? 0,
                                  astrologerName: a.name,
                                  ratePerMinute: a.charge > 0 ? a.charge.toDouble() : 15.0,
                                )
                            : () => _joinWaitlist(context, a, session),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  /// Minimalist Trust Strip
  Widget _trustGuaranteeStrip(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 8, 16, 28),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: const [
          _TrustItem(icon: Icons.lock_outline, label: '100% Confidential'),
          _TrustItem(icon: Icons.verified_user_outlined, label: 'Certified Astrologers'),
          _TrustItem(icon: Icons.bolt_rounded, label: 'Instant Connect'),
        ],
      ),
    );
  }

  Widget _avatarFallback(String name) {
    final initial = name.isNotEmpty ? name[0].toUpperCase() : 'A';
    return Container(
      color: const Color(0xFFFEF3C7),
      child: Center(
        child: Text(
          initial,
          style: const TextStyle(fontWeight: FontWeight.w900, color: Color(0xFFD97706), fontSize: 20),
        ),
      ),
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
                    backgroundImage: a.imageUrl.isNotEmpty ? NetworkImage(a.imageUrl) : null,
                    radius: 22,
                    child: a.imageUrl.isEmpty ? _avatarFallback(a.name) : null,
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(a.name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                        Text('${a.primarySkill} · Currently in Consultation', style: TextStyle(fontSize: 12, color: Colors.grey.shade600)),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              const Text(
                'Join the Priority Queue to receive an instant push notification the moment this astrologer finishes their session.',
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
                      style: FilledButton.styleFrom(backgroundColor: const Color(0xFFD97706)),
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
}

class _TrustItem extends StatelessWidget {
  const _TrustItem({required this.icon, required this.label});
  final IconData icon;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 15, color: const Color(0xFFD97706)),
        const SizedBox(width: 5),
        Text(
          label,
          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: Color(0xFF475569)),
        ),
      ],
    );
  }
}

