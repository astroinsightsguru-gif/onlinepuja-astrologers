import 'package:url_launcher/url_launcher.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';

/// Unified checkout for AstroMall products and Puja packages
/// (legacy `checkoutScreen.dart` + `deliveryAddressScreen.dart`).
///
/// Flow: pick/add delivery address → place order (`userOrder/add` for
/// products, `placedPujaOrder` for pujas — both wallet-funded server-side).
class CheckoutScreen extends StatefulWidget {
  const CheckoutScreen.product({super.key, required this.product})
      : puja = null,
        package = null;

  const CheckoutScreen.puja({
    super.key,
    required this.puja,
    required this.package,
  }) : product = null;

  final Product? product;
  final Puja? puja;
  final PujaPackage? package;

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  List<OrderAddress>? _addresses;
  OrderAddress? _selected;
  bool _loading = true;
  bool _placing = false;
  Object? _error;

  @override
  void initState() {
    super.initState();
    _loadAddresses();
  }

  double get _price {
    final p = widget.product;
    if (p != null) return p.displayPrice;
    return widget.package?.priceValue ?? 0;
  }

  String get _title {
    final p = widget.product;
    if (p != null) return p.name?.toString() ?? 'Product';
    return widget.puja?.title?.toString() ?? 'Puja';
  }

  Future<void> _loadAddresses() async {
    final userId = context.read<AppSession>().user?.id ?? 0;
    try {
      final list = await MiscApi.instance.addresses(userId: userId);
      if (!mounted) return;
      setState(() {
        _addresses = list;
        _selected ??= list.isEmpty ? null : list.first;
        _loading = false;
      });
    } catch (e) {
      if (mounted) {
        setState(() {
          _error = e;
          _loading = false;
        });
      }
    }
  }

  Future<void> _addAddress() async {
    final created = await showAddAddressSheet(context);
    if (created == null) return;
    setState(() {
      _addresses = [...?_addresses, created];
      _selected = created;
    });
  }

  Future<void> _placeOrder() async {
    final session = context.read<AppSession>();
    final userId = session.user?.id ?? 0;
    if (userId == 0) {
      showSnack(context, 'Log in to place an order', error: true);
      return;
    }
    final address = _selected;
    if (address == null) {
      showSnack(context, 'Add a delivery address first.', error: true);
      return;
    }
    setState(() => _placing = true);
    try {
      final product = widget.product;
      if (product != null) {
        await MallApi.instance.placeOrder(
          userId: userId,
          productId: product.id,
          categoryId: product.categoryId,
          addressId: address.id,
          payableAmount: _price,
          totalPayable: _price,
        );
      } else {
        final res = await PujaApi.instance.placeOrder(
          userId: userId,
          pujaId: widget.puja!.id,
          packageId: widget.package!.id,
          extra: {
            'orderAddressId': address.id,
            'addressId': address.id,
            'amount': _price,
          },
        );
        final redirectUrl = res['redirect']?.toString();
        if (redirectUrl != null && redirectUrl.isNotEmpty) {
          if (!mounted) return;
          final payOnline = await showDialog<bool>(
            context: context,
            builder: (ctx) => AlertDialog(
              title: const Text('Recharge or Pay Online'),
              content: const Text(
                  'Your wallet balance is insufficient for this Puja. Would you like to proceed with secure online payment?'),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(ctx, false),
                  child: const Text('Cancel'),
                ),
                FilledButton(
                  onPressed: () => Navigator.pop(ctx, true),
                  child: const Text('Pay Online'),
                ),
              ],
            ),
          );
          if (payOnline == true) {
            await launchUrl(Uri.parse(redirectUrl),
                mode: LaunchMode.externalApplication);
          }
          return;
        }
      }
      if (!mounted) return;
      await showDialog<void>(
        context: context,
        barrierDismissible: false,
        builder: (ctx) => AlertDialog(
          icon: const Icon(Icons.check_circle_rounded,
              color: Colors.green, size: 48),
          title: const Text('Order placed 🙏'),
          content: Text(
              '$_title ordered successfully. We will notify you at every step.'),
          actions: [
            FilledButton(
              onPressed: () => Navigator.pop(ctx),
              child: const Text('Done'),
            ),
          ],
        ),
      );
      if (mounted) Navigator.of(context).popUntil((r) => r.isFirst);
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    } catch (e) {
      if (mounted) showSnack(context, e.toString(), error: true);
    } finally {
      if (mounted) setState(() => _placing = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final currency = context.read<AppSession>().flags.currency;
    return Scaffold(
      appBar: AppBar(title: const Text('Checkout')),
      bottomNavigationBar: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: FilledButton(
            style:
                FilledButton.styleFrom(minimumSize: const Size.fromHeight(52)),
            onPressed: (_placing || _loading) ? null : _placeOrder,
            child: _placing
                ? const SizedBox(
                    width: 22,
                    height: 22,
                    child: CircularProgressIndicator(strokeWidth: 2))
                : Text('Place order · $currency${_price.toStringAsFixed(0)}'),
          ),
        ),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? StatusViews.error(context, _error!, onRetry: _loadAddresses)
              : ListView(
                  padding: const EdgeInsets.all(16),
                  children: [
                    Text('Deliver to',
                        style: Theme.of(context)
                            .textTheme
                            .titleMedium
                            ?.copyWith(fontWeight: FontWeight.w800)),
                    const SizedBox(height: 10),
                    if (_addresses == null || _addresses!.isEmpty)
                      Card(
                        child: ListTile(
                          leading: Icon(Icons.add_location_alt_outlined,
                              color: scheme.primary),
                          title: const Text('Add delivery address'),
                          onTap: _addAddress,
                        ),
                      )
                    else ...[
                      for (final a in _addresses!)
                        // ignore: deprecated_member_use
                        RadioListTile<OrderAddress>(
                          value: a,
                          // ignore: deprecated_member_use
                          groupValue: _selected,
                          // ignore: deprecated_member_use
                          onChanged: (v) => setState(() => _selected = v),
                          title: Text(a.name?.toString() ?? 'Address'),
                          subtitle: Text(a.fullAddress),
                          secondary: Text(a.phone?.toString() ?? ''),
                        ),
                    ],
                    const SizedBox(height: 8),
                    OutlinedButton.icon(
                      onPressed: _addAddress,
                      icon: const Icon(Icons.add_rounded),
                      label: const Text('New address'),
                    ),
                    const Divider(height: 32),
                    _row('Item', _title),
                    _row('Price', '$currency${_price.toStringAsFixed(0)}'),
                    _row(
                        'Payment',
                        'Wallet balance '
                            '$currency${context.read<AppSession>().user?.walletAmount.toStringAsFixed(0) ?? '0'}'),
                    const SizedBox(height: 8),
                    Text(
                      'The order amount is deducted from your wallet balance '
                      'by the server. Recharge in the wallet if it is low.',
                      style: Theme.of(context)
                          .textTheme
                          .bodySmall
                          ?.copyWith(color: scheme.outline),
                    ),
                  ],
                ),
    );
  }

  Widget _row(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(children: [
        Expanded(
            child: Text(label,
                style: Theme.of(context)
                    .textTheme
                    .bodyMedium
                    ?.copyWith(
                        color: Theme.of(context).colorScheme.outline))),
        Text(value, style: const TextStyle(fontWeight: FontWeight.w700)),
      ]),
    );
  }
}

/// Bottom-sheet form to create a delivery address
/// (legacy `orderAddress/add`). Returns the created [OrderAddress].
Future<OrderAddress?> showAddAddressSheet(BuildContext context) async {
  final name = TextEditingController();
  final phone = TextEditingController();
  final line1 = TextEditingController();
  final city = TextEditingController();
  final state = TextEditingController();
  final pincode = TextEditingController();
  final saving = ValueNotifier<bool>(false);

  Future<void> save() async {
    if (name.text.trim().isEmpty || line1.text.trim().isEmpty) return;
    saving.value = true;
    try {
      final session = context.read<AppSession>();
      final created = await MiscApi.instance.addAddress(
        userId: session.user?.id ?? 0,
        address: {
          'name': name.text.trim(),
          'phone': phone.text.trim(),
          'addressLine1': line1.text.trim(),
          'city': city.text.trim(),
          'state': state.text.trim(),
          'pincode': pincode.text.trim(),
          'country': 'India',
        },
      );
      if (context.mounted) Navigator.pop(context, created);
    } on ApiException catch (e) {
      if (context.mounted) showSnack(context, e.message, error: true);
    } finally {
      saving.value = false;
    }
  }

  final created = await showModalBottomSheet<OrderAddress>(
    context: context,
    isScrollControlled: true,
    builder: (ctx) => Padding(
      padding: EdgeInsets.fromLTRB(
          16, 16, 16, 16 + MediaQuery.of(ctx).viewInsets.bottom),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text('New delivery address',
              style: Theme.of(ctx)
                  .textTheme
                  .titleMedium
                  ?.copyWith(fontWeight: FontWeight.w800)),
          const SizedBox(height: 12),
          TextField(
            controller: name,
            decoration: const InputDecoration(labelText: 'Full name'),
          ),
          TextField(
            controller: phone,
            decoration: const InputDecoration(labelText: 'Phone'),
            keyboardType: TextInputType.phone,
          ),
          TextField(
            controller: line1,
            decoration: const InputDecoration(
                labelText: 'Address (house, street, area)'),
          ),
          Row(children: [
            Expanded(
                child: TextField(controller: city,
                    decoration:
                        const InputDecoration(labelText: 'City'))),
            const SizedBox(width: 10),
            Expanded(
                child: TextField(controller: state,
                    decoration:
                        const InputDecoration(labelText: 'State'))),
          ]),
          TextField(
            controller: pincode,
            decoration: const InputDecoration(labelText: 'PIN code'),
            keyboardType: TextInputType.number,
          ),
          const SizedBox(height: 14),
          ValueListenableBuilder<bool>(
            valueListenable: saving,
            builder: (_, busy, _) => FilledButton(
              onPressed: busy ? null : save,
              child: busy
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(strokeWidth: 2))
                  : const Text('Save address'),
            ),
          ),
        ],
      ),
    ),
  );
  for (final c in [name, phone, line1, city, state, pincode]) {
    c.dispose();
  }
  return created;
}
