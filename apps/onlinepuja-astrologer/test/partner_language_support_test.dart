import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:op_shared/op_shared.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  setUp(() async {
    SharedPreferences.setMockInitialValues({});
    await LocaleManager.instance.init();
    await LocaleManager.instance.setLanguage(AppLanguage.en);
  });

  test('Astrologer Partner Strings provide complete translations across all 15 languages', () {
    // English verification
    expect(AppStrings.overview, 'Overview');
    expect(AppStrings.requests, 'Requests');
    expect(AppStrings.fulfillment, 'Fulfillment');
    expect(AppStrings.earnings, 'Earnings');
    expect(AppStrings.withdrawFunds, 'Withdraw Funds');
    expect(AppStrings.availableBalance, 'AVAILABLE BALANCE');

    // Hindi verification (Polite, authentic spiritual Sanskritized Hindi)
    LocaleManager.instance.setLanguage(AppLanguage.hi);
    expect(AppStrings.overview, 'सिंहावलोकन');
    expect(AppStrings.requests, 'अनुरोध');
    expect(AppStrings.fulfillment, 'संकल्प सिद्धि');
    expect(AppStrings.earnings, 'आय एवं दक्षिणा');
    expect(AppStrings.withdrawFunds, 'धनराशि आहरण');
    expect(AppStrings.statements, 'विवरणिका');
    expect(AppStrings.readyForConsultations, 'परामर्श हेतु तत्पर');
    expect(AppStrings.currentlyOffline, 'अभी अनुपलब्ध');
    expect(AppStrings.practiceSummary, 'आज का कार्य सारांश');
    expect(AppStrings.consultationCalls, 'परामर्श कॉल');
    expect(AppStrings.chatSessions, 'चैट सत्र');
    expect(AppStrings.pujaFulfillment, 'पूजा संकल्प पूर्ति');
    expect(AppStrings.acceptAndStart, 'स्वीकार करें एवं प्रारंभ करें');
    expect(AppStrings.reject, 'अस्वीकार करें');
    expect(AppStrings.verifiedAstrologer, 'प्रमाणित ज्योतिषी');

    // Sanskrit verification
    LocaleManager.instance.setLanguage(AppLanguage.sa);
    expect(AppStrings.overview, 'सिंहावलोकनम्');
    expect(AppStrings.requests, 'अनुरोधाः');
    expect(AppStrings.fulfillment, 'संकल्पसिद्धिः');
    expect(AppStrings.earnings, 'दक्षिणा');
    expect(AppStrings.withdrawFunds, 'धनराशि नयनम्');
    expect(AppStrings.acceptAndStart, 'स्वीकरोतु आरभतु च');
    expect(AppStrings.reject, 'तिरस्करोतु');

    // Gujarati verification
    LocaleManager.instance.setLanguage(AppLanguage.gu);
    expect(AppStrings.overview, 'ઝાંખી');
    expect(AppStrings.requests, 'વિનંતીઓ');
    expect(AppStrings.fulfillment, 'સંકલ્પ સિદ્ધિ');
    expect(AppStrings.earnings, 'આવક અને દક્ષિણા');

    // Marathi verification
    LocaleManager.instance.setLanguage(AppLanguage.mr);
    expect(AppStrings.overview, 'आढावा');
    expect(AppStrings.requests, 'विनंत्या');
    expect(AppStrings.fulfillment, 'संकल्प पूर्ती');
    expect(AppStrings.earnings, 'कमाई व दक्षिणा');

    // Bengali verification
    LocaleManager.instance.setLanguage(AppLanguage.bn);
    expect(AppStrings.overview, 'সংক্ষিপ্ত বিবরণ');
    expect(AppStrings.requests, 'অনুরোধ');

    // Tamil verification
    LocaleManager.instance.setLanguage(AppLanguage.ta);
    expect(AppStrings.overview, 'மேலோட்டம்');
    expect(AppStrings.requests, 'கோரிக்கைகள்');

    // Telugu verification
    LocaleManager.instance.setLanguage(AppLanguage.te);
    expect(AppStrings.overview, 'అవలోకనం');
    expect(AppStrings.requests, 'అభ్యర్థనలు');

    // Kannada verification
    LocaleManager.instance.setLanguage(AppLanguage.kn);
    expect(AppStrings.overview, 'ಅವಲೋಕನ');
    expect(AppStrings.requests, 'ವಿನಂತಿಗಳು');

    // Malayalam verification
    LocaleManager.instance.setLanguage(AppLanguage.ml);
    expect(AppStrings.overview, 'അവലോകനം');
    expect(AppStrings.requests, 'അഭ്യർത്ഥനകൾ');

    // Odia verification
    LocaleManager.instance.setLanguage(AppLanguage.or);
    expect(AppStrings.overview, 'ସମୀକ୍ଷା');
    expect(AppStrings.requests, 'ଅନୁରୋଧ');

    // Punjabi verification
    LocaleManager.instance.setLanguage(AppLanguage.pa);
    expect(AppStrings.overview, 'ਸੰਖੇਪ ਜਾਣਕਾਰੀ');
    expect(AppStrings.requests, 'ਬੇਨਤੀਆਂ');

    // Assamese verification
    LocaleManager.instance.setLanguage(AppLanguage.as_);
    expect(AppStrings.overview, 'সাৰাংশ');
    expect(AppStrings.requests, 'অনুৰোধ');

    // Bhojpuri verification
    LocaleManager.instance.setLanguage(AppLanguage.bho);
    expect(AppStrings.overview, 'सिंहावलोकन');
    expect(AppStrings.requests, 'अनुरोध');

    // Maithili verification
    LocaleManager.instance.setLanguage(AppLanguage.mai);
    expect(AppStrings.overview, 'सिंहावलोकन');
    expect(AppStrings.requests, 'अनुरोध');
  });

  testWidgets('Astrologer UI reactively updates labels when language switches', (tester) async {
    await LocaleManager.instance.setLanguage(AppLanguage.en);

    await tester.pumpWidget(
      MaterialApp(
        home: Scaffold(
          body: ValueListenableBuilder<AppLanguage>(
            valueListenable: LocaleManager.instance.currentLanguage,
            builder: (context, lang, _) {
              return Column(
                children: [
                  Text(AppStrings.overview),
                  Text(AppStrings.requests),
                  Text(AppStrings.fulfillment),
                  Text(AppStrings.earnings),
                  Text(AppStrings.withdrawFunds),
                  Text(AppStrings.statements),
                ],
              );
            },
          ),
        ),
      ),
    );
    await tester.pump();

    // 1. English
    expect(find.text('Overview'), findsOneWidget);
    expect(find.text('Requests'), findsOneWidget);
    expect(find.text('Fulfillment'), findsOneWidget);
    expect(find.text('Earnings'), findsOneWidget);
    expect(find.text('Withdraw Funds'), findsOneWidget);
    expect(find.text('Statements'), findsOneWidget);

    // 2. Switch to Hindi
    await LocaleManager.instance.setLanguage(AppLanguage.hi);
    await tester.pump();

    expect(find.text('सिंहावलोकन'), findsOneWidget);
    expect(find.text('अनुरोध'), findsOneWidget);
    expect(find.text('संकल्प सिद्धि'), findsOneWidget);
    expect(find.text('आय एवं दक्षिणा'), findsOneWidget);
    expect(find.text('धनराशि आहरण'), findsOneWidget);
    expect(find.text('विवरणिका'), findsOneWidget);

    // 3. Switch to Sanskrit
    await LocaleManager.instance.setLanguage(AppLanguage.sa);
    await tester.pump();

    expect(find.text('सिंहावलोकनम्'), findsOneWidget);
    expect(find.text('अनुरोधाः'), findsOneWidget);
    expect(find.text('संकल्पसिद्धिः'), findsOneWidget);
    expect(find.text('दक्षिणा'), findsOneWidget);

    // Reset back to English
    await LocaleManager.instance.setLanguage(AppLanguage.en);
    await tester.pump();
    expect(find.text('Overview'), findsOneWidget);
  });
}
