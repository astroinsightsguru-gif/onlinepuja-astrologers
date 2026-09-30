import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../app.dart';
import '../../../state/app_session.dart';
import '../../theme/customer_theme.dart';
import '../../widgets/customer_widgets.dart';

class AstrologerDetailScreen extends StatefulWidget {
  const AstrologerDetailScreen({super.key, required this.id});

  static const route = '/astrologer';

  final int id;

  @override
  State<AstrologerDetailScreen> createState() => _AstrologerDetailScreenState();
}

class _AstrologerDetailScreenState extends State<AstrologerDetailScreen> {
  Astrologer? _astro;
  Object? _error;
  bool _following = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _error = null);
    try {
      final session = context.read<AppSession>();
      final a = await AstrologerApi.instance
          .byId(astrologerId: widget.id, userId: session.user?.id);
      if (!mounted) return;
      setState(() {
        _astro = a;
        _following = a.isFollow;
      });
    } catch (e) {
      if (mounted) setState(() => _error = e);
    }
  }

  Future<void> _toggleFollow() async {
    final session = context.read<AppSession>();
    final uid = session.user?.id;
    if (uid == null || _astro == null) return;
    final next = !_following;
    setState(() => _following = next);
    try {
      await AstrologerApi.instance.setFollow(
        userId: uid,
        astrologerId: _astro!.id ?? 0,
        follow: next,
      );
    } catch (_) {
      if (mounted) setState(() => _following = !next);
    }
  }

  @override
  Widget build(BuildContext context) {
    final session = context.watch<AppSession>();
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final a = _astro;

    return Scaffold(
      appBar: AppBar(
        title: Text(a?.name ?? 'Astrologer Sanctum'),
        actions: [
          if (a != null)
            IconButton(
              icon: Icon(
                _following ? Icons.favorite_rounded : Icons.favorite_border_rounded,
                color: _following ? CustomerTheme.brandCrimson : null,
              ),
              onPressed: _toggleFollow,
            ),
        ],
      ),
      body: Container(
        decoration: BoxDecoration(
          gradient: isDark
              ? CustomerTheme.cosmicDarkGradient
              : const LinearGradient(
                  colors: [
                    Color(0xFFFFFBEB),
                    Color(0xFFFAF7F2),
                  ],
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                ),
        ),
        child: _error != null
            ? StatusViews.error(context, _error!, onRetry: _load)
            : a == null
                ? StatusViews.loading(context)
                : ListView(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    children: [
                      // 1. Astrologer Profile Hero Card
                      _profileHeroCard(context, a, isDark),
                      const SizedBox(height: 14),

                      // 2. Stats Strip
                      _statsStrip(context, a, isDark),
                      const SizedBox(height: 16),

                      // 3. Specialties & Skills
                      _specialtiesCard(context, a, isDark),
                      const SizedBox(height: 16),

                      // 4. Vedic Bio & Experience
                      if (a.loginBio.isNotEmpty) ...[
                        _bioCard(context, a, isDark),
                        const SizedBox(height: 16),
                      ],

                      // 5. Consultation Options & Live Actions
                      _consultationOptions(context, a, session, isDark),
                      const SizedBox(height: 32),
                    ],
                  ),
      ),
    );
  }

  Widget _profileHeroCard(BuildContext context, Astrologer a, bool isDark) {
    return SacredCard(
      padding: const EdgeInsets.all(16),
      elevation: 2,
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          AstrologerAvatarRing(
            imageUrl: a.imageUrl,
            radius: 38,
            isOnline: a.isChatOnline || a.isCallOnline,
            badgeText: a.isChatOnline || a.isCallOnline ? 'LIVE' : null,
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Flexible(
                      child: Text(
                        a.name,
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.w900,
                          letterSpacing: -0.2,
                        ),
                      ),
                    ),
                    const SizedBox(width: 4),
                    const Icon(
                      Icons.verified_rounded,
                      size: 16,
                      color: CustomerTheme.brandGold,
                    ),
                  ],
                ),
                const SizedBox(height: 3),
                Text(
                  a.primarySkill.isNotEmpty ? a.primarySkill : 'Vedic Astrologer',
                  style: TextStyle(
                    fontSize: 12.5,
                    fontWeight: FontWeight.w600,
                    color: CustomerTheme.brandSaffron,
                  ),
                ),
                const SizedBox(height: 3),
                Text(
                  'Languages: ${a.languageKnown.isNotEmpty ? a.languageKnown : 'Hindi, English'}',
                  style: TextStyle(
                    fontSize: 11.5,
                    color: isDark ? Colors.white60 : Colors.black54,
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 2.5,
                      ),
                      decoration: BoxDecoration(
                        color: CustomerTheme.brandGold.withValues(alpha: 0.18),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Icon(
                            Icons.star_rounded,
                            size: 14,
                            color: CustomerTheme.brandDarkGold,
                          ),
                          const SizedBox(width: 3),
                          Text(
                            a.rating > 0 ? a.rating.toStringAsFixed(1) : '4.9',
                            style: const TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w800,
                              color: CustomerTheme.brandDarkGold,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 8),
                    Text(
                      '${a.reviews} Reviews',
                      style: TextStyle(
                        fontSize: 11,
                        color: isDark ? Colors.white54 : Colors.black45,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _statsStrip(BuildContext context, Astrologer a, bool isDark) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: isDark ? CustomerTheme.cosmicCardDark : Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: isDark
              ? CustomerTheme.brandGold.withValues(alpha: 0.15)
              : CustomerTheme.lightBorder,
        ),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _statItem('${a.experienceInYears}+ Yrs', 'Experience', Icons.workspace_premium_rounded),
          Container(height: 24, width: 1, color: Colors.grey.withValues(alpha: 0.2)),
          _statItem('${a.reviews}+', 'Orders', Icons.phone_in_talk_rounded),
          Container(height: 24, width: 1, color: Colors.grey.withValues(alpha: 0.2)),
          _statItem('₹${a.charge.toStringAsFixed(0)}/min', 'Dakshina', Icons.currency_rupee_rounded),
        ],
      ),
    );
  }

  Widget _statItem(String value, String label, IconData icon) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 14, color: CustomerTheme.brandSaffron),
            const SizedBox(width: 4),
            Text(
              value,
              style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 13),
            ),
          ],
        ),
        const SizedBox(height: 2),
        Text(
          label,
          style: const TextStyle(fontSize: 10, color: Colors.grey),
        ),
      ],
    );
  }

  Widget _specialtiesCard(BuildContext context, Astrologer a, bool isDark) {
    final skills = (a.allSkill.isNotEmpty ? a.allSkill : a.primarySkill)
        .split(',')
        .map((s) => s.trim())
        .where((s) => s.isNotEmpty)
        .toList();

    return SacredCard(
      padding: const EdgeInsets.all(14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Vedic Specialties & Expertise',
            style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 10),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: skills.map((s) {
              return Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                decoration: BoxDecoration(
                  color: isDark
                      ? CustomerTheme.cosmicCardElevated
                      : const Color(0xFFFFF7ED),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: CustomerTheme.brandGold.withValues(alpha: 0.3),
                  ),
                ),
                child: Text(
                  s,
                  style: TextStyle(
                    fontSize: 11.5,
                    fontWeight: FontWeight.w600,
                    color: isDark ? Colors.white70 : CustomerTheme.brandDarkGold,
                  ),
                ),
              );
            }).toList(),
          ),
        ],
      ),
    );
  }

  Widget _bioCard(BuildContext context, Astrologer a, bool isDark) {
    return SacredCard(
      padding: const EdgeInsets.all(14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'About Astrologer',
            style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 8),
          Text(
            a.loginBio,
            style: TextStyle(
              fontSize: 12.5,
              height: 1.45,
              color: isDark ? Colors.white70 : const Color(0xFF374151),
            ),
          ),
        ],
      ),
    );
  }

  Widget _consultationOptions(
    BuildContext context,
    Astrologer a,
    AppSession session,
    bool isDark,
  ) {
    final isFree = a.isFreeAvailable && !(session.user?.isFreeChat ?? false);

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Start Consultation',
          style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
        ),
        const SizedBox(height: 10),
        _actionTile(
          icon: Icons.chat_bubble_rounded,
          title: 'Start Chat Consultation',
          subtitle: a.isChatOnline
              ? 'Instant live conversation with astrologer'
              : 'Astrologer is currently offline',
          price: isFree
              ? 'FREE'
              : '₹${a.charge.toStringAsFixed(0)}/min',
          isFree: isFree,
          isOnline: a.isChatOnline,
          gradient: CustomerTheme.saffronGradient,
          onTap: a.isChatOnline
              ? () => context.openChat(
                    astrologerId: widget.id,
                    astrologerName: a.name,
                    isFree: isFree,
                  )
              : null,
        ),
        const SizedBox(height: 10),
        _actionTile(
          icon: Icons.phone_rounded,
          title: 'Audio Call Consultation',
          subtitle: a.isCallOnline
              ? 'High-clarity private spiritual voice call'
              : 'Astrologer is currently on another call',
          price: '₹${a.charge.toStringAsFixed(0)}/min',
          isFree: false,
          isOnline: a.isCallOnline,
          gradient: CustomerTheme.dakshinaGradient,
          onTap: a.isCallOnline
              ? () => context.openCall(
                    astrologerId: widget.id,
                    astrologerName: a.name,
                    ratePerMinute:
                        a.charge > 0 ? a.charge.toDouble() : 15.0,
                  )
              : null,
        ),
        const SizedBox(height: 10),
        _actionTile(
          icon: Icons.videocam_rounded,
          title: 'Live Video Consultation',
          subtitle: a.isCallOnline
              ? 'Face-to-face face reading & Kundli analysis'
              : 'Video line currently unavailable',
          price: '₹${(a.videoCallRate > 0 ? a.videoCallRate : a.charge * 1.5).toStringAsFixed(0)}/min',
          isFree: false,
          isOnline: a.isCallOnline,
          gradient: const LinearGradient(
            colors: [Color(0xFF8B5CF6), Color(0xFF6D28D9)],
          ),
          onTap: a.isCallOnline
              ? () => context.openCall(
                    astrologerId: widget.id,
                    astrologerName: a.name,
                    isVideo: true,
                    ratePerMinute: a.videoCallRate > 0
                        ? a.videoCallRate.toDouble()
                        : 25.0,
                  )
              : null,
        ),
      ],
    );
  }

  Widget _actionTile({
    required IconData icon,
    required String title,
    required String subtitle,
    required String price,
    required bool isFree,
    required bool isOnline,
    required Gradient gradient,
    required VoidCallback? onTap,
  }) {
    return SacredCard(
      padding: const EdgeInsets.all(14),
      elevation: isOnline ? 2 : 0,
      onTap: onTap,
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              gradient: isOnline ? gradient : null,
              color: isOnline ? null : Colors.grey.shade400,
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: Colors.white, size: 22),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14),
                ),
                const SizedBox(height: 2),
                Text(
                  subtitle,
                  style: TextStyle(
                    fontSize: 11,
                    color: isOnline ? Colors.grey : Colors.red.shade400,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 8),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: isFree
                      ? CustomerTheme.sacredEmerald
                      : CustomerTheme.brandGold.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Text(
                  price,
                  style: TextStyle(
                    color: isFree ? Colors.white : CustomerTheme.brandDarkGold,
                    fontWeight: FontWeight.w900,
                    fontSize: 12,
                  ),
                ),
              ),
              const SizedBox(height: 4),
              Text(
                isOnline ? 'TAP TO START' : 'UNAVAILABLE',
                style: TextStyle(
                  fontSize: 9,
                  fontWeight: FontWeight.w800,
                  letterSpacing: 0.4,
                  color: isOnline ? CustomerTheme.brandSaffron : Colors.grey,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
