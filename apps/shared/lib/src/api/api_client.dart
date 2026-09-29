import 'dart:convert';
import 'package:http/http.dart' as http;

import '../env.dart';

/// Thrown for any non-2xx API response or network failure.
class ApiException implements Exception {
  ApiException(this.message, {this.statusCode, this.errors});

  final String message;
  final int? statusCode;

  /// Field errors returned by the backend (`{"error": {...}}` on 400).
  final Map<String, dynamic>? errors;

  @override
  String toString() => message;
}

/// Uniform API result: mirrors the legacy `getAPIResult` pattern where every
/// endpoint responds with `{"status": 200/400, "recordList": ...}`.
class ApiResult<T> {
  ApiResult({required this.status, this.recordList, this.body});

  final int status;
  final T? recordList;

  /// Raw decoded response for endpoints that return extra keys
  /// (e.g. login: `token`, `token_type`).
  final Map<String, dynamic>? body;

  bool get isOk => status == 200;
}

/// Thin HTTP client on top of package:http with the legacy auth-header
/// convention (`Authorization: <token_type> <token>` from SharedPreferences).
class ApiClient {
  ApiClient._();
  static final ApiClient instance = ApiClient._();

  String? Function() tokenResolver = () => null;
  String? Function() tokenTypeResolver = () => null;

  Map<String, String> headers({bool auth = true}) {
    final h = <String, String>{
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    if (auth) {
      final token = tokenResolver();
      if (token != null && token.isNotEmpty) {
        final type = tokenTypeResolver() ?? 'Bearer';
        h['Authorization'] = '$type $token';
      }
    }
    return h;
  }

  /// POST and decode JSON; throws [ApiException] on failure.
  Future<dynamic> post(
    String path, {
    Map<String, dynamic>? body,
    bool auth = true,
  }) async {
    final url = path.startsWith('http') ? path : '${Env.apiBase}$path';
    final http.Response response;
    try {
      response = await http
          .post(Uri.parse(url), headers: headers(auth: auth),
              body: body == null ? null : json.encode(body))
          .timeout(const Duration(seconds: 30));
    } catch (e) {
      throw ApiException('Network error, please try again.', errors: {'_': e.toString()});
    }
    return _handle(response);
  }

  /// GET and decode JSON; throws [ApiException] on failure.
  Future<dynamic> get(String path, {bool auth = true}) async {
    final url = path.startsWith('http') ? path : '${Env.apiBase}$path';
    final http.Response response;
    try {
      response = await http
          .get(Uri.parse(url), headers: headers(auth: auth))
          .timeout(const Duration(seconds: 30));
    } catch (e) {
      throw ApiException('Network error, please try again.', errors: {'_': e.toString()});
    }
    return _handle(response);
  }

  dynamic _handle(http.Response response) {
    dynamic decoded;
    if (response.body.isNotEmpty) {
      try {
        decoded = json.decode(response.body);
      } catch (_) {
        decoded = response.body;
      }
    }
    final int effectiveStatus = (decoded is Map<String, dynamic> && decoded['status'] is int)
        ? (decoded['status'] as int)
        : response.statusCode;

    if (response.statusCode >= 400 || effectiveStatus >= 400) {
      Map<String, dynamic>? errors;
      var msg = 'Request failed ($effectiveStatus)';
      if (decoded is Map<String, dynamic>) {
        final err = decoded['error'];
        final rl = decoded['recordList'];
        if (err is Map<String, dynamic>) {
          errors = err;
          msg = err.values
              .whereType<List>()
              .map((l) => l.join(', '))
              .where((s) => s.isNotEmpty)
              .join('\n');
          if (msg.isEmpty) msg = err.values.join(', ');
        } else if (err is String && err.isNotEmpty) {
          msg = err;
        } else if (decoded['message'] is String && decoded['message'].toString().isNotEmpty) {
          msg = decoded['message'].toString();
        } else if (rl is Map && rl['message'] is String && rl['message'].toString().isNotEmpty) {
          msg = rl['message'].toString();
        }
      }
      throw ApiException(msg, statusCode: effectiveStatus, errors: errors);
    }
    return decoded;
  }
}
