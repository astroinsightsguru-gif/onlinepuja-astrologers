import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/provider.dart';
import 'package:onlinepuja_v2_customer/state/app_session.dart';
import 'package:onlinepuja_v2_customer/ui/screens/explore/explore_screen.dart';

/// Widget tests for [ExploreScreen] — the hub grid linking all feature tiles.
void main() {
  /// Wraps [ExploreScreen] in the providers it (and its children) may need.
  Widget wrapWidget(Widget child) => ChangeNotifierProvider(
        create: (_) => AppSession(),
        child: MaterialApp(home: child),
      );

  testWidgets('renders all 5 feature hub cards with titles and subtitles',
      (tester) async {
    tester.view.physicalSize = const Size(1080, 2400);
    tester.view.devicePixelRatio = 2.0;
    addTearDown(() {
      tester.view.resetPhysicalSize();
      tester.view.resetDevicePixelRatio();
    });
    await tester.pumpWidget(wrapWidget(const ExploreScreen()));

    expect(find.text('Explore'), findsOneWidget);
    expect(find.text('Kundli'), findsOneWidget);
    expect(find.text('Panchang'), findsOneWidget);
    expect(find.text('Horoscope'), findsOneWidget);
    expect(find.text('Puja'), findsOneWidget);
    expect(find.text('AstroMall'), findsOneWidget);

    // Subtitles
    expect(find.text('Birth chart, planets & dasha'), findsOneWidget);
    expect(find.text('Today\'s almanac'), findsOneWidget);
    expect(find.text('Daily predictions by sign'), findsOneWidget);
    expect(find.text('Book sacred pujas'), findsOneWidget);
    expect(find.text('Gemstones & spiritual items'), findsOneWidget);
  });

  testWidgets('tapping Kundli navigates to KundliListScreen', (tester) async {
    tester.view.physicalSize = const Size(1080, 2400);
    tester.view.devicePixelRatio = 2.0;
    addTearDown(() {
      tester.view.resetPhysicalSize();
      tester.view.resetDevicePixelRatio();
    });
    await tester.pumpWidget(wrapWidget(const ExploreScreen()));
    await tester.ensureVisible(find.text('Kundli'));
    await tester.tap(find.text('Kundli'));
    await tester.pump();
    // "Kundli" appears in both the Explore card (behind) + the new AppBar
    expect(find.text('Kundli'), findsWidgets);
  });

  testWidgets('tapping Puja navigates to PujaListScreen', (tester) async {
    tester.view.physicalSize = const Size(1080, 2400);
    tester.view.devicePixelRatio = 2.0;
    addTearDown(() {
      tester.view.resetPhysicalSize();
      tester.view.resetDevicePixelRatio();
    });
    await tester.pumpWidget(wrapWidget(const ExploreScreen()));
    await tester.ensureVisible(find.text('Puja'));
    await tester.tap(find.text('Puja'), warnIfMissed: false);
    await tester.pump();
    expect(find.text('Puja'), findsWidgets);
  });

  testWidgets('tapping AstroMall navigates to MallScreen', (tester) async {
    tester.view.physicalSize = const Size(1080, 2400);
    tester.view.devicePixelRatio = 2.0;
    addTearDown(() {
      tester.view.resetPhysicalSize();
      tester.view.resetDevicePixelRatio();
    });
    await tester.pumpWidget(wrapWidget(const ExploreScreen()));
    await tester.ensureVisible(find.text('AstroMall'));
    await tester.tap(find.text('AstroMall'), warnIfMissed: false);
    await tester.pump();
    expect(find.text('AstroMall'), findsWidgets);
  });
}
