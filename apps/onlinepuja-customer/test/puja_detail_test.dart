import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:op_shared/op_shared.dart';
import 'package:onlinepuja_customer/ui/screens/puja/puja_detail_screen.dart';

/// Widget tests for [PujaDetailScreen].
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

    tester.view.physicalSize = const Size(800, 1400);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(() => tester.view.resetPhysicalSize());

    await tester.pumpWidget(
      MaterialApp(home: PujaDetailScreen(puja: puja)),
    );
    await tester.pumpAndSettle();

    // Title appears in AppBar + body
    expect(find.text('Ganesh Chaturthi Puja'), findsWidgets);

    // Section headers
    expect(find.text('Choose Your Puja Package'), findsOneWidget);
    expect(find.text('Significance & Vidhi'), findsOneWidget);
    expect(find.text('Divine Blessings & Benefits'), findsOneWidget);

    // Package data
    expect(find.text('Standard'), findsWidgets);
    expect(find.text('Premium'), findsOneWidget);
    expect(find.text('₹500'), findsWidgets);
    expect(find.text('₹1000'), findsOneWidget);

    // Benefits
    expect(find.text('Removes obstacles'), findsOneWidget);

    // Participate CTA button
    expect(find.text('PARTICIPATE'), findsOneWidget);
  });

  testWidgets('renders puja with default fallback packages when none provided',
      (tester) async {
    final puja = Puja(
      id: 2,
      title: 'Simple Puja',
      longDescription: 'A simple worship.',
    );

    tester.view.physicalSize = const Size(800, 1400);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(() => tester.view.resetPhysicalSize());

    await tester.pumpWidget(
      MaterialApp(home: PujaDetailScreen(puja: puja)),
    );
    await tester.pumpAndSettle();

    expect(find.text('Simple Puja'), findsWidgets);
    expect(find.text('Significance & Vidhi'), findsOneWidget);

    // Default fallback packages are presented so devotee can book
    expect(find.text('Choose Your Puja Package'), findsOneWidget);
    expect(find.text('Individual Sankalp'), findsWidgets);
    expect(find.text('₹501'), findsWidgets);
  });
}
