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
                          onPressed: () => _openWithdrawSheet(context, options, balance, session),
                          icon: const Icon(Icons.currency_rupee_rounded),
                          label: const Text('Withdraw Request'),
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
                      title: Text(o['method_name']?.toString() ?? o['name']?.toString() ?? 'Payout Method'),
                      subtitle: Text((o['isActive'] == 1 || o['isActive'] == '1') ? 'Verified & Active' : 'Available on request'),
                      trailing: const Icon(Icons.chevron_right_rounded),
                      onTap: () => _openWithdrawSheet(context, options, balance, session, initialMethod: o['method_name']?.toString()),
                    ),
                  ),
              ],
            ),
          );
        },
      ),
    );
  }

  void _openWithdrawSheet(
    BuildContext context,
    List<Map<String, dynamic>> options,
    double balance,
    PartnerSession session, {
    String? initialMethod,
  }) {
    if (balance < 100) {
      showSnack(context, 'Minimum balance required for withdrawal is ₹100.', error: true);
      return;
    }

    final astroId = session.user?.id;
    if (astroId == null) {
      showSnack(context, 'Please log in to submit a withdrawal request.', error: true);
      return;
    }

    final amountCtrl = TextEditingController(text: balance.clamp(100.0, balance).toStringAsFixed(0));
    final activeMethods = options
        .map((o) => (o['method_name'] ?? o['name'] ?? '').toString())
        .where((m) => m.isNotEmpty)
        .toList();
    if (activeMethods.isEmpty) {
      activeMethods.addAll(['Bank Account', 'UPI']);
    }

    String selectedMethod = initialMethod ?? activeMethods.first;
    bool submitting = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setModalState) => Padding(
          padding: EdgeInsets.only(
            left: 20,
            right: 20,
            top: 20,
            bottom: MediaQuery.of(ctx).viewInsets.bottom + 20,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text(
                'Request Payout',
                style: Theme.of(ctx).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 6),
              Text(
                'Available: ${session.flags.currency}${balance.toStringAsFixed(2)} · Min: ₹100',
                style: TextStyle(color: Theme.of(ctx).colorScheme.outline, fontSize: 13),
              ),
              const SizedBox(height: 16),
              TextField(
                controller: amountCtrl,
                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                decoration: const InputDecoration(
                  labelText: 'Withdrawal Amount',
                  prefixText: '₹ ',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 14),
              DropdownButtonFormField<String>(
                initialValue: selectedMethod,
                decoration: const InputDecoration(
                  labelText: 'Payout Method',
                  border: OutlineInputBorder(),
                ),
                items: [
                  for (final m in activeMethods)
                    DropdownMenuItem(value: m, child: Text(m)),
                ],
                onChanged: (val) {
                  if (val != null) setModalState(() => selectedMethod = val);
                },
              ),
              const SizedBox(height: 20),
              FilledButton(
                onPressed: submitting
                    ? null
                    : () async {
                        final amt = double.tryParse(amountCtrl.text.trim());
                        if (amt == null || amt < 100) {
                          showSnack(ctx, 'Minimum withdrawal amount is ₹100', error: true);
                          return;
                        }
                        if (amt > balance) {
                          showSnack(ctx, 'Amount exceeds available balance', error: true);
                          return;
                        }
                        setModalState(() => submitting = true);
                        try {
                          await WalletApi.instance.requestWithdraw(
                            astrologerId: astroId,
                            withdrawAmount: amt,
                            paymentMethod: selectedMethod,
                          );
                          if (ctx.mounted) Navigator.pop(ctx);
                          if (context.mounted) {
                            showSnack(context, 'Withdrawal request of ₹${amt.toStringAsFixed(2)} submitted successfully!');
                            _reload();
                          }
                        } catch (e) {
                          if (ctx.mounted) {
                            showSnack(ctx, 'Failed to submit request: $e', error: true);
                            setModalState(() => submitting = false);
                          }
                        }
                      },
                child: submitting
                    ? const SizedBox(
                        height: 20,
                        width: 20,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : const Text('Submit Payout Request'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
