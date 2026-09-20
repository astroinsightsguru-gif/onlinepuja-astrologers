import '../api/api_client.dart';
import '../models/user.dart';

/// Partner (astrologer) side endpoints: availability status and the
/// incoming chat/call request inbox. Contracts mirror the legacy
/// `Astrologer-app/lib/services/apiHelper.dart`.
class PartnerApi {
  PartnerApi._();
  static final PartnerApi instance = PartnerApi._();

  final _api = ApiClient.instance;

  static List<Map<String, dynamic>> _list(dynamic decoded) {
    if (decoded is Map<String, dynamic>) {
      final rl = decoded['recordList'];
      if (rl is List) return rl.whereType<Map<String, dynamic>>().toList();
    }
    if (decoded is List) {
      return decoded.whereType<Map<String, dynamic>>().toList();
    }
    return const [];
  }

  static dynamic _payload(dynamic decoded) {
    if (decoded is Map<String, dynamic>) return decoded['recordList'];
    return null;
  }

  /// Flip chat/call availability (legacy `astrologer/update` with the
  /// astrologer profile payload). Only the changed fields are sent.
  Future<void> setStatus({
    required int astrologerId,
    String? chatStatus,
    String? callStatus,
  }) async {
    await _api.post('/astrologer/update', body: {
      'id': astrologerId,
      'chatStatus': ?chatStatus,
      'callStatus': ?callStatus,
    });
  }

  /// Incoming chat requests (legacy `chatRequest/get`).
  Future<List<Map<String, dynamic>>> chatRequests({
    required int astrologerId,
    int startIndex = 0,
  }) async {
    final decoded = await _api.post('/chatRequest/get', body: {
      'astrologerId': astrologerId,
      'startIndex': startIndex,
      'fetchRecord': 50,
    });
    return _list(_payload(decoded));
  }

  /// Incoming call requests (legacy `callRequest/get`).
  Future<List<Map<String, dynamic>>> callRequests({
    required int astrologerId,
    int startIndex = 0,
  }) async {
    final decoded = await _api.post('/callRequest/get', body: {
      'astrologerId': astrologerId,
      'startIndex': startIndex,
      'fetchRecord': 50,
    });
    return _list(_payload(decoded));
  }

  Future<void> acceptChatRequest(int chatId) =>
      _api.post('/chatRequest/acceptChatRequest', body: {'chatId': chatId});

  Future<void> rejectChatRequest(int chatId) =>
      _api.post('/chatRequest/rejectChatRequest', body: {'chatId': chatId});

  Future<void> acceptCallRequest(int callId) =>
      _api.post('/callRequest/acceptCallRequest', body: {'callId': callId});

  Future<void> rejectCallRequest(int callId) =>
      _api.post('/callRequest/rejectCallRequest', body: {'callId': callId});

  /// Partner ends a chat session.
  Future<void> endChat(int chatId) =>
      _api.post('/chatRequest/endChat', body: {'chatId': chatId});

  /// Partner ends a call session.
  Future<void> endCall(int callId) =>
      _api.post('/callRequest/end', body: {'callId': callId});

  /// Update partner profile (legacy `astrologer/update` with full payload).
  Future<void> updateProfile(User user) =>
      _api.post('/astrologer/update', body: user.toJson());

  /// Partner sign-up (legacy `partner/register`). Throws [ApiException] with
  /// field `errors` on validation failure (422) or duplicate email (409).
  Future<void> register({
    required String name,
    required String email,
    required String phone,
    required String password,
    required String businessName,
    required String businessAddress,
  }) =>
      _api.post('/partner/register', body: {
        'name': name,
        'email': email,
        'phone': phone,
        'password': password,
        'password_confirmation': password,
        'business_name': businessName,
        'business_address': businessAddress,
      }, auth: false);
}
