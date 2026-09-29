import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';

/// Kundli matching (guna milan) — legacy `kundliMatchingScreen.dart` +
/// `matchResultScreen.dart`. Two birth-detail forms → `/KundaliMatching/add`
/// → score card + defensive rendering of the engine payload.
class KundliMatchingScreen extends StatefulWidget {
  const KundliMatchingScreen({super.key});

  static const route = '/kundli-matching';

  @override
  State<KundliMatchingScreen> createState() => _KundliMatchingScreenState();
}

class _KundliMatchingScreenState extends State<KundliMatchingScreen> {
  List<Kundli>? _saved;
  bool _loading = false;
  Map<String, dynamic>? _result;
  Object? _error;

  final _boyName = TextEditingController();
  final _boyDate = TextEditingController();
  final _boyTime = TextEditingController();
  final _boyPlace = TextEditingController();
  final _girlName = TextEditingController();
  final _girlDate = TextEditingController();
  final _girlTime = TextEditingController();
  final _girlPlace = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadSaved();
  }

  @override
  void dispose() {
    for (final c in [
      _boyName, _boyDate, _boyTime, _boyPlace,
      _girlName, _girlDate, _girlTime, _girlPlace,
    ]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _loadSaved() async {
    final userId = context.read<AppSession>().user?.id ?? 0;
    if (userId == 0) return;
    try {
      final list = await KundliApi.instance.list(userId: userId);
      if (mounted) setState(() => _saved = list);
    } catch (_) {
      // optional prefill source; ignore failures
    }
  }

  void _prefill(int which, Kundli k) {
    setState(() {
      _boyName.text = which == 0 ? k.name : _boyName.text;
      _boyDate.text = which == 0 ? _fmtDate(k.birthDate) : _boyDate.text;
      _boyTime.text = which == 0 ? k.birthTime : _boyTime.text;
      _boyPlace.text = which == 0 ? k.birthPlace : _boyPlace.text;
      _girlName.text = which == 1 ? k.name : _girlName.text;
      _girlDate.text = which == 1 ? _fmtDate(k.birthDate) : _girlDate.text;
      _girlTime.text = which == 1 ? k.birthTime : _girlTime.text;
      _girlPlace.text = which == 1 ? k.birthPlace : _girlPlace.text;
    });
  }

  static String _fmtDate(dynamic raw) {
    if (raw is DateTime) return DateFormat('yyyy-MM-dd').format(raw);
    final d = DateTime.tryParse(raw?.toString() ?? '');
    return d == null ? (raw?.toString() ?? '') : DateFormat('yyyy-MM-dd').format(d);
  }

  Future<void> _match() async {
    if (_boyName.text.trim().isEmpty ||
        _boyDate.text.trim().isEmpty ||
        _girlName.text.trim().isEmpty ||
        _girlDate.text.trim().isEmpty) {
      showSnack(context, 'Fill at least both names and birth dates.',
          error: true);
      return;
    }
    setState(() {
      _loading = true;
      _error = null;
      _result = null;
    });
    try {
      final r = await KundliApi.instance.matching(
        boyName: _boyName.text.trim(),
        boyBirthDate: _boyDate.text.trim(),
        boyBirthTime:
            _boyTime.text.trim().isEmpty ? '12:00' : _boyTime.text.trim(),
        boyBirthPlace: _boyPlace.text.trim(),
        girlName: _girlName.text.trim(),
        girlBirthDate: _girlDate.text.trim(),
        girlBirthTime:
            _girlTime.text.trim().isEmpty ? '12:00' : _girlTime.text.trim(),
        girlBirthPlace: _girlPlace.text.trim(),
      );
      if (mounted) setState(() => _result = r);
    } catch (e) {
      if (mounted) setState(() => _error = e);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final saved = _saved ?? const <Kundli>[];
    return Scaffold(
      appBar: AppBar(title: const Text('Kundli Matching')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _personCard(
            context,
            title: 'Boy',
            icon: Icons.male_rounded,
            name: _boyName,
            date: _boyDate,
            time: _boyTime,
            place: _boyPlace,
            saved: saved,
            onPrefill: (k) => _prefill(0, k),
          ),
          const SizedBox(height: 14),
          _personCard(
            context,
            title: 'Girl',
            icon: Icons.female_rounded,
            name: _girlName,
            date: _girlDate,
            time: _girlTime,
            place: _girlPlace,
            saved: saved,
            onPrefill: (k) => _prefill(1, k),
          ),
          const SizedBox(height: 20),
          FilledButton(
            style:
                FilledButton.styleFrom(minimumSize: const Size.fromHeight(52)),
            onPressed: _loading ? null : _match,
            child: _loading
                ? const SizedBox(
                    width: 22,
                    height: 22,
                    child: CircularProgressIndicator(strokeWidth: 2))
                : const Text('Match Kundli ✨'),
          ),
          if (_error != null) ...[
            const SizedBox(height: 16),
            StatusViews.error(context, _error!, onRetry: _match),
          ],
          if (_result != null) ...[
            const SizedBox(height: 20),
            _resultCard(context, _result!),
          ],
        ],
      ),
    );
  }

  Widget _personCard(
    BuildContext context, {
    required String title,
    required IconData icon,
    required TextEditingController name,
    required TextEditingController date,
    required TextEditingController time,
    required TextEditingController place,
    required List<Kundli> saved,
    required ValueChanged<Kundli> onPrefill,
  }) {
    final scheme = Theme.of(context).colorScheme;
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(children: [
              Icon(icon, color: scheme.primary),
              const SizedBox(width: 8),
              Text(title,
                  style: Theme.of(context)
                      .textTheme
                      .titleMedium
                      ?.copyWith(fontWeight: FontWeight.w800)),
              const Spacer(),
              if (saved.isNotEmpty)
                PopupMenuButton<Kundli>(
                  tooltip: 'Prefill from saved kundli',
                  icon: const Icon(Icons.library_books_outlined, size: 20),
                  onSelected: onPrefill,
                  itemBuilder: (_) => [
                    for (final k in saved)
                      PopupMenuItem(
                        value: k,
                        child: Text('${k.name} · ${_fmtDate(k.birthDate)}'),
                      ),
                  ],
                ),
            ]),
            const SizedBox(height: 10),
            TextField(
              controller: name,
              decoration: const InputDecoration(labelText: 'Name'),
            ),
            const SizedBox(height: 10),
            Row(children: [
              Expanded(
                child: TextField(
                  controller: date,
                  readOnly: true,
                  decoration: const InputDecoration(labelText: 'Birth date'),
                  onTap: () async {
                    final d = await showDatePicker(
                      context: context,
                      initialDate: DateTime(1995),
                      firstDate: DateTime(1930),
                      lastDate: DateTime.now(),
                    );
                    if (d != null) {
                      date.text = DateFormat('yyyy-MM-dd').format(d);
                    }
                  },
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: TextField(
                  controller: time,
                  readOnly: true,
                  decoration: const InputDecoration(labelText: 'Birth time'),
                  onTap: () async {
                    final t =
                        await showTimePicker(context: context, initialTime: TimeOfDay.now());
                    if (t != null) {
                      time.text =
                          '${t.hour.toString().padLeft(2, '0')}:${t.minute.toString().padLeft(2, '0')}';
                    }
                  },
                ),
              ),
            ]),
            const SizedBox(height: 10),
            TextField(
              controller: place,
              decoration: const InputDecoration(labelText: 'Birth place'),
            ),
          ],
        ),
      ),
    );
  }

  /// Defensive result rendering: score banner + key/value sections for
  /// whatever the astrology engine returned.
  Widget _resultCard(BuildContext context, Map<String, dynamic> r) {
    final scheme = Theme.of(context).colorScheme;
    final pct = _findPercentage(r);
    final sections = _flattenSections(r);

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: double.infinity,
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            gradient: LinearGradient(colors: [scheme.primary, scheme.tertiary]),
            borderRadius: BorderRadius.circular(20),
          ),
          child: Column(children: [
            const Text('Matching score',
                style: TextStyle(color: Colors.white70)),
            const SizedBox(height: 6),
            Text(
              pct != null ? '$pct%' : '—',
              style: const TextStyle(
                  color: Colors.white,
                  fontSize: 44,
                  fontWeight: FontWeight.w900),
            ),
            Text(
              pct == null
                  ? 'See details below'
                  : pct >= 50
                      ? 'Great match 🎉'
                      : 'Below 50 — consult an astrologer',
              style: const TextStyle(color: Colors.white),
            ),
          ]),
        ),
        const SizedBox(height: 16),
        for (final section in sections) ...[
          Card(
            child: Padding(
              padding: const EdgeInsets.all(14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(section.$1,
                      style: Theme.of(context)
                          .textTheme
                          .titleSmall
                          ?.copyWith(fontWeight: FontWeight.w800)),
                  const SizedBox(height: 8),
                  for (final row in section.$2)
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 2),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Expanded(
                              flex: 2,
                              child: Text(row.$1,
                                  style: TextStyle(color: scheme.outline))),
                          Expanded(flex: 3, child: Text(row.$2)),
                        ],
                      ),
                    ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 10),
        ],
      ],
    );
  }

  double? _findPercentage(Map<String, dynamic> r) {
    for (final key in ['percentage', 'matchPercentage', 'score', 'percent']) {
      final v = r[key];
      final d =
          v is num ? v.toDouble() : double.tryParse(v?.toString() ?? '');
      if (d != null) return d;
    }
    // ashtakoota engines nest the score: {"ashtakoota": {"percentage": 36}}
    final ak = r['ashtakoota'];
    if (ak is Map<String, dynamic>) return _findPercentage(ak);
    return null;
  }

  /// Turn the (arbitrary) result payload into human sections:
  /// `(sectionTitle, [(key, value)])` pairs, skipping long text blobs.
  List<(String, List<(String, String)>)> _flattenSections(
      Map<String, dynamic> r) {
    final out = <(String, List<(String, String)>)>[];
    String pretty(String k) => k
        .replaceAllMapped(RegExp(r'([A-Z])'), (m) => ' ${m[1]}')
        .replaceFirstMapped(RegExp(r'^[a-z]'), (m) => m[0]!.toUpperCase());

    void flatten(String prefix, Map<String, dynamic> map) {
      final rows = <(String, String)>[];
      map.forEach((k, v) {
        if (v is num || v is String) {
          final s = v.toString();
          if (s.length < 400) rows.add((pretty(k), s));
        } else if (v is Map<String, dynamic> && prefix.isEmpty) {
          flatten(pretty(k), v);
        }
      });
      if (rows.isNotEmpty) {
        out.add((prefix.isEmpty ? 'Details' : prefix, rows));
      }
    }

    flatten('', r);
    return out.take(6).toList();
  }
}
