import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:op_shared/op_shared.dart';
import 'package:onlinepuja_customer/state/app_session.dart';
import 'package:onlinepuja_customer/ui/screens/home/consult_home_screen.dart';
import 'package:onlinepuja_customer/ui/screens/main_shell.dart';
import 'package:provider/provider.dart';

import 'package:shared_preferences/shared_preferences.dart';

void main() {
  setUp(() async {
    SharedPreferences.setMockInitialValues({});
    await LocaleManager.instance.setLanguage(AppLanguage.en);
  });

  test('AppStrings provides high-quality authentic Vedic translations across all 8 languages', () {
    // English verification
    expect(AppStrings.consult, 'Consult');
    expect(AppStrings.freeKundli, 'Free Kundli');
    expect(AppStrings.vedicSanctumTitle, 'Vedic Sanctum & Daily Rituals');

    // Hindi verification (Polite, authentic spiritual Sanskritized Hindi)
    LocaleManager.instance.setLanguage(AppLanguage.hi);
    expect(AppStrings.consult, 'परामर्श');
    expect(AppStrings.puja, 'पूजा');
    expect(AppStrings.explore, 'साधना व संस्कार');
    expect(AppStrings.astrologers, 'ज्योतिषी');
    expect(AppStrings.profile, 'मेरी प्रोफाइल');
    expect(AppStrings.freeKundli, 'निःशुल्क कुण्डली');
    expect(AppStrings.kundliMatching, 'गुण मिलान');
    expect(AppStrings.panchang, 'दैनिक पंचांग');
    expect(AppStrings.vedicSanctumTitle, 'वैदिक अनुष्ठान एवं नित्य साधन');
    expect(AppStrings.talkChatAstrologers, 'विद्वान ज्योतिषियों से बात व चैट करें');
    expect(AppStrings.sacredPujasChadhava, 'पवित्र पूजा एवं चढ़ावा');
    expect(AppStrings.participate, 'संकल्प लें');
    expect(AppStrings.shubhMuhuratActive, 'आज का शुभ मुहूर्त: अमृत काल सक्रिय');

    // Gujarati verification
    LocaleManager.instance.setLanguage(AppLanguage.gu);
    expect(AppStrings.freeKundli, 'મફત કુંડળી');
    expect(AppStrings.kundliMatching, 'ગુણ મિલન');
    expect(AppStrings.vedicSanctumTitle, 'વૈદિક અનુષ્ઠાન અને દૈનિક સાધન');

    // Marathi verification
    LocaleManager.instance.setLanguage(AppLanguage.mr);
    expect(AppStrings.freeKundli, 'मोफत पत्रिका');
    expect(AppStrings.kundliMatching, 'गुणमेलन');
    expect(AppStrings.vedicSanctumTitle, 'वैदिक विधी व नित्य साधना');

    // Bengali verification
    LocaleManager.instance.setLanguage(AppLanguage.bn);
    expect(AppStrings.freeKundli, 'বিনামূল্যে কুষ্ঠি');
    expect(AppStrings.kundliMatching, 'যোটক বিচার');

    // Tamil verification
    LocaleManager.instance.setLanguage(AppLanguage.ta);
    expect(AppStrings.freeKundli, 'இலவச ஜாதகம்');
    expect(AppStrings.kundliMatching, 'பொருத்தம்');

    // Telugu verification
    LocaleManager.instance.setLanguage(AppLanguage.te);
    expect(AppStrings.freeKundli, 'ఉచిత కుండలి');
    expect(AppStrings.kundliMatching, 'గుణ మేళనం');

    // Kannada verification
    LocaleManager.instance.setLanguage(AppLanguage.kn);
    expect(AppStrings.freeKundli, 'ಉಚಿತ ಕುಂಡಲಿ');
    expect(AppStrings.kundliMatching, 'ಗುಣ ಮಿಲನ');

    // Sanskrit verification
    LocaleManager.instance.setLanguage(AppLanguage.sa);
    expect(AppStrings.consult, 'परामर्शः');
    expect(AppStrings.freeKundli, 'निःशुल्क कुण्डली');

    // Malayalam verification
    LocaleManager.instance.setLanguage(AppLanguage.ml);
    expect(AppStrings.consult, 'കൂടിയാലോചന');
    expect(AppStrings.freeKundli, 'സൗജന്യ ജാതകം');

    // Odia verification
    LocaleManager.instance.setLanguage(AppLanguage.or);
    expect(AppStrings.consult, 'ପରାମର୍ଶ');
    expect(AppStrings.freeKundli, 'ମାଗଣା କୁଣ୍ଡଳୀ');

    // Punjabi verification
    LocaleManager.instance.setLanguage(AppLanguage.pa);
    expect(AppStrings.consult, 'ਸਲਾਹ ਲਵੋ');
    expect(AppStrings.freeKundli, 'ਮੁਫ਼ਤ ਕੁੰਡਲੀ');

    // Assamese verification
    LocaleManager.instance.setLanguage(AppLanguage.as_);
    expect(AppStrings.consult, 'পৰামৰ্শ');
    expect(AppStrings.freeKundli, 'বিনামূলীয়া কুণ্ডলী');

    // Bhojpuri verification
    LocaleManager.instance.setLanguage(AppLanguage.bho);
    expect(AppStrings.consult, 'सलाह लीं');
    expect(AppStrings.freeKundli, 'मुफ्त कुण्डली');

    // Maithili verification
    LocaleManager.instance.setLanguage(AppLanguage.mai);
    expect(AppStrings.consult, 'विमर्श');
    expect(AppStrings.freeKundli, 'मुफ्त कुण्डली');
  });

  testWidgets('Widgets bound to LocaleManager reactively update when language switches',
      (tester) async {
    await LocaleManager.instance.setLanguage(AppLanguage.en);

    await tester.pumpWidget(
      MaterialApp(
        home: Scaffold(
          body: ValueListenableBuilder<AppLanguage>(
            valueListenable: LocaleManager.instance.currentLanguage,
            builder: (context, lang, _) {
              return Column(
                children: [
                  Text(AppStrings.consult),
                  Text(AppStrings.puja),
                  Text(AppStrings.astrologers),
                  Text(AppStrings.freeKundli),
                  Text(AppStrings.shubhMuhuratActive),
                  Text(AppStrings.vedicSanctumTitle),
                ],
              );
            },
          ),
        ),
      ),
    );
    await tester.pump();

    // 1. Initial English assertions
    expect(find.text('Consult'), findsOneWidget);
    expect(find.text('Puja'), findsOneWidget);
    expect(find.text('Astrologers'), findsOneWidget);
    expect(find.text('Free Kundli'), findsOneWidget);
    expect(find.text("Today's Shubh Muhurat: Amrit Kaal Active"), findsOneWidget);
    expect(find.text('Vedic Sanctum & Daily Rituals'), findsOneWidget);

    // 2. Switch to Sanskritized Hindi
    await LocaleManager.instance.setLanguage(AppLanguage.hi);
    await tester.pump();

    expect(find.text('परामर्श'), findsOneWidget);
    expect(find.text('पूजा'), findsOneWidget);
    expect(find.text('ज्योतिषी'), findsOneWidget);
    expect(find.text('निःशुल्क कुण्डली'), findsOneWidget);
    expect(find.text('आज का शुभ मुहूर्त: अमृत काल सक्रिय'), findsOneWidget);
    expect(find.text('वैदिक अनुष्ठान एवं नित्य साधन'), findsOneWidget);

    // 3. Switch to Gujarati
    await LocaleManager.instance.setLanguage(AppLanguage.gu);
    await tester.pump();

    expect(find.text('પરામર્શ'), findsOneWidget);
    expect(find.text('પૂજા'), findsOneWidget);
    expect(find.text('જ્યોતિષીઓ'), findsOneWidget);
    expect(find.text('મફત કુંડળી'), findsOneWidget);
    expect(find.text('વૈદિક અનુષ્ઠાન અને દૈનિક સાધન'), findsOneWidget);

    // 4. Switch to Marathi
    await LocaleManager.instance.setLanguage(AppLanguage.mr);
    await tester.pump();

    expect(find.text('सल्ला घ्या'), findsOneWidget);
    expect(find.text('पूजा'), findsOneWidget);
    expect(find.text('ज्योतिषी'), findsOneWidget);
    expect(find.text('मोफत पत्रिका'), findsOneWidget);
    expect(find.text('वैदिक विधी व नित्य साधना'), findsOneWidget);

    // 5. Switch to Bengali
    await LocaleManager.instance.setLanguage(AppLanguage.bn);
    await tester.pump();

    expect(find.text('পরামর্শ'), findsOneWidget);
    expect(find.text('পূজা'), findsOneWidget);
    expect(find.text('বিনামূল্যে কুষ্ঠি'), findsOneWidget);

    // 6. Switch to Tamil
    await LocaleManager.instance.setLanguage(AppLanguage.ta);
    await tester.pump();

    expect(find.text('ஆலோசனை'), findsOneWidget);
    expect(find.text('பூஜை'), findsOneWidget);
    expect(find.text('இலவச ஜாதகம்'), findsOneWidget);

    // 7. Switch to Telugu
    await LocaleManager.instance.setLanguage(AppLanguage.te);
    await tester.pump();

    expect(find.text('సంప్రదించండి'), findsOneWidget);
    expect(find.text('పూజ'), findsOneWidget);
    expect(find.text('ఉచిత కుండలి'), findsOneWidget);

    // 8. Switch to Kannada
    await LocaleManager.instance.setLanguage(AppLanguage.kn);
    await tester.pump();

    expect(find.text('ಸಮಾಲೋಚನೆ'), findsOneWidget);
    expect(find.text('ಪೂಜೆ'), findsOneWidget);
    expect(find.text('ಉಚಿತ ಕುಂಡಲಿ'), findsOneWidget);

    // Reset back to English
    await LocaleManager.instance.setLanguage(AppLanguage.en);
    await tester.pump();
    expect(find.text('Consult'), findsOneWidget);
  });
}
