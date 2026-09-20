import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// Daily horoscope: sign grid + prediction detail
/// (legacy `dailyHoroscopeScreen.dart` / `dailyHoroScopeDetailScreen.dart`).
class DailyHoroscopeScreen extends StatefulWidget {
  const DailyHoroscopeScreen({super.key});

  @override
  State<DailyHoroscopeScreen> createState() => _DailyHoroscopeScreenState();
}

class _DailyHoroscopeScreenState extends State<DailyHoroscopeScreen> {
  static const _glyphs = [
    '♈', '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓',
  ];

  List<HoroscopeSign>? _signs;
  Object? _error;
  DailyHoroscope? _selected;
  bool _loadingDetail = false;

  @override
  void initState() {
    super.initState();
    _loadSigns();
  }

  Future<void> _loadSigns() async {
    setState(() => _error = null);
    try {
      final signs = await HoroscopeApi.instance.signs();
      if (mounted) setState(() => _signs = signs);
    } catch (e) {
      if (mounted) setState(() => _error = e);
    }
  }

  Future<void> _openSign(HoroscopeSign sign) async {
    setState(() {
      _loadingDetail = true;
      _selected = null;
    });
    try {
      final list = await HoroscopeApi.instance.daily(signId: sign.id);
      if (mounted) setState(() => _selected = list.isEmpty ? null : list.first);
    } catch (e) {
      if (mounted) {
        setState(() => _selected = null);
        showSnack(context, e.toString(), error: true);
      }
    } finally {
      if (mounted) setState(() => _loadingDetail = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(title: const Text('Daily Horoscope')),
      body: _error != null
          ? StatusViews.error(context, _error!, onRetry: _loadSigns)
          : _signs == null
              ? StatusViews.loading(context)
              : ListView(
                  padding: const EdgeInsets.all(16),
                  children: [
                    GridView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      gridDelegate:
                          const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 4,
                        mainAxisSpacing: 10,
                        crossAxisSpacing: 10,
                        childAspectRatio: 0.95,
                      ),
                      itemCount: _signs!.length,
                      itemBuilder: (context, i) {
                        final sign = _signs![i];
                        return InkWell(
                          borderRadius: BorderRadius.circular(14),
                          onTap: () => _openSign(sign),
                          child: Container(
                            decoration: BoxDecoration(
                              color: scheme.surface,
                              borderRadius: BorderRadius.circular(14),
                              border: Border.all(
                                  color:
                                      scheme.outline.withValues(alpha: 0.35)),
                            ),
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Text(
                                  i < _glyphs.length ? _glyphs[i] : '✷',
                                  style: TextStyle(
                                      fontSize: 24, color: scheme.primary),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  sign.name ?? '',
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  textAlign: TextAlign.center,
                                  style: Theme.of(context)
                                      .textTheme
                                      .labelSmall
                                      ?.copyWith(
                                          fontWeight: FontWeight.w700),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
                    const SizedBox(height: 18),
                    if (_loadingDetail)
                      const Center(
                          child: CircularProgressIndicator(strokeWidth: 2.4))
                    else if (_selected != null)
                      _predictionCard(context, scheme),
                  ],
                ),
    );
  }

  Widget _predictionCard(BuildContext context, ColorScheme scheme) {
    final h = _selected!;
    return Card(
      margin: EdgeInsets.zero,
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Text(h.signName ?? 'Horoscope',
                    style: Theme.of(context).textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.w800, color: scheme.primary)),
                const Spacer(),
                if ((h.date ?? '').isNotEmpty)
                  Text(h.date!,
                      style: Theme.of(context)
                          .textTheme
                          .bodySmall
                          ?.copyWith(color: scheme.outline)),
              ],
            ),
            const Divider(height: 24),
            Text(h.predictions ?? 'No prediction available today.',
                style: Theme.of(context).textTheme.bodyMedium),
            const SizedBox(height: 16),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: [
                if ((h.luckyNumber ?? '').isNotEmpty)
                  Chip(label: Text('Lucky number: ${h.luckyNumber}')),
                if ((h.luckyColor ?? '').isNotEmpty)
                  Chip(label: Text('Lucky color: ${h.luckyColor}')),
                if ((h.mood ?? '').isNotEmpty)
                  Chip(label: Text('Mood: ${h.mood}')),
                if ((h.compatibility ?? '').isNotEmpty)
                  Chip(label: Text('Compatible with ${h.compatibility}')),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
