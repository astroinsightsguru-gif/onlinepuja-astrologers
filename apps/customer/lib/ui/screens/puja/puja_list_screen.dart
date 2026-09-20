import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import 'puja_detail_screen.dart';

/// Puja categories + list (legacy `poojabookingScreen.dart`).
class PujaListScreen extends StatefulWidget {
  const PujaListScreen({super.key});

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
    return Card(
      margin: EdgeInsets.zero,
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () => Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => PujaDetailScreen(puja: p)),
        ),
        child: Row(
          children: [
            SizedBox(
              width: 110,
              height: 104,
              child: p.coverImage.isEmpty
                  ? Container(
                      color: scheme.primaryContainer,
                      child: Icon(Icons.local_fire_department_rounded,
                          color: scheme.primary),
                    )
                  : CachedNetworkImage(
                      imageUrl: MiscApi.imageUrl(p.coverImage),
                      fit: BoxFit.cover,
                      errorWidget: (_, _, _) => Container(
                        color: scheme.primaryContainer,
                        child: Icon(Icons.local_fire_department_rounded,
                            color: scheme.primary),
                      ),
                    ),
            ),
            Expanded(
              child: ListTile(
                title: Text(
                  p.title?.toString() ?? 'Puja',
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontWeight: FontWeight.w800),
                ),
                subtitle: Text(
                  [
                    if ((p.place ?? '').toString().isNotEmpty)
                      p.place.toString(),
                    if (_dateOnly(p.startDatetime).isNotEmpty)
                      _dateOnly(p.startDatetime),
                  ].join(' · '),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
