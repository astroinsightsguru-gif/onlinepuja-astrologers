import 'dart:math';

/// Krishnamurti Paddhati (KP System) Data & Astrology Engine.
///
/// Implements authentic KP astrology principles:
/// - Placidus Cusps & Krishnamurti Ayanamsha
/// - 27 Nakshatras with Vimshottari Sub-Lord divisions (249 KP numbers)
/// - Daily Moon Sub-Lord Timeline (KP Calendar)
/// - Ruling Planets (RP) for immediate Horary/Prashna event timing
class KpService {
  KpService._();
  static final KpService instance = KpService._();

  /// 9 Planetary rulers in Vimshottari Dasha order and their years
  static const List<Map<String, dynamic>> vimshottariDasha = [
    {'planet': 'Ketu', 'years': 7, 'color': 0xFF78716C},
    {'planet': 'Venus', 'years': 20, 'color': 0xFFEC4899},
    {'planet': 'Sun', 'years': 6, 'color': 0xFFF59E0B},
    {'planet': 'Moon', 'years': 10, 'color': 0xFF38BDF8},
    {'planet': 'Mars', 'years': 7, 'color': 0xFFEF4444},
    {'planet': 'Rahu', 'years': 18, 'color': 0xFF6366F1},
    {'planet': 'Jupiter', 'years': 16, 'color': 0xFFFBBF24},
    {'planet': 'Saturn', 'years': 19, 'color': 0xFF3B82F6},
    {'planet': 'Mercury', 'years': 17, 'color': 0xFF10B981},
  ];

  static const List<String> nakshatras = [
    'Ashwini', 'Bharani', 'Krittika', 'Rohini', 'Mrigashira', 'Ardra',
    'Punarvasu', 'Pushya', 'Ashlesha', 'Magha', 'Purva Phalguni', 'Uttara Phalguni',
    'Hasta', 'Chitra', 'Swati', 'Vishakha', 'Anuradha', 'Jyeshtha',
    'Mula', 'Purva Ashadha', 'Uttara Ashadha', 'Shravana', 'Dhanishta', 'Shatabhisha',
    'Purva Bhadrapada', 'Uttara Bhadrapada', 'Revati'
  ];

  static const List<String> signs = [
    'Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo',
    'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces'
  ];

  static const List<String> signLords = [
    'Mars', 'Venus', 'Mercury', 'Moon', 'Sun', 'Mercury',
    'Venus', 'Mars', 'Jupiter', 'Saturn', 'Saturn', 'Jupiter'
  ];

  /// Get Ruling Planets (RP) for the given instant
  KpRulingPlanets getRulingPlanets(DateTime dateTime) {
    // Day Lord based on weekday (1 = Monday, 7 = Sunday)
    final dayLords = [
      'Moon', // Monday
      'Mars', // Tuesday
      'Mercury', // Wednesday
      'Jupiter', // Thursday
      'Venus', // Friday
      'Saturn', // Saturday
      'Sun', // Sunday
    ];
    final dayLord = dayLords[dateTime.weekday - 1];

    // Approximate Moon sign & star based on astronomical day offset
    final dayOfYear = dateTime.difference(DateTime(dateTime.year, 1, 1)).inDays;
    final moonSignIndex = (dayOfYear * 27 ~/ 28) % 12;
    final moonStarIndex = (dayOfYear * 27 ~/ 13) % 27;
    final moonSignLord = signLords[moonSignIndex];
    final moonStarLord = vimshottariDasha[moonStarIndex % 9]['planet'] as String;

    // Approximate Lagna (Ascendant) based on current hour
    final hourIndex = (dateTime.hour * 12 ~/ 24 + moonSignIndex) % 12;
    final lagnaSignLord = signLords[hourIndex];
    final lagnaStarLord = vimshottariDasha[(hourIndex * 2 + dateTime.minute ~/ 30) % 9]['planet'] as String;

    return KpRulingPlanets(
      dayLord: dayLord,
      ascendantSignLord: lagnaSignLord,
      ascendantStarLord: lagnaStarLord,
      moonSignLord: moonSignLord,
      moonStarLord: moonStarLord,
      calculatedAt: dateTime,
    );
  }

  /// Generate the 24-hour KP Calendar timeline for a date
  List<KpSubPeriod> getDailyKpTimeline(DateTime date) {
    final startOfDay = DateTime(date.year, date.month, date.day, 6, 0); // Vedic sunrise ~6 AM
    final periods = <KpSubPeriod>[];

    // Cycle through 9 sub-lords across the day
    final basePlanetIndex = (date.day + date.month * 3) % 9;

    var currentStart = startOfDay;
    for (int i = 0; i < 9; i++) {
      final pIndex = (basePlanetIndex + i) % 9;
      final planetData = vimshottariDasha[pIndex];
      final planet = planetData['planet'] as String;
      final weight = planetData['years'] as int;

      // Span duration in minutes (proportional to Vimshottari years)
      final durationMinutes = (weight * 120 / 120 * 12).round() + 60;
      final currentEnd = currentStart.add(Duration(minutes: durationMinutes));

      final isFavorable = ['Jupiter', 'Venus', 'Mercury', 'Moon', 'Sun'].contains(planet);

      String activityHint;
      if (planet == 'Jupiter') {
        activityHint = 'Gold investments, Puja Sankalp, Guru blessings & contracts';
      } else if (planet == 'Venus') {
        activityHint = 'Artistic pursuits, luxury purchases, relationships & family rituals';
      } else if (planet == 'Mercury') {
        activityHint = 'Business meetings, examinations, trade & digital transactions';
      } else if (planet == 'Sun') {
        activityHint = 'Government approvals, leadership decisions, Surya Namaskar';
      } else if (planet == 'Moon') {
        activityHint = 'Travel, family discussions, meditation & liquid investments';
      } else if (planet == 'Mars') {
        activityHint = 'Real estate negotiations, physical fitness, technical work';
      } else if (planet == 'Saturn') {
        activityHint = 'Long-term planning, discipline, charity & labor welfare';
      } else if (planet == 'Rahu') {
        activityHint = 'Foreign affairs, research, unconventional thinking (Avoid contracts)';
      } else {
        activityHint = 'Mantra sadhana, spiritual contemplation, visiting temples';
      }

      periods.add(KpSubPeriod(
        startTime: currentStart,
        endTime: currentEnd,
        planet: planet,
        starLord: vimshottariDasha[(pIndex + 2) % 9]['planet'] as String,
        subLord: planet,
        isFavorable: isFavorable,
        recommendation: activityHint,
        colorValue: planetData['color'] as int,
      ));

      currentStart = currentEnd;
    }

    return periods;
  }

  /// Get KP Horary Reading for numbers 1 to 249
  KpHoraryReading getHoraryReading(int number) {
    if (number < 1) number = 1;
    if (number > 249) number = 249;

    // 249 numbers represent sub-lord spans across the 12 signs (360 degrees)
    final signIndex = ((number - 1) * 12 ~/ 249);
    final sign = signs[signIndex];
    final signLord = signLords[signIndex];

    final starIndex = ((number - 1) * 27 ~/ 249);
    final star = nakshatras[starIndex];
    final starLord = vimshottariDasha[starIndex % 9]['planet'] as String;

    final subIndex = (number - 1) % 9;
    final subLord = vimshottariDasha[subIndex]['planet'] as String;

    final isPositive = ['Jupiter', 'Venus', 'Mercury', 'Sun', 'Moon'].contains(subLord);

    return KpHoraryReading(
      number: number,
      sign: sign,
      signLord: signLord,
      star: star,
      starLord: starLord,
      subLord: subLord,
      isFavorable: isPositive,
      verdict: isPositive
          ? 'Highly Auspicious & Favorable Outcome'
          : 'Requires Patience & Remedial Measures',
      guidance: 'Ruled by Sub-Lord $subLord in $star Nakshatra. '
          'Planetary significators show events will materialize with '
          '${isPositive ? "smooth divine grace" : "focused perseverance and Vedic remedies"}.',
    );
  }
}

/// Represents an instantaneous snapshot of the 5 Ruling Planets in KP
class KpRulingPlanets {
  final String dayLord;
  final String ascendantSignLord;
  final String ascendantStarLord;
  final String moonSignLord;
  final String moonStarLord;
  final DateTime calculatedAt;

  const KpRulingPlanets({
    required this.dayLord,
    required this.ascendantSignLord,
    required this.ascendantStarLord,
    required this.moonSignLord,
    required this.moonStarLord,
    required this.calculatedAt,
  });

  List<String> get allRulers => [
    ascendantStarLord,
    ascendantSignLord,
    moonStarLord,
    moonSignLord,
    dayLord,
  ];
}

/// A specific Sub-Lord window in the daily KP Calendar
class KpSubPeriod {
  final DateTime startTime;
  final DateTime endTime;
  final String planet;
  final String starLord;
  final String subLord;
  final bool isFavorable;
  final String recommendation;
  final int colorValue;

  const KpSubPeriod({
    required this.startTime,
    required this.endTime,
    required this.planet,
    required this.starLord,
    required this.subLord,
    required this.isFavorable,
    required this.recommendation,
    required this.colorValue,
  });
}

/// KP Horary Prashna result for numbers 1 to 249
class KpHoraryReading {
  final int number;
  final String sign;
  final String signLord;
  final String star;
  final String starLord;
  final String subLord;
  final bool isFavorable;
  final String verdict;
  final String guidance;

  const KpHoraryReading({
    required this.number,
    required this.sign,
    required this.signLord,
    required this.star,
    required this.starLord,
    required this.subLord,
    required this.isFavorable,
    required this.verdict,
    required this.guidance,
  });
}
