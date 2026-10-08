import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:op_shared/op_shared.dart';
import 'choghadiya_radar_screen.dart';

/// Today's panchang and live Choghadiya for a selected location.
class PanchangScreen extends StatefulWidget {
  const PanchangScreen({super.key});

  static const route = '/panchang';

  @override
  State<PanchangScreen> createState() => _PanchangScreenState();
}

class _PanchangScreenState extends State<PanchangScreen> with SingleTickerProviderStateMixin {
  final _lat = TextEditingController(text: '28.6139');
  final _lng = TextEditingController(text: '77.2090');
  Panchang? _panchang;
  bool _loading = false;
  Object? _error;
  int _viewTab = 0; // 0: Panchang, 1: Choghadiya

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

  static const _cities = [
    ('New Delhi', '28.6139', '77.2090'),
    ('Varanasi', '25.3176', '82.9739'),
    ('Ayodhya', '26.7922', '82.1998'),
    ('Ujjain', '23.1765', '75.7885'),
    ('Haridwar', '29.9457', '78.1642'),
    ('Mumbai', '19.0760', '72.8777'),
    ('Bengaluru', '12.9716', '77.5946'),
    ('Kolkata', '22.5726', '88.3639'),
  ];
  String _selectedCity = 'New Delhi';

  @override
  void dispose() {
    _lat.dispose();
    _lng.dispose();
    super.dispose();
  }

  void _pickCity(String name, String lat, String lng) {
    setState(() {
      _selectedCity = name;
      _lat.text = lat;
      _lng.text = lng;
    });
    _load();
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(
        title: const Text('Vedic Panchang & Choghadiya', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 18)),
        actions: [
          IconButton(
            tooltip: 'Live Choghadiya Radar Clock',
            icon: const Icon(Icons.radar_rounded, color: Color(0xFFD97706)),
            onPressed: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const ChoghadiyaRadarScreen()),
            ),
          ),
        ],
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text('Select City / Location',
              style: Theme.of(context)
                  .textTheme
                  .titleSmall
                  ?.copyWith(fontWeight: FontWeight.w700)),
          const SizedBox(height: 8),
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: _cities.map((c) {
                final isSelected = _selectedCity == c.$1;
                return Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: ChoiceChip(
                    avatar: Icon(Icons.location_on_outlined,
                        size: 16,
                        color: isSelected ? scheme.onPrimary : scheme.primary),
                    label: Text(c.$1),
                    selected: isSelected,
                    onSelected: (_) => _pickCity(c.$1, c.$2, c.$3),
                  ),
                );
              }).toList(),
            ),
          ),
          const SizedBox(height: 14),
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
          const SizedBox(height: 16),

          // View Selector Tabs: Panchang vs Choghadiya
          Container(
            decoration: BoxDecoration(
              color: scheme.surfaceContainerHighest.withValues(alpha: 0.4),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Row(
              children: [
                Expanded(
                  child: GestureDetector(
                    onTap: () => setState(() => _viewTab = 0),
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      decoration: BoxDecoration(
                        color: _viewTab == 0 ? AppTheme.brandSaffron : Colors.transparent,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        '📜 Vedic Panchang',
                        style: TextStyle(
                          color: _viewTab == 0 ? Colors.white : scheme.onSurface,
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                        ),
                      ),
                    ),
                  ),
                ),
                Expanded(
                  child: GestureDetector(
                    onTap: () => setState(() => _viewTab = 1),
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      decoration: BoxDecoration(
                        color: _viewTab == 1 ? AppTheme.brandSaffron : Colors.transparent,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        '⚡ Choghadiya (चौघड़िया)',
                        style: TextStyle(
                          color: _viewTab == 1 ? Colors.white : scheme.onSurface,
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

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
            _viewTab == 0 ? _buildPanchangResult(context, scheme) : _buildChoghadiyaResult(context, scheme),
        ],
      ),
    );
  }

  Widget _buildPanchangResult(BuildContext context, ColorScheme scheme) {
    final p = _panchang!;
    final rows = <(String, String?)>[
      ('Sunrise (सूर्योदय)', p.sunrise),
      ('Sunset (सूर्यास्त)', p.sunset),
      ('Vedic sunrise', p.vedicSunrise),
      ('Vedic sunset', p.vedicSunset),
      ('Moonrise (चन्द्रोदय)', p.moonrise),
      ('Moonset (चन्द्रास्त)', p.moonset),
      ('Tithi (तिथि)', p.tithi),
      ('Nakshatra (नक्षत्र)', p.nakshatra),
      ('Yog (योग)', p.yog),
      ('Karan (करण)', p.karan),
      ('Rahu Kaal (राहुकाल)', p.rahukaal),
      ('Masa (मास)', p.masa),
      ('Paksha (पक्ष)', p.paksha),
      ('Ritu (ऋतु)', p.ritu),
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
              Divider(height: 1, indent: 16, color: scheme.outline.withValues(alpha: 0.2)),
          ],
        ],
      ),
    );
  }

  Widget _buildChoghadiyaResult(BuildContext context, ColorScheme scheme) {
    final now = DateTime.now();
    final weekday = now.weekday; // 1 = Mon, 7 = Sun

    // Vedic Day Choghadiya pattern for each weekday
    const weekdayPatterns = {
      7: ['Udveg (उद्वेग)', 'Char (चर)', 'Labh (लाभ)', 'Amrit (अमृत)', 'Kaal (काल)', 'Shubh (शुभ)', 'Rog (रोग)', 'Udveg (उद्वेग)'], // Sunday
      1: ['Amrit (अमृत)', 'Kaal (काल)', 'Shubh (शुभ)', 'Rog (रोग)', 'Udveg (उद्वेग)', 'Char (चर)', 'Labh (लाभ)', 'Amrit (अमृत)'], // Monday
      2: ['Rog (रोग)', 'Udveg (उद्वेग)', 'Char (चर)', 'Labh (लाभ)', 'Amrit (अमृत)', 'Kaal (काल)', 'Shubh (शुभ)', 'Rog (रोग)'], // Tuesday
      3: ['Labh (लाभ)', 'Amrit (अमृत)', 'Kaal (काल)', 'Shubh (शुभ)', 'Rog (रोग)', 'Udveg (उद्वेग)', 'Char (चर)', 'Labh (लाभ)'], // Wednesday
      4: ['Shubh (शुभ)', 'Rog (रोग)', 'Udveg (उद्वेग)', 'Char (चर)', 'Labh (लाभ)', 'Amrit (अमृत)', 'Kaal (काल)', 'Shubh (शुभ)'], // Thursday
      5: ['Char (चर)', 'Labh (लाभ)', 'Amrit (अमृत)', 'Kaal (काल)', 'Shubh (शुभ)', 'Rog (रोग)', 'Udveg (उद्वेग)', 'Char (चर)'], // Friday
      6: ['Kaal (काल)', 'Shubh (शुभ)', 'Rog (रोग)', 'Udveg (उद्वेग)', 'Char (चर)', 'Labh (लाभ)', 'Amrit (अमृत)', 'Kaal (काल)'], // Saturday
    };

    final sequence = weekdayPatterns[weekday] ?? weekdayPatterns[1]!;

    // Approximate Sunrise at 06:00 and Sunset at 18:00 if not parseable
    DateTime riseTime = DateTime(now.year, now.month, now.day, 6, 0);
    DateTime setTime = DateTime(now.year, now.month, now.day, 18, 0);

    final sunriseStr = _panchang?.sunrise ?? '';
    final sunsetStr = _panchang?.sunset ?? '';

    try {
      if (sunriseStr.contains(':')) {
        final parts = sunriseStr.split(':').map((s) => int.parse(s.replaceAll(RegExp(r'[^0-9]'), ''))).toList();
        riseTime = DateTime(now.year, now.month, now.day, parts[0], parts[1]);
      }
      if (sunsetStr.contains(':')) {
        final parts = sunsetStr.split(':').map((s) => int.parse(s.replaceAll(RegExp(r'[^0-9]'), ''))).toList();
        int hour = parts[0];
        if (hour < 12) hour += 12; // Evening sunset
        setTime = DateTime(now.year, now.month, now.day, hour, parts[1]);
      }
    } catch (_) {}

    final totalDayMinutes = setTime.difference(riseTime).inMinutes;
    final intervalMinutes = (totalDayMinutes / 8).floor();

    final timeFormatter = DateFormat('hh:mm a');
    int activeIndex = -1;

    final choghadiyaSlots = <Map<String, dynamic>>[];
    for (int i = 0; i < 8; i++) {
      final slotStart = riseTime.add(Duration(minutes: i * intervalMinutes));
      final slotEnd = riseTime.add(Duration(minutes: (i + 1) * intervalMinutes));
      final name = sequence[i];

      final isGood = name.contains('Amrit') || name.contains('Shubh') || name.contains('Labh');
      final isNeutral = name.contains('Char');

      final isCurrent = now.isAfter(slotStart) && now.isBefore(slotEnd);
      if (isCurrent) activeIndex = i;

      choghadiyaSlots.add({
        'name': name,
        'start': timeFormatter.format(slotStart),
        'end': timeFormatter.format(slotEnd),
        'isGood': isGood,
        'isNeutral': isNeutral,
        'isCurrent': isCurrent,
      });
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        // Live Active Choghadiya Hero Card
        if (activeIndex >= 0) ...[
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: choghadiyaSlots[activeIndex]['isGood']
                    ? [Colors.green.shade700, Colors.teal.shade800]
                    : [Colors.deepOrange.shade800, Colors.red.shade900],
              ),
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.15),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Row(
              children: [
                CircleAvatar(
                  radius: 24,
                  backgroundColor: Colors.white.withValues(alpha: 0.2),
                  child: Icon(
                    choghadiyaSlots[activeIndex]['isGood'] ? Icons.check_circle_outline : Icons.access_time_filled,
                    color: Colors.white,
                    size: 28,
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                            decoration: BoxDecoration(
                              color: Colors.white.withValues(alpha: 0.25),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: const Text(
                              'CURRENT CHOGHADIYA NOW',
                              style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Text(
                        choghadiyaSlots[activeIndex]['name'],
                        style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w800),
                      ),
                      Text(
                        '${choghadiyaSlots[activeIndex]['start']} – ${choghadiyaSlots[activeIndex]['end']} · ${choghadiyaSlots[activeIndex]['isGood'] ? 'अति शुभ (Highly Auspicious)' : 'अशुभ / सामान्य (Avoid New Ventures)'}',
                        style: TextStyle(color: Colors.white.withValues(alpha: 0.9), fontSize: 12),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 14),
        ],

        // 8 Day Choghadiya Slots Listing
        Card(
          margin: EdgeInsets.zero,
          child: Column(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: scheme.surfaceContainerHighest.withValues(alpha: 0.3),
                  borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.wb_sunny_rounded, color: Colors.orange, size: 18),
                    const SizedBox(width: 8),
                    Text(
                      'Day Choghadiya Timings (दिन का चौघड़िया)',
                      style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
              for (int i = 0; i < choghadiyaSlots.length; i++) ...[
                ListTile(
                  dense: true,
                  tileColor: choghadiyaSlots[i]['isCurrent'] ? Colors.amber.shade50 : null,
                  leading: Icon(
                    choghadiyaSlots[i]['isGood']
                        ? Icons.check_circle_rounded
                        : (choghadiyaSlots[i]['isNeutral'] ? Icons.remove_circle_outline : Icons.cancel_rounded),
                    color: choghadiyaSlots[i]['isGood']
                        ? Colors.green
                        : (choghadiyaSlots[i]['isNeutral'] ? Colors.orange : Colors.red),
                    size: 20,
                  ),
                  title: Row(
                    children: [
                      Text(
                        choghadiyaSlots[i]['name'],
                        style: TextStyle(
                          fontWeight: choghadiyaSlots[i]['isCurrent'] ? FontWeight.bold : FontWeight.w600,
                          fontSize: 13.5,
                        ),
                      ),
                      if (choghadiyaSlots[i]['isCurrent']) ...[
                        const SizedBox(width: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1.5),
                          decoration: BoxDecoration(
                            color: Colors.green,
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: const Text('NOW', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold)),
                        ),
                      ],
                    ],
                  ),
                  trailing: Text(
                    '${choghadiyaSlots[i]['start']} – ${choghadiyaSlots[i]['end']}',
                    style: TextStyle(
                      fontWeight: FontWeight.w600,
                      color: scheme.primary,
                      fontSize: 12,
                    ),
                  ),
                ),
                if (i < choghadiyaSlots.length - 1)
                  Divider(height: 1, indent: 16, color: scheme.outline.withValues(alpha: 0.15)),
              ],
            ],
          ),
        ),
      ],
    );
  }
}
