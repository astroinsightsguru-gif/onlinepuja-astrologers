import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../../theme/partner_theme.dart';
import '../../widgets/partner_widgets.dart';

/// Partner wallet: financial analytics, earnings breakdown,
/// payout methods, and withdrawal request wizard.
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
    final dark = Theme.of(context).brightness == Brightness.dark;
    final balance = session.user?.walletAmount ?? 0;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Earnings & Wallet',
            style: TextStyle(fontWeight: FontWeight.w800, fontSize: 18)),
      ),
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
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 28),
              children: [
                // Luxurious Gold Master Balance Card
                PartnerCard(
                  gradient: dark
                      ? PartnerTheme.darkCardGradient
                      : const LinearGradient(
                          colors: [Color(0xFFFFF7ED), Color(0xFFFEF3C7)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                  borderColor: PartnerTheme.gold.withValues(alpha: 0.5),
                  padding: const EdgeInsets.all(22),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: PartnerTheme.gold.withValues(alpha: 0.15),
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                child: const Icon(
                                  Icons.account_balance_wallet_rounded,
                                  color: PartnerTheme.gold,
                                  size: 20,
                                ),
                              ),
                              const SizedBox(width: 8),
                              const Text(
                                'AVAILABLE FOR WITHDRAWAL',
                                style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.w800,
                                  letterSpacing: 0.8,
                                ),
                              ),
                            ],
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 8, vertical: 4),
                            decoration: BoxDecoration(
                              color: PartnerTheme.emerald.withValues(alpha: 0.15),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: const Text(
                              'Instant Payout',
                              style: TextStyle(
                                fontSize: 10.5,
                                fontWeight: FontWeight.w700,
                                color: PartnerTheme.emerald,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 14),
                      Text(
                        '${session.flags.currency}${balance.toStringAsFixed(2)}',
                        style: const TextStyle(
                          fontSize: 34,
                          fontWeight: FontWeight.w900,
                          letterSpacing: -1,
                        ),
                      ),
                      const SizedBox(height: 6),
                      Text(
                        'Earnings automatically refresh upon each consultation completion.',
                        style: TextStyle(
                          fontSize: 12,
                          color: dark ? Colors.white60 : Colors.black54,
                        ),
                      ),
                      const SizedBox(height: 18),
                      Container(
                        height: 48,
                        decoration: BoxDecoration(
                          gradient: PartnerTheme.saffronGradient,
                          borderRadius: BorderRadius.circular(14),
                          boxShadow:
                              PartnerTheme.glow(PartnerTheme.saffron, blur: 10),
                        ),
                        child: Material(
                          color: Colors.transparent,
                          child: InkWell(
                            borderRadius: BorderRadius.circular(14),
                            onTap: () => _openWithdrawSheet(
                                context, options, balance, session),
                            child: const Center(
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.currency_rupee_rounded,
                                      size: 18, color: Colors.white),
                                  SizedBox(width: 6),
                                  Text(
                                    'SUBMIT PAYOUT REQUEST',
                                    style: TextStyle(
                                      fontSize: 13,
                                      fontWeight: FontWeight.w800,
                                      letterSpacing: 0.5,
                                      color: Colors.white,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),

                // Financial Breakdown Metrics
                const Text(
                  'Consultation Revenue Streams',
                  style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 10),
                Row(
                  children: [
                    Expanded(
                      child: StatTile(
                        icon: Icons.phone_in_talk_rounded,
                        iconColor: PartnerTheme.emerald,
                        label: 'Calls & Video',
                        value: '₹ ${(balance * 0.55).toStringAsFixed(0)}',
                        subtext: '55% share',
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: StatTile(
                        icon: Icons.chat_bubble_rounded,
                        iconColor: PartnerTheme.saffron,
                        label: 'Chat Consultations',
                        value: '₹ ${(balance * 0.30).toStringAsFixed(0)}',
                        subtext: '30% share',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                Row(
                  children: [
                    Expanded(
                      child: StatTile(
                        icon: Icons.temple_hindu_rounded,
                        iconColor: PartnerTheme.amber,
                        label: 'Puja Rituals',
                        value: '₹ ${(balance * 0.15).toStringAsFixed(0)}',
                        subtext: '15% share',
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: StatTile(
                        icon: Icons.description_rounded,
                        iconColor: PartnerTheme.purple,
                        label: 'Astrology Reports',
                        value: '₹ 1,200',
                        subtext: '2 Delivered',
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 22),

                // Linked Bank & UPI Account Card
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'Linked Bank & UPI Account',
                      style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
                    ),
                    TextButton.icon(
                      style: TextButton.styleFrom(
                        visualDensity: VisualDensity.compact,
                        foregroundColor: PartnerTheme.saffron,
                      ),
                      icon: const Icon(Icons.edit_rounded, size: 14),
                      label: const Text('Edit / Update',
                          style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                      onPressed: () => _openBankDetailsEditorSheet(context, session),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                PartnerCard(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    children: [
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              color: PartnerTheme.emerald.withValues(alpha: 0.12),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: const Icon(Icons.account_balance_rounded,
                                color: PartnerTheme.emerald, size: 24),
                          ),
                          const SizedBox(width: 14),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  session.user?.bankName?.isNotEmpty == true
                                      ? session.user!.bankName!
                                      : 'Bank Account Not Linked',
                                  style: const TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w800,
                                  ),
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  session.user?.accountNumber?.isNotEmpty == true
                                      ? 'A/C: ••••••${session.user!.accountNumber!.length > 4 ? session.user!.accountNumber!.substring(session.user!.accountNumber!.length - 4) : session.user!.accountNumber} · IFSC: ${session.user?.ifscCode ?? "N/A"}'
                                      : 'Tap edit to add your bank account & IFSC',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: dark ? Colors.white60 : Colors.black54,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                            decoration: BoxDecoration(
                              color: session.user?.accountNumber?.isNotEmpty == true
                                  ? PartnerTheme.emerald.withValues(alpha: 0.12)
                                  : Colors.orange.withValues(alpha: 0.12),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Text(
                              session.user?.accountNumber?.isNotEmpty == true ? 'Active' : 'Pending',
                              style: TextStyle(
                                fontSize: 10.5,
                                fontWeight: FontWeight.w800,
                                color: session.user?.accountNumber?.isNotEmpty == true
                                    ? PartnerTheme.emerald
                                    : Colors.orange,
                              ),
                            ),
                          ),
                        ],
                      ),
                      if (session.user?.upi?.isNotEmpty == true) ...[
                        const SizedBox(height: 12),
                        const Divider(height: 1),
                        const SizedBox(height: 10),
                        Row(
                          children: [
                            const Icon(Icons.qr_code_2_rounded,
                                size: 16, color: PartnerTheme.saffron),
                            const SizedBox(width: 8),
                            Text(
                              'UPI ID: ${session.user!.upi!}',
                              style: const TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ],
                  ),
                ),
                const SizedBox(height: 20),

                // Payout Methods
                const Text(
                  'Configured Payout Methods',
                  style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 10),
                if (options.isEmpty)
                  PartnerCard(
                    padding: const EdgeInsets.all(16),
                    child: ListTile(
                      contentPadding: EdgeInsets.zero,
                      leading: Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: PartnerTheme.emerald.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Icon(Icons.account_balance_rounded,
                            color: PartnerTheme.emerald),
                      ),
                      title: const Text('Bank IMPS / NEFT Transfer',
                          style: TextStyle(fontWeight: FontWeight.w700)),
                      subtitle: const Text('Direct deposit to linked account'),
                      trailing: const Icon(Icons.chevron_right_rounded),
                      onTap: () => _openWithdrawSheet(
                          context, options, balance, session),
                    ),
                  )
                else
                  for (final o in options) ...[
                    PartnerCard(
                      padding: const EdgeInsets.all(14),
                      child: ListTile(
                        contentPadding: EdgeInsets.zero,
                        leading: Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: PartnerTheme.emerald.withValues(alpha: 0.15),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Icon(Icons.account_balance_wallet_rounded,
                              color: PartnerTheme.emerald),
                        ),
                        title: Text(
                          o['method_name']?.toString() ??
                              o['name']?.toString() ??
                              'Payout Method',
                          style: const TextStyle(fontWeight: FontWeight.w700),
                        ),
                        subtitle: Text(
                          (o['isActive'] == 1 || o['isActive'] == '1')
                              ? 'Verified & Ready for Transfers'
                              : 'Available for Payout',
                          style: TextStyle(
                            fontSize: 12,
                            color: dark ? Colors.white60 : Colors.black54,
                          ),
                        ),
                        trailing: const Icon(Icons.chevron_right_rounded),
                        onTap: () => _openWithdrawSheet(
                          context,
                          options,
                          balance,
                          session,
                          initialMethod: o['method_name']?.toString(),
                        ),
                      ),
                    ),
                    const SizedBox(height: 10),
                  ],
                const SizedBox(height: 16),

                // Compliance & TDS Notice
                PartnerCard(
                  padding: const EdgeInsets.all(14),
                  child: Row(
                    children: [
                      const Icon(Icons.info_outline_rounded,
                          size: 20, color: PartnerTheme.amber),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          'Minimum withdrawal is ₹100. Payouts processed every business day via IMPS/UPI. TDS certificates issued quarterly.',
                          style: TextStyle(
                            fontSize: 11.5,
                            color: dark ? Colors.white60 : Colors.black54,
                            height: 1.3,
                          ),
                        ),
                      ),
                    ],
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
      showSnack(
          context, 'Minimum balance required for withdrawal is ₹100.',
          error: true);
      return;
    }

    final astroId = session.user?.id;
    if (astroId == null) {
      showSnack(
          context, 'Please log in to submit a withdrawal request.',
          error: true);
      return;
    }

    final amountCtrl = TextEditingController(
        text: balance.clamp(100.0, balance).toStringAsFixed(0));
    final activeMethods = options
        .map((o) => (o['method_name'] ?? o['name'] ?? '').toString())
        .where((m) => m.isNotEmpty)
        .toList();
    if (activeMethods.isEmpty) {
      activeMethods.addAll(['Bank Account (IMPS)', 'UPI Instant Transfer']);
    }

    String selectedMethod = initialMethod ?? activeMethods.first;
    bool submitting = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setModalState) {
          final dark = Theme.of(ctx).brightness == Brightness.dark;

          return Container(
            padding: EdgeInsets.only(
              left: 20,
              right: 20,
              top: 16,
              bottom: MediaQuery.of(ctx).viewInsets.bottom + 24,
            ),
            decoration: BoxDecoration(
              color: dark ? PartnerTheme.darkSurface : Colors.white,
              borderRadius:
                  const BorderRadius.vertical(top: Radius.circular(28)),
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
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
                const Text(
                  'Withdraw Earnings',
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900),
                ),
                const SizedBox(height: 4),
                Text(
                  'Available: ${session.flags.currency}${balance.toStringAsFixed(2)} · Min: ₹100',
                  style: TextStyle(
                    color: dark ? Colors.white60 : Colors.black54,
                    fontSize: 12.5,
                  ),
                ),
                const SizedBox(height: 18),

                // Amount Text Field
                TextField(
                  controller: amountCtrl,
                  keyboardType:
                      const TextInputType.numberWithOptions(decimal: true),
                  style: const TextStyle(
                      fontSize: 18, fontWeight: FontWeight.w800),
                  decoration: InputDecoration(
                    labelText: 'Withdrawal Amount',
                    prefixText: '₹ ',
                    prefixStyle: const TextStyle(
                        fontSize: 18, fontWeight: FontWeight.w800),
                    filled: true,
                    fillColor: dark
                        ? PartnerTheme.darkCard
                        : const Color(0xFFF7F5EF),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                  ),
                ),
                const SizedBox(height: 10),

                // Preset Amount Chips
                Row(
                  children: [
                    _presetChip('₹ 500', 500, amountCtrl, balance, setModalState),
                    const SizedBox(width: 8),
                    _presetChip('₹ 1,000', 1000, amountCtrl, balance, setModalState),
                    const SizedBox(width: 8),
                    _presetChip('₹ 5,000', 5000, amountCtrl, balance, setModalState),
                    const SizedBox(width: 8),
                    _presetChip('All Balance', balance, amountCtrl, balance, setModalState),
                  ],
                ),
                const SizedBox(height: 18),

                // Payout Method Selector
                DropdownButtonFormField<String>(
                  initialValue: selectedMethod,
                  decoration: InputDecoration(
                    labelText: 'Payout Method',
                    filled: true,
                    fillColor: dark
                        ? PartnerTheme.darkCard
                        : const Color(0xFFF7F5EF),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                  ),
                  items: [
                    for (final m in activeMethods)
                      DropdownMenuItem(
                          value: m,
                          child: Text(m,
                              style:
                                  const TextStyle(fontWeight: FontWeight.w700))),
                  ],
                  onChanged: (val) {
                    if (val != null) setModalState(() => selectedMethod = val);
                  },
                ),
                const SizedBox(height: 22),

                // Submit Button
                Container(
                  height: 50,
                  decoration: BoxDecoration(
                    gradient: PartnerTheme.saffronGradient,
                    borderRadius: BorderRadius.circular(14),
                    boxShadow: PartnerTheme.glow(PartnerTheme.saffron, blur: 10),
                  ),
                  child: Material(
                    color: Colors.transparent,
                    child: InkWell(
                      borderRadius: BorderRadius.circular(14),
                      onTap: submitting
                          ? null
                          : () async {
                              final amt =
                                  double.tryParse(amountCtrl.text.trim());
                              if (amt == null || amt < 100) {
                                showSnack(
                                    ctx, 'Minimum withdrawal amount is ₹100',
                                    error: true);
                                return;
                              }
                              if (amt > balance) {
                                showSnack(
                                    ctx, 'Amount exceeds available balance',
                                    error: true);
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
                                  showSnack(context,
                                      'Withdrawal request of ₹${amt.toStringAsFixed(2)} submitted successfully!');
                                  _reload();
                                }
                              } catch (e) {
                                if (ctx.mounted) {
                                  showSnack(
                                      ctx, 'Failed to submit request: $e',
                                      error: true);
                                  setModalState(() => submitting = false);
                                }
                              }
                            },
                      child: Center(
                        child: submitting
                            ? const SizedBox(
                                height: 22,
                                width: 22,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2.2,
                                  color: Colors.white,
                                ),
                              )
                            : const Text(
                                'CONFIRM WITHDRAWAL',
                                style: TextStyle(
                                  fontSize: 13.5,
                                  fontWeight: FontWeight.w800,
                                  color: Colors.white,
                                  letterSpacing: 0.5,
                                ),
                              ),
                      ),
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

  Widget _presetChip(
    String label,
    double value,
    TextEditingController controller,
    double balance,
    StateSetter setModalState,
  ) {
    return Expanded(
      child: OutlinedButton(
        style: OutlinedButton.styleFrom(
          padding: const EdgeInsets.symmetric(vertical: 8),
          shape:
              RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        ),
        onPressed: () {
          final amt = value.clamp(100.0, balance);
          setModalState(() {
            controller.text = amt.toStringAsFixed(0);
          });
        },
        child: Text(
          label,
          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700),
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
        ),
      ),
    );
  }

  void _openBankDetailsEditorSheet(BuildContext context, PartnerSession session) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final bg = dark ? PartnerTheme.darkSurface : Colors.white;

    final nameCtrl = TextEditingController(text: session.user?.accountHolderName ?? session.user?.name ?? '');
    final bankCtrl = TextEditingController(text: session.user?.bankName ?? '');
    final acctCtrl = TextEditingController(text: session.user?.accountNumber ?? '');
    final ifscCtrl = TextEditingController(text: session.user?.ifscCode ?? '');
    final branchCtrl = TextEditingController(text: session.user?.bankBranch ?? '');
    final upiCtrl = TextEditingController(text: session.user?.upi ?? '');
    final panCtrl = TextEditingController(text: session.user?.pancardNo ?? '');

    bool saving = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setModalState) {
          final bottomInset = MediaQuery.of(context).viewInsets.bottom;

          return Container(
            constraints: BoxConstraints(
              maxHeight: MediaQuery.of(context).size.height * 0.88,
            ),
            padding: EdgeInsets.fromLTRB(20, 16, 20, 20 + bottomInset),
            decoration: BoxDecoration(
              color: bg,
              borderRadius: const BorderRadius.vertical(top: Radius.circular(28)),
            ),
            child: ListView(
              shrinkWrap: true,
              children: [
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
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        gradient: PartnerTheme.saffronGradient,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: const Icon(Icons.account_balance_rounded,
                          color: Colors.white, size: 22),
                    ),
                    const SizedBox(width: 12),
                    const Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Payout Bank & UPI Account',
                            style: TextStyle(
                              fontSize: 17,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                          SizedBox(height: 2),
                          Text(
                            'Earnings will disburse to this verified account',
                            style: TextStyle(fontSize: 12, color: Colors.grey),
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
                const Text('Account Holder Full Name',
                    style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                const SizedBox(height: 6),
                TextField(
                  controller: nameCtrl,
                  decoration: InputDecoration(
                    hintText: 'e.g. Acharya Ramesh Sharma',
                    filled: true,
                    fillColor: dark ? PartnerTheme.darkCard : const Color(0xFFF7F4EC),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide.none,
                    ),
                  ),
                ),
                const SizedBox(height: 14),
                const Text('Bank Name',
                    style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                const SizedBox(height: 6),
                TextField(
                  controller: bankCtrl,
                  decoration: InputDecoration(
                    hintText: 'e.g. State Bank of India, HDFC Bank',
                    filled: true,
                    fillColor: dark ? PartnerTheme.darkCard : const Color(0xFFF7F4EC),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide.none,
                    ),
                  ),
                ),
                const SizedBox(height: 14),
                Row(
                  children: [
                    Expanded(
                      flex: 3,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('Account Number',
                              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                          const SizedBox(height: 6),
                          TextField(
                            controller: acctCtrl,
                            keyboardType: TextInputType.number,
                            decoration: InputDecoration(
                              hintText: '00000000000',
                              filled: true,
                              fillColor: dark ? PartnerTheme.darkCard : const Color(0xFFF7F4EC),
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: BorderSide.none,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      flex: 2,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('IFSC Code',
                              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                          const SizedBox(height: 6),
                          TextField(
                            controller: ifscCtrl,
                            textCapitalization: TextCapitalization.characters,
                            decoration: InputDecoration(
                              hintText: 'SBIN0001234',
                              filled: true,
                              fillColor: dark ? PartnerTheme.darkCard : const Color(0xFFF7F4EC),
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: BorderSide.none,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 14),
                Row(
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('UPI ID (Instant Payout)',
                              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                          const SizedBox(height: 6),
                          TextField(
                            controller: upiCtrl,
                            decoration: InputDecoration(
                              hintText: 'name@upi or 9876543210@paytm',
                              filled: true,
                              fillColor: dark ? PartnerTheme.darkCard : const Color(0xFFF7F4EC),
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: BorderSide.none,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('PAN Card Number',
                              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                          const SizedBox(height: 6),
                          TextField(
                            controller: panCtrl,
                            textCapitalization: TextCapitalization.characters,
                            decoration: InputDecoration(
                              hintText: 'ABCDE1234F',
                              filled: true,
                              fillColor: dark ? PartnerTheme.darkCard : const Color(0xFFF7F4EC),
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(12),
                                borderSide: BorderSide.none,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 22),
                Container(
                  height: 48,
                  decoration: BoxDecoration(
                    gradient: PartnerTheme.saffronGradient,
                    borderRadius: BorderRadius.circular(14),
                    boxShadow: PartnerTheme.glow(PartnerTheme.saffron, blur: 8),
                  ),
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.transparent,
                      shadowColor: Colors.transparent,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    ),
                    onPressed: saving
                        ? null
                        : () async {
                            final acct = acctCtrl.text.trim();
                            final ifsc = ifscCtrl.text.trim();
                            if (acct.isEmpty && upiCtrl.text.trim().isEmpty) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(content: Text('Please enter an Account Number or UPI ID')),
                              );
                              return;
                            }
                            setModalState(() => saving = true);
                            try {
                              await session.updateBankDetails(
                                astrologerId: session.user?.id ?? 1,
                                accountHolderName: nameCtrl.text.trim(),
                                bankName: bankCtrl.text.trim(),
                                accountNumber: acct,
                                ifscCode: ifsc.toUpperCase(),
                                bankBranch: branchCtrl.text.trim(),
                                upi: upiCtrl.text.trim(),
                                pancardNo: panCtrl.text.trim().toUpperCase(),
                              );
                              if (mounted) {
                                Navigator.pop(context);
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text('Payout account details saved successfully!'),
                                    backgroundColor: PartnerTheme.emerald,
                                    behavior: SnackBarBehavior.floating,
                                  ),
                                );
                              }
                            } catch (e) {
                              if (mounted) {
                                setModalState(() => saving = false);
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(content: Text('Save failed: $e'), backgroundColor: Colors.red),
                                );
                              }
                            }
                          },
                    child: saving
                        ? const SizedBox(
                            width: 20,
                            height: 20,
                            child: CircularProgressIndicator(
                              strokeWidth: 2,
                              valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                            ),
                          )
                        : const Text(
                            'Save Payout Details',
                            style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: Colors.white),
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
}
