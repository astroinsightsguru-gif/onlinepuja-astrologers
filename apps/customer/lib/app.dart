import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import 'ui/screens/astrologer/astrologer_detail_screen.dart';
import 'ui/screens/auth/otp_screen.dart';
import 'ui/screens/auth/phone_login_screen.dart';
import 'ui/screens/call/audio_call_screen.dart';
import 'ui/screens/chat/chat_session_screen.dart';
import 'ui/screens/main_shell.dart';
import 'ui/screens/profile/wallet_screen.dart';
import 'ui/screens/splash_screen.dart';

class OnlinePujaApp extends StatelessWidget {
  const OnlinePujaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Online Puja',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light(),
      darkTheme: AppTheme.dark(),
      themeMode: ThemeMode.system,
      initialRoute: SplashScreen.route,
      routes: {
        SplashScreen.route: (_) => const SplashScreen(),
        PhoneLoginScreen.route: (_) => const PhoneLoginScreen(),
        OtpScreen.route: (_) => const OtpScreen(),
        MainShell.route: (_) => const MainShell(),
        WalletScreen.route: (_) => const WalletScreen(),
      },
      onGenerateRoute: (settings) {
        final args = settings.arguments;
        Map<String, dynamic> map() =>
            args is Map<String, dynamic> ? args : const {};
        switch (settings.name) {
          case AstrologerDetailScreen.route:
            return MaterialPageRoute(
              builder: (_) =>
                  AstrologerDetailScreen(id: map()['astrologerId'] as int),
            );
          case ChatSessionScreen.route:
            return MaterialPageRoute(
              builder: (_) => ChatSessionScreen(
                astrologerId: map()['astrologerId'] as int,
                astrologerName: map()['astrologerName'] as String? ?? 'Astrologer',
                sessionId: map()['sessionId'] as String?,
                isFree: map()['isFree'] as bool? ?? false,
              ),
            );
          case AudioCallScreen.route:
            return MaterialPageRoute(
              builder: (_) => AudioCallScreen(
                astrologerId: map()['astrologerId'] as int,
                astrologerName:
                    map()['astrologerName'] as String? ?? 'Astrologer',
                sessionId: map()['sessionId'] as String?,
                isVideo: map()['isVideo'] as bool? ?? false,
              ),
            );
        }
        return null;
      },
    );
  }
}

/// Convenience navigation helpers.
extension OpNav on BuildContext {
  Future<void> openAstrologer(int id) => pushNamed(
      AstrologerDetailScreen.route, {'astrologerId': id});

  Future<void> openChat({
    required int astrologerId,
    required String astrologerName,
    String? sessionId,
    bool isFree = false,
  }) =>
      pushNamed(ChatSessionScreen.route, {
        'astrologerId': astrologerId,
        'astrologerName': astrologerName,
        'sessionId': sessionId,
        'isFree': isFree,
      });

  Future<void> openCall({
    required int astrologerId,
    required String astrologerName,
    String? sessionId,
    bool isVideo = false,
  }) =>
      pushNamed(AudioCallScreen.route, {
        'astrologerId': astrologerId,
        'astrologerName': astrologerName,
        'sessionId': sessionId,
        'isVideo': isVideo,
      });

  Future<void> pushNamed(String route, [Map<String, dynamic>? args]) =>
      Navigator.of(this).pushNamed(route, arguments: args);
}
