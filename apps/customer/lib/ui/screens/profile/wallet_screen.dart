import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';

/// Wallet: balance + recharge plans. Selecting a plan opens the legacy
/// payment-gateway redirect (Razorpay etc. already configured in Backend).
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
    final session = context.read<AppSession>();
    await session.refreshUser();
    return WalletApi.instance.rechargePlans();
  }

  void _reload() => setState(() => _future = _load());

  Future<void> _recharge(double amount) async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Recharge ₹${amount.toStringAsFixed(0)}'),
        content: const Text(
            'You will be taken to the secure payment gateway to complete this recharge.'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(false),
            child: const Text('Cancel'),
          ),
          FilledButton(
            onPressed: () => Navigator.of(ctx).pop(true),
            child: const Text('Continue'),
          ),
        ],
      ),
    );
    if (ok != true || !mounted) return;
    // v2: after the gateway confirms the txn, record it via addPayment.
    try {
      await WalletApi.instance.addPayment(amount: amount);
      if (mounted) {
        showSnack(context, 'Recharge recorded. Balance will update shortly.');
        await context.read<AppSession>().refreshUser();
      }
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    }
  }

  @override
  Widget build(BuildContext context) {
    final session = context.watch<AppSession>();
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
          final plans = snap.data ?? const <Map<String, dynamic>>[];
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
                        Text('Available balance',
                            style: Theme.of(context).textTheme.bodyMedium),
                        const SizedBox(height: 6),
                        Text(
                          '${session.flags.currency}${balance.toStringAsFixed(2)}',
                          style: Theme.of(context)
                              .textTheme
                              .headlineMedium
                              ?.copyWith(fontWeight: FontWeight.w800),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 18),
                Text('Recharge', style: Theme.of(context).textTheme.titleMedium),
                const SizedBox(height: 10),
                Wrap(
                  spacing: 10,
                  runSpacing: 10,
                  children: [
                    for (final plan in plans)
                      _planChip(context, plan, onTap: _recharge),
                  ],
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _planChip(
    BuildContext context,
    Map<String, dynamic> plan, {
    required ValueChanged<double> onTap,
  }) {
    final amount = double.tryParse(plan['amount']?.toString() ?? '') ?? 0;
    if (amount <= 0) return const SizedBox.shrink();
    return OutlinedButton(
      onPressed: () => onTap(amount),
      child: Text('₹${amount.toStringAsFixed(0)}'),
    );
  }
}
