/// One backend `systemFlag` row: name/value pair describing runtime config
/// (currency, app name, payment keys, feature toggles...).
class SystemFlag {
  SystemFlag({this.name, this.value, this.displayName});

  String? name;
  String? value;
  String? displayName;

  SystemFlag.fromJson(Map<String, dynamic> json) {
    name = json['name'] ?? '';
    value = json['value'] ?? '';
    displayName = json['displayName'] ?? '';
  }

  Map<String, dynamic> toJson() => {
        'name': name,
        'value': value,
        'displayName': displayName,
      };
}

/// Map-like accessor over the flag list returned with the user session.
class SystemFlags {
  SystemFlags(Iterable<SystemFlag> flags) : _flags = List.of(flags);

  final List<SystemFlag> _flags;

  static final SystemFlags empty = SystemFlags(const []);

  /// Raw rows (for persistence).
  List<Map<String, dynamic>> toRaw() =>
      _flags.map((f) => f.toJson()).toList();

  String call(String name, [String fallback = '']) {
    for (final f in _flags) {
      if (f.name == name) return f.value ?? fallback;
    }
    return fallback;
  }

  String get appName => call('AppName', 'Online Puja');
  String get currency => call('currency', '₹');
  String get appVersion => call('appVersion', '1.0.0');
  String get walletType => call('walletType', 'Wallet');
  bool get freeKundli => call('freeKundli', '1') == '1';
  bool get kundliMatching => call('kundliMatching', '1') == '1';
  bool get dailyHoroscope => call('dailyHoroscope', '1') == '1';
  bool get puja => call('puja', '1') == '1';
  bool get astromall => call('astromall', '1') == '1';
  bool get blog => call('bloc', '1') == '1';
  bool get panchang => call('todayPanchang', '1') == '1';
  String get supportPhone => call('supportPhone', call('contactNo', '+91 70071 58014'));
  String get supportWhatsapp => call('supportWhatsapp', call('whatsappNo', '917007158014'));
}
