import '../api/api_client.dart';
import '../models/puja.dart';

/// Puja (worship booking) endpoints mirroring the legacy Customer-app calls:
/// `/getPujaCategory`, `/getPujaList`, `/getPujaRecommend`, `/getPujafaq`,
/// `/getPujaRefund`, `/suggestedAstrologerPuja`, `/placedPujaOrder`.
class PujaApi {
  PujaApi._();
  static final PujaApi instance = PujaApi._();

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

  static Map<String, dynamic> _asMap(dynamic decoded) {
    if (decoded is Map<String, dynamic>) {
      final rl = decoded['recordList'];
      if (rl is Map<String, dynamic>) return rl;
      return decoded;
    }
    return const {};
  }

  /// Puja categories (legacy `getPujaCategory`).
  Future<List<PujaCategory>> categories() async {
    final decoded = await _api.post('/getPujaCategory');
    return _asMapList(decoded).map(PujaCategory.fromJson).toList();
  }

  /// Puja list, optionally filtered by category (legacy `getPujaList`).
  Future<List<Puja>> list({dynamic categoryId, int? userId}) async {
    final decoded = await _api.post('/getPujaList', body: {
      'categoryId': categoryId,
      'userId': userId,
    });
    return _asMapList(decoded).map(Puja.fromJson).toList();
  }

  /// Pujas recommended for a user (legacy `getPujaRecommend`).
  Future<List<Puja>> recommended({int? userId}) async {
    final decoded = await _api.post('/getPujaRecommend', body: {
      'userId': userId,
    });
    return _asMapList(decoded).map(Puja.fromJson).toList();
  }

  /// Pujas suggested by an astrologer (legacy `suggestedAstrologerPuja`).
  Future<List<Puja>> suggestedByAstrologer({
    required int astrologerId,
    int? userId,
  }) async {
    final decoded = await _api.post('/suggestedAstrologerPuja', body: {
      'astrologerId': astrologerId,
      'userId': userId,
    });
    return _asMapList(decoded).map(Puja.fromJson).toList();
  }

  /// FAQ list attached to a puja (legacy `getPujafaq`).
  Future<List<Map<String, dynamic>>> faqs({dynamic pujaId}) async {
    final decoded = await _api.post('/getPujafaq', body: {'pujaId': pujaId});
    return _asMapList(decoded);
  }

  /// Refund / cancellation policy content (legacy `getPujaRefund`).
  Future<Map<String, dynamic>> refundPolicy() async {
    final decoded = await _api.post('/getPujaRefund');
    return _asMap(decoded);
  }

  /// Place a puja order (legacy `placedPujaOrder`).
  Future<Map<String, dynamic>> placeOrder({
    required int userId,
    required dynamic pujaId,
    required dynamic packageId,
    Map<String, dynamic>? extra,
  }) async {
    final decoded = await _api.post('/placedPujaOrder', body: {
      'userId': userId,
      'pujaId': pujaId,
      'packageId': packageId,
      ...?extra,
    });
    return _asMap(decoded);
  }

  /// A single puja by id — falls back to filtering the list response since
  /// the legacy backend has no dedicated detail endpoint.
  Future<Puja?> byId({required dynamic pujaId}) async {
    final all = await list();
    for (final p in all) {
      if (p.id.toString() == pujaId.toString()) return p;
    }
    return null;
  }
}
