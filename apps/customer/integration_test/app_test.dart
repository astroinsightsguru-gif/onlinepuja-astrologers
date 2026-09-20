import 'package:flutter_test/flutter_test.dart';
import 'package:integration_test/integration_test.dart';
import 'package:provider/provider.dart';
import 'package:op_shared/op_shared.dart';
import 'package:onlinepuja_v2_customer/app.dart';
import 'package:onlinepuja_v2_customer/state/app_session.dart';

/// Integration test — app launch smoke test + Explore navigation flows.
///
/// Injects a pre-authenticated AppSession (bypassing SharedPreferences) so the
/// splash screen navigates straight to MainShell. From there the test drills
/// into each Explore feature card: Kundli, Puja, AstroMall.
void main() {
  IntegrationTestWidgetsFlutterBinding.ensureInitialized();

  testWidgets('app launches → splash → MainShell → Explore flows',
      (tester) async {
    // ── Set up a logged-in session without SharedPreferences ────────
    final session = AppSession();
    // Seed SessionStore directly so isLoggedIn returns true.
    SessionStore.instance
      ..token = 'test_token'
      ..tokenType = 'Bearer'
      ..user = User(id: 1, name: 'Test User', walletAmount: 100)
      ..flags = SystemFlags([
        SystemFlag(name: 'currency', value: '₹'),
      ]);
    session.user = SessionStore.instance.user;
    session.flags = SessionStore.instance.flags;
    session.booted = true;

    await tester.pumpWidget(
      ChangeNotifierProvider.value(
        value: session,
        child: const OnlinePujaApp(),
      ),
    );

    // ── 1. Splash screen visible ─────────────────────────────────────
    expect(
      find.text("Talk to India's best astrologers"),
      findsOneWidget,
    );

    // Advance past the 1.2 s splash delay.
    await tester.pump(const Duration(milliseconds: 1200));
    await tester.pump(); // settle the navigation

    // ── 2. MainShell bottom navigation visible ───────────────────────
    expect(find.text('Consult'), findsOneWidget);
    expect(find.text('Explore'), findsWidgets);
    expect(find.text('History'), findsOneWidget);

    // ── 3. Switch to the Explore tab ───────────────────────────────────
    await tester.tap(find.text('Explore'));
    await tester.pump();

    // Explore hub — all 5 feature cards
    expect(find.text('Kundli'), findsOneWidget);
    expect(find.text('Panchang'), findsOneWidget);
    expect(find.text('Horoscope'), findsOneWidget);
    expect(find.text('Puja'), findsOneWidget);
    expect(find.text('AstroMall'), findsOneWidget);

    // ── 4. Kundli flow ────────────────────────────────────────────────
    await tester.tap(find.text('Kundli'));
    await tester.pump();
    expect(find.text('Kundli'), findsWidgets); // card + AppBar
    await tester.pageBack();
    await tester.pump();

    // ── 5. Puja flow ──────────────────────────────────────────────────
    await tester.tap(find.text('Puja'));
    await tester.pump();
    expect(find.text('Puja'), findsWidgets);
    await tester.pageBack();
    await tester.pump();

    // ── 6. AstroMall flow ─────────────────────────────────────────────
    await tester.tap(find.text('AstroMall'));
    await tester.pump();
    expect(find.text('AstroMall'), findsWidgets);
  });
}
