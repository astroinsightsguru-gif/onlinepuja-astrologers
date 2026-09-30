import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/provider.dart';
import 'package:onlinepuja_astrologer/state/app_session.dart';
import 'package:onlinepuja_astrologer/ui/screens/home/home_shell.dart';
import 'package:onlinepuja_astrologer/ui/screens/orders/orders_fulfillment_screen.dart';

void main() {
  Widget wrap(Widget child) => ChangeNotifierProvider(
        create: (_) => PartnerSession(),
        child: MaterialApp(home: child),
      );

  testWidgets('HomeShell renders partner app bar and calls/chats tabs', (tester) async {
    await tester.pumpWidget(wrap(const HomeShell()));

    expect(find.text('Partner Portal'), findsOneWidget);
    expect(find.text('Calls'), findsOneWidget);
    expect(find.text('Chats'), findsOneWidget);
  });

  testWidgets('OrdersFulfillmentScreen renders Puja and Report tabs', (tester) async {
    tester.view.physicalSize = const Size(800, 1200);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(() => tester.view.resetPhysicalSize());

    await tester.pumpWidget(wrap(const OrdersFulfillmentScreen()));
    await tester.pump();

    expect(find.text('Order Fulfillment'), findsOneWidget);
    expect(find.text('Puja Bookings (0)'), findsOneWidget);
    expect(find.text('Reports (0)'), findsOneWidget);
  });
}
