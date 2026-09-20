/// Kundli (birth-chart) models mirroring the legacy
library;
/// `kundli_model.dart`, `kundliChartModel.dart`, `kundliInfoModel.dart`,
/// `chartDetailmodel.dart`, `planetreportmodel.dart`, `dashdetailmodel.dart`,
/// `doshaDetailModel.dart`, `astavargaDetailModel.dart`, `basicDetailmodel.dart`
/// and related contracts.
// ignore_for_file: non_constant_identifier_names

class Kundli {
  Kundli({
    this.id,
    required this.name,
    required this.gender,
    required this.birthDate,
    required this.birthTime,
    required this.birthPlace,
    this.latitude,
    this.longitude,
    this.timezone,
    this.pdfType,
    this.matchType,
    this.forMatch,
    this.lang,
    this.isActive,
    this.isDelete,
    this.createdAt,
    this.updatedAt,
    this.createdBy,
    this.modifiedBy,
    this.pdfLink,
    this.isForTrackPlanet,
  });

  int? id;
  String name;
  String gender;
  DateTime birthDate;
  String birthTime;
  String birthPlace;
  double? latitude;
  double? longitude;
  dynamic timezone;
  String? pdfType;
  String? matchType;
  dynamic forMatch;
  String? lang;
  int? isActive;
  int? isDelete;
  DateTime? createdAt;
  DateTime? updatedAt;
  int? createdBy;
  int? modifiedBy;
  String? pdfLink;
  dynamic isForTrackPlanet;

  factory Kundli.fromJson(Map<String, dynamic> json) => Kundli(
        id: json['id'],
        name: json['name'] ?? '',
        gender: json['gender'] ?? '',
        birthDate: json['birthDate'] != null
            ? DateTime.parse(json['birthDate'].toString())
            : DateTime.now(),
        birthTime: json['birthTime'] ?? '',
        birthPlace: json['birthPlace'] ?? '',
        latitude: _double(json['latitude']),
        longitude: _double(json['longitude']),
        timezone: _double(json['timezone']),
        pdfType: json['pdf_type'],
        matchType: json['match_type'],
        forMatch: json['forMatch'],
        lang: json['lang'],
        isActive: json['isActive'],
        isDelete: json['isDelete'],
        createdAt: json['created_at'] == null
            ? null
            : DateTime.parse(json['created_at'].toString()),
        updatedAt: json['updated_at'] == null
            ? null
            : DateTime.parse(json['updated_at'].toString()),
        createdBy: json['createdBy'],
        modifiedBy: json['modifiedBy'],
        pdfLink: json['pdf_link'],
        isForTrackPlanet: json['isForTrackPlanet'],
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'gender': gender,
        'birthDate': birthDate.toIso8601String(),
        'birthTime': birthTime,
        'birthPlace': birthPlace,
        'latitude': latitude,
        'longitude': longitude,
        'timezone': timezone,
        'pdf_type': pdfType,
        'match_type': matchType,
        'forMatch': forMatch,
        'lang': lang,
        'isActive': isActive,
        'isDelete': isDelete,
        'created_at': createdAt?.toIso8601String(),
        'updated_at': updatedAt?.toIso8601String(),
        'createdBy': createdBy,
        'modifiedBy': modifiedBy,
        'pdf_link': pdfLink,
        'isForTrackPlanet': isForTrackPlanet,
      };

  static double? _double(dynamic v) {
    if (v == null || v == '') return null;
    return double.tryParse(v.toString());
  }
}

class KundliBasic {
  KundliBasic({
    this.id,
    required this.tithi,
    required this.karan,
    required this.yog,
    required this.nakshatra,
    required this.sunRise,
    required this.sunSet,
  });

  int? id;
  String tithi;
  String karan;
  String yog;
  String nakshatra;
  String sunRise;
  String sunSet;

  factory KundliBasic.fromJson(Map<String, dynamic> json) => KundliBasic(
        id: json['id'],
        tithi: json['Tithi'] ?? '',
        karan: json['Karan'] ?? '',
        yog: json['Yog'] ?? '',
        nakshatra: json['Nakshatra'] ?? '',
        sunRise: json['SunRise'] ?? '',
        sunSet: json['SunSet'] ?? '',
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'Tithi': tithi,
        'Karan': karan,
        'Yog': yog,
        'Nakshatra': nakshatra,
        'SunRise': sunRise,
        'SunSet': sunSet,
      };
}

/// Chart payload from `kundali/chart` / `Kundali/show/:id`.
class KundliChartModel {
  KundliChartModel({
    this.message,
    this.recordList,
    this.kundaliChart,
    this.planetDetails,
    this.ashtakvarga,
    this.personalCharacteristics,
    this.status,
  });

  String? message;
  List<KundliRecord>? recordList;
  KundaliChart? kundaliChart;
  PlanetDetails? planetDetails;
  Ashtakvarga? ashtakvarga;
  PersonalCharacteristics? personalCharacteristics;
  int? status;

  factory KundliChartModel.fromJson(Map<String, dynamic> json) =>
      KundliChartModel(
        message: json['message'],
        recordList: json['recordList'] == null
            ? []
            : List<KundliRecord>.from(
                json['recordList'].map((x) => KundliRecord.fromJson(x))),
        kundaliChart: json['kundaliChart'] == null
            ? null
            : KundaliChart.fromJson(json['kundaliChart']),
        planetDetails: json['planetDetails'] == null
            ? null
            : PlanetDetails.fromJson(json['planetDetails']),
        ashtakvarga: json['ashtakvarga'] == null
            ? null
            : Ashtakvarga.fromJson(json['ashtakvarga']),
        personalCharacteristics: json['personalCharacteristics'] == null
            ? null
            : PersonalCharacteristics.fromJson(json['personalCharacteristics']),
        status: json['status'],
      );

  Map<String, dynamic> toJson() => {
        'message': message,
        'recordList': recordList == null
            ? []
            : List<dynamic>.from(recordList!.map((x) => x.toJson())),
        'kundaliChart': kundaliChart?.toJson(),
        'planetDetails': planetDetails?.toJson(),
        'ashtakvarga': ashtakvarga?.toJson(),
        'personalCharacteristics': personalCharacteristics?.toJson(),
        'status': status,
      };
}

class KundliRecord {
  KundliRecord({
    this.id,
    this.name,
    this.gender,
    this.birthDate,
    this.birthTime,
    this.birthPlace,
    this.isActive,
    this.isDelete,
    this.createdAt,
    this.updatedAt,
    this.createdBy,
    this.modifiedBy,
    this.latitude,
    this.longitude,
    this.timezone,
    this.isForTrackPlanet,
    this.pdfType,
    this.matchType,
    this.forMatch,
    this.pdfLink,
  });

  int? id;
  String? name;
  String? gender;
  DateTime? birthDate;
  String? birthTime;
  String? birthPlace;
  int? isActive;
  int? isDelete;
  DateTime? createdAt;
  DateTime? updatedAt;
  int? createdBy;
  int? modifiedBy;
  String? latitude;
  String? longitude;
  String? timezone;
  dynamic isForTrackPlanet;
  String? pdfType;
  String? matchType;
  int? forMatch;
  String? pdfLink;

  factory KundliRecord.fromJson(Map<String, dynamic> json) => KundliRecord(
        id: json['id'],
        name: json['name'],
        gender: json['gender'],
        birthDate: json['birthDate'] == null
            ? null
            : DateTime.parse(json['birthDate'].toString()),
        birthTime: json['birthTime'],
        birthPlace: json['birthPlace'],
        isActive: json['isActive'],
        isDelete: json['isDelete'],
        createdAt: json['created_at'] == null
            ? null
            : DateTime.parse(json['created_at'].toString()),
        updatedAt: json['updated_at'] == null
            ? null
            : DateTime.parse(json['updated_at'].toString()),
        createdBy: json['createdBy'],
        modifiedBy: json['modifiedBy'],
        latitude: json['latitude'],
        longitude: json['longitude'],
        timezone: json['timezone'],
        isForTrackPlanet: json['isForTrackPlanet'],
        pdfType: json['pdf_type'],
        matchType: json['match_type'],
        forMatch: json['forMatch'],
        pdfLink: json['pdf_link'],
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'gender': gender,
        'birthDate': birthDate?.toIso8601String(),
        'birthTime': birthTime,
        'birthPlace': birthPlace,
        'isActive': isActive,
        'isDelete': isDelete,
        'created_at': createdAt?.toIso8601String(),
        'updated_at': updatedAt?.toIso8601String(),
        'createdBy': createdBy,
        'modifiedBy': modifiedBy,
        'latitude': latitude,
        'longitude': longitude,
        'timezone': timezone,
        'isForTrackPlanet': isForTrackPlanet,
        'pdf_type': pdfType,
        'match_type': matchType,
        'forMatch': forMatch,
        'pdf_link': pdfLink,
      };
}

class KundaliChart {
  KundaliChart({
    this.axis,
    this.axisLabel,
    this.data,
    this.data1,
    this.data3,
    this.data5,
    this.data7,
    this.label,
    this.type,
  });

  List<String>? axis;
  List<String>? axisLabel;
  List<List<double>>? data;
  List<List<double>>? data1;
  List<List<double>>? data3;
  List<List<double>>? data5;
  List<List<double>>? data7;
  List<String>? label;
  String? type;

  factory KundaliChart.fromJson(Map<String, dynamic> json) => KundaliChart(
        axis: json['axis'] == null ? [] : List<String>.from(json['axis']),
        axisLabel: json['axisLabel'] == null
            ? []
            : List<String>.from(json['axisLabel']),
        data: _listOfListsDouble(json['data']),
        data1: _listOfListsDouble(json['data1']),
        data3: _listOfListsDouble(json['data3']),
        data5: _listOfListsDouble(json['data5']),
        data7: _listOfListsDouble(json['data7']),
        label: json['label'] == null ? [] : List<String>.from(json['label']),
        type: json['type'],
      );

  Map<String, dynamic> toJson() => {
        'axis': axis,
        'axisLabel': axisLabel,
        'data': data,
        'data1': data1,
        'data3': data3,
        'data5': data5,
        'data7': data7,
        'label': label,
        'type': type,
      };

  static List<List<double>>? _listOfListsDouble(dynamic v) {
    if (v == null) return null;
    return List<List<double>>.from(v.map(
        (x) => List<double>.from(x.map((e) => double.tryParse(e.toString()) ?? 0.0))));
  }
}

/// Planet + Ashtakvarga + Personal detail nested payloads.
class PlanetDetails {
  PlanetDetails({this.status, this.response});
  int? status;
  PlanetResponse? response;

  factory PlanetDetails.fromJson(Map<String, dynamic> json) => PlanetDetails(
        status: json['status'],
        response: json['response'] == null
            ? null
            : PlanetResponse.fromJson(json['response']),
      );

  Map<String, dynamic> toJson() =>
      {'status': status, 'response': response?.toJson()};
}

class PlanetResponse {
  PlanetResponse({
    this.the0, this.the1, this.the2, this.the3, this.the4,
    this.the5, this.the6, this.the7, this.the8, this.the9,
    this.birthDasa, this.currentDasa, this.birthDasaTime, this.currentDasaTime,
    this.luckyGem, this.luckyNum, this.luckyColors, this.luckyLetters,
    this.luckyNameStart, this.rasi, this.nakshatra, this.nakshatraPada,
    this.panchang, this.ghatkaChakra, this.ashtakvarga,
    this.moonSign, this.sunSign, this.northNode, this.southNode, this.isCombust,
  });

  PlanetElement? the0, the1, the2, the3, the4, the5, the6, the7, the8, the9;
  String? birthDasa, currentDasa, birthDasaTime, currentDasaTime, luckyGem;
  List<int>? luckyNum;
  List<String>? luckyColors, luckyLetters, luckyNameStart;
  String? rasi, nakshatra, moonSign, sunSign, northNode, southNode;
  int? nakshatraPada;
  PanchangDetail? panchang;
  GhatkaChakra? ghatkaChakra;
  Ashtakvarga? ashtakvarga;
  IsCombust? isCombust;

  factory PlanetResponse.fromJson(Map<String, dynamic> json) => PlanetResponse(
        the0: json['0'] == null ? null : PlanetElement.fromJson(json['0']),
        the1: json['1'] == null ? null : PlanetElement.fromJson(json['1']),
        the2: json['2'] == null ? null : PlanetElement.fromJson(json['2']),
        the3: json['3'] == null ? null : PlanetElement.fromJson(json['3']),
        the4: json['4'] == null ? null : PlanetElement.fromJson(json['4']),
        the5: json['5'] == null ? null : PlanetElement.fromJson(json['5']),
        the6: json['6'] == null ? null : PlanetElement.fromJson(json['6']),
        the7: json['7'] == null ? null : PlanetElement.fromJson(json['7']),
        the8: json['8'] == null ? null : PlanetElement.fromJson(json['8']),
        the9: json['9'] == null ? null : PlanetElement.fromJson(json['9']),
        birthDasa: json['birth_dasa'],
        currentDasa: json['current_dasa'],
        birthDasaTime: json['birth_dasa_time'],
        currentDasaTime: json['current_dasa_time'],
        luckyGem: json['lucky_gem'],
        luckyNum: json['lucky_num'] == null ? [] : List<int>.from(json['lucky_num']),
        luckyColors: json['lucky_colors'] == null ? [] : List<String>.from(json['lucky_colors']),
        luckyLetters: json['lucky_letters'] == null ? [] : List<String>.from(json['lucky_letters']),
        luckyNameStart: json['lucky_name_start'] == null ? [] : List<String>.from(json['lucky_name_start']),
        rasi: json['rasi'],
        nakshatra: json['nakshatra'],
        nakshatraPada: json['nakshatra_pada'],
        panchang: json['panchang'] == null ? null : PanchangDetail.fromJson(json['panchang']),
        ghatkaChakra: json['ghatka_chakra'] == null ? null : GhatkaChakra.fromJson(json['ghatka_chakra']),
        ashtakvarga: json['ashtakvarga'] == null ? null : Ashtakvarga.fromJson(json['ashtakvarga']),
        moonSign: json['moon_sign'],
        sunSign: json['sun_sign'],
        northNode: json['north_node'],
        southNode: json['south_node'],
        isCombust: json['is_combust'] == null ? null : IsCombust.fromJson(json['is_combust']),
      );

  Map<String, dynamic> toJson() => {
        '0': the0?.toJson(), '1': the1?.toJson(), '2': the2?.toJson(),
        '3': the3?.toJson(), '4': the4?.toJson(), '5': the5?.toJson(),
        '6': the6?.toJson(), '7': the7?.toJson(), '8': the8?.toJson(),
        '9': the9?.toJson(),
        'birth_dasa': birthDasa, 'current_dasa': currentDasa,
        'birth_dasa_time': birthDasaTime, 'current_dasa_time': currentDasaTime,
        'lucky_gem': luckyGem, 'lucky_num': luckyNum,
        'lucky_colors': luckyColors, 'lucky_letters': luckyLetters,
        'lucky_name_start': luckyNameStart,
        'rasi': rasi, 'nakshatra': nakshatra, 'nakshatra_pada': nakshatraPada,
        'panchang': panchang?.toJson(),
        'ghatka_chakra': ghatkaChakra?.toJson(),
        'ashtakvarga': ashtakvarga?.toJson(),
        'moon_sign': moonSign, 'sun_sign': sunSign,
        'north_node': northNode, 'south_node': southNode,
        'is_combust': isCombust?.toJson(),
      };
}

class PlanetElement {
  PlanetElement({
    this.name, this.fullName, this.localDegree, this.globalDegree,
    this.progressInPercentage, this.rasiNo, this.zodiac, this.house,
    this.nakshatra, this.nakshatraLord, this.nakshatraPada, this.nakshatraNo,
    this.zodiacLord, this.isPlanetSet, this.lordStatus, this.basicAvastha,
    this.isCombust, this.speedRadiansPerDay, this.retro,
  });

  String? name, fullName, zodiac, nakshatra, nakshatraLord, zodiacLord;
  double? localDegree, globalDegree, progressInPercentage, speedRadiansPerDay;
  int? rasiNo, house, nakshatraPada, nakshatraNo, isPlanetSet, isCombust, retro;
  String? lordStatus, basicAvastha;

  static double? _d(dynamic v) =>
      v == null ? null : double.tryParse(v.toString());

  factory PlanetElement.fromJson(Map<String, dynamic> json) => PlanetElement(
        name: json['name'],
        fullName: json['full_name'],
        localDegree: _d(json['local_degree']),
        globalDegree: _d(json['global_degree']),
        progressInPercentage: _d(json['progress_in_percentage']),
        rasiNo: json['rasi_no'],
        zodiac: json['zodiac'],
        house: json['house'],
        nakshatra: json['nakshatra'],
        nakshatraLord: json['nakshatra_lord'],
        nakshatraPada: json['nakshatra_pada'],
        nakshatraNo: json['nakshatra_no'],
        zodiacLord: json['zodiac_lord'],
        isPlanetSet: json['is_planet_set'],
        lordStatus: json['lord_status'],
        basicAvastha: json['basic_avastha'],
        isCombust: json['is_combust'],
        speedRadiansPerDay: _d(json['speed_radians_per_day']),
        retro: json['retro'],
      );

  Map<String, dynamic> toJson() => {
        'name': name, 'full_name': fullName,
        'local_degree': localDegree, 'global_degree': globalDegree,
        'progress_in_percentage': progressInPercentage,
        'rasi_no': rasiNo, 'zodiac': zodiac, 'house': house,
        'nakshatra': nakshatra, 'nakshatra_lord': nakshatraLord,
        'nakshatra_pada': nakshatraPada, 'nakshatra_no': nakshatraNo,
        'zodiac_lord': zodiacLord, 'is_planet_set': isPlanetSet,
        'lord_status': lordStatus, 'basic_avastha': basicAvastha,
        'is_combust': isCombust,
        'speed_radians_per_day': speedRadiansPerDay, 'retro': retro,
      };
}

class PanchangDetail {
  PanchangDetail({
    this.tithi, this.karan, this.yog, this.nakshatra,
    this.sunrise, this.sunset, this.vedicSunrise, this.vedicSunset,
  });

  String? tithi, karan, yog, nakshatra, sunrise, sunset, vedicSunrise, vedicSunset;

  factory PanchangDetail.fromJson(Map<String, dynamic> json) => PanchangDetail(
        tithi: json['tithi'],
        karan: json['karan'],
        yog: json['yog'],
        nakshatra: json['nakshatra'],
        sunrise: json['sunrise'],
        sunset: json['sunset'],
        vedicSunrise: json['vedic_sunrise'],
        vedicSunset: json['vedic_sunset'],
      );

  Map<String, dynamic> toJson() => {
        'tithi': tithi, 'karan': karan, 'yog': yog, 'nakshatra': nakshatra,
        'sunrise': sunrise, 'sunset': sunset,
        'vedic_sunrise': vedicSunrise, 'vedic_sunset': vedicSunset,
      };
}

class GhatkaChakra {
  GhatkaChakra({
    this.ascendant, this.moonSign, this.sunSign,
    this.northNode, this.southNode, this.planetPosition,
  });

  String? ascendant, moonSign, sunSign, northNode, southNode;
  Map<String, double>? planetPosition;

  factory GhatkaChakra.fromJson(Map<String, dynamic> json) => GhatkaChakra(
        ascendant: json['Ascendant'],
        moonSign: json['MoonSign'],
        sunSign: json['SunSign'],
        northNode: json['NorthNode'],
        southNode: json['SouthNode'],
        planetPosition: json['PlanetPosition'] == null
            ? {}
            : (json['PlanetPosition'] as Map).map((k, v) =>
                MapEntry(k.toString(), double.tryParse(v.toString()) ?? 0.0)),
      );

  Map<String, dynamic> toJson() => {
        'Ascendant': ascendant, 'MoonSign': moonSign, 'SunSign': sunSign,
        'NorthNode': northNode, 'SouthNode': southNode,
        'PlanetPosition': planetPosition,
      };
}

class Ashtakvarga {
  Ashtakvarga({this.status, this.response});
  int? status;
  AshtakvargaResponse? response;

  factory Ashtakvarga.fromJson(Map<String, dynamic> json) => Ashtakvarga(
        status: json['status'],
        response: json['response'] == null
            ? null
            : AshtakvargaResponse.fromJson(json['response']),
      );

  Map<String, dynamic> toJson() =>
      {'status': status, 'response': response?.toJson()};
}

class AshtakvargaResponse {
  AshtakvargaResponse({
    this.ashtakvargaOrder,
    this.ashtakvargaPoints,
    this.ashtakvargaTotal,
  });

  List<String>? ashtakvargaOrder;
  List<List<int>>? ashtakvargaPoints;
  List<int>? ashtakvargaTotal;

  factory AshtakvargaResponse.fromJson(Map<String, dynamic> json) =>
      AshtakvargaResponse(
        ashtakvargaOrder: json['ashtakvarga_order'] == null
            ? []
            : List<String>.from(json['ashtakvarga_order']),
        ashtakvargaPoints: json['ashtakvarga_points'] == null
            ? []
            : List<List<int>>.from(
                json['ashtakvarga_points'].map((x) => List<int>.from(x))),
        ashtakvargaTotal: json['ashtakvarga_total'] == null
            ? []
            : List<int>.from(json['ashtakvarga_total']),
      );

  Map<String, dynamic> toJson() => {
        'ashtakvarga_order': ashtakvargaOrder,
        'ashtakvarga_points': ashtakvargaPoints,
        'ashtakvarga_total': ashtakvargaTotal,
      };
}

class IsCombust {
  IsCombust({
    this.sun, this.moon, this.mars, this.mercury, this.jupiter,
    this.venus, this.saturn, this.rahu, this.ketu,
  });

  int? sun, moon, mars, mercury, jupiter, venus, saturn, rahu, ketu;

  factory IsCombust.fromJson(Map<String, dynamic> json) => IsCombust(
        sun: json['Sun'], moon: json['Moon'], mars: json['Mars'],
        mercury: json['Mercury'], jupiter: json['Jupiter'],
        venus: json['Venus'], saturn: json['Saturn'],
        rahu: json['Rahu'], ketu: json['Ketu'],
      );

  Map<String, dynamic> toJson() => {
        'Sun': sun, 'Moon': moon, 'Mars': mars, 'Mercury': mercury,
        'Jupiter': jupiter, 'Venus': venus, 'Saturn': saturn,
        'Rahu': rahu, 'Ketu': ketu,
      };
}

class PersonalCharacteristics {
  PersonalCharacteristics({this.status, this.response});
  int? status;
  PlanetResponse? response;

  factory PersonalCharacteristics.fromJson(Map<String, dynamic> json) =>
      PersonalCharacteristics(
        status: json['status'],
        response: json['response'] == null
            ? null
            : PlanetResponse.fromJson(json['response']),
      );

  Map<String, dynamic> toJson() =>
      {'status': status, 'response': response?.toJson()};
}
