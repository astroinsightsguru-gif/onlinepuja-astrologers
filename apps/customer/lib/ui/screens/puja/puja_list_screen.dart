import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import 'puja_detail_screen.dart';

/// Puja categories + list (legacy `poojabookingScreen.dart`).
class PujaListScreen extends StatefulWidget {
  const PujaListScreen({super.key});

  static const route = '/pujas';

  @override
  State<PujaListScreen> createState() => _PujaListScreenState();
}

class _PujaListScreenState extends State<PujaListScreen> {
  List<PujaCategory>? _categories;
  List<Puja>? _items;
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
      final cats = await PujaApi.instance.categories();
      final items = await PujaApi.instance.list(categoryId: _categoryId);
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

  static String _dateOnly(dynamic v) {
    final s = v?.toString() ?? '';
    return s.length >= 10 ? s.substring(0, 10) : s;
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(title: const Text('Puja')),
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
                            context, message: 'No pujas in this category'),
                      ),
                    )
                  else
                    SliverPadding(
                      padding: const EdgeInsets.fromLTRB(16, 6, 16, 24),
                      sliver: SliverList.separated(
                        itemCount: _items!.length,
                        separatorBuilder: (_, _) => const SizedBox(height: 12),
                        itemBuilder: (context, i) =>
                            _pujaCard(context, _items![i], scheme),
                      ),
                    ),
                ],
              ),
            ),
    );
  }

  Widget _pujaCard(BuildContext context, Puja p, ColorScheme scheme) {
    final price = p.startingPrice;
    return Card(
      margin: EdgeInsets.zero,
      elevation: 1.5,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () => Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => PujaDetailScreen(puja: p)),
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            SizedBox(
              width: 116,
              height: 120,
              child: Stack(
                fit: StackFit.expand,
                children: [
                  p.coverImage.isEmpty
                      ? Container(
                          color: scheme.primaryContainer,
                          child: Icon(Icons.local_fire_department_rounded,
                              size: 40, color: scheme.primary),
                        )
                      : CachedNetworkImage(
                          imageUrl: MiscApi.imageUrl(p.coverImage),
                          fit: BoxFit.cover,
                          errorWidget: (_, _, _) => Container(
                            color: scheme.primaryContainer,
                            child: Icon(Icons.local_fire_department_rounded,
                                size: 40, color: scheme.primary),
                          ),
                        ),
                  if (price != null)
                    Positioned(
                      left: 6,
                      bottom: 6,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: Colors.black.withValues(alpha: 0.75),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          '₹${price.toStringAsFixed(0)}',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                ],
              ),
            ),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      p.title?.toString() ?? 'Vedic Puja',
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: Theme.of(context).textTheme.titleSmall?.copyWith(
                            fontWeight: FontWeight.w800,
                            height: 1.2,
                          ),
                    ),
                    const SizedBox(height: 6),
                    if ((p.place ?? '').toString().isNotEmpty)
                      Row(
                        children: [
                          Icon(Icons.location_on_outlined, size: 14, color: scheme.primary),
                          const SizedBox(width: 4),
                          Expanded(
                            child: Text(
                              p.place.toString(),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                                    color: scheme.outline,
                                    fontSize: 12,
                                  ),
                            ),
                          ),
                        ],
                      ),
                    if (_dateOnly(p.startDatetime).isNotEmpty) ...[
                      const SizedBox(height: 4),
                      Row(
                        children: [
                          Icon(Icons.calendar_today_outlined, size: 13, color: scheme.outline),
                          const SizedBox(width: 4),
                          Text(
                            _dateOnly(p.startDatetime),
                            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                                  color: scheme.outline,
                                  fontSize: 11.5,
                                ),
                          ),
                        ],
                      ),
                    ],
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
