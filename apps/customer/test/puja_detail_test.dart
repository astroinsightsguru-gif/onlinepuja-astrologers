import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:op_shared/op_shared.dart';
import 'package:onlinepuja_v2_customer/ui/screens/puja/puja_detail_screen.dart';

/// Widget tests for [PujaDetailScreen].
///
/// The screen calls PujaApi for FAQs in initState, but the main content
/// (title, packages, benefits, book button) renders from the injected
/// `Puja` model — no waiting required.
void main() {
  testWidgets('renders puja title, packages, benefits, and book button',
      (tester) async {
    final puja = Puja(
      id: 1,
      title: 'Ganesh Chaturthi Puja',
      subtitle: 'Sacred Ganesha worship',
      place: 'Varanasi',
      longDescription:
          'Celebrate the remover of obstacles with this sacred puja.',
            benefits: ['Removes obstacles', 'Brings prosperity'],
      startDatetime: '2024-08-15 10:00:00',
      endDatetime: '2024-08-16 10:00:00',
      packages: [
        PujaPackage(
          id: 1,
          name: 'Standard',
          price: '500',
          inclusions: ['Flowers', 'Sacred thread'],
        ),
        PujaPackage(
          id: 2,
          name: 'Premium',
          price: '1000',
          inclusions: ['Flowers', 'Fruits', 'Sacred thread'],
        ),
      ],
    );

        await tester.pumpWidget(
      MaterialApp(home: PujaDetailScreen(puja: puja)),
    );
    // Let the async _loadFaqs() settle (it fails silently in tests).
    await tester.pumpAndSettle();

    // Title appears in AppBar + body
    expect(find.text('Ganesh Chaturthi Puja'), findsWidgets);

    // Section headers
    expect(find.text('About'), findsOneWidget);
    expect(find.text('Benefits'), findsOneWidget);
    expect(find.text('Packages'), findsOneWidget);

    // Package data
    expect(find.text('Standard'), findsOneWidget);
    expect(find.text('Premium'), findsOneWidget);
    expect(find.text('₹500'), findsOneWidget);
    expect(find.text('₹1000'), findsOneWidget);

    // Benefits
    expect(find.text('Removes obstacles'), findsOneWidget);

    // Book button (uses first package's price)
    expect(find.text('Book now · ₹500'), findsOneWidget);
  });

  testWidgets('renders puja without packages', (tester) async {
    final puja = Puja(
      id: 2,
      title: 'Simple Puja',
      longDescription: 'A simple worship.',
    );

        await tester.pumpWidget(
      MaterialApp(home: PujaDetailScreen(puja: puja)),
    );
    await tester.pumpAndSettle();

    expect(find.text('Simple Puja'), findsWidgets);
    expect(find.text('About'), findsOneWidget);
    expect(find.text('Book now · ₹0'), findsOneWidget);

    // No packages section
    expect(find.text('Packages'), findsNothing);
  });
}
