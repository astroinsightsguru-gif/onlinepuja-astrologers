import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// App-wide session state for the partner (astrologer) app.
///
/// Mirrors the customer `AppSession` but authenticates against the
/// astrologer login endpoints (`loginAppAstrologer`), so one JWT flows
/// through the same backend.
class PartnerSession extends ChangeNotifier {
  User? user;
  SystemFlags flags = SystemFlags.empty;
  bool booted = false;

  bool get isLoggedIn => SessionStore.instance.isLoggedIn;
  String get myId => (user?.id ?? 0).toString();
  int get astrologerId => user?.id ?? 0;

  ThemeMode get themeMode => SessionStore.instance.themeMode;

  Future<void> setThemeMode(ThemeMode mode) async {
    await SessionStore.instance.saveThemeMode(mode);
    notifyListeners();
  }

  /// Availability state surfaced in the home shell (legacy default online).
  String chatStatus = 'Online';
  String callStatus = 'Online';

  Future<void> init() async {
    ApiClient.instance.tokenResolver = () => SessionStore.instance.token;
    ApiClient.instance.tokenTypeResolver =
        () => SessionStore.instance.tokenType;
    await SessionStore.instance.load();
    user = SessionStore.instance.user;
    flags = SessionStore.instance.flags;
    chatStatus = user?.chatStatus ?? 'Online';
    callStatus = user?.callStatus ?? 'Online';
    booted = true;
    notifyListeners();
  }

  /// Ask backend to SMS the OTP (legacy `checkContactNoExist`).
  Future<void> sendOtp({
    required String contactNo,
    required String countryCode,
  }) =>
      AuthApi.instance.sendOtp(
        contactNo: contactNo,
        countryCode: countryCode,
        isPartner: true,
      );

  /// Login / auto-register (legacy `loginAppAstrologer`).
  Future<void> login({
    required String contactNo,
    required String countryCode,
    String? name,
    String? fcmToken,
  }) async {
    user = await AuthApi.instance.login(
      contactNo: contactNo,
      countryCode: countryCode,
      isPartner: true,
      name: name,
      fcmToken: fcmToken,
    );
    flags = SessionStore.instance.flags;
    chatStatus = user?.chatStatus ?? 'Online';
    callStatus = user?.callStatus ?? 'Online';
    notifyListeners();
  }

  /// Toggle chat/call availability via `astrologer/update`.
  Future<void> setStatus({
    String? chat,
    String? call,
  }) async {
    await PartnerApi.instance.setStatus(
      astrologerId: astrologerId,
      chatStatus: chat,
      callStatus: call,
    );
    chatStatus = chat ?? chatStatus;
    callStatus = call ?? callStatus;
    notifyListeners();
  }

  /// Refresh profile + wallet.
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
