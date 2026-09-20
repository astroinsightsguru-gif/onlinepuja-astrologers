/// Astrologer profile. Field names follow the live API contract
/// (`getAstrologer` / `getAstrologerForCustomer` responses).
library;

import '../env.dart';

class Astrologer {
  Astrologer({
    this.id,
    this.userId,
    this.name = '',
    this.email = '',
    this.primarySkill = '',
    this.allSkill = '',
    this.languageKnown = '',
    this.profileImage = '',
    this.charge = 0,
    this.videoCallRate = 0,
    this.experienceInYears = 0,
    this.currentCity = '',
    this.loginBio = '',
    this.chatStatus = 'Online',
    this.callStatus = 'Online',
    this.isFollow = false,
    this.isBlock = false,
    this.chatMin = 0,
    this.callMin = 0,
    this.rating = 0,
    this.reviews = 0,
    this.isFreeAvailable = false,
    this.isBoosted = false,
    this.totalOrder = 0,
    this.astroVideo,
    this.fcmToken,
  });

  final int? id;
  final int? userId;
  final String name;
  final String email;
  final String primarySkill;
  final String allSkill;
  final String languageKnown;
  final String profileImage;
  final double charge;
  final double videoCallRate;
  final int experienceInYears;
  final String currentCity;
  final String loginBio;
  final String chatStatus;
  final String callStatus;
  final bool isFollow;
  final bool isBlock;
  final int chatMin;
  final int callMin;
  final double rating;
  final int reviews;
  final bool isFreeAvailable;
  final bool isBoosted;
  final int totalOrder;
  final String? astroVideo;
  final String? fcmToken;

  static double _d(dynamic v) =>
      v == null ? 0 : (double.tryParse(v.toString()) ?? 0);

  static int _i(dynamic v) =>
      v == null ? 0 : (int.tryParse(v.toString()) ?? 0);

  Astrologer.fromJson(Map<String, dynamic> json)
      : id = _i(json['id']),
        userId = _i(json['userId']),
        name = json['name'] ?? '',
        email = json['email'] ?? '',
        primarySkill = json['primarySkill'] ?? '',
        allSkill = json['allSkill'] ?? '',
        languageKnown = json['languageKnown'] ?? '',
        profileImage = json['profileImage'] ?? '',
        charge = _d(json['charge']),
        videoCallRate = _d(json['videoCallRate']),
        experienceInYears = _i(json['experienceInYears']),
        currentCity = json['currentCity'] ?? '',
        loginBio = json['loginBio'] ?? '',
        chatStatus = json['chatStatus'] ?? 'Online',
        callStatus = json['callStatus'] ?? 'Online',
        isFollow = json['isFollow'] ?? false,
        isBlock = json['isBlock'] ?? false,
        chatMin = _i(json['chatMin']),
        callMin = _i(json['callMin']),
        rating = json['astrologerRating'] is Map
            ? _d(json['astrologerRating']['rating'])
            : _d(json['rating']),
        reviews = json['astrologerRating'] is Map
            ? _i(json['astrologerRating']['review'])
            : _i(json['reviews']),
        isFreeAvailable = json['isFreeAvailable'] ?? false,
        isBoosted = json['is_boosted'] ?? false,
        totalOrder = _i(json['totalOrder']),
        astroVideo = json['astro_video'],
        fcmToken = json['fcmToken'];

  Map<String, dynamic> toJson() => {
        'id': id,
        'userId': userId,
        'name': name,
        'email': email,
        'primarySkill': primarySkill,
        'allSkill': allSkill,
        'languageKnown': languageKnown,
        'profileImage': profileImage,
        'charge': charge,
        'videoCallRate': videoCallRate,
        'experienceInYears': experienceInYears,
        'currentCity': currentCity,
        'loginBio': loginBio,
        'chatStatus': chatStatus,
        'callStatus': callStatus,
        'isFollow': isFollow,
        'isBlock': isBlock,
        'chatMin': chatMin,
        'callMin': callMin,
        'rating': rating,
        'reviews': reviews,
        'isFreeAvailable': isFreeAvailable,
        'is_boosted': isBoosted,
        'totalOrder': totalOrder,
        'astro_video': astroVideo,
        'fcmToken': fcmToken,
      };

  /// URL for the profile image (backend returns a relative storage path).
  String get imageUrl {
    final p = profileImage.trim();
    if (p.isEmpty) return '';
    if (p.startsWith('http')) return p;
    return '${Env.imageBase}$p';
  }

  bool get isChatOnline => chatStatus == 'Online';
  bool get isCallOnline => callStatus == 'Online';

  String get skillsLabel {
    final primary = primarySkill.trim();
    if (allSkill.trim().isEmpty) return primary;
    return primary;
  }
}
