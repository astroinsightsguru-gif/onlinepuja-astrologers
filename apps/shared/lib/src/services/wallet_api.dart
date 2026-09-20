import '../api/api_client.dart';

/// Wallet / payment endpoints. Contracts mirror the legacy apps
/// (`getRechargeAmount`, `addpayment`, `withdrawlmethod/get`).
class WalletApi {
  WalletApi._();
  static final WalletApi instance = WalletApi._();

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

  /// Recharge plans for the customer wallet (legacy `getRechargeAmount`).
  /// Each row typically: {id, amount, ...promotional fields}.
  Future<List<Map<String, dynamic>>> rechargePlans() async {
    final decoded = await _api.post('/getRechargeAmount');
    return _list(decoded);
  }

  /// Record a successful payment (legacy `addpayment`). The web payment
  /// gateway (Razorpay etc.) calls back to the backend; the app calls this
  /// after the gateway confirms the transaction client-side.
  Future<void> addPayment({
    required double amount,
    String? txnId,
    String paymentGateway = 'razorpay',
    Map<String, dynamic>? extra,
  }) async {
    await _api.post('/addpayment', body: {
      'amount': amount,
      'txn_id': txnId,
      'payment_gateway': paymentGateway,
      ...?extra,
    });
  }

  /// Partner withdrawal options (legacy `withdrawlmethod/get`).
  Future<List<Map<String, dynamic>>> withdrawOptions() async {
    final decoded = await _api.post('/withdrawlmethod/get');
    return _list(decoded);
  }
}
