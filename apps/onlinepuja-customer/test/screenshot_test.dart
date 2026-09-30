import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/provider.dart';
import 'package:op_shared/op_shared.dart';
import 'package:onlinepuja_customer/state/app_session.dart';
import 'package:onlinepuja_customer/ui/theme/customer_theme.dart';
import 'package:onlinepuja_customer/ui/screens/main_shell.dart';
import 'package:onlinepuja_customer/ui/screens/explore/explore_screen.dart';

void main() {
  testWidgets('customer homepage light screenshot', (tester) async {
    tester.view.physicalSize = const Size(412, 915);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(() => tester.view.resetPhysicalSize());

    final session = AppSession();
    session.user = User(
      id: 1,
      name: 'Aarav Sharma',
      email: 'aarav.sharma@example.com',
      contactNo: '9876543210',
      walletAmount: 501.0,
    );

    await runZonedGuarded(() async {
      await tester.pumpWidget(
        ChangeNotifierProvider<AppSession>.value(
          value: session,
          child: MaterialApp(
            debugShowCheckedModeBanner: false,
            theme: CustomerTheme.light(),
            home: const MainShell(),
          ),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 300));

      await expectLater(
        find.byType(MaterialApp),
        matchesGoldenFile('goldens/customer_homepage_light.png'),
      );
    }, (error, stack) {});
  });

  testWidgets('customer homepage dark screenshot', (tester) async {
    tester.view.physicalSize = const Size(412, 915);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(() => tester.view.resetPhysicalSize());

    final session = AppSession();
    session.user = User(
      id: 1,
      name: 'Aarav Sharma',
      email: 'aarav.sharma@example.com',
      contactNo: '9876543210',
      walletAmount: 501.0,
    );

    await runZonedGuarded(() async {
      await tester.pumpWidget(
        ChangeNotifierProvider<AppSession>.value(
          value: session,
          child: MaterialApp(
            debugShowCheckedModeBanner: false,
            theme: CustomerTheme.dark(),
            home: const MainShell(),
          ),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 300));

      await expectLater(
        find.byType(MaterialApp),
        matchesGoldenFile('goldens/customer_homepage_dark.png'),
      );
    }, (error, stack) {});
  });

  testWidgets('explore sanctum light screenshot', (tester) async {
    tester.view.physicalSize = const Size(412, 915);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(() => tester.view.resetPhysicalSize());

    final session = AppSession();
    session.user = User(
      id: 1,
      name: 'Aarav Sharma',
      email: 'aarav.sharma@example.com',
      contactNo: '9876543210',
      walletAmount: 501.0,
    );

    await runZonedGuarded(() async {
      await tester.pumpWidget(
        ChangeNotifierProvider<AppSession>.value(
          value: session,
          child: MaterialApp(
            debugShowCheckedModeBanner: false,
            theme: CustomerTheme.light(),
            home: const Scaffold(body: ExploreScreen()),
          ),
        ),
      );
      await tester.pump();
      await tester.pump(const Duration(milliseconds: 300));

      await expectLater(
        find.byType(MaterialApp),
        matchesGoldenFile('goldens/explore_sanctum_light.png'),
      );
    }, (error, stack) {});
  });
}
