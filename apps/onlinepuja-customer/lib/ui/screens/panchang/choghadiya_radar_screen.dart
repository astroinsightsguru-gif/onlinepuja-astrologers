import 'dart:async';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:op_shared/op_shared.dart';

import '../../theme/customer_theme.dart';

class ChoghadiyaRadarScreen extends StatefulWidget {
  const ChoghadiyaRadarScreen({super.key});

  static const route = '/choghadiya-radar';

  @override
  State<ChoghadiyaRadarScreen> createState() => _ChoghadiyaRadarScreenState();
}

class _ChoghadiyaRadarScreenState extends State<ChoghadiyaRadarScreen> {
  bool _isDay = true;
  DateTime _now = DateTime.now();
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _timer = Timer.periodic(const Duration(seconds: 30), (_) {
      if (mounted) setState(() => _now = DateTime.now());
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  List<Map<String, dynamic>> _generateChoghadiya(bool isDay) {
    // 8 Choghadiya periods per day/night (~1.5 hours each)
    final sunrise = DateTime(_now.year, _now.month, _now.day, 6, 12);
    final sunset = DateTime(_now.year, _now.month, _now.day, 18, 24);

    final start = isDay ? sunrise : sunset;

    // Day Choghadiya pattern for Thursday (Guruvar):
    // Shubh, Rog, Udveg, Chal, Labh, Amrit, Kaal, Shubh
    final dayNames = ['Shubh', 'Rog', 'Udveg', 'Chal', 'Labh', 'Amrit', 'Kaal', 'Shubh'];
    final nightNames = ['Amrit', 'Chal', 'Rog', 'Kaal', 'Labh', 'Udveg', 'Shubh', 'Amrit'];

    final names = isDay ? dayNames : nightNames;
    final list = <Map<String, dynamic>>[];

    var curStart = start;
    for (int i = 0; i < 8; i++) {
      final curEnd = curStart.add(const Duration(minutes: 91));
      final name = names[i];

      final isGood = ['Shubh', 'Labh', 'Amrit'].contains(name);
      final isNeutral = name == 'Chal';

      final isActive = _now.isAfter(curStart) && _now.isBefore(curEnd);

      list.add({
        'name': name,
        'start': curStart,
        'end': curEnd,
        'isGood': isGood,
        'isNeutral': isNeutral,
        'isActive': isActive,
        'recommendation': _getRecommendation(name),
      });

      curStart = curEnd;
    }
    return list;
  }

  String _getRecommendation(String name) {
    switch (name) {
      case 'Amrit':
        return 'Highest Auspiciousness (Amrit Siddhi). Ideal for Puja, Gold, Marriage & Journey.';
      case 'Shubh':
        return 'Highly Favorable. Ideal for Religious rituals, New Ventures & Financial agreements.';
      case 'Labh':
        return 'Gain & Prosperity. Favorable for Business investments, Trade & Academics.';
      case 'Chal':
        return 'Moderate / Neutral. Suitable for Travel, Moving goods, Vehicle driving.';
      case 'Kaal':
        return 'Inauspicious (Ruled by Saturn). Avoid starting auspicious tasks or signing deals.';
      case 'Rog':
        return 'Inauspicious (Ruled by Mars). Avoid health-related risks, arguments or disputes.';
      case 'Udveg':
        return 'Inauspicious (Ruled by Sun). Causes anxiety and friction. Prayers recommended.';
      default:
        return 'General Vedic muhurat period.';
    }
  }

  @override
  Widget build(BuildContext context) {
    final choghadiyaList = _generateChoghadiya(_isDay);
    final activePeriod = choghadiyaList.firstWhere(
      (e) => e['isActive'] == true,
      orElse: () => choghadiyaList.first,
    );

    final timeFmt = DateFormat('hh:mm a');

    return Scaffold(
      backgroundColor: const Color(0xFFF9F7F2),
      appBar: AppBar(
        title: const Text('Live Choghadiya & Rahu Kaal',
            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 17)),
        backgroundColor: const Color(0xFF1A132F),
        foregroundColor: Colors.white,
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _buildActiveMuhuratHero(activePeriod, timeFmt),
          const SizedBox(height: 16),
          _buildSpecialMuhuratsCard(),
          const SizedBox(height: 20),
          _buildDayNightToggle(),
          const SizedBox(height: 12),
          ...choghadiyaList.map((item) => _buildChoghadiyaRow(item, timeFmt)),
        ],
      ),
    );
  }

  Widget _buildActiveMuhuratHero(
      Map<String, dynamic> active, DateFormat timeFmt) {
    final isGood = active['isGood'] as bool;
    final isNeutral = active['isNeutral'] as bool;

    final badgeColor = isGood
        ? const Color(0xFF10B981)
        : (isNeutral ? const Color(0xFF38BDF8) : const Color(0xFFEF4444));

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            const Color(0xFF1A132F),
            badgeColor.withOpacity(0.35),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: badgeColor.withOpacity(0.5)),
        boxShadow: [
          BoxShadow(
            color: badgeColor.withOpacity(0.18),
            blurRadius: 14,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    width: 10,
                    height: 10,
                    decoration: BoxDecoration(
                      color: badgeColor,
                      shape: BoxShape.circle,
                    ),
                  ),
                  const SizedBox(width: 8),
                  const Text(
                    'CURRENT ACTIVE CHOGHADIYA',
                    style: TextStyle(
                      color: Colors.white70,
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 0.8,
                    ),
                  ),
                ],
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                decoration: BoxDecoration(
                  color: badgeColor.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: badgeColor),
                ),
                child: Text(
                  isGood ? 'AUSPICIOUS 🪔' : (isNeutral ? 'MODERATE' : 'AVOID TASKS'),
                  style: TextStyle(
                    color: badgeColor,
                    fontSize: 10.5,
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            '${active['name']} Choghadiya',
            style: const TextStyle(
              color: Colors.white,
              fontSize: 24,
              fontWeight: FontWeight.w900,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            '${timeFmt.format(active['start'] as DateTime)} – ${timeFmt.format(active['end'] as DateTime)}',
            style: const TextStyle(color: CustomerTheme.brandGold, fontSize: 13, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 10),
          Text(
            active['recommendation'] as String,
            style: const TextStyle(color: Colors.white70, fontSize: 12, height: 1.3),
          ),
        ],
      ),
    );
  }

  Widget _buildSpecialMuhuratsCard() {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: _buildMuhuratPill('Rahu Kaal', '01:30 PM - 03:00 PM', Colors.red.shade700, 'Inauspicious'),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: _buildMuhuratPill('Abhijit Muhurat', '11:45 AM - 12:35 PM', const Color(0xFF10B981), 'Most Auspicious'),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(
                child: _buildMuhuratPill('Yamaganda', '06:15 AM - 07:45 AM', Colors.orange.shade800, 'Caution'),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: _buildMuhuratPill('Gulika Kaal', '09:15 AM - 10:45 AM', Colors.blue.shade700, 'Neutral'),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildMuhuratPill(String title, String timing, Color color, String badge) {
    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: const Color(0xFFF9F7F2),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.black12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
              Text(badge, style: TextStyle(color: color, fontSize: 9.5, fontWeight: FontWeight.bold)),
            ],
          ),
          const SizedBox(height: 4),
          Text(timing, style: const TextStyle(fontSize: 11, color: Colors.black54)),
        ],
      ),
    );
  }

  Widget _buildDayNightToggle() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.grey.shade200,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        children: [
          Expanded(
            child: GestureDetector(
              onTap: () => setState(() => _isDay = true),
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 10),
                decoration: BoxDecoration(
                  color: _isDay ? const Color(0xFF1A132F) : Colors.transparent,
                  borderRadius: BorderRadius.circular(20),
                ),
                alignment: Alignment.center,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.wb_sunny_rounded, size: 16, color: _isDay ? CustomerTheme.brandGold : Colors.black54),
                    const SizedBox(width: 6),
                    Text(
                      'Day Choghadiya (दिन)',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 12.5,
                        color: _isDay ? Colors.white : Colors.black87,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          Expanded(
            child: GestureDetector(
              onTap: () => setState(() => _isDay = false),
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 10),
                decoration: BoxDecoration(
                  color: !_isDay ? const Color(0xFF1A132F) : Colors.transparent,
                  borderRadius: BorderRadius.circular(20),
                ),
                alignment: Alignment.center,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.nightlight_round, size: 16, color: !_isDay ? CustomerTheme.brandGold : Colors.black54),
                    const SizedBox(width: 6),
                    Text(
                      'Night Choghadiya (रात)',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 12.5,
                        color: !_isDay ? Colors.white : Colors.black87,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildChoghadiyaRow(Map<String, dynamic> item, DateFormat timeFmt) {
    final isGood = item['isGood'] as bool;
    final isNeutral = item['isNeutral'] as bool;
    final isActive = item['isActive'] as bool;

    final color = isGood
        ? const Color(0xFF10B981)
        : (isNeutral ? const Color(0xFF0284C7) : const Color(0xFFEF4444));

    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: isActive ? color.withOpacity(0.08) : Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: isActive ? color : Colors.black.withOpacity(0.06),
          width: isActive ? 1.5 : 1,
        ),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              Container(
                width: 10,
                height: 10,
                decoration: BoxDecoration(color: color, shape: BoxShape.circle),
              ),
              const SizedBox(width: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Text(
                        item['name'] as String,
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 14,
                          color: isActive ? color : const Color(0xFF1A132F),
                        ),
                      ),
                      if (isActive) ...[
                        const SizedBox(width: 6),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 1.5),
                          decoration: BoxDecoration(
                            color: color,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: const Text(
                            'NOW',
                            style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold),
                          ),
                        ),
                      ],
                    ],
                  ),
                  const SizedBox(height: 2),
                  Text(
                    '${timeFmt.format(item['start'] as DateTime)} - ${timeFmt.format(item['end'] as DateTime)}',
                    style: const TextStyle(fontSize: 11, color: Colors.black54),
                  ),
                ],
              ),
            ],
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
            decoration: BoxDecoration(
              color: color.withOpacity(0.12),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Text(
              isGood ? 'SHUBH' : (isNeutral ? 'MODERATE' : 'ASHUBH'),
              style: TextStyle(color: color, fontSize: 10, fontWeight: FontWeight.w800),
            ),
          ),
        ],
      ),
    );
  }
}
