import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import 'product_detail_screen.dart';

/// AstroMall: category chips + product grid (legacy `astromallScreen.dart`).
class MallScreen extends StatefulWidget {
  const MallScreen({super.key});

  static const route = '/mall';

  @override
  State<MallScreen> createState() => _MallScreenState();
}

class _MallScreenState extends State<MallScreen> {
  List<ProductCategory>? _categories;
  List<Product>? _items;
  Object? _error;
  dynamic _categoryId;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _error = null);
    try {
      final cats = await MallApi.instance.categories();
      final items = await MallApi.instance.list(categoryId: _categoryId);
      if (mounted) {
        setState(() {
          _categories = cats;
          _items = items;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _error = e);
    }
  }

  void _pickCategory(dynamic id) {
    setState(() => _categoryId = (_categoryId == id) ? null : id);
    _load();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('AstroMall')),
      body: _error != null
          ? StatusViews.error(context, _error!, onRetry: _load)
          : RefreshIndicator(
              onRefresh: _load,
              child: CustomScrollView(
                slivers: [
                  if (_categories != null && _categories!.isNotEmpty)
                    SliverToBoxAdapter(
                      child: SizedBox(
                        height: 44,
                        child: ListView.separated(
                          scrollDirection: Axis.horizontal,
                          padding: const EdgeInsets.symmetric(
                              horizontal: 16, vertical: 6),
                          itemCount: _categories!.length,
                          separatorBuilder: (_, _) => const SizedBox(width: 8),
                          itemBuilder: (context, i) {
                            final c = _categories![i];
                            return ChoiceChip(
                              label: Text(c.name?.toString() ?? ''),
                              selected: _categoryId == c.id,
                              onSelected: (_) => _pickCategory(c.id),
                            );
                          },
                        ),
                      ),
                    ),
                  if (_items == null)
                    const SliverPadding(
                      padding: EdgeInsets.all(60),
                      sliver: SliverToBoxAdapter(
                        child: Center(
                            child:
                                CircularProgressIndicator(strokeWidth: 2.4)),
                      ),
                    )
                  else if (_items!.isEmpty)
                    SliverToBoxAdapter(
                      child: SizedBox(
                        height: 300,
                        child: StatusViews.empty(
                            context, message: 'No products in this category'),
                      ),
                    )
                  else
                    SliverPadding(
                      padding: const EdgeInsets.fromLTRB(16, 6, 16, 24),
                      sliver: SliverGrid.builder(
                        gridDelegate:
                            const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 2,
                          mainAxisSpacing: 12,
                          crossAxisSpacing: 12,
                          childAspectRatio: 0.72,
                        ),
                        itemCount: _items!.length,
                        itemBuilder: (context, i) =>
                            _productCard(context, _items![i]),
                      ),
                    ),
                ],
              ),
            ),
    );
  }

  Widget _productCard(BuildContext context, Product product) {
    final scheme = Theme.of(context).colorScheme;
    final image = (product.images == null || product.images!.isEmpty)
        ? ''
        : product.images!.first;
    final hasDiscount = product.discountPrice != null &&
        product.discountPrice != '' &&
        double.tryParse(product.discountPrice.toString()) != null;
    return Card(
      margin: EdgeInsets.zero,
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () => Navigator.of(context).push(
          MaterialPageRoute(
            builder: (_) => ProductDetailScreen(product: product),
          ),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Expanded(
              child: image.isEmpty
                  ? Container(
                      color: scheme.primaryContainer,
                      child: Icon(Icons.storefront_rounded,
                          size: 40, color: scheme.primary),
                    )
                  : CachedNetworkImage(
                      imageUrl: MiscApi.imageUrl(image),
                      fit: BoxFit.cover,
                      errorWidget: (_, _, _) => Container(
                        color: scheme.primaryContainer,
                        child: Icon(Icons.storefront_rounded,
                            size: 40, color: scheme.primary),
                      ),
                    ),
            ),
            Padding(
              padding: const EdgeInsets.all(10),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    product.name?.toString() ?? 'Product',
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: Theme.of(context)
                        .textTheme
                        .bodyMedium
                        ?.copyWith(fontWeight: FontWeight.w700),
                  ),
                  const SizedBox(height: 6),
                  Row(
                    children: [
                      Text(
                        '₹${product.displayPrice.toStringAsFixed(0)}',
                        style: Theme.of(context)
                            .textTheme
                            .titleMedium
                            ?.copyWith(
                                fontWeight: FontWeight.w800,
                                color: scheme.primary),
                      ),
                      if (hasDiscount) ...[
                        const SizedBox(width: 6),
                        Text(
                          '₹${double.tryParse(product.price?.toString() ?? '')?.toStringAsFixed(0) ?? '-'}',
                          style: Theme.of(context)
                              .textTheme
                              .bodySmall
                              ?.copyWith(
                                  color: scheme.outline,
                                  decoration: TextDecoration.lineThrough),
                        ),
                      ],
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
