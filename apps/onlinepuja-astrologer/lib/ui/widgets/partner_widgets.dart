import 'package:flutter/material.dart';
import '../theme/partner_theme.dart';

/// Elevated card with subtle border highlight and shadow.
class PartnerCard extends StatelessWidget {
  const PartnerCard({
    super.key,
    required this.child,
    this.padding = const EdgeInsets.all(16),
    this.margin = EdgeInsets.zero,
    this.onTap,
    this.gradient,
    this.color,
    this.borderColor,
    this.borderRadius = 20,
  });

  final Widget child;
  final EdgeInsetsGeometry padding;
  final EdgeInsetsGeometry margin;
  final VoidCallback? onTap;
  final Gradient? gradient;
  final Color? color;
  final Color? borderColor;
  final double borderRadius;

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final defaultBg = dark ? PartnerTheme.darkCard : Colors.white;
    final defaultBorder = dark ? PartnerTheme.darkBorder : const Color(0xFFEFE8DE);

    final card = Container(
      margin: margin,
      decoration: BoxDecoration(
        color: gradient == null ? (color ?? defaultBg) : null,
        gradient: gradient,
        borderRadius: BorderRadius.circular(borderRadius),
        border: Border.all(
          color: borderColor ?? defaultBorder,
          width: 1.2,
        ),
        boxShadow: PartnerTheme.cardShadow(dark),
      ),
      child: Material(
        color: Colors.transparent,
        borderRadius: BorderRadius.circular(borderRadius),
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(borderRadius),
          child: Padding(
            padding: padding,
            child: child,
          ),
        ),
      ),
    );

    return card;
  }
}

/// Astrologer profile avatar with gold aura ring and online indicator.
class AstrologerAvatar extends StatelessWidget {
  const AstrologerAvatar({
    super.key,
    this.imageUrl,
    this.name = 'Astrologer',
    this.radius = 28,
    this.isOnline = true,
    this.showBadge = true,
  });

  final String? imageUrl;
  final String name;
  final double radius;
  final bool isOnline;
  final bool showBadge;

  @override
  Widget build(BuildContext context) {
    return Stack(
      clipBehavior: Clip.none,
      children: [
        Container(
          padding: const EdgeInsets.all(2.5),
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            gradient: PartnerTheme.luxuryGold,
            boxShadow: isOnline
                ? PartnerTheme.glow(PartnerTheme.saffron, blur: 8)
                : null,
          ),
          child: CircleAvatar(
            radius: radius,
            backgroundColor: const Color(0xFF2E243A),
            backgroundImage: (imageUrl != null && imageUrl!.startsWith('http'))
                ? NetworkImage(imageUrl!)
                : null,
            child: (imageUrl == null || !imageUrl!.startsWith('http'))
                ? Text(
                    name.isNotEmpty ? name.substring(0, 1).toUpperCase() : 'A',
                    style: TextStyle(
                      fontSize: radius * 0.9,
                      fontWeight: FontWeight.w800,
                      color: PartnerTheme.gold,
                    ),
                  )
                : null,
          ),
        ),
        if (showBadge)
          Positioned(
            right: 0,
            bottom: 0,
            child: Container(
              width: radius * 0.65,
              height: radius * 0.65,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: isOnline ? PartnerTheme.emerald : Colors.grey.shade600,
                border: Border.all(
                  color: Theme.of(context).scaffoldBackgroundColor,
                  width: 2.2,
                ),
                boxShadow: isOnline
                    ? PartnerTheme.glow(PartnerTheme.emerald, blur: 6)
                    : null,
              ),
            ),
          ),
      ],
    );
  }
}

/// Status Pill showing Online, Offline or Busy with glowing indicator.
class StatusPill extends StatelessWidget {
  const StatusPill({
    super.key,
    required this.isOnline,
    this.onTap,
    this.label,
    this.showIcon = true,
  });

  final bool isOnline;
  final VoidCallback? onTap;
  final String? label;
  final bool showIcon;

  @override
  Widget build(BuildContext context) {
    final statusColor = isOnline ? PartnerTheme.emerald : Colors.grey.shade500;
    final text = label ?? (isOnline ? 'ONLINE' : 'OFFLINE');

    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 250),
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(
          color: isOnline
              ? PartnerTheme.emerald.withValues(alpha: 0.14)
              : Colors.grey.withValues(alpha: 0.15),
          borderRadius: BorderRadius.circular(24),
          border: Border.all(
            color: statusColor.withValues(alpha: 0.5),
            width: 1.2,
          ),
          boxShadow: isOnline
              ? PartnerTheme.glow(PartnerTheme.emerald, blur: 8)
              : null,
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 8,
              height: 8,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: statusColor,
                boxShadow: isOnline
                    ? [
                        BoxShadow(
                          color: PartnerTheme.emerald.withValues(alpha: 0.7),
                          blurRadius: 4,
                          spreadRadius: 1,
                        )
                      ]
                    : null,
              ),
            ),
            const SizedBox(width: 7),
            Text(
              text,
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w800,
                color: statusColor,
                letterSpacing: 0.5,
              ),
            ),
            if (showIcon && onTap != null) ...[
              const SizedBox(width: 4),
              Icon(Icons.swap_horiz_rounded, size: 14, color: statusColor),
            ],
          ],
        ),
      ),
    );
  }
}

/// Stat Metric Tile for the Astrologer Dashboard.
class StatTile extends StatelessWidget {
  const StatTile({
    super.key,
    required this.icon,
    required this.label,
    required this.value,
    this.subtext,
    this.iconColor,
    this.onTap,
  });

  final IconData icon;
  final String label;
  final String value;
  final String? subtext;
  final Color? iconColor;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final tint = iconColor ?? PartnerTheme.saffron;

    return PartnerCard(
      onTap: onTap,
      padding: const EdgeInsets.all(14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: tint.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, color: tint, size: 20),
              ),
              if (subtext != null)
                Flexible(
                  child: Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
                    decoration: BoxDecoration(
                      color: PartnerTheme.emerald.withValues(alpha: 0.12),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      subtext!,
                      style: const TextStyle(
                        fontSize: 9.5,
                        fontWeight: FontWeight.w700,
                        color: PartnerTheme.emerald,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ),
            ],
          ),
          const Spacer(),
          Text(
            value,
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.w800,
              letterSpacing: -0.5,
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 3),
          Text(
            label,
            style: TextStyle(
              fontSize: 11.5,
              fontWeight: FontWeight.w500,
              color: dark ? Colors.white70 : const Color(0xFF6B7280),
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }
}

/// Segmented Pill Tab Bar with smooth selection.
class SegmentedPills extends StatelessWidget {
  const SegmentedPills({
    super.key,
    required this.items,
    required this.selectedIndex,
    required this.onChanged,
  });

  final List<String> items;
  final int selectedIndex;
  final ValueChanged<int> onChanged;

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;

    return Container(
      height: 44,
      padding: const EdgeInsets.all(4),
      decoration: BoxDecoration(
        color: dark ? PartnerTheme.darkSurface : const Color(0xFFF3EFEA),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(
          color: dark ? PartnerTheme.darkBorder : const Color(0xFFE5DDD0),
        ),
      ),
      child: Row(
        children: List.generate(items.length, (index) {
          final isSelected = index == selectedIndex;
          return Expanded(
            child: GestureDetector(
              onTap: () => onChanged(index),
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 200),
                alignment: Alignment.center,
                decoration: BoxDecoration(
                  gradient: isSelected ? PartnerTheme.saffronGradient : null,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: isSelected
                      ? PartnerTheme.glow(PartnerTheme.saffron, blur: 8)
                      : null,
                ),
                child: Text(
                  items[index],
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: isSelected ? FontWeight.w800 : FontWeight.w600,
                    color: isSelected
                        ? Colors.white
                        : (dark ? Colors.white70 : Colors.black87),
                  ),
                ),
              ),
            ),
          );
        }),
      ),
    );
  }
}

/// Devotee Birth Details & Kundli Bottom Sheet (Accessible during call, chat & requests).
class DevoteeKundliSheet extends StatelessWidget {
  const DevoteeKundliSheet({
    super.key,
    required this.devoteeName,
    this.birthDate = '15 Aug 1994',
    this.birthTime = '08:45 AM',
    this.birthPlace = 'Varanasi, Uttar Pradesh',
    this.rashi = 'Simha (Leo)',
    this.nakshatra = 'Purva Phalguni',
    this.lagna = 'Kanya (Virgo)',
    this.concern = 'Career promotion & Marriage timing consultation',
    this.gender = 'Male',
  });

  final String devoteeName;
  final String birthDate;
  final String birthTime;
  final String birthPlace;
  final String rashi;
  final String nakshatra;
  final String lagna;
  final String concern;
  final String gender;

  static void show(
    BuildContext context, {
    required String devoteeName,
    String? birthDate,
    String? birthTime,
    String? birthPlace,
    String? rashi,
    String? nakshatra,
    String? lagna,
    String? concern,
    String? gender,
  }) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => DevoteeKundliSheet(
        devoteeName: devoteeName,
        birthDate: birthDate ?? '15 Aug 1994',
        birthTime: birthTime ?? '08:45 AM',
        birthPlace: birthPlace ?? 'Varanasi, Uttar Pradesh',
        rashi: rashi ?? 'Simha (Leo)',
        nakshatra: nakshatra ?? 'Purva Phalguni',
        lagna: lagna ?? 'Kanya (Virgo)',
        concern: concern ?? 'Career & Life Path Astrology',
        gender: gender ?? 'Male',
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final bg = dark ? PartnerTheme.darkSurface : Colors.white;

    return Container(
      constraints: BoxConstraints(
        maxHeight: MediaQuery.of(context).size.height * 0.78,
      ),
      padding: const EdgeInsets.fromLTRB(20, 12, 20, 28),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(28)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.3),
            blurRadius: 24,
            offset: const Offset(0, -6),
          ),
        ],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Drag handle
          Center(
            child: Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey.withValues(alpha: 0.4),
                borderRadius: BorderRadius.circular(4),
              ),
            ),
          ),
          const SizedBox(height: 16),

          // Header
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  gradient: PartnerTheme.saffronGradient,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: const Icon(Icons.auto_awesome, color: Colors.white, size: 22),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      devoteeName,
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      'Devotee Horoscope & Birth Details',
                      style: TextStyle(
                        fontSize: 12,
                        color: dark ? Colors.white60 : Colors.black54,
                      ),
                    ),
                  ],
                ),
              ),
              IconButton(
                icon: const Icon(Icons.close_rounded),
                onPressed: () => Navigator.pop(context),
              ),
            ],
          ),
          const SizedBox(height: 18),
          const Divider(height: 1),
          const SizedBox(height: 16),

          // Concern Box
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: PartnerTheme.amber.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: PartnerTheme.amber.withValues(alpha: 0.3),
              ),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(Icons.help_outline_rounded,
                    color: PartnerTheme.amber, size: 20),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Primary Consultation Query',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          color: PartnerTheme.amber,
                        ),
                      ),
                      const SizedBox(height: 3),
                      Text(
                        concern,
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 18),

          // Planetary Details Grid
          const Text(
            'Birth & Astrological Coordinates',
            style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 12),
          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            mainAxisSpacing: 10,
            crossAxisSpacing: 10,
            childAspectRatio: 2.3,
            children: [
              _infoTile(context, Icons.calendar_today_rounded, 'Date of Birth', birthDate),
              _infoTile(context, Icons.access_time_rounded, 'Time of Birth', birthTime),
              _infoTile(context, Icons.location_on_rounded, 'Place of Birth', birthPlace),
              _infoTile(context, Icons.person_outline_rounded, 'Gender', gender),
              _infoTile(context, Icons.wb_sunny_outlined, 'Lagna (Ascendant)', lagna),
              _infoTile(context, Icons.nightlight_round, 'Moon Sign (Rashi)', rashi),
              _infoTile(context, Icons.star_border_rounded, 'Birth Nakshatra', nakshatra),
              _infoTile(context, Icons.security_rounded, 'Kundli Status', 'Manglik: Mild'),
            ],
          ),
        ],
      ),
    );
  }

  static Widget _infoTile(
      BuildContext context, IconData icon, String title, String value) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: dark ? PartnerTheme.darkCard : const Color(0xFFF9F6F0),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: dark ? PartnerTheme.darkBorder : const Color(0xFFEDE4D4),
        ),
      ),
      child: Row(
        children: [
          Icon(icon, size: 18, color: PartnerTheme.saffron),
          const SizedBox(width: 8),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  title,
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.w600,
                    color: dark ? Colors.white60 : Colors.black54,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 2),
                Text(
                  value,
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
