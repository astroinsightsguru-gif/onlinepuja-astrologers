import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';

/// Supported languages across OnlinePuja ecosystem
enum AppLanguage {
  en('English', 'en', 'English'),
  hi('हिन्दी', 'hi', 'Hindi'),
  gu('ગુજરાતી', 'gu', 'Gujarati'),
  mr('मराठी', 'mr', 'Marathi'),
  bn('বাংলা', 'bn', 'Bengali'),
  ta('தமிழ்', 'ta', 'Tamil'),
  te('తెలుగు', 'te', 'Telugu'),
  kn('ಕನ್ನಡ', 'kn', 'Kannada');

  final String label;
  final String code;
  final String englishName;

  const AppLanguage(this.label, this.code, this.englishName);

  static AppLanguage fromCode(String? code) {
    if (code == null) return AppLanguage.en;
    return AppLanguage.values.firstWhere(
      (e) => e.code == code.toLowerCase(),
      orElse: () => AppLanguage.en,
    );
  }
}

/// Global Reactive Localization Manager
class LocaleManager {
  LocaleManager._();
  static final LocaleManager instance = LocaleManager._();

  static const _kPrefLang = 'selected_app_language';
  final ValueNotifier<AppLanguage> currentLanguage =
      ValueNotifier<AppLanguage>(AppLanguage.en);

  Future<void> init() async {
    try {
      final sp = await SharedPreferences.getInstance();
      final code = sp.getString(_kPrefLang);
      if (code != null) {
        currentLanguage.value = AppLanguage.fromCode(code);
      }
    } catch (_) {}
  }

  Future<void> setLanguage(AppLanguage lang) async {
    currentLanguage.value = lang;
    try {
      final sp = await SharedPreferences.getInstance();
      await sp.setString(_kPrefLang, lang.code);
    } catch (_) {}
  }
}

/// Dictionary of translations for OnlinePuja
class AppStrings {
  AppStrings._();

  static AppLanguage get _lang => LocaleManager.instance.currentLanguage.value;

  static String tr(String key) {
    final map = _translations[key];
    if (map == null) return key;
    return map[_lang] ?? map[AppLanguage.en] ?? key;
  }

  // Common quick getters
  static String get appTitle => tr('app_title');
  static String get consult => tr('consult');
  static String get puja => tr('puja');
  static String get explore => tr('explore');
  static String get astrologers => tr('astrologers');
  static String get profile => tr('profile');
  static String get wallet => tr('wallet');
  static String get recharge => tr('recharge');
  static String get bookPuja => tr('book_puja');
  static String get kpCalendar => tr('kp_calendar');
  static String get rulingPlanets => tr('ruling_planets');
  static String get subLord => tr('sub_lord');
  static String get auspicious => tr('auspicious');
  static String get inauspicious => tr('inauspicious');
  static String get neutral => tr('neutral');
  static String get prashnaKundli => tr('prashna_kundli');
  static String get annadaan => tr('annadaan');
  static String get astroMall => tr('astro_mall');
  static String get dailyHoroscope => tr('daily_horoscope');
  static String get liveDarshan => tr('live_darshan');
  static String get selectLanguage => tr('select_language');
  static String get verifiedPandits => tr('verified_pandits');
  static String get skip => tr('skip');

  static final Map<String, Map<AppLanguage, String>> _translations = {
    'app_title': {
      AppLanguage.en: 'Online Puja',
      AppLanguage.hi: 'ऑनलाइन पूजा',
      AppLanguage.gu: 'ઓનલાઇન પૂજા',
      AppLanguage.mr: 'ऑनलाइन पूजा',
      AppLanguage.bn: 'অনলাইন পূজা',
      AppLanguage.ta: 'ஆன்லைன் பூஜை',
      AppLanguage.te: 'ఆన్‌లైన్ పూజ',
      AppLanguage.kn: 'ಆನ್‌ಲೈನ್ ಪೂಜೆ',
    },
    'consult': {
      AppLanguage.en: 'Consult',
      AppLanguage.hi: 'परामर्श',
      AppLanguage.gu: 'પરામર્શ',
      AppLanguage.mr: 'सल्ला घ्या',
      AppLanguage.bn: 'পরামর্শ',
      AppLanguage.ta: 'ஆலோசனை',
      AppLanguage.te: 'సంప్రదించండి',
      AppLanguage.kn: 'ಸಮಾಲೋಚನೆ',
    },
    'puja': {
      AppLanguage.en: 'Puja',
      AppLanguage.hi: 'पूजा',
      AppLanguage.gu: 'પૂજા',
      AppLanguage.mr: 'पूजा',
      AppLanguage.bn: 'পূজা',
      AppLanguage.ta: 'பூஜை',
      AppLanguage.te: 'పూజ',
      AppLanguage.kn: 'ಪೂಜೆ',
    },
    'explore': {
      AppLanguage.en: 'Explore',
      AppLanguage.hi: 'एक्सप्लोर',
      AppLanguage.gu: 'અન્વેષણ',
      AppLanguage.mr: 'शोधा',
      AppLanguage.bn: 'অন্বেষণ',
      AppLanguage.ta: 'ஆராயுங்கள்',
      AppLanguage.te: 'అన్వేషించండి',
      AppLanguage.kn: 'ಅನ್ವೇಷಿಸಿ',
    },
    'astrologers': {
      AppLanguage.en: 'Astrologers',
      AppLanguage.hi: 'ज्योतिषी',
      AppLanguage.gu: 'જ્યોતિષીઓ',
      AppLanguage.mr: 'ज्योतिषी',
      AppLanguage.bn: 'জ্যোতিষী',
      AppLanguage.ta: 'ஜோதிடர்கள்',
      AppLanguage.te: 'జ్యోతిష్యులు',
      AppLanguage.kn: 'ಜ್ಯೋತಿಷಿಗಳು',
    },
    'profile': {
      AppLanguage.en: 'Profile',
      AppLanguage.hi: 'प्रोफ़ाइल',
      AppLanguage.gu: 'પ્રોફાઇલ',
      AppLanguage.mr: 'प्रोफाइल',
      AppLanguage.bn: 'প্রোফাইল',
      AppLanguage.ta: 'சுயவிவரம்',
      AppLanguage.te: 'ప్రొఫైల్',
      AppLanguage.kn: 'ಪ್ರೊಫೈಲ್',
    },
    'wallet': {
      AppLanguage.en: 'Wallet',
      AppLanguage.hi: 'वॉलेट',
      AppLanguage.gu: 'વોલેટ',
      AppLanguage.mr: 'वॉलेट',
      AppLanguage.bn: 'ওয়ালেট',
      AppLanguage.ta: 'பணப்பை',
      AppLanguage.te: 'వాలెట్',
      AppLanguage.kn: 'ವಾಲೆಟ್',
    },
    'recharge': {
      AppLanguage.en: 'Recharge',
      AppLanguage.hi: 'रिचार्ज करें',
      AppLanguage.gu: 'રીચાર્જ કરો',
      AppLanguage.mr: 'रिचार्ज करा',
      AppLanguage.bn: 'রিচার্জ করুন',
      AppLanguage.ta: 'ரீசார்ஜ் செய்',
      AppLanguage.te: 'రీఛార్జ్ చేయండి',
      AppLanguage.kn: 'ರೀಚಾರ್ಜ್ ಮಾಡಿ',
    },
    'book_puja': {
      AppLanguage.en: 'Book Puja',
      AppLanguage.hi: 'पूजा बुक करें',
      AppLanguage.gu: 'પૂજા બુક કરો',
      AppLanguage.mr: 'पूजा बुक करा',
      AppLanguage.bn: 'পূজা বুক করুন',
      AppLanguage.ta: 'பூஜை முன்பதிவு',
      AppLanguage.te: 'పూజ బుక్ చేయండి',
      AppLanguage.kn: 'ಪೂಜೆ ಕಾಯ್ದಿರಿಸಿ',
    },
    'kp_calendar': {
      AppLanguage.en: 'KP Calendar & Sub-Lords',
      AppLanguage.hi: 'के.पी. कैलेंडर एवं उप-स्वामी',
      AppLanguage.gu: 'કે.પી. કેલેન્ડર અને ઉપ-સ્વામી',
      AppLanguage.mr: 'के.पी. कॅलेंडर आणि उप-स्वामी',
      AppLanguage.bn: 'কে.পি. ক্যালেন্ডার ও সাব-লর্ড',
      AppLanguage.ta: 'கே.பி. காலண்டர் & உப-அதிபதி',
      AppLanguage.te: 'కే.పీ. క్యాలెండర్ & ఉప-అధిపతి',
      AppLanguage.kn: 'ಕೆ.ಪಿ. ಕ್ಯಾಲೆಂಡರ್ ಮತ್ತು ಉಪ-ಅಧಿಪತಿ',
    },
    'ruling_planets': {
      AppLanguage.en: 'Ruling Planets (RP)',
      AppLanguage.hi: 'शासक / नियमक ग्रह (RP)',
      AppLanguage.gu: 'શાસક ગ્રહો (RP)',
      AppLanguage.mr: 'शासक ग्रह (RP)',
      AppLanguage.bn: 'শাসক গ্রহ (RP)',
      AppLanguage.ta: 'ஆளும் கிரகங்கள் (RP)',
      AppLanguage.te: 'పరిపాలక గ్రహాలు (RP)',
      AppLanguage.kn: 'ಆಡಳಿತ ಗ್ರಹಗಳು (RP)',
    },
    'sub_lord': {
      AppLanguage.en: 'Sub-Lord',
      AppLanguage.hi: 'उप-स्वामी (Sub-Lord)',
      AppLanguage.gu: 'ઉપ-સ્વામી',
      AppLanguage.mr: 'उप-स्वामी',
      AppLanguage.bn: 'সাব-লর্ড',
      AppLanguage.ta: 'உப-அதிபதி',
      AppLanguage.te: 'ఉప-అధిపతి',
      AppLanguage.kn: 'ಉಪ-ಅಧಿಪತಿ',
    },
    'auspicious': {
      AppLanguage.en: 'Auspicious (Shubh)',
      AppLanguage.hi: 'शुभ (उत्तम फलदायी)',
      AppLanguage.gu: 'શુભ (શ્રેષ્ઠ)',
      AppLanguage.mr: 'शुभ (उत्तम)',
      AppLanguage.bn: 'শুভ',
      AppLanguage.ta: 'சுபம் (நல்ல நேரம்)',
      AppLanguage.te: 'శుభం (మంచి సమయం)',
      AppLanguage.kn: 'ಶುಭ (ಉತ್ತಮ ಸಮಯ)',
    },
    'inauspicious': {
      AppLanguage.en: 'Inauspicious (Ashubh)',
      AppLanguage.hi: 'अशुभ (सतर्क रहें)',
      AppLanguage.gu: 'અશુભ (સાવચેત રહો)',
      AppLanguage.mr: 'अशुभ (सावध राहा)',
      AppLanguage.bn: 'অশুভ (সাবধান)',
      AppLanguage.ta: 'அசுபம் (எச்சரிக்கை)',
      AppLanguage.te: 'అశుభం (జాగ్రత్త)',
      AppLanguage.kn: 'ಅಶುಭ (ಎಚ್ಚರಿಕೆ)',
    },
    'neutral': {
      AppLanguage.en: 'Moderate / Mixed',
      AppLanguage.hi: 'मध्यम / सामान्य',
      AppLanguage.gu: 'મધ્યમ / સામાન્ય',
      AppLanguage.mr: 'मध्यम / सर्वसाधारण',
      AppLanguage.bn: 'মধ্যম / মিশ্র',
      AppLanguage.ta: 'மிதமான',
      AppLanguage.te: 'సాధారణం',
      AppLanguage.kn: 'ಸಾಮಾನ್ಯ',
    },
    'prashna_kundli': {
      AppLanguage.en: 'KP Horary / Prashna (1-249)',
      AppLanguage.hi: 'के.पी. प्रश्न कुण्डली (1-249)',
      AppLanguage.gu: 'કે.પી. પ્રશ્ન કુંડળી (1-249)',
      AppLanguage.mr: 'के.पी. प्रश्न कुंडली (1-249)',
      AppLanguage.bn: 'কে.পি. প্রশ্ন কুন্ডলী (১-২৪৯)',
      AppLanguage.ta: 'கே.பி. பிரஸ்ன ஜாதகம் (1-249)',
      AppLanguage.te: 'కే.పీ. ప్రశ్న కుండలి (1-249)',
      AppLanguage.kn: 'ಕೆ.ಪಿ. ಪ್ರಶ್ನ ಕುಂಡಲಿ (1-249)',
    },
    'annadaan': {
      AppLanguage.en: 'Annadaan & Seva',
      AppLanguage.hi: 'अन्नदान एवं सेवा',
      AppLanguage.gu: 'અન્નદાન અને સેવા',
      AppLanguage.mr: 'अन्नदान आणि सेवा',
      AppLanguage.bn: 'অন্নদান ও সেবা',
      AppLanguage.ta: 'அன்னதானம் மற்றும் சேவை',
      AppLanguage.te: 'అన్నదానం మరియు సేవ',
      AppLanguage.kn: 'ಅನ್ನದಾನ ಮತ್ತು ಸೇವೆ',
    },
    'astro_mall': {
      AppLanguage.en: 'Astro Mall & Gemstones',
      AppLanguage.hi: 'एस्ट्रो मॉल एवं रत्न',
      AppLanguage.gu: 'એસ્ટ્રો મોલ અને રત્નો',
      AppLanguage.mr: 'अ‍ॅस्ट्रो मॉल आणि रत्ने',
      AppLanguage.bn: 'অ্যাস্ট্রো মল ও রত্ন',
      AppLanguage.ta: 'ஆஸ்ட்ரோ மால் & ரத்தினங்கள்',
      AppLanguage.te: 'ఆస్ట్రో మాల్ & రత్నాలు',
      AppLanguage.kn: 'ಆಸ್ಟ್ರೋ ಮಾಲ್ & ರತ್ನಗಳು',
    },
    'daily_horoscope': {
      AppLanguage.en: 'Daily Horoscope',
      AppLanguage.hi: 'दैनिक राशिफल',
      AppLanguage.gu: 'દૈનિક રાશિફળ',
      AppLanguage.mr: 'दैनिक राशीभविष्य',
      AppLanguage.bn: 'দৈনিক রাশিফল',
      AppLanguage.ta: 'தினசரி ராசிபலன்',
      AppLanguage.te: 'రోజువారీ రాశిఫలాలు',
      AppLanguage.kn: 'ದೈನಂದಿನ ರಾಶಿಫಲ',
    },
    'live_darshan': {
      AppLanguage.en: 'Live Mandir Darshan',
      AppLanguage.hi: 'लाइव मंदिर दर्शन',
      AppLanguage.gu: 'લાઈવ મંદિર દર્શન',
      AppLanguage.mr: 'थेट मंदिर दर्शन',
      AppLanguage.bn: 'লাইভ মন্দির দর্শন',
      AppLanguage.ta: 'நேரலை கோவில் தரிசனம்',
      AppLanguage.te: 'ప్రత్యక్ష ఆలయ దర్శనం',
      AppLanguage.kn: 'ನೇರ ದೇವಾಲಯ ದರ್ಶನ',
    },
    'select_language': {
      AppLanguage.en: 'Select Language',
      AppLanguage.hi: 'भाषा चुनें',
      AppLanguage.gu: 'ભાષા પસંદ કરો',
      AppLanguage.mr: 'भाषा निवडा',
      AppLanguage.bn: 'ভাষা নির্বাচন করুন',
      AppLanguage.ta: 'மொழியைத் தேர்ந்தெடுக்கவும்',
      AppLanguage.te: 'భాషను ఎంచుకోండి',
      AppLanguage.kn: 'ಭಾಷೆಯನ್ನು ಆಯ್ಕೆಮಾಡಿ',
    },
    'verified_pandits': {
      AppLanguage.en: 'Verified Vedic Pandits & Acharyas',
      AppLanguage.hi: 'सत्यापित वैदिक पंडित एवं आचार्य',
      AppLanguage.gu: 'ચકાસાયેલ વૈદિક પંડિતો અને આચાર્યો',
      AppLanguage.mr: 'सत्यापित वैदिक पंडित आणि आचार्य',
      AppLanguage.bn: 'যাচাইকৃত বৈদিক পণ্ডিত ও আচার্যগণ',
      AppLanguage.ta: 'சரிபார்க்கப்பட்ட வேத பண்டிதர்கள்',
      AppLanguage.te: 'ధృవీకరించబడిన వేద పండితులు',
      AppLanguage.kn: 'ಪರಿಶೀಲಿಸಿದ ವೈದಿಕ ಪಂಡಿತರು',
    },
    'skip': {
      AppLanguage.en: 'Skip',
      AppLanguage.hi: 'छोड़ें',
      AppLanguage.gu: 'છોડો',
      AppLanguage.mr: 'वगळा',
      AppLanguage.bn: 'এড়িয়ে যান',
      AppLanguage.ta: 'தவிர்',
      AppLanguage.te: 'దాటవేయి',
      AppLanguage.kn: 'ಬಿಟ್ಟುಬಿಡಿ',
    },
  };
}
