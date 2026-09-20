import '../api/api_client.dart';
import '../env.dart';
import '../models/misc.dart';

/// Misc customer endpoints mirroring the legacy Customer-app calls:
/// `/getReportType`, `/userReport/add`, `/getAppBlog`, `/addBlogReader`,
/// `/getStory`, `/getAstrologerStory`, `/clickStory`, `/getGift`,
/// `/sendGift`, `/getOrderAddress`, `/orderAddress/*`, `/getUserNotification`
/// and the userNotification delete endpoints.
class MiscApi {
  MiscApi._();
  static final MiscApi instance = MiscApi._();

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

  static Map<String, dynamic> _asMap(dynamic decoded) {
    if (decoded is Map<String, dynamic>) {
      final rl = decoded['recordList'];
      if (rl is Map<String, dynamic>) return rl;
      return decoded;
    }
    return const {};
  }

  // ---------------- Reports ----------------

  /// Available report types with prices (legacy `getReportType`).
  Future<List<ReportType>> reportTypes() async {
    final decoded = await _api.post('/getReportType');
    return _asMapList(decoded).map(ReportType.fromJson).toList();
  }

  /// Request a paid astrologer report (legacy `userReport/add`).
  Future<Map<String, dynamic>> addReportRequest({
    required int userId,
    required dynamic astrologerId,
    required dynamic reportTypeId,
    Map<String, dynamic>? extra,
  }) async {
    final decoded = await _api.post('/userReport/add', body: {
      'userId': userId,
      'astrologerId': astrologerId,
      'reportTypeId': reportTypeId,
      ...?extra,
    });
    return _asMap(decoded);
  }

  // ---------------- Blogs ----------------

  /// Blog listing (legacy `getAppBlog`).
  Future<List<Blog>> blogs() async {
    final decoded = await _api.post('/getAppBlog');
    return _asMapList(decoded).map(Blog.fromJson).toList();
  }

  /// Track a blog read (legacy `addBlogReader`).
  Future<void> addBlogReader({required dynamic blogId, int? userId}) async {
    await _api.post('/addBlogReader', body: {
      'blogId': blogId,
      'userId': userId,
    });
  }

  // ---------------- Stories ----------------

  /// Home stories (legacy `getStory`).
  Future<List<Story>> stories() async {
    final decoded = await _api.post('/getStory');
    return _asMapList(decoded).map(Story.fromJson).toList();
  }

  /// Astrologer stories (legacy `getAstrologerStory`).
  Future<List<Story>> astrologerStories({dynamic astrologerId}) async {
    final decoded =
        await _api.post('/getAstrologerStory', body: {'astrologerId': astrologerId});
    return _asMapList(decoded).map(Story.fromJson).toList();
  }

  /// Track a story click (legacy `clickStory`).
  Future<void> clickStory({required dynamic storyId}) async {
    await _api.post('/clickStory', body: {'storyId': storyId});
  }

  // ---------------- Gifts ----------------

  /// Gift catalogue (legacy `getGift`).
  Future<List<Gift>> gifts() async {
    final decoded = await _api.post('/getGift');
    return _asMapList(decoded).map(Gift.fromJson).toList();
  }

  /// Send a gift to an astrologer (legacy `sendGift`).
  Future<Map<String, dynamic>> sendGift({
    required int userId,
    required dynamic astrologerId,
    required dynamic giftId,
  }) async {
    final decoded = await _api.post('/sendGift', body: {
      'userId': userId,
      'astrologerId': astrologerId,
      'giftId': giftId,
    });
    return _asMap(decoded);
  }

  // ---------------- Order addresses ----------------

  /// Saved addresses for mall/puja deliveries (legacy `getOrderAddress`).
  Future<List<OrderAddress>> addresses({required int userId}) async {
    final decoded = await _api.post('/getOrderAddress', body: {'userId': userId});
    return _asMapList(decoded).map(OrderAddress.fromJson).toList();
  }

  /// Add an address (legacy `orderAddress/add`).
  Future<Map<String, dynamic>> addAddress({
    required int userId,
    required Map<String, dynamic> address,
  }) async {
    final decoded = await _api.post('/orderAddress/add', body: {
      'userId': userId,
      ...address,
    });
    return _asMap(decoded);
  }

  /// Update an address (legacy `orderAddress/update/:id`).
  Future<void> updateAddress({
    required dynamic addressId,
    required Map<String, dynamic> address,
  }) async {
    await _api.post('/orderAddress/update/$addressId', body: address);
  }

  // ---------------- Notifications ----------------

  /// In-app notifications (legacy `getUserNotification`).
  Future<List<Map<String, dynamic>>> notifications({required int userId}) async {
    final decoded =
        await _api.post('/getUserNotification', body: {'userId': userId});
    return _asMapList(decoded);
  }

  /// Delete one notification (legacy `userNotification/deleteUserNotification`).
  Future<void> deleteNotification({required dynamic notificationId}) async {
    await _api.post('/userNotification/deleteUserNotification',
        body: {'id': notificationId});
  }

  /// Clear all notifications
  /// (legacy `userNotification/deleteAllNotification`).
  Future<void> deleteAllNotifications({required int userId}) async {
    await _api.post('/userNotification/deleteAllNotification',
        body: {'userId': userId});
  }

  /// Resolved image URL helper for blog/story/gift images.
  static String imageUrl(String path) {
    final p = path.trim();
    if (p.isEmpty) return '';
    if (p.startsWith('http')) return p;
    return '${Env.imageBase}$p';
  }
}
