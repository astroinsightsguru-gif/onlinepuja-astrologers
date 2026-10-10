import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import 'app.dart';
import 'state/app_session.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await LocaleManager.instance.init();
  final session = PartnerSession();
  await session.init();
  runApp(
    ChangeNotifierProvider.value(
      value: session,
      child: const OnlinePujaPartnerApp(),
    ),
  );
}
