import '../api/api_client.dart';
import '../models/system_flag.dart';
import '../models/user.dart';
import 'session_store.dart';

/// Authentication endpoints shared by both apps.
///
/// Customer: `loginAppUser` | Partner: `loginAppAstrologer` (legacy contract).
class AuthApi {
  AuthApi._();
  static final AuthApi instance = AuthApi._();

  final _api = ApiClient.instance;

  /// 1) Check whether the contact already exists (sends the OTP server-side).
  /// Legacy: `checkContactNoExistForUser` / `checkContactNoExist`.
  Future<String?> sendOtp({
    required String contactNo,
    required String countryCode,
    required bool isPartner,
  }) async {
    // Both customer and partner login OTPs are handled by checkContactNoExistForUser.
    // checkContactNoExist is the registration existence validator (which returns 400 Already Register).
    const path = '/checkContactNoExistForUser';
    final cleanCountryCode = countryCode.replaceAll('+', '').trim();
    final decoded = await _api.post(path, auth: false, body: {
      'contactNo': contactNo,
      'countryCode': cleanCountryCode.isEmpty ? '91' : cleanCountryCode,
      'fromApp': isPartner ? 'astrologer' : 'user',
      'type': 'login',
    });
    if (decoded is Map<String, dynamic>) {
      final status = decoded['status'] ?? 200;
      if (status != 200 && status != '200') {
        throw ApiException(
            decoded['message']?.toString() ?? 'Failed to send OTP.');
      }
      return decoded['otp']?.toString();
    }
    return null;
  }

  /// 2) Login / auto-register. Returns the persisted session user.
  /// Response shape:
  /// - Customer: `{status, token, token_type, recordList: {recordList: user}}`
  /// - Partner: `{status, token, token_type, recordList: [user]}`
  Future<User> login({
    required String contactNo,
    required String countryCode,
    required bool isPartner,
    String? name,
    String? email,
    String? fcmToken,
    String? referralCode,
  }) async {
    final path = isPartner ? '/loginAppAstrologer' : '/loginAppUser';
    final decoded = await _api.post(path, auth: false, body: {
      'contactNo': contactNo,
      'countryCode': countryCode,
      'fromApp': '1',
      if (name != null && name.isNotEmpty) ...{
        'username': name,
        'name': name,
      },
      if (email != null && email.isNotEmpty) 'email': email,
      if (fcmToken != null) 'deviceInfo': {'fcmToken': fcmToken},
      if (referralCode != null && referralCode.isNotEmpty)
        'referalCode': referralCode,
    });
    if (decoded is! Map<String, dynamic>) {
      throw ApiException('Unexpected server response.');
    }
    final status = decoded['status'] ?? 200;
    if (status != 200 && status != '200') {
      throw ApiException(decoded['message']?.toString() ?? 'Login failed.');
    }

    final payload = decoded['recordList'];
    final userJson = payload is List && payload.isNotEmpty
        ? (payload.first is Map<String, dynamic>
            ? payload.first as Map<String, dynamic>
            : <String, dynamic>{})
        : (payload is Map<String, dynamic>
            ? (payload['recordList'] is Map<String, dynamic>
                ? payload['recordList'] as Map<String, dynamic>
                : payload)
            : <String, dynamic>{});
    var user = User.fromJson(userJson);

    final token = (decoded['token'] ??
            (payload is Map<String, dynamic> ? payload['token'] : null) ??
            '')
        .toString();
    final tokenType = (decoded['token_type'] ??
            (payload is Map<String, dynamic> ? payload['token_type'] : null) ??
            'Bearer')
        .toString();

    await SessionStore.instance
        .saveSession(token: token, tokenType: tokenType, user: user);

    // If user record has an id, hydrate full profile
    final uid = user.id;
    if (uid != null && uid > 0 && !isPartner) {
      try {
        user = await getUser(uid);
      } catch (_) {}
    }

    // System flags ride along with the login response (legacy behavior) —
    // fall back to the flags map on the user object.
    final rawFlags = decoded['systemFlag'] ??
        userJson['systemFlag'] ??
        (payload is Map<String, dynamic> ? payload['systemFlag'] : null);
    if (rawFlags is List) {
      await SessionStore.instance.saveFlags(SystemFlags(rawFlags.map((e) =>
          SystemFlag.fromJson(e is Map<String, dynamic> ? e : const {}))));
    }
    return user;
  }

  /// Profile refresh (`getUserById`).
  Future<User> getUser(int userId) async {
    final decoded = await _api.post('/getUserById',
        body: {'userId': userId});
    final list = decoded is Map<String, dynamic>
        ? decoded['recordList']
        : null;
    if (list is List && list.isNotEmpty && list.first is Map<String, dynamic>) {
      final user = User.fromJson(list.first);
      await SessionStore.instance.saveUser(user);
      return user;
    }
    throw ApiException('Profile not found.');
  }

  Future<void> logout() async {
    try {
      await _api.post('/logout');
    } catch (_) {/* server-side best effort */}
    await SessionStore.instance.clear();
  }
}
