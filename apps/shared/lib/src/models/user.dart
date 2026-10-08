/// Signed-in app user. Field names follow the legacy API contract exactly
/// (`Customer-app/lib/model/current_user_model.dart`).
class User {
  User({
    this.id,
    this.name = 'User',
    this.contactNo,
    this.email = '',
    this.birthDate,
    this.birthTime = '',
    this.profile = '',
    this.birthPlace = '',
    this.gender = 'Male',
    this.walletAmount = 0,
    this.countryCode = '+91',
    this.isFreeChat = false,
    this.chatStatus = 'Online',
    this.callStatus = 'Online',
    this.charge = 25.0,
    this.videoCallRate = 50.0,
    this.reportRate = 199.0,
    this.emergencyChatCharge = 50.0,
    this.emergencyAudioCharge = 70.0,
    this.emergencyVideoCharge = 100.0,
    this.emergencyChatStatus = false,
    this.emergencyCallStatus = false,
    this.bankName,
    this.accountNumber,
    this.accountHolderName,
    this.ifscCode,
    this.bankBranch,
    this.accountType,
    this.upi,
    this.pancardNo,
    this.aadharNo,
  });

  int? id;
  String? name;
  String? contactNo;
  String? email;
  DateTime? birthDate;
  String? birthTime;
  String? profile;
  String? birthPlace;
  String? gender;
  double walletAmount;
  String? countryCode;
  bool isFreeChat;
  String chatStatus;
  String callStatus;
  double charge;
  double videoCallRate;
  double reportRate;
  double emergencyChatCharge;
  double emergencyAudioCharge;
  double emergencyVideoCharge;
  bool emergencyChatStatus;
  bool emergencyCallStatus;
  String? bankName;
  String? accountNumber;
  String? accountHolderName;
  String? ifscCode;
  String? bankBranch;
  String? accountType;
  String? upi;
  String? pancardNo;
  String? aadharNo;

  static bool _b(dynamic v) {
    if (v == null) return false;
    if (v is bool) return v;
    if (v is num) return v != 0;
    final s = v.toString().trim().toLowerCase();
    return s == '1' || s == 'true' || s == 'yes';
  }

  static double _d(dynamic v, [double def = 0.0]) {
    if (v == null) return def;
    if (v is num) return v.toDouble();
    return double.tryParse(v.toString()) ?? def;
  }

  User.fromJson(Map<String, dynamic> json)
      : id = json['id'],
        name = json['name'] ?? 'User',
        contactNo = json['contactNo'] ?? '',
        email = json['email'] ?? '',
        birthDate = json['birthDate'] == null
            ? null
            : DateTime.tryParse(json['birthDate'].toString()),
        birthTime = json['birthTime'] ?? '',
        profile = json['profile'] ?? json['profileImage'] ?? '',
        birthPlace = json['birthPlace'] ?? '',
        gender = json['gender'] ?? 'Male',
        walletAmount = double.tryParse(
                json['totalWalletAmount']?.toString() ?? '0') ??
            0,
        countryCode = json['countryCode'] ?? '+91',
        isFreeChat = _b(json['is_freechat']),
        chatStatus = json['chatStatus'] ?? 'Online',
        callStatus = json['callStatus'] ?? 'Online',
        charge = _d(json['charge'], 25.0),
        videoCallRate = _d(json['videoCallRate'], 50.0),
        reportRate = _d(json['reportRate'], 199.0),
        emergencyChatCharge = _d(json['emergency_chat_charge'], 50.0),
        emergencyAudioCharge = _d(json['emergency_audio_charge'], 70.0),
        emergencyVideoCharge = _d(json['emergency_video_charge'], 100.0),
        emergencyChatStatus = _b(json['emergencyChatStatus']),
        emergencyCallStatus = _b(json['emergencyCallStatus']),
        bankName = json['bankName'],
        accountNumber = json['accountNumber'],
        accountHolderName = json['accountHolderName'],
        ifscCode = json['ifscCode'],
        bankBranch = json['bankBranch'],
        accountType = json['accountType'],
        upi = json['upi'],
        pancardNo = json['pancardNo'],
        aadharNo = json['aadharNo'];

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'contactNo': contactNo,
        'email': email,
        'birthDate': birthDate?.toIso8601String(),
        'birthTime': birthTime,
        'profile': profile,
        'birthPlace': birthPlace,
        'gender': gender,
        'totalWalletAmount': walletAmount,
        'countryCode': countryCode,
        'is_freechat': isFreeChat,
        'chatStatus': chatStatus,
        'callStatus': callStatus,
        'charge': charge,
        'videoCallRate': videoCallRate,
        'reportRate': reportRate,
        'emergency_chat_charge': emergencyChatCharge,
        'emergency_audio_charge': emergencyAudioCharge,
        'emergency_video_charge': emergencyVideoCharge,
        'emergencyChatStatus': emergencyChatStatus,
        'emergencyCallStatus': emergencyCallStatus,
        'bankName': bankName,
        'accountNumber': accountNumber,
        'accountHolderName': accountHolderName,
        'ifscCode': ifscCode,
        'bankBranch': bankBranch,
        'accountType': accountType,
        'upi': upi,
        'pancardNo': pancardNo,
        'aadharNo': aadharNo,
      };

  String get displayName {
    final n = (name ?? '').trim();
    if (n.isEmpty || n.toLowerCase() == 'user') return 'Devotee';
    return n;
  }
}
