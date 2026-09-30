import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/provider.dart';
import 'package:op_shared/op_shared.dart';
import 'package:onlinepuja_astrologer/state/app_session.dart';
import 'package:onlinepuja_astrologer/ui/screens/home/home_shell.dart';

void main() {
  testWidgets('homepage light screenshot', (tester) async {
    tester.view.physicalSize = const Size(412, 915);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(() => tester.view.resetPhysicalSize());

    final session = PartnerSession();
    session.user = User(
      id: 108,
      name: 'Acharya Rajesh Sharma',
      email: 'rajesh.sharma@vedicastrology.com',
      contactNo: '9876543210',
      walletAmount: 24580.0,
      chatStatus: 'Online',
      callStatus: 'Online',
    );
    session.chatStatus = 'Online';
    session.callStatus = 'Online';

    await runZonedGuarded(() async {
      await tester.pumpWidget(
        ChangeNotifierProvider<PartnerSession>.value(
          value: session,
          child: MaterialApp(
            debugShowCheckedModeBanner: false,
            theme: AppTheme.light(),
            home: const HomeShell(),
          ),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 300));

      await expectLater(
        find.byType(MaterialApp),
        matchesGoldenFile('goldens/homepage_light.png'),
      );
    }, (error, stack) {});
  });

  testWidgets('homepage dark screenshot', (tester) async {
    tester.view.physicalSize = const Size(412, 915);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(() => tester.view.resetPhysicalSize());

    final session = PartnerSession();
    session.user = User(
      id: 108,
      name: 'Acharya Rajesh Sharma',
      email: 'rajesh.sharma@vedicastrology.com',
      contactNo: '9876543210',
      walletAmount: 24580.0,
      chatStatus: 'Online',
      callStatus: 'Online',
    );
    session.chatStatus = 'Online';
    session.callStatus = 'Online';

    await runZonedGuarded(() async {
      await tester.pumpWidget(
        ChangeNotifierProvider<PartnerSession>.value(
          value: session,
          child: MaterialApp(
            debugShowCheckedModeBanner: false,
            theme: AppTheme.dark(),
            home: const HomeShell(),
          ),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 300));

      await expectLater(
        find.byType(MaterialApp),
        matchesGoldenFile('goldens/homepage_dark.png'),
      );
    }, (error, stack) {});
  });
}
