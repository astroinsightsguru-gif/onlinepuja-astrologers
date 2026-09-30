import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import '../checkout/checkout_screen.dart';

/// Puja detail: description, benefits, packages & FAQ
/// (legacy `poojadetailScreen.dart` + `packageDetailscreen.dart`).
class PujaDetailScreen extends StatefulWidget {
  const PujaDetailScreen({super.key, required this.puja});

  final Puja puja;

  @override
  State<PujaDetailScreen> createState() => _PujaDetailScreenState();
}

class _PujaDetailScreenState extends State<PujaDetailScreen> {
  List<Map<String, dynamic>>? _faqs;
  bool _booking = false;

  @override
  void initState() {
    super.initState();
    _loadFaqs();
  }

  Future<void> _loadFaqs() async {
    try {
      final faqs = await PujaApi.instance.faqs(pujaId: widget.puja.id);
      if (mounted) setState(() => _faqs = faqs);
    } catch (_) {
      // FAQ is optional; silent failure keeps the detail page usable.
      if (mounted) setState(() => _faqs = const []);
    }
  }

  static String _dateOnly(dynamic v) {
    final s = v?.toString() ?? '';
    return s.length >= 10 ? s.substring(0, 10) : s;
  }

  Future<void> _book() async {
    final packages = widget.puja.packages ?? const <PujaPackage>[];
    if (packages.isEmpty) return;
    setState(() => _booking = true);
    try {
      PujaPackage? selected = packages.length == 1 ? packages.first : null;
      selected ??= await showDialog<PujaPackage>(
          context: context,
          builder: (ctx) => SimpleDialog(
            title: const Text('Choose a package'),
            children: [
              for (final p in packages)
                SimpleDialogOption(
                  onPressed: () => Navigator.pop(ctx, p),
                  child: ListTile(
                    contentPadding: EdgeInsets.zero,
                    title: Text(p.name?.toString() ?? 'Package'),
                    subtitle: p.inclusions == null || p.inclusions!.isEmpty
                        ? null
                        : Text(p.inclusions!.join(', ')),
                    trailing: Text(
                      '₹${p.priceValue.toStringAsFixed(0)}',
                      style: const TextStyle(fontWeight: FontWeight.w800),
                    ),
                  ),
                ),
            ],
          ),
        );
      if (selected == null || !mounted) return;
      await Navigator.of(context).push(MaterialPageRoute(
        builder: (_) => CheckoutScreen.puja(
            puja: widget.puja, package: selected!),
      ));
    } finally {
      if (mounted) setState(() => _booking = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final p = widget.puja;
    final packages = p.packages ?? const <PujaPackage>[];
    return Scaffold(
      appBar: AppBar(title: Text(p.title?.toString() ?? 'Puja')),
      bottomNavigationBar: packages.isEmpty
          ? null
          : SafeArea(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: FilledButton(
                  style: FilledButton.styleFrom(
                      minimumSize: const Size.fromHeight(50)),
                  onPressed: _booking ? null : _book,
                  child: _booking
                      ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(strokeWidth: 2))
                      : Text(p.isPurchased == 1
                          ? 'Already booked'
                          : 'Book this puja'),
                ),
              ),
            ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          if (p.coverImage.isNotEmpty)
            ClipRRect(
              borderRadius: BorderRadius.circular(16),
              child: AspectRatio(
                aspectRatio: 16 / 8,
                child: CachedNetworkImage(
                  imageUrl: MiscApi.imageUrl(p.coverImage),
                  fit: BoxFit.cover,
                  errorWidget: (_, _, _) =>
                      Container(color: scheme.primaryContainer),
                ),
              ),
            ),
          const SizedBox(height: 14),
          if ((p.subtitle ?? '').toString().isNotEmpty)
            Text(p.subtitle.toString(),
                style: Theme.of(context)
                    .textTheme
                    .bodyMedium
                    ?.copyWith(color: scheme.outline)),
          const SizedBox(height: 6),
          Text(p.title?.toString() ?? 'Puja',
              style: Theme.of(context)
                  .textTheme
                  .headlineSmall
                  ?.copyWith(fontWeight: FontWeight.w800)),
          const SizedBox(height: 8),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: [
              if ((p.place ?? '').toString().isNotEmpty)
                Chip(
                    avatar: const Icon(Icons.place_outlined, size: 18),
                    label: Text(p.place.toString())),
              if (_dateOnly(p.startDatetime).isNotEmpty)
                Chip(
                    avatar: const Icon(Icons.event_outlined, size: 18),
                    label: Text('Starts ${_dateOnly(p.startDatetime)}')),
              if (_dateOnly(p.endDatetime).isNotEmpty)
                Chip(
                    avatar: const Icon(Icons.event_busy_outlined, size: 18),
                    label: Text('Ends ${_dateOnly(p.endDatetime)}')),
            ],
          ),
          if ((p.longDescription ?? '').toString().isNotEmpty) ...[
            const SizedBox(height: 18),
            Text('About',
                style: Theme.of(context)
                    .textTheme
                    .titleMedium
                    ?.copyWith(fontWeight: FontWeight.w800)),
            const SizedBox(height: 8),
            Text(p.longDescription.toString(),
                style: Theme.of(context).textTheme.bodyMedium),
          ],
          if ((p.benefits ?? const <String>[]).isNotEmpty) ...[
            const SizedBox(height: 18),
            Text('Benefits',
                style: Theme.of(context)
                    .textTheme
                    .titleMedium
                    ?.copyWith(fontWeight: FontWeight.w800)),
            const SizedBox(height: 8),
            for (final b in p.benefits!)
              ListTile(
                dense: true,
                contentPadding: EdgeInsets.zero,
                leading: Icon(Icons.check_circle_outline,
                    color: scheme.primary, size: 20),
                title: Text(b),
              ),
          ],
          if (packages.isNotEmpty) ...[
            const SizedBox(height: 14),
            Text('Packages',
                style: Theme.of(context)
                    .textTheme
                    .titleMedium
                    ?.copyWith(fontWeight: FontWeight.w800)),
            const SizedBox(height: 8),
            for (final pkg in packages)
              Card(
                margin: const EdgeInsets.only(bottom: 10),
                child: ListTile(
                  title: Text(pkg.name?.toString() ?? 'Package',
                      style: const TextStyle(fontWeight: FontWeight.w700)),
                  subtitle: (pkg.inclusions ?? const <String>[]).isEmpty
                      ? null
                      : Text(pkg.inclusions!.join(', ')),
                  trailing: Text('₹${pkg.priceValue.toStringAsFixed(0)}',
                      style: TextStyle(
                          fontWeight: FontWeight.w800, color: scheme.primary)),
                ),
              ),
          ],
          if (_faqs != null && _faqs!.isNotEmpty) ...[
            const SizedBox(height: 14),
            Text('FAQs',
                style: Theme.of(context)
                    .textTheme
                    .titleMedium
                    ?.copyWith(fontWeight: FontWeight.w800)),
            for (final f in _faqs!)
              ExpansionTile(
                tilePadding: EdgeInsets.zero,
                title: Text((f['question'] ?? f['title'] ?? 'Q').toString()),
                childrenPadding: const EdgeInsets.only(bottom: 10),
                children: [
                  Align(
                    alignment: Alignment.centerLeft,
                    child: Text(
                      (f['answer'] ?? f['description'] ?? '').toString(),
                      style: Theme.of(context).textTheme.bodyMedium,
                    ),
                  ),
                ],
              ),
          ],
          const SizedBox(height: 24),
        ],
      ),
    );
  }
}
