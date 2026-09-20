import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// Kundli details: Basic / Planets / Dasha / Dosha tabs
/// (legacy `kundliDetailsScreen.dart` + `basicdetailwidget.dart`).
class KundliDetailScreen extends StatefulWidget {
  const KundliDetailScreen({super.key, required this.kundli});

  final Kundli kundli;

  @override
  State<KundliDetailScreen> createState() => _KundliDetailScreenState();
}

class _KundliDetailScreenState extends State<KundliDetailScreen>
    with SingleTickerProviderStateMixin {
  KundliBasic? _basic;
  Map<String, dynamic>? _chart;
  Map<String, dynamic>? _dasha;
  Map<String, dynamic>? _dosha;
  Object? _error;
  late final TabController _tabs =
      TabController(length: 4, vsync: this);

  double get _tz {
    final t = double.tryParse(widget.kundli.timezone?.toString() ?? '');
    return t ?? 5.5;
  }

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _error = null);
    final k = widget.kundli;
    final api = KundliApi.instance;
    try {
      final results = await Future.wait([
        api.basic(
            date: k.birthDate,
            time: k.birthTime,
            lat: k.latitude ?? 0,
            lng: k.longitude ?? 0,
            tz: _tz),
        api.chart(
            date: k.birthDate,
            time: k.birthTime,
            lat: k.latitude ?? 0,
            lng: k.longitude ?? 0,
            tz: _tz),
        api.dasha(
            date: k.birthDate,
            time: k.birthTime,
            lat: k.latitude ?? 0,
            lng: k.longitude ?? 0,
            tz: _tz),
        api.dosha(
            date: k.birthDate,
            time: k.birthTime,
            lat: k.latitude ?? 0,
            lng: k.longitude ?? 0,
            tz: _tz),
      ]);
      if (!mounted) return;
      setState(() {
        _basic = results[0] as KundliBasic;
        _chart = results[1] as Map<String, dynamic>;
        _dasha = results[2] as Map<String, dynamic>;
        _dosha = results[3] as Map<String, dynamic>;
      });
    } catch (e) {
      if (mounted) setState(() => _error = e);
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final k = widget.kundli;
    const tabs = ['Basic', 'Planets', 'Dasha', 'Dosha'];
    return Scaffold(
      appBar: AppBar(title: Text(k.name)),
      body: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: Text(
              '${k.birthDate.toIso8601String().substring(0, 10)} · '
              '${k.birthTime} · ${k.birthPlace}',
              style: Theme.of(context)
                  .textTheme
                  .bodySmall
                  ?.copyWith(color: scheme.outline),
            ),
          ),
          TabBar(
            controller: _tabs,
            tabs: [for (final t in tabs) Tab(text: t)],
          ),
          Expanded(
            child: _error != null
                ? StatusViews.error(context, _error!, onRetry: _load)
                : _basic == null
                    ? StatusViews.loading(context)
                    : _body(context),
          ),
        ],
      ),
    );
  }

  Widget _body(BuildContext context) {
    switch (_tabs.index) {
      case 0:
        return _basicTab();
      case 1:
        return _planetsTab();
      case 2:
        return _dashaTab();
      default:
        return _doshaTab();
    }
  }

  Widget _infoGrid(List<(String, String?)> rows) {
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        Card(
          margin: EdgeInsets.zero,
          child: Column(
            children: [
              for (final (label, value) in rows) ...[
                ListTile(
                  dense: true,
                  title: Text(label),
                  trailing: Text(
                    (value ?? '—').isEmpty ? '—' : value!,
                    style: const TextStyle(fontWeight: FontWeight.w700),
                  ),
                ),
                if (label != rows.last.$1)
                  const Divider(height: 1, indent: 16),
              ],
            ],
          ),
        ),
      ],
    );
  }

  Widget _basicTab() {
    final b = _basic!;
    return _infoGrid([
      ('Tithi', b.tithi),
      ('Karan', b.karan),
      ('Yog', b.yog),
      ('Nakshatra', b.nakshatra),
      ('Sunrise', b.sunRise),
      ('Sunset', b.sunSet),
    ]);
  }

  List<PlanetElement> get _planets {
    final pd = _chart?['planetDetails'];
    final response = pd is Map<String, dynamic> ? pd['response'] : null;
    if (response is! Map<String, dynamic>) return const [];
    return [
      for (var i = 0; i <= 9; i++)
        if (response['$i'] is Map<String, dynamic>)
          PlanetElement.fromJson(response['$i']),
    ];
  }

  Map<String, dynamic>? get _planetResponse {
    final pd = _chart?['planetDetails'];
    final response = pd is Map<String, dynamic> ? pd['response'] : null;
    return response is Map<String, dynamic> ? response : null;
  }

  Widget _planetsTab() {
    final planets = _planets;
    if (planets.isEmpty) {
      return StatusViews.empty(context, message: 'No planet data available');
    }
    final rasi = _planetResponse?['rasi']?.toString();
    final nak = _planetResponse?['nakshatra']?.toString();
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        if ((rasi ?? '').isNotEmpty || (nak ?? '').isNotEmpty)
          Padding(
            padding: const EdgeInsets.only(bottom: 12),
            child: Wrap(
              spacing: 8,
              runSpacing: 8,
              children: [
                if ((rasi ?? '').isNotEmpty) Chip(label: Text('Rasi: $rasi')),
                if ((nak ?? '').isNotEmpty)
                  Chip(label: Text('Nakshatra: $nak')),
              ],
            ),
          ),
        Card(
          margin: EdgeInsets.zero,
          child: Column(
            children: [
              for (final p in planets)
                ListTile(
                  dense: true,
                  title: Text(p.fullName ?? p.name ?? 'Planet'),
                  subtitle: Text(
                    '${p.zodiac ?? '—'} · House ${p.house ?? '-'}'
                    '${p.retro == 1 ? ' · Retrograde' : ''}',
                  ),
                  trailing: Text(
                    p.localDegree == null
                        ? '—'
                        : '${p.localDegree!.toStringAsFixed(2)}°',
                    style: const TextStyle(fontWeight: FontWeight.w700),
                  ),
                ),
            ],
          ),
        ),
      ],
    );
  }

  List<Map<String, dynamic>> _dashaRoots() {
    final d = _dasha ?? const {};
    dynamic list = d['recordList'] ?? d['response'] ?? d['dasha'];
    if (list is Map<String, dynamic>) list = list['response'];
    if (list is List) {
      return list.whereType<Map<String, dynamic>>().toList();
    }
    return const [];
  }

  Widget _dashaTab() {
    final roots = _dashaRoots();
    if (roots.isEmpty) {
      return StatusViews.empty(context, message: 'No dasha data available');
    }
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        for (final root in roots) _dashaNode(context, root, 0),
      ],
    );
  }

  Widget _dashaNode(
      BuildContext context, Map<String, dynamic> node, int depth) {
    final scheme = Theme.of(context).colorScheme;
    final planet = (node['planet'] ?? node['name'] ?? '—').toString();
    final range = (node['range'] ?? node['period'] ?? '').toString();
    final children =
        (node['children'] ?? node['sub'] ?? node['childrens']) as List?;
    final subs =
        children?.whereType<Map<String, dynamic>>().toList() ?? const [];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          margin: EdgeInsets.only(left: depth * 20.0, bottom: 6),
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
          decoration: BoxDecoration(
            color: depth == 0 ? scheme.primaryContainer : scheme.surface,
            borderRadius: BorderRadius.circular(10),
            border: Border.all(color: scheme.outline.withValues(alpha: 0.35)),
          ),
          child: Row(
            children: [
              Expanded(
                child: Text(
                  depth == 0 ? planet : '${'· ' * depth}$planet',
                  style: TextStyle(
                    fontWeight: depth == 0 ? FontWeight.w800 : FontWeight.w600,
                    color: depth == 0 ? scheme.primary : null,
                  ),
                ),
              ),
              if (range.isNotEmpty)
                Text(range,
                    style: Theme.of(context)
                        .textTheme
                        .bodySmall
                        ?.copyWith(color: scheme.outline)),
            ],
          ),
        ),
        for (final sub in subs.take(10)) _dashaNode(context, sub, depth + 1),
      ],
    );
  }

  Widget _doshaTab() {
    final d = _dosha ?? const {};
    final rows = <(String, String)>[
      for (final entry in d.entries)
        if (entry.key != 'status' && entry.key != 'recordList')
          (entry.key, entry.value == null ? '—' : entry.value.toString()),
    ];
    if (rows.isEmpty) {
      return StatusViews.empty(context, message: 'No dosha data available');
    }
    return _infoGrid([for (final (k, v) in rows) (k, v)]);
  }
}
