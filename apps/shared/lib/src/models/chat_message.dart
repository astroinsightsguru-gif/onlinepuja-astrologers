/// Chat message for consultation sessions.
///
/// v2 stores messages via the backend (`chatRequest/*`, `sessionChat/*` REST
/// endpoints) instead of the legacy Firebase RTDB — single source of truth on
/// our own server (docs/new-apps/04-FREE_STACK_PROVIDERS.md §4.3).
class ChatMessage {
  ChatMessage({
    this.id,
    required this.sessionId,
    required this.fromUserId,
    required this.text,
    this.attachmentPath,
    this.sentAt,
    this.isMine = false,
  });

  final int? id;
  final String sessionId;
  final String fromUserId;
  final String text;
  final String? attachmentPath;
  final DateTime? sentAt;
  final bool isMine;

  ChatMessage.fromJson(Map<String, dynamic> json, String myId)
      : id = int.tryParse(json['id']?.toString() ?? ''),
        sessionId = json['sessionId']?.toString() ?? '',
        fromUserId = json['fromUserId']?.toString() ?? '',
        text = json['message'] ?? json['text'] ?? '',
        attachmentPath = json['attachment'] ?? json['attachmentPath'],
        sentAt = json['created_at'] == null
            ? null
            : DateTime.tryParse(json['created_at'].toString()),
        isMine = json['fromUserId']?.toString() == myId;

  Map<String, dynamic> toJson() => {
        'id': id,
        'sessionId': sessionId,
        'fromUserId': fromUserId,
        'message': text,
        'attachment': attachmentPath,
        'created_at': sentAt?.toIso8601String(),
        'isMine': isMine,
      };
}
