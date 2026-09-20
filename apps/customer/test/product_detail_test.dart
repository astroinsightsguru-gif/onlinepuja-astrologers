import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:op_shared/op_shared.dart';
import 'package:onlinepuja_v2_customer/ui/screens/mall/product_detail_screen.dart';

void main() {
  const rs = '\u20B9';
  const dot = '\u00B7';

  testWidgets('renders product info with discount price', (tester) async {
    final product = Product(
      id: 1,
      name: 'Rahu Phasmat',
      description: 'Energized Rahu phasmat stone.',
      price: '1500',
      discountPrice: '1200',
      stock: 5,
    );

    await tester.pumpWidget(
      MaterialApp(home: ProductDetailScreen(product: product)),
    );
    await tester.pumpWidget(
      MaterialApp(home: ProductDetailScreen(product: product)),
    );
    await tester.pump();

    expect(find.text('Rahu Phasmat'), findsOneWidget);
    expect(find.text('${rs}1200'), findsOneWidget);
    expect(find.text('In stock'), findsOneWidget);
    expect(find.text('Order now $dot ${rs}1200'), findsOneWidget);
  });
}
