import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:op_shared/op_shared.dart';
import 'package:onlinepuja_v2_customer/ui/screens/kundli/kundli_detail_screen.dart';

/// Widget tests for [KundliDetailScreen].
///
/// The screen calls KundliApi in initState, so after a single pump the
/// loading spinner + tab headers are asserted (API not yet resolved).
void main() {
  testWidgets('shows AppBar title, tab headers, and loading indicator',
      (tester) async {
    final kundli = Kundli(
      id: 1,
      name: 'Test Kundli',
      gender: 'Male',
      birthDate: DateTime(1990, 5, 15),
      birthTime: '14:30',
      birthPlace: 'Delhi',
      latitude: 28.7041,
      longitude: 77.1025,
    );

    await tester.pumpWidget(
      MaterialApp(home: KundliDetailScreen(kundli: kundli)),
    );

    // AppBar title
    expect(find.text('Test Kundli'), findsOneWidget);

    // Tab headers (always shown, even during loading)
    expect(find.text('Basic'), findsOneWidget);
    expect(find.text('Planets'), findsOneWidget);
    expect(find.text('Dasha'), findsOneWidget);
    expect(find.text('Dosha'), findsOneWidget);

    // Loading spinner (API not yet resolved)
    expect(find.byType(CircularProgressIndicator), findsWidgets);
  });
}
