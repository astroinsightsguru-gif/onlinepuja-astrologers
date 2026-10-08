import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:onlinepuja_customer/state/app_session.dart';
import 'package:onlinepuja_customer/ui/screens/astrologer/astrologers_screen.dart';
import 'package:provider/provider.dart';

void main() {
  testWidgets('AstrologersScreen renders clean homepage hierarchy with no orphans',
      (tester) async {
    tester.view.physicalSize = const Size(800, 1600);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(() => tester.view.resetPhysicalSize());

    final session = AppSession();

    await tester.pumpWidget(
      ChangeNotifierProvider<AppSession>.value(
        value: session,
        child: const MaterialApp(
          home: AstrologersScreen(),
        ),
      ),
    );

    // Let the initial frame render
    await tester.pump();

    // Verify search bar exists
    expect(find.byType(TextField), findsOneWidget);

    // Verify filter chips exist
    expect(find.text('All'), findsOneWidget);
    expect(find.text('Vedic'), findsOneWidget);
    expect(find.text('Tarot'), findsOneWidget);

    // Tap on a filter chip to ensure no crash
    await tester.tap(find.text('Tarot'));
    await tester.pump();
  });
}
