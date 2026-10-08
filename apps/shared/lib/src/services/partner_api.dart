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

  /// Flip chat/call availability via lightweight endpoint.
  Future<void> setStatus({
    required int astrologerId,
    String? chatStatus,
    String? callStatus,
  }) async {
    await _api.post('/astrologer/updateStatus', body: {
      'astrologerId': astrologerId,
      if (chatStatus != null) 'chatStatus': chatStatus,
      if (callStatus != null) 'callStatus': callStatus,
    });
  }

  /// Update astrologer consultation rates & emergency pricing.
  Future<Map<String, dynamic>?> updateRates({
    required int astrologerId,
    double? charge,
    double? audioCallRate,
    double? videoCallRate,
    double? reportRate,
    double? emergencyChatCharge,
    double? emergencyAudioCharge,
    double? emergencyVideoCharge,
    bool? emergencyChatStatus,
    bool? emergencyCallStatus,
  }) async {
    final decoded = await _api.post('/astrologer/updateRates', body: {
      'astrologerId': astrologerId,
      if (charge != null) 'charge': charge,
      if (audioCallRate != null) 'audioCallRate': audioCallRate,
      if (videoCallRate != null) 'videoCallRate': videoCallRate,
      if (reportRate != null) 'reportRate': reportRate,
      if (emergencyChatCharge != null)
        'emergency_chat_charge': emergencyChatCharge,
      if (emergencyAudioCharge != null)
        'emergency_audio_charge': emergencyAudioCharge,
      if (emergencyVideoCharge != null)
        'emergency_video_charge': emergencyVideoCharge,
      if (emergencyChatStatus != null)
        'emergencyChatStatus': emergencyChatStatus ? 1 : 0,
      if (emergencyCallStatus != null)
        'emergencyCallStatus': emergencyCallStatus ? 1 : 0,
    });
    if (decoded is Map<String, dynamic>) {
      final rl = decoded['recordList'];
      if (rl is Map<String, dynamic>) return rl;
    }
    return null;
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

  /// Fetch astrologer's assigned puja bookings and custom pujas.
  Future<List<Map<String, dynamic>>> astrologerPujaList({required int astrologerId}) async {
    final decoded = await _api.post('/astrologerPujaList', body: {
      'astrologerId': astrologerId,
    });
    return _list(_payload(decoded));
  }

  /// Fetch pending and delivered report consultation requests.
  Future<List<Map<String, dynamic>>> getUserReportRequests({required int astrologerId}) async {
    final decoded = await _api.post('/getUserReport', body: {
      'astrologerId': astrologerId,
    });
    return _list(_payload(decoded));
  }

  /// Submit astrologer report response (PDF / text).
  Future<void> submitUserReport({
    required int reportId,
    required String reportText,
    String? fileUrl,
  }) =>
      _api.post('/userreport/add', body: {
        'id': reportId,
        'report': reportText,
        'reportFile': fileUrl,
      });

  /// Update astrologer Bank and UPI payout KYC details.
  Future<Map<String, dynamic>> updateBankDetails({
    required int astrologerId,
    String? bankName,
    String? accountNumber,
    String? accountHolderName,
    String? ifscCode,
    String? bankBranch,
    String? accountType,
    String? upi,
    String? pancardNo,
    String? aadharNo,
  }) async {
    final decoded = await _api.post('/astrologer/updateBankDetails', body: {
      'astrologerId': astrologerId,
      if (bankName != null) 'bankName': bankName,
      if (accountNumber != null) 'accountNumber': accountNumber,
      if (accountHolderName != null) 'accountHolderName': accountHolderName,
      if (ifscCode != null) 'ifscCode': ifscCode,
      if (bankBranch != null) 'bankBranch': bankBranch,
      if (accountType != null) 'accountType': accountType,
      if (upi != null) 'upi': upi,
      if (pancardNo != null) 'pancardNo': pancardNo,
      if (aadharNo != null) 'aadharNo': aadharNo,
    });
    if (decoded is Map<String, dynamic>) {
      final rl = decoded['recordList'];
      if (rl is Map<String, dynamic>) return rl;
    }
    return const {};
  }

  /// Recommend a Vedic Puja / Remedy to a client during or after consultation.
  Future<bool> sendPujaToUser({
    required int astrologerId,
    required int userId,
    required int pujaId,
    String? sessionId,
  }) async {
    final decoded = await _api.post('/sendPujatoUser', body: {
      'astrologerId': astrologerId,
      'userId': userId,
      'puja_id': pujaId,
      if (sessionId != null) 'sessionId': sessionId,
    });
    if (decoded is Map<String, dynamic>) {
      return decoded['status'] == 200 || decoded['status'] == true;
    }
    return false;
  }
}
