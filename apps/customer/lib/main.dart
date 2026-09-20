import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'app.dart';
import 'state/app_session.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  final session = AppSession();
  await session.init();
  runApp(
    ChangeNotifierProvider.value(
      value: session,
      child: const OnlinePujaApp(),
    ),
  );
}
