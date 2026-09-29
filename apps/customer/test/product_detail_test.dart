import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:op_shared/op_shared.dart';
import 'package:onlinepuja_v2_customer/ui/screens/mall/product_detail_screen.dart';

void main() {
  testWidgets('renders product info with discount price', (tester) async {
    final product = Product(
      id: 1,
      name: 'Rahu Phasmat',
      description: 'Energized Rahu phasmat stone.',
      price: '1500',
      discountPrice: '1200',
      stock: 5,
    );

    tester.view.physicalSize = const Size(800, 1200);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(() => tester.view.resetPhysicalSize());

    await tester.pumpWidget(
      MaterialApp(home: ProductDetailScreen(product: product)),
    );
    await tester.pump();

    expect(find.text('Rahu Phasmat'), findsNWidgets(2));
    expect(find.textContaining('1200'), findsWidgets);
    expect(find.text('In stock'), findsOneWidget);
  });
}
