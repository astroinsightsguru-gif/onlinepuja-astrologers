import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';

import '../models/astrologer.dart';

/// Consultation astrologer list item — modern card with live status,
/// rating, price/min and free-session badge.
class AstrologerCard extends StatelessWidget {
  const AstrologerCard({
    super.key,
    required this.astrologer,
    this.onTap,
    this.onChat,
    this.onCall,
  });

  final Astrologer astrologer;
  final VoidCallback? onTap;
  final VoidCallback? onChat;
  final VoidCallback? onCall;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final online = astrologer.isChatOnline || astrologer.isCallOnline;
    return Card(
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              _avatar(context, scheme, online),
              const SizedBox(width: 14),
              Expanded(child: _info(context, scheme)),
              const SizedBox(width: 10),
              _priceAndActions(context, scheme),
            ],
          ),
        ),
      ),
    );
  }

  Widget _info(BuildContext context, ColorScheme scheme) => Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Flexible(
                child: Text(
                  astrologer.name,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: Theme.of(context)
                      .textTheme
                      .titleMedium
                      ?.copyWith(fontWeight: FontWeight.w700),
                ),
              ),
              if (astrologer.isBoosted) ...[
                const SizedBox(width: 6),
                Icon(Icons.trending_up, size: 16, color: scheme.tertiary),
              ],
            ],
          ),
          const SizedBox(height: 3),
          Text(
            '${astrologer.primarySkill} · ${astrologer.languageKnown}',
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: Theme.of(context)
                .textTheme
                .bodySmall
                ?.copyWith(color: scheme.outline),
          ),
          const SizedBox(height: 7),
          Row(
            children: [
              Icon(Icons.star_rounded, size: 17, color: Colors.amber[700]),
              const SizedBox(width: 3),
              Text(
                '${astrologer.rating.toStringAsFixed(1)} (${astrologer.reviews})',
                style: Theme.of(context).textTheme.bodySmall,
              ),
              const SizedBox(width: 10),
              Text(
                '${astrologer.experienceInYears}+ yrs',
                style: Theme.of(context)
                    .textTheme
                    .bodySmall
                    ?.copyWith(color: scheme.outline),
              ),
              const Spacer(),
              if (astrologer.isFreeAvailable) const _FreeBadge(),
            ],
          ),
        ],
      );

  Widget _avatar(BuildContext context, ColorScheme scheme, bool online) =>
      Stack(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(14),
            child: CachedNetworkImage(
              imageUrl: astrologer.imageUrl,
              width: 62,
              height: 62,
              fit: BoxFit.cover,
              placeholder: (_, _) => Container(
                width: 62,
                height: 62,
                color: scheme.primaryContainer,
                child: Icon(Icons.auto_awesome, color: scheme.primary),
              ),
              errorWidget: (_, _, _) => Container(
                width: 62,
                height: 62,
                color: scheme.primaryContainer,
                child: Icon(Icons.person, color: scheme.primary),
              ),
            ),
          ),
          if (online)
            Positioned(
              right: 0,
              bottom: 0,
              child: Container(
                width: 14,
                height: 14,
                decoration: BoxDecoration(
                  color: Colors.green,
                  shape: BoxShape.circle,
                  border: Border.all(color: scheme.surface, width: 2),
                ),
              ),
            ),
        ],
      );

  Widget _priceAndActions(BuildContext context, ColorScheme scheme) => Column(
        children: [
          Text(
            '₹${astrologer.charge.toStringAsFixed(0)}',
            style: Theme.of(context)
                .textTheme
                .titleSmall
                ?.copyWith(fontWeight: FontWeight.w800),
          ),
          Text(
            '/min',
            style: Theme.of(context)
                .textTheme
                .labelSmall
                ?.copyWith(color: scheme.outline),
          ),
          const SizedBox(height: 8),
          Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              IconButton(
                visualDensity: VisualDensity.compact,
                onPressed: onChat,
                icon: Icon(Icons.chat_rounded,
                    size: 20,
                    color: astrologer.isChatOnline
                        ? scheme.primary
                        : scheme.outline),
              ),
              IconButton(
                visualDensity: VisualDensity.compact,
                onPressed: onCall,
                icon: Icon(Icons.call_rounded,
                    size: 20,
                    color: astrologer.isCallOnline
                        ? scheme.tertiary
                        : scheme.outline),
              ),
            ],
          ),
        ],
      );
}

/// Green "FREE" chip for astrologers offering the free first session.
class _FreeBadge extends StatelessWidget {
  const _FreeBadge();

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: Colors.green.withValues(alpha: 0.14),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        'FREE',
        style: Theme.of(context).textTheme.labelSmall?.copyWith(
              color: Colors.green[800] ?? scheme.secondary,
              fontWeight: FontWeight.w800,
            ),
      ),
    );
  }
}
