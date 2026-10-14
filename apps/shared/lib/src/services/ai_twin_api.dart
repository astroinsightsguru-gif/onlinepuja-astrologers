import 'package:flutter/foundation.dart';
import '../api/api_client.dart';

/// Client API for the Agentic Swarm of Vedic AI Astrologers & Astrologer AI Twins
class AiTwinApi {
  AiTwinApi._();
  static final AiTwinApi instance = AiTwinApi._();

  final _api = ApiClient.instance;

  /// Fetch all active AI Astrologers & specialized swarm agents
  Future<List<Map<String, dynamic>>> fetchSwarmAgents() async {
    try {
      final res = await _api.get('/ai-astrologer-list');
      final list = res['recordList'];
      if (list is List) {
        return list.map((e) => Map<String, dynamic>.from(e as Map)).toList();
      }
      return const [];
    } catch (e) {
      debugPrint('fetchSwarmAgents error: $e');
      return const [];
    }
  }

  /// Chat with an AI Astrologer or an Astrologer's Personal AI Twin
  Future<Map<String, dynamic>> chat({
    required String message,
    int? aiAstrologerId,
    int? astrologerId,
    Map<String, dynamic>? context,
    String? language,
  }) async {
    final body = <String, dynamic>{
      'message': message,
    };
    if (aiAstrologerId != null) {
      body['ai_astrologer_id'] = aiAstrologerId;
    }
    if (astrologerId != null) {
      body['astrologer_id'] = astrologerId;
    }
    if (context != null) {
      body['context'] = context;
    }
    if (language != null) {
      body['language'] = language;
    }

    final res = await _api.post('/ai-astrologer/chat', body: body);
    final record = res['recordList'];
    if (record is Map<String, dynamic>) {
      return record;
    }
    return res;
  }

  /// Setup / configure an Astrologer's personal AI Twin
  Future<Map<String, dynamic>> setupAiTwin({
    required int astrologerId,
    bool? enabled,
    String? name,
    String? tone,
    String? greeting,
    String? prompt,
    String? system,
    double? charge,
    bool? autoOffline,
  }) async {
    final body = <String, dynamic>{
      'astrologer_id': astrologerId,
    };
    if (enabled != null) {
      body['ai_twin_enabled'] = enabled;
    }
    if (name != null) {
      body['ai_twin_name'] = name;
    }
    if (tone != null) {
      body['ai_twin_tone'] = tone;
    }
    if (greeting != null) {
      body['ai_twin_greeting'] = greeting;
    }
    if (prompt != null) {
      body['ai_twin_prompt'] = prompt;
    }
    if (system != null) {
      body['ai_twin_system'] = system;
    }
    if (charge != null) {
      body['ai_twin_charge'] = charge;
    }
    if (autoOffline != null) {
      body['ai_twin_auto_offline'] = autoOffline;
    }

    final res = await _api.post('/astrologer/ai-twin/setup', body: body);
    final record = res['recordList'];
    if (record is Map<String, dynamic>) {
      return record;
    }
    return res;
  }

  /// Fetch an Astrologer's personal AI Twin status and stats
  Future<Map<String, dynamic>> getAiTwinStatus({required int astrologerId}) async {
    final res = await _api.get('/astrologer/ai-twin/status', query: {
      'astrologer_id': astrologerId.toString(),
    });
    final record = res['recordList'];
    if (record is Map<String, dynamic>) {
      return record;
    }
    return res;
  }

  /// Astrologer prescribes a Vedic Puja / Remedy to a client
  Future<Map<String, dynamic>> prescribeRemedy({
    required int astrologerId,
    required int userId,
    String? title,
    String? remedyTitle,
    String remedyType = 'puja',
    int? pujaId,
    String? callId,
    String? reason,
    String? prescribedGemstone,
    String? gemstone,
    String? prescribedMantra,
    String? mantra,
    double? suggestedAmount,
  }) async {
    final effectiveTitle = title ?? remedyTitle ?? 'Vedic Remedy Prescription';
    final effectiveGem = prescribedGemstone ?? gemstone;
    final effectiveMantra = prescribedMantra ?? mantra;

    final body = <String, dynamic>{
      'astrologer_id': astrologerId,
      'user_id': userId,
      'title': effectiveTitle,
      'remedy_type': remedyType,
    };
    if (pujaId != null) {
      body['puja_id'] = pujaId;
    }
    if (callId != null) {
      body['call_id'] = callId;
    }
    if (reason != null && reason.isNotEmpty) {
      body['reason'] = reason;
    }
    if (effectiveGem != null && effectiveGem.isNotEmpty) {
      body['prescribed_gemstone'] = effectiveGem;
    }
    if (effectiveMantra != null && effectiveMantra.isNotEmpty) {
      body['prescribed_mantra'] = effectiveMantra;
    }
    if (suggestedAmount != null) {
      body['suggested_amount'] = suggestedAmount;
    }

    final res = await _api.post('/astrologer/prescribe', body: body);
    final record = res['recordList'];
    if (record is Map<String, dynamic>) {
      return record;
    }
    return res;
  }

  /// Fetch prescribed remedies for a customer or astrologer
  Future<List<Map<String, dynamic>>> fetchPrescriptions({
    int? userId,
    int? astrologerId,
  }) async {
    try {
      final query = <String, dynamic>{};
      if (userId != null) {
        query['user_id'] = userId.toString();
      }
      if (astrologerId != null) {
        query['astrologer_id'] = astrologerId.toString();
      }
      final res = await _api.get('/astrologer/prescriptions', query: query);
      final list = res['recordList'];
      if (list is List) {
        return list.map((e) => Map<String, dynamic>.from(e as Map)).toList();
      }
      return const [];
    } catch (e) {
      debugPrint('fetchPrescriptions error: $e');
      return const [];
    }
  }

  /// Fetch available active pujas for the astrologer's prescription picker
  Future<List<Map<String, dynamic>>> fetchAvailablePujas() async {
    try {
      final res = await _api.get('/astrologer/prescriptions/pujas');
      final list = res['recordList'];
      if (list is List) {
        return list.map((e) => Map<String, dynamic>.from(e as Map)).toList();
      }
      return const [];
    } catch (e) {
      debugPrint('fetchAvailablePujas error: $e');
      return const [];
    }
  }
}
