import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../app.dart';
import '../../../state/app_session.dart';

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
    final a = _astro;
    return Scaffold(
      appBar: AppBar(title: Text(a?.name ?? 'Astrologer')),
      body: _error != null
          ? StatusViews.error(context, _error!, onRetry: _load)
          : a == null
              ? StatusViews.loading(context)
              : ListView(
                  padding: const EdgeInsets.all(16),
                  children: [
                    _header(context, a),
                    const SizedBox(height: 10),
                    Row(
                      children: [
                        _pill(
                            context,
                            a.isChatOnline ? 'Chat: Online' : 'Chat: Offline'),
                        const SizedBox(width: 8),
                        _pill(context,
                            a.isCallOnline ? 'Call: Online' : 'Call: Offline'),
                        const Spacer(),
                        TextButton.icon(
                          onPressed: _toggleFollow,
                          icon: Icon(_following
                              ? Icons.favorite
                              : Icons.favorite_border),
                          label: Text(_following ? 'Following' : 'Follow'),
                        ),
                      ],
                    ),
                    if (a.loginBio.isNotEmpty) ...[
                      const SizedBox(height: 12),
                      Text(a.loginBio),
                    ],
                    const SizedBox(height: 22),
                    _consultButton(
                      context,
                      icon: Icons.chat_rounded,
                      label: 'Chat now · ₹${a.charge.toStringAsFixed(0)}/min',
                      enabled: a.isChatOnline,
                      onTap: () => context.openChat(
                        astrologerId: widget.id,
                        astrologerName: a.name,
                        isFree:
                            a.isFreeAvailable && !session.user!.isFreeChat,
                      ),
                    ),
                    const SizedBox(height: 12),
                    _consultButton(
                      context,
                      icon: Icons.call_rounded,
                      label:
                          'Audio call · ₹${a.charge.toStringAsFixed(0)}/min',
                      enabled: a.isCallOnline,
                      onTap: () => context.openCall(
                        astrologerId: widget.id,
                        astrologerName: a.name,
                      ),
                    ),
                    const SizedBox(height: 12),
                    _consultButton(
                      context,
                      icon: Icons.videocam_rounded,
                      label:
                          'Video call · ₹${a.videoCallRate.toStringAsFixed(0)}/min',
                      enabled: a.isCallOnline,
                      onTap: () => context.openCall(
                        astrologerId: widget.id,
                        astrologerName: a.name,
                        isVideo: true,
                      ),
                    ),
                  ],
                ),
    );
  }

  Widget _header(BuildContext context, Astrologer a) {
    return Row(
      children: [
        ClipRRect(
          borderRadius: BorderRadius.circular(18),
          child: CachedNetworkImage(
            imageUrl: a.imageUrl,
            width: 84,
            height: 84,
            fit: BoxFit.cover,
            errorWidget: (_, _, _) => Container(
              width: 84,
              height: 84,
              color: Theme.of(context).colorScheme.primaryContainer,
              child: const Icon(Icons.person),
            ),
          ),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(a.name, style: Theme.of(context).textTheme.headlineSmall),
              Text('${a.primarySkill} · ${a.languageKnown}',
                  style: Theme.of(context).textTheme.bodySmall),
              Row(
                children: [
                  Icon(Icons.star_rounded, size: 18, color: Colors.amber[700]),
                  Text(
                    ' ${a.rating.toStringAsFixed(1)} (${a.reviews}) · ${a.experienceInYears}+ yrs',
                    style: Theme.of(context).textTheme.bodySmall,
                  ),
                ],
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _pill(BuildContext context, String text) {
    final scheme = Theme.of(context).colorScheme;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: scheme.primaryContainer,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(text, style: Theme.of(context).textTheme.labelSmall),
    );
  }

  Widget _consultButton(
    BuildContext context, {
    required IconData icon,
    required String label,
    required bool enabled,
    required VoidCallback onTap,
  }) {
    final scheme = Theme.of(context).colorScheme;
    return FilledButton.icon(
      style: FilledButton.styleFrom(
        minimumSize: const Size.fromHeight(54),
        backgroundColor: enabled ? scheme.primary : scheme.outline,
      ),
      onPressed: enabled ? onTap : null,
      icon: Icon(icon),
      label: Text(label),
    );
  }
}
