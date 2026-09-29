import '../api/api_client.dart';
import '../models/horoscope.dart';
import '../models/panchang.dart';

/// Horoscope + Panchang endpoints (legacy `/getHororscopeSign`,
/// `/getDailyHoroscope`, `/get/panchang`).
class HoroscopeApi {
  HoroscopeApi._();
  static final HoroscopeApi instance = HoroscopeApi._();

  final _api = ApiClient.instance;

  static List<Map<String, dynamic>> _asMapList(dynamic decoded) {
    if (decoded is Map<String, dynamic>) {
      final rl = decoded['recordList'];
      if (rl is List) {
        return rl.whereType<Map<String, dynamic>>().toList();
      }
    }
    if (decoded is List) {
      return decoded.whereType<Map<String, dynamic>>().toList();
    }
    return const [];
  }

  /// Zodiac signs (legacy `getHororscopeSign`).
  Future<List<HoroscopeSign>> signs() async {
    final decoded = await _api.post('/getHororscopeSign');
    return _asMapList(decoded).map(HoroscopeSign.fromJson).toList();
  }

  /// Daily, weekly, or yearly horoscope for a sign.
  Future<List<DailyHoroscope>> daily({
    int? signId,
    String type = 'today',
  }) async {
    final decoded = await _api.post('/getDailyHoroscope', body: {
      'horoscopeSignId': ?signId,
      'horoscopeType': type,
    });
    if (decoded is Map<String, dynamic>) {
      final vedic = decoded['vedicList'];
      if (vedic is Map<String, dynamic>) {
        final listKey = type.toLowerCase() == 'weekly'
            ? 'weeklyHoroScope'
            : (type.toLowerCase() == 'yearly'
                ? 'yearlyHoroScope'
                : 'todayHoroscope');
        final items = vedic[listKey];
        if (items is List && items.isNotEmpty) {
          return items
              .whereType<Map<String, dynamic>>()
              .map(DailyHoroscope.fromJson)
              .toList();
        }
      }
      return _asMapList(decoded).map(DailyHoroscope.fromJson).toList();
    }
    return const [];
  }

  /// Today's panchang for a location (legacy `get/panchang`).
  Future<Panchang> panchang({
    required double lat,
    required double lng,
    String? placeId,
  }) async {
    final decoded = await _api.post('/get/panchang', body: {
      'lat': lat,
      'lon': lng,
      'placeId': placeId,
    });
    if (decoded is Map<String, dynamic>) {
      final rl = decoded['recordList'];
      if (rl is Map<String, dynamic>) return Panchang.fromJson(rl);
      return Panchang.fromJson(decoded);
    }
    return Panchang();
  }
}
