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

  User.fromJson(Map<String, dynamic> json)
      : id = json['id'],
        name = json['name'] ?? 'User',
        contactNo = json['contactNo'] ?? '',
        email = json['email'] ?? '',
        birthDate = json['birthDate'] == null
            ? null
            : DateTime.tryParse(json['birthDate'].toString()),
        birthTime = json['birthTime'] ?? '',
        profile = json['profile'] ?? '',
        birthPlace = json['birthPlace'] ?? '',
        gender = json['gender'] ?? 'Male',
        walletAmount = double.tryParse(
                json['totalWalletAmount']?.toString() ?? '0') ??
            0,
        countryCode = json['countryCode'] ?? '+91',
        isFreeChat = json['is_freechat'] ?? false,
        chatStatus = json['chatStatus'] ?? 'Online',
        callStatus = json['callStatus'] ?? 'Online';

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
      };

  String get displayName {
    final n = (name ?? '').trim();
    if (n.isEmpty || n.toLowerCase() == 'user') return 'Devotee';
    return n;
  }
}
