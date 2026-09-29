import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

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
  static const _defaultCuratedPlans = [
    {'amount': 50, 'bonus': '100% Extra'},
    {'amount': 100, 'bonus': 'Popular'},
    {'amount': 200, 'bonus': '+₹20 Extra'},
    {'amount': 500, 'bonus': '+₹75 Extra'},
    {'amount': 1000, 'bonus': '+₹200 Extra'},
    {'amount': 2000, 'bonus': '+₹500 Extra'},
  ];
  final _customAmountController = TextEditingController();

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
    try {
      final payUrl = await WalletApi.instance.addPayment(amount: amount);
      if (payUrl != null && payUrl.isNotEmpty) {
        final uri = Uri.parse(payUrl);
        final launched = await launchUrl(uri, mode: LaunchMode.inAppBrowserView);
        if (!launched) {
          await launchUrl(uri, mode: LaunchMode.externalApplication);
        }
      }
      if (mounted) {
        showSnack(context, 'Complete payment in checkout window to update balance.');
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
          if (snap.connectionState == ConnectionState.waiting && !snap.hasData) {
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
                Text('Select Recharge Plan', style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.bold)),
                const SizedBox(height: 10),
                Wrap(
                  spacing: 10,
                  runSpacing: 10,
                  children: [
                    for (final plan in (plans.isNotEmpty ? plans : _defaultCuratedPlans))
                      _planChip(context, plan, onTap: _recharge),
                  ],
                ),
                const SizedBox(height: 24),
                Card(
                  elevation: 0,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                    side: BorderSide(color: scheme.outline.withValues(alpha: 0.25)),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Custom Amount', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Expanded(
                              child: TextField(
                                controller: _customAmountController,
                                keyboardType: TextInputType.number,
                                decoration: InputDecoration(
                                  prefixText: '₹ ',
                                  hintText: 'Enter amount (e.g. 150)',
                                  contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                                ),
                              ),
                            ),
                            const SizedBox(width: 10),
                            FilledButton(
                              style: FilledButton.styleFrom(
                                backgroundColor: AppTheme.brandSaffron,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                              ),
                              onPressed: () {
                                final val = double.tryParse(_customAmountController.text.trim()) ?? 0;
                                if (val > 0) {
                                  _recharge(val);
                                } else {
                                  showSnack(context, 'Please enter a valid amount', error: true);
                                }
                              },
                              child: const Text('Add Money'),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
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
    final bonus = plan['bonus']?.toString();
    return OutlinedButton(
      style: OutlinedButton.styleFrom(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),
      onPressed: () => onTap(amount),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text('₹${amount.toStringAsFixed(0)}', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
          if (bonus != null)
            Text(bonus, style: const TextStyle(fontSize: 10, color: Colors.green, fontWeight: FontWeight.w700)),
        ],
      ),
    );
  }
}
