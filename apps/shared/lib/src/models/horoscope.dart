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

/// Daily horoscope payload from `/getDailyHoroscope`.
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
  });

  int? id, signId;
  String? signName, date, predictions, luckyNumber, luckyColor, mood;
  String? compatibility;

  factory DailyHoroscope.fromJson(Map<String, dynamic> json) => DailyHoroscope(
        id: json['id'],
        signId: json['signId'] ?? json['sign_id'] ?? json['sign_id'],
        signName: json['signName'] ?? json['sign_name'],
        date: json['date'] ?? json['horoscopeDate'],
        predictions: json['predictions'] ??
            json['prediction'] ??
            json['horoscope'],
        luckyNumber: json['luckyNumber']?.toString() ??
            json['lucky_number']?.toString(),
        luckyColor: json['luckyColor'] ?? json['lucky_color'],
        mood: json['mood'],
        compatibility: json['compatibility'],
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
      };
}
