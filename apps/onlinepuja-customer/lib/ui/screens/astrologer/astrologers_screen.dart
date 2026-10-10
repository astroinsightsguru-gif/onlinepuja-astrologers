import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../app.dart';
import '../../../state/app_session.dart';
import '../../theme/customer_theme.dart';
import '../notifications_screen.dart';
import '../profile/wallet_screen.dart';

/// Clean, Luxury & High-Conversion Astrologer Discovery & Consultation Directory.
class AstrologersScreen extends StatefulWidget {
  const AstrologersScreen({super.key});

  @override
  State<AstrologersScreen> createState() => _AstrologersScreenState();
}

class _AstrologersScreenState extends State<AstrologersScreen> {
  late Future<List<Astrologer>> _future;
  Map<String, dynamic>? _firstConsultOffer;
  String _query = '';
  String _selectedFilter = 'All';
  final TextEditingController _searchController = TextEditingController();

  static const List<String> _filters = [
    'All',
    'Online Now',
    'Vedic',
    'Tarot',
    'Kundli',
    'Love & Relations',
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
    final currency = session.flags.currency.isNotEmpty ? session.flags.currency : '₹';
    final wallet = session.user?.walletAmount ?? 0.0;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F0C1B) : const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: isDark ? const Color(0xFF161224) : Colors.white,
        elevation: 0.5,
        titleSpacing: 16,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Text(
                  'OnlinePuja',
                  style: TextStyle(
                    fontSize: 17,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -0.3,
                    color: isDark ? Colors.white : const Color(0xFF1E293B),
                  ),
                ),
                const Text(
                  '.live',
                  style: TextStyle(
                    fontSize: 17,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -0.3,
                    color: Color(0xFFD97706),
                  ),
                ),
                const SizedBox(width: 6),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 1.5),
                  decoration: BoxDecoration(
                    color: const Color(0xFF10B981).withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: const Text(
                    'VERIFIED',
                    style: TextStyle(
                      fontSize: 8.5,
                      fontWeight: FontWeight.w900,
                      color: Color(0xFF10B981),
                      letterSpacing: 0.5,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 1),
            Text(
              'Certified Vedic Acharyas & Astrologers',
              style: TextStyle(
                fontSize: 11,
                color: isDark ? Colors.white60 : const Color(0xFF64748B),
                fontWeight: FontWeight.w500,
              ),
            ),
          ],
        ),
        actions: [
          // Wallet Balance Chip
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
                    color: const Color(0xFF047857).withValues(alpha: 0.25),
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
            icon: const Icon(Icons.notifications_none_rounded),
            onPressed: () => Navigator.of(context).push(
              MaterialPageRoute(builder: (_) => const NotificationsScreen()),
            ),
          ),
          const SizedBox(width: 4),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async => _reload(),
        child: CustomScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          slivers: [
            // 1. Promotional ₹1 Banner (Clean Ribbon)
            if (_firstConsultOffer?['eligible'] == true)
              SliverToBoxAdapter(
                child: _firstConsultRibbon(context),
              ),

            // 2. Search Field
            SliverToBoxAdapter(
              child: _searchBar(isDark),
            ),

            // 3. Filter Category Pills
            SliverToBoxAdapter(
              child: _filterPills(isDark),
            ),

            // 4. Astrologer Directory List
            FutureBuilder<List<Astrologer>>(
              future: _future,
              builder: (context, snap) => _buildAstrologerList(context, snap, session, currency, isDark),
            ),

            // Bottom Spacing
            const SliverToBoxAdapter(child: SizedBox(height: 32)),
          ],
        ),
      ),
    );
  }

  /// Compact Promotional Ribbon
  Widget _firstConsultRibbon(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 12, 16, 4),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF78350F), Color(0xFFB45309)],
          begin: Alignment.centerLeft,
          end: Alignment.centerRight,
        ),
        borderRadius: BorderRadius.circular(14),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF78350F).withValues(alpha: 0.25),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: const Row(
        children: [
          Icon(Icons.stars_rounded, color: Color(0xFFFDE68A), size: 20),
          SizedBox(width: 10),
          Expanded(
            child: Text(
              'First consultation at ₹1 only • 5 mins free guidance',
              style: TextStyle(
                color: Color(0xFFFEF3C7),
                fontSize: 12,
                fontWeight: FontWeight.w700,
                letterSpacing: 0.1,
              ),
            ),
          ),
          Icon(Icons.arrow_forward_ios_rounded, color: Color(0xFFFDE68A), size: 12),
        ],
      ),
    );
  }

  /// Clean Search Field
  Widget _searchBar(bool isDark) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
      child: Container(
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF1E1830) : Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: isDark ? Colors.white12 : const Color(0xFFE2E8F0),
            width: 1,
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: isDark ? 0.2 : 0.03),
              blurRadius: 6,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: TextField(
          controller: _searchController,
          onChanged: (v) => setState(() => _query = v),
          style: TextStyle(
            fontSize: 13.5,
            color: isDark ? Colors.white : const Color(0xFF1E293B),
          ),
          decoration: InputDecoration(
            hintText: 'Search astrologers by name, Vedic skill or language…',
            hintStyle: TextStyle(
              fontSize: 12.5,
              color: isDark ? Colors.white38 : const Color(0xFF94A3B8),
            ),
            prefixIcon: Icon(
              Icons.search_rounded,
              size: 20,
              color: isDark ? Colors.white54 : const Color(0xFF64748B),
            ),
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
    );
  }

  /// Clean Horizontal Filter Pills
  Widget _filterPills(bool isDark) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: SizedBox(
        height: 38,
        child: ListView.separated(
          scrollDirection: Axis.horizontal,
          physics: const BouncingScrollPhysics(),
          padding: const EdgeInsets.symmetric(horizontal: 16),
          itemCount: _filters.length,
          separatorBuilder: (_, __) => const SizedBox(width: 8),
          itemBuilder: (context, i) {
            final f = _filters[i];
            final isSelected = _selectedFilter == f;
            final isOnlinePill = f == 'Online Now';

            return GestureDetector(
              onTap: () => setState(() => _selectedFilter = f),
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 180),
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                decoration: BoxDecoration(
                  color: isSelected
                      ? CustomerTheme.brandSaffron
                      : (isDark ? const Color(0xFF1E1830) : Colors.white),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(
                    color: isSelected
                        ? CustomerTheme.brandSaffron
                        : (isDark ? Colors.white12 : const Color(0xFFE2E8F0)),
                    width: 1,
                  ),
                  boxShadow: isSelected
                      ? [
                          BoxShadow(
                            color: CustomerTheme.brandSaffron.withValues(alpha: 0.3),
                            blurRadius: 6,
                            offset: const Offset(0, 2),
                          ),
                        ]
                      : null,
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    if (isOnlinePill) ...[
                      Container(
                        width: 7,
                        height: 7,
                        margin: const EdgeInsets.only(right: 6),
                        decoration: const BoxDecoration(
                          color: Color(0xFF10B981),
                          shape: BoxShape.circle,
                        ),
                      ),
                    ],
                    Text(
                      f,
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: isSelected ? FontWeight.w800 : FontWeight.w600,
                        color: isSelected
                            ? Colors.white
                            : (isDark ? Colors.white70 : const Color(0xFF475569)),
                      ),
                    ),
                  ],
                ),
              ),
            );
          },
        ),
      ),
    );
  }

  /// Astrologer Directory Cards List
  Widget _buildAstrologerList(
    BuildContext context,
    AsyncSnapshot<List<Astrologer>> snap,
    AppSession session,
    String currency,
    bool isDark,
  ) {
    if (snap.connectionState == ConnectionState.waiting) {
      return const SliverToBoxAdapter(
        child: Padding(
          padding: EdgeInsets.symmetric(vertical: 60),
          child: Center(
            child: CircularProgressIndicator(color: CustomerTheme.brandSaffron),
          ),
        ),
      );
    }

    if (snap.hasError) {
      return SliverToBoxAdapter(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 40),
          child: StatusViews.error(context, snap.error!, onRetry: _reload),
        ),
      );
    }

    final all = snap.data ?? const <Astrologer>[];
    var list = all;

    // Search query filter
    final q = _query.trim().toLowerCase();
    if (q.isNotEmpty) {
      list = list.where((a) {
        return a.name.toLowerCase().contains(q) ||
            a.primarySkill.toLowerCase().contains(q) ||
            a.allSkill.toLowerCase().contains(q) ||
            a.languageKnown.toLowerCase().contains(q);
      }).toList();
    }

    // Category / availability filter
    if (_selectedFilter == 'Online Now') {
      list = list.where((a) => a.isChatOnline || a.isCallOnline).toList();
    } else if (_selectedFilter != 'All') {
      final s = _selectedFilter.toLowerCase();
      list = list.where((a) {
        return a.primarySkill.toLowerCase().contains(s) || a.allSkill.toLowerCase().contains(s);
      }).toList();
    }

    if (list.isEmpty) {
      return SliverToBoxAdapter(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 60),
          child: Center(
            child: Column(
              children: [
                Icon(
                  Icons.search_off_rounded,
                  size: 48,
                  color: isDark ? Colors.white38 : const Color(0xFF94A3B8),
                ),
                const SizedBox(height: 14),
                Text(
                  'No Astrologers Found',
                  style: TextStyle(
                    fontWeight: FontWeight.w800,
                    fontSize: 16,
                    color: isDark ? Colors.white : const Color(0xFF1E293B),
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  'Try resetting filters or searching for another Vedic skill.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: isDark ? Colors.white60 : const Color(0xFF64748B),
                    fontSize: 12.5,
                  ),
                ),
                const SizedBox(height: 16),
                OutlinedButton(
                  onPressed: () {
                    setState(() {
                      _query = '';
                      _selectedFilter = 'All';
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

    final isEligible = _firstConsultOffer?['eligible'] == true;

    return SliverPadding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      sliver: SliverList.separated(
        itemCount: list.length,
        separatorBuilder: (_, __) => const SizedBox(height: 12),
        itemBuilder: (context, i) {
          final a = list[i];
          return _cleanAstrologerCard(context, a, session, currency, isEligible, isDark);
        },
      ),
    );
  }

  /// Clean, Modern Astrologer Card
  Widget _cleanAstrologerCard(
    BuildContext context,
    Astrologer a,
    AppSession session,
    String currency,
    bool isEligible,
    bool isDark,
  ) {
    final isOnline = a.isChatOnline || a.isCallOnline;
    final rate = a.charge > 0 ? '$currency${a.charge.toInt()}/min' : 'FREE';

    return Container(
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF1A1429) : Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: isDark ? Colors.white10 : const Color(0xFFE2E8F0),
          width: 1,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: isDark ? 0.25 : 0.04),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(16),
          onTap: () => context.openAstrologer(a.id ?? 0).then((_) => _reload()),
          child: Padding(
            padding: const EdgeInsets.all(14),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Top Row: Avatar + Details + Rate
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Avatar with Status Ring
                    Stack(
                      children: [
                        Container(
                          width: 66,
                          height: 66,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(
                              color: isOnline
                                  ? const Color(0xFF10B981)
                                  : (isDark ? Colors.white12 : const Color(0xFFE2E8F0)),
                              width: 2,
                            ),
                          ),
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(12),
                            child: a.imageUrl.isNotEmpty
                                ? Image.network(
                                    a.imageUrl,
                                    fit: BoxFit.cover,
                                    errorBuilder: (_, __, ___) => _avatarFallback(a.name),
                                  )
                                : _avatarFallback(a.name),
                          ),
                        ),
                        if (isOnline)
                          Positioned(
                            bottom: 2,
                            right: 2,
                            child: Container(
                              width: 13,
                              height: 13,
                              decoration: BoxDecoration(
                                color: const Color(0xFF10B981),
                                shape: BoxShape.circle,
                                border: Border.all(
                                  color: isDark ? const Color(0xFF1A1429) : Colors.white,
                                  width: 2,
                                ),
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
                                  style: TextStyle(
                                    fontWeight: FontWeight.w800,
                                    fontSize: 15,
                                    color: isDark ? Colors.white : const Color(0xFF0F172A),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 4),
                              const Icon(Icons.verified_rounded, size: 16, color: Color(0xFF0284C7)),
                            ],
                          ),
                          const SizedBox(height: 2),
                          Text(
                            a.primarySkill.isNotEmpty ? a.primarySkill : 'Vedic Astrologer',
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(
                              fontSize: 12,
                              color: isDark ? Colors.white70 : const Color(0xFF475569),
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            a.languageKnown.isNotEmpty ? a.languageKnown : 'Hindi, English',
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(
                              fontSize: 11,
                              color: isDark ? Colors.white38 : const Color(0xFF94A3B8),
                            ),
                          ),
                          const SizedBox(height: 6),

                          // Rating and Experience Strip
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1.5),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFFEF3C7),
                                  borderRadius: BorderRadius.circular(5),
                                ),
                                child: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    const Icon(Icons.star_rounded, size: 13, color: Color(0xFFD97706)),
                                    const SizedBox(width: 2),
                                    Text(
                                      a.rating > 0 ? a.rating.toStringAsFixed(1) : '4.8',
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
                                '${a.experienceInYears > 0 ? a.experienceInYears : 5}+ yrs exp',
                                style: TextStyle(
                                  fontSize: 11,
                                  color: isDark ? Colors.white60 : const Color(0xFF64748B),
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),

                    // Price Tag
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        if (isEligible && a.charge > 0) ...[
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1.5),
                            decoration: BoxDecoration(
                              color: const Color(0xFFFEE2E2),
                              borderRadius: BorderRadius.circular(5),
                            ),
                            child: const Text(
                              '₹1 OFFER',
                              style: TextStyle(
                                color: Color(0xFFB91C1C),
                                fontWeight: FontWeight.w900,
                                fontSize: 9,
                              ),
                            ),
                          ),
                          const SizedBox(height: 2),
                          const Text(
                            '₹1/min',
                            style: TextStyle(
                              fontSize: 14.5,
                              fontWeight: FontWeight.w900,
                              color: Color(0xFFDC2626),
                            ),
                          ),
                          Text(
                            rate,
                            style: TextStyle(
                              fontSize: 10.5,
                              decoration: TextDecoration.lineThrough,
                              color: isDark ? Colors.white38 : const Color(0xFF94A3B8),
                            ),
                          ),
                        ] else ...[
                          Text(
                            rate,
                            style: TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w900,
                              color: a.charge > 0 ? const Color(0xFF047857) : const Color(0xFF10B981),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ],
                ),
                const SizedBox(height: 12),

                // Subtle Divider
                Divider(
                  height: 1,
                  color: isDark ? Colors.white10 : const Color(0xFFF1F5F9),
                ),
                const SizedBox(height: 10),

                // Clean Action Buttons: Chat & Call
                Row(
                  children: [
                    // Chat Action Button
                    Expanded(
                      child: OutlinedButton.icon(
                        style: OutlinedButton.styleFrom(
                          foregroundColor: a.isChatOnline
                              ? const Color(0xFF047857)
                              : (isDark ? Colors.white38 : const Color(0xFF94A3B8)),
                          side: BorderSide(
                            color: a.isChatOnline
                                ? const Color(0xFF10B981)
                                : (isDark ? Colors.white12 : const Color(0xFFCBD5E1)),
                            width: 1.2,
                          ),
                          padding: const EdgeInsets.symmetric(vertical: 8),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                        ),
                        icon: Icon(
                          Icons.chat_bubble_outline_rounded,
                          size: 15,
                          color: a.isChatOnline ? const Color(0xFF047857) : const Color(0xFF94A3B8),
                        ),
                        label: Text(
                          a.isChatOnline ? 'Chat Now' : 'Chat Offline',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: a.isChatOnline
                                ? const Color(0xFF047857)
                                : (isDark ? Colors.white38 : const Color(0xFF94A3B8)),
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
                    const SizedBox(width: 8),

                    // Call Action Button
                    Expanded(
                      child: FilledButton.icon(
                        style: FilledButton.styleFrom(
                          backgroundColor: a.isCallOnline
                              ? const Color(0xFFD97706)
                              : (isDark ? const Color(0xFF261E38) : const Color(0xFFF1F5F9)),
                          foregroundColor: a.isCallOnline
                              ? Colors.white
                              : (isDark ? Colors.white38 : const Color(0xFF64748B)),
                          padding: const EdgeInsets.symmetric(vertical: 8),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                          elevation: a.isCallOnline ? 1 : 0,
                        ),
                        icon: Icon(
                          Icons.phone_rounded,
                          size: 15,
                          color: a.isCallOnline ? Colors.white : const Color(0xFF94A3B8),
                        ),
                        label: Text(
                          a.isCallOnline ? 'Call Now' : 'Call Offline',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: a.isCallOnline
                                ? Colors.white
                                : (isDark ? Colors.white38 : const Color(0xFF64748B)),
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
        return SafeArea(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Container(
                    width: 36,
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
                          Text(
                            '${a.primarySkill.isNotEmpty ? a.primarySkill : "Vedic Acharya"} · Offline',
                            style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                const Text(
                  'This Astrologer is currently offline or in an ongoing consultation. Would you like to receive an instant push notification when they become available?',
                  style: TextStyle(fontSize: 13, height: 1.4),
                ),
                const SizedBox(height: 18),
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () => Navigator.pop(ctx),
                        child: const Text('Cancel'),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: FilledButton(
                        style: FilledButton.styleFrom(backgroundColor: CustomerTheme.brandSaffron),
                        onPressed: () {
                          Navigator.pop(ctx);
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text('You will be notified when ${a.name} comes online.'),
                              backgroundColor: CustomerTheme.brandSaffron,
                            ),
                          );
                        },
                        child: const Text('Notify Me'),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
