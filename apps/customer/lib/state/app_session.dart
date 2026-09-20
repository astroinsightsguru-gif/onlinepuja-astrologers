import 'package:flutter/foundation.dart';
import 'package:op_shared/op_shared.dart';

/// App-wide session state for the customer app.
class AppSession extends ChangeNotifier {
  User? user;
  SystemFlags flags = SystemFlags.empty;
  bool booted = false;

  bool get isLoggedIn => SessionStore.instance.isLoggedIn;
  String get myId => (user?.id ?? 0).toString();

  Future<void> init() async {
    ApiClient.instance.tokenResolver = () => SessionStore.instance.token;
    ApiClient.instance.tokenTypeResolver =
        () => SessionStore.instance.tokenType;
    await SessionStore.instance.load();
    user = SessionStore.instance.user;
    flags = SessionStore.instance.flags;
    booted = true;
    notifyListeners();
  }

  /// Ask backend to SMS the OTP (legacy `checkContactNoExistForUser`).
  Future<void> sendOtp({
    required String contactNo,
    required String countryCode,
  }) =>
      AuthApi.instance.sendOtp(
        contactNo: contactNo,
        countryCode: countryCode,
        isPartner: false,
      );

  /// Login / auto-register (legacy `loginAppUser`). Saves token + user.
  Future<void> login({
    required String contactNo,
    required String countryCode,
    String? name,
    String? fcmToken,
  }) async {
    user = await AuthApi.instance.login(
      contactNo: contactNo,
      countryCode: countryCode,
      isPartner: false,
      name: name,
      fcmToken: fcmToken,
    );
    flags = SessionStore.instance.flags;
    notifyListeners();
  }

  /// Refresh profile + wallet from `getUserById`.
  Future<void> refreshUser() async {
    final id = user?.id;
    if (id == null || id == 0) return;
    try {
      user = await AuthApi.instance.getUser(id);
      notifyListeners();
    } on ApiException catch (e) {
      debugPrint('refreshUser failed: $e');
    }
  }

  Future<void> logout() async {
    await AuthApi.instance.logout();
    user = null;
    notifyListeners();
  }
}
