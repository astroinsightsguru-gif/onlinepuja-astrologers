/// Panchang models mirroring the legacy `panchangModel.dart` and
/// `vedicPanchangModel.dart` contracts (`/get/panchang`).
library;

class Panchang {
  Panchang({
    this.day,
    this.month,
    this.year,
    this.sunrise,
    this.sunset,
    this.moonrise,
    this.moonset,
    this.vFN,
    this.tithi,
    this.nakshatra,
    this.yog,
    this.karan,
    this.rahukaal,
    this.vikramSamvat,
    this.shakaSamvat,
    this.masa,
    this.paksha,
    this.ritu,
    this.vedicSunrise,
    this.vedicSunset,
  });

  int? day, month, year;
  String? sunrise, sunset, moonrise, moonset;
  String? vFN, tithi, nakshatra, yog, karan, rahukaal;
  String? vikramSamvat, shakaSamvat, masa, paksha, ritu;
  String? vedicSunrise, vedicSunset;

  factory Panchang.fromJson(Map<String, dynamic> json) => Panchang(
        day: json['day'],
        month: json['month'],
        year: json['year'],
        sunrise: json['sunrise'],
        sunset: json['sunset'],
        moonrise: json['moonrise'],
        moonset: json['moonset'],
        vFN: json['VFN'] ?? json['vFN'],
        tithi: json['tithi'],
        nakshatra: json['nakshatra'],
        yog: json['yog'],
        karan: json['karan'],
        rahukaal: json['rahukaal'],
        vikramSamvat: json['vikram_samvat'] ?? json['vikramSamvat'],
        shakaSamvat: json['shaka_samvat'] ?? json['shakaSamvat'],
        masa: json['masa'],
        paksha: json['paksha'],
        ritu: json['ritu'],
        vedicSunrise: json['vedic_sunrise'],
        vedicSunset: json['vedic_sunset'],
      );

  Map<String, dynamic> toJson() => {
        'day': day,
        'month': month,
        'year': year,
        'sunrise': sunrise,
        'sunset': sunset,
        'moonrise': moonrise,
        'moonset': moonset,
        'VFN': vFN,
        'tithi': tithi,
        'nakshatra': nakshatra,
        'yog': yog,
        'karan': karan,
        'rahukaal': rahukaal,
        'vikram_samvat': vikramSamvat,
        'shaka_samvat': shakaSamvat,
        'masa': masa,
        'paksha': paksha,
        'ritu': ritu,
        'vedic_sunrise': vedicSunrise,
        'vedic_sunset': vedicSunset,
      };
}

/// Wraps the standard `/get/panchang` response shape:
/// `{"status":..., "recordList":{...}}`.
class PanchangResponse {
  PanchangResponse({this.status, this.recordList, this.message});

  int? status;
  String? message;
  Panchang? recordList;

  factory PanchangResponse.fromJson(Map<String, dynamic> json) =>
      PanchangResponse(
        status: json['status'],
        message: json['message'],
        recordList: json['recordList'] == null
            ? null
            : Panchang.fromJson(json['recordList']),
      );
}
