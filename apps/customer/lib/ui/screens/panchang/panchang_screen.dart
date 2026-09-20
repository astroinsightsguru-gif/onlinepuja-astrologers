import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// Today's panchang for a location (legacy `panchangScreen.dart`).
class PanchangScreen extends StatefulWidget {
  const PanchangScreen({super.key});

  @override
  State<PanchangScreen> createState() => _PanchangScreenState();
}

class _PanchangScreenState extends State<PanchangScreen> {
  final _lat = TextEditingController(text: '28.6139');
  final _lng = TextEditingController(text: '77.2090');
  Panchang? _panchang;
  bool _loading = false;
  Object? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final lat = double.tryParse(_lat.text.trim());
    final lng = double.tryParse(_lng.text.trim());
    if (lat == null || lng == null) {
      setState(() => _error = 'Enter valid latitude and longitude.');
      return;
    }
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final p = await HoroscopeApi.instance.panchang(lat: lat, lng: lng);
      if (mounted) setState(() => _panchang = p);
    } catch (e) {
      if (mounted) setState(() => _error = e);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  void dispose() {
    _lat.dispose();
    _lng.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(title: const Text('Panchang')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Row(
            children: [
              Expanded(
                child: TextField(
                  controller: _lat,
                  keyboardType: const TextInputType.numberWithOptions(
                      decimal: true, signed: true),
                  decoration: const InputDecoration(
                      labelText: 'Latitude', prefixIcon: Icon(Icons.explore)),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: TextField(
                  controller: _lng,
                  keyboardType: const TextInputType.numberWithOptions(
                      decimal: true, signed: true),
                  decoration: const InputDecoration(
                      labelText: 'Longitude', prefixIcon: Icon(Icons.explore)),
                ),
              ),
              const SizedBox(width: 10),
              FilledButton.tonal(
                onPressed: _loading ? null : _load,
                child: const Text('Go'),
              ),
            ],
          ),
          const SizedBox(height: 20),
          if (_loading)
            const Padding(
              padding: EdgeInsets.symmetric(vertical: 60),
              child: Center(child: CircularProgressIndicator(strokeWidth: 2.4)),
            )
          else if (_error != null)
            StatusViews.error(context, _error!, onRetry: _load)
          else if (_panchang == null)
            StatusViews.empty(context, message: 'Enter a location and search')
          else
            _buildResult(context, scheme),
        ],
      ),
    );
  }

  Widget _buildResult(BuildContext context, ColorScheme scheme) {
    final p = _panchang!;
    final rows = <(String, String?)>[
      ('Sunrise', p.sunrise),
      ('Sunset', p.sunset),
      ('Vedic sunrise', p.vedicSunrise),
      ('Vedic sunset', p.vedicSunset),
      ('Moonrise', p.moonrise),
      ('Moonset', p.moonset),
      ('Tithi', p.tithi),
      ('Nakshatra', p.nakshatra),
      ('Yog', p.yog),
      ('Karan', p.karan),
      ('Rahukaal', p.rahukaal),
      ('Masa', p.masa),
      ('Paksha', p.paksha),
      ('Ritu', p.ritu),
      ('Vikram Samvat', p.vikramSamvat),
      ('Shaka Samvat', p.shakaSamvat),
    ].where((r) => (r.$2 ?? '').isNotEmpty).toList();
    return Card(
      margin: EdgeInsets.zero,
      child: Column(
        children: [
          for (final (label, value) in rows) ...[
            ListTile(
              dense: true,
              title: Text(label, style: Theme.of(context).textTheme.bodyMedium),
              trailing: Text(
                value ?? '',
                style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                      fontWeight: FontWeight.w700,
                      color: scheme.primary,
                    ),
              ),
            ),
            if (label != rows.last.$1)
              Divider(height: 1, indent: 16, color: scheme.outline),
          ],
        ],
      ),
    );
  }
}
