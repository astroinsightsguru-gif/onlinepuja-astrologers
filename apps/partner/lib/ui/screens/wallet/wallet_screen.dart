import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';

/// Partner wallet: earnings + withdrawal options (legacy wallet screens).
class WalletScreen extends StatefulWidget {
  const WalletScreen({super.key});

  static const route = '/wallet';

  @override
  State<WalletScreen> createState() => _WalletScreenState();
}

class _WalletScreenState extends State<WalletScreen> {
  late Future<List<Map<String, dynamic>>> _future;

  @override
  void initState() {
    super.initState();
    _future = _load();
  }

  Future<List<Map<String, dynamic>>> _load() async {
    final session = context.read<PartnerSession>();
    await session.refreshUser();
    return WalletApi.instance.withdrawOptions();
  }

  void _reload() => setState(() => _future = _load());

  @override
  Widget build(BuildContext context) {
    final session = context.watch<PartnerSession>();
    final scheme = Theme.of(context).colorScheme;
    final balance = session.user?.walletAmount ?? 0;
    return Scaffold(
      appBar: AppBar(title: const Text('Wallet')),
      body: FutureBuilder<List<Map<String, dynamic>>>(
        future: _future,
        builder: (context, snap) {
          if (snap.connectionState == ConnectionState.waiting) {
            return StatusViews.loading(context);
          }
          if (snap.hasError) {
            return StatusViews.error(context, snap.error!, onRetry: _reload);
          }
          final options = snap.data ?? const <Map<String, dynamic>>[];
          return RefreshIndicator(
            onRefresh: () async => _reload(),
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                Card(
                  color: scheme.primaryContainer,
                  child: Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Available earnings',
                            style: Theme.of(context).textTheme.bodyMedium),
                        const SizedBox(height: 6),
                        Text(
                          '${session.flags.currency}${balance.toStringAsFixed(2)}',
                          style: Theme.of(context)
                              .textTheme
                              .headlineMedium
                              ?.copyWith(fontWeight: FontWeight.w800),
                        ),
                        const SizedBox(height: 12),
                        FilledButton.icon(
                          onPressed: () => showSnack(context,
                              'Withdrawal is handled by the admin panel'),
                          icon: const Icon(Icons.currency_rupee_rounded),
                          label: const Text('Withdraw'),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 18),
                Text('Withdrawal methods',
                    style: Theme.of(context).textTheme.titleMedium),
                const SizedBox(height: 10),
                for (final o in options)
                  Card(
                    child: ListTile(
                      leading: Icon(Icons.account_balance_wallet_outlined,
                          color: scheme.primary),
                      title: Text(o['name']?.toString() ?? 'Method'),
                      subtitle: Text(o['details']?.toString() ?? ''),
                      trailing: const Icon(Icons.chevron_right_rounded),
                    ),
                  ),
              ],
            ),
          );
        },
      ),
    );
  }
}
