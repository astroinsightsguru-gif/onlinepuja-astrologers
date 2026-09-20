import 'dart:convert';

import 'package:shared_preferences/shared_preferences.dart';

import '../models/system_flag.dart';
import '../models/user.dart';

/// Persists the auth token + user (same keys as the legacy apps so a device
/// upgraded from v1 stays logged in where possible).
class SessionStore {
  SessionStore._();
  static final SessionStore instance = SessionStore._();

  static const _kToken = 'token';
  static const _kTokenType = 'tokenType';
  static const _kUser = 'currentUser';
  static const _kFlags = 'systemFlags';

  SharedPreferences? _prefs;

  Future<SharedPreferences> get _sp async =>
      _prefs ??= await SharedPreferences.getInstance();

  String? token;
  String? tokenType;
  User? user;
  SystemFlags flags = SystemFlags.empty;

  bool get isLoggedIn => token != null && (user?.id ?? 0) > 0;

  Future<void> load() async {
    final sp = await _sp;
    token = sp.getString(_kToken);
    tokenType = sp.getString(_kTokenType) ?? 'Bearer';
    final raw = sp.getString(_kUser);
    if (raw != null) {
      try {
        user = User.fromJson(json.decode(raw) as Map<String, dynamic>);
      } catch (_) {
        user = null;
      }
    }
    final rawFlags = sp.getString(_kFlags);
    if (rawFlags != null) {
      try {
        final list = (json.decode(rawFlags) as List)
            .map((e) => SystemFlag.fromJson(e as Map<String, dynamic>));
        flags = SystemFlags(list);
      } catch (_) {}
    }
  }

  Future<void> saveSession({
    required String token,
    required String tokenType,
    required User user,
  }) async {
    this.token = token;
    this.tokenType = tokenType;
    this.user = user;
    final sp = await _sp;
    await sp.setString(_kToken, token);
    await sp.setString(_kTokenType, tokenType);
    await sp.setString(_kUser, json.encode(user.toJson()));
  }

  Future<void> saveUser(User user) async {
    this.user = user;
    final sp = await _sp;
    await sp.setString(_kUser, json.encode(user.toJson()));
  }

  Future<void> saveFlags(SystemFlags flags) async {
    this.flags = flags;
    final sp = await _sp;
    await sp.setString(_kFlags, json.encode(flags.toRaw()));
  }

  Future<void> clear() async {
    token = null;
    tokenType = null;
    user = null;
    final sp = await _sp;
    await sp.remove(_kToken);
    await sp.remove(_kTokenType);
    await sp.remove(_kUser);
  }
}
