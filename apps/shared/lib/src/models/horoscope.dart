/// Horoscope models mirroring the legacy `hororscopeSignModel.dart`,
/// `horoscopeModel.dart` and `dailyHoroscopeModel.dart` contracts.
library;

/// One zodiac sign from `/getHororscopeSign`.
class HoroscopeSign {
  HoroscopeSign({
    this.id,
    this.name,
    this.image,
    this.dateRange,
    this.isActive,
  });

  int? id;
  String? name, image, dateRange;
  int? isActive;

  factory HoroscopeSign.fromJson(Map<String, dynamic> json) => HoroscopeSign(
        id: json['id'],
        name: json['name'],
        image: json['image'],
        dateRange: json['dateRange'] ?? json['date_range'],
        isActive: json['isActive'],
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'image': image,
        'dateRange': dateRange,
        'isActive': isActive,
      };
}

/// Daily / Weekly / Yearly horoscope payload.
class DailyHoroscope {
  DailyHoroscope({
    this.id,
    this.signId,
    this.signName,
    this.date,
    this.predictions,
    this.luckyNumber,
    this.luckyColor,
    this.mood,
    this.compatibility,
    this.physique,
    this.status,
    this.finances,
    this.relationship,
    this.career,
    this.travel,
    this.family,
    this.friends,
    this.health,
  });

  int? id, signId;
  String? signName, date, predictions, luckyNumber, luckyColor, mood;
  String? compatibility;
  int? physique, status, finances, relationship, career, travel, family, friends, health;

  static int? _parseInt(dynamic val) {
    if (val == null) return null;
    if (val is num) return val.toInt();
    if (val is String) {
      final clean = val.replaceAll('%', '').trim();
      return int.tryParse(clean) ?? double.tryParse(clean)?.toInt();
    }
    return null;
  }

  factory DailyHoroscope.fromJson(Map<String, dynamic> json) => DailyHoroscope(
        id: json['id'],
        signId: json['signId'] ?? json['sign_id'] ?? json['horoscopeSignId'],
        signName: json['signName'] ?? json['sign_name'] ?? json['zodiac'],
        date: json['date'] ?? json['horoscopeDate'] ?? json['start_date'],
        predictions: json['predictions'] ??
            json['prediction'] ??
            json['bot_response'] ??
            json['horoscope'],
        luckyNumber: json['luckyNumber']?.toString() ??
            json['lucky_number']?.toString(),
        luckyColor: json['luckyColor'] ?? json['lucky_color'],
        mood: json['mood'],
        compatibility: json['compatibility'],
        physique: _parseInt(json['physique']),
        status: _parseInt(json['status']),
        finances: _parseInt(json['finances']),
        relationship: _parseInt(json['relationship']),
        career: _parseInt(json['career']),
        travel: _parseInt(json['travel']),
        family: _parseInt(json['family']),
        friends: _parseInt(json['friends']),
        health: _parseInt(json['health']),
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'signId': signId,
        'signName': signName,
        'date': date,
        'predictions': predictions,
        'luckyNumber': luckyNumber,
        'luckyColor': luckyColor,
        'mood': mood,
        'compatibility': compatibility,
        'physique': physique,
        'status': status,
        'finances': finances,
        'relationship': relationship,
        'career': career,
        'travel': travel,
        'family': family,
        'friends': friends,
        'health': health,
      };
}
