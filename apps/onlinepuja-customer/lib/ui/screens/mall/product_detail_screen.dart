import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import '../checkout/checkout_screen.dart';

/// Product detail page (legacy `productDetailScreen.dart`).
///
/// Checkout goes through the unified [CheckoutScreen.product] flow:
/// delivery address → `userOrder/add` (wallet-funded server-side).
class ProductDetailScreen extends StatefulWidget {
  static const route = '/product-detail';
  const ProductDetailScreen({super.key, required this.product});

  final Product product;

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  final _page = PageController();
  bool _ordering = false;

  @override
  void dispose() {
    _page.dispose();
    super.dispose();
  }

  Future<void> _order() async {
    if (!mounted) return;
    setState(() => _ordering = true);
    await Navigator.of(context).push(MaterialPageRoute(
      builder: (_) => CheckoutScreen.product(product: widget.product),
    ));
    if (mounted) setState(() => _ordering = false);
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final p = widget.product;
    final images = p.images ?? const <String>[];
    final hasDiscount = p.discountPrice != null &&
        p.discountPrice != '' &&
        double.tryParse(p.discountPrice.toString()) != null;
    return Scaffold(
      appBar: AppBar(title: Text(p.name?.toString() ?? 'Product')),
      bottomNavigationBar: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: FilledButton(
            style:
                FilledButton.styleFrom(minimumSize: const Size.fromHeight(50)),
            onPressed: _ordering ? null : _order,
            child: _ordering
                ? const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(strokeWidth: 2))
                : Text('Order now · ₹${p.displayPrice.toStringAsFixed(0)}'),
          ),
        ),
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(16),
            child: AspectRatio(
              aspectRatio: 4 / 3,
              child: images.isEmpty
                  ? Container(
                      color: scheme.primaryContainer,
                      child: Icon(Icons.storefront_rounded,
                          color: scheme.primary),
                    )
                  : PageView.builder(
                      controller: _page,
                      itemCount: images.length,
                      itemBuilder: (context, i) => CachedNetworkImage(
                        imageUrl: MiscApi.imageUrl(images[i]),
                        fit: BoxFit.cover,
                        errorWidget: (_, _, _) =>
                            Container(color: scheme.primaryContainer),
                      ),
                    ),
            ),
          ),
          const SizedBox(height: 16),
          Text(p.name?.toString() ?? 'Product',
              style: Theme.of(context)
                  .textTheme
                  .headlineSmall
                  ?.copyWith(fontWeight: FontWeight.w800)),
          const SizedBox(height: 8),
          Row(
            children: [
              Text('₹${p.displayPrice.toStringAsFixed(0)}',
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                      fontWeight: FontWeight.w800, color: scheme.primary)),
              if (hasDiscount) ...[
                const SizedBox(width: 8),
                Text(
                  '₹${double.tryParse(p.price?.toString() ?? '')?.toStringAsFixed(0) ?? '-'}',
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                      color: scheme.outline,
                      decoration: TextDecoration.lineThrough),
                ),
              ],
              const Spacer(),
              Text(
                ((p.stock is int ? p.stock as int : int.tryParse(p.stock?.toString() ?? '') ?? 0)) > 0
                    ? 'In stock'
                    : 'Out of stock',
                style: Theme.of(context).textTheme.bodySmall?.copyWith(
                    color: scheme.primary, fontWeight: FontWeight.w700),
              ),
            ],
          ),
          const SizedBox(height: 18),
          if ((p.description ?? '').toString().isNotEmpty) ...[
            Text('Description',
                style: Theme.of(context)
                    .textTheme
                    .titleMedium
                    ?.copyWith(fontWeight: FontWeight.w800)),
            const SizedBox(height: 8),
            Text(p.description.toString(),
                style: Theme.of(context).textTheme.bodyMedium),
          ],
          const SizedBox(height: 24),
        ],
      ),
    );
  }
}
