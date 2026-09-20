import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../app.dart';
import '../../../state/app_session.dart';

/// Consultation home: astrologer list with search + free-session banner.
class AstrologersScreen extends StatefulWidget {
  const AstrologersScreen({super.key});

  @override
  State<AstrologersScreen> createState() => _AstrologersScreenState();
}

class _AstrologersScreenState extends State<AstrologersScreen> {
  late Future<List<Astrologer>> _future;
  String _query = '';

  @override
  void initState() {
    super.initState();
    _future = _load();
  }

  Future<List<Astrologer>> _load() async {
    final session = context.read<AppSession>();
    final list = await AstrologerApi.instance
        .list(userId: session.user?.id, sortBy: 'rating');
    return list.where((a) => !a.isBlock).toList();
  }

  void _reload() => setState(() => _future = _load());

  @override
  Widget build(BuildContext context) {
    final session = context.watch<AppSession>();
    final showFreeBanner = session.user != null && !session.user!.isFreeChat;
    return Scaffold(
      appBar: AppBar(
        title: Text(session.flags.appName),
        actions: [
          IconButton(
            tooltip: 'Recharge wallet',
            icon: const Icon(Icons.account_balance_wallet_outlined),
            onPressed: () => Navigator.of(context)
                .pushNamed('/wallet')
                .then((_) => session.refreshUser()),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async => _reload(),
        child: CustomScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          slivers: [
            SliverToBoxAdapter(child: _header(context, session, showFreeBanner)),
            FutureBuilder<List<Astrologer>>(
              future: _future,
              builder: (context, snap) => _list(context, snap, session),
            ),
          ],
        ),
      ),
    );
  }

  Widget _header(
      BuildContext context, AppSession session, bool showFreeBanner) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (showFreeBanner)
          Container(
            margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                  colors: [AppTheme.brandSaffron, AppTheme.gold]),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Row(
              children: [
                const Icon(Icons.card_giftcard, color: Colors.white),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    '✨ First session with an astrologer is FREE!',
                    style: Theme.of(context).textTheme.titleSmall?.copyWith(
                        color: Colors.white, fontWeight: FontWeight.w700),
                  ),
                ),
              ],
            ),
          ),
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 14, 16, 4),
          child: TextField(
            onChanged: (v) => setState(() => _query = v),
            decoration: const InputDecoration(
              hintText: 'Search astrologer or skill…',
              prefixIcon: Icon(Icons.search_rounded),
            ),
          ),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 10, 16, 6),
          child:
              Text('Talk to Astrologers', style: Theme.of(context).textTheme.titleMedium),
        ),
      ],
    );
  }

  Widget _list(BuildContext context,
      AsyncSnapshot<List<Astrologer>> snap, AppSession session) {
    if (snap.connectionState == ConnectionState.waiting) {
      return SliverFillRemaining(
        child: StatusViews.skeletonList(context, items: 5),
      );
    }
    if (snap.hasError) {
      return SliverFillRemaining(
        child: StatusViews.error(context, snap.error!, onRetry: _reload),
      );
    }
    final all = snap.data ?? const <Astrologer>[];
    final q = _query.trim().toLowerCase();
    final items = q.isEmpty
        ? all
        : all
            .where((a) =>
                a.name.toLowerCase().contains(q) ||
                a.primarySkill.toLowerCase().contains(q))
            .toList();
    if (items.isEmpty) {
      return SliverFillRemaining(child: StatusViews.empty(context));
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
                    )
                : null,
          );
        },
      ),
    );
  }
}
