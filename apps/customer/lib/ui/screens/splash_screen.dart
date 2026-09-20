import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../state/app_session.dart';
import 'auth/phone_login_screen.dart';
import 'main_shell.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  static const route = '/';

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _bootstrap();
  }

  Future<void> _bootstrap() async {
    await Future<void>.delayed(const Duration(milliseconds: 1200));
    if (!mounted) return;
    final session = context.read<AppSession>();
    if (!mounted) return;
    Navigator.of(context).pushNamedAndRemoveUntil(
      session.isLoggedIn ? MainShell.route : PhoneLoginScreen.route,
      (r) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    return Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // On dark backgrounds the deep-maroon text is unreadable → tint.
            IgnorePointer(
              child: dark
                  ? ShaderMask(
                      shaderCallback: (b) => const LinearGradient(
                        colors: [Colors.white, AppTheme.gold],
                      ).createShader(b),
                      child: Brand.logo(),
                    )
                  : Brand.logo(),
            ),
            const SizedBox(height: 14),
            Text(
              'Talk to India\'s best astrologers',
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color: Theme.of(context).colorScheme.outline,
                  ),
            ),
            const SizedBox(height: 28),
            const SizedBox(
              width: 26,
              height: 26,
              child: CircularProgressIndicator(strokeWidth: 2.4),
            ),
          ],
        ),
      ),
    );
  }
}
