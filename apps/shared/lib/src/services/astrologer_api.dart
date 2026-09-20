import '../api/api_client.dart';
import '../env.dart';
import '../models/astrologer.dart';
import '../models/chat_message.dart';
import '../models/skill.dart';

/// LiveKit join bundle returned by `POST /api/livekit/token`.
class LiveKitSession {
  LiveKitSession({
    required this.token,
    required this.wsUrl,
    required this.room,
    required this.identity,
  });

  final String token;
  final String wsUrl;
  final String room;
  final String identity;
}

/// Astrologer discovery + consultation-session endpoints.
class AstrologerApi {
  AstrologerApi._();
  static final AstrologerApi instance = AstrologerApi._();

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

  /// Astrologer list with filters/sort (legacy `getAstrologer` contract).
  Future<List<Astrologer>> list({
    int? userId,
    String sortBy = 'experience',
    String? categoryId,
    List<String> skills = const [],
    String? gender,
    String? language,
    int startIndex = 0,
  }) async {
    final decoded = await _api.post('/getAstrologer', body: {
      'userId': userId,
      'fetchRecord': 100,
      'astrologerCategoryId': categoryId,
      'filterData': {
        'skills': skills,
        'languageKnown': <String>[?language],
        'gender': gender,
      },
      'sortBy': sortBy,
      'startIndex': startIndex,
      'inRandom': false,
    });
    return _asMapList(decoded).map(Astrologer.fromJson).toList();
  }

  /// Single astrologer for a customer (legacy `getAstrologerForCustomer`).
  Future<Astrologer> byId({required int astrologerId, int? userId}) async {
    final decoded = await _api.post('/getAstrologerForCustomer', body: {
      'astrologerId': astrologerId,
      'userId': userId,
    });
    final maps = _asMapList(decoded);
    if (maps.isEmpty) throw ApiException('Astrologer not found.');
    return Astrologer.fromJson(maps.first);
  }

  /// Skills/categories (legacy `getSkill`).
  Future<List<Skill>> skills() async {
    final decoded = await _api.post('/getSkill');
    return _asMapList(decoded).map(Skill.fromJson).toList();
  }

  // ---------------- Chat session flow (legacy REST contract) ----------------

  /// Start a chat request → returns the created session id (or null).
  Future<String?> addChatRequest({
    required int astrologerId,
    required int userId,
    bool isFree = false,
  }) async {
    final decoded = await _api.post('/chatRequest/add', body: {
      'astrologerId': astrologerId,
      'userId': userId,
      'isFreeSession': isFree,
    });
    if (decoded is Map<String, dynamic>) {
      final rl = decoded['recordList'];
      if (rl is Map<String, dynamic>) {
        return (rl['chatId'] ?? rl['id'] ?? rl['_id'])?.toString();
      }
      if (rl is String || rl is int) return rl.toString();
    }
    return null;
  }

  /// Poll chat history for a session (v2 polls REST every few seconds until
  /// Laravel Reverb websockets are enabled server-side — doc 05 §5.3).
  Future<List<ChatMessage>> chatHistory({
    required String sessionId,
    required String myId,
    int startIndex = 0,
  }) async {
    final decoded = await _api.post('/sessionChat/get', body: {
      'sessionId': sessionId,
      'startIndex': startIndex,
      'fetchRecord': 100,
    });
    return _asMapList(decoded)
        .map((m) => ChatMessage.fromJson(m, myId))
        .toList();
  }

  /// Send one chat message.
  Future<void> sendMessage({
    required String sessionId,
    required String fromUserId,
    required String text,
  }) async {
    await _api.post('/sessionChat/add', body: {
      'sessionId': sessionId,
      'fromUserId': fromUserId,
      'message': text,
    });
  }

  /// End the chat session.
  Future<void> endChat({required String sessionId}) async {
    await _api.post('/chatRequest/endChat', body: {'chatId': sessionId});
  }

  /// Check if the user already has an open chat with this astrologer
  /// (legacy `checkChatSessionAvailable`).
  Future<String?> existingChatSession(int astrologerId) async {
    final decoded = await _api.post('/checkChatSessionAvailable',
        body: {'astrologerId': astrologerId});
    if (decoded is Map<String, dynamic>) {
      final rl = decoded['recordList'];
      if (rl is Map<String, dynamic> && (rl['chatId'] ?? rl['id']) != null) {
        return (rl['chatId'] ?? rl['id']).toString();
      }
    }
    return null;
  }

  // ---------------- Call session flow ----------------

  /// Start an audio/video call request → returns session id.
  Future<String?> addCallRequest({
    required int astrologerId,
    required int userId,
    required bool isVideo,
    bool isFree = false,
  }) async {
    final decoded = await _api.post('/callRequest/add', body: {
      'astrologerId': astrologerId,
      'userId': userId,
      'callType': isVideo ? 'video' : 'audio',
      'isFreeSession': isFree,
    });
    if (decoded is Map<String, dynamic>) {
      final rl = decoded['recordList'];
      if (rl is Map<String, dynamic>) {
        return (rl['callId'] ?? rl['id'] ?? rl['_id'])?.toString();
      }
      if (rl is String || rl is int) return rl.toString();
    }
    return null;
  }

  /// RTC token for the call. v2 target is the LiveKit token endpoint
  /// (doc 04 §4.2). Returns null when not yet provisioned → caller falls
  /// back gracefully.
  Future<String?> rtcToken({required String sessionId}) async {
    try {
      final decoded = await _api.post('/livekit/token', body: {
        'sessionId': sessionId,
        'roomId': sessionId,
      });
      if (decoded is Map<String, dynamic>) {
        final rl = decoded['recordList'];
        if (rl is Map<String, dynamic>) return rl['token']?.toString();
        if (rl is String) return rl;
      }
    } on ApiException catch (e) {
      if (e.statusCode != null && e.statusCode! < 500) rethrow;
      return null;
    }
    return null;
  }

  /// Full LiveKit join info (token + ws url + room) for a call session.
  /// Returns null when the endpoint is not yet provisioned server-side,
  /// so callers can degrade to the REST-only session flow.
  Future<LiveKitSession?> liveKitSession({
    required String sessionId,
    required String identity,
    String? displayName,
    String? room,
  }) async {
    try {
      final decoded = await _api.post('/livekit/token', body: {
        'sessionId': sessionId,
        'room': room ?? sessionId,
        'identity': identity,
        if (displayName != null) 'displayName': displayName,
      });
      if (decoded is Map<String, dynamic>) {
        final rl = decoded['recordList'];
        if (rl is Map<String, dynamic> && rl['token'] != null) {
          return LiveKitSession(
            token: rl['token'].toString(),
            wsUrl: (rl['wsUrl'] ?? Env.liveKitUrl).toString(),
            room: (rl['room'] ?? sessionId).toString(),
            identity: (rl['identity'] ?? identity).toString(),
          );
        }
      }
    } on ApiException catch (e) {
      if (e.statusCode != null && e.statusCode! < 500) rethrow;
      return null;
    }
    return null;
  }

  /// End a call session (stops the server-side billing timer).
  Future<void> endCall({required String callId}) async {
    await _api.post('/callRequest/end', body: {'callId': callId});
  }

  // ---------------- Misc ----------------

  /// Follow / unfollow an astrologer.
  Future<void> setFollow({
    required int userId,
    required int astrologerId,
    required bool follow,
  }) async {
    await _api.post(follow ? '/follower/add' : '/follower/update', body: {
      'userId': userId,
      'astrologerId': astrologerId,
      'isFollow': follow,
    });
  }

  /// Rate & review after a session (legacy `userReview/add`).
  Future<void> addReview({
    required int astrologerId,
    double? rating,
    String? review,
    bool isPublic = true,
  }) async {
    await _api.post('/userReview/add', body: {
      'astrologerId': astrologerId,
      'rating': rating,
      'review': review,
      'isPublic': isPublic,
    });
  }

  /// Resolved image URL helper for astrologer profile photos.
  static String imageUrl(String path) {
    final p = path.trim();
    if (p.isEmpty) return '';
    if (p.startsWith('http')) return p;
    return '${Env.imageBase}$p';
  }
}
