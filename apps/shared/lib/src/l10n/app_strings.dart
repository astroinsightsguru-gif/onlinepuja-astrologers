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

/// High-Quality Vedic & Devotional Dictionary for OnlinePuja.live
class AppStrings {
  AppStrings._();

  static AppLanguage get _lang => LocaleManager.instance.currentLanguage.value;

  static String tr(String key) {
    final map = _translations[key];
    if (map == null) return key;
    return map[_lang] ?? map[AppLanguage.en] ?? key;
  }

  // Quick Getters for Core Navigation & Headers
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

  // Extended Vedic & Sanctum Getters
  static String get freeKundli => tr('free_kundli');
  static String get kundliMatching => tr('kundli_matching');
  static String get panchang => tr('panchang');
  static String get horoscope => tr('horoscope');
  static String get onlinePujaAi => tr('online_puja_ai');
  static String get cosmicAi => onlinePujaAi;
  static String get japaMala => tr('japa_mala');
  static String get mySankalp => tr('my_sankalp');
  static String get choghadiyaRadar => tr('choghadiya_radar');
  static String get shubhMuhuratActive => tr('shubh_muhurat_active');
  static String get searchPlaceholder => tr('search_placeholder');
  static String get vedicSanctumTitle => tr('vedic_sanctum_title');
  static String get talkChatAstrologers => tr('talk_chat_astrologers');
  static String get sacredPujasChadhava => tr('sacred_pujas_chadhava');
  static String get viewAll => tr('view_all');
  static String get participate => tr('participate');
  static String get online => tr('online');
  static String get firstConsultOffer => tr('first_consult_offer');
  static String get choosePackage => tr('choose_package');
  static String get significanceVidhi => tr('significance_vidhi');
  static String get divineBenefits => tr('divine_benefits');

  static final Map<String, Map<AppLanguage, String>> _translations = {
    'app_title': {
      AppLanguage.en: 'OnlinePuja.live',
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
      AppLanguage.hi: 'साधना व संस्कार',
      AppLanguage.gu: 'સાધના અને અન્વેષણ',
      AppLanguage.mr: 'संस्कार व साधन',
      AppLanguage.bn: 'অনুসন্ধান ও সাধনা',
      AppLanguage.ta: 'ஆன்மீக வழிபாடுகள்',
      AppLanguage.te: 'సాధన & అన్వేషణ',
      AppLanguage.kn: 'ಸಾಧನೆ ಮತ್ತು ಅನ್ವೇಷಣೆ',
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
      AppLanguage.hi: 'मेरी प्रोफाइल',
      AppLanguage.gu: 'મારી પ્રોફાઇલ',
      AppLanguage.mr: 'माझी प्रोफाइल',
      AppLanguage.bn: 'আমার প্রোফাইল',
      AppLanguage.ta: 'சுயவிவரம்',
      AppLanguage.te: 'నా ప్రొఫైల్',
      AppLanguage.kn: 'ನನ್ನ ಪ್ರೊಫೈಲ್',
    },
    'wallet': {
      AppLanguage.en: 'Wallet',
      AppLanguage.hi: 'दक्षिणा वॉलेट',
      AppLanguage.gu: 'દક્ષિણા વોલેટ',
      AppLanguage.mr: 'दक्षिणा वॉलेट',
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
      AppLanguage.hi: 'पूजा संकल्प लें',
      AppLanguage.gu: 'પૂજા સંકલ્પ લો',
      AppLanguage.mr: 'पूजा संकल्प करा',
      AppLanguage.bn: 'পূজা সংকল্প নিন',
      AppLanguage.ta: 'பூஜை முன்பதிவு',
      AppLanguage.te: 'పూజ సంకల్పం చేయండి',
      AppLanguage.kn: 'ಪೂಜಾ ಸಂಕಲ್ಪ ಮಾಡಿ',
    },
    'kp_calendar': {
      AppLanguage.en: 'KP Calendar & Sub-Lords',
      AppLanguage.hi: 'के.पी. पंचांग एवं उप-स्वामी',
      AppLanguage.gu: 'કે.પી. કેલેન્ડર અને ઉપ-સ્વામી',
      AppLanguage.mr: 'के.पी. कॅलेंडर आणि उप-स्वामी',
      AppLanguage.bn: 'কে.পি. ক্যালেন্ডার ও সাব-লর্ড',
      AppLanguage.ta: 'கே.பி. காலண்டர் & உப-அதிபதி',
      AppLanguage.te: 'కే.పీ. క్యాలెండర్ & ఉప-అధిపతి',
      AppLanguage.kn: 'ಕೆ.ಪಿ. ಕ್ಯಾಲೆಂಡರ್ ಮತ್ತು ಉಪ-ಅಧಿಪತಿ',
    },
    'ruling_planets': {
      AppLanguage.en: 'Ruling Planets (RP)',
      AppLanguage.hi: 'शासक ग्रह (Ruling Planets)',
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
      AppLanguage.hi: 'शुभ (अमृत काल)',
      AppLanguage.gu: 'શુભ (શ્રેષ્ઠ)',
      AppLanguage.mr: 'शुभ (उत्तम काळ)',
      AppLanguage.bn: 'শুভ (অমৃত কাল)',
      AppLanguage.ta: 'சுபம் (நல்ல நேரம்)',
      AppLanguage.te: 'శుభం (మంచి సమయం)',
      AppLanguage.kn: 'ಶುಭ (ಉತ್ತಮ ಸಮಯ)',
    },
    'inauspicious': {
      AppLanguage.en: 'Inauspicious (Ashubh)',
      AppLanguage.hi: 'अशुभ (वर्जित काल)',
      AppLanguage.gu: 'અશુભ (સાવચેત રહો)',
      AppLanguage.mr: 'अशुभ (वर्ज्य काळ)',
      AppLanguage.bn: 'অশুভ (বর্জিত সময়)',
      AppLanguage.ta: 'அசுபம் (எச்சரிக்கை)',
      AppLanguage.te: 'అశుభం (జాగ్రత్త)',
      AppLanguage.kn: 'ಅಶುಭ (ಎಚ್ಚರಿಕೆ)',
    },
    'neutral': {
      AppLanguage.en: 'Moderate / Mixed',
      AppLanguage.hi: 'मध्यम (सामान्य फल)',
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
      AppLanguage.hi: 'अन्नदान एवं गौ सेवा',
      AppLanguage.gu: 'અન્નદાન અને ગૌ સેવા',
      AppLanguage.mr: 'अन्नदान व गोसेवा',
      AppLanguage.bn: 'অন্নদান ও গোসেবা',
      AppLanguage.ta: 'அன்னதானம் & கோசேவை',
      AppLanguage.te: 'అన్నదానం & గోసేవ',
      AppLanguage.kn: 'ಅನ್ನದಾನ ಮತ್ತು ಗೋಸೇವೆ',
    },
    'astro_mall': {
      AppLanguage.en: 'Astro Mall & Gemstones',
      AppLanguage.hi: 'एस्ट्रोमॉल (रत्न व उपाय)',
      AppLanguage.gu: 'એસ્ટ્રો મોલ અને રત્નો',
      AppLanguage.mr: 'अ‍ॅस्ट्रो मॉल व सिद्ध रत्ने',
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
      AppLanguage.hi: 'लाइव मन्दिर दर्शन',
      AppLanguage.gu: 'લાઈવ મંદિર દર્શન',
      AppLanguage.mr: 'थेट मंदिर दर्शन',
      AppLanguage.bn: 'লাইভ মন্দির দর্শন',
      AppLanguage.ta: 'நேரலை கோவில் தரிசனம்',
      AppLanguage.te: 'ప్రత్యక్ష ఆలయ దర్శనం',
      AppLanguage.kn: 'ನೇರ ದೇವಾಲಯ ದರ್ಶನ',
    },
    'select_language': {
      AppLanguage.en: 'Select Language',
      AppLanguage.hi: 'पवित्र भाषा चुनें',
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
      AppLanguage.hi: 'आगे बढ़ें',
      AppLanguage.gu: 'આગળ વધો',
      AppLanguage.mr: 'पुढे जा',
      AppLanguage.bn: 'এড়িয়ে যান',
      AppLanguage.ta: 'தவிர்',
      AppLanguage.te: 'దాటవేయి',
      AppLanguage.kn: 'ಮುಂದುವರಿಯಿರಿ',
    },
    'free_kundli': {
      AppLanguage.en: 'Free Kundli',
      AppLanguage.hi: 'निःशुल्क कुण्डली',
      AppLanguage.gu: 'મફત કુંડળી',
      AppLanguage.mr: 'मोफत पत्रिका',
      AppLanguage.bn: 'বিনামূল্যে কুষ্ঠি',
      AppLanguage.ta: 'இலவச ஜாதகம்',
      AppLanguage.te: 'ఉచిత కుండలి',
      AppLanguage.kn: 'ಉಚಿತ ಕುಂಡಲಿ',
    },
    'kundli_matching': {
      AppLanguage.en: 'Kundli Match',
      AppLanguage.hi: 'गुण मिलान',
      AppLanguage.gu: 'ગુણ મિલન',
      AppLanguage.mr: 'गुणमेलन',
      AppLanguage.bn: 'যোটক বিচার',
      AppLanguage.ta: 'பொருத்தம்',
      AppLanguage.te: 'గుణ మేళనం',
      AppLanguage.kn: 'ಗುಣ ಮಿಲನ',
    },
    'panchang': {
      AppLanguage.en: 'Panchang',
      AppLanguage.hi: 'दैनिक पंचांग',
      AppLanguage.gu: 'દૈનિક પંચાંગ',
      AppLanguage.mr: 'दैनिक पंचांग',
      AppLanguage.bn: 'পঞ্জিকা',
      AppLanguage.ta: 'பஞ்சாங்கம்',
      AppLanguage.te: 'పంచాంగం',
      AppLanguage.kn: 'ಪಂಚಾಂಗ',
    },
    'horoscope': {
      AppLanguage.en: 'Horoscope',
      AppLanguage.hi: 'राशिफल',
      AppLanguage.gu: 'રાશિફળ',
      AppLanguage.mr: 'राशीभविष्य',
      AppLanguage.bn: 'রাশিফল',
      AppLanguage.ta: 'ராசிபலன்',
      AppLanguage.te: 'రాశిఫలాలు',
      AppLanguage.kn: 'ರಾಶಿಫಲ',
    },
    'online_puja_ai': {
      AppLanguage.en: 'OnlinePuja AI',
      AppLanguage.hi: 'ऑनलाइन पूजा AI गुरु',
      AppLanguage.gu: 'ઓનલાઇન પૂજા AI ગુરુ',
      AppLanguage.mr: 'ऑनलाइन पूजा AI गुरू',
      AppLanguage.bn: 'অনলাইন পূজা এআই গুরু',
      AppLanguage.ta: 'ஆன்லைன் பூஜை AI',
      AppLanguage.te: 'ఆన్‌లైన్ పూజ AI',
      AppLanguage.kn: 'ಆನ್‌ಲೈನ್ ಪೂಜೆ AI',
    },
    'cosmic_ai': {
      AppLanguage.en: 'OnlinePuja AI',
      AppLanguage.hi: 'ऑनलाइन पूजा AI गुरु',
      AppLanguage.gu: 'ઓનલાઇન પૂજા AI ગુરુ',
      AppLanguage.mr: 'ऑनलाइन पूजा AI गुरू',
      AppLanguage.bn: 'অনলাইন পূজা এআই গুরু',
      AppLanguage.ta: 'ஆன்லைன் பூஜை AI',
      AppLanguage.te: 'ఆన్‌లైన్ పూజ AI',
      AppLanguage.kn: 'ಆನ್‌ಲೈನ್ ಪೂಜೆ AI',
    },
    'japa_mala': {
      AppLanguage.en: 'Japa Mala',
      AppLanguage.hi: '१०८ जप माला',
      AppLanguage.gu: '૧૦૮ જપ માળા',
      AppLanguage.mr: '१०८ जप माळ',
      AppLanguage.bn: '১০৮ জপ মালা',
      AppLanguage.ta: '108 ஜப மாலை',
      AppLanguage.te: '108 జపమాల',
      AppLanguage.kn: '108 ಜಪ ಮಾಲೆ',
    },
    'my_sankalp': {
      AppLanguage.en: 'My Sankalp',
      AppLanguage.hi: 'मेरा संकल्प',
      AppLanguage.gu: 'મારો સંકલ્પ',
      AppLanguage.mr: 'माझा संकल्प',
      AppLanguage.bn: 'আমার সংকল্প',
      AppLanguage.ta: 'எனது சங்கல்பம்',
      AppLanguage.te: 'నా సంకల్పం',
      AppLanguage.kn: 'ನನ್ನ ಸಂಕಲ್ಪ',
    },
    'choghadiya_radar': {
      AppLanguage.en: 'Choghadiya Radar',
      AppLanguage.hi: 'चौघड़िया मुहूर्त',
      AppLanguage.gu: 'ચોઘડિયા રડાર',
      AppLanguage.mr: 'चौघडिया मुहूर्त',
      AppLanguage.bn: 'চৌঘড়িয়া মুহূর্ত',
      AppLanguage.ta: 'சோகடியா முஹூர்த்தம்',
      AppLanguage.te: 'చౌఘడియా ముహూర్తం',
      AppLanguage.kn: 'ಚೌಘಡಿಯಾ ಮುಹೂರ್ತ',
    },
    'shubh_muhurat_active': {
      AppLanguage.en: "Today's Shubh Muhurat: Amrit Kaal Active",
      AppLanguage.hi: 'आज का शुभ मुहूर्त: अमृत काल सक्रिय',
      AppLanguage.gu: 'આજનો શુભ મુહૂર્ત: અમૃત કાળ સક્રિય',
      AppLanguage.mr: 'आजचा शुभ मुहूर्त: अमृत काळ सक्रिय',
      AppLanguage.bn: 'আজকের শুভ মুহূর্ত: অমৃত কাল সক্রিয়',
      AppLanguage.ta: 'இன்றைய சுப முஹூர்த்தம்: அம்ரித காலம்',
      AppLanguage.te: 'నేటి శుభ ముహూర్తం: అమృత కాలం',
      AppLanguage.kn: 'ಇಂದಿನ ಶುಭ ಮುಹೂರ್ತ: ಅಮೃತ ಕಾಲ',
    },
    'search_placeholder': {
      AppLanguage.en: 'Search verified astrologers, pujas, kundli…',
      AppLanguage.hi: 'ज्योतिषी, पूजा, कुण्डली खोजें…',
      AppLanguage.gu: 'જ્યોતિષીઓ, પૂજા, કુંડળી શોધો…',
      AppLanguage.mr: 'ज्योतिषी, पूजा, कुंडली शोधा…',
      AppLanguage.bn: 'জ্যোতিষী, পূজা, কুষ্ঠি খুঁজুন…',
      AppLanguage.ta: 'ஜோதிடர்கள், பூஜைகள், ஜாதகம் தேடுங்கள்…',
      AppLanguage.te: 'జ్యోతిష్యులు, పూజలు, కుండలి శోధించండి…',
      AppLanguage.kn: 'ಜ್ಯೋತಿಷಿಗಳು, ಪೂಜೆಗಳು, ಕುಂಡಲಿ ಹುಡುಕಿ…',
    },
    'vedic_sanctum_title': {
      AppLanguage.en: 'Vedic Sanctum & Daily Rituals',
      AppLanguage.hi: 'वैदिक अनुष्ठान एवं नित्य साधन',
      AppLanguage.gu: 'વૈદિક અનુષ્ઠાન અને દૈનિક સાધન',
      AppLanguage.mr: 'वैदिक विधी व नित्य साधना',
      AppLanguage.bn: 'বৈদিক পূজা ও নিত্যকর্ম',
      AppLanguage.ta: 'வேத சடங்குகள் & தினசரி வழிபாடுகள்',
      AppLanguage.te: 'వైదిక ఆచారాలు & నిత్య పూజలు',
      AppLanguage.kn: 'ವೈದಿಕ ವಿಧಿಗಳು ಮತ್ತು ದೈನಂದಿನ ಆಚರಣೆಗಳು',
    },
    'talk_chat_astrologers': {
      AppLanguage.en: 'Talk & Chat with Astrologers',
      AppLanguage.hi: 'विद्वान ज्योतिषियों से बात व चैट करें',
      AppLanguage.gu: 'જ્યોતિષીઓ સાથે વાત અને ચેટ કરો',
      AppLanguage.mr: 'ज्योतिषींशी थेट बोला व चॅट करा',
      AppLanguage.bn: 'জ্যোতিষীদের সাথে কথা ও চ্যাট করুন',
      AppLanguage.ta: 'ஜோதிடர்களுடன் பேசவும் & அரட்டையடிக்கவும்',
      AppLanguage.te: 'జ్యోతిష్యులతో మాట్లాడండి & చాట్ చేయండి',
      AppLanguage.kn: 'ಜ್ಯೋತಿಷಿಗಳೊಂದಿಗೆ ಮಾತನಾಡಿ ಮತ್ತು ಚಾಟ್ ಮಾಡಿ',
    },
    'sacred_pujas_chadhava': {
      AppLanguage.en: 'Sacred Pujas & Chadhava',
      AppLanguage.hi: 'पवित्र पूजा एवं चढ़ावा',
      AppLanguage.gu: 'પવિત્ર પૂજા અને ચઢાવો',
      AppLanguage.mr: 'पवित्र पूजा आणि अर्पण',
      AppLanguage.bn: 'পবিত্র পূজা ও নৈবেদ্য',
      AppLanguage.ta: 'புனித பூஜைகள் & காணிக்கை',
      AppLanguage.te: 'పవిత్ర పూజలు & కానుకలు',
      AppLanguage.kn: 'ಪವಿತ್ರ ಪೂಜೆಗಳು ಮತ್ತು ಕಾಣಿಕೆ',
    },
    'view_all': {
      AppLanguage.en: 'View All',
      AppLanguage.hi: 'सभी देखें',
      AppLanguage.gu: 'બધા જુઓ',
      AppLanguage.mr: 'सर्व पहा',
      AppLanguage.bn: 'সব দেখুন',
      AppLanguage.ta: 'அனைத்தையும் காண்க',
      AppLanguage.te: 'అన్నీ చూడండి',
      AppLanguage.kn: 'ಎಲ್ಲವನ್ನೂ ವೀಕ್ಷಿಸಿ',
    },
    'participate': {
      AppLanguage.en: 'PARTICIPATE',
      AppLanguage.hi: 'संकल्प लें',
      AppLanguage.gu: 'સંકલ્પ લો',
      AppLanguage.mr: 'संकल्प करा',
      AppLanguage.bn: 'সংকল্প নিন',
      AppLanguage.ta: 'பங்கேற்கவும்',
      AppLanguage.te: 'పాల్గొనండి',
      AppLanguage.kn: 'ಭಾಗವಹಿಸಿ',
    },
    'online': {
      AppLanguage.en: 'Online',
      AppLanguage.hi: 'उपलब्ध',
      AppLanguage.gu: 'ઓનલાઈન',
      AppLanguage.mr: 'उपलब्ध',
      AppLanguage.bn: 'অনলাইন',
      AppLanguage.ta: 'ஆன்லைன்',
      AppLanguage.te: 'ఆన్‌లైన్',
      AppLanguage.kn: 'ಆನ್‌ಲೈನ್',
    },
    'first_consult_offer': {
      AppLanguage.en: 'FIRST CONSULTATION AT ₹1 ONLY',
      AppLanguage.hi: 'प्रथम परामर्श मात्र ₹१ में',
      AppLanguage.gu: 'પ્રથમ પરામર્શ માત્ર ₹૧ માં',
      AppLanguage.mr: 'पहिले संभाषण फक्त ₹१ मध्ये',
      AppLanguage.bn: 'প্রথম পরামর্শ মাত্র ₹১ তে',
      AppLanguage.ta: 'முதல் ஆலோசனை வெறும் ₹1 மட்டுமே',
      AppLanguage.te: 'మొదటి సంప్రదింపు కేవలం ₹1 మాత్రమే',
      AppLanguage.kn: 'ಮೊದಲ ಸಮಾಲೋಚನೆ ಕೇವಲ ₹1 ಮಾತ್ರ',
    },
    'choose_package': {
      AppLanguage.en: 'Choose Your Puja Package',
      AppLanguage.hi: 'पूजा सेवा पैकेज चुनें',
      AppLanguage.gu: 'પૂજા સેવા પેકેજ પસંદ કરો',
      AppLanguage.mr: 'पूजा सेवा पॅकेज निवडा',
      AppLanguage.bn: 'পূজা সেবা প্যাকেজ নির্বাচন করুন',
      AppLanguage.ta: 'பூஜை தொகுப்பைத் தேர்ந்தெடுக்கவும்',
      AppLanguage.te: 'పూజా ప్యాకేజీని ఎంచుకోండి',
      AppLanguage.kn: 'ಪೂಜಾ ಪ್ಯಾಕೇಜ್ ಆಯ್ಕೆಮಾಡಿ',
    },
    'significance_vidhi': {
      AppLanguage.en: 'Significance & Vidhi',
      AppLanguage.hi: 'महत्व एवं पूजा विधि',
      AppLanguage.gu: 'મહત્વ અને પૂજા વિધિ',
      AppLanguage.mr: 'महत्त्व व पूजा विधी',
      AppLanguage.bn: 'তাৎপর্য ও পূজা বিধি',
      AppLanguage.ta: 'முக்கியத்துவம் & பூஜா முறை',
      AppLanguage.te: 'ప్రాముఖ్యత & పూజా విధానం',
      AppLanguage.kn: 'ಮಹತ್ವ ಮತ್ತು ಪೂಜಾ ವಿಧಾನ',
    },
    'divine_benefits': {
      AppLanguage.en: 'Divine Blessings & Benefits',
      AppLanguage.hi: 'दिव्य कृपा एवं लाभ',
      AppLanguage.gu: 'દિવ્ય કૃપા અને લાભ',
      AppLanguage.mr: 'दिव्य आशीर्वाद व लाभ',
      AppLanguage.bn: 'দিব্য আশীর্বাদ ও সুফল',
      AppLanguage.ta: 'தெய்வீக ஆசிகள் & நன்மைகள்',
      AppLanguage.te: 'దివ్య ఆశీస్సులు & ప్రయోజనాలు',
      AppLanguage.kn: 'ದೈವಿಕ ಆಶೀರ್ವಾದಗಳು ಮತ್ತು ಪ್ರಯೋಜನಗಳು',
    },
  };
}
